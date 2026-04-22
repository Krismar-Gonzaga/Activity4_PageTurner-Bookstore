<?php
// app/Services/BookImportService.php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\BookImport;
use App\Models\BookImportError;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookImportService
{
    protected $chunkSize = 1000;
    protected $validCategories = [];

    public function __construct()
    {
        $this->validCategories = Category::pluck('id', 'name')->toArray();
    }

    public function validateHeaders($headers, $requiredHeaders = [
        'ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'
    ])
    {
        $missingHeaders = array_diff($requiredHeaders, $headers);
        
        if (!empty($missingHeaders)) {
            throw new \Exception('Missing required headers: ' . implode(', ', $missingHeaders));
        }
        
        return true;
    }

    public function validateBook($bookData, $rowNumber)
    {
        $errors = [];

        // ISBN Validation (ISBN-10 or ISBN-13)
        if (empty($bookData['ISBN'])) {
            $errors[] = 'ISBN is required';
        } else {
            $isbn = preg_replace('/[^0-9xX]/', '', $bookData['ISBN']);
            if (!preg_match('/^(\d{10}|\d{13})$/i', $isbn) && 
                !preg_match('/^\d{9}[\dXx]$/', $isbn)) {
                $errors[] = 'Invalid ISBN format. Must be ISBN-10 or ISBN-13';
            }
        }

        // Title Validation
        if (empty($bookData['Title'])) {
            $errors[] = 'Title is required';
        } elseif (strlen($bookData['Title']) > 255) {
            $errors[] = 'Title must not exceed 255 characters';
        }

        // Author Validation
        if (empty($bookData['Author'])) {
            $errors[] = 'Author is required';
        }

        // Price Validation
        if (empty($bookData['Price'])) {
            $errors[] = 'Price is required';
        } elseif (!is_numeric($bookData['Price'])) {
            $errors[] = 'Price must be numeric';
        } elseif ($bookData['Price'] <= 0) {
            $errors[] = 'Price must be positive';
        } elseif ($bookData['Price'] > 9999.99) {
            $errors[] = 'Price cannot exceed 9999.99';
        }

        // Stock Validation
        if (!isset($bookData['Stock']) || $bookData['Stock'] === '') {
            $errors[] = 'Stock is required';
        } elseif (!is_numeric($bookData['Stock']) || !ctype_digit((string)$bookData['Stock'])) {
            $errors[] = 'Stock must be an integer';
        } elseif ($bookData['Stock'] < 0) {
            $errors[] = 'Stock cannot be negative';
        }

        // Category Validation
        if (empty($bookData['Category'])) {
            $errors[] = 'Category is required';
        } elseif (!isset($this->validCategories[$bookData['Category']])) {
            $errors[] = 'Category "' . $bookData['Category'] . '" does not exist';
        }

        // Description Validation (optional)
        if (!empty($bookData['Description']) && strlen($bookData['Description']) > 5000) {
            $errors[] = 'Description must not exceed 5000 characters';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $bookData
        ];
    }

    public function processImport($importId, $filePath, $duplicateAction = 'skip')
    {
        $import = BookImport::find($importId);
        $import->update(['status' => 'processing']);

        try {
            $data = $this->readFile($filePath);
            $totalRows = count($data);
            $import->update(['total_rows' => $totalRows]);

            $successfulRows = 0;
            $failedRows = 0;
            $processedRows = 0;

            // Process in chunks
            $chunks = array_chunk($data, $this->chunkSize);
            
            foreach ($chunks as $chunkIndex => $chunk) {
                DB::beginTransaction();
                
                foreach ($chunk as $rowIndex => $row) {
                    $rowNumber = $rowIndex + 2; // +2 for header row and 1-based index
                    
                    $validation = $this->validateBook($row, $rowNumber);
                    
                    if (!$validation['valid']) {
                        $failedRows++;
                        BookImportError::create([
                            'book_import_id' => $importId,
                            'row_number' => $rowNumber,
                            'row_data' => $row,
                            'errors' => $validation['errors']
                        ]);
                        continue;
                    }

                    try {
                        $this->saveBook($validation['data'], $duplicateAction);
                        $successfulRows++;
                    } catch (\Exception $e) {
                        $failedRows++;
                        BookImportError::create([
                            'book_import_id' => $importId,
                            'row_number' => $rowNumber,
                            'row_data' => $row,
                            'errors' => [$e->getMessage()]
                        ]);
                    }
                    
                    $processedRows++;
                    $import->update([
                        'processed_rows' => $processedRows,
                        'successful_rows' => $successfulRows,
                        'failed_rows' => $failedRows
                    ]);
                }
                
                DB::commit();
            }

            $import->update([
                'status' => 'completed',
                'completed_at' => now(),
                'successful_rows' => $successfulRows,
                'failed_rows' => $failedRows
            ]);

            Storage::delete($filePath);
            
            return true;
        } catch (\Exception $e) {
            $import->update([
                'status' => 'failed',
                'errors' => ['message' => $e->getMessage()]
            ]);
            throw $e;
        }
    }

    protected function readFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        if ($extension === 'csv') {
            return $this->readCSV($filePath);
        } elseif ($extension === 'xlsx') {
            return $this->readExcel($filePath);
        }
        
        throw new \Exception('Unsupported file format');
    }

    protected function readCSV($filePath)
    {
        $data = [];
        if (($handle = fopen(storage_path('app/' . $filePath), 'r')) !== false) {
            $headers = fgetcsv($handle);
            $this->validateHeaders($headers);
            
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($headers, $row);
            }
            fclose($handle);
        }
        return $data;
    }

    protected function readExcel($filePath)
    {
        require_once app_path('Helpers/PhpSpreadsheet/autoload.php');
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/' . $filePath));
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $headers = array_shift($rows);
        $this->validateHeaders($headers);
        
        $data = [];
        foreach ($rows as $row) {
            if (!empty(array_filter($row))) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        return $data;
    }

    protected function saveBook($bookData, $duplicateAction)
    {
        $categoryId = $this->validCategories[$bookData['Category']];
        
        $existingBook = Book::where('isbn', $bookData['ISBN'])->first();
        
        if ($existingBook && $duplicateAction === 'skip') {
            throw new \Exception('Duplicate ISBN. Book already exists.');
        }
        
        $bookAttributes = [
            'category_id' => $categoryId,
            'title' => trim($bookData['Title']),
            'author' => trim($bookData['Author']),
            'isbn' => preg_replace('/[^0-9xX]/', '', $bookData['ISBN']),
            'price' => (float)$bookData['Price'],
            'stock_quantity' => (int)$bookData['Stock'],
            'description' => $bookData['Description'] ?? null,
        ];
        
        if ($existingBook && $duplicateAction === 'update') {
            $existingBook->update($bookAttributes);
            return $existingBook;
        }
        
        return Book::create($bookAttributes);
    }
}