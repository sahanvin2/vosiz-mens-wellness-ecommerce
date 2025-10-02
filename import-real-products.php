<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "🗑️ Clearing existing placeholder products from MongoDB...\n\n";

try {
    $client = new Client($_ENV['MONGODB_DSN']);
    $database = $client->selectDatabase('sahannawarathne2004_db_user');
    $collection = $database->selectCollection('products');
    
    // Clear existing products
    $deleteResult = $collection->deleteMany([]);
    echo "✅ Deleted {$deleteResult->getDeletedCount()} existing products\n\n";
    
    // Read and import new products from JSON file
    $jsonFile = __DIR__ . '/mongodb-products-import.json';
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $products = json_decode($jsonData, true);
        
        if ($products && is_array($products)) {
            $productCount = count($products);
            echo "📦 Importing {$productCount} new products...\n\n";
            
            foreach ($products as $index => $product) {
                // Convert MongoDB ObjectId format
                if (isset($product['_id']['$oid'])) {
                    $product['_id'] = new \MongoDB\BSON\ObjectId($product['_id']['$oid']);
                }
                
                // Convert date fields
                if (isset($product['created_at']['$date'])) {
                    $product['created_at'] = new \MongoDB\BSON\UTCDateTime(strtotime($product['created_at']['$date']) * 1000);
                }
                if (isset($product['updated_at']['$date'])) {
                    $product['updated_at'] = new \MongoDB\BSON\UTCDateTime(strtotime($product['updated_at']['$date']) * 1000);
                }
                
                try {
                    $collection->insertOne($product);
                    echo "✅ Imported: {$product['name']}\n";
                    echo "   💰 Price: $" . number_format($product['price'], 2) . "\n";
                    echo "   📂 Category: {$product['category_name']}\n";
                    echo "   🖼️ Image: {$product['images'][0]}\n\n";
                } catch (Exception $e) {
                    echo "❌ Failed to import {$product['name']}: " . $e->getMessage() . "\n\n";
                }
            }
            
            $finalCount = $collection->countDocuments();
            echo "🎉 Import completed! Total products in database: {$finalCount}\n\n";
            
        } else {
            echo "❌ Invalid JSON data in import file\n";
        }
    } else {
        echo "❌ Import file not found: {$jsonFile}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "📋 NEXT STEPS:\n";
echo "==============\n";
echo "1. Add real product images to: /public/images/products/\n";
echo "2. Name your images exactly as referenced in the JSON:\n";
echo "   - hims-shampoo.jpg\n";
echo "   - manskin-moisturizer.jpg\n";
echo "   - marlowe-grooming-set.jpg\n";
echo "   - premium-beard-oil.jpg\n";
echo "   - beard-trimmer-kit.jpg\n";
echo "   - executive-cologne.jpg\n";
echo "   - hair-pomade.jpg\n";
echo "   - vitamin-c-serum.jpg\n";
echo "   - natural-deodorant.jpg\n";
echo "   - shaving-kit.jpg\n\n";
echo "3. Test your website: http://localhost:8000/products\n";
echo "4. Use admin panel to add more products: http://localhost:8000/admin/products\n\n";
echo "✨ Your real product database is now ready!\n";
?>