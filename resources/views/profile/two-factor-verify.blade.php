@extends('layouts.app')

@section('title', 'Two-Factor Verification - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Two-Factor Verification</h1>
            <p class="text-gray-100/80 mt-2">Additional security verification required</p>
        </div>
    </div>
@endsection

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="max-w-md w-full">
        <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-[rgba(139,69,19,0.12)] relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[var(--pageturner-dark)] via-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]"></div>
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[var(--pageturner-dark)] mb-2">Verify Your Identity</h2>
                <p class="text-gray-600">
                    @if(auth()->user()->two_factor_type === 'app')
                        Enter the 6-digit code from your authenticator app
                    @else
                        Enter the 6-digit code sent to your email
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('two-factor.verify.submit') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           maxlength="10"
                           class="w-full text-center text-2xl tracking-widest px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--pageturner-primary)] focus:border-transparent"
                           placeholder="000000"
                           autofocus
                           required>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-[var(--pageturner-primary)] text-white rounded-lg hover:bg-[var(--pageturner-secondary)] transition-colors font-medium">
                        Verify
                    </button>
                </div>
            </form>

            @if(auth()->user()->two_factor_type === 'email')
                <div class="mt-4 text-center">
                    <form method="POST" action="{{ route('two-factor.resend') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-[var(--pageturner-primary)] hover:text-[var(--pageturner-secondary)]">
                            Resend verification code
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-2">Having trouble?</p>
                <p class="text-xs text-gray-500">
                    If you've lost access to your 2FA method, you can use one of your 
                    <button type="button" onclick="showRecoveryForm()" class="text-[var(--pageturner-primary)] hover:underline">
                        recovery codes
                    </button>
                </p>
            </div>

            <!-- Recovery Code Form (Hidden by default) -->
            <div id="recovery-form" class="hidden mt-6 pt-6 border-t border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3">Use Recovery Code</h3>
                <form method="POST" action="{{ route('two-factor.verify.submit') }}" class="space-y-4">
                    @csrf
                    <div>
                        <input type="text" 
                               name="code" 
                               placeholder="Enter recovery code"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--pageturner-primary)] focus:border-transparent">
                    </div>
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Verify Recovery Code
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRecoveryForm() {
    document.getElementById('recovery-form').classList.remove('hidden');
}
</script>
@endsection