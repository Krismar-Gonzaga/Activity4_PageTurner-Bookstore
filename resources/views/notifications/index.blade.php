@extends('layouts.app')

@section('title', 'Notifications - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Notifications</h1>
            <p class="text-gray-100/80 mt-2">Stay updated with your activity</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition">
                    Mark All as Read
                </button>
            </form>
        @endif
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

        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <!-- Icon based on type -->
                            <div class="flex-shrink-0">
                                @if(isset($notification->data['type']))
                                    @switch($notification->data['type'])
                                        @case('order')
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            @break
                                        @case('security')
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            @break
                                        @default
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                    @endswitch
                                @endif
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                                <p class="text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" 
                                  onsubmit="return confirm('Delete this notification?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(isset($notification->data['action_url']))
                        <div class="mt-3">
                            <a href="{{ $notification->data['action_url'] }}" 
                               class="text-sm text-[var(--pageturner-primary)] hover:text-[var(--pageturner-secondary)]">
                                View Details →
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700">No notifications</h3>
                    <p class="text-gray-500 mt-1">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection