<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MongoDBProduct;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function dashboard()
    {
        $supplierId = Auth::id();
        
        // Get supplier's product statistics using both databases
        $stats = [
            // MySQL Products
            'mysql_products' => Product::where('supplier_id', $supplierId)->count(),
            'mysql_active' => Product::where('supplier_id', $supplierId)->where('is_active', true)->count(),
            
            // MongoDB Products (if they have supplier_id field)
            'mongo_products' => 0,
            'mongo_active' => 0,
            
            // Recent products from MySQL
            'recent_products' => Product::where('supplier_id', $supplierId)->latest()->take(5)->get(),
            'low_stock' => Product::where('supplier_id', $supplierId)->where('stock_quantity', '<', 10)->count(),
        ];
        
        // Try to get MongoDB stats if available
        try {
            $stats['mongo_products'] = MongoDBProduct::count();
            $stats['mongo_active'] = MongoDBProduct::where('status', 'active')->count();
        } catch (\Exception $e) {
            // MongoDB not available or no supplier_id field
        }

        return view('supplier.dashboard', compact('stats'));
    }

    public function products()
    {
        // Get products from MySQL for this supplier
        $products = Product::where('supplier_id', Auth::id())
                          ->with('category')
                          ->paginate(20);
        return view('supplier.products', compact('products'));
    }
    
    public function orders()
    {
        // Get orders for supplier's products
        $products = Product::where('supplier_id', Auth::id())->pluck('id');
        $orders = \App\Models\OrderItem::whereIn('product_id', $products)
                                      ->with(['order.user', 'product'])
                                      ->latest()
                                      ->paginate(20);
        return view('supplier.orders', compact('orders'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('supplier.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'nullable|string',
            'benefits' => 'nullable|string',
            'skin_type' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $productData = $request->all();
        $productData['supplier_id'] = Auth::id();
        $productData['slug'] = Str::slug($request->name);

        // Handle images upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images/products', $imageName, 'public');
                $images[] = $imagePath;
            }
            $productData['images'] = $images;
        }

        // Convert ingredients and benefits to arrays
        if ($request->ingredients) {
            $productData['ingredients'] = array_map('trim', explode(',', $request->ingredients));
        }
        if ($request->benefits) {
            $productData['benefits'] = array_map('trim', explode(',', $request->benefits));
        }

        // Generate SKU if not provided
        if (!$request->sku) {
            $productData['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }

        Product::create($productData);

        return redirect()->route('supplier.products')
                        ->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        // Ensure the supplier can only edit their own products
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('supplier.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Ensure the supplier can only update their own products
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0|gt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products')->ignore($product->id)],
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'nullable|string',
            'benefits' => 'nullable|string',
            'skin_type' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $productData = $request->all();
        $productData['slug'] = Str::slug($request->name);

        // Handle new images upload
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            // Upload new images
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('images/products', $imageName, 'public');
                $images[] = $imagePath;
            }
            $productData['images'] = $images;
        }

        // Convert ingredients and benefits to arrays
        if ($request->ingredients) {
            $productData['ingredients'] = array_map('trim', explode(',', $request->ingredients));
        } else {
            $productData['ingredients'] = null;
        }

        if ($request->benefits) {
            $productData['benefits'] = array_map('trim', explode(',', $request->benefits));
        } else {
            $productData['benefits'] = null;
        }

        // Generate SKU if not provided
        if (!$request->sku) {
            $productData['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }

        $product->update($productData);

        return redirect()->route('supplier.products')
                        ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Ensure the supplier can only delete their own products
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('supplier.products')
                        ->with('success', 'Product deleted successfully!');
    }

    /**
     * Toggle product status (active/inactive).
     */
    public function toggleStatus(Product $product)
    {
        // Ensure the supplier can only toggle their own products
        if ($product->supplier_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('supplier.products')
                        ->with('success', "Product {$status} successfully!");
    }
}
