@extends('layouts.app')

@section('title', 'Edit Order - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">
                Edit Order #{{ $order->order_number }}
            </h1>
            <p class="text-gray-100/80 mt-2">Update order details and tracking information</p>
        </div>
        <a href="{{ route('orders.show', $order->id) }}" 
           class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-md transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            View Order Details
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Order Summary Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="bg-[#F5EBDC] px-6 py-4 border-b border-[#E6D5B8]">
            <h2 class="text-lg font-semibold text-[#5D4037] flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Order Summary
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-[#5D4037] w-32">Order Number:</span>
                        <span class="text-sm text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-[#5D4037] w-32">Order Date:</span>
                        <span class="text-sm text-gray-900">{{ $order->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                    @if(Auth::user()->isAdmin())
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-[#5D4037] w-32">Customer:</span>
                        <span class="text-sm text-gray-900">{{ $order->user->name ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-[#5D4037] w-32">Total Amount:</span>
                        <span class="text-lg font-bold text-[#8B4513]">${{ number_format($order->total_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-[#5D4037] w-32">Items:</span>
                        <span class="text-sm text-gray-900">{{ $order->items->count() }} item(s)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-[#F5EBDC] px-6 py-4 border-b border-[#E6D5B8]">
            <h2 class="text-lg font-semibold text-[#5D4037] flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Order Details
            </h2>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('orders.update', $order->id) }}">
                @csrf
                @method('PUT')

                <!-- Status and Payment Status Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Status Field -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-[#5D4037] mb-2">
                            Order Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#8B4513] focus:border-[#8B4513] @error('status') border-red-500 @enderror" 
                                name="status" 
                                required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        
                        <!-- Status Help Text -->
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-yellow-400 mr-1"></span> Pending
                            </span>
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-[#F4A460] mr-1"></span> Processing
                            </span>
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-blue-400 mr-1"></span> Shipped
                            </span>
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-green-400 mr-1"></span> Delivered
                            </span>
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-red-400 mr-1"></span> Cancelled
                            </span>
                        </div>
                    </div>

                    <!-- Payment Status Field -->
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-[#5D4037] mb-2">
                            Payment Status <span class="text-red-500">*</span>
                        </label>
                        <select id="payment_status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#8B4513] focus:border-[#8B4513] @error('payment_status') border-red-500 @enderror" 
                                name="payment_status" 
                                required>
                            @foreach($paymentStatuses as $value => $label)
                                <option value="{{ $value }}" {{ $order->payment_status == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        
                        <!-- Payment Status Help Text -->
                        <div class="mt-2 text-xs text-gray-500">
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-yellow-400 mr-1"></span> Pending
                            </span>
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-green-400 mr-1"></span> Paid
                            </span>
                            <span class="inline-flex items-center mr-3">
                                <span class="w-2 h-2 rounded-full bg-red-400 mr-1"></span> Failed
                            </span>
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-purple-400 mr-1"></span> Refunded
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tracking Number Field -->
                <div class="mb-6">
                    <label for="tracking_number" class="block text-sm font-medium text-[#5D4037] mb-2">
                        Tracking Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <input id="tracking_number" 
                               type="text" 
                               class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-md focus:ring-[#8B4513] focus:border-[#8B4513] @error('tracking_number') border-red-500 @enderror" 
                               name="tracking_number" 
                               value="{{ old('tracking_number', $order->tracking_number) }}"
                               placeholder="Enter tracking number if available">
                    </div>
                    @error('tracking_number')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Add tracking number once the order has been shipped</p>
                </div>

                <!-- Notes Field -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-[#5D4037] mb-2">
                        Order Notes
                    </label>
                    <textarea id="notes" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#8B4513] focus:border-[#8B4513] @error('notes') border-red-500 @enderror" 
                              name="notes" 
                              rows="4"
                              placeholder="Add any notes about this order...">{{ old('notes', $order->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Internal notes about the order (customer requests, special instructions, etc.)</p>
                </div>

                <!-- Read-only Information Section -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-[#5D4037] mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Additional Information (Read-only)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Shipping Address -->
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Shipping Address</p>
                            <p class="text-sm text-gray-900">{{ $order->shipping_address ?? 'N/A' }}</p>
                        </div>
                        
                        <!-- Billing Address -->
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Billing Address</p>
                            <p class="text-sm text-gray-900">{{ $order->billing_address ?? 'N/A' }}</p>
                        </div>
                        
                        <!-- Payment Method -->
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                            <p class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $order->payment_method ?? 'N/A') }}</p>
                        </div>
                        
                        <!-- Last Updated -->
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Last Updated</p>
                            <p class="text-sm text-gray-900">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('orders.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#8B4513] text-white rounded-md hover:bg-[#D2691E] transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Warning for Admin Only -->
    @if(Auth::user()->isAdmin())
    <div class="mt-4 text-xs text-gray-500 text-center">
        <svg class="w-4 h-4 inline-block mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Changes made here will be reflected immediately. Please ensure accuracy before updating.
    </div>
    @endif
</div>
@endsection