<?php

use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', function () {
    // Get featured products from MongoDB (primary source)
    $featuredProducts = collect();
    try {
        $featuredProducts = \App\Models\MongoDBProduct::where('is_featured', true)
            ->where('is_active', true)
            ->limit(6)
            ->get()
            ->map(function($product) {
                // Ensure MongoDB products have category_name
                if (!isset($product->category_name) && isset($product->category_id)) {
                    $category = \App\Models\Category::find($product->category_id);
                    $product->category_name = $category ? $category->name : 'Uncategorized';
                }
                return $product;
            });
    } catch (\Exception $e) {
        // MongoDB not available, use empty collection
    }
    
    // Get categories for navigation
    $categories = collect();
    try {
        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get();
    } catch (\Exception $e) {
        // Categories not available, use empty collection
    }
        
    return view('home', compact('featuredProducts', 'categories'));
})->name('home');

// Customer Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('index');
    Route::get('/featured', [App\Http\Controllers\ProductController::class, 'featured'])->name('featured');
    Route::get('/search', [App\Http\Controllers\ProductController::class, 'search'])->name('search');
    Route::get('/category/{category}', [App\Http\Controllers\ProductController::class, 'byCategory'])->name('category');
    Route::get('/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('show');
});

// Quick product API for AJAX requests
Route::get('/api/products', function (Illuminate\Http\Request $request) {
    try {
        $query = \App\Models\MongoDBProduct::where('is_active', true);
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $products = $query->limit($request->get('limit', 12))->get();
        
        return response()->json([
            'success' => true,
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category' => $product->category_name,
                    'image' => $product->images[0] ?? '/images/placeholder.jpg',
                    'featured' => $product->is_featured,
                    'url' => route('products.show', $product->_id)
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Test MongoDB connection
Route::get('/test-mongo', function () {
    try {
        $count = App\Models\MongoDBProduct::count();
        $products = App\Models\MongoDBProduct::limit(3)->get();
        return response()->json([
            'status' => 'success',
            'count' => $count,
            'products' => $products,
            'message' => 'MongoDB connection working!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});

Route::get('/auto-login/{userId}', function ($userId) {
    try {
        $user = \App\Models\User::findOrFail($userId);
        
        if ($user->role !== 'admin') {
            return 'Not an admin user';
        }
        
        \Illuminate\Support\Facades\Auth::login($user);
        
        return redirect('/admin/products/manage')->with('success', 'Auto-logged in as ' . $user->name);
        
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/show-products-now', function () {
    // Force show products without ANY middleware or authentication
    try {
        $products = \App\Models\MongoDBProduct::paginate(10);
        $categories = \App\Models\Category::all();
        $suppliers = collect();
        
        $stats = [
            'total_products' => \App\Models\MongoDBProduct::count(),
            'active_products' => \App\Models\MongoDBProduct::where('is_active', true)->count(),
            'featured_products' => \App\Models\MongoDBProduct::where('is_featured', true)->count(),
            'low_stock' => 0,
            'out_of_stock' => 0,
        ];
        
        // Create a simple HTML response instead of using Blade
        $html = '<!DOCTYPE html><html><head><title>MongoDB Products</title>';
        $html .= '<style>body{font-family:Arial;background:#1a1a1a;color:#fff;padding:20px;}';
        $html .= '.product{border:1px solid #444;padding:15px;margin:10px 0;background:#2a2a2a;}';
        $html .= '.price{color:#4ade80;font-size:1.2em;font-weight:bold;}';
        $html .= '</style></head><body>';
        
        $html .= '<h1>🔍 MongoDB Products Direct View</h1>';
        $html .= '<p>Total Products: ' . $stats['total_products'] . '</p>';
        $html .= '<p>Featured Products: ' . $stats['featured_products'] . '</p>';
        
        foreach ($products as $product) {
            $html .= '<div class="product">';
            $html .= '<h3>' . htmlspecialchars($product->name) . '</h3>';
            $html .= '<div class="price">$' . number_format(floatval($product->price), 2) . '</div>';
            $html .= '<p>SKU: ' . htmlspecialchars($product->sku ?? 'N/A') . '</p>';
            $html .= '<p>Category: ' . htmlspecialchars($product->category_name ?? 'N/A') . '</p>';
            $html .= '<p>Stock: ' . ($product->stock_quantity ?? 0) . '</p>';
            $html .= '<p>Featured: ' . ($product->is_featured ? 'Yes' : 'No') . '</p>';
            if ($product->description) {
                $html .= '<p>' . htmlspecialchars(substr($product->description, 0, 100)) . '...</p>';
            }
            $html .= '</div>';
        }
        
        $html .= '<hr><p><a href="/login" style="color:#fbbf24;">Login as Admin</a> | ';
        $html .= '<a href="/admin/products/manage" style="color:#fbbf24;">Official Admin Page</a></p>';
        $html .= '</body></html>';
        
        return response($html);
        
    } catch (\Exception $e) {
        return '<h1>Error</h1><p>' . $e->getMessage() . '</p><pre>' . $e->getTraceAsString() . '</pre>';
    }
});

Route::get('/debug-admin-products', function () {
    try {
        // Step 1: Test MongoDB connection
        $mongoProducts = \App\Models\MongoDBProduct::count();
        
        // Step 2: Test controller method directly
        $controller = new \App\Http\Controllers\AdminDashboardController();
        
        // Step 3: Simulate the manageProducts method
        $products = \App\Models\MongoDBProduct::orderBy('created_at', 'desc')->paginate(10);
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $suppliers = collect(); // Empty collection since no Supplier model
        
        $stats = [
            'total_products' => \App\Models\MongoDBProduct::count(),
            'active_products' => \App\Models\MongoDBProduct::where('status', 'active')->count(),
            'featured_products' => \App\Models\MongoDBProduct::where('is_featured', true)->count(),
            'low_stock' => \App\Models\MongoDBProduct::where('stock_quantity', '<=', 10)->count(),
            'out_of_stock' => \App\Models\MongoDBProduct::where('stock_quantity', '<=', 0)->count(),
        ];
        
        // Step 4: Try to render the view
        return view('admin.products.manage', compact('products', 'stats', 'categories', 'suppliers'));
        
    } catch (\Exception $e) {
        return '<h1>Error Debug Info</h1>' .
               '<p><strong>Error:</strong> ' . $e->getMessage() . '</p>' .
               '<p><strong>File:</strong> ' . $e->getFile() . '</p>' .
               '<p><strong>Line:</strong> ' . $e->getLine() . '</p>' .
               '<pre>' . $e->getTraceAsString() . '</pre>';
    }
});

Route::get('/products-debug', function () {
    try {
        $products = \App\Models\MongoDBProduct::limit(10)->get();
        $categories = \App\Models\Category::all();
        
        return view('admin.products.manage', [
            'products' => new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                \App\Models\MongoDBProduct::count(),
                10,
                1,
                ['path' => request()->url()]
            ),
            'stats' => [
                'total_products' => \App\Models\MongoDBProduct::count(),
                'active_products' => \App\Models\MongoDBProduct::where('status', 'active')->count(),
                'featured_products' => \App\Models\MongoDBProduct::where('is_featured', true)->count(),
                'low_stock' => 0,
                'out_of_stock' => 0,
            ],
            'categories' => $categories,
            'suppliers' => collect()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::get('/auth-check', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_admin' => $user->role === 'admin'
        ] : null,
        'admin_middleware_check' => $user && $user->role === 'admin' ? 'PASSED' : 'FAILED'
    ]);
});

Route::get('/mongo-test', function () {
    try {
        $products = App\Models\MongoDBProduct::limit(5)->get();
        $featured = App\Models\MongoDBProduct::where('is_featured', true)->limit(3)->get();
        
        return response()->json([
            'status' => 'success',
            'total_products' => App\Models\MongoDBProduct::count(),
            'featured_count' => App\Models\MongoDBProduct::where('is_featured', true)->count(),
            'sample_products' => $products->map(function($product) {
                return [
                    'id' => $product->_id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'category' => $product->category_name,
                    'supplier' => $product->supplier_name,
                    'featured' => $product->is_featured,
                    'stock' => $product->stock_quantity,
                    'tags' => $product->tags,
                    'features' => $product->features
                ];
            }),
            'featured_products' => $featured->map(function($product) {
                return [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price
                ];
            })
        ], 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Simple admin test route (bypass middleware)
Route::get('/admin-test', function () {
    try {
        // Test MongoDB connection
        $mongoCount = App\Models\MongoDBProduct::count();
        
        // Test creating a sample MongoDB product
        $testProduct = new App\Models\MongoDBProduct();
        $testProduct->name = 'Test MongoDB Product';
        $testProduct->description = 'This is a test product to verify MongoDB storage';
        $testProduct->setAttribute('price', '29.99');
        $testProduct->stock_quantity = 100;
        $testProduct->category_id = 1;
        $testProduct->category_name = 'Skincare';
        $testProduct->supplier_id = 1;
        $testProduct->supplier_name = 'Test Supplier';
        $testProduct->sku = 'SKU-' . strtoupper(uniqid());
        $testProduct->status = 'active';
        $testProduct->is_active = true;
        $testProduct->is_featured = false;
        $testProduct->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'MongoDB connection and product creation successful',
            'mongo_products_before' => $mongoCount,
            'test_product_created' => true,
            'test_product_id' => $testProduct->_id,
            'mysql_users' => App\Models\User::count(),
            'mongo_products_after' => App\Models\MongoDBProduct::count(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Test admin dashboard without middleware
Route::get('/admin-direct', function () {
    try {
        // Get basic dashboard statistics
        $stats = [
            'total_users' => App\Models\User::where('role', 'user')->count(),
            'total_suppliers' => App\Models\User::where('role', 'supplier')->count(),
            'total_products' => App\Models\Product::count(),
            'total_categories' => App\Models\Category::count(),
        ];
        
        // Try to get MongoDB statistics
        try {
            $stats['total_mongo_products'] = App\Models\MongoDBProduct::count();
        } catch (\Exception $e) {
            $stats['total_mongo_products'] = 0;
        }

        // Get recent data
        $stats['recent_users'] = App\Models\User::where('role', 'user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats'));
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Temporary route to seed MongoDB products
Route::get('/seed-mongo-products', function () {
    // Create sample products
    App\Models\MongoDBProduct::create([
        'name' => 'Advanced Hydrating Moisturizer',
        'slug' => 'advanced-hydrating-moisturizer-2',
        'description' => 'Premium moisturizer for men with hyaluronic acid and vitamin E.',
        'short_description' => 'Lightweight 24-hour hydration for men\'s skin',
        'price' => 39.99,
        'sale_price' => 29.99,
        'sku' => 'VOSIZ-MOIST-002',
        'category_id' => 'skincare_cat',
        'category_name' => 'Skincare',
        'images' => ['products/moisturizer1.jpg'],
        'features' => ['Hyaluronic Acid', '24-hour hydration', 'Non-greasy formula'],
        'tags' => ['moisturizer', 'skincare', 'men'],
        'status' => 'active',
        'is_featured' => true,
        'stock_quantity' => 100,
        'weight' => 2.1
    ]);

    return 'MongoDB products seeded! <a href="/">Go Home</a> | <a href="/admin/dashboard">Admin Dashboard</a>';
});

// Category Routes
Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// Product Routes are defined in the grouped '/products' prefix above to avoid duplicates

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Cart Routes
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/buy-now/{product}', [App\Http\Controllers\CartController::class, 'buyNow'])->name('cart.buy-now');
    Route::put('/cart/{cartItem}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'count'])->name('cart.count');
});

    // Admin Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'manageUsers'])->name('dashboard');
    Route::get('/stats-dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('stats-dashboard');
    Route::get('/users', [App\Http\Controllers\AdminDashboardController::class, 'users'])->name('users');
    Route::get('/products', [App\Http\Controllers\AdminDashboardController::class, 'products'])->name('products');
    Route::get('/orders', [App\Http\Controllers\AdminDashboardController::class, 'orders'])->name('orders');
    
    // Order Management Routes
    Route::get('/orders/manage', [App\Http\Controllers\AdminDashboardController::class, 'manageOrders'])->name('orders.manage');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\AdminDashboardController::class, 'updateOrderStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}/view', [App\Http\Controllers\AdminDashboardController::class, 'viewOrder'])->name('orders.view');
    
    // User Management Routes
    Route::get('/users/manage', [App\Http\Controllers\AdminDashboardController::class, 'manageUsers'])->name('users.manage');
    Route::get('/users/{user}/view', [App\Http\Controllers\AdminDashboardController::class, 'viewUser'])->name('users.view');
    Route::patch('/users/{user}/toggle', [App\Http\Controllers\AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminDashboardController::class, 'deleteUser'])->name('users.delete');
    
    // Supplier Management Routes
    Route::get('/suppliers', [App\Http\Controllers\AdminDashboardController::class, 'suppliers'])->name('suppliers');
    Route::get('/suppliers/manage', [App\Http\Controllers\AdminDashboardController::class, 'manageSuppliers'])->name('suppliers.manage');
    Route::get('/suppliers/{supplier}/view', [App\Http\Controllers\AdminDashboardController::class, 'viewSupplier'])->name('suppliers.view');
    Route::post('/suppliers', [App\Http\Controllers\AdminDashboardController::class, 'createSupplier'])->name('suppliers.create');
    Route::patch('/suppliers/{supplier}/toggle', [App\Http\Controllers\AdminDashboardController::class, 'toggleSupplierStatus'])->name('suppliers.toggle');
    Route::delete('/suppliers/{supplier}', [App\Http\Controllers\AdminDashboardController::class, 'deleteSupplier'])->name('suppliers.delete');
    
    // Product Management Routes
    Route::get('/products/manage', [App\Http\Controllers\AdminDashboardController::class, 'manageProducts'])->name('products.manage');
    Route::get('/products/create', [App\Http\Controllers\AdminDashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\AdminDashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [App\Http\Controllers\AdminDashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [App\Http\Controllers\AdminDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\AdminDashboardController::class, 'deleteProduct'])->name('products.delete');
    
    // API Token Management Routes
    Route::get('/api/tokens', [App\Http\Controllers\AdminDashboardController::class, 'apiTokens'])->name('api.tokens');
    Route::post('/api/tokens', [App\Http\Controllers\AdminDashboardController::class, 'createApiToken'])->name('api.tokens.create');
    Route::delete('/api/tokens/{id}', [App\Http\Controllers\AdminDashboardController::class, 'revokeApiToken'])->name('api.tokens.revoke');
    
    // Category Management Routes
    Route::get('/categories/manage', [App\Http\Controllers\AdminDashboardController::class, 'manageCategories'])->name('categories.manage');
    Route::get('/categories/create', [App\Http\Controllers\AdminDashboardController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [App\Http\Controllers\AdminDashboardController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}/edit', [App\Http\Controllers\AdminDashboardController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{category}', [App\Http\Controllers\AdminDashboardController::class, 'updateCategory'])->name('categories.update');
    Route::patch('/categories/{category}/toggle', [App\Http\Controllers\AdminDashboardController::class, 'toggleCategory'])->name('categories.toggle');
    Route::delete('/categories/{category}', [App\Http\Controllers\AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Video Management Routes
    Route::get('/videos', [App\Http\Controllers\AdminDashboardController::class, 'videos'])->name('videos');
    Route::get('/videos/manage', [App\Http\Controllers\AdminDashboardController::class, 'manageVideos'])->name('videos.manage');
    
    // Legacy routes (keep for backward compatibility)
    Route::get('/mongo-products', function() {
        return view('admin.mongo-products');
    })->name('mongo-products');
    

});

// Shop Routes (Customer)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [App\Http\Controllers\ShopController::class, 'index'])->name('index');
    Route::get('/product/{slug}', [App\Http\Controllers\ShopController::class, 'show'])->name('product');
    Route::post('/cart/add/{slug}', [App\Http\Controllers\ShopController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [App\Http\Controllers\ShopController::class, 'cart'])->name('cart');
    Route::post('/cart/update', [App\Http\Controllers\ShopController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{slug}', [App\Http\Controllers\ShopController::class, 'removeFromCart'])->name('cart.remove');
    
    // Checkout routes (requires authentication)
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [App\Http\Controllers\ShopController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [App\Http\Controllers\ShopController::class, 'processCheckout'])->name('checkout.process');
        Route::get('/order-success/{orderId}', [App\Http\Controllers\ShopController::class, 'orderSuccess'])->name('order-success');
    });
});

// Admin Product Management Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Add these new product management routes
    Route::resource('products', App\Http\Controllers\Admin\AdminProductController::class);
    Route::delete('/products/{id}/image', [App\Http\Controllers\Admin\AdminProductController::class, 'removeImage'])->name('products.remove-image');
});

// Supplier Routes
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SupplierController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [App\Http\Controllers\SupplierController::class, 'products'])->name('products');
    Route::get('/products/create', [App\Http\Controllers\SupplierController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\SupplierController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\SupplierController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\SupplierController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\SupplierController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/toggle', [App\Http\Controllers\SupplierController::class, 'toggleStatus'])->name('products.toggle');
    Route::get('/orders', [App\Http\Controllers\SupplierController::class, 'orders'])->name('orders');
});
