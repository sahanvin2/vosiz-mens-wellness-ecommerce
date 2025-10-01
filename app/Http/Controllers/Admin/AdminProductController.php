<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoDBProduct;
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
        $products = MongoDBProduct::orderBy('created_at', 'desc')->get();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        // Get available categories
        $categories = collect([
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare', 
            'hair-care' => 'Hair Care',
            'body-care' => 'Body Care',
            'supplements' => 'Supplements',
            'accessories' => 'Accessories'
        ]);
        
        return view('admin.products.create', compact('categories'));
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
            'sku' => 'required|string|unique:mongodb_products,sku',
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|string',
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
        $product = MongoDBProduct::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => (float) $request->price,
            'compare_price' => $request->compare_price ? (float) $request->compare_price : null,
            'sku' => $request->sku,
            'stock_quantity' => (int) $request->stock_quantity,
            'category' => $request->category,
            'brand' => $request->brand,
            'images' => $imagePaths,
            'ingredients' => $request->ingredients ? explode(',', $request->ingredients) : [],
            'benefits' => $request->benefits ? explode(',', $request->benefits) : [],
            'how_to_use' => $request->how_to_use,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $product = MongoDBProduct::findOrFail($id);
        
        // Get available categories
        $categories = collect([
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare', 
            'hair-care' => 'Hair Care',
            'body-care' => 'Body Care',
            'supplements' => 'Supplements',
            'accessories' => 'Accessories'
        ]);
        
        return view('admin.products.edit', compact('product', 'categories'));
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
            'sku' => 'required|string|unique:mongodb_products,sku,' . $id,
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|string',
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
            'category' => $request->category,
            'brand' => $request->brand,
            'images' => $imagePaths,
            'ingredients' => $request->ingredients ? explode(',', $request->ingredients) : [],
            'benefits' => $request->benefits ? explode(',', $request->benefits) : [],
            'how_to_use' => $request->how_to_use,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.products.index')
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