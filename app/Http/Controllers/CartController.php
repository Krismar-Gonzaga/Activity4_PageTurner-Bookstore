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
        // Get cart from session
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;
        
        // Loop through cart items to build cartItems array with book details
        foreach ($cart as $id => $item) {
            // Find the book in database
            $book = Book::find($id);
            
            if ($book) {
                // Book exists - use real data
                $cartItems[$id] = [
                    'title' => $book->title,
                    'price' => $book->price,
                    'quantity' => $item['quantity'],
                    'image' => $book->cover_image ?? 'covers/default.jpg',
                    'stock' => $book->stock_quantity ?? 0,
                ];
                
                // Add to total
                $total += $book->price * $item['quantity'];
            } else {
                // Book no longer exists - use session data as fallback
                $cartItems[$id] = [
                    'title' => $item['title'] ?? 'Unknown Book',
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                    'image' => 'covers/default.jpg',
                    'stock' => 0,
                ];
                
                // Add to total using session price
                $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            // Get the book from database to check stock
            $book = Book::find($id); // Adjust based on your model
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Book not found.'
                ], 404);
            }
            
            // Check if requested quantity exceeds stock
            if ($request->quantity > $book->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$book->stock_quantity} items available in stock.",
                    'max_stock' => $book->stock_quantity,
                    'current_quantity' => $cart[$id]['quantity']
                ], 422);
            }
            
            // Update quantity
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            
            // Recalculate total
            $total = $this->calculateTotal($cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'total' => $total,
                'item_total' => $cart[$id]['price'] * $request->quantity,
                'new_quantity' => $request->quantity
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart.'
        ], 404);
    }

    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
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
        // Get cart from session
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $id => $item) {
            $book = Book::find($id);
            
            if ($book) {
                $cartItems[$id] = [
                    'title' => $book->title,
                    'price' => $book->price,
                    'quantity' => $item['quantity'],
                    'image' => $book->image ?? 'covers/default.jpg',
                ];
                
                $total += $book->price * $item['quantity'];
            } else {
                // If book not found, use session data
                $cartItems[$id] = [
                    'title' => $item['title'] ?? 'Unknown Book',
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                    'image' => 'covers/default.jpg',
                ];
                
                $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            }
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