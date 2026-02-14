<style>
    /* PageTurner Bookstore Registration Page Styles */
    /* Consistent with login page design */

    /* Use the same root variables as login page */
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

    /* Main registration container styling */
    .register-page-wrapper {
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
    .register-page-wrapper::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><text x="30" y="50" font-family="Georgia" font-size="18" fill="%23e8dfd6" opacity="0.3">📚</text><text x="140" y="100" font-family="Georgia" font-size="20" fill="%23e8dfd6" opacity="0.3">📖</text><text x="60" y="170" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">✍️</text><text x="160" y="40" font-family="Georgia" font-size="22" fill="%23e8dfd6" opacity="0.3">📝</text><text x="40" y="120" font-family="Georgia" font-size="14" fill="%23e8dfd6" opacity="0.3">🔖</text></svg>');
        z-index: 0;
    }

    /* Main card styling - designed to look like an open book */
    .register-card {
        width: 100%;
        max-width: 520px;
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
    .register-card::before {
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
    .register-header {
        text-align: center;
        margin-bottom: 32px;
        position: relative;
    }

    .register-header h1 {
        font-family: 'Playfair Display', 'Georgia', serif;
        color: var(--pageturner-primary);
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: 1px;
        position: relative;
        display: inline-block;
    }

    .register-header h1::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 25%;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, transparent, var(--pageturner-accent), transparent);
    }

    .register-header p {
        color: var(--pageturner-text);
        font-size: 1.1rem;
        margin-top: 16px;
        font-style: italic;
        opacity: 0.8;
    }

    /* Form styling */
    .register-form {
        margin-top: 24px;
    }

    /* Form group styling */
    .register-form-group {
        margin-bottom: 24px;
        position: relative;
    }

    /* Label styling */
    .reg-input-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--pageturner-dark);
        font-size: 1rem;
        letter-spacing: 0.5px;
        transition: var(--pageturner-transition);
    }

    /* Input field styling */
    .reg-text-input {
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

    .reg-text-input:focus {
        outline: none;
        border-color: var(--pageturner-accent);
        box-shadow: 0 0 0 3px rgba(244, 164, 96, 0.3);
        background-color: white;
    }

    /* Input icons for registration form */
    .register-form-group::before {
        font-family: 'Segoe UI Emoji', 'Apple Color Emoji', sans-serif;
        position: absolute;
        left: 16px;
        top: 38px;
        font-size: 1.2rem;
        color: var(--pageturner-secondary);
        z-index: 2;
    }

    .register-form-group:nth-child(1)::before {
        content: "👤";
    }

    .register-form-group:nth-child(2)::before {
        content: "📧";
    }

    .register-form-group:nth-child(3)::before {
        content: "🔒";
    }

    .register-form-group:nth-child(4)::before {
        content: "✓";
        font-size: 1.4rem;
        font-weight: bold;
    }

    /* Error message styling */
    .reg-input-error {
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

    /* Password strength indicator */
    .password-strength {
        margin-top: 8px;
        height: 6px;
        border-radius: 3px;
        background-color: #e0e0e0;
        overflow: hidden;
        position: relative;
    }

    .password-strength-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .password-strength-weak {
        background-color: #f44336;
        width: 33%;
    }

    .password-strength-medium {
        background-color: #ff9800;
        width: 66%;
    }

    .password-strength-strong {
        background-color: #4caf50;
        width: 100%;
    }

    .password-strength-text {
        font-size: 0.8rem;
        margin-top: 4px;
        color: var(--pageturner-text);
        opacity: 0.7;
    }

    /* Registration benefits section */
    .registration-benefits {
        background-color: rgba(244, 164, 96, 0.1);
        border-radius: var(--pageturner-border-radius);
        padding: 16px;
        margin: 24px 0;
        border-left: 4px solid var(--pageturner-accent);
    }

    .registration-benefits h3 {
        color: var(--pageturner-primary);
        font-size: 1.1rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .registration-benefits h3::before {
        content: "📚";
        font-size: 1.2rem;
    }

    .registration-benefits ul {
        list-style-type: none;
        padding-left: 0;
    }

    .registration-benefits li {
        padding: 6px 0;
        padding-left: 24px;
        position: relative;
        color: var(--pageturner-text);
        font-size: 0.95rem;
    }

    .registration-benefits li::before {
        content: "✓";
        position: absolute;
        left: 0;
        color: var(--pageturner-success);
        font-weight: bold;
    }

    /* Login link */
    .login-link {
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

    .login-link:hover {
        color: var(--pageturner-primary);
    }

    .login-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--pageturner-primary);
        transition: width 0.3s ease;
    }

    .login-link:hover::after {
        width: 100%;
    }

    /* Button container */
    .register-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid rgba(139, 69, 19, 0.1);
    }

    /* Primary button styling */
    .reg-primary-button {
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

    .reg-primary-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(139, 69, 19, 0.3);
        background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
    }

    .reg-primary-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(139, 69, 19, 0.3);
    }

    .reg-primary-button::after {
        content: "📖";
        margin-left: 8px;
        font-weight: bold;
    }

    /* Decorative elements - book corner */
    .register-card::after {
        content: "";
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%238B4513" opacity="0.1"><path d="M19 2H5C3.3 2 2 3.3 2 5v14c0 1.7 1.3 3 3 3h14c1.7 0 3-1.3 3-3V5c0-1.7-1.3-3-3-3zM8 4h3v16H8V4zm8 16h-3V4h3v16z"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .register-card {
            padding: 30px 24px;
            margin: 0 16px;
        }
        
        .register-header h1 {
            font-size: 2rem;
        }
        
        .register-actions {
            flex-direction: column;
            gap: 20px;
            align-items: stretch;
        }
        
        .login-link {
            text-align: center;
            margin-bottom: 10px;
            justify-content: center;
        }
        
        .reg-primary-button {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animation for page load */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .register-card {
        animation: fadeIn 0.6s ease-out;
    }

    /* Focus styles for accessibility */
    .reg-text-input:focus-visible,
    .reg-primary-button:focus-visible,
    .login-link:focus-visible {
        outline: 2px solid var(--pageturner-accent);
        outline-offset: 2px;
    }

    /* Tooltip for password requirements */
    .password-requirements {
        font-size: 0.85rem;
        color: var(--pageturner-text);
        opacity: 0.7;
        margin-top: 6px;
        padding-left: 8px;
    }
</style>


<div class="register-page-wrapper">
    <div class="register-card">
        <div class="register-header">
            <h1>PageTurner Bookstore</h1>
            <p>Join our community of book lovers</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="register-form">
            @csrf

            <!-- Name -->
            <div class="register-form-group">
                <x-input-label for="name" :value="__('Name')" class="reg-input-label" />
                <x-text-input id="name" class="reg-text-input block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="reg-input-error mt-2" />
            </div>

            <!-- Email Address -->
            <div class="register-form-group">
                <x-input-label for="email" :value="__('Email')" class="reg-input-label" />
                <x-text-input id="email" class="reg-text-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="reg-input-error mt-2" />
            </div>

            <!-- Password -->
            <div class="register-form-group">
                <x-input-label for="password" :value="__('Password')" class="reg-input-label" />
                <x-text-input id="password" class="reg-text-input block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                
                <!-- Password strength indicator (optional) -->
                <div class="password-strength">
                    <div class="password-strength-bar" id="password-strength-bar"></div>
                </div>
                <div class="password-strength-text" id="password-strength-text">Password strength</div>
                <div class="password-requirements">Use at least 8 characters with a mix of letters, numbers & symbols</div>
                
                <x-input-error :messages="$errors->get('password')" class="reg-input-error mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="register-form-group">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="reg-input-label" />
                <x-text-input id="password_confirmation" class="reg-text-input block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="reg-input-error mt-2" />
            </div>

            <!-- Registration Benefits -->
            <div class="registration-benefits">
                <h3>Join PageTurner to unlock:</h3>
                <ul>
                    <li>Personalized book recommendations</li>
                    <li>Wishlist and reading tracker</li>
                    <li>Exclusive member discounts</li>
                    <li>Early access to new releases</li>
                </ul>
            </div>

            <div class="register-actions">
                <a class="login-link underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    <span>←</span> {{ __('Already registered?') }}
                </a>

                <x-primary-button class="reg-primary-button ms-4">
                    {{ __('Create Account') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

<!-- Optional JavaScript for password strength indicator -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        
        if (passwordInput && strengthBar && strengthText) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Check password strength
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                // Update strength indicator
                strengthBar.className = 'password-strength-bar';
                if (password.length === 0) {
                    strengthBar.style.width = '0';
                    strengthText.textContent = 'Password strength';
                } else if (strength <= 2) {
                    strengthBar.classList.add('password-strength-weak');
                    strengthText.textContent = 'Weak password';
                } else if (strength === 3) {
                    strengthBar.classList.add('password-strength-medium');
                    strengthText.textContent = 'Medium password';
                } else {
                    strengthBar.classList.add('password-strength-strong');
                    strengthText.textContent = 'Strong password';
                }
            });
        }
    });
</script>
