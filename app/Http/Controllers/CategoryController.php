<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MongoDBProduct;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return view('categories.index', compact('categories'));
    }
    
    public function show(Category $category)
    {
        $products = MongoDBProduct::where(function($query) use ($category) {
                // Handle both string and integer category IDs
                $query->where('category_id', $category->id)
                      ->orWhere('category_id', (string) $category->id);
            })
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('categories.show', compact('category', 'products'));
    }
}
