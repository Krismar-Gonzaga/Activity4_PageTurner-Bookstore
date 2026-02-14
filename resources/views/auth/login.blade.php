<style>
    /* PageTurner Bookstore Login Page Styles */
    /* Book-themed design with warm, inviting colors */

    /* Root variables for consistent theming */
    :root {
        --pageturner-primary: #8B4513; /* Saddle brown - book binding color */
        --pageturner-secondary: #D2691E; /* Chocolate brown */
        --pageturner-accent: #F4A460; /* Sandy brown */
        --pageturner-light: #F5EBDC; /* Parchment paper color */
        --pageturner-dark: #5D4037; /* Dark brown */
        --pageturner-text: #3E2723; /* Almost black brown */
        --pageturner-success: #2E7D32; /* Green for bookmarks */
        --pageturner-error: #C62828; /* Red for errors */
        --pageturner-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
        --pageturner-border-radius: 8px;
        --pageturner-transition: all 0.3s ease;
    }

    /* Main login container styling */
    .login-page-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background-color: #f9f5f0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23f9f5f0"/><path d="M20,20 L80,20 L80,80 L20,80 Z" fill="none" stroke="%23e8dfd6" stroke-width="1"/><path d="M30,30 L70,30 L70,70 L30,70 Z" fill="none" stroke="%23e8dfd6" stroke-width="0.5"/></svg>');
        font-family: 'Georgia', 'Times New Roman', serif;
        position: relative;
        overflow: hidden;
    }

    /* Decorative book-themed elements */
    .login-page-wrapper::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><text x="20" y="40" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">📚</text><text x="120" y="90" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">📖</text><text x="50" y="150" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">🔖</text><text x="150" y="180" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">✍️</text></svg>');
        z-index: 0;
    }

    /* Main card styling - designed to look like an open book */
    .login-card {
        width: 100%;
        max-width: 480px;
        background-color: var(--pageturner-light);
        border-radius: 12px;
        box-shadow: var(--pageturner-shadow), 
                    0 10px 30px rgba(139, 69, 19, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
        padding: 40px;
        position: relative;
        z-index: 1;
        border: 1px solid rgba(139, 69, 19, 0.1);
        overflow: hidden;
    }

    /* Book spine effect on the left side */
    .login-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 12px;
        height: 100%;
        background: linear-gradient(to right, 
            var(--pageturner-primary), 
            var(--pageturner-dark));
        border-radius: 12px 0 0 12px;
    }

    /* Header styling */
    .login-header {
        text-align: center;
        margin-bottom: 32px;
        position: relative;
    }

    .login-header h1 {
        font-family: 'Playfair Display', 'Georgia', serif;
        color: var(--pageturner-primary);
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: 1px;
        position: relative;
        display: inline-block;
    }

    .login-header h1::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 25%;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, transparent, var(--pageturner-accent), transparent);
    }

    .login-header p {
        color: var(--pageturner-text);
        font-size: 1.1rem;
        margin-top: 16px;
        font-style: italic;
        opacity: 0.8;
    }

    /* Form styling */
    .login-form {
        margin-top: 24px;
    }

    /* Form group styling */
    .form-group {
        margin-bottom: 24px;
        position: relative;
    }

    /* Label styling */
    .x-input-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--pageturner-dark);
        font-size: 1rem;
        letter-spacing: 0.5px;
        transition: var(--pageturner-transition);
    }

    /* Input field styling */
    .x-text-input {
        width: 100%;
        padding: 14px 16px;
        padding-left: 44px;
        border: 2px solid rgba(139, 69, 19, 0.2);
        border-radius: var(--pageturner-border-radius);
        background-color: rgba(255, 255, 255, 0.9);
        font-family: 'Georgia', serif;
        font-size: 1rem;
        color: var(--pageturner-text);
        transition: var(--pageturner-transition);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .x-text-input:focus {
        outline: none;
        border-color: var(--pageturner-accent);
        box-shadow: 0 0 0 3px rgba(244, 164, 96, 0.3);
        background-color: white;
    }

    /* Input icons */
    .form-group::before {
        font-family: 'Segoe UI Emoji', 'Apple Color Emoji', sans-serif;
        position: absolute;
        left: 16px;
        top: 38px;
        font-size: 1.2rem;
        color: var(--pageturner-secondary);
        z-index: 2;
    }

    .form-group:nth-child(1)::before {
        content: "📧";
    }

    .form-group:nth-child(2)::before {
        content: "🔒";
    }

    /* Error message styling */
    .x-input-error {
        color: var(--pageturner-error);
        font-size: 0.875rem;
        margin-top: 6px;
        padding-left: 8px;
        border-left: 3px solid var(--pageturner-error);
        background-color: rgba(198, 40, 40, 0.05);
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: 8px;
    }

    /* Remember me checkbox styling */
    .remember-me {
        display: flex;
        align-items: center;
        margin: 24px 0;
        padding: 12px 0;
        border-top: 1px dashed rgba(139, 69, 19, 0.2);
        border-bottom: 1px dashed rgba(139, 69, 19, 0.2);
    }

    #remember_me {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        accent-color: var(--pageturner-primary);
        cursor: pointer;
    }

    .remember-me label {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: var(--pageturner-text);
        font-size: 0.95rem;
    }

    /* Forgot password link */
    .forgot-password-link {
        color: var(--pageturner-secondary);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: var(--pageturner-transition);
        position: relative;
        padding: 4px 0;
    }

    .forgot-password-link:hover {
        color: var(--pageturner-primary);
    }

    .forgot-password-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--pageturner-primary);
        transition: width 0.3s ease;
    }

    .forgot-password-link:hover::after {
        width: 100%;
    }

    /* Button container */
    .login-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 32px;
    }

    /* Primary button styling */
    .x-primary-button {
        background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-secondary));
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: var(--pageturner-border-radius);
        font-family: 'Georgia', serif;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--pageturner-transition);
        letter-spacing: 0.5px;
        box-shadow: 0 4px 8px rgba(139, 69, 19, 0.25);
        position: relative;
        overflow: hidden;
    }

    .x-primary-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(139, 69, 19, 0.3);
        background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
    }

    .x-primary-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(139, 69, 19, 0.3);
    }

    .x-primary-button::after {
        content: "→";
        margin-left: 8px;
        font-weight: bold;
    }

    /* Session status message */
    .x-auth-session-status {
        padding: 16px;
        border-radius: var(--pageturner-border-radius);
        margin-bottom: 24px;
        text-align: center;
        font-weight: 500;
        background-color: rgba(46, 125, 50, 0.1);
        color: var(--pageturner-success);
        border-left: 4px solid var(--pageturner-success);
    }

    /* Decorative elements - book corner */
    .login-card::after {
        content: "";
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%238B4513" opacity="0.1"><path d="M18 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM6 4h5v16H6V4zm12 16h-5V4h5v16z"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .login-card {
            padding: 30px 24px;
            margin: 0 16px;
        }
        
        .login-header h1 {
            font-size: 2rem;
        }
        
        .login-actions {
            flex-direction: column;
            gap: 20px;
            align-items: stretch;
        }
        
        .forgot-password-link {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .x-primary-button {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animation for page load */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .login-card {
        animation: fadeIn 0.6s ease-out;
    }

    /* Focus styles for accessibility */
    .x-text-input:focus-visible,
    .x-primary-button:focus-visible,
    .forgot-password-link:focus-visible {
        outline: 2px solid var(--pageturner-accent);
        outline-offset: 2px;
    }

        /* Register link styling */
    .register-link-container {
        text-align: center;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px dashed rgba(139, 69, 19, 0.2);
    }

    .register-link {
        color: var(--pageturner-primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: var(--pageturner-transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: var(--pageturner-border-radius);
        background-color: rgba(139, 69, 19, 0.05);
    }

    .register-link:hover {
        color: var(--pageturner-secondary);
        background-color: rgba(139, 69, 19, 0.1);
        transform: translateY(-2px);
    }

    .register-link::before {
        content: "📚";
        font-size: 1.2rem;
    }

    /* Or if you want a simpler link without background */
    .simple-register-link {
        color: var(--pageturner-secondary);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: var(--pageturner-transition);
        position: relative;
        padding: 4px 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .simple-register-link:hover {
        color: var(--pageturner-primary);
    }

    .simple-register-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--pageturner-primary);
        transition: width 0.3s ease;
    }

    .simple-register-link:hover::after {
        width: 100%;
    }

    .simple-register-link::before {
        content: "→";
        font-weight: bold;
    }
</style>


<div class="login-page-wrapper">
    <div class="login-card">
        <!-- Session Status -->
        <x-auth-session-status class="x-auth-session-status mb-4" :status="session('status')" />

        <div class="login-header">
            <h1>PageTurner Bookstore</h1>
            <p>Log in to your literary haven</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <x-input-label for="email" :value="__('Email')" class="x-input-label" />
                <x-text-input id="email" class="x-text-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="x-input-error mt-2" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <x-input-label for="password" :value="__('Password')" class="x-input-label" />

                <x-text-input id="password" class="x-text-input block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="x-input-error mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="remember-me">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#8B4513] shadow-sm focus:ring-[#8B4513]" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="login-actions">
                @if (Route::has('password.request'))
                    <a class="forgot-password-link underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8B4513]" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="x-primary-button ms-3">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
            <div class="register-link-container">
                <p class="text-gray-600 mb-3">New to PageTurner?</p>
                <a class="register-link" href="{{ route('register') }}">
                    Create your free account
                </a>
                
                <!-- Alternative: Simple link version -->
                <!--
                <a class="simple-register-link" href="{{ route('register') }}">
                    {{ __('Don\'t have an account? Register') }}
                </a>
                -->
            </div>
            
        </form>
    </div>
</div>

