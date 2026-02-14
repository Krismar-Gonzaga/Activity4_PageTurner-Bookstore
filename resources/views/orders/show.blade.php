@extends('layouts.app')

@section('title', 'Order Confirmation - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-[var(--pageturner-dark)]">Order Confirmation</h1>
            <p class="text-gray-100/80 mt-2">Thank you for your purchase!</p>
        </div>
        <a href="{{ route('books.index') }}" 
           class="text-[var(--pageturner-light)] hover:text-[var(--pageturner-accent)] font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Continue Shopping
        </a>
    </div>
@endsection

@section('content')
<style>
    .order-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .success-message {
        background: #d1fae5;
        border: 1px solid #10b981;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .success-message::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .success-icon {
        width: 4rem;
        height: 4rem;
        background: #10b981;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .success-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #065f46;
        margin-bottom: 0.5rem;
    }

    .order-number {
        font-size: 1.25rem;
        color: #047857;
        font-weight: 600;
    }

    /* Order Details Card */
    .order-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px -5px rgba(139, 69, 19, 0.15);
        border: 1px solid rgba(244, 164, 96, 0.3);
        overflow: hidden;
        position: relative;
        margin-bottom: 2rem;
    }

    .order-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(135deg, #8B4513, #F4A460);
    }

    .order-header {
        padding: 1.5rem;
        background: #F5EBDC;
        border-bottom: 1px solid rgba(244, 164, 96, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .order-status {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-processing {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .order-body {
        padding: 1.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .info-section {
        background: #FDF8F0;
        padding: 1.25rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(244, 164, 96, 0.2);
    }

    .info-title {
        font-size: 1rem;
        font-weight: 600;
        color: #8B4513;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(139, 69, 19, 0.1);
    }

    .info-content {
        color: #4b5563;
        line-height: 1.6;
    }

    .info-content p {
        margin-bottom: 0.5rem;
    }

    .info-label {
        font-weight: 500;
        color: #374151;
    }

    /* Order Items */
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table th {
        text-align: left;
        padding: 1rem;
        background: #F5EBDC;
        color: #8B4513;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .items-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(139, 69, 19, 0.1);
    }

    .items-table tr:last-child td {
        border-bottom: none;
    }

    .item-image {
        width: 60px;
        height: 80px;
        object-fit: cover;
        border-radius: 0.5rem;
    }

    .item-title {
        font-weight: 600;
        color: #1f2937;
    }

    .item-author {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .item-price {
        color: #8B4513;
        font-weight: 600;
    }

    .item-total {
        font-weight: 700;
        color: #8B4513;
    }

    /* Order Summary */
    .order-summary {
        background: #FDF8F0;
        padding: 1.5rem;
        border-radius: 0.75rem;
        margin-top: 2rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        color: #4b5563;
    }

    .summary-row.total {
        font-size: 1.25rem;
        font-weight: 700;
        color: #8B4513;
        border-top: 2px solid rgba(139, 69, 19, 0.2);
        padding-top: 1rem;
        margin-top: 0.5rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #8B4513, #D2691E);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #D2691E, #8B4513);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #8B4513;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 2px solid #8B4513;
    }

    .btn-secondary:hover {
        background: #F5EBDC;
        transform: translateY(-2px);
    }

    @media print {
        .action-buttons, .btn-primary, .btn-secondary, .success-message {
            display: none;
        }
    }
</style>

<div class="order-container">
    @if(session('success'))
        <div class="success-message">
            <div class="success-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="success-title">{{ session('success') }}</h2>
            <p class="order-number">Order #{{ $order->order_number }}</p>
            <p class="text-gray-600 mt-2">A confirmation email has been sent to your email address.</p>
        </div>
    @endif

    <div class="order-card">
        <div class="order-header">
            <div>
                <h2 class="text-xl font-bold text-[#8B4513]">Order #{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-600 mt-1">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            <div>
                <span class="order-status status-{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <div class="order-body">
            <!-- Customer Information -->
            <div class="info-grid">
                <div class="info-section">
                    <h3 class="info-title">Shipping Address</h3>
                    <div class="info-content">
                        <p><span class="info-label">{{ $order->user->name }}</span></p>
                        <p>{{ $order->shipping_address }}</p>
                        <p>Email: {{ $order->user->email }}</p>
                        <p>Phone: {{ $order->phone ?? 'Not provided' }}</p>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-title">Payment Information</h3>
                    <div class="info-content">
                        <p><span class="info-label">Method:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <p><span class="info-label">Status:</span> {{ ucfirst($order->payment_status) }}</p>
                        @if($order->tracking_number)
                            <p><span class="info-label">Tracking #:</span> {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <h3 class="text-lg font-semibold text-[#8B4513] mb-4">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-4">
                                        @if($item->book->cover_image)
                                            <img src="{{ asset('storage/' . $item->book->cover_image) }}" 
                                                 alt="{{ $item->book->title }}" 
                                                 class="item-image">
                                        @else
                                            <div class="w-[60px] h-[80px] bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="item-title">{{ $item->book->title }}</div>
                                            <div class="item-author">by {{ $item->book->author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="item-price">${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="item-total">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-row">
                    <span>Tax:</span>
                    <span>$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            @if($order->notes)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-2">Order Notes:</h4>
                    <p class="text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('books.index') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Continue Shopping
        </a>
        <a href="{{ route('orders.index') }}" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            View All Orders
        </a>
        <button onclick="window.print()" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Receipt
        </button>
    </div>
</div>
@endsection