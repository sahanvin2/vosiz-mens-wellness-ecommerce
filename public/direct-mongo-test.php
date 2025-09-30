<?php
// Simple MongoDB Test - No Laravel, No Authentication
echo "<h1>Direct MongoDB Connection Test</h1>";

try {
    // Try to connect to MongoDB directly
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->vosiz_laravel;
    $collection = $database->mongo_d_b_products;
    
    $count = $collection->countDocuments();
    echo "<p>✅ MongoDB Connection: SUCCESS</p>";
    echo "<p>📊 Products found: $count</p>";
    
    if ($count > 0) {
        echo "<h2>Sample Products:</h2>";
        $products = $collection->find([], ['limit' => 5]);
        
        foreach ($products as $product) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h3>" . ($product['name'] ?? 'No Name') . "</h3>";
            echo "<p>Price: $" . ($product['price'] ?? '0.00') . "</p>";
            echo "<p>SKU: " . ($product['sku'] ?? 'No SKU') . "</p>";
            echo "<p>Category: " . ($product['category_name'] ?? 'No Category') . "</p>";
            echo "<p>Featured: " . (($product['is_featured'] ?? false) ? 'Yes' : 'No') . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>❌ No products found in MongoDB</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ MongoDB Error: " . $e->getMessage() . "</p>";
    
    // Try Laravel connection as fallback
    echo "<h2>Trying Laravel MongoDB...</h2>";
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $kernel->bootstrap();
        
        $count = \App\Models\MongoDBProduct::count();
        echo "<p>✅ Laravel MongoDB: SUCCESS - $count products</p>";
        
        $products = \App\Models\MongoDBProduct::limit(5)->get();
        foreach ($products as $product) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<h3>" . $product->name . "</h3>";
            echo "<p>Price: $" . $product->price . "</p>";
            echo "<p>SKU: " . $product->sku . "</p>";
            echo "</div>";
        }
        
    } catch (Exception $e2) {
        echo "<p>❌ Laravel Error: " . $e2->getMessage() . "</p>";
    }
}
?>