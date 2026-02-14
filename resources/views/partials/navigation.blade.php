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
                    <span class="page-turner-font tracking-wide text-sm sm:text-base">
                        PageTurner
                    </span>
                </a>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden sm:flex ml-8 space-x-1 text-sm font-medium" style="margin-left: 200px;">
                    <a href="{{ route('home') }}" 
                       class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('home') ? 'bg-white/15' : '' }}">
                        Home
                    </a>

                    <a href="{{ route('books.index') }}"
                       class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('books.*') ? 'bg-white/15' : '' }}">
                        Books
                    </a>

                    <a href="{{ route('categories.index') }}"
                       class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('categories.*') ? 'bg-white/15' : '' }}">
                        Categories
                    </a>

                    <a href="{{ route('orders.index') }}"
                        class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('orders.*') ? 'bg-white/15' : '' }}">
                        Orders
                    </a>

                    

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.books.create') }}" 
                               class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('admin.books.*') ? 'bg-white/15' : '' }}">
                                Add Book
                            </a>

                            <a href="{{ route('admin.categories.create') }}" 
                               class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('admin.categories.*') ? 'bg-white/15' : '' }}">
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
                       class="px-4 py-2 rounded-full hover:bg-white/10 transition duration-200">
                        Login
                    </a>

                    <a href="{{ route('register') }}" 
                       class="px-4 py-2 rounded-full font-semibold bg-[var(--pageturner-accent)] text-[var(--pageturner-dark)] hover:bg-[var(--pageturner-light)] hover:text-[var(--pageturner-primary)] shadow-sm transition duration-200">
                        Register
                    </a>
                @endguest

                @auth
                    <!-- Authenticated User Links -->
                    <a href="{{ route('orders.index') }}" 
                      class="hidden sm:inline-flex px-3 py-2 rounded-full hover:bg-white/10 transition duration-200 {{ request()->routeIs('orders.*') ? 'bg-white/15' : '' }}">
                        Orders
                    </a>

                    

                    <!-- Add Cart button here -->
                    <a href="{{ route('cart.index') }}" 
                    class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('cart.*') ? 'bg-white/15' : '' }} flex items-center gap-1 relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Cart
                        
                    </a>

                    <a href="{{ route('profile.edit') }}" 
                      class="hidden sm:inline-flex px-3 py-2 rounded-full hover:bg-white/10 transition duration-200 {{ request()->routeIs('profile.*') ? 'bg-white/15' : '' }}">
                        Profile
                    </a>

                    <div class="flex items-center gap-2 px-3 py-1.5 bg-black/15 rounded-full">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-[var(--pageturner-accent)] text-[var(--pageturner-dark)] font-bold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="hidden xl:inline font-medium">
                            {{ auth()->user()->name }}
                        </span>
                    </div>

                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-3 py-2 rounded-full hover:bg-white/10 transition duration-200">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="md:hidden hidden py-4 space-y-2 border-t border-white/15">
            <a href="{{ route('home') }}" 
               class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200 {{ request()->routeIs('home') ? 'bg-white/15' : '' }}">
                Home
            </a>

            <a href="{{ route('books.index') }}"
               class="block px-4 py-2 rounded-md hover:bg-white/10 transition duration-200 {{ request()->routeIs('books.*') ? 'bg-white/15' : '' }}">
                Books
            </a>

            <a href="{{ route('orders.index') }}"
                class="px-3 py-2 rounded-full transition duration-200 hover:bg-white/10 {{ request()->routeIs('orders.*') ? 'bg-white/15' : '' }}">
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
