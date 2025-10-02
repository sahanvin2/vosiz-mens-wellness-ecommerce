<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoDBProduct;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = MongoDBProduct::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'ingredients' => 'nullable|string',
            'features' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Get category details
        $category = Category::find($validated['category_id']);
        $validated['category_name'] = $category->name;
        
        // Convert string fields to arrays for MongoDB
        if (isset($validated['ingredients'])) {
            $validated['ingredients'] = array_filter(array_map('trim', explode(',', $validated['ingredients'])));
        }
        if (isset($validated['features'])) {
            $validated['features'] = array_filter(array_map('trim', explode(',', $validated['features'])));
        }
        if (isset($validated['tags'])) {
            $validated['tags'] = array_filter(array_map('trim', explode(',', $validated['tags'])));
        }
        
        // Calculate discount percentage if sale_price is set
        if ($validated['sale_price'] && $validated['sale_price'] < $validated['price']) {
            $validated['discount_percentage'] = round((($validated['price'] - $validated['sale_price']) / $validated['price']) * 100);
        } else {
            $validated['discount_percentage'] = 0;
            $validated['sale_price'] = $validated['sale_price'] ?: $validated['price'];
        }
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $filename);
                $images[] = '/images/products/' . $filename;
            }
        } elseif ($request->hasFile('image')) {
            // Handle single image upload
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $filename);
            $images[] = '/images/products/' . $filename;
        }
        $validated['images'] = $images;

        // Set defaults and metadata
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $validated['rating_average'] = 0.0;
        $validated['rating_count'] = 0;
        $validated['views_count'] = 0;
        $validated['sales_count'] = 0;
        $validated['weight'] = $validated['weight'] ?? 100;

        try {
            $product = MongoDBProduct::create($validated);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully! ID: ' . $product->_id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function show(MongoDBProduct $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(MongoDBProduct $product)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, MongoDBProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:mongodb.products,sku,' . $product->_id,
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|string',
            'brand' => 'required|string|max:100',
            'ingredients' => 'array',
            'benefits' => 'array',
            'how_to_use' => 'required|string',
            'tags' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $images = $product->images ?? [];
            foreach ($request->file('images') as $index => $image) {
                $base64 = 'data:' . $image->getMimeType() . ';base64,' . base64_encode(file_get_contents($image->getRealPath()));
                $images[] = [
                    'type' => count($images) === 0 ? 'main' : 'gallery',
                    'base64' => $base64,
                    'filename' => $image->getClientOriginalName(),
                    'alt_text' => $validated['name'] . ' - Image ' . (count($images) + 1),
                    'display_order' => count($images) + 1
                ];
            }
            $validated['images'] = $images;
        }

        $validated['updated_at'] = now();
        $validated['category_id'] = $this->getCategoryId($validated['category']);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(MongoDBProduct $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}