<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing MongoDB Product IDs\n";
echo "==============================\n\n";

try {
    $products = App\Models\MongoDBProduct::limit(10)->get();
    
    if ($products->count() > 0) {
        echo "Found {$products->count()} products:\n\n";
        
        foreach ($products as $product) {
            echo "ID: {$product->_id}\n";
            echo "Name: {$product->name}\n";
            echo "Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
            echo "URL: http://127.0.0.1:8000/products/{$product->_id}\n";
            echo "---\n";
        }
        
        // Test the first product URL
        $firstProduct = $products->first();
        echo "\n🧪 Testing Product URL Fix:\n";
        echo "Product ID: {$firstProduct->_id}\n";
        echo "URL to test: http://127.0.0.1:8000/products/{$firstProduct->_id}\n";
        
    } else {
        echo "❌ No products found in MongoDB\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}