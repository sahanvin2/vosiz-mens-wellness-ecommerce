<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoDBProduct;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                abort(403, 'Access denied. Admin only.');
            }
            return $next($request);
        });
        
        $this->imageService = $imageService;
    }

    /**
     * Display listing of products for admin
     */
    public function index()
    {
        // Redirect to centralized admin manage page which paginates and shows stats
        return redirect()->route('admin.products.manage');
    }

    /**
     * Show create product form
     */
    public function create()
    {
        // Get available categories as objects with id and name for the view
        $categories = collect([
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare', 
            'hair-care' => 'Hair Care',
            'body-care' => 'Body Care',
            'supplements' => 'Supplements',
            'accessories' => 'Accessories'
        ])->map(function ($label, $slug) {
            return (object) ['id' => $slug, 'name' => $label];
        })->values();
        // Also provide suppliers (admins and suppliers) for the select box
        $suppliers = User::whereIn('role', ['supplier', 'admin'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            // SKU uniqueness is handled against MongoDB below
            'sku' => 'required|string',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required',
            'supplier_id' => 'nullable',
            'brand' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'nullable|string',
            'benefits' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        
        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $imagePaths = $this->imageService->uploadProductImages($request->file('images'), $slug);
        }

        // Create product
        // Ensure SKU is unique in MongoDB
        if (MongoDBProduct::where('sku', $request->sku)->exists()) {
            return back()->withInput()->withErrors(['sku' => 'The SKU has already been taken.']);
        }
        // Resolve category and supplier names
        $categoryName = null;
        $supplierName = null;
        if ($request->filled('category_id')) {
            $cat = Category::find($request->category_id);
            $categoryName = $cat ? $cat->name : null;
        }
        if ($request->filled('supplier_id')) {
            $sup = User::find($request->supplier_id);
            $supplierName = $sup ? $sup->name : null;
        }

        // Determine categoryId for MySQL mirror
        $categoryId = null;
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;
        } elseif ($categoryName) {
            $categoryId = Category::where('name', $categoryName)->value('id');
        }

        $product = MongoDBProduct::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => (float) $request->price,
            'compare_price' => $request->compare_price ? (float) $request->compare_price : null,
            'sku' => $request->sku,
            'stock_quantity' => (int) $request->stock_quantity,
            'category_id' => $request->category_id ?? null,
            'category_name' => $categoryName,
            'brand' => $request->brand,
            'images' => $imagePaths,
            'ingredients' => $request->ingredients ? explode(',', $request->ingredients) : [],
            'benefits' => $request->benefits ? explode(',', $request->benefits) : [],
            'how_to_use' => $request->how_to_use,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'supplier_id' => $request->supplier_id ?? null,
            'supplier_name' => $supplierName,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mirror to MySQL products table for visibility in SQL-backed admin pages
        try {
            $match = [];
            if (!empty($product->sku)) {
                $match['sku'] = $product->sku;
            } else {
                $match['slug'] = $product->slug;
            }

            Product::updateOrCreate($match, [
                'category_id' => $product->category_id ?? $categoryId,
                'supplier_id' => $product->supplier_id ?? null,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description ?? null,
                'short_description' => $product->short_description ?? null,
                'price' => isset($product->price) ? (float)$product->price : 0,
                'compare_price' => isset($product->compare_price) ? (float)$product->compare_price : null,
                'stock_quantity' => isset($product->stock_quantity) ? (int)$product->stock_quantity : 0,
                'sku' => $product->sku ?? null,
                'images' => $product->images ?? [],
                'ingredients' => $product->ingredients ?? [],
                'benefits' => $product->benefits ?? [],
                'is_featured' => isset($product->is_featured) ? (bool)$product->is_featured : false,
                'is_active' => isset($product->is_active) ? (bool)$product->is_active : ($product->status === 'active' ? true : false),
                'weight' => isset($product->weight) ? (float)$product->weight : 0,
            ]);
        } catch (\Exception $e) {
            // Log but don't block admin flow
            \Illuminate\Support\Facades\Log::error('Failed to mirror product to MySQL: ' . $e->getMessage());
        }

        return redirect()->route('admin.products.manage')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $product = MongoDBProduct::findOrFail($id);
        
        // Get available categories as objects with id and name for the view
        $categories = collect([
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare', 
            'hair-care' => 'Hair Care',
            'body-care' => 'Body Care',
            'supplements' => 'Supplements',
            'accessories' => 'Accessories'
        ])->map(function ($label, $slug) {
            return (object) ['id' => $slug, 'name' => $label];
        })->values();
        // Provide suppliers for the edit view as well
        $suppliers = User::whereIn('role', ['supplier', 'admin'])->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = MongoDBProduct::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            // SKU uniqueness handled against MongoDB below
            'sku' => 'required|string',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required',
            'supplier_id' => 'nullable',
            'brand' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'nullable|string',
            'benefits' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        
        // Handle new image uploads
        $imagePaths = $product->images ?? [];
        if ($request->hasFile('images')) {
            $newImages = $this->imageService->uploadProductImages($request->file('images'), $slug);
            $imagePaths = array_merge($imagePaths, $newImages);
        }

        // Ensure SKU is unique in MongoDB (exclude current product)
        $existing = MongoDBProduct::where('sku', $request->sku)->first();
        if ($existing && (string) $existing->_id !== (string) $product->_id) {
            return back()->withInput()->withErrors(['sku' => 'The SKU has already been taken.']);
        }

        // Resolve category and supplier names for update
        $categoryName = null;
        $supplierName = null;
        if ($request->filled('category_id')) {
            $cat = Category::find($request->category_id);
            $categoryName = $cat ? $cat->name : null;
        }
        if ($request->filled('supplier_id')) {
            $sup = User::find($request->supplier_id);
            $supplierName = $sup ? $sup->name : null;
        }

        // Determine categoryId for MySQL mirror
        $categoryId = null;
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;
        } elseif ($categoryName) {
            $categoryId = Category::where('name', $categoryName)->value('id');
        }

        // Resolve category and supplier names for update
        $categoryName = null;
        $supplierName = null;
        if ($request->filled('category_id')) {
            $cat = Category::find($request->category_id);
            $categoryName = $cat ? $cat->name : null;
        }
        if ($request->filled('supplier_id')) {
            $sup = User::find($request->supplier_id);
            $supplierName = $sup ? $sup->name : null;
        }

        // Update product
        $product->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => (float) $request->price,
            'compare_price' => $request->compare_price ? (float) $request->compare_price : null,
            'sku' => $request->sku,
            'stock_quantity' => (int) $request->stock_quantity,
            'category_id' => $request->category_id ?? null,
            'category_name' => $categoryName,
            'brand' => $request->brand,
            'images' => $imagePaths,
            'ingredients' => $request->ingredients ? explode(',', $request->ingredients) : [],
            'benefits' => $request->benefits ? explode(',', $request->benefits) : [],
            'how_to_use' => $request->how_to_use,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'supplier_id' => $request->supplier_id ?? null,
            'supplier_name' => $supplierName,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'updated_at' => now(),
        ]);

        // Mirror update to MySQL
        try {
            $match = [];
            if (!empty($product->sku)) {
                $match['sku'] = $product->sku;
            } else {
                $match['slug'] = $product->slug;
            }

            Product::updateOrCreate($match, [
                'category_id' => $product->category_id ?? $categoryId,
                'supplier_id' => $product->supplier_id ?? null,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description ?? null,
                'short_description' => $product->short_description ?? null,
                'price' => isset($product->price) ? (float)$product->price : 0,
                'compare_price' => isset($product->compare_price) ? (float)$product->compare_price : null,
                'stock_quantity' => isset($product->stock_quantity) ? (int)$product->stock_quantity : 0,
                'sku' => $product->sku ?? null,
                'images' => $product->images ?? [],
                'ingredients' => $product->ingredients ?? [],
                'benefits' => $product->benefits ?? [],
                'is_featured' => isset($product->is_featured) ? (bool)$product->is_featured : false,
                'is_active' => isset($product->is_active) ? (bool)$product->is_active : ($product->status === 'active' ? true : false),
                'weight' => isset($product->weight) ? (float)$product->weight : 0,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to mirror updated product to MySQL: ' . $e->getMessage());
        }

        return redirect()->route('admin.products.manage')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $product = MongoDBProduct::findOrFail($id);
        
        // Delete associated images
        if (!empty($product->images)) {
            $this->imageService->deleteProductImages($product->images);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Remove specific image from product
     */
    public function removeImage(Request $request, $id)
    {
        $product = MongoDBProduct::findOrFail($id);
        $imageIndex = $request->input('image_index');
        
        if (isset($product->images[$imageIndex])) {
            // Delete the physical file
            $this->imageService->deleteProductImages([$product->images[$imageIndex]]);
            
            // Remove from array
            $images = $product->images;
            unset($images[$imageIndex]);
            $images = array_values($images); // Re-index array
            
            $product->update(['images' => $images]);
        }

        return response()->json(['success' => true]);
    }
}