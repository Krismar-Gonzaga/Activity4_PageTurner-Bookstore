<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookImport;
use App\Models\BookImportError;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BookImportController extends Controller
{
    public function showForm()
    {
        $recentImports = BookImport::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.books.import', compact('recentImports'));
    }

public function upload(Request $request)
    {
        try {
            Log::info('Book import started', $request->all());
            
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
                'duplicate_action' => 'required|in:skip,update'
            ]);

            // Get valid categories
            $validCategories = Category::pluck('id', 'name')->toArray();
            
            if (empty($validCategories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found. Please add categories before importing books.'
                ], 400);
            }

            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('imports', $filename);

            // Create import record
            $import = BookImport::create([
                'user_id' => auth()->id(),
                'filename' => $path,
                'original_name' => $file->getClientOriginalName(),
                'status' => 'pending',
                'duplicate_handling' => ['action' => $request->duplicate_action]
            ]);

            // Dispatch to queue for background processing
            ProcessBookImport::dispatch($import->id, $path, $request->duplicate_action);

            AuditLogService::logImport($import, [
                'original_name' => $file->getClientOriginalName(),
                'duplicate_action' => $request->duplicate_action
            ]);

            return response()->json([
                'success' => true,
                'import_id' => $import->id,
                'message' => 'Import started. You will be notified when complete.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Import upload error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function processImportFile($import, $filePath, $duplicateAction, $validCategories)
    {
        try {
            $fullPath = Storage::path($filePath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception("File not found: {$fullPath}");
            }
            
            [$headers, $rows] = $this->readImportData($fullPath);
            
            $requiredHeaders = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category'];
            $missingHeaders = [];
            
            foreach ($requiredHeaders as $required) {
                if (!in_array($required, $headers)) {
                    $missingHeaders[] = $required;
                }
            }
            
            if (!empty($missingHeaders)) {
                throw new \Exception("Missing required headers: " . implode(', ', $missingHeaders));
            }
            
            $successfulRows = 0;
            $failedRows = 0;
            $rowNumber = 2;
            $totalRows = 0;
            
            // Count total rows first
            foreach ($rows as $row) {
                if (!empty(array_filter($row, fn($value) => trim((string) $value) !== ''))) {
                    $totalRows++;
                }
            }
            
            $import->update(['total_rows' => $totalRows]);
            $processedRows = 0;
            
foreach ($rows as $row) {
                // Skip empty rows
                if (empty(array_filter($row, fn($value) => trim((string) $value) !== ''))) {
                    $rowNumber++;
                    continue;
                }
                
                // Ensure row has same number of columns as headers
                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                } elseif (count($row) > count($headers)) {
                    $row = array_slice($row, 0, count($headers));
                }
                
                // Combine headers with row data
                $rowData = array_combine($headers, $row);
                if ($rowData === false) {
                    throw new \Exception("Invalid CSV format at row {$rowNumber}");
                }
                
                // Validate row
                $validation = $this->validateRow($rowData, $validCategories);
                
                if (!$validation['valid']) {
                    $failedRows++;
                    BookImportError::create([
                        'book_import_id' => $import->id,
                        'row_number' => $rowNumber,
                        'row_data' => $rowData,
                        'errors' => $validation['errors']
                    ]);
                } else {
                    try {
                        $this->saveBook($validation['data'], $duplicateAction, $validCategories);
                        $successfulRows++;
                    } catch (\Exception $e) {
                        $failedRows++;
                        BookImportError::create([
                            'book_import_id' => $import->id,
                            'row_number' => $rowNumber,
                            'row_data' => $rowData,
                            'errors' => [$e->getMessage()]
                        ]);
                    }
                }
                
                $processedRows++;
                $rowNumber++;
                
                // Update progress every 10 rows
                if ($processedRows % 10 == 0) {
                    $import->update([
                        'processed_rows' => $processedRows,
                        'successful_rows' => $successfulRows,
                        'failed_rows' => $failedRows
                    ]);
                }
            }
            
            // Final update
            $import->update([
                'status' => 'completed',
                'processed_rows' => $processedRows,
                'successful_rows' => $successfulRows,
                'failed_rows' => $failedRows,
                'completed_at' => now()
            ]);
            
            // Delete the temp file
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
            
            return [
                'total' => $totalRows,
                'successful' => $successfulRows,
                'failed' => $failedRows
            ];
            
        } catch (\Exception $e) {
            $import->update([
                'status' => 'failed',
                'errors' => ['message' => $e->getMessage()]
            ]);
            throw $e;
        }
    }

    private function readImportData(string $fullPath): array
    {
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($extension === 'xlsx') {
            $spreadsheet = IOFactory::load($fullPath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray('', true, true, false);

            if (empty($sheetData) || empty(array_filter($sheetData[0] ?? []))) {
                throw new \Exception("Invalid Excel file: No headers found");
            }

            $headers = $this->cleanHeaders($sheetData[0]);
            $rows = array_slice($sheetData, 1);

            return [$headers, $rows];
        }

        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open file: {$fullPath}");
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception("Invalid CSV file: No headers found");
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return [$this->cleanHeaders($headers), $rows];
    }

    private function cleanHeaders(array $headers): array
    {
        return array_map(function($header) {
            $header = (string) $header;
            return trim($header, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $headers);
    }

    private function validateRow($rowData, $validCategories)
    {
        $errors = [];
        
        // Validate ISBN
        if (empty($rowData['ISBN'])) {
            $errors[] = 'ISBN is required';
        } else {
            $isbn = preg_replace('/[^0-9xX]/', '', $rowData['ISBN']);
            if (!preg_match('/^(\d{10}|\d{13})$/i', $isbn) && !preg_match('/^\d{9}[\dXx]$/', $isbn)) {
                $errors[] = 'Invalid ISBN format (must be ISBN-10 or ISBN-13)';
            }
        }
        
        // Validate Title
        if (empty($rowData['Title'])) {
            $errors[] = 'Title is required';
        } elseif (strlen($rowData['Title']) > 255) {
            $errors[] = 'Title must not exceed 255 characters';
        }
        
        // Validate Author
        if (empty($rowData['Author'])) {
            $errors[] = 'Author is required';
        }
        
        // Validate Price
        if (empty($rowData['Price'])) {
            $errors[] = 'Price is required';
        } elseif (!is_numeric($rowData['Price'])) {
            $errors[] = 'Price must be numeric';
        } elseif ($rowData['Price'] <= 0) {
            $errors[] = 'Price must be greater than 0';
        } elseif ($rowData['Price'] > 9999.99) {
            $errors[] = 'Price cannot exceed 9999.99';
        }
        
        // Validate Stock
        if (!isset($rowData['Stock']) || $rowData['Stock'] === '') {
            $errors[] = 'Stock is required';
        } elseif (!is_numeric($rowData['Stock']) || !ctype_digit((string)$rowData['Stock'])) {
            $errors[] = 'Stock must be a positive integer';
        } elseif ($rowData['Stock'] < 0) {
            $errors[] = 'Stock cannot be negative';
        }
        
        // Validate Category
        if (empty($rowData['Category'])) {
            $errors[] = 'Category is required';
        } elseif (!isset($validCategories[$rowData['Category']])) {
            $errors[] = 'Category "' . $rowData['Category'] . '" does not exist. Available: ' . implode(', ', array_keys($validCategories));
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $rowData
        ];
    }

    private function saveBook($rowData, $duplicateAction, $validCategories)
    {
        $isbn = preg_replace('/[^0-9xX]/', '', $rowData['ISBN']);
        $existingBook = Book::where('isbn', $isbn)->first();
        
        if ($existingBook && $duplicateAction === 'skip') {
            throw new \Exception('Duplicate ISBN - book already exists');
        }
        
        $bookData = [
            'isbn' => $isbn,
            'title' => trim($rowData['Title']),
            'author' => trim($rowData['Author']),
            'price' => (float)$rowData['Price'],
            'stock_quantity' => (int)$rowData['Stock'],
            'category_id' => $validCategories[$rowData['Category']],
            'description' => $rowData['Description'] ?? null,
        ];
        
        if ($existingBook && $duplicateAction === 'update') {
            $existingBook->update($bookData);
            return $existingBook;
        }
        
        return Book::create($bookData);
    }

    public function getStatus($id)
    {
        try {
            $import = BookImport::with('errorDetails')->findOrFail($id);
            
            return response()->json([
                'status' => $import->status,
                'total_rows' => $import->total_rows,
                'processed_rows' => $import->processed_rows,
                'successful_rows' => $import->successful_rows,
                'failed_rows' => $import->failed_rows,
                'completed_at' => $import->completed_at
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Import not found'], 404);
        }
    }

    public function downloadTemplate()
    {
        $headers = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'];
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility
            fputcsv($file, $headers);
            
            // Add sample data
            fputcsv($file, ['9780141182636', 'The Great Gatsby', 'F. Scott Fitzgerald', '12.99', '10', 'Fiction', 'A classic novel about the Jazz Age']);
            fputcsv($file, ['9780451524935', '1984', 'George Orwell', '9.99', '5', 'Fiction', 'A dystopian social science fiction novel']);
            fputcsv($file, ['9780061120084', 'To Kill a Mockingbird', 'Harper Lee', '14.99', '8', 'Classic', 'A gripping coming-of-age story']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="book_import_template.csv"'
        ]);
    }

    public function downloadErrorReport($id)
    {
        $import = BookImport::with('errorDetails')->findOrFail($id);
        
        $callback = function() use ($import) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Row Number', 'Errors', 'Data']);
            
            foreach ($import->errorDetails as $error) {
                fputcsv($file, [
                    $error->row_number,
                    implode('; ', $error->errors),
                    json_encode($error->row_data, JSON_UNESCAPED_UNICODE)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="import_errors_' . $import->id . '.csv"'
        ]);
    }
}