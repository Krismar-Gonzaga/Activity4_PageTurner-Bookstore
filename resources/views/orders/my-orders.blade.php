@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
        <div class="flex gap-2">
            <a href="{{ route('my-orders.export', ['format' => 'pdf']) }}" class="px-3 py-2 border rounded-md text-sm hover:bg-gray-50">Export Personal Order History (PDF Invoice)</a>
        </div>
    </div>

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3">Order #</th>
                    <th class="text-left px-4 py-3">Date</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Payment</th>
                    <th class="text-left px-4 py-3">Total</th>
                    <th class="text-left px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                        <td class="px-4 py-3">{{ ucfirst($order->payment_status) }}</td>
                        <td class="px-4 py-3">P{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('orders.show', $order) }}" class="text-[#8B4513] hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $orders->links() }}
    </div>
</div>
@endsection
