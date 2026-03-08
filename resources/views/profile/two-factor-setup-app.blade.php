@extends('layouts.app')

@section('title', 'Setup Authenticator App - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Setup Authenticator App</h1>
            <p class="text-gray-100/80 mt-2">Scan the QR code with your authenticator app</p>
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

        <div class="max-w-2xl mx-auto">
            <!-- Step 1: Install Authenticator App -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-[var(--pageturner-dark)] mb-3">Step 1: Install an Authenticator App</h2>
                <p class="text-gray-700 mb-2">Download and install one of these authenticator apps on your mobile device:</p>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Google Authenticator (iOS/Android)</li>
                    <li>Microsoft Authenticator (iOS/Android)</li>
                    <li>Authy (iOS/Android)</li>
                </ul>
            </div>

            <!-- Step 2: Scan QR Code -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-[var(--pageturner-dark)] mb-3">Step 2: Scan QR Code</h2>
                <p class="text-gray-700 mb-4">Open your authenticator app and scan this QR code:</p>
                
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ urlencode($qrCodeUrl) }}&choe=UTF-8" 
     alt="QR Code" 
     class="mx-auto"
     style="width: 200px; height: 200px;">
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Can't scan the QR code? Enter this code manually:</p>
                    <div class="flex items-center space-x-2">
                        <code class="bg-gray-100 px-4 py-2 rounded font-mono text-sm">{{ $secret }}</code>
                        <button onclick="copyToClipboard('{{ $secret }}')" class="text-[var(--pageturner-primary)] hover:text-[var(--pageturner-secondary)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Verify Code -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-[var(--pageturner-dark)] mb-3">Step 3: Verify Setup</h2>
                <p class="text-gray-700 mb-4">Enter the 6-digit code from your authenticator app to complete setup:</p>
                
                <form method="POST" action="{{ route('profile.two-factor.verify-setup') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                        <input type="text" 
                               name="code" 
                               id="code" 
                               maxlength="6"
                               pattern="[0-9]{6}"
                               inputmode="numeric"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[var(--pageturner-primary)] focus:border-transparent"
                               placeholder="000000"
                               required>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('profile.two-factor') }}" class="text-gray-600 hover:text-gray-800">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-[var(--pageturner-primary)] text-white rounded-lg hover:bg-[var(--pageturner-secondary)] transition-colors font-medium">
                            Verify and Enable
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Secret key copied to clipboard!');
    }, function() {
        alert('Failed to copy secret key.');
    });
}
</script>