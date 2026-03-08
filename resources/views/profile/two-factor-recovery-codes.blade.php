@extends('layouts.app')

@section('title', 'Recovery Codes - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Recovery Codes</h1>
            <p class="text-gray-100/80 mt-2">Save these codes in a secure location</p>
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

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[var(--pageturner-dark)] page-turner-font mb-2">Your Recovery Codes</h2>
            <p class="text-gray-700">
                Each code can only be used once. Store these codes in a safe place - 
                they are your only way to access your account if you lose your 2FA device.
            </p>
        </div>

        <!-- Warning Message -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-yellow-700">
                    <strong>Important:</strong> These codes will not be shown again. 
                    Download or print them now.
                </p>
            </div>
        </div>

        <!-- Recovery Codes Grid -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            @foreach($recoveryCodes as $code)
                <div class="bg-gray-50 p-3 rounded-lg font-mono text-sm text-center border border-gray-200">
                    {{ $code }}
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center">
            <button onclick="downloadCodes()" 
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Codes
            </button>
            
            <button onclick="printCodes()" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Codes
            </button>
            
            <form method="POST" action="{{ route('profile.two-factor.recovery-codes.regenerate') }}" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Are you sure? This will invalidate your existing recovery codes.')"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Regenerate Codes
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('profile.two-factor') }}" class="text-[var(--pageturner-primary)] hover:text-[var(--pageturner-secondary)] transition-colors">
                ← Back to 2FA Settings
            </a>
        </div>
    </div>
</div>
@endsection

<script>
function downloadCodes() {
    const codes = @json($recoveryCodes);
    const content = 'PageTurner Recovery Codes\n' +
                   '========================\n\n' +
                   codes.join('\n') +
                   '\n\nKeep these codes safe. Each code can only be used once.';
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'pageturner-recovery-codes.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function printCodes() {
    const codes = @json($recoveryCodes);
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>PageTurner Recovery Codes</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1 { color: #8B4513; }
                    .codes { 
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 10px;
                        margin: 20px 0;
                    }
                    .code {
                        font-family: monospace;
                        padding: 10px;
                        background: #f5f5f5;
                        border: 1px solid #ddd;
                        text-align: center;
                    }
                    .warning {
                        color: #856404;
                        background: #fff3cd;
                        border: 1px solid #ffeeba;
                        padding: 10px;
                        border-radius: 5px;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <h1>PageTurner Recovery Codes</h1>
                <div class="codes">
                    ${codes.map(code => `<div class="code">${code}</div>`).join('')}
                </div>
                <div class="warning">
                    <strong>Important:</strong> Keep these codes safe. Each code can only be used once.
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
</script>