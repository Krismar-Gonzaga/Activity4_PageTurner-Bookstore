
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* Profile picture container */
    .profile-picture-container {
        position: relative;
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Profile picture image */
    .profile-picture-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.2s ease;
    }

    .profile-picture-container img:hover {
        transform: scale(1.05);
    }

    /* Fallback initials styling */
    .fallback-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-secondary));
        color: white;
        font-weight: bold;
        font-size: 0.875rem;
    }

    /* Error state - hide broken image and show initials */
    img[onerror] {
        display: none;
    }

    img[onerror] + .fallback-initials {
        display: flex !important;
    }
</style>

<nav class="relative z-20 border-b border-black/10 bg-gradient-to-r from-[color-mix(in_srgb,var(--pageturner-primary)_90%,#000_10%)] to-[color-mix(in_srgb,var(--pageturner-secondary)_85%,#000_15%)] text-black shadow-md">
    

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex justify-between h-16 items-center">
            <!-- Left Side: Logo & Navigation Links -->
            <div class="flex items-center">
                <!-- Logo with book spine accent -->
                <a href="{{ route('home') }}" class="group relative flex items-center gap-2 pr-4 pl-3 py-2 rounded-full bg-white/5 hover:bg-white/10 transition-colors duration-200">
                    <span class="absolute left-0 inset-y-1 w-1.5 rounded-full bg-gradient-to-b from-[var(--pageturner-light)] to-[var(--pageturner-accent)] shadow-sm"></span>
                    <svg class="w-6 h-6 text-[var(--pageturner-light)]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="page-turner-font text-white tracking-wide text-sm sm:text-base">
                        PageTurner
                    </span>
                </a>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden sm:flex ml-8 space-x-1 text-sm font-medium" style="margin-left: 200px;">
                    <a href="{{ route('home') }}" 
                       class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('home') ? 'bg-white/15' : '' }}">
                        Home
                    </a>

                    <a href="{{ route('books.index') }}"
                       class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('books.*') ? 'bg-white/15' : '' }}">
                        Books
                    </a>

                    <a href="{{ route('categories.index') }}"
                       class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('categories.*') ? 'bg-white/15' : '' }}">
                        Categories
                    </a>

                    <a href="{{ route('orders.index') }}"
                        class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('orders.*') ? 'bg-white/15' : '' }}">
                        Orders
                    </a>

                    

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.books.create') }}" 
                               class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('admin.books.*') ? 'bg-white/15' : '' }}">
                                Add Book
                            </a>

                            <a href="{{ route('admin.categories.create') }}" 
                               class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('admin.categories.*') ? 'bg-white/15' : '' }}">
                                Add Category
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side: User Actions -->
            <div class="flex items-center space-x-3 text-sm">
                @guest
                    <!-- Guest Links -->
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 rounded-full hover:bg-white/10 transition duration-200 text-white ">
                        Login
                    </a>

                    <a href="{{ route('register') }}" 
                       class="px-4 py-2 rounded-full font-semibold bg-[var(--pageturner-accent)] text-[var(--pageturner-dark)] hover:bg-[var(--pageturner-light)] hover:text-[var(--pageturner-primary)] shadow-sm transition duration-200">
                        Register
                    </a>
                @endguest

                @auth
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('admin.*') ? 'bg-white/15' : '' }}">
                            Admin Dashboard
                        </a>
                    @else
                        <!-- Add Cart button here -->
                        <a href="{{ route('cart.index') }}" 
                        class="px-3 py-2 rounded-full transition duration-200 text-white hover:bg-white/10 {{ request()->routeIs('cart.*') ? 'bg-white/15' : '' }} flex items-center gap-1 relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Cart
                            <span id="cart-count-desktop" class="ml-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display: none;">0</span>
                        </a>
                    @endif

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <!-- Dropdown Trigger -->
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 bg-black/15 rounded-full hover:bg-black/25 transition duration-200 focus:outline-none">
                            <div class="relative w-10 h-10 rounded-full overflow-hidden ring-2 ring-[var(--pageturner-accent)] ring-offset-1 ring-offset-[var(--pageturner-primary)] bg-gradient-to-br from-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]">
                                @php
                                    $user = auth()->user();
                                    $profilePicture = $user->profile_picture;
                                    $hasPicture = $profilePicture && file_exists(public_path('storage/' . $profilePicture));
                                @endphp
                                
                                @if($hasPicture)
                                    <img src="{{ asset('storage/' . $profilePicture) }}" 
                                        alt="{{ $user->name }}" 
                                        class="w-full h-full object-cover object-center"
                                        loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="hidden xl:inline font-medium text-white">
                                {{ $user->name }}
                            </span>
                            <svg class="w-4 h-4 text-[#8B4513] transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-[var(--pageturner-accent)]/30"
                            style="display: none;">
                            
                            <!-- Profile Link -->
                            <a href="{{ route('profile.edit') }}" 
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-[var(--pageturner-light)] hover:text-[var(--pageturner-primary)] transition duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile
                            </a>

                            <!-- Admin Links (if user is admin) -->
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('admin.books.create') }}" 
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-[var(--pageturner-light)] hover:text-[var(--pageturner-primary)] transition duration-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Book
                                </a>
                                <a href="{{ route('admin.categories.create') }}" 
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-[var(--pageturner-light)] hover:text-[var(--pageturner-primary)] transition duration-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Add Category
                                </a>
                            @endif

                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-[var(--pageturner-light)] hover:text-red-600 transition duration-200 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="md:hidden hidden py-4 space-y-2 border-t border-white/15">
            <a href="{{ route('home') }}" 
               class="block px-4 py-2 rounded-md hover:bg-white/10 text-white transition duration-200 {{ request()->routeIs('home') ? 'bg-white/15' : '' }}">
                Home
            </a>

            <a href="{{ route('books.index') }}"
               class="block px-4 py-2 rounded-md hover:bg-white/10 text-white transition duration-200 {{ request()->routeIs('books.*') ? 'bg-white/15' : '' }}">
                Books
            </a>

            <a href="{{ route('orders.index') }}"
                class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 text-white {{ request()->routeIs('orders.*') ? 'bg-white/15' : '' }}">
                Orders
            </a>

            @auth
                <a href="{{ route('orders.index') }}" 
                   class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200 {{ request()->routeIs('orders.*') ? 'bg-white/15' : '' }}">
                    Orders
                </a>

                <!-- Add Cart button here -->
                <a href="{{ route('cart.index') }}" 
                class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('cart.*') ? 'bg-white/15' : '' }} flex items-center gap-1 relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Cart
                    <span id="cart-count-mobile" class="ml-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" style="display: none;">0</span>
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200 {{ request()->routeIs('profile.*') ? 'bg-white/15' : '' }}">
                    Profile
                </a>

                <div class="px-4 py-2 text-sm text-[var(--pageturner-light)] bg-black/15 rounded-md">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-[var(--pageturner-accent)] rounded-full flex items-center justify-center text-[var(--pageturner-dark)] font-bold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span>Welcome, {{ auth()->user()->name }}</span>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.books.create') }}" 
                       class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200 {{ request()->routeIs('admin.books.*') ? 'bg-white/15' : '' }}">
                        Add Book
                    </a>

                    <a href="{{ route('admin.categories.create') }}" 
                       class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-white/15' : '' }}">
                        Add Category
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left px-4 py-2 rounded-md hover:bg-white/10 transition duration-200">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" 
                   class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200">
                    Login
                </a>

                <a href="{{ route('register') }}" 
                   class="block px-4 py-2 rounded-md font-semibold bg-[var(--pageturner-accent)] text-[var(--pageturner-dark)] hover:bg-[var(--pageturner-light)] hover:text-[var(--pageturner-primary)] transition duration-200">
                    Register
                </a>
            @endguest
        </div>
    </div>
</nav>

<!-- JavaScript for Mobile Menu Toggle -->
<script>
    

    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuText = document.getElementById('menu-text');
        
        if (mobileMenuButton && mobileMenu && menuText) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                
                // Change button text based on menu state
                if (mobileMenu.classList.contains('hidden')) {
                    menuText.textContent = 'Menu';
                } else {
                    menuText.textContent = 'Close';
                }
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                    menuText.textContent = 'Menu';
                }
            });
        }
        
    });

    
</script>
