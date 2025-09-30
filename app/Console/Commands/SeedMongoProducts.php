<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\Category;

class SeedMongoProducts extends Command
{
    protected $signature = 'mongo:seed-products';
    protected $description = 'Seed MongoDB with sample products for Vosiz Men\'s Health & Wellness';

    public function handle()
    {
        $this->info('🌱 Starting MongoDB product seeding for Vosiz...');
        $this->newLine();

        try {
            // Check connection first
            $existingCount = MongoDBProduct::count();
            $this->info("📊 Current MongoDB products: {$existingCount}");

            // Get categories and suppliers from MySQL
            $categories = Category::all();
            
            // Create default suppliers since Supplier model doesn't exist
            $suppliers = collect([
                (object)['id' => 1, 'name' => 'Vosiz Official'],
                (object)['id' => 2, 'name' => 'Premium Beauty Co'],
                (object)['id' => 3, 'name' => 'Men\'s Care Solutions'],
                (object)['id' => 4, 'name' => 'Natural Wellness']
            ]);

            if ($categories->isEmpty()) {
                $this->error('❌ No categories found in MySQL. Please create categories first.');
                return 1;
            }

            $this->info("✅ Found {$categories->count()} categories and {$suppliers->count()} default suppliers");
            $this->newLine();

            // Sample product data for men's health & wellness
            $products = [
                [
                    'name' => 'Premium Face Wash for Men',
                    'description' => 'Deep cleansing face wash specially formulated for men\'s skin. Removes dirt, oil, and impurities while maintaining skin\'s natural moisture balance. Perfect for daily use.',
                    'price' => '24.99',
                    'stock_quantity' => 150,
                    'category_name' => 'Skincare',
                    'is_featured' => true,
                    'tags' => ['skincare', 'face wash', 'men', 'cleansing', 'daily care'],
                    'features' => ['Deep cleansing', 'Oil control', 'Moisturizing', 'For all skin types', 'pH balanced']
                ],
                [
                    'name' => 'Anti-Aging Eye Cream',
                    'description' => 'Advanced eye cream that reduces fine lines, dark circles, and puffiness. Perfect for the modern man who wants to maintain youthful appearance.',
                    'price' => '39.99',
                    'stock_quantity' => 85,
                    'category_name' => 'Skincare',
                    'is_featured' => true,
                    'tags' => ['anti-aging', 'eye cream', 'men', 'wrinkles', 'dark circles'],
                    'features' => ['Reduces fine lines', 'Diminishes dark circles', 'Hydrating formula', 'Quick absorption', 'Caffeine extract']
                ],
                [
                    'name' => 'Energizing Body Wash',
                    'description' => 'Invigorating body wash with natural essential oils. Cleanses and energizes your skin for a fresh start to your day.',
                    'price' => '18.99',
                    'stock_quantity' => 200,
                    'category_name' => 'Body Care',
                    'is_featured' => false,
                    'tags' => ['body wash', 'energizing', 'essential oils', 'men', 'fresh'],
                    'features' => ['Natural essential oils', 'Energizing scent', 'Deep cleansing', 'Long-lasting freshness', 'Paraben-free']
                ],
                [
                    'name' => 'Moisturizing Body Lotion',
                    'description' => 'Rich moisturizing lotion that absorbs quickly without leaving greasy residue. Keeps your skin hydrated all day long.',
                    'price' => '22.99',
                    'stock_quantity' => 120,
                    'category_name' => 'Body Care',
                    'is_featured' => false,
                    'tags' => ['body lotion', 'moisturizer', 'men', 'hydrating', 'non-greasy'],
                    'features' => ['Quick absorption', 'Non-greasy formula', '24-hour hydration', 'Suitable for all skin types', 'Vitamin E enriched']
                ],
                [
                    'name' => 'Strengthening Shampoo',
                    'description' => 'Professional-grade shampoo that strengthens hair from root to tip. Formulated specifically for men\'s hair care needs.',
                    'price' => '26.99',
                    'stock_quantity' => 175,
                    'category_name' => 'Hair Care',
                    'is_featured' => true,
                    'tags' => ['shampoo', 'strengthening', 'men', 'hair care', 'professional'],
                    'features' => ['Strengthens hair', 'Reduces breakage', 'Deep cleansing', 'Suitable for daily use', 'Biotin enriched']
                ],
                [
                    'name' => 'Hair Growth Serum',
                    'description' => 'Advanced hair growth serum with clinically proven ingredients. Promotes healthy hair growth and reduces hair loss.',
                    'price' => '49.99',
                    'stock_quantity' => 60,
                    'category_name' => 'Hair Care',
                    'is_featured' => true,
                    'tags' => ['hair growth', 'serum', 'men', 'hair loss', 'treatment'],
                    'features' => ['Clinically proven', 'Promotes growth', 'Reduces hair loss', 'Easy application', 'Caffeine complex']
                ],
                [
                    'name' => 'Vitamin C Face Serum',
                    'description' => 'Powerful vitamin C serum that brightens skin and provides antioxidant protection. Perfect for daily skincare routine.',
                    'price' => '34.99',
                    'stock_quantity' => 95,
                    'category_name' => 'Skincare',
                    'is_featured' => false,
                    'tags' => ['vitamin c', 'serum', 'brightening', 'antioxidant', 'men'],
                    'features' => ['20% Vitamin C', 'Brightens skin', 'Antioxidant protection', 'Lightweight formula', 'Hyaluronic acid']
                ],
                [
                    'name' => 'Exfoliating Body Scrub',
                    'description' => 'Gentle yet effective body scrub that removes dead skin cells and leaves skin smooth and refreshed.',
                    'price' => '28.99',
                    'stock_quantity' => 110,
                    'category_name' => 'Body Care',
                    'is_featured' => false,
                    'tags' => ['body scrub', 'exfoliating', 'men', 'smooth skin', 'refreshing'],
                    'features' => ['Gentle exfoliation', 'Natural ingredients', 'Smooths skin', 'Refreshing scent', 'Sea salt base']
                ],
                [
                    'name' => 'Premium Beard Oil',
                    'description' => 'Nourishing beard oil blend that softens facial hair and moisturizes the skin underneath. Essential for beard care.',
                    'price' => '31.99',
                    'stock_quantity' => 140,
                    'category_name' => 'Hair Care',
                    'is_featured' => true,
                    'tags' => ['beard oil', 'facial hair', 'men', 'nourishing', 'grooming'],
                    'features' => ['Softens beard', 'Moisturizes skin', 'Natural oils', 'Pleasant scent', 'Argan oil blend']
                ],
                [
                    'name' => 'Hydrating Night Cream',
                    'description' => 'Rich night cream that works while you sleep to repair and hydrate your skin. Wake up with refreshed, renewed skin.',
                    'price' => '42.99',
                    'stock_quantity' => 75,
                    'category_name' => 'Skincare',
                    'is_featured' => false,
                    'tags' => ['night cream', 'hydrating', 'repair', 'men', 'skincare'],
                    'features' => ['Night repair formula', 'Deep hydration', 'Anti-aging properties', 'Rich texture', 'Retinol complex']
                ],
                [
                    'name' => 'Revitalizing Hair Conditioner',
                    'description' => 'Professional conditioner that detangles, smooths, and adds shine to your hair. Perfect complement to our strengthening shampoo.',
                    'price' => '24.99',
                    'stock_quantity' => 165,
                    'category_name' => 'Hair Care',
                    'is_featured' => false,
                    'tags' => ['conditioner', 'revitalizing', 'men', 'hair care', 'shine'],
                    'features' => ['Detangles hair', 'Adds shine', 'Smooths texture', 'Color-safe formula', 'Keratin complex']
                ],
                [
                    'name' => 'Cooling Aftershave Balm',
                    'description' => 'Soothing aftershave balm with cooling menthol. Calms irritation and leaves skin feeling fresh and comfortable.',
                    'price' => '19.99',
                    'stock_quantity' => 130,
                    'category_name' => 'Skincare',
                    'is_featured' => false,
                    'tags' => ['aftershave', 'cooling', 'menthol', 'soothing', 'men'],
                    'features' => ['Cooling effect', 'Soothes irritation', 'Quick absorption', 'Fresh scent', 'Aloe vera infused']
                ],
                [
                    'name' => 'Intensive Hand Cream',
                    'description' => 'Fast-absorbing hand cream that provides long-lasting moisture protection. Perfect for hardworking hands.',
                    'price' => '16.99',
                    'stock_quantity' => 185,
                    'category_name' => 'Body Care',
                    'is_featured' => false,
                    'tags' => ['hand cream', 'intensive', 'moisture', 'protection', 'men'],
                    'features' => ['Fast absorption', 'Long-lasting protection', 'Non-greasy', 'Strengthens skin', 'Shea butter enriched']
                ],
                [
                    'name' => 'Charcoal Face Mask',
                    'description' => 'Deep purifying charcoal mask that draws out impurities and excess oil. Perfect for weekly skincare routine.',
                    'price' => '21.99',
                    'stock_quantity' => 90,
                    'category_name' => 'Skincare',
                    'is_featured' => false,
                    'tags' => ['charcoal', 'face mask', 'purifying', 'deep clean', 'men'],
                    'features' => ['Deep purification', 'Draws out impurities', 'Controls oil', 'Weekly treatment', 'Activated charcoal']
                ],
                [
                    'name' => 'Deodorant Stick - Fresh Mint',
                    'description' => 'Long-lasting deodorant with fresh mint scent. Provides 24-hour protection against odor and wetness.',
                    'price' => '12.99',
                    'stock_quantity' => 220,
                    'category_name' => 'Body Care',
                    'is_featured' => false,
                    'tags' => ['deodorant', 'fresh mint', '24-hour protection', 'men', 'long-lasting'],
                    'features' => ['24-hour protection', 'Fresh mint scent', 'No white marks', 'Quick-dry formula', 'Aluminum-free']
                ]
            ];

            $successCount = 0;
            $skippedCount = 0;

            foreach ($products as $productData) {
                try {
                    // Find category and supplier
                    $category = $categories->where('name', $productData['category_name'])->first();
                    $supplier = $suppliers->random(); // Random supplier for variety

                    if (!$category) {
                        $this->warn("⚠️  Category '{$productData['category_name']}' not found, skipping product: {$productData['name']}");
                        $skippedCount++;
                        continue;
                    }

                    // Check if product already exists
                    $existing = MongoDBProduct::where('name', $productData['name'])->first();
                    if ($existing) {
                        $this->line("📦 Product '{$productData['name']}' already exists, skipping...");
                        $skippedCount++;
                        continue;
                    }

                    // Create new MongoDB product
                    $product = new MongoDBProduct();
                    $product->name = $productData['name'];
                    $product->description = $productData['description'];
                    $product->price = $productData['price'];
                    $product->stock_quantity = $productData['stock_quantity'];
                    $product->category_id = $category->id;
                    $product->category_name = $category->name;
                    $product->supplier_id = $supplier->id;
                    $product->supplier_name = $supplier->name;
                    $product->sku = 'VOSIZ-' . strtoupper(uniqid());
                    $product->status = 'active';
                    $product->is_active = true;
                    $product->is_featured = $productData['is_featured'];
                    $product->features = $productData['features'];
                    $product->tags = $productData['tags'];
                    $product->images = []; // Will be added later
                    $product->meta_data = [
                        'created_by' => 'seeder',
                        'brand' => 'Vosiz',
                        'target_audience' => 'men',
                        'age_group' => '18-65',
                        'created_at' => now()->toISOString()
                    ];

                    $product->save();
                    $this->line("✅ Created: {$product->name} (SKU: {$product->sku})");
                    $successCount++;

                } catch (\Exception $e) {
                    $this->error("❌ Failed to create product '{$productData['name']}': " . $e->getMessage());
                    $skippedCount++;
                }
            }

            $finalCount = MongoDBProduct::count();
            
            $this->newLine();
            $this->info('=== 🎉 Seeding Complete ===');
            $this->info("✅ Products created: {$successCount}");
            $this->info("⏭️  Products skipped: {$skippedCount}");
            $this->info("📊 Total MongoDB products: {$finalCount}");
            $this->info("🔗 MongoDB connection: ✓ Working");
            
            $this->newLine();
            $this->comment('💡 Next steps:');
            $this->line('   • Add product images through admin panel');
            $this->line('   • Visit: http://127.0.0.1:8000/admin/products/manage');
            $this->line('   • Test product creation and editing');

            return 0;

        } catch (\Exception $e) {
            $this->error('💥 MongoDB connection failed: ' . $e->getMessage());
            $this->error('📁 File: ' . $e->getFile());
            $this->error('📍 Line: ' . $e->getLine());
            return 1;
        }
    }
}