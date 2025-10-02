<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\MongoDBProduct;
use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Set up Laravel database configuration
$capsule = new Capsule;

// MySQL connection
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_DATABASE'] ?? 'laravel',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
], 'mysql');

// MongoDB connection
$capsule->addConnection([
    'driver' => 'mongodb',
    'dsn' => $_ENV['MONGODB_DSN'],
    'database' => 'sahannawarathne2004_db_user',
], 'mongodb');

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Checking Products via Laravel Models...\n\n";

try {
    // Check MongoDB products using the model
    $mongoProducts = MongoDBProduct::all();
    echo "📦 Products in MongoDB (via Laravel Model): " . $mongoProducts->count() . "\n";
    
    if ($mongoProducts->count() > 0) {
        echo "✅ Sample products:\n";
        foreach ($mongoProducts->take(3) as $product) {
            echo "   - {$product->name} (ID: {$product->_id})\n";
            echo "     Category: {$product->category_name}\n";
            echo "     Price: $" . number_format((float)$product->price, 2) . "\n";
            echo "     Images: " . (count($product->images ?? []) ? count($product->images) . " images" : "No images") . "\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error checking MongoDB products: " . $e->getMessage() . "\n";
}

echo "🎯 Checking website functionality...\n";
echo "🌐 Open these URLs to test:\n";
echo "   📱 Products page: http://localhost:8000/products\n";
echo "   ⚙️ Admin panel: http://localhost:8000/admin/products\n";
echo "   👤 Login: http://localhost:8000/login\n\n";
?>