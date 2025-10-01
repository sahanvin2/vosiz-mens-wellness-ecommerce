<?php

namespace App\Http\Controllers;

use App\Models\MongoDBProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display products for customers
     */
    public function index(Request $request)
    {
        $query = MongoDBProduct::query();
        
        // Filter active products if the field exists
        $query->where(function($q) {
            $q->where('is_active', true)
              ->orWhereNull('is_active'); // Include products where is_active is null (for backward compatibility)
        });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort options
        $sortBy = $request->get('sort', 'name');
        
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy($sortBy, 'asc');
        }

        $products = $query->paginate(12);
        
        // Get available categories for filter
        $allProducts = MongoDBProduct::whereNotNull('category')->get();
        $categoryNames = $allProducts->pluck('category')->unique()->filter()->sort();
            
        // Create category objects for the view
        $categories = $categoryNames->map(function($categoryName) {
            return (object) [
                'id' => $categoryName,
                'name' => ucfirst(str_replace('-', ' ', $categoryName)),
                'slug' => $categoryName
            ];
        });

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display single product
     */
    public function show($id)
    {
        // Try to find by MongoDB ObjectID first, then by slug
        $product = null;
        
        // Check if it's a valid MongoDB ObjectID (24 character hex string)
        if (preg_match('/^[a-f\d]{24}$/i', $id)) {
            try {
                $product = MongoDBProduct::where('_id', $id)
                    ->where(function($q) {
                        $q->where('is_active', true)
                          ->orWhereNull('is_active'); // Include products where is_active is null
                    })
                    ->first();
            } catch (\Exception $e) {
                // If MongoDB ObjectID fails, try slug
            }
        }
        
        // If not found by ObjectID, try by slug
        if (!$product) {
            $product = MongoDBProduct::where('slug', $id)
                ->where(function($q) {
                    $q->where('is_active', true)
                      ->orWhereNull('is_active'); // Include products where is_active is null
                })
                ->first();
        }
        
        // If still not found, throw 404
        if (!$product) {
            abort(404, 'Product not found');
        }

        // Get related products
        $relatedProducts = MongoDBProduct::where('category', $product->category)
            ->where('_id', '!=', $product->_id)
            ->where(function($q) {
                $q->where('is_active', true)
                  ->orWhereNull('is_active'); // Include products where is_active is null
            })
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Get products by category
     */
    public function category($category)
    {
        $products = MongoDBProduct::where('category', $category)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->paginate(12);

        $categoryName = ucfirst(str_replace('-', ' ', $category));

        return view('products.category', compact('products', 'category', 'categoryName'));
    }

    /**
     * Search products API endpoint
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        if (empty($search)) {
            return response()->json([]);
        }

        $products = MongoDBProduct::where('is_active', true)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['name', 'slug', 'price', 'images']);

        return response()->json($products);
    }

    /**
     * Get featured products for homepage
     */
    public function featured()
    {
        $featuredProducts = MongoDBProduct::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();

        return view('products.featured', compact('featuredProducts'));
    }
}
