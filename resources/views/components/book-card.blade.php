@props(['book'])

<style>
    :root {
        --pageturner-primary: #8B4513;
        --pageturner-secondary: #D2691E;
        --pageturner-accent: #F4A460;
        --pageturner-light: #F5EBDC;
        --pageturner-very-light: #FDF8F0;
        --pageturner-dark: #5D4037;
        --pageturner-text: #3E2723;
        --pageturner-gold: #C6A43F;
        --shadow-elegant: 0 10px 30px -5px rgba(90, 60, 50, 0.15);
        --shadow-hover: 0 20px 40px -8px rgba(139, 69, 19, 0.25);
    }

    .book-card {
        background: #ffffff;
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(139, 69, 19, 0.12);
        position: relative;
        width: 200%; 
       
        max-width: 300px;
        box-shadow: var(--shadow-elegant);
    }

    .book-card::before {
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

    .book-card:hover::before {
        opacity: 1;
    }

    .book-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(244, 164, 96, 0.3);
    }

    /* Cover Section */
    .cover-section {
        height: 100px;
        background: linear-gradient(135deg, var(--pageturner-light) 0%, var(--pageturner-accent) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .cover-image {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .book-card:hover .cover-image {
        transform: scale(1.08);
    }

    .no-cover {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1rem;
    }

    .no-cover-icon {
        height: 5rem;
        width: 5rem;
        color: rgba(139, 69, 19, 0.3);
    }

    .no-cover-text {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: var(--pageturner-dark);
        font-weight: 500;
    }

    /* Spine Effect Overlay */
    .spine-effect {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        background: linear-gradient(to bottom, var(--pageturner-primary), var(--pageturner-secondary));
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .book-card:hover .spine-effect {
        opacity: 1;
    }

    /* Stock Badge */
    .stock-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        z-index: 5;
        backdrop-filter: blur(4px);
    }

    .stock-badge.in-stock {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .stock-badge.out-of-stock {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    /* Content Section */
    .content-section {
        padding: 1.5rem;
    }

    /* Title */
    .book-title {
        font-weight: 700;
        font-size: 1.125rem;
        color: #1f2937;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.5rem;
        transition: color 0.3s ease;
    }

    .book-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .book-title a:hover {
        color: var(--pageturner-primary);
        text-decoration: underline;
    }

    /* Author */
    .book-author {
        color: #4b5563;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
        font-weight: 500;
    }

    .author-name {
        font-weight: 600;
        color: var(--pageturner-dark);
    }

    /* Category */
    .category-tag {
        margin-bottom: 0.75rem;
    }

    .category-badge {
        display: inline-block;
        background: var(--pageturner-light);
        color: var(--pageturner-primary);
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        border: 1px solid rgba(244, 164, 96, 0.3);
    }

    /* Price */
    .book-price {
        color: var(--pageturner-primary);
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }

    /* Rating Section */
    .rating-section {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stars-container {
        display: flex;
        align-items: center;
        gap: 0.125rem;
    }

    .star-filled {
        width: 1rem;
        height: 1rem;
        color: #fbbf24;
        fill: currentColor;
    }

    .star-half-container {
        position: relative;
        width: 1rem;
        height: 1rem;
    }

    .star-half-bg {
        position: absolute;
        width: 1rem;
        height: 1rem;
        color: #d1d5db;
        fill: currentColor;
    }

    .star-half-fg {
        position: absolute;
        width: 1rem;
        height: 1rem;
        color: #fbbf24;
        fill: currentColor;
        clip-path: inset(0 50% 0 0);
    }

    .star-empty {
        width: 1rem;
        height: 1rem;
        color: #d1d5db;
        fill: currentColor;
    }

    .rating-count {
        margin-left: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .rating-average {
        margin-left: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--pageturner-primary);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .view-details-btn {
        flex: 1;
        text-align: center;
        background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-secondary));
        color: white;
        max-height: 65px;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(139, 69, 19, 0.2);
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .view-details-btn:hover {
        background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
    }

    .add-to-cart-btn {
        flex: 1;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .add-to-cart-btn:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    /* Login Prompt for Guests */
    .login-prompt-btn {
        flex: 1;
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        font-weight: 600;
        font-size: 0.875rem;
        text-align: center;
        text-decoration: none;
        display: block;
    }

    .login-prompt-btn:hover {
        background: linear-gradient(135deg, #4b5563, #374151);
        transform: translateY(-2px);
    }

    /* Hover Effects */
    .hover-lift {
        transition: transform 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
    }
</style>

<div class="book-card" >
    <!-- Book Cover -->
    <div class="cover-section">
        @if($book->cover_image)
            <img src="{{ asset('storage/' . $book->cover_image) }}" 
                 alt="{{ $book->title }}" 
                 class="cover-image">
        @else
            <div class="no-cover">
                <svg class="no-cover-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
                <span class="no-cover-text">No Cover</span>
            </div>
        @endif
        
        <!-- Book spine effect overlay -->
        <div class="spine-effect"></div>
        
        <!-- Stock Status Badge -->
        @if($book->stock_quantity > 0)
            <div class="stock-badge in-stock">
                In Stock
            </div>
        @else
            <div class="stock-badge out-of-stock">
                Out of Stock
            </div>
        @endif
    </div>

    <!-- Book Info -->
    <div class="content-section">
        <!-- Title -->
        <h3 class="book-title">
            <a href="{{ route('books.show', $book) }}">
                {{ $book->title }}
            </a>
        </h3>
        
        <!-- Author -->
        <p class="book-author">
            by <span class="author-name">{{ $book->author }}</span>
        </p>
        
        <!-- Category -->
        @if($book->category)
            <div class="category-tag">
                <span class="category-badge">
                    {{ $book->category->name }}
                </span>
            </div>
        @endif
        
        <!-- Price -->
        <p class="book-price">
            ${{ number_format($book->price, 2) }}
        </p>

        <!-- Star Rating -->
        <div class="rating-section">
            <div class="stars-container">
                @php
                    $averageRating = $book->average_rating ?? 0;
                    $fullStars = floor($averageRating);
                    $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                @endphp
                
                <!-- Full Stars -->
                @for($i = 1; $i <= $fullStars; $i++)
                    <svg class="star-filled" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                
                <!-- Half Star -->
                @if($hasHalfStar)
                    <div class="star-half-container">
                        <svg class="star-half-bg" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <svg class="star-half-fg" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                @endif
                
                <!-- Empty Stars -->
                @for($i = 1; $i <= $emptyStars; $i++)
                    <svg class="star-empty" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
            
            <!-- Review Count -->
            <span class="rating-count">
                ({{ $book->reviews_count ?? $book->reviews->count() }})
            </span>
            
            <!-- Average Rating Number -->
            @if($averageRating > 0)
                <span class="rating-average">
                    {{ number_format($averageRating, 1) }}
                </span>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('books.show', $book) }}" class="view-details-btn">
                View Details
            </a>
            
            @auth
                @if(!auth()->user()->isAdmin() && $book->stock_quantity > 0)
                    <form action="{{ route('cart.add', $book) }}" method="POST" class="flex-1 flex items-center gap-2">
                        @csrf
                        <input type="number" style="display:none;"
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="{{ $book->stock_quantity }}"
                            class="w-16 px-2 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513]"
                            required>
                        <button type="submit" class="add-to-cart-btn text-sm py-2 px-3">
                            Add to Cart
                        </button>
                    </form>
                @endif
            @else
                @if($book->stock_quantity > 0)
                    <a href="{{ route('login') }}" class="login-prompt-btn">
                        Login to Buy
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>