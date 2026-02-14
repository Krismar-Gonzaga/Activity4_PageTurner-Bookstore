@extends('layouts.app')

@section('title', 'Checkout - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-[var(--pageturner-dark)]">Checkout</h1>
            <p class="text-gray-100/80 mt-2">Complete your purchase</p>
        </div>
        <a href="{{ route('cart.index') }}" 
           class="text-[var(--pageturner-light)] hover:text-[var(--pageturner-accent)] font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Cart
        </a>
    </div>
@endsection

@section('content')
<style>
    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr 1.5fr;
        }
    }

    /* Order Summary Card */
    .summary-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px -5px rgba(139, 69, 19, 0.15);
        border: 1px solid rgba(244, 164, 96, 0.3);
        overflow: hidden;
        position: relative;
        height: fit-content;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(135deg, #8B4513, #F4A460);
    }

    .summary-header {
        padding: 1.5rem;
        background: #F5EBDC;
        border-bottom: 1px solid rgba(244, 164, 96, 0.3);
    }

    .summary-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(139, 69, 19, 0.1);
    }

    .summary-item-info {
        flex: 1;
    }

    .summary-item-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .summary-item-price {
        color: #8B4513;
        font-weight: 600;
    }

    .summary-item-quantity {
        color: #6b7280;
        font-size: 0.9rem;
        margin-left: 1rem;
    }

    .summary-totals {
        padding: 1.5rem;
        background: #FDF8F0;
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
        margin-bottom: 0;
    }

    /* Checkout Form Card */
    .form-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px -5px rgba(139, 69, 19, 0.15);
        border: 1px solid rgba(244, 164, 96, 0.3);
        overflow: hidden;
        position: relative;
    }

    .form-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(135deg, #8B4513, #F4A460);
    }

    .form-header {
        padding: 1.5rem;
        background: #F5EBDC;
        border-bottom: 1px solid rgba(244, 164, 96, 0.3);
    }

    .form-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }

    .form-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-input:focus {
        outline: none;
        border-color: #8B4513;
        box-shadow: 0 0 0 2px rgba(139, 69, 19, 0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .place-order-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #8B4513, #D2691E);
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .place-order-btn:hover {
        background: linear-gradient(135deg, #D2691E, #8B4513);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
    }

    .place-order-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #ef4444;
        color: #b91c1c;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Payment Methods */
    .payment-methods {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .payment-method {
        flex: 1;
        padding: 1rem;
        border: 2px solid #d1d5db;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method:hover {
        border-color: #8B4513;
    }

    .payment-method.selected {
        border-color: #8B4513;
        background: #F5EBDC;
    }

    .payment-method svg {
        width: 2rem;
        height: 2rem;
        margin: 0 auto 0.5rem;
        color: #8B4513;
    }

    .payment-method span {
        display: block;
        font-size: 0.9rem;
        font-weight: 500;
        color: #4b5563;
    }
</style>

<div class="checkout-container">
    @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="checkout-grid">
        <!-- Order Summary -->
        <div>
            <div class="summary-card">
                <div class="summary-header">
                    <h2 class="summary-title">Order Summary</h2>
                    <p class="text-sm text-gray-600">{{ count($cartItems) }} items</p>
                </div>

                @foreach($cartItems as $id => $item)
                    <div class="summary-item">
                        <div class="summary-item-info">
                            <div class="summary-item-title">{{ $item['title'] }}</div>
                            <div class="summary-item-price">${{ number_format($item['price'], 2) }}</div>
                        </div>
                        <div class="summary-item-quantity">x{{ $item['quantity'] }}</div>
                    </div>
                @endforeach

                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>${{ number_format($total, 2) }}</span>
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
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        
        <div>
            <div class="form-card">
                <div class="form-header">
                    <h2>Shipping Information</h2>
                </div>

                <div class="form-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf

                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" 
                                id="name" 
                                name="name" 
                                value="{{ auth()->user()->name ?? '' }}" 
                                class="form-input" 
                                required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" 
                                id="email" 
                                name="email" 
                                value="{{ auth()->user()->email ?? '' }}" 
                                class="form-input" 
                                required>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" 
                                id="phone" 
                                name="phone" 
                                class="form-input" 
                                required>
                        </div>

                        <!-- Shipping Address -->
                        <div class="form-group">
                            <label for="shipping_address" class="form-label">Shipping Address *</label>
                            <input type="text" 
                                id="shipping_address" 
                                name="shipping_address" 
                                class="form-input" 
                                placeholder="Street address"
                                required>
                        </div>

                        <!-- Billing Address -->
                        <div class="form-group">
                            <label for="billing_address" class="form-label">Billing Address *</label>
                            <input type="text" 
                                id="billing_address" 
                                name="billing_address" 
                                class="form-input" 
                                placeholder="Street address"
                                required>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label for="city" class="form-label">City *</label>
                            <input type="text" 
                                id="city" 
                                name="city" 
                                class="form-input" 
                                required>
                        </div>

                        <!-- Zip Code -->
                        <div class="form-group">
                            <label for="zip" class="form-label">Zip Code *</label>
                            <input type="text" 
                                id="zip" 
                                name="zip" 
                                class="form-input" 
                                required>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group">
                            <label class="form-label">Payment Method *</label>
                            <div class="payment-methods">
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="credit_card" class="hidden" checked>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span>Credit Card</span>
                                </label>

                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="paypal" class="hidden">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span>PayPal</span>
                                </label>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="form-group">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea id="notes" 
                                    name="notes" 
                                    rows="3" 
                                    class="form-input" 
                                    placeholder="Any special instructions?"></textarea>
                        </div>

                        <button type="submit" class="place-order-btn">
                            Place Order - ${{ number_format($total, 2) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Payment method selection
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethods = document.querySelectorAll('.payment-method');
        
        paymentMethods.forEach(method => {
            method.addEventListener('click', function() {
                // Remove selected class from all methods
                paymentMethods.forEach(m => m.classList.remove('selected'));
                
                // Add selected class to clicked method
                this.classList.add('selected');
                
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });

        
    });
</script>
@endsection