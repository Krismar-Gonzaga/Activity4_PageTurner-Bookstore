@extends('layouts.app')

@section('title', 'Shopping Cart - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Shopping Cart</h1>
            <p class="text-gray-100/80 mt-2">Review and manage your items</p>
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
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .cart-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 30px -5px rgba(139, 69, 19, 0.15);
        border: 1px solid rgba(244, 164, 96, 0.3);
        overflow: hidden;
        position: relative;
    }

    .cart-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(135deg, #8B4513, #F4A460);
    }

    .cart-item {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(139, 69, 19, 0.1);
        transition: background-color 0.3s ease;
    }

    .cart-item:hover {
        background-color: #FDF8F0;
    }

    .cart-item-image {
        width: 80px;
        height: 100px;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .cart-item-details {
        flex: 1;
        margin-left: 1.5rem;
    }

    .cart-item-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .cart-item-author {
        color: #6b7280;
        font-size: 0.9rem;
    }

    .cart-item-price {
        color: #8B4513;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .quantity-input {
        width: 70px;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        text-align: center;
        font-size: 0.9rem;
    }

    .quantity-input:focus {
        outline: none;
        border-color: #8B4513;
        box-shadow: 0 0 0 2px rgba(139, 69, 19, 0.1);
    }

    .remove-btn {
        color: #ef4444;
        transition: color 0.3s ease;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }

    .remove-btn:hover {
        color: #b91c1c;
    }

    .cart-summary {
        padding: 1.5rem;
        background: #F5EBDC;
        border-top: 1px solid rgba(244, 164, 96, 0.3);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .summary-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #8B4513;
        border-top: 2px solid rgba(139, 69, 19, 0.2);
        padding-top: 1rem;
        margin-top: 0.5rem;
    }

    .checkout-btn {
        display: block;
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #8B4513, #D2691E);
        color: white;
        text-align: center;
        font-weight: 600;
        font-size: 1.1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .checkout-btn:hover {
        background: linear-gradient(135deg, #D2691E, #8B4513);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
    }

    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-cart-icon {
        width: 6rem;
        height: 6rem;
        color: #d1d5db;
        margin: 0 auto 1.5rem;
    }

    .empty-cart-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .empty-cart-text {
        color: #6b7280;
        margin-bottom: 2rem;
    }

    .browse-btn {
        display: inline-block;
        padding: 0.75rem 2rem;
        background: #8B4513;
        color: white;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: background 0.3s ease;
    }

    .browse-btn:hover {
        background: #D2691E;
    }

    .alert-success {
        background: #d1fae5;
        border: 1px solid #10b981;
        color: #065f46;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #ef4444;
        color: #b91c1c;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .clear-cart-btn {
        color: #ef4444;
        background: none;
        border: 1px solid #ef4444;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .clear-cart-btn:hover {
        background: #ef4444;
        color: white;
    }

    .update-btn {
        background: #8B4513;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background 0.3s ease;
    }

    .update-btn:hover {
        background: #D2691E;
    }

    /* Add to your existing styles */
    .stock-error-message {
        animation: slideDown 0.3s ease;
        width: 100%;
        margin-top: 0.5rem;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .quantity-field.error {
        border-color: #ef4444 !important;
        background-color: #fee2e2 !important;
    }

    /* Tooltip for max stock info */
    .quantity-field[title] {
        cursor: help;
    }
</style>

<div class="cart-container">
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="cart-card">
        @if(empty($cartItems))
            <div class="empty-cart">
                <svg class="empty-cart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="empty-cart-title">Your cart is empty</h2>
                <p class="empty-cart-text">Looks like you haven't added any books to your cart yet.</p>
                <a href="{{ route('books.index') }}" class="browse-btn">
                    Browse Books
                </a>
            </div>
        @else
            @foreach($cartItems as $id => $item)
                <div class="cart-item">
                    <img src="{{ asset('storage/' . ($item['image'] ?? 'covers/default.jpg')) }}" 
                         alt="{{ $item['title'] }}" 
                         class="cart-item-image">
                    
                    <div class="cart-item-details">
                        <h3 class="cart-item-title">{{ $item['title'] }}</h3>
                        <p class="cart-item-author">Price: ${{ number_format($item['price'], 2) }}</p>
                        <p class="text-sm text-gray-500">Available Stock: {{ $item['stock'] }}</p>

                    </div>

                    <div class="cart-item-actions">
                        <form action="{{ route('cart.update', $id) }}" method="POST" class="quantity-form" data-item-id="{{ $id }}">
                            @csrf
                            <input type="number" 
                                name="quantity" 
                                value="{{ $item['quantity'] }}" 
                                min="1" 
                                max="{{ $item['stock'] }}"
                                class="quantity-input quantity-field" 
                                data-item-id="{{ $id }}"
                                data-price="{{ $item['price'] }}"
                                data-original="{{ $item['quantity'] }}">
                        </form>

                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                            @csrf
                            <button type="submit" class="remove-btn" onclick="return confirm('Remove this item from cart?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>

                <div class="flex justify-between items-center mt-6">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="clear-cart-btn" onclick="return confirm('Clear your entire cart?')">
                            Clear Cart
                        </button>
                    </form>

                    <a href="{{ route('cart.checkout') }}" class="checkout-btn" style="width: auto; padding: 0.75rem 2rem;">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let updateTimeout;
    
    // Function to update cart total
    function updateCartTotal() {
        let subtotal = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const quantityInput = item.querySelector('.quantity-field');
            const price = parseFloat(quantityInput?.dataset.price || 0);
            const quantity = parseInt(quantityInput?.value || 0);
            subtotal += price * quantity;
        });
        
        // Update displays
        const subtotalElement = document.querySelector('.summary-row:first-child span:last-child');
        const totalElement = document.querySelector('.summary-total span:last-child');
        
        if (subtotalElement) subtotalElement.textContent = '$' + subtotal.toFixed(2);
        if (totalElement) totalElement.textContent = '$' + subtotal.toFixed(2);
    }

    // Function to send AJAX update
    function updateCartItem(itemId, quantity, inputElement) {
        // Show loading state
        inputElement.classList.add('updating');
        inputElement.style.opacity = '0.7';
        
        // Remove any existing error message for this item
        removeItemError(itemId);
        
        fetch(`/cart/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update original value
                inputElement.dataset.original = quantity;
                inputElement.style.borderColor = '#10b981';
                
                // Update max attribute if provided
                if (data.max_stock) {
                    inputElement.max = data.max_stock;
                }
                
                // Update totals with server-calculated values
                updateCartTotal(data.total);
                
                // Show success indicator
                showNotification(data.message, 'success');
                
                // Reset border after delay
                setTimeout(() => {
                    inputElement.style.borderColor = '#d1d5db';
                }, 1000);
            } else {
                // Revert to original value
                inputElement.value = inputElement.dataset.original;
                inputElement.style.borderColor = '#ef4444';
                
                
                
                // Show notification
                showNotification(data.message, 'error');
                
                setTimeout(() => {
                    inputElement.style.borderColor = '#d1d5db';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            inputElement.value = inputElement.dataset.original;
            inputElement.style.borderColor = '#ef4444';
            showNotification('Error updating cart', 'error');
            
            setTimeout(() => {
                inputElement.style.borderColor = '#d1d5db';
            }, 2000);
        })
        .finally(() => {
            inputElement.classList.remove('updating');
            inputElement.style.opacity = '1';
        });
    }

    function showStockError(itemId, message, maxStock) {
        // Just show a floating notification instead of inline error
        showNotification(message, 'error');
        
        // Highlight the quantity input briefly
        const input = document.querySelector(`[data-item-id="${itemId}"]`);
        if (input) {
            input.style.borderColor = '#ef4444';
            input.style.backgroundColor = '#fee2e2';
            
            // Reset styling after delay
            setTimeout(() => {
                input.style.borderColor = '#d1d5db';
                input.style.backgroundColor = 'white';
            }, 2000);
        }
        
        // Update the max attribute to prevent future invalid entries
        if (maxStock) {
            input.max = maxStock;
        }
    }

    function removeItemError(itemId) {
        // Just reset input styling if needed
        const input = document.querySelector(`[data-item-id="${itemId}"]`);
        if (input) {
            input.style.borderColor = '#d1d5db';
            input.style.backgroundColor = 'white';
        }
    }

    // Update this function to use the server total
    function updateCartTotal(serverTotal = null) {
        if (serverTotal !== null) {
            // Use server-calculated total
            const subtotalElement = document.querySelector('.summary-row:first-child span:last-child');
            const totalElement = document.querySelector('.summary-total span:last-child');
            
            if (subtotalElement) subtotalElement.textContent = '$' + serverTotal.toFixed(2);
            if (totalElement) totalElement.textContent = '$' + serverTotal.toFixed(2);
        } else {
            // Fallback to client-side calculation (for initial load or if server total not available)
            let subtotal = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const quantityInput = item.querySelector('.quantity-field');
                const price = parseFloat(quantityInput?.dataset.price || 0);
                const quantity = parseInt(quantityInput?.value || 0);
                subtotal += price * quantity;
            });
            
            const subtotalElement = document.querySelector('.summary-row:first-child span:last-child');
            const totalElement = document.querySelector('.summary-total span:last-child');
            
            if (subtotalElement) subtotalElement.textContent = '$' + subtotal.toFixed(2);
            if (totalElement) totalElement.textContent = '$' + subtotal.toFixed(2);
        }
    }

    // Handle quantity changes
    document.querySelectorAll('.quantity-field').forEach(input => {
        // Store original value
        input.dataset.original = input.value;
        
        input.addEventListener('input', function() {
            // Remove error styling when user starts typing
            removeItemError(this.dataset.itemId);
            
            // Get current values
            let quantity = parseInt(this.value);
            const maxStock = parseInt(this.max) || 99;
            const originalQuantity = parseInt(this.dataset.original);
            
            // Live preview update (always update total as user types)
            updateCartTotal();
            
            // Check if the new quantity is within stock limits
            if (!isNaN(quantity) && quantity >= 1 && quantity <= maxStock) {
                // Valid quantity within stock
                this.style.borderColor = '#10b981';
                this.style.backgroundColor = '#f0fdf4';
                
                // If it's different from original and within stock, auto-update after a short delay
                if (quantity !== originalQuantity) {
                    // Clear any pending timeout
                    if (window.updateTimeout) clearTimeout(window.updateTimeout);
                    
                    // Auto-update after 1 second of valid input
                    window.updateTimeout = setTimeout(() => {
                        // Only update if still within stock
                        const currentQuantity = parseInt(this.value);
                        if (currentQuantity <= maxStock && currentQuantity >= 1) {
                            updateCartItem(this.dataset.itemId, currentQuantity, this);
                        }
                    }, 1000);
                }
            } else if (!isNaN(quantity) && quantity > maxStock) {
                // Quantity exceeds stock - show red but don't auto-update
                this.style.borderColor = '#ef4444';
                this.style.backgroundColor = '#fee2e2';
                
                // Clear any pending auto-update
                if (window.updateTimeout) clearTimeout(window.updateTimeout);
            }else {
                // Invalid quantity (exceeds stock or below minimum)
                this.style.borderColor = '#ef4444';
                this.style.backgroundColor = '#fee2e2';
                
                // Clear any pending auto-update
                if (window.updateTimeout) clearTimeout(window.updateTimeout);
            }
            
            // Visual feedback for unsaved change (different from original)
            if (quantity !== originalQuantity) {
                // Already handled above with colors
            } else {
                this.style.borderColor = '#d1d5db';
                this.style.backgroundColor = 'white';
            }
        });
        
        input.addEventListener('change', function() {
            // Validate input
            let quantity = parseInt(this.value);
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || 99;
            
            if (isNaN(quantity) || quantity < min) {
                quantity = min;
                this.value = min;
            }
            
            // Only update if value changed and within stock limits
            if (quantity.toString() !== this.dataset.original) {
                if (quantity <= max) {
                    // Valid quantity within stock - update immediately
                    updateCartItem(this.dataset.itemId, quantity, this);
                } else {
                    // Exceeds stock - set to max and update
                    this.value = max;
                    // Then show error message
                    showNotification(`Only ${max} items available in stock.`, 'error');
                    
                    // Update with the max value
                    updateCartItem(this.dataset.itemId, max, this);
                    
                    // Highlight the input briefly
                    this.style.borderColor = '#ef4444';
                    this.style.backgroundColor = '#fee2e2';
                    setTimeout(() => {
                        this.style.borderColor = '#d1d5db';
                        this.style.backgroundColor = 'white';
                    }, 2000);
                }
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent form submission
                
                // Validate input
                let quantity = parseInt(this.value);
                const min = parseInt(this.min) || 1;
                const max = parseInt(this.max) || 99;
                
                if (isNaN(quantity) || quantity < min) {
                    quantity = min;
                    this.value = min;
                }
                
                // Only update if value changed
                if (quantity.toString() !== this.dataset.original) {
                    updateCartItem(this.dataset.itemId, quantity, this);
                }
            }
        });
    });

    // Notification function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = type === 'success' ? 'alert-success' : 'alert-error';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.animation = 'slideIn 0.3s ease';
        notification.style.minWidth = '250px';
        notification.style.padding = '1rem';
        notification.style.borderRadius = '0.5rem';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
        updateCartTotal();
    }

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .quantity-field.updating {
            cursor: wait;
        }
    `;
    document.head.appendChild(style);

    // Initial total
    updateCartTotal();
});
</script>
@endpush