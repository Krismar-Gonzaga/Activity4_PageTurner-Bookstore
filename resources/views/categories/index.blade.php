@extends('layouts.app')

@section('title', 'Categories - PageTurner')

@section('header')
    <div class="categories-header">
        <div>
            <h1 class="categories-title">Book Categories</h1>
            <p class="categories-subtitle">Browse books by category</p>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.categories.create') }}" class="admin-add-btn">
                    <svg class="admin-add-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Category
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
        --shadow-elegant: 0 10px 30px -5px rgba(139, 69, 19, 0.15);
        --shadow-hover: 0 20px 40px -8px rgba(139, 69, 19, 0.25);
    }

    /* Header Styles */
    .categories-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .categories-title {
        font-size: 1.875rem;
        font-weight: 700;
        font-family: 'Playfair Display', Georgia, serif;
        color: var(--pageturner-light);
    }

    .categories-subtitle {
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.5rem;
    }

    .admin-add-btn {
        background: var(--pageturner-primary);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .admin-add-btn:hover {
        background: var(--pageturner-secondary);
    }

    .admin-add-icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
    }

    /* Category Card */
    .category-card {
        background: #ffffff;
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(244, 164, 96, 0.3);
        position: relative;
        box-shadow: var(--shadow-elegant);
        padding: 1.5rem;
    }

    .category-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-accent));
        border-radius: 6px 0 0 6px;
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 10;
    }

    .category-card:hover::before {
        opacity: 1;
    }

    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(244, 164, 96, 0.6);
    }

    /* Category Header */
    .category-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .category-icon-wrapper {
        width: 3rem;
        height: 3rem;
        background: var(--pageturner-light);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(139, 69, 19, 0.1);
    }

    .category-card:hover .category-icon-wrapper {
        background: linear-gradient(135deg, var(--pageturner-light), var(--pageturner-accent));
        transform: scale(1.05);
    }

    .category-icon {
        width: 1.5rem;
        height: 1.5rem;
        color: var(--pageturner-primary);
        transition: all 0.3s ease;
    }

    .category-card:hover .category-icon {
        color: var(--pageturner-secondary);
        transform: rotate(5deg);
    }

    .category-info {
        flex: 1;
    }

    .category-name {
        font-weight: 700;
        font-size: 1.125rem;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .category-name a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .category-name a:hover {
        color: var(--pageturner-primary);
        text-decoration: underline;
    }

    .category-book-count {
        font-size: 0.875rem;
        color: #6b7280;
        display: flex;
        align-items: center;
    }

    .book-count-number {
        font-weight: 600;
        color: var(--pageturner-primary);
        margin-left: 0.25rem;
    }

    /* Category Description */
    .category-description {
        color: #4b5563;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
        padding-left: 0.25rem;
        border-left: 2px solid transparent;
        transition: border-color 0.3s ease;
    }

    .category-card:hover .category-description {
        border-left-color: var(--pageturner-accent);
    }

    /* Category Footer */
    .category-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(244, 164, 96, 0.2);
    }

    .view-link {
        color: var(--pageturner-primary);
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.3s ease;
    }

    .view-link:hover {
        color: var(--pageturner-secondary);
        transform: translateX(4px);
    }

    .view-arrow {
        width: 1rem;
        height: 1rem;
        transition: transform 0.3s ease;
    }

    .view-link:hover .view-arrow {
        transform: translateX(2px);
    }

    /* Admin Actions */
    .admin-actions {
        display: flex;
        gap: 0.75rem;
    }

    .edit-btn {
        color: var(--pageturner-primary);
        transition: all 0.3s ease;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
    }

    .edit-btn:hover {
        color: var(--pageturner-secondary);
        transform: scale(1.1);
        background: rgba(244, 164, 96, 0.1);
    }

    .delete-btn {
        color: #ef4444;
        transition: all 0.3s ease;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
    }

    .delete-btn:hover {
        color: #b91c1c;
        transform: scale(1.1);
        background: rgba(239, 68, 68, 0.1);
    }

    .action-icon {
        width: 1.25rem;
        height: 1.25rem;
    }

    /* Grid Layout */
    .categories-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 640px) {
        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) {
        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .categories-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 2rem;
    }

    /* Empty State */
    .empty-state {
        background: var(--pageturner-light);
        border: 1px solid var(--pageturner-accent);
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
    }

    .empty-icon {
        width: 4rem;
        height: 4rem;
        color: var(--pageturner-primary);
        margin: 0 auto 1rem;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--pageturner-dark);
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: var(--pageturner-primary);
        margin-bottom: 1rem;
    }

    .empty-btn {
        display: inline-flex;
        align-items: center;
        background: var(--pageturner-primary);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
        text-decoration: none;
    }

    .empty-btn:hover {
        background: var(--pageturner-secondary);
    }

    .empty-btn-icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
    }

    /* Utility Classes */
    .mt-8 {
        margin-top: 2rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .space-x-2 > * + * {
        margin-left: 0.5rem;
    }

    .mr-2 {
        margin-right: 0.5rem;
    }

    .ml-2 {
        margin-left: 0.5rem;
    }
</style>

    @if($categories->count() > 0)
        <div class="categories-grid">
            @foreach($categories as $category)
                <div class="category-card">
                    <div class="category-header">
                        <div class="category-icon-wrapper">
                            <svg class="category-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="category-info">
                            <h3 class="category-name">
                                <a href="{{ route('categories.show', $category) }}">
                                    {{ $category->name }}
                                </a>
                            </h3>
                            <p class="category-book-count">
                                <span class="book-count-number">{{ $category->books_count }}</span> books
                            </p>
                        </div>
                    </div>
                    
                    @if($category->description)
                        <p class="category-description">
                            {{ $category->description }}
                        </p>
                    @endif
                    
                    <div class="category-footer">
                        <a href="{{ route('categories.show', $category) }}" class="view-link">
                            View Books
                            <svg class="view-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        
                        @auth
                            @if(auth()->user()->isAdmin())
                                <div class="admin-actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="edit-btn">
                                        <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">
                                            <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $categories->links() }}
        </div>
    @else
        <div class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <h3 class="empty-title">No Categories Available</h3>
            <p class="empty-text">Start by adding your first book category.</p>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.categories.create') }}" class="empty-btn">
                        <svg class="empty-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add First Category
                    </a>
                @endif
            @endauth
        </div>
    @endif
@endsection