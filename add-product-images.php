<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🛍️ Adding Your Product Images to MongoDB Database...\n\n";

use App\Models\MongoDBProduct;
use App\Models\Category;

// Get category IDs
$categories = Category::all()->keyBy('name');

// Create placeholder image URLs (since I can't directly save your images)
// In a real scenario, you would upload these images to your storage
$imageProducts = [
    [
        'name' => 'HIMS Premium Skincare Serum',
        'slug' => 'hims-premium-skincare-serum',
        'description' => 'Advanced anti-aging serum from HIMS featuring vitamin C and retinol. Specifically formulated for men\'s skin to reduce fine lines, improve texture, and provide deep hydration for a youthful appearance.',
        'short_description' => 'Premium anti-aging serum with vitamin C and retinol',
        'price' => 39.99,
        'sale_price' => 34.99,
        'discount_percentage' => 13,
        'sku' => 'HIMS-SERUM-001',
        'category_id' => $categories['Skincare']->id,
        'category_name' => 'Skincare',
        'images' => ['/images/products/hims-serum-collection.jpg'], // Image 1
        'features' => ['Vitamin C', 'Retinol Formula', 'Anti-Aging', 'Deep Hydration', 'Men\'s Formula'],
        'ingredients' => ['Vitamin C', 'Retinol', 'Hyaluronic Acid', 'Niacinamide', 'Peptides'],
        'usage_instructions' => 'Apply 2-3 drops to clean face morning and evening. Start with every other night for retinol tolerance.',
        'tags' => ['hims', 'serum', 'anti-aging', 'vitamin-c', 'retinol', 'men'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 85,
        'weight' => 30,
        'rating_average' => 4.8,
        'rating_count' => 42,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'ManSkin Everyday Face Care Kit',
        'slug' => 'manskin-everyday-face-care-kit',
        'description' => 'Complete daily skincare routine for men featuring cleanser, moisturizer, and treatment products. This comprehensive kit provides everything needed for healthy, clear skin with a masculine approach to skincare.',
        'short_description' => 'Complete daily skincare routine kit for men',
        'price' => 59.99,
        'sale_price' => 49.99,
        'discount_percentage' => 17,
        'sku' => 'MANSKIN-KIT-002',
        'category_id' => $categories['Skincare']->id,
        'category_name' => 'Skincare',
        'images' => ['/images/products/manskin-face-kit.jpg'], // Image 2
        'features' => ['Complete Kit', '4-Step Routine', 'Natural Ingredients', 'Daily Use', 'Travel Friendly'],
        'ingredients' => ['Salicylic Acid', 'Aloe Vera', 'Tea Tree Oil', 'Vitamin E', 'Natural Extracts'],
        'usage_instructions' => 'Follow the 4-step routine: cleanse, tone, treat, moisturize. Use morning and evening.',
        'tags' => ['manskin', 'skincare-kit', 'daily-routine', 'men', 'complete-set'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 65,
        'weight' => 400,
        'rating_average' => 4.7,
        'rating_count' => 28,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'MARLOWE Complete Grooming Collection',
        'slug' => 'marlowe-complete-grooming-collection',
        'description' => 'Premium men\'s grooming collection featuring body wash, face lotion with SPF, facial scrub, gentle foaming cleanser, and exfoliating soap bar. Everything a modern gentleman needs for complete body and face care.',
        'short_description' => 'Complete premium grooming collection for men',
        'price' => 89.99,
        'sale_price' => 74.99,
        'discount_percentage' => 17,
        'sku' => 'MARLOWE-COLLECTION-003',
        'category_id' => $categories['Body Care']->id,
        'category_name' => 'Body Care',
        'images' => ['/images/products/marlowe-grooming-collection.jpg'], // Image 3
        'features' => ['Complete Collection', 'SPF Protection', 'Exfoliating', 'Natural Ingredients', 'Premium Quality'],
        'ingredients' => ['Natural Botanicals', 'SPF 50', 'Exfoliating Beads', 'Moisturizing Agents', 'Essential Oils'],
        'usage_instructions' => 'Use each product as directed. Body wash for daily cleansing, face products for morning and evening routine.',
        'tags' => ['marlowe', 'grooming-collection', 'body-care', 'spf', 'complete-set', 'men'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 45,
        'weight' => 800,
        'rating_average' => 4.9,
        'rating_count' => 35,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'Executive Cologne & Fragrance',
        'slug' => 'executive-cologne-fragrance',
        'description' => 'Sophisticated masculine fragrance perfect for the modern professional. Features a balanced blend of fresh citrus top notes, woody middle notes, and warm base notes for all-day confidence.',
        'short_description' => 'Premium masculine cologne for professionals',
        'price' => 67.99,
        'sale_price' => 57.99,
        'discount_percentage' => 15,
        'sku' => 'EXECUTIVE-COLOGNE-004',
        'category_id' => $categories['Body Care']->id,
        'category_name' => 'Body Care',
        'images' => ['/images/products/executive-cologne.jpg'], // Image 4
        'features' => ['Long Lasting', 'Professional Scent', 'Premium Quality', 'Day & Night', 'Sophisticated'],
        'ingredients' => ['Citrus Top Notes', 'Woody Middle Notes', 'Warm Base Notes', 'Premium Alcohol', 'Essential Oils'],
        'usage_instructions' => 'Spray 2-3 times on pulse points (wrists, neck, chest). Best applied to clean, dry skin.',
        'tags' => ['cologne', 'fragrance', 'professional', 'executive', 'men', 'sophisticated'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 70,
        'weight' => 150,
        'rating_average' => 4.6,
        'rating_count' => 31,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'Gentleman\'s Premium Cologne',
        'slug' => 'gentlemans-premium-cologne',
        'description' => 'Luxury cologne crafted for the distinguished gentleman. This signature scent combines traditional elegance with modern appeal, featuring notes that evolve throughout the day for a lasting impression.',
        'short_description' => 'Luxury signature cologne for distinguished gentlemen',
        'price' => 54.99,
        'sale_price' => 46.99,
        'discount_percentage' => 15,
        'sku' => 'GENTLEMAN-COLOGNE-005',
        'category_id' => $categories['Body Care']->id,
        'category_name' => 'Body Care',
        'images' => ['/images/products/gentlemans-cologne.jpg'], // Image 5
        'features' => ['Signature Scent', 'All-Day Wear', 'Premium Ingredients', 'Elegant Bottle', 'Gift Ready'],
        'ingredients' => ['Bergamot', 'Lavender', 'Sandalwood', 'Vanilla', 'Musk'],
        'usage_instructions' => 'Apply to pulse points after showering. Allow to dry naturally for best scent development.',
        'tags' => ['cologne', 'luxury', 'gentleman', 'signature-scent', 'premium', 'men'],
        'is_active' => true,
        'is_featured' => false,
        'stock_quantity' => 55,
        'weight' => 120,
        'rating_average' => 4.7,
        'rating_count' => 24,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'Professional Hair Styling Collection',
        'slug' => 'professional-hair-styling-collection',
        'description' => 'Complete hair styling collection featuring pomades, waxes, and gels in various holds and finishes. Perfect for creating any hairstyle from classic to contemporary with professional-grade results.',
        'short_description' => 'Complete professional hair styling product collection',
        'price' => 79.99,
        'sale_price' => 67.99,
        'discount_percentage' => 15,
        'sku' => 'HAIR-STYLING-006',
        'category_id' => $categories['Hair Care']->id,
        'category_name' => 'Hair Care',
        'images' => ['/images/products/hair-styling-collection.jpg'], // Image 6
        'features' => ['Multiple Hold Strengths', 'Various Finishes', 'Professional Grade', 'All Hair Types', 'Long Lasting'],
        'ingredients' => ['Natural Waxes', 'Conditioning Agents', 'Hold Polymers', 'Shine Enhancers', 'Nourishing Oils'],
        'usage_instructions' => 'Choose appropriate product for desired style. Apply to damp or dry hair, style as desired.',
        'tags' => ['hair-styling', 'pomade', 'wax', 'gel', 'professional', 'men', 'collection'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 40,
        'weight' => 600,
        'rating_average' => 4.8,
        'rating_count' => 37,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'Artisan Beard Care Essentials',
        'slug' => 'artisan-beard-care-essentials',
        'description' => 'Handcrafted beard care collection featuring premium beard oil, conditioning balm, and specialized shampoo. Made with natural ingredients for the discerning bearded gentleman.',
        'short_description' => 'Handcrafted premium beard care collection',
        'price' => 69.99,
        'sale_price' => 59.99,
        'discount_percentage' => 14,
        'sku' => 'ARTISAN-BEARD-007',
        'category_id' => $categories['Beard Care']->id,
        'category_name' => 'Beard Care',
        'images' => ['/images/products/artisan-beard-care.jpg'], // Image 7
        'features' => ['Handcrafted', 'Natural Ingredients', 'Complete Care', 'Premium Quality', 'Masculine Scents'],
        'ingredients' => ['Organic Oils', 'Natural Waxes', 'Essential Oils', 'Vitamin E', 'Botanical Extracts'],
        'usage_instructions' => 'Use beard shampoo 2-3 times weekly, apply oil daily, use balm for styling and extra conditioning.',
        'tags' => ['beard-care', 'artisan', 'natural', 'handcrafted', 'premium', 'men'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 60,
        'weight' => 350,
        'rating_average' => 4.9,
        'rating_count' => 29,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'Professional Shaving Kit',
        'slug' => 'professional-shaving-kit',
        'description' => 'Traditional wet shaving kit featuring high-quality razor, shaving brush, and premium shaving cream. Experience the art of traditional shaving with modern precision and comfort.',
        'short_description' => 'Traditional wet shaving kit with premium tools',
        'price' => 94.99,
        'sale_price' => 79.99,
        'discount_percentage' => 16,
        'sku' => 'SHAVING-KIT-008',
        'category_id' => $categories['Grooming Tools']->id,
        'category_name' => 'Grooming Tools',
        'images' => ['/images/products/professional-shaving-kit.jpg'], // Image 8
        'features' => ['Traditional Wet Shaving', 'Premium Tools', 'Close Shave', 'Comfortable Grip', 'Professional Quality'],
        'specifications' => ['Stainless Steel Razor', 'Badger Hair Brush', 'Shaving Cream 100ml', 'Wooden Stand'],
        'usage_instructions' => 'Prepare skin with warm water, apply cream with brush, shave with gentle strokes, rinse and moisturize.',
        'tags' => ['shaving-kit', 'traditional', 'wet-shaving', 'professional', 'tools', 'men'],
        'is_active' => true,
        'is_featured' => true,
        'stock_quantity' => 35,
        'weight' => 450,
        'rating_average' => 4.8,
        'rating_count' => 22,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'Advanced Men\'s Skincare System',
        'slug' => 'advanced-mens-skincare-system',
        'description' => 'Professional-grade skincare system with moisturizing toner, oil control moisturizer, and lip balm. Scientifically formulated for men\'s skin needs with advanced ingredients for optimal results.',
        'short_description' => 'Professional skincare system for men',
        'price' => 64.99,
        'sale_price' => 54.99,
        'discount_percentage' => 15,
        'sku' => 'SKINCARE-SYSTEM-009',
        'category_id' => $categories['Skincare']->id,
        'category_name' => 'Skincare',
        'images' => ['/images/products/advanced-skincare-system.jpg'], // Image 9
        'features' => ['3-Step System', 'Oil Control', 'Advanced Formula', 'Men\'s Specific', 'Professional Grade'],
        'ingredients' => ['Hyaluronic Acid', 'Niacinamide', 'Salicylic Acid', 'Vitamin E', 'Natural Moisturizers'],
        'usage_instructions' => 'Use toner after cleansing, follow with moisturizer, apply lip balm as needed throughout the day.',
        'tags' => ['skincare-system', 'advanced', 'professional', 'oil-control', 'men', 'complete-care'],
        'is_active' => true,
        'is_featured' => false,
        'stock_quantity' => 75,
        'weight' => 280,
        'rating_average' => 4.6,
        'rating_count' => 33,
        'views_count' => 0,
        'sales_count' => 0,
        'created_at' => now(),
        'updated_at' => now()
    ]
];

$created = 0;
$failed = 0;

foreach ($imageProducts as $productData) {
    try {
        $product = MongoDBProduct::create($productData);
        echo "✅ Created: {$product->name}\n";
        echo "   💰 Price: \${$product->price} (Sale: \${$product->sale_price})\n";
        echo "   📁 Category: {$product->category_name}\n";
        echo "   🖼️ Image: {$product->images[0]}\n\n";
        $created++;
    } catch (Exception $e) {
        echo "❌ Failed to create {$productData['name']}: " . $e->getMessage() . "\n\n";
        $failed++;
    }
}

echo "\n📊 Summary:\n";
echo "✅ Products created: {$created}\n";
echo "❌ Failed: {$failed}\n";
echo "📦 Total products in database: " . MongoDBProduct::count() . "\n";

// Display products by category with counts
echo "\n📁 Products by Category:\n";
foreach ($categories as $category) {
    $count = MongoDBProduct::where('category_name', $category->name)->count();
    echo "- {$category->name}: {$count} products\n";
}

echo "\n🎉 Product images added successfully!\n";
echo "🔗 View products: http://localhost:8000/products\n";
echo "👤 Admin panel: http://localhost:8000/admin/products\n\n";

echo "📝 Note: The image paths are set up. To see actual images:\n";
echo "1. Place your product images in: public/images/products/\n";
echo "2. Name them according to the paths in the database\n";
echo "3. Or update the image paths to match your uploaded files\n";