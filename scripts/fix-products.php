<?php

// Fix script to update product data structure
require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MongoDBProduct;

echo "🔧 Fixing MongoDB Products...\n\n";

// Get all products
$products = MongoDBProduct::all();
echo "Found {$products->count()} products to fix\n\n";

foreach ($products as $product) {
    echo "Fixing: {$product->name}\n";
    
    // Update the product with correct fields
    $updateData = [
        'is_active' => true,
        'category' => $product->category ?? 'skincare',
        'updated_at' => now()
    ];
    
    // Make sure is_featured is set correctly
    if (!isset($product->is_featured)) {
        $updateData['is_featured'] = in_array($product->name, [
            'Premium Beard Oil',
            'Activated Charcoal Face Wash', 
            'Anti-Aging Eye Cream'
        ]);
    }
    
    $product->update($updateData);
    echo "✅ Fixed: {$product->name} - is_active: true, category: {$product->category}\n";
}

echo "\n🎉 All products fixed!\n";

// Verify the fixes
echo "\n📊 Verification:\n";
$activeProducts = MongoDBProduct::where('is_active', true)->count();
$featuredProducts = MongoDBProduct::where('is_featured', true)->count();
echo "Active products: {$activeProducts}\n";
echo "Featured products: {$featuredProducts}\n";

// Show sample product
$sampleProduct = MongoDBProduct::where('is_active', true)->first();
if ($sampleProduct) {
    echo "\n📦 Sample Active Product:\n";
    echo "Name: {$sampleProduct->name}\n";
    echo "Category: {$sampleProduct->category}\n";
    echo "Price: $" . $sampleProduct->price . "\n";
    echo "Active: " . ($sampleProduct->is_active ? 'YES' : 'NO') . "\n";
    echo "Featured: " . ($sampleProduct->is_featured ? 'YES' : 'NO') . "\n";
}

echo "\n✅ Fix complete!\n";