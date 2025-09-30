<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\Category;
use App\Models\User;

class VosizStatus extends Command
{
    protected $signature = 'vosiz:status';
    protected $description = 'Show current status of Vosiz MongoDB products and system';

    public function handle()
    {
        $this->info('🏢 Vosiz Men\'s Health & Wellness - System Status');
        $this->newLine();

        try {
            // MongoDB Products Stats
            $totalProducts = MongoDBProduct::count();
            $featuredProducts = MongoDBProduct::where('is_featured', true)->count();
            $activeProducts = MongoDBProduct::where('is_active', true)->count();
            
            $this->comment('📦 MongoDB Products:');
            $this->line("   Total Products: {$totalProducts}");
            $this->line("   Featured Products: {$featuredProducts}");
            $this->line("   Active Products: {$activeProducts}");
            
            // Category breakdown
            $categories = Category::all();
            $this->newLine();
            $this->comment('📂 Products by Category:');
            foreach ($categories as $category) {
                $count = MongoDBProduct::where('category_name', $category->name)->count();
                $this->line("   {$category->name}: {$count} products");
            }
            
            // Price ranges
            $this->newLine();
            $this->comment('💰 Price Analysis:');
            $products = MongoDBProduct::all();
            if ($products->count() > 0) {
                $prices = $products->pluck('price')->map(function($price) {
                    return floatval($price);
                });
                
                $this->line("   Lowest Price: $" . number_format($prices->min(), 2));
                $this->line("   Highest Price: $" . number_format($prices->max(), 2));
                $this->line("   Average Price: $" . number_format($prices->avg(), 2));
                
                $under25 = $prices->filter(fn($p) => $p < 25)->count();
                $between25_50 = $prices->filter(fn($p) => $p >= 25 && $p < 50)->count();
                $over50 = $prices->filter(fn($p) => $p >= 50)->count();
                
                $this->line("   Under $25: {$under25} products");
                $this->line("   $25-$50: {$between25_50} products");
                $this->line("   Over $50: {$over50} products");
            }
            
            // Stock levels
            $this->newLine();
            $this->comment('📊 Stock Levels:');
            $lowStock = MongoDBProduct::where('stock_quantity', '<=', 50)->count();
            $mediumStock = MongoDBProduct::where('stock_quantity', '>', 50)->where('stock_quantity', '<=', 150)->count();
            $highStock = MongoDBProduct::where('stock_quantity', '>', 150)->count();
            
            $this->line("   Low Stock (≤50): {$lowStock} products");
            $this->line("   Medium Stock (51-150): {$mediumStock} products");
            $this->line("   High Stock (>150): {$highStock} products");
            
            // Recent products
            $this->newLine();
            $this->comment('🆕 Recent Products:');
            $recent = MongoDBProduct::orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($recent as $product) {
                $this->line("   • {$product->name} (SKU: {$product->sku})");
            }
            
            // System info
            $this->newLine();
            $this->comment('🔧 System Information:');
            $this->line("   Total Users: " . User::count());
            $this->line("   Total Categories: " . Category::count());
            $this->line("   MongoDB Connection: ✅ Working");
            $this->line("   Products Directory: " . (is_dir(public_path('storage/products')) ? '✅ Ready' : '❌ Missing'));
            
            $this->newLine();
            $this->info('🎯 Ready for image uploads! You can now:');
            $this->line('   • Visit admin panel: http://127.0.0.1:8000/admin/products/manage');
            $this->line('   • Edit products to add images');
            $this->line('   • Test product creation with images');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}