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
        --pageturner-warning: #FF8F00; /* Amber for warnings */
        --pageturner-shadow: 0 4px 12px rgba(139, 69, 19, 0.15);
        --pageturner-border-radius: 8px;
        --pageturner-transition: all 0.3s ease;
    }

    /* Main forgot password container styling */
    .forgot-password-wrapper {
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
    .forgot-password-wrapper::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><text x="40" y="60" font-family="Georgia" font-size="22" fill="%23e8dfd6" opacity="0.3">📧</text><text x="140" y="110" font-family="Georgia" font-size="18" fill="%23e8dfd6" opacity="0.3">🔑</text><text x="70" y="170" font-family="Georgia" font-size="20" fill="%23e8dfd6" opacity="0.3">📖</text><text x="160" y="40" font-family="Georgia" font-size="16" fill="%23e8dfd6" opacity="0.3">✉️</text><text x="30" y="120" font-family="Georgia" font-size="24" fill="%23e8dfd6" opacity="0.3">🔓</text></svg>');
        z-index: 0;
    }

    /* Main card styling - designed to look like an open book */
    .forgot-password-card {
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
    .forgot-password-card::before {
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
    .forgot-password-header {
        text-align: center;
        margin-bottom: 32px;
        position: relative;
    }

    .forgot-password-header h1 {
        font-family: 'Playfair Display', 'Georgia', serif;
        color: var(--pageturner-primary);
        font-size: 2.3rem;
        font-weight: 700;
        margin-bottom: 16px;
        letter-spacing: 1px;
        position: relative;
        display: inline-block;
    }

    .forgot-password-header h1::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 25%;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, transparent, var(--pageturner-accent), transparent);
    }

    .forgot-password-header .icon {
        font-size: 3.5rem;
        margin-bottom: 20px;
        display: block;
        animation: gentleFloat 3s ease-in-out infinite;
    }

    @keyframes gentleFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    /* Instructions styling */
    .forgot-instructions {
        background-color: rgba(244, 164, 96, 0.08);
        border-radius: var(--pageturner-border-radius);
        padding: 20px;
        margin-bottom: 32px;
        border-left: 4px solid var(--pageturner-warning);
        line-height: 1.6;
    }

    .forgot-instructions p {
        color: var(--pageturner-text);
        font-size: 1.05rem;
        margin: 0;
        text-align: center;
    }

    .forgot-instructions::before {
        content: "💡";
        font-size: 1.5rem;
        display: block;
        text-align: center;
        margin-bottom: 12px;
    }

    /* Form styling */
    .forgot-password-form {
        margin-top: 8px;
    }

    /* Form group styling */
    .forgot-form-group {
        margin-bottom: 28px;
        position: relative;
    }

    /* Label styling */
    .forgot-input-label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--pageturner-dark);
        font-size: 1rem;
        letter-spacing: 0.5px;
        transition: var(--pageturner-transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .forgot-input-label::before {
        content: "📧";
        font-size: 1.2rem;
    }

    /* Input field styling */
    .forgot-text-input {
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

    .forgot-text-input:focus {
        outline: none;
        border-color: var(--pageturner-accent);
        box-shadow: 0 0 0 3px rgba(244, 164, 96, 0.3);
        background-color: white;
    }

    /* Email icon inside input */
    .forgot-form-group::before {
        font-family: 'Segoe UI Emoji', 'Apple Color Emoji', sans-serif;
        position: absolute;
        left: 16px;
        top: 40px;
        font-size: 1.3rem;
        color: var(--pageturner-secondary);
        z-index: 2;
    }

    .forgot-form-group::before {
        content: "✉️";
    }

    /* Error message styling */
    .forgot-input-error {
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

    /* Session status message */
    .forgot-session-status {
        padding: 16px;
        border-radius: var(--pageturner-border-radius);
        margin-bottom: 28px;
        text-align: center;
        font-weight: 500;
        background-color: rgba(46, 125, 50, 0.1);
        color: var(--pageturner-success);
        border-left: 4px solid var(--pageturner-success);
    }

    /* What happens next section */
    .next-steps {
        background-color: rgba(21, 101, 192, 0.05);
        border-radius: var(--pageturner-border-radius);
        padding: 18px;
        margin: 28px 0;
        border-left: 4px solid var(--pageturner-info);
    }

    .next-steps h4 {
        color: var(--pageturner-primary);
        font-size: 1.05rem;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .next-steps h4::before {
        content: "⏳";
        font-size: 1.2rem;
    }

    .next-steps ol {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
        counter-reset: step-counter;
    }

    .next-steps li {
        padding: 8px 0;
        padding-left: 32px;
        position: relative;
        color: var(--pageturner-text);
        font-size: 0.95rem;
        counter-increment: step-counter;
    }

    .next-steps li::before {
        content: counter(step-counter);
        position: absolute;
        left: 8px;
        top: 6px;
        width: 20px;
        height: 20px;
        background-color: var(--pageturner-accent);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
    }

    /* Button container */
    .forgot-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 36px;
        padding-top: 24px;
        border-top: 1px solid rgba(139, 69, 19, 0.1);
    }

    /* Primary button styling */
    .forgot-primary-button {
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

    .forgot-primary-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(139, 69, 19, 0.3);
        background: linear-gradient(135deg, var(--pageturner-secondary), var(--pageturner-primary));
    }

    .forgot-primary-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(139, 69, 19, 0.3);
    }

    .forgot-primary-button::after {
        content: "📤";
        font-size: 1.2rem;
    }

    /* Back to login link */
    .forgot-back-to-login-link {
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

    .forgot-back-to-login-link:hover {
        color: var(--pageturner-primary);
    }

    .forgot-back-to-login-link::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--pageturner-primary);
        transition: width 0.3s ease;
    }

    .forgot-back-to-login-link:hover::after {
        width: 100%;
    }

    .forgot-back-to-login-link::before {
        content: "←";
        font-weight: bold;
    }

    /* Decorative elements - book corner */
    .forgot-password-card::after {
        content: "";
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%238B4513" opacity="0.1"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
        z-index: 0;
    }

    /* Success message after submission */
    .success-message {
        background-color: rgba(46, 125, 50, 0.1);
        border-radius: var(--pageturner-border-radius);
        padding: 20px;
        margin-bottom: 28px;
        text-align: center;
        border-left: 4px solid var(--pageturner-success);
    }

    .success-message h4 {
        color: var(--pageturner-success);
        font-size: 1.1rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .success-message h4::before {
        content: "✅";
        font-size: 1.3rem;
    }

    .success-message p {
        color: var(--pageturner-text);
        font-size: 1rem;
        margin: 0;
        line-height: 1.5;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .forgot-password-card {
            padding: 30px 24px;
            margin: 0 16px;
        }
        
        .forgot-password-header h1 {
            font-size: 2rem;
        }
        
        .forgot-actions {
            flex-direction: column;
            gap: 20px;
            align-items: stretch;
        }
        
        .forgot-back-to-login-link {
            text-align: center;
            margin-bottom: 10px;
            justify-content: center;
        }
        
        .forgot-primary-button {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animation for page load */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .forgot-password-card {
        animation: fadeIn 0.6s ease-out;
    }

    /* Focus styles for accessibility */
    .forgot-text-input:focus-visible,
    .forgot-primary-button:focus-visible,
    .forgot-back-to-login-link:focus-visible {
        outline: 2px solid var(--pageturner-accent);
        outline-offset: 2px;
    }
</style>


<div class="forgot-password-wrapper">
    <div class="forgot-password-card">
        <div class="forgot-password-header">
            <div class="icon">🔑</div>
            <h1>Reset Your Password</h1>
        </div>

        <!-- Instructions -->
        <div class="forgot-instructions">
            <p>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="forgot-session-status mb-4" :status="session('status')" />

        <!-- What happens next (optional informational section) -->
        <div class="next-steps">
            <h4>What happens next?</h4>
            <ol>
                <li>Enter your email address below</li>
                <li>Check your inbox for our password reset email</li>
                <li>Click the secure link in the email</li>
                <li>Create your new password</li>
            </ol>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="forgot-password-form">
            @csrf

            <!-- Email Address -->
            <div class="forgot-form-group">
                <x-input-label for="email" :value="__('Your Email Address')" class="forgot-input-label" />
                <x-text-input id="email" class="forgot-text-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your registered email address" />
                <x-input-error :messages="$errors->get('email')" class="forgot-input-error mt-2" />
            </div>

            <div class="forgot-actions">
                <a class="forgot-back-to-login-link" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>

                <x-primary-button class="forgot-primary-button">
                    {{ __('Send Reset Link') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Optional: Additional help section -->
        <div class="next-steps" style="margin-top: 24px; margin-bottom: 0; background-color: rgba(139, 69, 19, 0.05); border-left-color: var(--pageturner-secondary);">
            <h4 style="color: var(--pageturner-secondary);">Need more help?</h4>
            <p style="color: var(--pageturner-text); font-size: 0.95rem; margin: 0; line-height: 1.5;">
                If you're having trouble receiving the email, please check your spam folder or 
                <a href="/contact" style="color: var(--pageturner-primary); text-decoration: underline;">contact our support team</a>.
            </p>
        </div>
    </div>
</div>

<!-- Optional: Success message template (for JavaScript) -->
<template id="successTemplate">
    <div class="success-message">
        <h4>Email Sent Successfully!</h4>
        <p>We've sent a password reset link to your email address. Please check your inbox and follow the instructions in the email.</p>
    </div>
</template>

<!-- Optional JavaScript for form submission feedback -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.forgot-password-form');
        const successTemplate = document.getElementById('successTemplate');
        
        if (form && successTemplate) {
            form.addEventListener('submit', function(e) {
                // Optional: Add loading state to button
                const submitButton = this.querySelector('.forgot-primary-button');
                const originalText = submitButton.textContent;
                
                // You can add loading animation here
                // submitButton.innerHTML = '<span class="loading-spinner"></span> Sending...';
                // submitButton.disabled = true;
                
                // Note: In a real application, this would be handled by the form submission
                // and the server response. This is just for visual feedback.
            });
        }
        
        // If there's a session status message, focus on it for accessibility
        const statusMessage = document.querySelector('.forgot-session-status');
        if (statusMessage && statusMessage.textContent.trim() !== '') {
            statusMessage.setAttribute('tabindex', '-1');
            statusMessage.focus();
        }
    });
</script>
