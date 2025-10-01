<?php

namespace App\Http\Controllers;

use App\Models\MongoDBProduct;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = MongoDBProduct::where('is_active', true);

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('tags', 'like', '%' . $request->search . '%');
            });
        }

        // Price filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(12);

        $categories = [
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare',
            'hair-care' => 'Hair Care',
            'body-care' => 'Body Care'
        ];

        return view('shop.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = MongoDBProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get related products from same category
        $relatedProducts = MongoDBProduct::where('category', $product->category)
            ->where('slug', '!=', $slug)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('shop.product', compact('product', 'relatedProducts'));
    }

    public function addToCart(Request $request, $slug)
    {
        $product = MongoDBProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock_quantity
        ]);

        // Get cart from session
        $cart = session()->get('cart', []);

        // Add or update item in cart
        if (isset($cart[$slug])) {
            $cart[$slug]['quantity'] += $validated['quantity'];
        } else {
            $cart[$slug] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'image' => $product->images[0]['base64'] ?? null,
                'slug' => $slug
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart!',
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ]);
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('shop.cart', compact('cart', 'total'));
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);
        
        foreach ($request->quantities as $slug => $quantity) {
            if (isset($cart[$slug])) {
                if ($quantity > 0) {
                    $cart[$slug]['quantity'] = $quantity;
                } else {
                    unset($cart[$slug]);
                }
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('shop.cart')->with('success', 'Cart updated!');
    }

    public function removeFromCart($slug)
    {
        $cart = session()->get('cart', []);
        unset($cart[$slug]);
        session()->put('cart', $cart);

        return redirect()->route('shop.cart')->with('success', 'Item removed from cart!');
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty!');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('shop.checkout', compact('cart', 'total'));
    }

    public function processCheckout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'shipping_country' => 'required|string',
            'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer'
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty!');
        }

        DB::beginTransaction();
        
        try {
            // Calculate total
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'VO-' . strtoupper(uniqid()),
                'status' => 'pending',
                'total_amount' => $total,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'shipping_country' => $validated['shipping_country'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending'
            ]);

            // Create order items
            foreach ($cart as $slug => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_slug' => $slug,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity']
                ]);

                // Update product stock
                $product = MongoDBProduct::where('slug', $slug)->first();
                if ($product) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            DB::commit();

            // Clear cart
            session()->forget('cart');

            return redirect()->route('shop.order-success', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function orderSuccess($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->with('orderItems')
            ->firstOrFail();

        return view('shop.order-success', compact('order'));
    }
}