@extends('layouts.app')

@section('title', 'My Dashboard – PageTurner')

@section('header')
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">
                Dashboard
            </h1>
            <p class="dashboard-welcome">
                Welcome back, <span class="dashboard-welcome-name">{{ auth()->user()->name }}</span>
            </p>
        </div>

        <div class="dashboard-user-info">
            <div class="user-avatar-wrapper">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="user-name">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<style>
    :root {
        --pageturner-primary:   #8B4513;
        --pageturner-secondary: #D2691E;
        --pageturner-accent:    #F4A460;
        --pageturner-light:     #F5EBDC;
        --pageturner-very-light:#FDF8F0;
        --pageturner-dark:      #5D4037;
        --pageturner-text:      #3E2723;
        --shadow-card:          0 10px 28px rgba(139,69,19,0.12);
        --radius:               14px;
        --transition:           all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header Styles */
    .dashboard-header {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .dashboard-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .dashboard-title {
        font-size: 1.875rem;
        font-weight: 700;
        font-family: 'Playfair Display', Georgia, serif;
        color: var(--pageturner-light);
    }

    @media (min-width: 768px) {
        .dashboard-title {
            font-size: 2.25rem;
        }
    }

    .dashboard-welcome {
        margin-top: 0.5rem;
        font-size: 1.125rem;
        color: var(--pageturner-light);
    }

    .dashboard-welcome-name {
        font-weight: 600;
        color: var(--pageturner-accent);
    }

    .dashboard-user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .user-avatar-wrapper {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.625rem 1rem;
        background: var(--pageturner-light);
        border-radius: 0.75rem;
        border: 1px solid rgba(244, 164, 96, 0.3);
    }

    .user-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 9999px;
        background: linear-gradient(to bottom right, var(--pageturner-accent), var(--pageturner-secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .user-name {
        font-weight: 500;
        color: var(--pageturner-text);
        display: none;
    }

    @media (min-width: 640px) {
        .user-name {
            display: inline;
        }
    }

    /* Page Turner Font Utility */
    .page-turner-font {
        font-family: 'Playfair Display', Georgia, serif;
    }

    /* Dashboard Card Styles */
    .dash-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow-card);
        border: 1px solid rgba(139,69,19,0.07);
        overflow: hidden;
        transition: var(--transition);
        position: relative;
    }

    .dash-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(139,69,19,0.16);
    }

    /* Spine Bar */
    .spine-bar {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        background: linear-gradient(to bottom,
            var(--pageturner-dark),
            var(--pageturner-primary),
            var(--pageturner-secondary));
    }

    .spine-bar.rounded-left {
        border-radius: 0.75rem 0 0 0.75rem;
    }

    /* Button Styles */
    .btn-book {
        background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-secondary));
        color: white;
        padding: 0.9rem 1.8rem;
        border-radius: var(--radius);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(139,69,19,0.24);
        transition: var(--transition);
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
    }

    @media (min-width: 768px) {
        .btn-book {
            font-size: 1.125rem;
        }
    }

    .btn-book:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(139,69,19,0.32);
        background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
    }

    .btn-outline-book {
        border: 2px solid var(--pageturner-primary);
        color: var(--pageturner-primary);
        padding: 0.85rem 1.8rem;
        border-radius: var(--radius);
        font-weight: 600;
        transition: var(--transition);
        text-decoration: none;
        display: inline-block;
        font-size: 1rem;
    }

    @media (min-width: 768px) {
        .btn-outline-book {
            font-size: 1.125rem;
        }
    }

    .btn-outline-book:hover {
        background: var(--pageturner-primary);
        color: white;
        transform: translateY(-3px);
    }

    /* Quick Action Link */
    .quick-action-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: var(--pageturner-very-light);
        border-radius: 10px;
        color: var(--pageturner-text);
        font-weight: 500;
        transition: var(--transition);
        text-decoration: none;
    }

    .quick-action-link:hover {
        background: var(--pageturner-light);
        color: var(--pageturner-primary);
        transform: translateX(4px);
    }

    .quick-action-icon {
        width: 1.5rem;
        height: 1.5rem;
        color: var(--pageturner-primary);
    }

    /* Card Content Styles */
    .card-icon-wrapper {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.75rem;
        background: linear-gradient(to bottom right, var(--pageturner-primary), var(--pageturner-secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .card-icon {
        width: 1.75rem;
        height: 1.75rem;
        color: white;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--pageturner-dark);
        margin-bottom: 0.5rem;
    }

    .card-description {
        color: #4b5563;
        margin-bottom: 1rem;
        line-height: 1.625;
    }

    .card-link {
        color: var(--pageturner-primary);
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        text-decoration: none;
        transition: color 0.3s;
    }

    .card-link:hover {
        color: var(--pageturner-secondary);
    }

    .card-arrow {
        width: 1rem;
        height: 1rem;
        transition: transform 0.3s;
    }

    .card-link:hover .card-arrow {
        transform: translateX(4px);
    }

    /* Welcome Section Styles */
    .welcome-avatar {
        width: 5rem;
        height: 5rem;
        border-radius: 1rem;
        background: linear-gradient(to bottom right, var(--pageturner-primary), var(--pageturner-secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        color: white;
        font-size: 1.875rem;
        font-weight: 700;
    }

    @media (min-width: 768px) {
        .welcome-avatar {
            width: 6rem;
            height: 6rem;
            font-size: 2.25rem;
        }
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Playfair Display', Georgia, serif;
        color: var(--pageturner-dark);
        margin-bottom: 0.75rem;
    }

    @media (min-width: 768px) {
        .welcome-title {
            font-size: 1.875rem;
        }
    }

    .welcome-text {
        font-size: 1.125rem;
        color: #374151;
        margin-bottom: 1.5rem;
        line-height: 1.625;
    }

    /* Admin Section Styles */
    .admin-title {
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Playfair Display', Georgia, serif;
        color: var(--pageturner-dark);
        margin-bottom: 1.5rem;
    }

    /* Layout Spacing */
    .dashboard-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .dashboard-content {
            gap: 2.5rem;
        }
    }

    .cards-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 640px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .cards-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .flex-wrap-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .admin-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    @media (min-width: 768px) {
        .admin-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .admin-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Card Padding */
    .card-padding {
        padding: 1.5rem;
    }

    @media (min-width: 768px) {
        .card-padding {
            padding: 1.75rem;
        }
    }

    .large-card-padding {
        padding: 2rem;
    }

    @media (min-width: 768px) {
        .large-card-padding {
            padding: 2.5rem;
        }
    }

    /* Flex Layouts */
    .flex-row-start {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .flex-row-start {
            flex-direction: row;
            gap: 2.5rem;
        }
    }

    .flex-1 {
        flex: 1;
    }
</style>

<div class="dashboard-content">

    <!-- Quick Access Cards -->
    <div class="cards-grid">
        <div class="dash-card card-padding">
            <div class="spine-bar"></div>
            <div class="flex-row-start" style="gap: 1.25rem;">
                <div class="card-icon-wrapper" style="background: linear-gradient(to bottom right, var(--pageturner-primary), var(--pageturner-secondary));">
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h3 class="card-title">Browse Books</h3>
                    <p class="card-description">Discover new titles and classics</p>
                    <a href="{{ route('books.index') }}" class="card-link">
                        Explore Library
                        <svg class="card-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="dash-card card-padding">
            <div class="spine-bar"></div>
            <div class="flex-row-start" style="gap: 1.25rem;">
                <div class="card-icon-wrapper" style="background: linear-gradient(to bottom right, var(--pageturner-accent), var(--pageturner-secondary));">
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="card-title">My Orders</h3>
                    <p class="card-description">Track purchases & history</p>
                    <a href="{{ route('orders.index') }}" class="card-link">
                        View Orders
                        <svg class="card-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="dash-card card-padding">
            <div class="spine-bar"></div>
            <div class="flex-row-start" style="gap: 1.25rem;">
                <div class="card-icon-wrapper" style="background: linear-gradient(to bottom right, var(--pageturner-dark), var(--pageturner-primary));">
                    <svg class="card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="card-title">Profile</h3>
                    <p class="card-description">Account settings & preferences</p>
                    <a href="{{ route('profile.edit') }}" class="card-link">
                        Manage Profile
                        <svg class="card-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome / Main Action Area -->
    <div class="dash-card large-card-padding">
        <div class="spine-bar rounded-left"></div>

        <div class="flex-row-start">
            <div class="welcome-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div class="flex-1">
                <h2 class="welcome-title">
                    Hello{{ strpos(auth()->user()->name, ' ') !== false ? ', ' . explode(' ', auth()->user()->name)[0] : '' }}!
                </h2>
                <p class="welcome-text">
                    You're now part of the PageTurner community. Start exploring books, manage your orders, or update your reading preferences.
                </p>

                <div class="flex-wrap-group">
                    <a href="{{ route('books.index') }}" class="btn-book">
                        Browse Books
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn-outline-book">
                        Explore Categories
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Admin Section -->
    <div class="dash-card large-card-padding">
        <div class="spine-bar rounded-left"></div>

        <h3 class="admin-title">
            Admin Quick Actions
        </h3>

        <div class="admin-grid">
            <a href="{{ route('admin.books.create') }}" class="quick-action-link">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add New Book</span>
            </a>

            <a href="{{ route('admin.categories.create') }}" class="quick-action-link">
                <svg class="quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Add New Category</span>
            </a>

            <!-- You can easily add more admin shortcuts here -->
        </div>
    </div>
    @endif

</div>
@endsection