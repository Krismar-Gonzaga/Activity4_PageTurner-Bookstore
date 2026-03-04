@extends('layouts.app')

@section('title', 'PageTurner – Discover Your Next Great Read')

@section('content')
    <style>
        /* ============================================= */
        /*        PageTurner – Shared Theme (Home)       */
        /*        Matches the Login Page Styling         */
        /* ============================================= */

        :root {
            --pageturner-primary: #8B4513;
            --pageturner-secondary: #D2691E;
            --pageturner-accent: #F4A460;
            --pageturner-light: #F5EBDC;
            --pageturner-very-light: #FDF8F0;
            --pageturner-dark: #5D4037;
            --pageturner-text: #3E2723;
            --pageturner-success: #2E7D32;
            --pageturner-error: #C62828;
            --pageturner-shadow: 0 10px 30px rgba(139, 69, 19, 0.18);
            --pageturner-border-radius: 14px;
            --pageturner-transition: all 0.32s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-turner-font {
            font-family: 'Playfair Display', 'Georgia', serif;
        }

        .home-page-wrapper {
            min-height: 100vh;
            margin-left: -100px;        
            display: flex;
            align-items: stretch;
            background-color: #f9f5f0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23f9f5f0"/><path d="M20,20 L80,20 L80,80 L20,80 Z" fill="none" stroke="%23e8dfd6" stroke-width="1"/><path d="M30,30 L70,30 L70,70 L30,70 Z" fill="none" stroke="%23e8dfd6" stroke-width="0.5"/></svg>');
            font-family: 'Georgia', 'Times New Roman', serif;
            position: relative;
            overflow: hidden;
        }

        .home-page-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><text x="20" y="40" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.25">📚</text><text x="120" y="90" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.25">📖</text><text x="50" y="150" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.25">🔖</text><text x="150" y="180" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.25">✍️</text></svg>');
            z-index: 0;
        }

        .home-inner {
            width: 100%;
            max-width: 1120px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .section-gap {
            margin-bottom: 4.5rem;
        }

        /* Generic card style (matches login card feel) */
        .content-card {
            background-color: var(--pageturner-light);
            border-radius: var(--pageturner-border-radius);
            box-shadow: var(--pageturner-shadow),
                0 10px 30px rgba(139, 69, 19, 0.18),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(139, 69, 19, 0.15);
            overflow: hidden;
            transition: var(--pageturner-transition);
        }

        .content-card--white {
            background-color: #ffffff;
        }

        .content-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(139, 69, 19, 0.25);
        }

        /* Book spine accent, same as login */
        .spine-accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 12px;
            background: linear-gradient(to bottom, var(--pageturner-primary), var(--pageturner-dark));
            border-radius: var(--pageturner-border-radius) 0 0 var(--pageturner-border-radius);
        }

        /* Simple text helpers using theme colors */
        .pt-text-main {
            color: var(--pageturner-text);
        }

        .pt-text-dark {
            color: var(--pageturner-dark);
        }

        .pt-text-primary {
            color: var(--pageturner-primary);
        }

        .pt-text-secondary {
            color: var(--pageturner-secondary);
        }

        /* Hero */
        .hero-section {
            position: relative;
            padding: 3rem 2.5rem;
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.2fr);
            gap: 2.5rem;
        }

        .hero-heading {
            font-size: clamp(2.4rem, 3vw + 1.4rem, 3.5rem);
            font-weight: 700;
            line-height: 1.1;
            color: var(--pageturner-primary);
            margin-bottom: 0.8rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--pageturner-text);
            opacity: 0.9;
            margin-left: 150px;
            line-height: 1.7;
            max-width: 34rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            margin-top: 1.8rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-secondary));
            color: white;
            padding: 0.9rem 1.9rem;
            border-radius: var(--pageturner-border-radius);
            font-weight: 600;
            font-size: 1rem;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            letter-spacing: 0.4px;
            box-shadow: 0 4px 10px rgba(139, 69, 19, 0.25);
            transition: var(--pageturner-transition);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(139, 69, 19, 0.3);
            background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
        }

        .btn-outline {
            border: 2px solid var(--pageturner-primary);
            color: var(--pageturner-primary);
            padding: 0.85rem 1.8rem;
            border-radius: var(--pageturner-border-radius);
            font-weight: 600;
            font-size: 0.98rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.6);
            transition: var(--pageturner-transition);
        }

        .btn-outline:hover {
            background: var(--pageturner-primary);
            color: white;
            transform: translateY(-2px);
        }

        /* Decorative floating book icon (subtle) */
        @keyframes subtleFloat {
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .float-anim {
            animation: subtleFloat 7s ease-in-out infinite;
        }

        /* Stats section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.4rem;
        }

        .stat-card {
            text-align: center;
            padding: 1.8rem 1.4rem;
        }

        .stat-number {
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--pageturner-primary);
            margin-bottom: 0.4rem;
            transition: var(--pageturner-transition);
        }

        .content-card:hover .stat-number {
            color: var(--pageturner-secondary);
        }

        /* Section headers */
        .section-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.4rem;
            margin-bottom: 1.8rem;
        }

        .section-title {
            font-size: clamp(1.8rem, 2vw + 0.8rem, 2.6rem);
            font-weight: 700;
            color: var(--pageturner-primary);
            margin-bottom: 0.2rem;
        }

        .section-subtitle {
            font-size: 1rem;
            color: var(--pageturner-text);
            opacity: 0.85;
        }

        .section-link {
            font-weight: 600;
            color: var(--pageturner-primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.98rem;
            position: relative;
            padding-bottom: 2px;
            transition: var(--pageturner-transition);
        }

        .section-link::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 1px;
            width: 0;
            background: linear-gradient(to right, transparent, var(--pageturner-accent), transparent);
            transition: width 0.3s ease;
        }

        .section-link:hover {
            color: var(--pageturner-secondary);
        }

        .section-link:hover::after {
            width: 100%;
        }

        /* Categories grid */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 1.2rem;
        }

        .category-item {
            background: var(--pageturner-very-light);
            border-radius: var(--pageturner-border-radius);
            text-align: center;
            padding: 1.2rem 1rem 1.4rem;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 0.4rem;
            color: var(--pageturner-text);
            position: relative;
            transition: var(--pageturner-transition);
        }

        .category-item:hover {
            background: #ffffff;
            transform: translateY(-6px);
            box-shadow: var(--pageturner-shadow);
        }

        .category-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--pageturner-accent), var(--pageturner-secondary));
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(139, 69, 19, 0.25);
            margin-bottom: 0.4rem;
        }

        .category-name {
            font-weight: 600;
            font-size: 0.98rem;
        }

        .category-count {
            font-size: 0.8rem;
            color: #6b7280;
            padding: 0.25rem 0.9rem;
            border-radius: 999px;
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* Featured books grid */
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8.0rem;
        }

        /* Newsletter */
        .newsletter-card {
            position: relative;
            padding: 2.6rem 2.4rem 2.4rem;
            text-align: center;
        }

        .newsletter-title {
            font-size: clamp(1.8rem, 2vw + 0.8rem, 2.4rem);
            font-weight: 700;
            color: var(--pageturner-primary);
            margin-bottom: 0.7rem;
        }

        .newsletter-text {
            font-size: 1rem;
            color: var(--pageturner-text);
            opacity: 0.9;
            max-width: 32rem;
            margin: 0 auto 1.9rem;
        }

        .newsletter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            max-width: 30rem;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1 1 180px;
            padding: 0.9rem 1rem;
            border-radius: 999px;
            border: 2px solid rgba(139, 69, 19, 0.18);
            background-color: #ffffff;
            font-family: 'Georgia', serif;
            font-size: 0.98rem;
            color: var(--pageturner-text);
            transition: var(--pageturner-transition);
        }

        .newsletter-input:focus {
            outline: none;
            border-color: var(--pageturner-accent);
            box-shadow: 0 0 0 3px rgba(244, 164, 96, 0.3);
        }

        .newsletter-note {
            margin-top: 0.9rem;
            font-size: 0.78rem;
            color: #6b7280;
        }

        /* Responsive layouts */
        @media (max-width: 1024px) {
            .hero-section {
                grid-template-columns: minmax(0, 1.7fr);
            }

            .featured-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .categories-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            

            .hero-section {
                padding: 2.2rem 1.6rem;
            }

            .section-gap {
                margin-bottom: 3.5rem;
            }

            .categories-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .featured-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .hero-section {
                padding: 1.9rem 1.5rem 2rem;
            }

            

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1rem;
            }

            .categories-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .featured-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .newsletter-card {
                padding-inline: 1.6rem;
            }
        }
    </style>
    @auth
        @if(!auth()->user()->hasVerifiedEmail())
            <div class="alert alert-warning">
                Please verify your email address. 
                <a href="{{ route('verification.notice') }}">Resend verification email</a>
            </div>
        @endif
    @endauth

    <div class="home-page-wrapper">
        <div class="home-inner">

        <!-- Hero -->
        <section class="content-card content-card--white hero-section section-gap">
            <div class="spine-accent"></div>

            <div>
                <h1 class="hero-heading page-turner-font">
                    PageTurner Bookstore
                </h1>
                <p class="hero-subtitle">
                    Discover stories that stay with you. Curated collections, timeless classics, and fresh voices — all waiting to be read.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('books.index') }}" class="btn-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Explore Books
                    </a>

                    <a href="#categories" class="btn-outline">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Browse Categories
                    </a>
                </div>
            </div>

            <!-- Subtle floating element -->
            <div class="absolute -bottom-6 -right-6 opacity-10 float-anim hidden lg:block">
                <svg class="w-48 h-48" style="color: var(--pageturner-primary);" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
                </svg>
            </div>
        </section>

        

        <!-- Categories -->
        <section id="categories" class="section-gap">
            <div class="section-header">
                <div>
                    <h2 class="section-link">Explore Genres</h2>
                    <p class="section-subtitle">Find your next favorite story</p>
                </div>
                <a href="{{ route('categories.index') }}" class="section-link">
                    All Categories
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="categories-grid">
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category) }}"
                       class="category-item">
                        <div class="category-icon">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="category-name pt-text-main">
                            {{ $category->name }}
                        </h3>
                        <div class="category-count">
                            {{ $category->books_count ?? '?' }} books
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Featured Books -->
        <section class="section-gap">
            <div class="section-header">
                <div>
                    <h2 class="section-link" >Featured Titles</h2>
                    <p class="section-subtitle">Handpicked stories our readers love</p>
                </div>
                <a href="{{ route('books.index') }}" class="section-link">
                    View All Books
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($featuredBooks->isNotEmpty())
                <div class="featured-grid">
                    @foreach($featuredBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            @else
                <div class="content-card p-12 text-center">
                    <div class="text-7xl mb-6 opacity-70">📚</div>
                    <h3 class="text-2xl font-bold pt-text-dark mb-3">Building Our Shelves</h3>
                    <p class="text-lg text-gray-600 max-w-lg mx-auto">
                        Amazing books are on the way. Check back soon or browse categories above.
                    </p>
                </div>
            @endif
        </section>

        

        </div>
    </div>
@endsection