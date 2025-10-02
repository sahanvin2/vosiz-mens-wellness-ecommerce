<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "🔍 Checking MongoDB Collections...\n\n";

try {
    $client = new Client($_ENV['MONGODB_DSN']);
    $database = $client->selectDatabase('sahannawarathne2004_db_user');
    
    // List all collections
    echo "📚 Available Collections:\n";
    $collections = $database->listCollections();
    foreach ($collections as $collection) {
        $name = $collection->getName();
        $count = $database->selectCollection($name)->countDocuments();
        echo "   - {$name}: {$count} documents\n";
    }
    
    echo "\n";
    
    // Check specifically for products in different possible collections
    $possibleCollections = ['products', 'mongodb_products', 'product'];
    
    foreach ($possibleCollections as $collectionName) {
        try {
            $collection = $database->selectCollection($collectionName);
            $count = $collection->countDocuments();
            if ($count > 0) {
                echo "✅ Found products in '{$collectionName}': {$count} items\n";
                
                // Show first product as sample
                $firstProduct = $collection->findOne();
                if ($firstProduct) {
                    echo "   Sample product: " . ($firstProduct['name'] ?? 'No name') . "\n";
                    echo "   Has images: " . (isset($firstProduct['images']) ? 'Yes' : 'No') . "\n";
                }
            }
        } catch (Exception $e) {
            // Collection doesn't exist
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>