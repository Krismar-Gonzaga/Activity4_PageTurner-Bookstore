<?php
// app/Services/BookExportService.php

namespace App\Services;

use App\Models\Book;
use App\Models\ExportJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BookExportService
{
    protected $chunkSize = 1000;
    protected $availableFormats = ['xlsx', 'csv', 'pdf'];
    protected $availableFields = [
        'isbn' => 'ISBN',
        'title' => 'Title',
        'author' => 'Author',
        'category' => 'Category',
        'price' => 'Price',
        'stock_quantity' => 'Stock',
        'description' => 'Description',
        'published_year' => 'Published Year',
        'publisher' => 'Publisher',
        'language' => 'Language',
        'pages' => 'Pages',
        'created_at' => 'Created Date',
        'average_rating' => 'Average Rating',
        'reviews_count' => 'Number of Reviews'
    ];

    public function getAvailableFields()
    {
        return $this->availableFields;
    }

    public function export($exportId, $filters, $selectedFields, $format, $userId)
    {
        $export = ExportJob::find($exportId);
        $export->update(['status' => 'processing']);

        try {
            $query = $this->buildQuery($filters);
            $totalRecords = $query->count();
            
            $export->update(['total_records' => $totalRecords]);

            if ($totalRecords > 10000) {
                // Queue for large exports
                return $this->processLargeExport($export, $query, $selectedFields, $format);
            } else {
                // Process small exports directly
                return $this->processExport($export, $query, $selectedFields, $format);
            }
        } catch (\Exception $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function buildQuery($filters)
    {
        $query = Book::query()->with('category');

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('author', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('isbn', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['price_range'])) {
            $priceRange = explode('-', $filters['price_range']);
            if (count($priceRange) == 2) {
                $query->whereBetween('price', [(float)$priceRange[0], (float)$priceRange[1]]);
            }
        }

        if (!empty($filters['in_stock'])) {
            $query->where('stock_quantity', '>', 0);
        }

        if (!empty($filters['low_stock'])) {
            $query->whereBetween('stock_quantity', [1, 5]);
        }

        if (!empty($filters['out_of_stock'])) {
            $query->where('stock_quantity', 0);
        }

        if (!empty($filters['min_rating'])) {
            $minRating = (float)$filters['min_rating'];
            $query->whereHas('reviews', function($q) use ($minRating) {
                $q->select('book_id')
                  ->groupBy('book_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['stock_min'])) {
            $query->where('stock_quantity', '>=', (int)$filters['stock_min']);
        }

        if (!empty($filters['stock_max'])) {
            $query->where('stock_quantity', '<=', (int)$filters['stock_max']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (float)$filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (float)$filters['price_max']);
        }

        // Apply sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        
        switch ($sortField) {
            case 'title':
            case 'author':
            case 'price':
            case 'stock_quantity':
            case 'created_at':
                $query->orderBy($sortField, $sortDirection);
                break;
            case 'category':
                $query->join('categories', 'books.category_id', '=', 'categories.id')
                      ->orderBy('categories.name', $sortDirection)
                      ->select('books.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    protected function processExport($export, $query, $selectedFields, $format)
    {
        $records = $query->get();
        $filePath = $this->generateFile($records, $selectedFields, $format, $export->id);
        
        $filename = 'books_export_' . date('Ymd_His') . '.' . $format;
        
        $export->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'filename' => $filename,
            'completed_at' => now(),
            'processed_records' => $records->count()
        ]);

        return $filePath;
    }

    protected function processLargeExport($export, $query, $selectedFields, $format)
    {
        // Process in chunks
        $filePath = storage_path('app/exports/temp_' . $export->id . '.' . $format);
        $firstChunk = true;
        $processedCount = 0;

        $query->chunk($this->chunkSize, function($chunk) use (&$firstChunk, $selectedFields, $format, $filePath, $export, &$processedCount) {
            $this->appendToFile($chunk, $selectedFields, $format, $filePath, $firstChunk);
            $firstChunk = false;
            
            $processedCount += $chunk->count();
            $export->update([
                'processed_records' => $processedCount,
                'progress' => ($processedCount / $export->total_records) * 100
            ]);
        });

        // Finalize the temp CSV to proper format for xlsx/csv
        $tempFile = $filePath . '.tmp';
        if (file_exists($tempFile)) {
            rename($tempFile, $filePath);
        }

        $filename = 'books_export_' . date('Ymd_His') . '.' . $format;
        
        $export->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'filename' => $filename,
            'completed_at' => now()
        ]);

        return $filePath;
    }

    protected function generateFile($records, $selectedFields, $format, $exportId)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [];
        $column = 'A';
        foreach ($selectedFields as $field) {
            $headers[$column] = $this->availableFields[$field];
            $sheet->setCellValue($column . '1', $this->availableFields[$field]);
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B4513']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ]);
            $column++;
        }

        // Add data rows
        $row = 2;
        foreach ($records as $record) {
            $column = 'A';
            foreach ($selectedFields as $field) {
                $value = $this->getFieldValue($record, $field);
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create directory if not exists
        $directory = storage_path('app/exports');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = "export_{$exportId}_" . date('Ymd_His') . ".{$format}";
        $filepath = $directory . DIRECTORY_SEPARATOR . $filename;

        // Write file based on format
        switch ($format) {
            case 'xlsx':
                $writer = new Xlsx($spreadsheet);
                break;
            case 'csv':
                $writer = new Csv($spreadsheet);
                break;
            case 'pdf':
                $writer = new Pdf($spreadsheet);
                break;
            default:
                throw new \Exception('Unsupported format');
        }
        
        $writer->save($filepath);
        
        // Cleanup
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        return $filepath;
    }

    protected function appendToFile($chunk, $selectedFields, $format, $filePath, $isFirstChunk)
    {
        // For simplicity, we'll use a temporary CSV approach for chunks
        // This can be optimized based on your needs
        $tempFile = $filePath . '.tmp';
        
        if ($isFirstChunk) {
            // Create new file with headers
            $handle = fopen($tempFile, 'w');
            $headers = [];
            foreach ($selectedFields as $field) {
                $headers[] = $this->availableFields[$field];
            }
            fputcsv($handle, $headers);
        } else {
            $handle = fopen($tempFile, 'a');
        }
        
        foreach ($chunk as $record) {
            $row = [];
            foreach ($selectedFields as $field) {
                $row[] = $this->getFieldValue($record, $field);
            }
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        
        // If this is the last chunk, convert to requested format
        // This is simplified - for production, you'd want better handling
    }

    protected function getFieldValue($book, $field)
    {
        switch ($field) {
            case 'category':
                return $book->category ? $book->category->name : '';
            case 'average_rating':
                return number_format($book->average_rating, 1);
            case 'reviews_count':
                return $book->reviews->count();
            case 'created_at':
                return $book->created_at ? $book->created_at->format('Y-m-d H:i:s') : '';
            default:
                return $book->$field ?? '';
        }
    }
}