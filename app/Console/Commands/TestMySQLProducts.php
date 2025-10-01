<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;

class TestMySQLProducts extends Command
{
    protected $signature = 'vosiz:test-mysql-products';
    protected $description = 'Test MySQL product functionality';

    public function handle()
    {
        $this->info('🛍️  Testing MySQL Product Functionality...');
        $this->newLine();

        try {
            // Test product count
            $productCount = Product::count();
            $this->line("✅ Total products: {$productCount}");

            // Test active products
            $activeProducts = Product::where('is_active', true)->count();
            $this->line("✅ Active products: {$activeProducts}");

            // Test featured products
            $featuredProducts = Product::where('is_featured', true)->count();
            $this->line("✅ Featured products: {$featuredProducts}");

            // Test categories
            $categoryCount = Category::count();
            $this->line("✅ Total categories: {$categoryCount}");

            if ($productCount > 0) {
                $this->newLine();
                $this->info('📦 Sample Products:');
                
                $products = Product::with('category')->limit(3)->get();
                
                $data = [];
                foreach ($products as $product) {
                    $data[] = [
                        'ID' => $product->id,
                        'Name' => $product->name,
                        'Price' => '$' . number_format((float)$product->price, 2),
                        'Category' => $product->category->name ?? 'No Category',
                        'Status' => $product->is_active ? 'Active' : 'Inactive'
                    ];
                }
                
                $this->table(['ID', 'Name', 'Price', 'Category', 'Status'], $data);
            }

            $this->newLine();
            $this->info('🎉 MySQL Product functionality is working correctly!');
            $this->line('   The products page should now work without MongoDB Atlas errors.');

        } catch (\Exception $e) {
            $this->error('❌ MySQL Product test failed: ' . $e->getMessage());
            $this->line('   Error details: ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}