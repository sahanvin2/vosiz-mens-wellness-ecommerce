<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;
use Illuminate\Support\Facades\DB;

class TestMongoDBConnection extends Command
{
    protected $signature = 'mongodb:test-connection';
    protected $description = 'Test MongoDB connection and create vosiz_products database';

    public function handle()
    {
        $this->info('🔄 Testing MongoDB Connection...');
        $this->newLine();

        try {
            // Test basic connection
            $mongoHost = env('MONGO_DB_HOST', '127.0.0.1');
            $mongoPort = env('MONGO_DB_PORT', 27017);
            $mongoDatabase = env('MONGODB_DATABASE', 'vosiz_products');

            $this->info("📡 Attempting connection to MongoDB:");
            $this->line("   Host: {$mongoHost}");
            $this->line("   Port: {$mongoPort}");
            $this->line("   Database: {$mongoDatabase}");
            $this->newLine();

            // Test MongoDB connection using Laravel models
            try {
                $this->info("✅ MongoDB Laravel package is installed");
                
                // Test MongoDB connection
                $connection = DB::connection('mongodb');
                $this->info("✅ MongoDB connection established");

                // Test MongoDB models
                $productCount = MongoDBProduct::count();
                $categoryCount = MongoCategory::count();
                
                $this->info("📁 MongoDB Collections:");
                $this->line("   - Products: {$productCount} documents");
                $this->line("   - Categories: {$categoryCount} documents");

                // Test creating a document
                $testProduct = MongoDBProduct::create([
                    'name' => 'Test Connection Product',
                    'slug' => 'test-connection-product',
                    'description' => 'This is a test product for connection testing',
                    'price' => 29.99,
                    'sku' => 'TEST-CONN-001',
                    'stock_quantity' => 10,
                    'status' => 'active',
                    'is_featured' => false,
                    'category_name' => 'Test',
                    'tags' => ['test', 'connection']
                ]);

                if ($testProduct->_id) {
                    $this->info("✅ Successfully created test document");
                    $this->line("   Document ID: " . $testProduct->_id);

                    // Clean up test document
                    $testProduct->delete();
                    $this->info("✅ Test document cleaned up");
                }

                $this->newLine();
                $this->info("🎉 MongoDB is ready for use!");
                $this->newLine();
                $this->comment("💡 Next steps:");
                $this->line("   1. Run: php artisan mongo:create-samples");
                $this->line("   2. Test with: php artisan mongo:query");
                $this->line("   3. Access admin: /admin/products/manage");

            } catch (\Exception $e) {
                $this->error("❌ Database connection failed: " . $e->getMessage());
                $this->showConnectionHelp();
            }

        } catch (\Exception $e) {
            $this->error("❌ Connection test failed: " . $e->getMessage());
            $this->showConnectionHelp();
        }
    }

    private function showConnectionHelp()
    {
        $this->newLine();
        $this->comment("🔧 Troubleshooting MongoDB Connection:");
        $this->line("1. Make sure MongoDB is running:");
        $this->line("   - Check MongoDB Compass");
        $this->line("   - Or check Windows Services for 'MongoDB'");
        $this->newLine();
        $this->line("2. Verify connection details in .env:");
        $this->line("   MONGODB_DSN=mongodb://localhost:27017");
        $this->line("   MONGODB_DATABASE=vosiz_products");
        $this->newLine();
        $this->line("3. Test MongoDB directly:");
        $this->line("   - Open MongoDB Compass");
        $this->line("   - Connect to mongodb://localhost:27017");
        $this->line("   - Create database 'vosiz_products'");
    }

    private function showInstallationHelp()
    {
        $this->newLine();
        $this->comment("📦 MongoDB Package Installation:");
        $this->line("The MongoDB package might not be properly installed.");
        $this->newLine();
        $this->line("Please run:");
        $this->line("   composer require mongodb/laravel-mongodb");
        $this->line("   composer dump-autoload");
    }
}