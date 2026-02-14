@extends('layouts.app')

@section('title', 'Profile - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-[var(--pageturner-dark)]">Profile</h1>
            <p class="text-gray-100/80 mt-2">Manage your account information</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
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

    <!-- Delete Account -->
    <div class="rounded-xl p-6 sm:p-8 bg-[var(--pageturner-very-light)] shadow-sm border border-red-200 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-red-700 via-red-500 to-[var(--pageturner-secondary)]"></div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-red-700 page-turner-font mb-2">Delete Account</h2>
            <p class="text-gray-700">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
        </div>
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
