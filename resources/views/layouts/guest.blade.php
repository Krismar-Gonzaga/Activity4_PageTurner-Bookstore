<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PageTurner Bookstore') }} • @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bookish elegant typography -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Vite assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ============================================= -->
    <!--          Guest / Auth Pages Theme             -->
    <!-- ============================================= -->
    <style>
        :root {
            --pageturner-primary:   #8B4513;
            --pageturner-secondary: #D2691E;
            --pageturner-accent:    #F4A460;
            --pageturner-light:     #F5EBDC;
            --pageturner-very-light:#FDF8F0;
            --pageturner-dark:      #5D4037;
            --pageturner-text:      #3E2723;
            --shadow-soft:          0 10px 30px rgba(139, 69, 19, 0.16);
            --shadow-deeper:        0 14px 40px rgba(93, 64, 55, 0.22);
            --radius:               12px;
            --transition:           all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: var(--pageturner-very-light);
            background-image:
                url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'><rect width='120' height='120' fill='%23fdf8f0'/><path d='M15,15 L105,15 L105,105 L15,105 Z' fill='none' stroke='%23e8dfd6' stroke-width='0.8'/></svg>");
            color: var(--pageturner-text);
            font-family: 'Merriweather', Georgia, serif;
            line-height: 1.7;
            min-height: 100vh;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--pageturner-primary);
        }

        /* Main container */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Logo + brand area */
        .brand-block {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            width: 5.5rem;
            height: 5.5rem;
            margin: 0 auto 1.25rem;
            filter: drop-shadow(0 4px 10px rgba(139,69,19,0.25));
        }

        .brand-title {
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            margin-bottom: 0.25rem;
            background: linear-gradient(90deg, var(--pageturner-primary), var(--pageturner-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-tagline {
            font-size: 1.15rem;
            font-style: italic;
            color: var(--pageturner-dark);
            opacity: 0.85;
        }

        /* Card / form container */
        .auth-card {
            position: relative;
            width: 100%;
            max-width: 480px;
            background: var(--pageturner-light);
            border-radius: var(--radius);
            box-shadow: var(--shadow-deeper), inset 0 1px 0 rgba(255,255,255,0.6);
            border: 1px solid rgba(139,69,19,0.09);
            padding: 2.5rem 2rem;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Book spine decorative element */
        .auth-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 12px;
            background: linear-gradient(to bottom,
                var(--pageturner-primary) 0%,
                var(--pageturner-secondary) 40%,
                var(--pageturner-dark) 100%);
            border-radius: var(--radius) 0 0 var(--radius);
            box-shadow: 2px 0 8px rgba(0,0,0,0.15);
        }

        /* Subtle page corner decoration */
        .auth-card::after {
            content: "";
            position: absolute;
            bottom: 16px;
            right: 16px;
            width: 64px;
            height: 64px;
            opacity: 0.08;
            background: url("data:image/svg+xml;utf8,<svg viewBox='0 0 24 24' fill='%238B4513'><path d='M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z'/></svg>") no-repeat center;
            background-size: contain;
            pointer-events: none;
        }

        /* Footer note */
        .auth-footer {
            margin-top: 2.5rem;
            color: var(--pageturner-dark);
            opacity: 0.7;
            font-size: 0.92rem;
            text-align: center;
            line-height: 1.5;
        }

        .auth-footer .year {
            font-weight: 500;
        }

        /* Focus visibility */
        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 3px solid var(--pageturner-accent);
            outline-offset: 3px;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }
            .brand-title {
                font-size: 2.4rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased">

    <div class="auth-wrapper">

        <!-- Brand / Logo area -->
        <div class="brand-block">
            <x-application-logo class="brand-logo fill-current text-gray-600" />
            <h1 class="brand-title">PageTurner</h1>
            <p class="brand-tagline">Your literary sanctuary</p>
        </div>

        <!-- Main card with form slot -->
        <div class="auth-card">
            {{ $slot }}
        </div>

        <!-- Footer note -->
        <div class="auth-footer">
            <p>© <span class="year">{{ date('Y') }}</span> PageTurner Bookstore</p>
            <p>Celebrating stories • Sharing knowledge • Since 2023</p>
        </div>

    </div>

    @stack('scripts')
</body>
</html>