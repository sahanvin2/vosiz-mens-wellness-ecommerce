<?php

// Alternative fix script using direct MongoDB operations
require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MongoDBProduct;

echo "🔧 Alternative Fix for MongoDB Products...\n\n";

// Get all products and fix them one by one
$products = MongoDBProduct::all();
echo "Found {$products->count()} products to fix\n\n";

foreach ($products as $product) {
    echo "Fixing: {$product->name}\n";
    
    // Set properties directly
    $product->is_active = true;
    $product->category = $product->category ?? 'skincare';
    $product->updated_at = now();
    
    // Set featured status
    if (!isset($product->is_featured) || $product->is_featured === null) {
        $product->is_featured = in_array($product->name, [
            'Premium Beard Oil',
            'Activated Charcoal Face Wash', 
            'Anti-Aging Eye Cream'
        ]);
    }
    
    // Save the product
    $saved = $product->save();
    echo "✅ " . ($saved ? "Saved" : "Failed to save") . ": {$product->name}\n";
}

echo "\n🎉 Fix attempt complete!\n";

// Verify the fixes
echo "\n📊 Verification:\n";
try {
    $total = MongoDBProduct::count();
    $activeProducts = MongoDBProduct::where('is_active', true)->count();
    $featuredProducts = MongoDBProduct::where('is_featured', true)->count();
    
    echo "Total products: {$total}\n";
    echo "Active products: {$activeProducts}\n";
    echo "Featured products: {$featuredProducts}\n";
    
    // Show all products with their status
    echo "\n📋 All Products Status:\n";
    $allProducts = MongoDBProduct::all();
    foreach ($allProducts as $p) {
        $active = $p->is_active ? 'YES' : 'NO';
        $featured = $p->is_featured ? 'YES' : 'NO';
        echo "- {$p->name}: Active={$active}, Featured={$featured}, Category={$p->category}\n";
    }
    
} catch (Exception $e) {
    echo "Error during verification: " . $e->getMessage() . "\n";
}

echo "\n✅ Complete!\n";