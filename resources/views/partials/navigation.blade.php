
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
                <div class="hidden sm:flex ml-8 space-x-1 text-sm font-medium" style="margin-left: 100px;">
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

                    <!-- Add this after the Cart button and before the Profile Dropdown in your nav -->
                    @auth
                        <!-- Notification Bell -->
                        <div class="relative" x-data="notificationBell()" x-init="init()">
                            <button @click="toggleDropdown()" class="relative px-2 py-2 text-white hover:bg-white/10 rounded-full transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                
                                <!-- Notification Badge -->
                                <span x-show="unreadCount > 0" 
                                    x-text="unreadCount"
                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                </span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="open" 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200 max-h-[32rem] overflow-y-auto"
                                style="display: none;">
                                
                                <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                                    <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark all as read
                                    </button>
                                </div>

                                <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p>No notifications</p>
                                </div>

                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 cursor-pointer" 
                                        :class="{ 'bg-blue-50': !notification.read_at }"
                                        @click="markAsRead(notification.id, notification.data.action_url)">
                                        <div class="flex items-start">
                                            <!-- Icon based on notification type -->
                                            <div class="flex-shrink-0 mr-3">
                                                <template x-if="notification.data.type === 'order'">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                    </svg>
                                                </template>
                                                <template x-if="notification.data.type === 'security'">
                                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </template>
                                                <template x-if="notification.data.type === 'admin_order'">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </template>
                                                <template x-if="notification.data.type === 'review'">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                </template>
                                            </div>

                                            <!-- Notification Content -->
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900" x-text="notification.data.title"></p>
                                                <p class="text-xs text-gray-600 mt-1" x-text="notification.data.message"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="formatDate(notification.created_at)"></p>
                                            </div>

                                            <!-- Unread Indicator -->
                                            <div x-show="!notification.read_at" class="ml-2 w-2 h-2 bg-blue-600 rounded-full"></div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="notifications.length > 0" class="px-4 py-2 border-t border-gray-100 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth

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

                <!-- Add this after the Cart button and before the Profile Dropdown in your nav -->
                @auth
                    <!-- Notification Bell -->
                    <div class="relative" x-data="notificationBell()" x-init="init()">
                        <button @click="toggleDropdown()" class="relative px-2 py-2 text-white hover:bg-white/10 rounded-full transition duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            
                            <!-- Notification Badge -->
                            <span x-show="unreadCount > 0" 
                                x-text="unreadCount"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            </span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200 max-h-[32rem] overflow-y-auto"
                            style="display: none;">
                            
                            <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                                <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-blue-600 hover:text-blue-800">
                                    Mark all as read
                                </button>
                            </div>

                            <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p>No notifications</p>
                            </div>

                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 cursor-pointer" 
                                    :class="{ 'bg-blue-50': !notification.read_at }"
                                    @click="markAsRead(notification.id, notification.data.action_url)">
                                    <div class="flex items-start">
                                        <!-- Icon based on notification type -->
                                        <div class="flex-shrink-0 mr-3">
                                            <template x-if="notification.data.type === 'order'">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </template>
                                            <template x-if="notification.data.type === 'security'">
                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </template>
                                            <template x-if="notification.data.type === 'admin_order'">
                                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </template>
                                            <template x-if="notification.data.type === 'review'">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                            </template>
                                        </div>

                                        <!-- Notification Content -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.data.title"></p>
                                            <p class="text-xs text-gray-600 mt-1" x-text="notification.data.message"></p>
                                            <p class="text-xs text-gray-400 mt-1" x-text="formatDate(notification.created_at)"></p>
                                        </div>

                                        <!-- Unread Indicator -->
                                        <div x-show="!notification.read_at" class="ml-2 w-2 h-2 bg-blue-600 rounded-full"></div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="notifications.length > 0" class="px-4 py-2 border-t border-gray-100 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth

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
    
    function notificationBell() {
        return {
            open: false,
            unreadCount: 0,
            notifications: [],
            
            init() {
                this.fetchNotifications();
                // Poll for new notifications every 60 seconds
                setInterval(() => this.fetchNotifications(), 60000);
                
                // Listen for new notification events
                window.addEventListener('notification-received', () => this.fetchNotifications());
            },
            
            fetchNotifications() {
                    fetch('/notifications/fetch')
                    .then(response => response.json())
                    .then(data => {
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            },
            
            toggleDropdown() {
                this.open = !this.open;
                if (this.open) {
                    this.fetchNotifications();
                }
            },
            
            markAsRead(id, actionUrl) {
                fetch(`/notifications/${id}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => {
                    this.fetchNotifications();
                    if (actionUrl) {
                        window.location.href = actionUrl;
                    }
                });
            },
            
            markAllAsRead() {
                fetch('/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => {
                    this.fetchNotifications();
                });
            },
            
            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);
                
                if (diffMins < 1) return 'Just now';
                if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
                if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                if (diffDays === 1) return 'Yesterday';
                if (diffDays < 7) return `${diffDays} days ago`;
                
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }
        }
    }

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
