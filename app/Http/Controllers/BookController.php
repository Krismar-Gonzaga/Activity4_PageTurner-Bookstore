<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        
        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Search by title or author
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Price range filter
        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) == 2) {
                $query->whereBetween('price', [(float)$priceRange[0], (float)$priceRange[1]]);
            }
        }
        
        // In Stock
        if ($request->filled('in_stock') && $request->in_stock == '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Low Stock
        if ($request->filled('low_stock') && $request->low_stock == '1') {
            $query->whereBetween('stock_quantity', [1, 5]);
        }
        
      
        // Rating filter
        if ($request->filled('min_rating')) {
            $minRating = (float)$request->min_rating;
            $query->whereIn('id', function($q) use ($minRating) {
                $q->select('book_id')
                ->from('reviews')
                ->groupBy('book_id')
                ->havingRaw('AVG(rating) >= ?', [$minRating]);
            });
        }
        
        // Year filter
        if ($request->filled('year')) {
            $query->where('published_year', $request->year);
        }
        
        // Sorting
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
            case 'rating_desc':
                // Sort by average rating
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'newest':
                // Sort by published_year (newest first)
                $query->orderBy('published_year', 'desc');
                break;
            case 'oldest':
                // Sort by published_year (oldest first)
                $query->orderBy('published_year', 'asc');
                break;
            default:
                $query->latest();
                break;
        }
        
        $query->with(['category', 'reviews']);
        
        // Get categories with book counts
        $categories = Category::withCount('books')->get();
        
        // Paginate results
        $books = $query->paginate(12)->withQueryString();
        
        return view('books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:20480',
            'pages' => 'nullable|integer|min:1',              
        'publisher' => 'nullable|string|max:255',        
        'language' => 'nullable|string|max:50',           
        'published_year' => 'nullable|integer|min:1000|max:' . date('Y'), 
        ]);
        
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }
        
        Book::create($validated);
        
        return redirect()->route('books.index')
            ->with('success', 'Book added successfully!');
    }

    public function show(Book $book)
    {
        $book->load(['category', 'reviews.user']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'pages' => 'nullable|integer|min:1',             
            'publisher' => 'nullable|string|max:255',        
            'language' => 'nullable|string|max:50',          
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
        ]);
        
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }
        
        $book->update($validated);
        
        return redirect()->route('books.show', $book)
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        
        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }
}