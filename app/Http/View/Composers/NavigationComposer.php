<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Category;

class NavigationComposer
{
    public function compose(View $view)
    {
        // Get categories safely for navigation
        $categories = collect();
        try {
            $categories = Category::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            // Categories not available, use empty collection
        }
        
        $view->with('categories', $categories);
    }
}