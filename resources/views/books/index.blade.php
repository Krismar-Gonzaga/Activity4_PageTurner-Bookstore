@extends('layouts.app')

@section('title', 'All Books - PageTurner')

@section('header')
    <div class="books-header">
        <div>
            <h1 class="books-title">Browse Books</h1>
            <p class="books-subtitle">Discover our collection of {{ $books->total() }} books</p>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.books.create') }}" class="admin-add-btn">
                    <svg class="admin-add-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Book
                </a>
            @endif
        @endauth
    </div>
@endsection

@section('content')
<style>
    :root {
        --pageturner-primary: #8B4513;
        --pageturner-secondary: #D2691E;
        --pageturner-accent: #F4A460;
        --pageturner-light: #F5EBDC;
        --pageturner-very-light: #FDF8F0;
        --pageturner-dark: #5D4037;
        --pageturner-text: #3E2723;
    }

    /* Main Container with Left/Right Margins */
    .main-container {
        max-width: 1440px;
        margin-left: -3rem;
        
        
    }

    @media (min-width: 640px) {
        .main-container {
            
        }
    }

    @media (min-width: 1024px) {
        .main-container {
            
            
        }
    }

    @media (min-width: 1440px) {
        .main-container {
            
           
        }
    }

    /* Header Styles */
    .books-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .books-title {
        font-size: 1.875rem;
        font-weight: 700;
        font-family: 'Playfair Display', Georgia, serif;
        color: var(--pageturner-light);
    }

    .books-subtitle {
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.5rem;
    }

    .admin-add-btn {
        background: #8B4513;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .admin-add-btn:hover {
        background: #D2691E;
    }

    .admin-add-icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
    }

    /* Filter Section */
    .filter-section {
        background: var(--pageturner-very-light);
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(139,69,19,0.12);
        margin-bottom: 2rem;
    }

    /* Horizontal Filter Layout */
    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .filter-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .filter-row {
            flex-direction: row;
            gap: 1rem;
        }
    }

    .search-wrapper {
        flex: 1;
    }

    .search-input-container {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        padding-left: 0.75rem;
        display: flex;
        align-items: center;
        pointer-events: none;
    }

    .search-icon-svg {
        height: 1.25rem;
        width: 1.25rem;
        color: #9ca3af;
    }

    .search-input {
        padding-left: 2.5rem;
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        height: 2.5rem;
    }

    .search-input:focus {
        outline: none;
        box-shadow: 0 0 0 2px var(--pageturner-accent);
        border-color: var(--pageturner-primary);
    }

    /* Filter Selects */
    .filter-select {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        height: 2.5rem;
        padding: 0 0.75rem;
    }

    @media (min-width: 768px) {
        .filter-select {
            width: 16rem;
        }
    }

    .filter-select:focus {
        outline: none;
        box-shadow: 0 0 0 2px var(--pageturner-accent);
        border-color: var(--pageturner-primary);
    }

    .filter-select.small {
        width: 100%;
    }

    @media (min-width: 768px) {
        .filter-select.small {
            width: 12rem;
        }
    }

    /* Advanced Filters Toggle */
    .advanced-toggle {
        color: #8B4513;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        background: none;
        border: none;
        cursor: pointer;
    }

    .advanced-toggle:hover {
        color: #D2691E;
    }

    .toggle-icon {
        width: 1rem;
        height: 1rem;
        margin-left: 0.25rem;
        transition: transform 0.2s;
    }

    .toggle-icon.rotated {
        transform: rotate(180deg);
    }

    /* Advanced Filters Panel */
    .advanced-panel {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .advanced-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .advanced-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Checkbox Styles */
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .checkbox-input {
        border-radius: 0.25rem;
        border: 1px solid #d1d5db;
        color: var(--pageturner-primary);
    }

    .checkbox-input:focus {
        box-shadow: 0 0 0 2px var(--pageturner-primary);
    }

    .checkbox-text {
        margin-left: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    /* Filter Section Titles */
    .filter-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--pageturner-dark);
        margin-bottom: 0.5rem;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid rgba(139,69,19,0.12);
    }

    .filter-count {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .clear-filters {
        color: #dc2626;
        font-weight: 500;
        text-decoration: none;
    }

    .clear-filters:hover {
        color: #b91c1c;
    }

    .button-group {
        display: flex;
        gap: 0.5rem;
    }

    .apply-btn {
        background: var(--pageturner-primary);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        transition: background 0.3s;
        font-weight: 500;
        display: flex;
        align-items: center;
        border: none;
        cursor: pointer;
    }

    .apply-btn:hover {
        background: var(--pageturner-secondary);
    }

    .apply-icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
    }

    /* Results Summary */
    .results-summary {
        margin-bottom: 1.5rem;
    }

    .summary-card {
        background: var(--pageturner-light);
        border: 1px solid var(--pageturner-accent);
        border-radius: 0.5rem;
        padding: 1rem;
    }

    .summary-content {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }

    .summary-text {
        font-weight: 500;
        color: var(--pageturner-dark);
    }

    .summary-query {
        color: #4b5563;
        margin-left: 0.5rem;
    }

    .filter-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    @media (min-width: 768px) {
        .filter-tags {
            margin-top: 0;
        }
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        background: var(--pageturner-accent);
        color: var(--pageturner-dark);
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    .filter-tag.in-stock {
        background: #d1fae5;
        color: #065f46;
    }

    .filter-tag.rating {
        background: #fef3c7;
        color: #92400e;
    }

    .tag-remove {
        margin-left: 0.5rem;
        color: inherit;
        text-decoration: none;
        opacity: 0.7;
    }

    .tag-remove:hover {
        opacity: 1;
    }

    /* Books Grid */
    .books-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 4.0rem;
    }

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

    /* Pagination */
    .pagination-wrapper {
        margin-top: 2rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 0;
    }

    .empty-icon {
        margin: 0 auto;
        height: 4rem;
        width: 4rem;
        color: #9ca3af;
    }

    .empty-title {
        margin-top: 1rem;
        font-size: 1.125rem;
        font-weight: 500;
        color: #111827;
    }

    .empty-text {
        margin-top: 0.5rem;
        color: #4b5563;
    }

    .empty-action {
        margin-top: 1.5rem;
    }

    .clear-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border: 1px solid transparent;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        color: white;
        background: #8B4513;
        text-decoration: none;
    }

    .clear-btn:hover {
        background: #D2691E;
    }

    /* Margin Utilities */
    .mb-8 {
        margin-bottom: 2rem;
    }

    .mb-6 {
        margin-bottom: 1.5rem;
    }

    .mt-2 {
        margin-top: 0.5rem;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .mt-8 {
        margin-top: 2rem;
    }

    .ml-2 {
        margin-left: 0.5rem;
    }

    .ml-1 {
        margin-left: 0.25rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .py-12 {
        padding-top: 3rem;
        padding-bottom: 3rem;
    }

    .pt-4 {
        padding-top: 1rem;
    }

    .pb-4 {
        padding-bottom: 1rem;
    }

    /* Flex Utilities */
    .flex {
        display: flex;
    }

    .inline-flex {
        display: inline-flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .gap-4 {
        gap: 1rem;
    }

    .gap-6 {
        gap: 1.5rem;
    }

    .space-y-2 > * + * {
        margin-top: 0.5rem;
    }

    .space-y-4 > * + * {
        margin-top: 1rem;
    }

    .space-y-6 > * + * {
        margin-top: 1.5rem;
    }

    /* Grid Utilities */
    .grid {
        display: grid;
    }

    .grid-cols-1 {
        grid-template-columns: repeat(1, 1fr);
    }

    /* Width Utilities */
    .w-full {
        width: 100%;
    }

    .w-4 {
        width: 1rem;
    }

    .w-5 {
        width: 1.25rem;
    }

    .w-16 {
        width: 4rem;
    }

    .h-4 {
        height: 1rem;
    }

    .h-5 {
        height: 1.25rem;
    }

    .h-16 {
        height: 4rem;
    }

    /* Text Utilities */
    .text-sm {
        font-size: 0.875rem;
    }

    .text-lg {
        font-size: 1.125rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-semibold {
        font-weight: 600;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .text-gray-600 {
        color: #4b5563;
    }

    .text-gray-900 {
        color: #111827;
    }

    .text-red-600 {
        color: #dc2626;
    }

    .text-red-800 {
        color: #991b1b;
    }

    .text-green-800 {
        color: #166534;
    }

    .text-yellow-800 {
        color: #854d0e;
    }

    .text-white {
        color: white;
    }

    /* Background Utilities */
    .bg-green-100 {
        background: #dcfce7;
    }

    .bg-yellow-100 {
        background: #fef9c3;
    }

    /* Border Utilities */
    .border {
        border: 1px solid;
    }

    .border-t {
        border-top: 1px solid;
    }

    .border-gray-200 {
        border-color: #e5e7eb;
    }

    .rounded-md {
        border-radius: 0.375rem;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .rounded-xl {
        border-radius: 0.75rem;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    /* Shadow */
    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    /* Transitions */
    .transition-colors {
        transition: all 0.3s;
    }

    .transition-transform {
        transition: transform 0.2s;
    }

    .duration-200 {
        transition-duration: 200ms;
    }

    /* Cursor */
    .cursor-pointer {
        cursor: pointer;
    }

    /* Hover Effects */
    .hover\:bg-\[\#D2691E\]:hover {
        background: #D2691E;
    }

    .hover\:text-\[\#D2691E\]:hover {
        color: #D2691E;
    }

    .hover\:text-red-800:hover {
        color: #991b1b;
    }

    .hover\:text-green-800:hover {
        color: #166534;
    }

    .hover\:text-yellow-800:hover {
        color: #854d0e;
    }

    /* Focus States */
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px var(--pageturner-accent);
    }

    .focus\:border-var\(--pageturner-primary\):focus {
        border-color: var(--pageturner-primary);
    }
</style>

<!-- Alpine.js for Advanced Filters Toggle -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="main-container">
    <!-- Search and Filter Section -->
    <div class="filter-section">
        <form action="{{ route('books.index') }}" method="GET" class="filter-form">
            <!-- Main Filters Row - Horizontal Layout -->
            <div class="filter-row">
                <!-- Search Bar -->
                <div class="search-wrapper">
                    <div class="search-input-container">
                        <div class="search-icon">
                            <svg class="search-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by title, author, or ISBN..."
                               class="search-input">
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select name="category" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->books_count ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sort Options -->
                <div>
                    <select name="sort" class="filter-select">
                        <option value="">Sort by</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
                
                <!-- Price Range -->
                <div>
                    <select name="price_range" class="filter-select small">
                        <option value="">All Prices</option>
                        <option value="0-25" {{ request('price_range') == '0-25' ? 'selected' : '' }}>Under $25</option>
                        <option value="25-50" {{ request('price_range') == '25-50' ? 'selected' : '' }}>$25 - $50</option>
                        <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>$50 - $100</option>
                        <option value="100-500" {{ request('price_range') == '100-500' ? 'selected' : '' }}>Over $100</option>
                    </select>
                </div>
            </div>
            
            <!-- Advanced Filters (Collapsible) -->
            <div x-data="{ showAdvanced: false }">
                <button type="button" 
                        @click="showAdvanced = !showAdvanced"
                        class="advanced-toggle">
                    <span>Advanced Filters</span>
                    <svg class="toggle-icon" 
                        :class="{ 'rotated': showAdvanced }" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="showAdvanced" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="advanced-panel">
                    <div class="advanced-grid">
                        <!-- Stock Status - Now using stock_quantity -->
                        <div>
                            <label class="filter-label">Stock Status</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                        name="in_stock" 
                                        value="1" 
                                        {{ request('in_stock') ? 'checked' : '' }}
                                        class="checkbox-input">
                                    <span class="checkbox-text">In Stock Only</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                        name="low_stock" 
                                        value="1" 
                                        {{ request('low_stock') ? 'checked' : '' }}
                                        class="checkbox-input">
                                    <span class="checkbox-text">Low Stock (1-5 items)</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Rating Filter -->
                        <div>
                            <label class="filter-label">Minimum Rating</label>
                            <select name="min_rating" class="filter-select">
                                <option value="">Any Rating</option>
                                <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4 Stars & Above</option>
                                <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3 Stars & Above</option>
                                <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2 Stars & Above</option>
                            </select>
                        </div>
                        
                        <!-- Publication Year - Using published_year -->
                        <div>
                            <label class="filter-label">Publication Year</label>
                            <input type="number" 
                                name="year" 
                                value="{{ request('year') }}"
                                placeholder="e.g., 2023"
                                min="1900" 
                                max="{{ date('Y') }}"
                                class="search-input">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <div class="filter-count">
                    @if(request()->hasAny(['search', 'category', 'sort', 'price_range', 'in_stock', 'low_stock', 'min_rating', 'year']))
                        <a href="{{ route('books.index') }}" class="clear-filters">
                            Clear All Filters
                        </a>
                    @endif
                </div>
                <div class="button-group">
                    <button type="submit" class="apply-btn">
                        <svg class="apply-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Summary -->
    @if(request()->hasAny(['search', 'category', 'sort', 'price_range', 'in_stock', 'low_stock', 'min_rating', 'year']))
        <div class="results-summary">
            <div class="summary-card">
                <div class="summary-content">
                    <div>
                        <span class="summary-text">
                            {{ $books->total() }} book{{ $books->total() !== 1 ? 's' : '' }} found
                        </span>
                        @if(request('search'))
                            <span class="summary-query">
                                for "{{ request('search') }}"
                            </span>
                        @endif
                    </div>
                    <div class="filter-tags">
                        @if(request('category'))
                            @php
                                $selectedCategory = $categories->firstWhere('id', request('category'));
                            @endphp
                            <span class="filter-tag">
                                Category: {{ $selectedCategory->name ?? 'Unknown' }}
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('category')) }}" 
                                class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif
                        
                        @if(request('in_stock'))
                            <span class="filter-tag in-stock">
                                In Stock Only
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('in_stock')) }}" 
                                class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif
                        
                        @if(request('low_stock'))
                            <span class="filter-tag">
                                Low Stock
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('low_stock')) }}" 
                                class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif
                        
                        @if(request('min_rating'))
                            <span class="filter-tag rating">
                                {{ request('min_rating') }}+ Stars
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('min_rating')) }}" 
                                class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif
                        
                        @if(request('year'))
                            <span class="filter-tag">
                                Year: {{ request('year') }}
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('year')) }}" 
                                class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="books-grid">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="empty-title">No books found</h3>
            <p class="empty-text">
                @if(request()->hasAny(['search', 'category', 'price_range']))
                    No books match your search criteria. Try adjusting your filters.
                @else
                    No books are currently available in our collection.
                @endif
            </p>
            @if(request()->hasAny(['search', 'category', 'price_range']))
                <div class="empty-action">
                    <a href="{{ route('books.index') }}" class="clear-btn">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        
        // Auto-submit for select changes
        const selectFilters = document.querySelectorAll('select[name="sort"], select[name="price_range"], select[name="min_rating"], select[name="category"]');
        
        selectFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                form.submit();
            });
        });
        
        // Optional: Auto-submit for checkbox changes (with debounce)
        const checkboxFilters = document.querySelectorAll('input[type="checkbox"]');
        checkboxFilters.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Update hidden inputs
                if (this.id === 'in_stock_checkbox') {
                    document.getElementById('in_stock_hidden').disabled = this.checked;
                }
                if (this.id === 'low_stock_checkbox') {
                    document.getElementById('low_stock_hidden').disabled = this.checked;
                }
                form.submit();
            });
        });
    });
</script>
@endpush