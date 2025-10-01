<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing MongoDB Connection on Production Server\n";
echo "================================================\n\n";

// Test 1: Check if MongoDB extension is loaded
echo "1️⃣ Checking PHP MongoDB extension...\n";
if (extension_loaded('mongodb')) {
    echo "✅ MongoDB extension is loaded\n";
    $version = phpversion('mongodb');
    echo "   Version: {$version}\n";
} else {
    echo "❌ MongoDB extension is NOT loaded\n";
    echo "   Run the fix-mongodb-ssl.sh script to install it\n";
    exit(1);
}

echo "\n";

// Test 2: Test connection using different methods
echo "2️⃣ Testing MongoDB Atlas connection...\n";

// Method 1: Using DSN (recommended for production)
try {
    echo "   Method 1: DSN Connection...\n";
    $dsn = "mongodb+srv://sahannawarathne2004_db_user:j6caFrJSLCuJ2uP6@cluster0.2m8hhzb.mongodb.net/vosiz_products?retryWrites=true&w=majority&tls=false&ssl=false";
    
    $client = new MongoDB\Client($dsn);
    $database = $client->selectDatabase('vosiz_products');
    $collection = $database->selectCollection('products');
    
    $count = $collection->countDocuments();
    echo "   ✅ DSN Connection successful! Found {$count} products\n";
    
} catch (Exception $e) {
    echo "   ❌ DSN Connection failed: " . $e->getMessage() . "\n";
    
    // Method 2: Try without SSL/TLS
    try {
        echo "   Method 2: Direct connection without SSL...\n";
        $dsn2 = "mongodb://sahannawarathne2004_db_user:j6caFrJSLCuJ2uP6@cluster0-shard-00-00.2m8hhzb.mongodb.net:27017,cluster0-shard-00-01.2m8hhzb.mongodb.net:27017,cluster0-shard-00-02.2m8hhzb.mongodb.net:27017/vosiz_products?ssl=false&replicaSet=atlas-default-shard-0&authSource=admin&retryWrites=true&w=majority";
        
        $client2 = new MongoDB\Client($dsn2);
        $database2 = $client2->selectDatabase('vosiz_products');
        $collection2 = $database2->selectCollection('products');
        
        $count2 = $collection2->countDocuments();
        echo "   ✅ Direct connection successful! Found {$count2} products\n";
        
    } catch (Exception $e2) {
        echo "   ❌ Direct connection failed: " . $e2->getMessage() . "\n";
        
        // Method 3: Test with Laravel configuration
        try {
            echo "   Method 3: Laravel configuration test...\n";
            
            // Use environment variables
            putenv('MONGODB_DSN=mongodb+srv://sahannawarathne2004_db_user:j6caFrJSLCuJ2uP6@cluster0.2m8hhzb.mongodb.net/vosiz_products?retryWrites=true&w=majority&tls=false&ssl=false');
            
            $products = App\Models\MongoDBProduct::limit(1)->get();
            echo "   ✅ Laravel MongoDB connection successful! Found " . $products->count() . " products\n";
            
        } catch (Exception $e3) {
            echo "   ❌ Laravel connection failed: " . $e3->getMessage() . "\n";
            
            echo "\n";
            echo "🔧 RECOMMENDED FIX:\n";
            echo "==================\n";
            echo "1. Run: chmod +x scripts/fix-mongodb-ssl.sh\n";
            echo "2. Run: ./scripts/fix-mongodb-ssl.sh\n";
            echo "3. Update your .env file with:\n";
            echo "   MONGODB_DSN=\"mongodb+srv://sahannawarathne2004_db_user:j6caFrJSLCuJ2uP6@cluster0.2m8hhzb.mongodb.net/vosiz_products?retryWrites=true&w=majority&tls=false&ssl=false\"\n";
            echo "4. Run: php artisan config:clear\n";
            echo "5. Run: php artisan cache:clear\n";
        }
    }
}

echo "\n";
echo "3️⃣ System Information:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Operating System: " . PHP_OS . "\n";
echo "   Extensions: " . implode(', ', array_filter(['mongodb', 'openssl', 'curl'], 'extension_loaded')) . "\n";

echo "\n";
echo "🎯 Test completed!\n";