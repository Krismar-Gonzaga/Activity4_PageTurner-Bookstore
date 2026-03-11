<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category')
            ->withCount('reviews');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Price range filter
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case '0-25':
                    $query->whereBetween('price', [0, 25]);
                    break;
                case '25-50':
                    $query->whereBetween('price', [25, 50]);
                    break;
                case '50-100':
                    $query->whereBetween('price', [50, 100]);
                    break;
                case '100-500':
                    $query->where('price', '>', 100);
                    break;
            }
        }
        
        // Stock status filters
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }
        
        if ($request->boolean('low_stock')) {
            $query->whereBetween('stock_quantity', [1, 5]);
        }
        
        if ($request->boolean('out_of_stock')) {
            $query->where('stock_quantity', '<=', 0);
        }
        
        // Rating filter
        if ($request->filled('min_rating')) {
            $query->having('reviews_count', '>', 0)
                  ->whereHas('reviews', function($q) use ($request) {
                      $q->select('book_id', \DB::raw('avg(rating) as avg_rating'))
                        ->groupBy('book_id')
                        ->havingRaw('avg(rating) >= ?', [$request->min_rating]);
                  });
        }
        
        // Year filter
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        
        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'stock_asc':
                    $query->orderBy('stock_quantity', 'asc');
                    break;
                case 'stock_desc':
                    $query->orderBy('stock_quantity', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }
        
        $books = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('books')->get();
        
        // Get statistics
        $stats = [
            'total_books' => Book::count(),
            'total_categories' => Category::count(),
            'total_stock' => Book::sum('stock_quantity'),
            'low_stock_count' => Book::whereBetween('stock_quantity', [1, 5])->count(),
            'out_of_stock_count' => Book::where('stock_quantity', '<=', 0)->count(),
            'total_value' => Book::sum(DB::raw('price * stock_quantity')),
        ];
        
        // Use the correct admin inventory view path
        return view('admin.inventory.index', compact('books', 'categories', 'stats'));
    }
}