<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Product Controller Fix\n";
echo "==================================\n\n";

// Test the specific product that was failing
$productId = '68dcd7c0f741bb990902b3b3';

echo "Testing product ID: {$productId}\n";

try {
    // Simulate what the controller does
    $product = null;
    
    // Check if it's a valid MongoDB ObjectID (24 character hex string)
    if (preg_match('/^[a-f\d]{24}$/i', $productId)) {
        echo "✅ Valid MongoDB ObjectID format detected\n";
        
        try {
            $product = App\Models\MongoDBProduct::where('_id', $productId)
                ->where(function($q) {
                    $q->where('is_active', true)
                      ->orWhereNull('is_active');
                })
                ->first();
                
            if ($product) {
                echo "✅ Product found by ObjectID!\n";
                echo "   Name: {$product->name}\n";
                echo "   Price: \${$product->price}\n";
                echo "   Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
            } else {
                echo "❌ Product not found by ObjectID\n";
            }
        } catch (Exception $e) {
            echo "❌ Error finding by ObjectID: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Not a valid MongoDB ObjectID format\n";
    }
    
    if ($product) {
        echo "\n🎯 Product Controller Fix: SUCCESS!\n";
        echo "The URL http://127.0.0.1:8000/products/{$productId} should now work.\n";
    } else {
        echo "\n❌ Product Controller Fix: FAILED!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}