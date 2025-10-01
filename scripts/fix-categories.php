<?php

// Fix the category assignments
require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MongoDBProduct;

echo "🔧 Fixing Product Categories...\n\n";

// Define proper category mappings
$categoryMappings = [
    'Premium Beard Oil' => 'beard-care',
    'Activated Charcoal Face Wash' => 'skincare',
    'Moisturizing Hair Pomade' => 'hair-care',
    'Anti-Aging Eye Cream' => 'skincare',
    'Natural Deodorant Stick' => 'body-care'
];

$products = MongoDBProduct::all();

foreach ($products as $product) {
    $oldCategory = $product->category;
    $newCategory = $categoryMappings[$product->name] ?? 'skincare';
    
    $product->category = $newCategory;
    $product->save();
    
    echo "✅ {$product->name}: '{$oldCategory}' → '{$newCategory}'\n";
}

echo "\n📊 Updated Categories:\n";
$categories = MongoDBProduct::distinct('category')->get()->pluck('category')->unique()->sort();
foreach ($categories as $category) {
    $count = MongoDBProduct::where('category', $category)->count();
    echo "  - {$category}: {$count} products\n";
}

echo "\n✅ Categories fixed!\n";