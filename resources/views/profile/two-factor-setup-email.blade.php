@extends('layouts.app')

@section('title', 'Setup Email OTP - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Setup Email OTP</h1>
            <p class="text-gray-100/80 mt-2">Verify your email address to enable 2FA</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-[rgba(139,69,19,0.12)] relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[var(--pageturner-dark)] via-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]"></div>
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Message -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[var(--pageturner-dark)] mb-2">Check Your Email</h2>
                <p class="text-gray-700">
                    We've sent a one-time password (OTP) to <strong>{{ auth()->user()->email }}</strong>
                </p>
                <p class="text-sm text-gray-500 mt-2">The code will expire in 10 minutes.</p>
            </div>

            <!-- Verification Form -->
            <form method="POST" action="{{ route('profile.two-factor.verify-setup') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Enter OTP Code</label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           maxlength="6"
                           pattern="[0-9]{6}"
                           inputmode="numeric"
                           class="w-48 mx-auto text-center text-2xl tracking-widest px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--pageturner-primary)] focus:border-transparent"
                           placeholder="000000"
                           required>
                </div>

                <div>
                    <button type="submit" 
                            class="px-8 py-3 bg-[var(--pageturner-primary)] text-white rounded-lg hover:bg-[var(--pageturner-secondary)] transition-colors font-medium">
                        Verify and Enable 2FA
                    </button>
                </div>

                <div class="text-sm text-gray-600">
                    Didn't receive the code? 
                    <button type="button" onclick="resendCode()" class="text-[var(--pageturner-primary)] hover:text-[var(--pageturner-secondary)] font-medium">
                        Resend
                    </button>
                </div>

                <div class="pt-4">
                    <a href="{{ route('profile.two-factor') }}" class="text-gray-600 hover:text-gray-800">
                        ← Back to 2FA Settings
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
function resendCode() {
    fetch('{{ route("profile.two-factor.setup") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: 'email'
        })
    })
    .then(response => response.json())
    .then(data => {
        alert('A new OTP code has been sent to your email.');
    })
    .catch(error => {
        alert('Failed to resend code. Please try again.');
    });
}
</script>