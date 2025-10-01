<?php

// Debug script to check product data structure
require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MongoDBProduct;

echo "🔍 Debugging MongoDB Products...\n\n";

// Check total products
$total = MongoDBProduct::count();
echo "Total products: {$total}\n";

if ($total > 0) {
    // Get first product to see structure
    $product = MongoDBProduct::first();
    echo "\n📦 First Product Structure:\n";
    echo "ID: " . $product->_id . "\n";
    echo "Name: " . $product->name . "\n";
    echo "Is Active: " . ($product->is_active ?? 'NULL') . "\n";
    echo "Featured: " . ($product->is_featured ?? 'NULL') . "\n";
    echo "Category: " . ($product->category ?? 'NULL') . "\n";
    echo "Price: " . ($product->price ?? 'NULL') . "\n";
    
    // Check all products is_active status
    echo "\n📊 All Products Status:\n";
    $products = MongoDBProduct::all();
    foreach ($products as $p) {
        $activeStatus = isset($p->is_active) ? ($p->is_active ? 'TRUE' : 'FALSE') : 'NULL';
        echo "- {$p->name}: is_active = {$activeStatus}\n";
    }
    
    // Try to query active products
    echo "\n🔍 Querying Active Products:\n";
    $activeProducts = MongoDBProduct::where('is_active', true)->get();
    echo "Products where is_active = true: " . $activeProducts->count() . "\n";
    
    $activeProducts2 = MongoDBProduct::where('is_active', 1)->get();
    echo "Products where is_active = 1: " . $activeProducts2->count() . "\n";
    
    // Check if any products exist without is_active filter
    $allProducts = MongoDBProduct::all();
    echo "All products (no filter): " . $allProducts->count() . "\n";
    
} else {
    echo "No products found!\n";
}

echo "\n✅ Debug complete!\n";