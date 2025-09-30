<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;

class MongoQuery extends Command
{
    protected $signature = 'mongo:query';
    protected $description = 'Query MongoDB data using Laravel models';

    public function handle()
    {
        $this->info('🔍 Querying MongoDB data...');

        try {
            // Count documents
            $productCount = MongoDBProduct::count();
            $categoryCount = MongoCategory::count();

            $this->info("📊 Total Products: {$productCount}");
            $this->info("📊 Total Categories: {$categoryCount}");

            if ($productCount > 0) {
                $this->info('');
                $this->info('🛍️  Sample Products:');
                
                $products = MongoDBProduct::limit(5)->get();
                foreach ($products as $product) {
                    $price = $product->sale_price ? "\${$product->sale_price}" : "\${$product->price}";
                    $status = $product->is_featured ? ' ⭐ Featured' : '';
                    $this->line("   • {$product->name} - {$price}{$status}");
                }

                // Test queries
                $this->info('');
                $this->info('🔎 Query Results:');
                
                $activeProducts = MongoDBProduct::where('status', 'active')->count();
                $featuredProducts = MongoDBProduct::where('is_featured', true)->count();
                $skincareProducts = MongoDBProduct::where('category', 'skincare')->count();

                $this->line("   • Active Products: {$activeProducts}");
                $this->line("   • Featured Products: {$featuredProducts}");
                $this->line("   • Skincare Products: {$skincareProducts}");

                // Search test
                $searchResults = MongoDBProduct::where('name', 'regex', '/moisturizer/i')->count();
                $this->line("   • Products containing 'moisturizer': {$searchResults}");
            }

            $this->info('');
            $this->info('✅ MongoDB query completed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ MongoDB query failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}