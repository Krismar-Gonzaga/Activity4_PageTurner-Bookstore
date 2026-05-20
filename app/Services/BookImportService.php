<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\BookImport;
use App\Models\BookImportError;
use App\Imports\BooksImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookImportService
{
    protected $chunkSize = 1000;

    public function import($importId, $filePath, $duplicateAction = 'skip')
    {
        $import = BookImport::find($importId);
        $import->update(['status' => 'processing']);

        try {
            Excel::filter('chunk')->import(new BooksImport($importId, $duplicateAction), $filePath);

            Storage::delete($filePath);

            return true;
        } catch (\Exception $e) {
            $import->update([
                'status' => 'failed',
                'errors' => ['message' => $e->getMessage()]
            ]);
            Log::error('Import failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processImport($importId, $filePath, $duplicateAction = 'skip')
    {
        return $this->import($importId, $filePath, $duplicateAction);
    }

    public function validateHeaders($headers, $requiredHeaders = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'])
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

        if (empty($bookData['ISBN'])) {
            $errors[] = 'ISBN is required';
        } else {
            $isbn = preg_replace('/[^0-9xX]/', '', $bookData['ISBN']);
            if (!preg_match('/^(\d{10}|\d{13})$/i', $isbn) && !preg_match('/^\d{9}[\dXx]$/', $isbn)) {
                $errors[] = 'Invalid ISBN format. Must be ISBN-10 or ISBN-13';
            }
        }

        if (empty($bookData['Title'])) {
            $errors[] = 'Title is required';
        } elseif (strlen($bookData['Title']) > 255) {
            $errors[] = 'Title must not exceed 255 characters';
        }

        if (empty($bookData['Author'])) {
            $errors[] = 'Author is required';
        }

        if (empty($bookData['Price'])) {
            $errors[] = 'Price is required';
        } elseif (!is_numeric($bookData['Price'])) {
            $errors[] = 'Price must be numeric';
        } elseif ($bookData['Price'] <= 0) {
            $errors[] = 'Price must be positive';
        } elseif ($bookData['Price'] > 9999.99) {
            $errors[] = 'Price cannot exceed 9999.99';
        }

        if (!isset($bookData['Stock']) || $bookData['Stock'] === '') {
            $errors[] = 'Stock is required';
        } elseif (!is_numeric($bookData['Stock']) || !ctype_digit((string)$bookData['Stock'])) {
            $errors[] = 'Stock must be an integer';
        } elseif ($bookData['Stock'] < 0) {
            $errors[] = 'Stock cannot be negative';
        }

        $validCategories = Category::pluck('id', 'name')->toArray();
        if (empty($bookData['Category'])) {
            $errors[] = 'Category is required';
        } elseif (!isset($validCategories[$bookData['Category']])) {
            $errors[] = 'Category "' . $bookData['Category'] . '" does not exist';
        }

        if (!empty($bookData['Description']) && strlen($bookData['Description']) > 5000) {
            $errors[] = 'Description must not exceed 5000 characters';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $bookData
        ];
    }

    public function downloadTemplate()
    {
        $headers = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $headers);
            fputcsv($file, ['9780141182636', 'The Great Gatsby', 'F. Scott Fitzgerald', '12.99', '10', 'Fiction', 'A classic novel about the Jazz Age']);
            fputcsv($file, ['9780451524935', '1984', 'George Orwell', '9.99', '5', 'Fiction', 'A dystopian social science fiction novel']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="book_import_template.csv"'
        ]);
    }
}