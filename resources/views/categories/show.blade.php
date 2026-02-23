@extends('layouts.app')

@section('title', $category->name . ' - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">{{ $category->name }}</h1>
            <p class="text-gray-100/80 mt-2">Browse all books in this category</p>
        </div>
        <a href="{{ route('categories.index') }}" 
           class="text-[var(--pageturner-light)] hover:text-[var(--pageturner-accent)] font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
        </a>
    </div>
@endsection


<style>
    /* Books Grid - Vertical Layout */
    .books-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    /* Responsive breakpoints for columns */
    @media (min-width: 640px) {
        .books-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) {
        .books-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .books-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (min-width: 1280px) {
        .books-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Optional: Add spacing between cards */
    .card-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
    }

    /* Make book card take full width of its container */
    .card-wrapper > * {
        width: 100%;
    }

    /* Container for the whole section */
    .books-section {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
    }
</style>

@section('content')
    @if($category->description)
        <div class="bg-emerald-50 rounded-xl p-6 mb-8 border border-emerald-100">
            <h2 class="font-semibold text-emerald-800 mb-2">About This Category</h2>
            <p class="text-emerald-700">{{ $category->description }}</p>
        </div>
    @endif

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Books in {{ $category->name }} style</h2>
            <p class="text-gray-600 mt-1">{{ $books->total() }} books found</p>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                <div class="flex space-x-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Category
                    </a>
                </div>
            @endif
        @endauth
    </div>

    @if($books->count() > 0)
        <div class="books-section">
            <div class="books-grid">
                @foreach($books as $book)
                    <div class="card-wrapper">
                        <x-book-card :book="$book" />
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="text-xl font-bold text-emerald-800 mb-2">No Books in This Category</h3>
            <p class="text-emerald-600">No books have been added to this category yet.</p>
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="mt-6">
                        <a href="{{ route('admin.books.create') }}" 
                        class="inline-flex items-center bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Book
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    @endif
@endsection