<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\BookImport;
use App\Models\BookImportError;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BooksImport implements OnEachRow, WithHeadingRow, WithChunkReading, WithBatchInserts, WithEvents
{
    protected $importId;
    protected $duplicateAction;
    protected $validCategories;
    protected $successfulRows = 0;
    protected $failedRows = 0;
    protected $rowNumber = 1;

    public function __construct($importId, $duplicateAction = 'skip')
    {
        $this->importId = $importId;
        $this->duplicateAction = $duplicateAction;
        $this->validCategories = Category::pluck('id', 'name')->toArray();
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $import = BookImport::find($this->importId);
                $import->update(['status' => 'processing']);
            },
            AfterImport::class => function (AfterImport $event) {
                $import = BookImport::find($this->importId);
                $import->update([
                    'status' => 'completed',
                    'successful_rows' => $this->successfulRows,
                    'failed_rows' => $this->failedRows,
                    'completed_at' => now()
                ]);
            },
        ];
    }

    public function onRow(Row $row)
    {
        $this->rowNumber++;
        $rowData = $row->toArray();

        $validation = $this->validateRow($rowData);

        if (!$validation['valid']) {
            $this->failedRows++;
            BookImportError::create([
                'book_import_id' => $this->importId,
                'row_number' => $this->rowNumber,
                'row_data' => $rowData,
                'errors' => $validation['errors']
            ]);
            return;
        }

        try {
            DB::beginTransaction();
            $this->saveBook($validation['data']);
            $this->successfulRows++;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->failedRows++;
            BookImportError::create([
                'book_import_id' => $this->importId,
                'row_number' => $this->rowNumber,
                'row_data' => $rowData,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    protected function validateRow($rowData)
    {
        $errors = [];
        $requiredHeaders = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category'];

        foreach ($requiredHeaders as $required) {
            if (empty($rowData[$required])) {
                $errors[] = "{$required} is required";
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        if (empty($rowData['ISBN'])) {
            $errors[] = 'ISBN is required';
        } else {
            $isbn = preg_replace('/[^0-9xX]/', '', $rowData['ISBN']);
            if (!preg_match('/^(\d{10}|\d{13})$/i', $isbn) && !preg_match('/^\d{9}[\dXx]$/', $isbn)) {
                $errors[] = 'Invalid ISBN format (must be ISBN-10 or ISBN-13)';
            }
        }

        if (empty($rowData['Title'])) {
            $errors[] = 'Title is required';
        } elseif (strlen($rowData['Title']) > 255) {
            $errors[] = 'Title must not exceed 255 characters';
        }

        if (empty($rowData['Author'])) {
            $errors[] = 'Author is required';
        }

        if (empty($rowData['Price'])) {
            $errors[] = 'Price is required';
        } elseif (!is_numeric($rowData['Price'])) {
            $errors[] = 'Price must be numeric';
        } elseif ($rowData['Price'] <= 0) {
            $errors[] = 'Price must be greater than 0';
        } elseif ($rowData['Price'] > 9999.99) {
            $errors[] = 'Price cannot exceed 9999.99';
        }

        if (!isset($rowData['Stock']) || $rowData['Stock'] === '') {
            $errors[] = 'Stock is required';
        } elseif (!is_numeric($rowData['Stock']) || !ctype_digit((string)$rowData['Stock'])) {
            $errors[] = 'Stock must be a positive integer';
        } elseif ($rowData['Stock'] < 0) {
            $errors[] = 'Stock cannot be negative';
        }

        if (empty($rowData['Category'])) {
            $errors[] = 'Category is required';
        } elseif (!isset($this->validCategories[$rowData['Category']])) {
            $errors[] = 'Category "' . $rowData['Category'] . '" does not exist';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $rowData
        ];
    }

    protected function saveBook($rowData)
    {
        $isbn = preg_replace('/[^0-9xX]/', '', $rowData['ISBN']);
        $existingBook = Book::where('isbn', $isbn)->first();

        if ($existingBook && $this->duplicateAction === 'skip') {
            throw new \Exception('Duplicate ISBN - book already exists');
        }

        $bookData = [
            'isbn' => $isbn,
            'title' => trim($rowData['Title']),
            'author' => trim($rowData['Author']),
            'price' => (float)$rowData['Price'],
            'stock_quantity' => (int)$rowData['Stock'],
            'category_id' => $this->validCategories[$rowData['Category']],
            'description' => $rowData['Description'] ?? null,
        ];

        if ($existingBook && $this->duplicateAction === 'update') {
            $existingBook->update($bookData);
            return $existingBook;
        }

        return Book::create($bookData);
    }
}