@extends('layouts.app')

@section('title', 'Two-Factor Authentication - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Two-Factor Authentication</h1>
            <p class="text-gray-100/80 mt-2">Enhance your account security</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-[rgba(139,69,19,0.12)] relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[var(--pageturner-dark)] via-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]"></div>
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[var(--pageturner-dark)] page-turner-font mb-2">Two-Factor Authentication Settings</h2>
            <p class="text-gray-700">Add an extra layer of security to your account.</p>
        </div>

        @if(auth()->user()->two_factor_enabled)
            <!-- 2FA is enabled -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-green-800 font-medium">Two-factor authentication is enabled ({{ ucfirst(auth()->user()->two_factor_type) }})</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Recovery Codes</h3>
                    <p class="text-sm text-gray-600 mb-3">Keep these codes in a safe place. They can be used to access your account if you lose your 2FA device.</p>
                    <a href="{{ route('profile.two-factor.recovery-codes') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        View Recovery Codes
                    </a>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Disable Two-Factor Authentication</h3>
                    <p class="text-sm text-gray-600 mb-3">Disabling 2FA will make your account less secure.</p>
                    <form method="POST" action="{{ route('profile.two-factor.disable') }}" onsubmit="return confirm('Are you sure you want to disable two-factor authentication?');">
                        @csrf
                        <div class="flex items-center space-x-3">
                            <input type="password" name="password" placeholder="Enter your password" class="rounded-lg border-gray-300 text-sm flex-1" required>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                Disable 2FA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- 2FA is disabled -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-yellow-800 font-medium">Two-factor authentication is not enabled</span>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Authenticator App Option -->
                <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Authenticator App</h3>
                    <p class="text-sm text-gray-600 mb-4">Use an authenticator app like Google Authenticator, Microsoft Authenticator, or Authy.</p>
                    <form method="POST" action="{{ route('profile.two-factor.setup') }}">
                        @csrf
                        <input type="hidden" name="type" value="app">
                        <button type="submit" class="w-full px-4 py-2 bg-[var(--pageturner-primary)] text-white rounded-lg hover:bg-[var(--pageturner-secondary)] transition-colors">
                            Set up Authenticator App
                        </button>
                    </form>
                </div>

                <!-- Email OTP Option -->
                <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Email OTP</h3>
                    <p class="text-sm text-gray-600 mb-4">Receive one-time passwords via email for verification.</p>
                    <form method="POST" action="{{ route('profile.two-factor.setup') }}">
                        @csrf
                        <input type="hidden" name="type" value="email">
                        <button type="submit" class="w-full px-4 py-2 bg-[var(--pageturner-primary)] text-white rounded-lg hover:bg-[var(--pageturner-secondary)] transition-colors">
                            Set up Email OTP
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('profile.edit') }}" class="text-[var(--pageturner-primary)] hover:text-[var(--pageturner-secondary)] transition-colors text-sm inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Profile
            </a>
        </div>
    </div>
</div>
@endsection