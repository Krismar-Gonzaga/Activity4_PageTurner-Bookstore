@extends('layouts.app')

@section('title', 'Inventory Management - PageTurner')

@section('header')
    <div class="inventory-header">
        <div>
            <h1 class="inventory-title">Inventory Management</h1>
            <p class="inventory-subtitle">Manage your products, stock levels, and categories</p>
        </div>
        
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
    }

    /* Header Styles */
    .inventory-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .inventory-title {
        font-size: 1.875rem;
        font-weight: 700;
        font-family: 'Playfair Display', Georgia, serif;
        color: var(--pageturner-light);
    }

    .inventory-subtitle {
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.5rem;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s;
        text-decoration: none;
    }

    .action-btn.primary {
        background: var(--pageturner-accent);
        color: var(--pageturner-dark);
    }

    .action-btn.primary:hover {
        background: var(--pageturner-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .action-btn.secondary {
        background: white;
        color: var(--pageturner-primary);
        border: 1px solid var(--pageturner-accent);
    }

    .action-btn.secondary:hover {
        background: var(--pageturner-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(139, 69, 19, 0.1);
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(139, 69, 19, 0.2);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-title {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-icon {
        width: 2rem;
        height: 2rem;
        padding: 0.375rem;
        border-radius: 0.5rem;
    }

    .stat-icon.blue { background: #dbeafe; color: #1e40af; }
    .stat-icon.green { background: #d1fae5; color: #065f46; }
    .stat-icon.yellow { background: #fef3c7; color: #92400e; }
    .stat-icon.red { background: #fee2e2; color: #991b1b; }
    .stat-icon.purple { background: #f3e8ff; color: #6b21a8; }

    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

    .stat-change {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
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

    /* Advanced Filters */
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

    .filter-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--pageturner-dark);
        margin-bottom: 0.5rem;
    }

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

    .checkbox-text {
        margin-left: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
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

    .export-btn {
        background: white;
        color: var(--pageturner-primary);
        padding: 0.5rem 1.5rem;
        border-radius: 0.375rem;
        transition: all 0.3s;
        font-weight: 500;
        display: flex;
        align-items: center;
        border: 1px solid var(--pageturner-accent);
        cursor: pointer;
    }

    .export-btn:hover {
        background: var(--pageturner-light);
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

    .filter-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
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

    .filter-tag.low-stock {
        background: #fef3c7;
        color: #92400e;
    }

    .filter-tag.out-of-stock {
        background: #fee2e2;
        color: #991b1b;
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

    /* Inventory Table */
    .inventory-table-container {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(139, 69, 19, 0.1);
        overflow-x: auto;
    }

    .inventory-table {
        width: 100%;
        border-collapse: collapse;
    }

    .inventory-table th {
        background: var(--pageturner-light);
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--pageturner-dark);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid var(--pageturner-accent);
    }

    .inventory-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .inventory-table tbody tr:hover {
        background: var(--pageturner-very-light);
    }

    /* Product Info */
    .product-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .product-image {
        width: 3rem;
        height: 3rem;
        border-radius: 0.375rem;
        object-fit: cover;
    }

    .product-image-placeholder {
        width: 3rem;
        height: 3rem;
        border-radius: 0.375rem;
        background: var(--pageturner-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--pageturner-primary);
        font-size: 0.875rem;
        font-weight: 600;
    }

    .product-details {
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-weight: 600;
        color: #111827;
        text-decoration: none;
    }

    .product-title:hover {
        color: var(--pageturner-primary);
    }

    .product-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .product-category {
        display: inline-block;
        background: var(--pageturner-accent);
        color: var(--pageturner-dark);
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        text-decoration: none;
    }

    /* Stock Badges */
    .stock-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stock-badge.high {
        background: #d1fae5;
        color: #065f46;
    }

    .stock-badge.medium {
        background: #fef3c7;
        color: #92400e;
    }

    .stock-badge.low {
        background: #fee2e2;
        color: #991b1b;
    }

    .stock-quantity {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    /* Price */
    .price {
        font-weight: 600;
        color: #111827;
    }

    /* Rating */
    .rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .rating-stars {
        color: #fbbf24;
        font-size: 1rem;
    }

    .rating-value {
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .table-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.375rem;
        transition: all 0.3s;
        color: #6b7280;
        background: transparent;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .table-action-btn:hover {
        background: var(--pageturner-light);
        color: var(--pageturner-primary);
        transform: translateY(-2px);
    }

    .table-action-btn.delete:hover {
        background: #fee2e2;
        color: #991b1b;
    }

    .table-action-btn svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
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

    /* Pagination */
    .pagination-wrapper {
        margin-top: 2rem;
    }

    /* Quick Actions */
    .quick-actions {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .quick-action-card {
        flex: 1;
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        border: 1px solid rgba(139, 69, 19, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s;
        text-decoration: none;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(139, 69, 19, 0.2);
        border-color: var(--pageturner-accent);
    }

    .quick-action-icon {
        width: 2.5rem;
        height: 2.5rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
    }

    .quick-action-icon.blue { background: #dbeafe; color: #1e40af; }
    .quick-action-icon.green { background: #d1fae5; color: #065f46; }

    .quick-action-content {
        flex: 1;
    }

    .quick-action-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .quick-action-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
    }
</style>

<!-- Alpine.js -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="main-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Products</span>
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_books']) }}</div>
            <div class="stat-change">{{ $stats['total_categories'] }} categories</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Stock</span>
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_stock']) }}</div>
            <div class="stat-change">units in stock</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Low Stock</span>
                <div class="stat-icon yellow">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['low_stock_count'] }}</div>
            <div class="stat-change">products need reorder</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Out of Stock</span>
                <div class="stat-icon red">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['out_of_stock_count'] }}</div>
            <div class="stat-change">need restock</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Inventory Value</span>
                <div class="stat-icon purple">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">₱{{ number_format($stats['total_value'], 2) }}</div>
            <div class="stat-change">total inventory value</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('admin.books.create') }}" class="quick-action-card">
            <div class="quick-action-icon blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Add New Book</div>
                <div class="quick-action-subtitle">Create a new product listing</div>
            </div>
        </a>

        <a href="{{ route('admin.categories.create') }}" class="quick-action-card">
            <div class="quick-action-icon green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div class="quick-action-content">
                <div class="quick-action-title">Add New Category</div>
                <div class="quick-action-subtitle">Create a new product category</div>
            </div>
        </a>
        {{-- Add this to the inventory page header actions --}}
        <div class="header-actions">
            <a href="{{ route('admin.books.import.form') }}" class="action-btn secondary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Books
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="filter-form">
            <!-- Main Filters Row -->
            <div class="filter-row">
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

                <div>
                    <select name="sort" class="filter-select">
                        <option value="">Sort by</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stock: Low to High</option>
                        <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stock: High to Low</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <div>
                    <select name="price_range" class="filter-select small">
                        <option value="">All Prices</option>
                        <option value="0-25" {{ request('price_range') == '0-25' ? 'selected' : '' }}>Under ₱25</option>
                        <option value="25-50" {{ request('price_range') == '25-50' ? 'selected' : '' }}>₱25 - ₱50</option>
                        <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>₱50 - ₱100</option>
                        <option value="100-500" {{ request('price_range') == '100-500' ? 'selected' : '' }}>Over ₱100</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div x-data="{ showAdvanced: {{ request()->hasAny(['in_stock', 'low_stock', 'out_of_stock', 'min_rating', 'year']) ? 'true' : 'false' }} }">
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
                        <!-- Stock Status -->
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
                                <label class="checkbox-label">
                                    <input type="checkbox" 
                                        name="out_of_stock" 
                                        value="1" 
                                        {{ request('out_of_stock') ? 'checked' : '' }}
                                        class="checkbox-input">
                                    <span class="checkbox-text">Out of Stock</span>
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

                        <!-- Year Filter -->
                        <div>
                            <label class="filter-label">Added Year</label>
                            <input type="number" 
                                name="year" 
                                value="{{ request('year') }}"
                                placeholder="e.g., 2024"
                                min="2000" 
                                max="{{ date('Y') }}"
                                class="search-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="filter-count">
                    Showing {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} products
                    @if(request()->hasAny(['search', 'category', 'price_range', 'in_stock', 'low_stock', 'out_of_stock', 'min_rating', 'year']))
                        <a href="{{ route('admin.inventory.index') }}" class="clear-filters ml-2">
                            Clear Filters
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
    @if(request()->hasAny(['search', 'category', 'in_stock', 'low_stock', 'out_of_stock', 'min_rating', 'year']))
        <div class="results-summary">
            <div class="summary-card">
                <div class="summary-content">
                    <div>
                        <span class="summary-text">
                            {{ $books->total() }} product{{ $books->total() !== 1 ? 's' : '' }} found
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
                                In Stock
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('in_stock')) }}" 
                                   class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if(request('low_stock'))
                            <span class="filter-tag low-stock">
                                Low Stock
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('low_stock')) }}" 
                                   class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if(request('out_of_stock'))
                            <span class="filter-tag out-of-stock">
                                Out of Stock
                                <a href="{{ url()->current() . '?' . http_build_query(request()->except('out_of_stock')) }}" 
                                   class="tag-remove">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if(request('min_rating'))
                            <span class="filter-tag">
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

    <!-- Inventory Table -->
    @if($books->count() > 0)
        <div class="inventory-table-container">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock Status</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                        <tr>
                            <td>
                                <div class="product-info">
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                             alt="{{ $book->title }}" 
                                             class="product-image">
                                    @else
                                        <div class="product-image-placeholder">
                                            {{ substr($book->title, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="product-details">
                                        <a href="{{ route('books.show', $book) }}" class="product-title">
                                            {{ $book->title }}
                                        </a>
                                        <span class="product-meta">
                                            {{ $book->author }} | ISBN: {{ $book->isbn }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($book->category)
                                    <a href="{{ route('categories.show', $book->category) }}" class="product-category">
                                        {{ $book->category->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">No category</span>
                                @endif
                            </td>
                            <td>
                                <span class="price">₱{{ number_format($book->price, 2) }}</span>
                            </td>
                            <td>
                                @if($book->stock_quantity > 10)
                                    <div>
                                        <span class="stock-badge high">In Stock</span>
                                        <div class="stock-quantity">{{ $book->stock_quantity }} units</div>
                                    </div>
                                @elseif($book->stock_quantity > 0)
                                    <div>
                                        <span class="stock-badge medium">Low Stock</span>
                                        <div class="stock-quantity">Only {{ $book->stock_quantity }} left!</div>
                                    </div>
                                @else
                                    <div>
                                        <span class="stock-badge low">Out of Stock</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($book->reviews_count > 0)
                                    <div class="rating">
                                        <span class="rating-stars">★</span>
                                        <span class="rating-value">
                                            {{ number_format($book->reviews_avg_rating ?? 0, 1) }}
                                            ({{ $book->reviews_count }})
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No reviews</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('books.show', $book) }}" 
                                       class="table-action-btn" 
                                       title="View">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.books.edit', $book) }}" 
                                       class="table-action-btn" 
                                       title="Edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this book?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-action-btn delete" title="Delete">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="empty-title">No products found</h3>
            <p class="empty-text">
                @if(request()->hasAny(['search', 'category', 'price_range']))
                    No products match your search criteria. Try adjusting your filters.
                @else
                    No products are currently available in your inventory.
                @endif
            </p>
            @if(request()->hasAny(['search', 'category', 'price_range']))
                <div class="empty-action">
                    <a href="{{ route('admin.inventory.index') }}" class="clear-btn">
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    @endif
    {{-- Export Modal --}}
<div x-data="exportModal()" x-init="init()">
    <!-- Export Button -->
    <button @click="openModal()" class="export-btn ml-2">
        <svg class="apply-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export
    </button>

    <!-- Export Modal -->
    <div x-show="isOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="closeModal()">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitExport()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Export Books
                                </h3>
                                
                                <!-- Format Selection -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Export Format
                                    </label>
                                    <div class="flex gap-3">
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="exportFormat" value="xlsx" class="form-radio">
                                            <span class="ml-2">Excel (XLSX)</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="exportFormat" value="csv" class="form-radio">
                                            <span class="ml-2">CSV</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="exportFormat" value="pdf" class="form-radio">
                                            <span class="ml-2">PDF</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Field Selection -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Fields to Export
                                    </label>
                                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded p-3">
                                        <template x-for="(label, field) in availableFields" :key="field">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" 
                                                       x-model="selectedFields" 
                                                       :value="field" 
                                                       class="form-checkbox">
                                                <span class="ml-2 text-sm" x-text="label"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Date Range Filter -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Date Range (Optional)
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="date" 
                                               x-model="dateFrom" 
                                               class="border rounded px-3 py-2 text-sm"
                                               placeholder="From">
                                        <input type="date" 
                                               x-model="dateTo" 
                                               class="border rounded px-3 py-2 text-sm"
                                               placeholder="To">
                                    </div>
                                </div>

                                <!-- Stock Range Filter -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Stock Range (Optional)
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" 
                                               x-model="stockMin" 
                                               class="border rounded px-3 py-2 text-sm"
                                               placeholder="Min stock">
                                        <input type="number" 
                                               x-model="stockMax" 
                                               class="border rounded px-3 py-2 text-sm"
                                               placeholder="Max stock">
                                    </div>
                                </div>

                                <!-- Price Range Filter -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Price Range (Optional)
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" 
                                               x-model="priceMin" 
                                               class="border rounded px-3 py-2 text-sm"
                                               placeholder="Min price"
                                               step="0.01">
                                        <input type="number" 
                                               x-model="priceMax" 
                                               class="border rounded px-3 py-2 text-sm"
                                               placeholder="Max price"
                                               step="0.01">
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div x-show="exporting" class="mt-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Exporting...</span>
                                        <span x-text="exportProgress + '%'"></span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" :style="{ width: exportProgress + '%' }"></div>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div x-show="errorMessage" class="mt-4 p-3 bg-red-100 text-red-700 rounded text-sm" x-text="errorMessage"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :disabled="exporting || selectedFields.length === 0"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-pageturner-primary text-base font-medium text-white hover:bg-pageturner-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pageturner-primary sm:ml-3 sm:w-auto sm:text-sm"
                                :class="{'opacity-50 cursor-not-allowed': exporting || selectedFields.length === 0}">
                            <span x-show="!exporting">Start Export</span>
                            <span x-show="exporting">Processing...</span>
                        </button>
                        <button type="button" 
                                @click="closeModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pageturner-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Exports Panel -->
    <div class="mt-4" x-show="recentExports.length > 0">
        <div class="bg-white rounded-lg shadow p-4">
            <h4 class="font-semibold mb-3">Recent Exports</h4>
            <div class="space-y-2">
                <template x-for="export in recentExports" :key="export.id">
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <div>
                            <p class="text-sm font-medium" x-text="export.filename || 'Export ' + export.id"></p>
                            <p class="text-xs text-gray-500" x-text="formatDate(export.created_at)"></p>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-xs px-2 py-1 rounded" 
                                  :class="{
                                      'bg-yellow-100 text-yellow-800': export.status === 'pending',
                                      'bg-blue-100 text-blue-800': export.status === 'processing',
                                      'bg-green-100 text-green-800': export.status === 'completed',
                                      'bg-red-100 text-red-800': export.status === 'failed'
                                  }"
                                  x-text="export.status">
                            </span>
                            <template x-if="export.status === 'completed'">
                                <a :href="`{{ url('admin/books/export/download') }}/${export.id}`" 
                                   class="text-pageturner-primary hover:text-pageturner-secondary text-sm">
                                    Download
                                </a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function exportModal() {
    return {
        isOpen: false,
        exporting: false,
        exportFormat: 'xlsx',
        selectedFields: ['isbn', 'title', 'author', 'category', 'price', 'stock_quantity'],
        availableFields: {
            'isbn': 'ISBN',
            'title': 'Title',
            'author': 'Author',
            'category': 'Category',
            'price': 'Price',
            'stock_quantity': 'Stock',
            'description': 'Description',
            'published_year': 'Published Year',
            'publisher': 'Publisher',
            'language': 'Language',
            'pages': 'Pages',
            'created_at': 'Created Date',
            'average_rating': 'Average Rating',
            'reviews_count': 'Number of Reviews'
        },
        dateFrom: '',
        dateTo: '',
        stockMin: '',
        stockMax: '',
        priceMin: '',
        priceMax: '',
        exportProgress: 0,
        errorMessage: '',
        exportId: null,
        progressInterval: null,
        recentExports: [],
        
        init() {
            this.loadRecentExports();
            setInterval(() => this.loadRecentExports(), 30000); // Refresh every 30 seconds
        },
        
        openModal() {
            this.isOpen = true;
            this.loadRecentExports();
        },
        
        closeModal() {
            this.isOpen = false;
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }
        },
        
        async submitExport() {
            if (this.selectedFields.length === 0) {
                alert('Please select at least one field to export');
                return;
            }
            
            this.exporting = true;
            this.errorMessage = '';
            
            // Get current filters from the page
            const formData = new FormData();
            formData.append('format', this.exportFormat);
            this.selectedFields.forEach(field => {
                formData.append('fields[]', field);
            });
            
            // Add current filters
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && searchInput.value) formData.append('search', searchInput.value);
            
            const categorySelect = document.querySelector('select[name="category"]');
            if (categorySelect && categorySelect.value) formData.append('category', categorySelect.value);
            
            const priceRange = document.querySelector('select[name="price_range"]');
            if (priceRange && priceRange.value) formData.append('price_range', priceRange.value);
            
            const inStock = document.querySelector('input[name="in_stock"]');
            if (inStock && inStock.checked) formData.append('in_stock', '1');
            
            const lowStock = document.querySelector('input[name="low_stock"]');
            if (lowStock && lowStock.checked) formData.append('low_stock', '1');
            
            const outOfStock = document.querySelector('input[name="out_of_stock"]');
            if (outOfStock && outOfStock.checked) formData.append('out_of_stock', '1');
            
            const minRating = document.querySelector('select[name="min_rating"]');
            if (minRating && minRating.value) formData.append('min_rating', minRating.value);
            
            const sort = document.querySelector('select[name="sort"]');
            if (sort && sort.value) formData.append('sort', sort.value);
            
            // Add custom filters
            if (this.dateFrom) formData.append('date_from', this.dateFrom);
            if (this.dateTo) formData.append('date_to', this.dateTo);
            if (this.stockMin !== '') formData.append('stock_min', this.stockMin);
            if (this.stockMax !== '') formData.append('stock_max', this.stockMax);
            if (this.priceMin !== '') formData.append('price_min', this.priceMin);
            if (this.priceMax !== '') formData.append('price_max', this.priceMax);
            
            try {
                const response = await fetch('{{ route("admin.books.export") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.exportId = data.export_id;
                    this.startPolling();
                } else {
                    this.errorMessage = data.message || 'Export failed';
                    this.exporting = false;
                }
            } catch (error) {
                console.error('Export error:', error);
                this.errorMessage = 'An error occurred while starting the export';
                this.exporting = false;
            }
        },
        
        startPolling() {
            this.progressInterval = setInterval(async () => {
                await this.fetchProgress();
            }, 2000);
        },
        
        async fetchProgress() {
            if (!this.exportId) return;
            
            try {
                const response = await fetch(`{{ url("admin/books/export/status") }}/${this.exportId}`);
                const data = await response.json();
                
                this.exportProgress = data.progress;
                
                if (data.status === 'completed') {
                    clearInterval(this.progressInterval);
                    this.exporting = false;
                    this.loadRecentExports();
                    setTimeout(() => {
                        window.location.href = `{{ url("admin/books/export/download") }}/${this.exportId}`;
                        this.closeModal();
                    }, 1000);
                } else if (data.status === 'failed') {
                    clearInterval(this.progressInterval);
                    this.exporting = false;
                    this.errorMessage = data.error_message || 'Export failed';
                }
            } catch (error) {
                console.error('Error fetching progress:', error);
            }
        },
        
        async loadRecentExports() {
            try {
                const response = await fetch('{{ route("admin.books.export.list") }}');
                const data = await response.json();
                this.recentExports = data.data || [];
            } catch (error) {
                console.error('Error loading exports:', error);
            }
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString();
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
.form-radio, .form-checkbox {
    width: 1rem;
    height: 1rem;
    color: #8B4513;
}
</style>
</div>
@endsection