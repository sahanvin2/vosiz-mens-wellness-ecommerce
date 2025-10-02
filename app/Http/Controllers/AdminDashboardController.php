<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\MongoDBProduct;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get basic dashboard statistics
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_suppliers' => User::where('role', 'supplier')->count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
        ];
        
        // Try to get MongoDB statistics
        try {
            $stats['total_mongo_products'] = MongoDBProduct::count();
        } catch (\Exception $e) {
            $stats['total_mongo_products'] = 0;
        }

        // Get basic orders count
        try {
            $stats['total_orders'] = Order::count();
            $stats['today_orders'] = Order::whereDate('created_at', Carbon::today())->count();
            $stats['monthly_revenue'] = Order::whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->sum('total_amount');
        } catch (\Exception $e) {
            $stats['total_orders'] = 0;
            $stats['today_orders'] = 0;
            $stats['monthly_revenue'] = 0;
        }

        // Get recent users for dashboard activity
        try {
            $stats['recent_users'] = User::where('role', 'user')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(5)
                                        ->get();
        } catch (\Exception $e) {
            $stats['recent_users'] = collect();
        }

        // Get recent orders for dashboard activity
        try {
            $stats['recent_orders'] = Order::with('user')
                                           ->orderBy('created_at', 'desc')
                                           ->limit(5)
                                           ->get();
        } catch (\Exception $e) {
            $stats['recent_orders'] = collect();
        }

        return view('admin.dashboard', compact('stats'));
    }

    public function manageCategories()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->paginate(10);
        
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'categories_with_products' => Category::has('products')->count(),
        ];

        return view('admin.categories.manage', compact('categories', 'stats'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('images/categories', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.manage')->with('success', 'Category created successfully!');
    }

    public function editCategory(Category $category)
    {
        $stats = [
            'products_count' => $category->products()->count(),
            'active_products' => $category->products()->where('is_active', true)->count(),
        ];

        return view('admin.categories.edit', compact('category', 'stats'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
            }
            
            $imageName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('images/categories', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.manage')->with('success', 'Category updated successfully!');
    }

    public function toggleCategory(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.categories.manage')->with('success', "Category {$status} successfully!");
    }

    public function destroyCategory(Category $category)
    {
        // Check if category has products
        $productCount = $category->products()->count();
        
        if ($productCount > 0) {
            return redirect()->route('admin.categories.manage')
                ->with('error', "Cannot delete category '{$category->name}' as it has {$productCount} associated products.");
        }

        // Delete category image if exists
        if ($category->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.manage')
            ->with('success', "Category '{$categoryName}' has been deleted successfully.");
    }

    public function manageUsers(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'suppliers' => User::where('role', 'supplier')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.manage', compact('users', 'stats'));
    }

    public function toggleUserStatus(User $user)
    {
        // Prevent disabling the current admin user
        if ($user->id === Auth::id() && $user->role === 'admin') {
            return redirect()->route('admin.users.manage')
                ->with('error', 'You cannot disable your own admin account.');
        }

        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.manage')
            ->with('success', "User {$user->name} has been {$status} successfully.");
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.manage')
                ->with('error', 'You cannot delete your own account.');
        }

        // Check if user has any associated data
        if ($user->role === 'supplier') {
            $productCount = $user->products()->count();
            if ($productCount > 0) {
                return redirect()->route('admin.users.manage')
                    ->with('error', "Cannot delete supplier '{$user->name}' as they have {$productCount} associated products.");
            }
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.manage')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }

    public function viewUser(User $user)
    {
        $stats = [];
        
        if ($user->role === 'supplier') {
            $stats = [
                'total_products' => $user->products()->count(),
                'active_products' => $user->products()->where('is_active', true)->count(),
                'total_sales' => 0, // Implement when order system is ready
            ];
        }

        return view('admin.users.view', compact('user', 'stats'));
    }

    // Simple redirect methods for navigation
    public function users()
    {
        return redirect()->route('admin.users.manage');
    }

    public function products()
    {
        return redirect()->route('admin.products.manage');
    }

    public function orders()
    {
        return redirect()->route('admin.orders.manage');
    }

    // Order Management Methods
    public function manageOrders()
    {
        $orders = Order::with(['user', 'items'])->orderBy('created_at', 'desc')->paginate(15);
        
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.orders.manage', compact('orders', 'stats'));
    }

    public function viewOrder(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.view', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function suppliers()
    {
        return redirect()->route('admin.suppliers.manage');
    }

    // Supplier Management Methods
    public function manageSuppliers()
    {
        $suppliers = User::where('role', 'supplier')->withCount('products')->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'total_suppliers' => User::where('role', 'supplier')->count(),
            'active_suppliers' => User::where('role', 'supplier')->where('is_active', true)->count(),
            'suppliers_with_products' => User::where('role', 'supplier')->has('products')->count(),
        ];

        return view('admin.suppliers.manage', compact('suppliers', 'stats'));
    }

    public function viewSupplier(User $supplier)
    {
        if ($supplier->role !== 'supplier') {
            return redirect()->route('admin.suppliers.manage')->with('error', 'Invalid supplier.');
        }

        $stats = [
            'total_products' => $supplier->products()->count(),
            'active_products' => $supplier->products()->where('is_active', true)->count(),
            'total_sales' => 0, // Implement when order system is ready
        ];

        return view('admin.suppliers.view', compact('supplier', 'stats'));
    }

    public function createSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'supplier',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.suppliers.manage')->with('success', "Supplier '{$user->name}' created successfully!");
    }

    public function toggleSupplierStatus(User $supplier)
    {
        if ($supplier->role !== 'supplier') {
            return redirect()->route('admin.suppliers.manage')->with('error', 'Invalid supplier.');
        }

        $supplier->update(['is_active' => !$supplier->is_active]);
        
        $status = $supplier->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.suppliers.manage')->with('success', "Supplier {$status} successfully!");
    }

    public function deleteSupplier(User $supplier)
    {
        if ($supplier->role !== 'supplier') {
            return redirect()->route('admin.suppliers.manage')->with('error', 'Invalid supplier.');
        }

        // Check if supplier has products
        $productCount = $supplier->products()->count();
        if ($productCount > 0) {
            return redirect()->route('admin.suppliers.manage')
                ->with('error', "Cannot delete supplier '{$supplier->name}' as they have {$productCount} associated products.");
        }

        $supplierName = $supplier->name;
        $supplier->delete();

        return redirect()->route('admin.suppliers.manage')
            ->with('success', "Supplier '{$supplierName}' has been deleted successfully.");
    }

    // Product Management Methods
    public function manageProducts()
    {
        $mongoError = null;

        try {
            // Attempt to pull MongoDB products
            $products = MongoDBProduct::orderBy('created_at', 'desc')->paginate(10);

            $stats = [
                'total_products'      => MongoDBProduct::count(),
                'active_products'     => MongoDBProduct::where('status', 'active')->count(),
                'featured_products'   => MongoDBProduct::where('is_featured', true)->count(),
                'low_stock'           => MongoDBProduct::where('stock_quantity', '<=', 10)->count(),
                'out_of_stock'        => MongoDBProduct::where('stock_quantity', '<=', 0)->count(),
            ];
        } catch (\Throwable $e) {
            // Graceful fallback if Mongo is down / IP not whitelisted
            Log::error('Admin manageProducts Mongo failure: '.$e->getMessage());
            $mongoError = 'MongoDB unavailable: '. $e->getMessage();
            // Empty paginator so blade logic still works without changes
            $products = new LengthAwarePaginator([], 0, 10);
            $stats = [
                'total_products'    => 0,
                'active_products'   => 0,
                'featured_products' => 0,
                'low_stock'         => 0,
                'out_of_stock'      => 0,
            ];
        }

        // These rely on MySQL – still load even if Mongo failed
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $suppliers  = User::whereIn('role', ['supplier', 'admin'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.manage', compact('products', 'stats', 'categories', 'suppliers', 'mongoError'));
    }

    public function createProduct()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        // Include both suppliers and admins as potential suppliers
        $suppliers = User::whereIn('role', ['supplier', 'admin'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:users,id',
            'sku' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('images/products', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Get category and supplier names for MongoDB storage
        $category = Category::find($validated['category_id']);
        $supplier = User::find($validated['supplier_id']);

        // Prepare data for MongoDB
        $mongoData = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'price' => (float) $validated['price'],
            'stock_quantity' => (int) $validated['stock_quantity'],
            'category_id' => $validated['category_id'],
            'category_name' => $category->name,
            'supplier_id' => $validated['supplier_id'],
            'supplier_name' => $supplier->name,
            'image' => $validated['image'] ?? null,
            'images' => isset($validated['image']) ? [$validated['image']] : [],
            'status' => $validated['is_active'] ? 'active' : 'inactive',
            'is_active' => $validated['is_active'],
            'is_featured' => false,
            'tags' => [],
            'features' => [],
            'weight' => 0,
            'sku' => $validated['sku'] ?? 'VOSIZ-' . strtoupper(uniqid()),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Store in MongoDB
        MongoDBProduct::create($mongoData);

        return redirect()->route('admin.products.manage')->with('success', 'Product created successfully and stored in MongoDB!');
    }

    public function editProduct($id)
    {
        // Find MongoDB product by ID
        $product = MongoDBProduct::where('_id', $id)->first();
        
        if (!$product) {
            return redirect()->route('admin.products.manage')->with('error', 'Product not found.');
        }
        
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        // Include both suppliers and admins as potential suppliers
        $suppliers = User::whereIn('role', ['supplier', 'admin'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function updateProduct(Request $request, $id)
    {
        try {
            // Find MongoDB product by ID
            $product = MongoDBProduct::where('_id', $id)->first();
            
            if (!$product) {
                return redirect()->route('admin.products.manage')->with('error', 'Product not found.');
            }
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'supplier_id' => 'required|exists:users,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tags' => 'nullable|string',
                'weight' => 'nullable|numeric|min:0',
                'compare_price' => 'nullable|numeric|min:0',
            ]);

            // Get category and supplier names for MongoDB storage
            $category = Category::find($validated['category_id']);
            $supplier = User::find($validated['supplier_id']);

            if (!$category || !$supplier) {
                return redirect()->back()->with('error', 'Invalid category or supplier selected.');
            }

            // Handle image upload
            $images = $product->images ?? [];
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs('images/products', $imageName, 'public');
                
                // Add new image to the beginning of the array
                array_unshift($images, $imagePath);
                $images = array_slice($images, 0, 5); // Keep only 5 images max
            }

            // Process tags
            $tags = [];
            if (!empty($validated['tags'])) {
                $tags = array_map('trim', explode(',', $validated['tags']));
            }

            // Prepare update data for MongoDB
            $updateData = [
                'name' => $validated['name'],
                'slug' => \Illuminate\Support\Str::slug($validated['name']),
                'description' => $validated['description'],
                'price' => (float) $validated['price'],
                'compare_price' => $validated['compare_price'] ? (float) $validated['compare_price'] : null,
                'stock_quantity' => (int) $validated['stock_quantity'],
                'weight' => $validated['weight'] ? (float) $validated['weight'] : 0,
                'category_id' => $validated['category_id'],
                'category_name' => $category->name,
                'supplier_id' => $validated['supplier_id'],
                'supplier_name' => $supplier->name,
                'images' => $images,
                'tags' => $tags,
                'status' => $request->has('is_active') ? 'active' : 'inactive',
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'updated_at' => now(),
            ];

            // Update in MongoDB
            $product->update($updateData);

            return redirect()->route('admin.products.manage')->with('success', 'Product "' . $validated['name'] . '" updated successfully!');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteProduct($id)
    {
        // Find MongoDB product by ID
        $product = MongoDBProduct::where('_id', $id)->first();
        
        if (!$product) {
            return redirect()->route('admin.products.manage')->with('error', 'Product not found.');
        }

        // Delete product images if they exist
        if (!empty($product->images)) {
            foreach ($product->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
            }
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('admin.products.manage')
            ->with('success', "Product '{$productName}' has been deleted successfully from MongoDB.");
    }

    // Video Management Methods
    public function videos()
    {
        return view('admin.videos');
    }

    public function manageVideos()
    {
        return view('admin.videos.manage');
    }

    // API Token Management Methods
    public function apiTokens()
    {
        $tokens = request()->user()->tokens;
        return view('admin.api-tokens', compact('tokens'));
    }

    public function createApiToken(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'sometimes|array'
        ]);

        $abilities = $validated['abilities'] ?? ['*'];
        
        // If "all permissions" is selected, use wildcard
        if (in_array('*', $abilities)) {
            $abilities = ['*'];
        }

        $token = $request->user()->createToken(
            $validated['name'],
            $abilities
        );

        return redirect()->route('admin.api.tokens')->with([
            'token' => $token->plainTextToken,
            'success' => 'API token created successfully!'
        ]);
    }

    public function revokeApiToken($id)
    {
        request()->user()->tokens()->where('id', $id)->delete();
        
        return redirect()->route('admin.api.tokens')->with(
            'success', 
            'API token revoked successfully!'
        );
    }
}
