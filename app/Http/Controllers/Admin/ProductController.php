<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoDBProduct;
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
        $categories = [
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare', 
            'hair-care' => 'Hair Care',
            'body-care' => 'Body Care'
        ];
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:mongodb.products,sku',
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

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $base64 = 'data:' . $image->getMimeType() . ';base64,' . base64_encode(file_get_contents($image->getRealPath()));
                $images[] = [
                    'type' => $index === 0 ? 'main' : 'gallery',
                    'base64' => $base64,
                    'filename' => $image->getClientOriginalName(),
                    'alt_text' => $validated['name'] . ' - Image ' . ($index + 1),
                    'display_order' => $index + 1
                ];
            }
        }
        $validated['images'] = $images;

        // Set defaults
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        $validated['category_id'] = $this->getCategoryId($validated['category']);

        MongoDBProduct::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(MongoDBProduct $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(MongoDBProduct $product)
    {
        $categories = [
            'beard-care' => 'Beard Care',
            'skincare' => 'Skincare',
            'hair-care' => 'Hair Care', 
            'body-care' => 'Body Care'
        ];
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

    private function getCategoryId($category)
    {
        $categoryMap = [
            'beard-care' => 1,
            'skincare' => 2,
            'hair-care' => 3,
            'body-care' => 4
        ];
        return $categoryMap[$category] ?? 1;
    }
}