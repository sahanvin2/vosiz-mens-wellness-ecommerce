<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;

class CreateSampleProducts extends Command
{
    protected $signature = 'mongo:create-samples';
    protected $description = 'Create sample products in MongoDB using Laravel models';

    public function handle()
    {
        $this->info('🚀 Creating sample products in MongoDB...');

        try {
            // Clear existing data
            MongoDBProduct::truncate();
            MongoCategory::truncate();

            // Create categories
            $skincare = MongoCategory::create([
                'name' => 'Skincare',
                'slug' => 'skincare',
                'description' => 'Premium skincare products for men',
                'status' => true,
                'sort_order' => 1
            ]);
            $this->info('✅ Created category: Skincare');

            $haircare = MongoCategory::create([
                'name' => 'Hair Care',
                'slug' => 'hair-care',
                'description' => 'Professional hair care solutions',
                'status' => true,
                'sort_order' => 2
            ]);
            $this->info('✅ Created category: Hair Care');

            $bodycare = MongoCategory::create([
                'name' => 'Body Care',
                'slug' => 'body-care',
                'description' => 'Complete body care essentials',
                'status' => true,
                'sort_order' => 3
            ]);
            $this->info('✅ Created category: Body Care');

            // Create products
            $products = [
                [
                    'name' => 'Premium Face Moisturizer',
                    'slug' => 'premium-face-moisturizer',
                    'description' => 'Advanced hydrating formula with hyaluronic acid and vitamin E. Perfect for daily use to keep your skin moisturized and healthy.',
                    'short_description' => 'Daily hydrating moisturizer for men',
                    'price' => 49.99,
                    'sale_price' => 39.99,
                    'sku' => 'VSZ-FM-001',
                    'stock_quantity' => 150,
                    'category_id' => $skincare->_id,
                    'category_name' => 'Skincare',
                    'brand' => 'Vosiz',
                    'status' => 'active',
                    'is_featured' => true,
                    'tags' => ['moisturizer', 'hydrating', 'daily-care', 'hyaluronic-acid'],
                    'images' => ['/images/products/moisturizer-1.jpg'],
                    'specifications' => [
                        'Size' => '50ml',
                        'Type' => 'Cream',
                        'Skin Type' => 'All skin types'
                    ],
                    'ingredients' => ['Hyaluronic Acid', 'Vitamin E', 'Glycerin'],
                    'features' => ['24-hour hydration', 'Non-greasy formula', 'Fast absorption'],
                ],
                [
                    'name' => 'Charcoal Face Cleanser',
                    'slug' => 'charcoal-face-cleanser',
                    'description' => 'Deep cleansing charcoal face wash that removes impurities and excess oil.',
                    'short_description' => 'Deep cleansing charcoal face wash',
                    'price' => 29.99,
                    'sku' => 'VSZ-FC-002',
                    'stock_quantity' => 200,
                    'category_id' => $skincare->_id,
                    'category_name' => 'Skincare',
                    'brand' => 'Vosiz',
                    'status' => 'active',
                    'is_featured' => false,
                    'tags' => ['cleanser', 'charcoal', 'deep-clean'],
                    'images' => ['/images/products/cleanser-1.jpg'],
                    'specifications' => [
                        'Size' => '150ml',
                        'Type' => 'Gel'
                    ],
                    'ingredients' => ['Activated Charcoal', 'Salicylic Acid'],
                    'features' => ['Removes blackheads', 'Controls oil'],
                ],
                [
                    'name' => 'Professional Hair Styling Wax',
                    'slug' => 'professional-hair-styling-wax',
                    'description' => 'Strong hold hair wax with natural shine finish.',
                    'short_description' => 'Strong hold styling wax',
                    'price' => 35.99,
                    'sale_price' => 28.99,
                    'sku' => 'VSZ-HW-003',
                    'stock_quantity' => 75,
                    'category_id' => $haircare->_id,
                    'category_name' => 'Hair Care',
                    'brand' => 'Vosiz',
                    'status' => 'active',
                    'is_featured' => true,
                    'tags' => ['hair-wax', 'styling', 'strong-hold'],
                    'images' => ['/images/products/hair-wax-1.jpg'],
                    'specifications' => [
                        'Size' => '100g',
                        'Hold' => 'Strong'
                    ],
                    'ingredients' => ['Beeswax', 'Carnauba Wax'],
                    'features' => ['All-day hold', 'Easy to wash out'],
                ],
                [
                    'name' => 'Revitalizing Body Wash',
                    'slug' => 'revitalizing-body-wash',
                    'description' => 'Energizing body wash with natural extracts.',
                    'short_description' => 'Energizing body wash',
                    'price' => 24.99,
                    'sku' => 'VSZ-BW-004',
                    'stock_quantity' => 120,
                    'category_id' => $bodycare->_id,
                    'category_name' => 'Body Care',
                    'brand' => 'Vosiz',
                    'status' => 'active',
                    'is_featured' => false,
                    'tags' => ['body-wash', 'energizing', 'natural'],
                    'images' => ['/images/products/body-wash-1.jpg'],
                    'specifications' => [
                        'Size' => '300ml',
                        'Type' => 'Liquid'
                    ],
                    'ingredients' => ['Aloe Vera', 'Citrus Extracts'],
                    'features' => ['Long-lasting fragrance', 'Moisturizing'],
                ],
                [
                    'name' => 'Anti-Aging Eye Cream',
                    'slug' => 'anti-aging-eye-cream',
                    'description' => 'Specialized eye cream to reduce fine lines and dark circles.',
                    'short_description' => 'Anti-aging eye cream',
                    'price' => 59.99,
                    'sale_price' => 47.99,
                    'sku' => 'VSZ-EC-005',
                    'stock_quantity' => 80,
                    'category_id' => $skincare->_id,
                    'category_name' => 'Skincare',
                    'brand' => 'Vosiz',
                    'status' => 'active',
                    'is_featured' => true,
                    'tags' => ['eye-cream', 'anti-aging', 'dark-circles'],
                    'images' => ['/images/products/eye-cream-1.jpg'],
                    'specifications' => [
                        'Size' => '15ml',
                        'Type' => 'Cream'
                    ],
                    'ingredients' => ['Retinol', 'Caffeine', 'Peptides'],
                    'features' => ['Reduces fine lines', 'Diminishes dark circles'],
                ]
            ];

            foreach ($products as $productData) {
                $product = MongoDBProduct::create($productData);
                $this->info('✅ Created product: ' . $product->name);
            }

            // Display summary
            $totalCategories = MongoCategory::count();
            $totalProducts = MongoDBProduct::count();
            $activeProducts = MongoDBProduct::where('status', 'active')->count();
            $featuredProducts = MongoDBProduct::where('is_featured', true)->count();

            $this->info('');
            $this->info('📊 Summary:');
            $this->info("   Categories: {$totalCategories}");
            $this->info("   Products: {$totalProducts}");
            $this->info("   Active Products: {$activeProducts}");
            $this->info("   Featured Products: {$featuredProducts}");
            $this->info('');
            $this->info('🎉 Sample products created successfully in MongoDB!');

        } catch (\Exception $e) {
            $this->error('❌ Failed to create samples: ' . $e->getMessage());
            $this->error('Details: ' . $e->getFile() . ':' . $e->getLine());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}