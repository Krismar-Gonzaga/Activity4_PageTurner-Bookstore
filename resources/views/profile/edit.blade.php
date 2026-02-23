@extends('layouts.app')

@section('title', 'Profile - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Profile</h1>
            <p class="text-gray-100/80 mt-2">Manage your account information</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Profile Picture -->
    <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-[rgba(139,69,19,0.12)] relative overflow-hidden mb-6">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[var(--pageturner-dark)] via-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]"></div>
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="flex-1">
                
                
                <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <!-- Preview Section -->
                    <div class="md:w-64 bg-[var(--pageturner-light)] rounded-lg p-6 text-center">
                        <h3 class="font-semibold text-[var(--pageturner-dark)] mb-3">Profile Picture</h3>
                        <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-[var(--pageturner-primary)] to-[var(--pageturner-secondary)] flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-3 overflow-hidden">
                            <div id="preview-container">
                                
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                        alt="{{ auth()->user()->name }}" 
                                        class="w-full h-full object-cover rounded-full">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            
                            </div>
                            <span id="preview-initial" class="{{ auth()->user()->profile_picture ? 'hidden' : '' }}">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Current picture</p>
                            @if(auth()->user()->profile_picture)
                                <button type="button" 
                                        onclick="document.getElementById('remove-profile-picture-form').submit();"
                                        class="text-sm text-red-600 hover:text-red-800 mt-1">
                                    Remove picture
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Choose new picture</label>
                        <div class="flex items-center space-x-4">
                            <input type="file" 
                                name="profile_picture" 
                                id="profile_picture" 
                                accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[var(--pageturner-primary)] file:text-white hover:file:bg-[var(--pageturner-secondary)] transition-colors cursor-pointer">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Accepted formats: JPEG, PNG, GIF. Max size: 2MB</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-[var(--pageturner-primary)] text-black rounded-lg hover:bg-[var(--pageturner-secondary)] transition-colors font-medium">
                            Upload Picture
                        </button>
                    </div>
                </form>

                <!-- Remove Picture Form (hidden) -->
                @if(auth()->user()->profile_picture)
                    <form id="remove-profile-picture-form" 
                        action="{{ route('profile.picture.remove') }}" 
                        method="POST" 
                        class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>

            
        </div>
    </div>

    <!-- Profile Information -->
    <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-[rgba(139,69,19,0.12)] relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[var(--pageturner-dark)] via-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]"></div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[var(--pageturner-dark)] page-turner-font mb-2">Profile Information</h2>
            <p class="text-gray-700">Update your account's profile information and email address.</p>
        </div>
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-[rgba(139,69,19,0.12)] relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[var(--pageturner-dark)] via-[var(--pageturner-primary)] to-[var(--pageturner-secondary)]"></div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-[var(--pageturner-dark)] page-turner-font mb-2">Update Password</h2>
            <p class="text-gray-700">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    
</div>
@endsection


<script>
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                const previewInitial = document.getElementById('preview-initial');
                
                if (!preview) {
                    // Create preview image if it doesn't exist
                    const container = document.getElementById('preview-container');
                    const img = document.createElement('img');
                    img.id = 'preview';
                    img.className = 'w-full h-full object-cover';
                    img.src = e.target.result;
                    container.innerHTML = '';
                    container.appendChild(img);
                } else {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                
                if (previewInitial) {
                    previewInitial.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>