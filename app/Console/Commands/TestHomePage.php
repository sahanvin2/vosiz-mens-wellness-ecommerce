<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\MongoDBProduct;

class TestHomePage extends Command
{
    protected $signature = 'test:homepage';
    protected $description = 'Test homepage data availability';

    public function handle()
    {
        $this->info('🏠 Testing Homepage Data');
        $this->newLine();

        // Test MySQL Products
        try {
            $mysqlProducts = Product::where('is_featured', true)
                ->where('is_active', true)
                ->count();
            $this->line("✅ MySQL Featured Products: {$mysqlProducts}");
        } catch (\Exception $e) {
            $this->error("❌ MySQL Products Error: " . $e->getMessage());
        }

        // Test Categories
        try {
            $categories = Category::where('is_active', true)->count();
            $this->line("✅ Active Categories: {$categories}");
        } catch (\Exception $e) {
            $this->error("❌ Categories Error: " . $e->getMessage());
        }

        // Test MongoDB Products
        try {
            $mongoProducts = MongoDBProduct::where('is_featured', true)
                ->where('status', 'active')
                ->count();
            $this->line("✅ MongoDB Featured Products: {$mongoProducts}");
        } catch (\Exception $e) {
            $this->error("❌ MongoDB Products Error: " . $e->getMessage());
        }

        $this->newLine();
        $this->comment('🌐 Test homepage at: http://127.0.0.1:8001/');

        return 0;
    }
}