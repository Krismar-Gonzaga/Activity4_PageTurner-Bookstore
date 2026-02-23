@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Cards -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Books</h3>
            <p class="text-3xl font-bold">{{ $stats['total_books'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Orders</h3>
            <p class="text-3xl font-bold">{{ $stats['total_orders'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Total Users</h3>
            <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm">Categories</h3>
            <p class="text-3xl font-bold">{{ $stats['total_categories'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
            <div class="space-y-4">
                @foreach($stats['recent_orders'] as $order)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-medium">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded 
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Users</h2>
            <div class="space-y-4">
                @foreach($stats['recent_users'] as $user)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        </div>
                        <span class="text-sm text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection