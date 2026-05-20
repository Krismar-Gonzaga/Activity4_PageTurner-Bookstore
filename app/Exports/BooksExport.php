<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Illuminate\Support\Facades\Storage;

class BooksExport implements FromQuery, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $filters;
    protected $selectedFields;
    protected $totalRecords;
    protected $availableFields;

    public function __construct($filters = [], $selectedFields = [])
    {
        $this->filters = $filters;
        $this->selectedFields = $selectedFields ?: array_keys($this->getAvailableFields());
        $this->availableFields = $this->getAvailableFields();
    }

    public function getAvailableFields(): array
    {
        return [
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
        ];
    }

    public function headings(): array
    {
        return array_map(function ($field) {
            return $this->availableFields[$field] ?? $field;
        }, $this->selectedFields);
    }

    public function query()
    {
        $query = Book::query()->with('category');

        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('author', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('isbn', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        if (!empty($this->filters['category'])) {
            $query->where('category_id', $this->filters['category']);
        }

        if (!empty($this->filters['price_range'])) {
            $priceRange = explode('-', $this->filters['price_range']);
            if (count($priceRange) == 2) {
                $query->whereBetween('price', [(float)$priceRange[0], (float)$priceRange[1]]);
            }
        }

        if (!empty($this->filters['in_stock'])) {
            $query->where('stock_quantity', '>', 0);
        }

        if (!empty($this->filters['low_stock'])) {
            $query->whereBetween('stock_quantity', [1, 5]);
        }

        if (!empty($this->filters['out_of_stock'])) {
            $query->where('stock_quantity', 0);
        }

        if (!empty($this->filters['min_rating'])) {
            $minRating = (float)$this->filters['min_rating'];
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->select('book_id')
                    ->groupBy('book_id')
                    ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['stock_min'])) {
            $query->where('stock_quantity', '>=', (int)$this->filters['stock_min']);
        }

        if (!empty($this->filters['stock_max'])) {
            $query->where('stock_quantity', '<=', (int)$this->filters['stock_max']);
        }

        if (!empty($this->filters['price_min'])) {
            $query->where('price', '>=', (float)$this->filters['price_min']);
        }

        if (!empty($this->filters['price_max'])) {
            $query->where('price', '<=', (float)$this->filters['price_max']);
        }

        $this->totalRecords = $query->count();

        $sortField = $this->filters['sort'] ?? 'created_at';
        $sortDirection = $this->filters['direction'] ?? 'desc';

        switch ($sortField) {
            case 'category':
                $query->join('categories', 'books.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $sortDirection)
                    ->select('books.*');
                break;
            case 'title':
            case 'author':
            case 'price':
            case 'stock_quantity':
            case 'created_at':
                $query->orderBy($sortField, $sortDirection);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function map($book): array
    {
        $row = [];
        foreach ($this->selectedFields as $field) {
            $row[] = $this->getFieldValue($book, $field);
        }
        return $row;
    }

    protected function getFieldValue($book, $field)
    {
        switch ($field) {
            case 'category':
                return $book->category ? $book->category->name : '';
            case 'created_at':
                return $book->created_at ? $book->created_at->format('Y-m-d H:i:s') : '';
            case 'average_rating':
                return number_format($book->average_rating, 1);
            default:
                return $book->$field ?? '';
        }
    }

    public function getTotalRecords(): int
    {
        return $this->totalRecords;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('1:1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B4513']],
                ]);

                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);

                $sheet->freezePane('A2');
            },
        ];
    }
}