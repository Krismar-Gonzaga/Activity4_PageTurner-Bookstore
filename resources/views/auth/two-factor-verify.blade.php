@extends('layouts.app')

@section('content')
<div class="login-page-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h1>Two-Factor Authentication</h1>
            <p>Enter the verification code from your {{ ucfirst(session('two_factor_method')) }} app</p>
        </div>

        @if(session('error'))
            <div class="x-auth-session-status" style="background-color: rgba(198, 40, 40, 0.1); color: #C62828; border-left-color: #C62828;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="x-auth-session-status">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify.submit') }}" class="login-form">
            @csrf

            <!-- Verification Code -->
            <div class="form-group">
                <x-input-label for="code" :value="__('Verification Code')" class="x-input-label" />
                <x-text-input id="code" class="x-text-input block mt-1 w-full" 
                              type="text" 
                              name="code" 
                              placeholder="Enter 6-digit code"
                              required autofocus />
                <x-input-error :messages="$errors->get('code')" class="x-input-error mt-2" />
            </div>

            <!-- Trust Device Option -->
            <div class="remember-me">
                <label for="trust_device" class="inline-flex items-center">
                    <input id="trust_device" type="checkbox" class="rounded border-gray-300 text-[#8B4513] shadow-sm focus:ring-[#8B4513]" name="trust_device">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Trust this device for 30 days') }}</span>
                </label>
            </div>

            <div class="login-actions" style="justify-content: center;">
                <x-primary-button class="x-primary-button ms-3">
                    {{ __('Verify') }}
                </x-primary-button>
            </div>

            @if(session('two_factor_method') === 'email')
            <div class="register-link-container" style="margin-top: 16px;">
                <p class="text-gray-600 mb-3">Didn't receive the code?</p>
                <form method="POST" action="{{ route('two-factor.resend') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="simple-register-link" style="background: none; border: none; cursor: pointer;">
                        {{ __('Resend Code') }}
                    </button>
                </form>
            </div>
            @endif

            <div class="register-link-container">
                <p class="text-gray-600 mb-3">Having trouble?</p>
                <a class="simple-register-link" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection