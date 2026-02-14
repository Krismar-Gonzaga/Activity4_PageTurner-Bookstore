<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $total = 0;
        
        // Calculate total
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            if ($book) {
                $total += $book->price * $item['quantity'];
            }
        }
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request, Book $book)  // Changed signature to use Book $book
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Check stock availability
        if ($book->stock_quantity < $request->quantity) {
            return redirect()->back()
                ->with('error', 'Insufficient stock. Only ' . $book->stock_quantity . ' items available.');
        }
        
        $cart = session()->get('cart', []);
        $bookId = $book->id;
        
        // Check if book already in cart
        if (isset($cart[$bookId])) {
            $cart[$bookId]['quantity'] += $request->quantity;
        } else {
            $cart[$bookId] = [
                'book_id' => $book->id,
                'title' => $book->title,
                'price' => $book->price,
                'quantity' => $request->quantity,
                'image' => $book->cover_image,
            ];
        }
        
        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $book->title . ' added to cart!',
                'count' => $this->getCartCount()
            ]);
        }
        
        return redirect()->route('cart.index')
            ->with('success', $book->title . ' added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $bookId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $book = Book::findOrFail($bookId);
        
        if ($book->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $book->stock_quantity . ' items available.'
            ]);
        }
        
        $cart = session()->get('cart', []);
        
        if (isset($cart[$bookId])) {
            $cart[$bookId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart.'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($bookId)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$bookId])) {
            unset($cart[$bookId]);
            session()->put('cart', $cart);
            
            return redirect()->route('cart.index')
                ->with('success', 'Item removed from cart.');
        }
        
        return redirect()->route('cart.index')
            ->with('error', 'Item not found in cart.');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->route('cart.index')
            ->with('success', 'Cart cleared successfully.');
    }

    /**
     * Checkout process
     */
    public function checkout()
    {
        $cartItems = session()->get('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }
        
        // Check stock availability for all items
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            if (!$book || $book->stock_quantity < $item['quantity']) {
                return redirect()->route('cart.index')
                    ->with('error', $book->title . ' has insufficient stock.');
            }
        }
        
        $total = 0;
        foreach ($cartItems as $item) {
            $book = Book::find($item['book_id']);
            $total += $book->price * $item['quantity'];
        }
        
        return view('cart.checkout', compact('cartItems', 'total'));
    }

    public function getCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        
        foreach ($cart as $item) {
            $count += $item['quantity'] ?? 1;
        }
        
        return response()->json(['count' => $count]);
    }

    private function getCartCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'] ?? 1;
        }
        return $count;
    }
}