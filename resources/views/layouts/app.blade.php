<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PageTurner Bookstore')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Elegant book typography -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ============================================= -->
    <!--           Core Theme Variables               -->
    <!-- ============================================= -->
    <style>
        :root {
            --pageturner-primary:   #8B4513;     /* saddle brown   - book spine */
            --pageturner-secondary: #A0522D;     /* sienna          - warmer variant */
            --pageturner-accent:    #C19A6B;     /* softer tan      - highlights */
            --pageturner-light:     #FDF8F0;     /* cream parchment */
            --pageturner-very-light:#FEFCFA;     /* almost white    - backgrounds */
            --pageturner-dark:      #5D4037;     /* dark walnut     - text & nav */
            --pageturner-text:      #3F2A1D;     /* deep coffee     - body text */
            --pageturner-success:   #2E7D32;
            --pageturner-error:     #C62828;

            --shadow-soft:          0 6px 20px rgba(139, 69, 19, 0.10);
            --shadow-card:          0 10px 30px rgba(139, 69, 19, 0.14), inset 0 1px 0 rgba(255,255,255,0.65);
            --shadow-lift:          0 16px 40px rgba(139, 69, 19, 0.18);

            --radius:               14px;
            --radius-lg:            18px;
            --transition:           all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            @apply antialiased min-h-screen flex flex-col bg-[var(--pageturner-very-light)];
            background-image:
                url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'><rect width='100' height='100' fill='%23fefcfa'/><path d='M8,8 L92,8 L92,92 L8,92 Z' fill='none' stroke='%23f0e6d9' stroke-width='0.5'/></svg>");
            color: var(--pageturner-text);
            font-family: 'Merriweather', Georgia, serif;
            line-height: 1.72;
        }

        .page-turner-font,
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 600;
        }

        /* Page corner book decoration – subtle version */
        .page-decoration::before,
        .page-decoration::after {
            content: "";
            position: absolute;
            width: 64px;
            height: 64px;
            opacity: 0.05;
            pointer-events: none;
            z-index: -1;
        }

        .page-decoration::before {
            bottom: 16px;
            right: 16px;
            background: url("data:image/svg+xml;utf8,<svg viewBox='0 0 24 24' fill='%238B4513'><path d='M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z'/></svg>") no-repeat center;
            background-size: contain;
        }

        .page-decoration::after {
            top: 16px;
            left: 16px;
            transform: scaleX(-1);
        }

        /* Main content wrapper card */
        .content-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            border: 1px solid rgba(139,69,19,0.06);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .content-card:hover {
            box-shadow: var(--shadow-lift);
            transform: translateY(-3px);
        }

        /* Consistent book spine accent */
        .book-spine-edge {
            position: absolute;
            inset-block: 0;
            left: 0;
            width: 9px;
            background: linear-gradient(to bottom,
                var(--pageturner-dark) 0%,
                var(--pageturner-primary) 50%,
                var(--pageturner-secondary) 100%);
            border-radius: var(--radius-lg) 0 0 var(--radius-lg);
            pointer-events: none;
        }

        /* Improved link styling */
        a.text-link {
            color: var(--pageturner-secondary);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: var(--transition);
        }

        a.text-link:hover,
        a.text-link:focus-visible {
            color: var(--pageturner-primary);
            border-bottom-color: var(--pageturner-accent);
        }

        /* Header styling – softer gradient */
        header.bg-header {
            background: linear-gradient(135deg,
                color-mix(in srgb, var(--pageturner-primary) 85%, #000 15%),
                color-mix(in srgb, var(--pageturner-secondary) 80%, #000 20%));
            color: white;
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
        }

        /* Focus states for accessibility */
        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 3px solid var(--pageturner-accent);
            outline-offset: 2px;
        }
    </style>

    @stack('styles')
</head>

<body class="page-decoration">

    <!-- Navigation -->
    @include('partials.navigation')

    <!-- Page Heading / Hero -->
    @hasSection('header')
        <header class="bg-header py-12 md:py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>
    @endif

    <!-- Flash Messages / Notifications -->
    @include('partials.flash-messages')

    <!-- Main Content -->
    <main class="flex-grow pt-10 pb-16 md:pt-12 md:pb-20 lg:pt-14 lg:pb-24">
        @if(Request::is('/'))
            <!-- Homepage – full width -->
            @yield('content')
        @else
            <!-- Inner pages – full width content with centered card -->
            <div class="w-full">
                <div class="max-w-7xl mx-auto">
                    <div class="content-card p-7 md:p-10 lg:p-12 mx-5 sm:mx-6 lg:mx-8">
                        <div class="book-spine-edge"></div>
                        @yield('content')
                    </div>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    @include('partials.footer')

    @stack('scripts')
</body>
</html>