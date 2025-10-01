<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MongoDBAtlasService;

class PopulateMongoAtlas extends Command
{
    protected $signature = 'vosiz:populate-mongo-atlas';
    protected $description = 'Populate MongoDB Atlas with sample product data';

    public function handle()
    {
        $this->info('🚀 Populating MongoDB Atlas with Sample Data...');
        $this->newLine();

        try {
            $mongoService = new MongoDBAtlasService();
            
            if (!$mongoService->testConnection()) {
                throw new \Exception('MongoDB Atlas connection failed');
            }

            $this->line('✅ MongoDB Atlas connection verified');

            // Get database instance
            $database = $mongoService->getDatabase();

            // 1. Create Products Collection with Sample Data
            $this->info('📦 Creating Products...');
            $products = $this->getProductSamples();
            $productsCollection = $database->selectCollection('products');
            
            foreach ($products as $product) {
                $result = $productsCollection->insertOne($product);
                $this->line("   ✅ Created product: {$product['name']} (ID: {$result->getInsertedId()})");
            }

            // 2. Create Categories Collection
            $this->info('📁 Creating Categories...');
            $categories = $this->getCategorySamples();
            $categoriesCollection = $database->selectCollection('categories');
            
            foreach ($categories as $category) {
                $result = $categoriesCollection->insertOne($category);
                $this->line("   ✅ Created category: {$category['name']} (ID: {$result->getInsertedId()})");
            }

            // 3. Create Reviews Collection
            $this->info('⭐ Creating Sample Reviews...');
            $reviews = $this->getReviewSamples();
            $reviewsCollection = $database->selectCollection('reviews');
            
            foreach ($reviews as $review) {
                $result = $reviewsCollection->insertOne($review);
                $this->line("   ✅ Created review: {$review['title']} (ID: {$result->getInsertedId()})");
            }

            // 4. Create Indexes for Performance
            $this->info('🔍 Creating Database Indexes...');
            $this->createIndexes($database);

            // 5. Show Summary
            $this->newLine();
            $this->info('📊 Database Population Summary:');
            
            $stats = [
                ['Collection', 'Documents', 'Status'],
                ['Products', $productsCollection->countDocuments(), '✅ Ready'],
                ['Categories', $categoriesCollection->countDocuments(), '✅ Ready'],
                ['Reviews', $reviewsCollection->countDocuments(), '✅ Ready'],
            ];
            
            $this->table($stats[0], array_slice($stats, 1));

            $this->newLine();
            $this->info('🎉 MongoDB Atlas database populated successfully!');
            $this->line('   🌐 Database: vosiz_products');
            $this->line('   📊 Total Collections: 3');
            $this->line('   🔧 Indexes: Created for optimal performance');

        } catch (\Exception $e) {
            $this->error('❌ Database population failed: ' . $e->getMessage());
            $this->line('   Details: ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    private function getProductSamples()
    {
        $timestamp = now();
        
        return [
            [
                'name' => 'Premium Beard Oil',
                'slug' => 'premium-beard-oil',
                'description' => 'Nourishing beard oil crafted with natural ingredients to promote healthy beard growth and maintain soft, manageable facial hair.',
                'short_description' => 'Natural beard oil for healthy, soft facial hair',
                'price' => 29.99,
                'compare_price' => 39.99,
                'sku' => 'VOSIZ-BEARD-001',
                'stock_quantity' => 150,
                'category' => 'beard-care',
                'category_id' => 1,
                'brand' => 'Vosiz',
                'images' => [
                    '/images/products/beard-oil-1.jpg',
                    '/images/products/beard-oil-2.jpg'
                ],
                'ingredients' => ['Jojoba Oil', 'Argan Oil', 'Sweet Almond Oil', 'Vitamin E', 'Essential Oils'],
                'benefits' => ['Promotes Growth', 'Reduces Itching', 'Adds Shine', 'Softens Hair'],
                'how_to_use' => 'Apply 3-5 drops to palm, rub hands together, and massage into beard and skin.',
                'tags' => ['natural', 'organic', 'beard', 'grooming', 'men'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Activated Charcoal Face Wash',
                'slug' => 'activated-charcoal-face-wash',
                'description' => 'Deep cleansing face wash with activated charcoal to remove impurities, excess oil, and toxins for a fresh, clean complexion.',
                'short_description' => 'Deep cleansing charcoal face wash for men',
                'price' => 24.99,
                'compare_price' => 29.99,
                'sku' => 'VOSIZ-FACE-002',
                'stock_quantity' => 200,
                'category' => 'skincare',
                'category_id' => 2,
                'brand' => 'Vosiz',
                'images' => [
                    '/images/products/charcoal-wash-1.jpg',
                    '/images/products/charcoal-wash-2.jpg'
                ],
                'ingredients' => ['Activated Charcoal', 'Glycolic Acid', 'Tea Tree Oil', 'Aloe Vera'],
                'benefits' => ['Deep Cleansing', 'Oil Control', 'Pore Minimizing', 'Anti-Bacterial'],
                'how_to_use' => 'Wet face, apply small amount, massage gently, rinse thoroughly with water.',
                'tags' => ['charcoal', 'cleansing', 'skincare', 'men', 'deep-clean'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Moisturizing Hair Pomade',
                'slug' => 'moisturizing-hair-pomade',
                'description' => 'Strong hold hair pomade with moisturizing properties. Perfect for creating classic and modern hairstyles while keeping hair healthy.',
                'short_description' => 'Strong hold pomade with moisture protection',
                'price' => 22.99,
                'compare_price' => null,
                'sku' => 'VOSIZ-HAIR-003',
                'stock_quantity' => 75,
                'category' => 'hair-care',
                'category_id' => 3,
                'brand' => 'Vosiz',
                'images' => [
                    '/images/products/pomade-1.jpg',
                    '/images/products/pomade-2.jpg'
                ],
                'ingredients' => ['Beeswax', 'Shea Butter', 'Coconut Oil', 'Lanolin', 'Castor Oil'],
                'benefits' => ['Strong Hold', 'Moisture Protection', 'Natural Shine', 'Easy Application'],
                'how_to_use' => 'Work small amount between hands, apply to damp or dry hair, style as desired.',
                'tags' => ['pomade', 'hair', 'styling', 'hold', 'moisture'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Anti-Aging Eye Cream',
                'slug' => 'anti-aging-eye-cream',
                'description' => 'Specialized eye cream designed for men to reduce dark circles, puffiness, and fine lines around the delicate eye area.',
                'short_description' => 'Men\'s anti-aging eye cream for dark circles and wrinkles',
                'price' => 34.99,
                'compare_price' => 44.99,
                'sku' => 'VOSIZ-EYE-004',
                'stock_quantity' => 60,
                'category' => 'skincare',
                'category_id' => 2,
                'brand' => 'Vosiz',
                'images' => [
                    '/images/products/eye-cream-1.jpg'
                ],
                'ingredients' => ['Retinol', 'Hyaluronic Acid', 'Peptides', 'Caffeine', 'Vitamin C'],
                'benefits' => ['Reduces Dark Circles', 'Minimizes Puffiness', 'Anti-Aging', 'Hydrating'],
                'how_to_use' => 'Gently pat small amount around eye area morning and evening.',
                'tags' => ['eye-cream', 'anti-aging', 'skincare', 'men', 'dark-circles'],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Natural Deodorant Stick',
                'slug' => 'natural-deodorant-stick',
                'description' => 'Aluminum-free natural deodorant with long-lasting protection. Made with organic ingredients for sensitive skin.',
                'short_description' => 'Aluminum-free natural deodorant for all-day protection',
                'price' => 12.99,
                'compare_price' => null,
                'sku' => 'VOSIZ-DEO-005',
                'stock_quantity' => 120,
                'category' => 'body-care',
                'category_id' => 4,
                'brand' => 'Vosiz',
                'images' => [
                    '/images/products/deodorant-1.jpg'
                ],
                'ingredients' => ['Coconut Oil', 'Shea Butter', 'Arrowroot Powder', 'Baking Soda', 'Essential Oils'],
                'benefits' => ['Aluminum-Free', 'Long-Lasting', 'Natural Ingredients', 'Sensitive Skin Friendly'],
                'how_to_use' => 'Apply to clean, dry underarms. Allow to absorb before dressing.',
                'tags' => ['natural', 'deodorant', 'aluminum-free', 'organic', 'sensitive-skin'],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]
        ];
    }

    private function getCategorySamples()
    {
        $timestamp = now();
        
        return [
            [
                'name' => 'Beard Care',
                'slug' => 'beard-care',
                'description' => 'Complete beard care products for grooming and maintenance',
                'image' => '/images/categories/beard-care.jpg',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $timestamp
            ],
            [
                'name' => 'Skincare',
                'slug' => 'skincare',
                'description' => 'Men\'s skincare essentials for healthy, clear skin',
                'image' => '/images/categories/skincare.jpg',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $timestamp
            ],
            [
                'name' => 'Hair Care',
                'slug' => 'hair-care',
                'description' => 'Professional hair styling and care products',
                'image' => '/images/categories/hair-care.jpg',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $timestamp
            ],
            [
                'name' => 'Body Care',
                'slug' => 'body-care',
                'description' => 'Complete body care and hygiene products',
                'image' => '/images/categories/body-care.jpg',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => $timestamp
            ]
        ];
    }

    private function getReviewSamples()
    {
        $timestamp = now();
        
        return [
            [
                'product_id' => 'premium-beard-oil',
                'user_id' => 1,
                'rating' => 5,
                'title' => 'Amazing beard oil!',
                'comment' => 'This beard oil is fantastic. Made my beard so much softer and easier to manage. The scent is perfect too.',
                'verified_purchase' => true,
                'helpful_votes' => 12,
                'created_at' => $timestamp
            ],
            [
                'product_id' => 'activated-charcoal-face-wash',
                'user_id' => 2,
                'rating' => 4,
                'title' => 'Great for oily skin',
                'comment' => 'Really helps control oil and keeps my face feeling clean all day. Highly recommend for guys with oily skin.',
                'verified_purchase' => true,
                'helpful_votes' => 8,
                'created_at' => $timestamp
            ],
            [
                'product_id' => 'anti-aging-eye-cream',
                'user_id' => 3,
                'rating' => 5,
                'title' => 'Reduced my dark circles significantly',
                'comment' => 'After using for 3 weeks, I can see a real difference in my dark circles. Worth every penny.',
                'verified_purchase' => true,
                'helpful_votes' => 15,
                'created_at' => $timestamp
            ]
        ];
    }

    private function createIndexes($database)
    {
        try {
            // Products indexes
            $products = $database->selectCollection('products');
            $products->createIndex(['name' => 'text', 'description' => 'text', 'tags' => 'text']);
            $products->createIndex(['category' => 1, 'is_active' => 1]);
            $products->createIndex(['price' => 1]);
            $products->createIndex(['is_featured' => 1, 'is_active' => 1]);
            $products->createIndex(['sku' => 1], ['unique' => true]);
            $this->line('   ✅ Product indexes created');

            // Reviews indexes
            $reviews = $database->selectCollection('reviews');
            $reviews->createIndex(['product_id' => 1]);
            $reviews->createIndex(['user_id' => 1]);
            $reviews->createIndex(['rating' => 1]);
            $this->line('   ✅ Review indexes created');

            // Categories indexes
            $categories = $database->selectCollection('categories');
            $categories->createIndex(['slug' => 1], ['unique' => true]);
            $categories->createIndex(['is_active' => 1, 'sort_order' => 1]);
            $this->line('   ✅ Category indexes created');

        } catch (\Exception $e) {
            $this->warn('   ⚠️  Index creation failed: ' . $e->getMessage());
        }
    }
}