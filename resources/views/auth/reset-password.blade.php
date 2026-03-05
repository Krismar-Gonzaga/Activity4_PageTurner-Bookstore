<style>
   
    /* Use the same root variables */
    :root {
        --pageturner-primary: #8B4513; /* Saddle brown - book binding color */
        --pageturner-secondary: #D2691E; /* Chocolate brown */
        --pageturner-accent: #F4A460; /* Sandy brown */
        --pageturner-light: #F5EBDC; /* Parchment paper color */
        --pageturner-dark: #5D4037; /* Dark brown */
        --pageturner-text: #3E2723; /* Almost black brown */
        --pageturner-success: #2E7D32; /* Green for bookmarks */
        --pageturner-error: #C62828; /* Red for errors */
        --pageturner-info: #1565C0; /* Blue for info */
        --pageturner-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
        --pageturner-border-radius: 8px;
        --pageturner-transition: all 0.3s ease;
    }

    /* Main reset password container styling */
    .reset-password-wrapper {
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
    .reset-password-wrapper::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><text x="30" y="60" font-family="Georgia" font-size="18" fill="%23e8dfd6" opacity="0.3">🔐</text><text x="140" y="110" font-family="Georgia" font-size="20" fill="%23e8dfd6" opacity="0.3">🔑</text><text x="60" y="170" font-family="Georgia" font-size="22" fill="%23e8dfd6" opacity="0.3">🔄</text><text x="160" y="40" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">📖</text><text x="40" y="120" font-family="Georgia" font-size="14" fill="%23e8dfd6" opacity="0.3">✍️</text></svg>');
        z-index: 0;
    }

    /* Main card styling - designed to look like an open book */
    .reset-password-card {
        width: 100%;
        max-width: 500px;
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
    .reset-password-card::before {
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
    .reset-password-header {
        text-align: center;
        margin-bottom: 32px;
        position: relative;
    }

    .reset-password-header h1 {
        font-family: 'Playfair Display', 'Georgia', serif;
        color: var(--pageturner-primary);
        font-size: 2.3rem;
        font-weight: 700;
        margin-bottom: 12px;
        letter-spacing: 1px;
        position: relative;
        display: inline-block;
    }

    .reset-password-header h1::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 25%;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, transparent, var(--pageturner-accent), transparent);
    }

    .reset-password-header p {
        color: var(--pageturner-text);
        font-size: 1.05rem;
        margin-top: 20px;
        line-height: 1.5;
        opacity: 0.9;
    }

    .reset-password-header .icon {
        font-size: 3rem;
        margin-bottom: 16px;
        display: block;
    }

    /* Form styling */
    .reset-password-form {
        margin-top: 24px;
    }

    /* Form group styling */
    .reset-form-group {
        margin-bottom: 26px;
        position: relative;
    }

    /* Label styling */
    .reset-input-label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--pageturner-dark);
        font-size: 1rem;
        letter-spacing: 0.5px;
        transition: var(--pageturner-transition);
    }

    /* Input field styling */
    .reset-text-input {
        width: 100%;
        padding: 15px 16px;
        padding-left: 48px;
        border: 2px solid rgba(139, 69, 19, 0.25);
        border-radius: var(--pageturner-border-radius);
        background-color: rgba(255, 255, 255, 0.95);
        font-family: 'Georgia', serif;
        font-size: 1rem;
        color: var(--pageturner-text);
        transition: var(--pageturner-transition);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .reset-text-input:focus {
        outline: none;
        border-color: var(--pageturner-accent);
        box-shadow: 0 0 0 3px rgba(244, 164, 96, 0.3);
        background-color: white;
    }

    /* Input icons for reset password form */
    .reset-form-group::before {
        font-family: 'Segoe UI Emoji', 'Apple Color Emoji', sans-serif;
        position: absolute;
        left: 16px;
        top: 40px;
        font-size: 1.3rem;
        color: var(--pageturner-secondary);
        z-index: 2;
    }

    .reset-form-group:nth-child(2)::before {
        content: "📧";
    }

    .reset-form-group:nth-child(3)::before {
        content: "🔒";
    }

    .reset-form-group:nth-child(4)::before {
        content: "✓";
        font-size: 1.4rem;
        font-weight: bold;
    }

    /* Error message styling */
    .reset-input-error {
        color: var(--pageturner-error);
        font-size: 0.9rem;
        margin-top: 8px;
        padding-left: 12px;
        border-left: 3px solid var(--pageturner-error);
        background-color: rgba(198, 40, 40, 0.05);
        padding: 10px 14px;
        border-radius: 4px;
        margin-top: 10px;
    }

    /* Password strength indicator */
    .reset-password-strength {
        margin-top: 10px;
        height: 6px;
        border-radius: 3px;
        background-color: #e0e0e0;
        overflow: hidden;
        position: relative;
    }

    .reset-password-strength-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .reset-password-strength-weak {
        background-color: #f44336;
        width: 33%;
    }

    .reset-password-strength-medium {
        background-color: #ff9800;
        width: 66%;
    }

    .reset-password-strength-strong {
        background-color: #4caf50;
        width: 100%;
    }

    .reset-password-strength-text {
        font-size: 0.85rem;
        margin-top: 6px;
        color: var(--pageturner-text);
        opacity: 0.7;
    }

    /* Password requirements */
    .password-instructions {
        background-color: rgba(244, 164, 96, 0.08);
        border-radius: var(--pageturner-border-radius);
        padding: 16px;
        margin: 20px 0;
        border-left: 4px solid var(--pageturner-accent);
    }

    .password-instructions h4 {
        color: var(--pageturner-primary);
        font-size: 1rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .password-instructions h4::before {
        content: "💡";
        font-size: 1.2rem;
    }

    .password-instructions ul {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }

    .password-instructions li {
        padding: 4px 0;
        padding-left: 24px;
        position: relative;
        color: var(--pageturner-text);
        font-size: 0.9rem;
    }

    .password-instructions li::before {
        content: "•";
        position: absolute;
        left: 8px;
        color: var(--pageturner-secondary);
        font-weight: bold;
    }

    /* Button container */
    .reset-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 36px;
        padding-top: 24px;
        border-top: 1px solid rgba(139, 69, 19, 0.1);
    }

    /* Primary button styling */
    .reset-primary-button {
        background: linear-gradient(135deg, var(--pageturner-primary), var(--pageturner-secondary));
        color: white;
        border: none;
        padding: 15px 36px;
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
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .reset-primary-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(139, 69, 19, 0.3);
        background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
    }

    .reset-primary-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(139, 69, 19, 0.3);
    }

    .reset-primary-button::after {
        content: "🔄";
        font-size: 1.2rem;
    }

    /* Login link */
    .back-to-login-link {
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
        margin-right: auto;
    }

    .back-to-login-link:hover {
        color: var(--pageturner-primary);
    }

    .back-to-login-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--pageturner-primary);
        transition: width 0.3s ease;
    }

    .back-to-login-link:hover::after {
        width: 100%;
    }

    .back-to-login-link::before {
        content: "←";
        font-weight: bold;
    }

    /* Success/Info message styling */
    .reset-info-message {
        padding: 16px;
        border-radius: var(--pageturner-border-radius);
        margin-bottom: 24px;
        text-align: center;
        font-weight: 500;
        background-color: rgba(21, 101, 192, 0.1);
        color: var(--pageturner-info);
        border-left: 4px solid var(--pageturner-info);
    }

    /* Decorative elements - book corner */
    .reset-password-card::after {
        content: "";
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%238B4513" opacity="0.1"><path d="M12 15c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-10c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .reset-password-card {
            padding: 30px 24px;
            margin: 0 16px;
        }
        
        .reset-password-header h1 {
            font-size: 2rem;
        }
        
        .reset-actions {
            flex-direction: column;
            gap: 20px;
            align-items: stretch;
        }
        
        .back-to-login-link {
            text-align: center;
            margin-bottom: 10px;
            justify-content: center;
            margin-right: 0;
        }
        
        .reset-primary-button {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animation for page load */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .reset-password-card {
        animation: fadeIn 0.6s ease-out;
    }

    /* Focus styles for accessibility */
    .reset-text-input:focus-visible,
    .reset-primary-button:focus-visible,
    .back-to-login-link:focus-visible {
        outline: 2px solid var(--pageturner-accent);
        outline-offset: 2px;
    }
</style>


<div class="reset-password-wrapper">
    <div class="reset-password-card">
        <div class="reset-password-header">
            <div class="icon">🔐</div>
            <h1>Reset Your Password</h1>
            <p>Enter your email and choose a new secure password to regain access to your PageTurner account.</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="reset-info-message">
                {{ session('status') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="reset-input-error mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- FIXED: Changed action from 'password.store' to 'password.update' (Laravel default) -->
        <form method="POST" action="{{ route('password.update') }}" class="reset-password-form">
            @csrf

            <!-- Password Reset Token - FIXED: Use $token variable passed from controller -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address - FIXED: Use $email variable passed from controller -->
            <div class="reset-form-group">
                <label for="email" class="reset-input-label">{{ __('Email Address') }}</label>
                <input id="email" 
                       class="reset-text-input" 
                       type="email" 
                       name="email" 
                       value="{{ $email ?? old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username" 
                       placeholder="Enter your email address" />
                
                @error('email')
                    <div class="reset-input-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="reset-form-group">
                <label for="password" class="reset-input-label">{{ __('New Password') }}</label>
                <input id="password" 
                       class="reset-text-input" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password" 
                       placeholder="Enter new password" />
                
                <!-- Password strength indicator -->
                <div class="reset-password-strength">
                    <div class="reset-password-strength-bar" id="reset-password-strength-bar"></div>
                </div>
                <div class="reset-password-strength-text" id="reset-password-strength-text">Password strength</div>
                
                @error('password')
                    <div class="reset-input-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="reset-form-group">
                <label for="password_confirmation" class="reset-input-label">{{ __('Confirm New Password') }}</label>
                <input id="password_confirmation" 
                       class="reset-text-input" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password" 
                       placeholder="Confirm new password" />
                
                @error('password_confirmation')
                    <div class="reset-input-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Instructions -->
            <div class="password-instructions">
                <h4>Password Requirements</h4>
                <ul>
                    <li>Minimum 8 characters in length</li>
                    <li>Include at least one uppercase letter</li>
                    <li>Include at least one number</li>
                    <li>Include at least one special character</li>
                </ul>
            </div>

            <div class="reset-actions">
                <a class="back-to-login-link" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>

                <button type="submit" class="reset-primary-button">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for password strength indicator -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('reset-password-strength-bar');
        const strengthText = document.getElementById('reset-password-strength-text');
        
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
                strengthBar.className = 'reset-password-strength-bar';
                if (password.length === 0) {
                    strengthBar.style.width = '0';
                    strengthText.textContent = 'Password strength';
                } else if (strength <= 2) {
                    strengthBar.classList.add('reset-password-strength-weak');
                    strengthBar.style.width = '33%';
                    strengthText.textContent = 'Weak password - add more characters and symbols';
                } else if (strength === 3) {
                    strengthBar.classList.add('reset-password-strength-medium');
                    strengthBar.style.width = '66%';
                    strengthText.textContent = 'Medium password - almost there!';
                } else {
                    strengthBar.classList.add('reset-password-strength-strong');
                    strengthBar.style.width = '100%';
                    strengthText.textContent = 'Strong password - excellent!';
                }
            });
        }
    });
</script>
