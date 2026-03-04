<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is admin
        if (Auth::user()->isAdmin()) {
            // Admin sees all orders
            $orders = Order::with(['user', 'items.book'])
                ->latest()
                ->paginate(15);
        } else {
            // Regular users see only their own orders
            $orders = Order::where('user_id', Auth::id())
                ->with(['items.book'])
                ->latest()
                ->paginate(15);
        }
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Usually orders are created from cart checkout, not via form
        return redirect()->route('cart.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user is verified
        if (!$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'You must verify your email before placing an order.');
        }

        // This would typically be called from cart checkout
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'billing_address' => 'required|string|max:500',
            'phone' => 'required|string|max:500',
            'payment_method' => 'required|in:cash_on_delivery,credit_card,paypal',
        ]);
        
        // Get cart items from session (you'll need to implement cart logic)
        $cartItems = session()->get('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }
        
        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            if ($book && $book->stock_quantity >= $item['quantity']) {
                $total += $book->price * $item['quantity'];
            }
        }
        
        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $total,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'billing_address' => $request->billing_address,
            'payment_method' => $request->payment_method,
            'phone' => $request->phone,
            'payment_status' => 'pending',
        ]);
        
        // Create order items and update book stock
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            
            if ($book && $book->stock_quantity >= $item['quantity']) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'quantity' => $item['quantity'],
                    'price' => $book->price,
                    'subtotal' => $book->price * $item['quantity'],
                ]);
                
                // Update book stock
                $book->decrement('stock_quantity', $item['quantity']);
            }
        }
        
        // Clear cart
        session()->forget('cart');
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully! Your order number is: ' . $order->order_number);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Check authorization - user can only view their own orders unless admin
        if (!Auth::user()->isAdmin() && $order->user_id !== Auth::id()) {
            abort(403);
        }
        
        $order->load(['user', 'items.book']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Only admin can edit orders
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        
        $paymentStatuses = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ];
        
        return view('orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Only admin can update orders
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // If order is cancelled, restore book stock
        if ($request->status == 'cancelled' && $order->status != 'cancelled') {
            foreach ($order->items as $item) {
                $item->book->increment('stock_quantity', $item->quantity);
            }
        }
        
        $order->update($validated);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Only admin can delete orders
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Restore book stock if order is not cancelled
        if ($order->status != 'cancelled') {
            foreach ($order->items as $item) {
                $item->book->increment('stock_quantity', $item->quantity);
            }
        }
        
        $order->delete();
        
        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully!');
    }
    
    /**
     * User's order history
     */
    public function myOrders()
    {
        $orders = Auth::user()->orders()
            ->with(['items.book'])
            ->latest()
            ->paginate(10);
            
        return view('orders.my-orders', compact('orders'));
    }
    
    /**
     * Admin: Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);
        
        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);
        
        // If cancelled, restore stock
        if ($request->status == 'cancelled' && $oldStatus != 'cancelled') {
            foreach ($order->items as $item) {
                $item->book->increment('stock_quantity', $item->quantity);
            }
        }
        
        return redirect()->back()->with('success', 'Order status updated!');
    }
    
    /**
     * Admin: Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);
        
        $order->update(['payment_status' => $request->payment_status]);
        
        return redirect()->back()->with('success', 'Payment status updated!');
    }
}