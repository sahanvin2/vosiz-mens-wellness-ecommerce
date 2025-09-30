<?php

namespace App\Http\Controllers;

use App\Models\MongoDBProduct;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of all products for customers
     */
    public function index(Request $request)
    {
        $query = MongoDBProduct::where('is_active', true);
        
        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('tags', 'like', '%' . $search . '%');
            });
        }
        
        // Sort options
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $products = $query->paginate(12);
        
        // Get categories for filter
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        // Get featured products for homepage
        $featuredProducts = MongoDBProduct::where('is_featured', true)
            ->where('is_active', true)
            ->limit(6)
            ->get();
            
        return view('products.index', compact('products', 'categories', 'featuredProducts'));
    }

    /**
     * Display the specified product details
     */
    public function show($id)
    {
        try {
            $product = MongoDBProduct::findOrFail($id);
            
            if (!$product->is_active) {
                abort(404, 'Product not found');
            }
            
            // Get related products from same category
            $relatedProducts = MongoDBProduct::where('category_name', $product->category_name)
                ->where('_id', '!=', $product->_id)
                ->where('is_active', true)
                ->limit(4)
                ->get();
                
            return view('products.show', compact('product', 'relatedProducts'));
            
        } catch (\Exception $e) {
            abort(404, 'Product not found');
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        $products = MongoDBProduct::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('products.category', compact('products', 'categories', 'category'));
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $categoryId = $request->get('category');
        
        $query = MongoDBProduct::where('is_active', true);
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('category_name', 'like', '%' . $search . '%');
            });
        }
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return view('products.search', compact('products', 'categories', 'search'));
    }

    /**
     * Get featured products for homepage
     */
    public function featured()
    {
        $products = MongoDBProduct::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();
            
        return view('products.featured', compact('products'));
    }
}
