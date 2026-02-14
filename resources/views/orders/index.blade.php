@extends('layouts.app')

@section('title', 'Orders - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-[var(--pageturner-dark)]">
                @if(Auth::user()->isAdmin())
                    All Orders
                @else
                    My Orders
                @endif
            </h1>
            <p class="text-gray-100/80 mt-2">Manage your book orders</p>
        </div>
    </div>
@endsection

@section('content')
    @if($orders->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#F5EBDC]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Order #</th>
                            @if(Auth::user()->isAdmin())
                                <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Customer</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#5D4037] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $order->id }}</div>
                                </td>
                                
                                @if(Auth::user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->user->email ?? '' }}</div>
                                    </td>
                                @endif
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->items->count() }} items
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#8B4513]">
                                    ${{ number_format($order->total_amount ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'shipped' => 'bg-blue-100 text-blue-800',
                                            'processing' => 'bg-[#F4A460] text-[#5D4037]',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $paymentColors = [
                                            'paid' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                        ];
                                        $paymentColor = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $paymentColor }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('orders.show', $order->id) }}" 
                                       class="text-[#8B4513] hover:text-[#D2691E] mr-3">
                                        View
                                    </a>
                                    
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('orders.edit', $order->id) }}" 
                                           class="text-[#F4A460] hover:text-[#D2691E]">
                                            Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No orders found</h3>
            @if(!Auth::user()->isAdmin())
                <p class="text-gray-600 mb-6">You haven't placed any orders yet.</p>
                <a href="{{ route('books.index') }}" 
                   class="inline-flex items-center bg-[#8B4513] text-white px-6 py-3 rounded-md hover:bg-[#D2691E] transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Start Shopping
                </a>
            @endif
        </div>
    @endif
@endsection
