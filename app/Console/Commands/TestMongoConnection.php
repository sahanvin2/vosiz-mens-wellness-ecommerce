<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;

class TestMongoConnection extends Command
{
    protected $signature = 'mongo:test';
    protected $description = 'Test MongoDB connection';

    public function handle()
    {
        try {
            $this->info('Testing MongoDB connection...');
            
            // Test creating a category
            $category = MongoCategory::create([
                'name' => 'Test Category',
                'slug' => 'test-category',
                'description' => 'This is a test category',
                'status' => true,
                'sort_order' => 1
            ]);
            
            $this->info('✅ MongoDB Category created with ID: ' . $category->_id);
            
            // Test creating a product
            $product = MongoDBProduct::create([
                'name' => 'Test Product',
                'slug' => 'test-product',
                'description' => 'This is a test product for MongoDB',
                'short_description' => 'Test product',
                'price' => 29.99,
                'sale_price' => 24.99,
                'sku' => 'TEST-001',
                'stock_quantity' => 100,
                'category_id' => $category->_id,
                'brand' => 'Vosiz',
                'status' => 'active',
                'is_featured' => true,
                'tags' => ['test', 'mongodb', 'skincare']
            ]);
            
            $this->info('✅ MongoDB Product created with ID: ' . $product->_id);
            
            // Count documents
            $categoryCount = MongoCategory::count();
            $productCount = MongoDBProduct::count();
            
            $this->info("Total categories in MongoDB: {$categoryCount}");
            $this->info("Total products in MongoDB: {$productCount}");
            
            // Test querying
            $activeProducts = MongoDBProduct::active()->count();
            $featuredProducts = MongoDBProduct::featured()->count();
            
            $this->info("Active products: {$activeProducts}");
            $this->info("Featured products: {$featuredProducts}");
            
            // Clean up test data
            $product->delete();
            $category->delete();
            
            $this->info('✅ Test documents removed');
            $this->info('✅ MongoDB connection and operations successful!');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ MongoDB connection failed: ' . $e->getMessage());
            $this->error('Error details: ' . $e->getFile() . ':' . $e->getLine());
            return Command::FAILURE;
        }
    }
}