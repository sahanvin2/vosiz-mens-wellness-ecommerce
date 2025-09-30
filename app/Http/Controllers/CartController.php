<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the user's cart
     */
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add a product to cart
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'integer|min:1|max:' . $product->stock_quantity,
        ]);

        $quantity = $request->input('quantity', 1);

        // Check if product is already in cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $quantity;
            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', 'Not enough stock available.');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Buy now - Add to cart and redirect to checkout
     */
    public function buyNow(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'integer|min:1|max:' . $product->stock_quantity,
        ]);

        $quantity = $request->input('quantity', 1);

        // Check if product is already in cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $quantity;
            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', 'Not enough stock available.');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        // Redirect to cart/checkout page
        return redirect()->route('cart.index')->with('success', 'Product added to cart! Proceed to checkout.');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, Cart $cartItem)
    {
        // Make sure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock_quantity,
        ]);

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove item from cart
     */
    public function remove(Cart $cartItem)
    {
        // Make sure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Cart cleared successfully!');
    }

    /**
     * Get cart count (for header display)
     */
    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json(['count' => $count]);
    }
}
