<?php

// Simple script to populate MongoDB with sample products
// Run this script: php scripts/populate-mongodb.php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\MongoDBProduct;

// Load Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Starting MongoDB population...\n";

// Clear existing products
try {
    MongoDBProduct::truncate();
    echo "✅ Cleared existing products\n";
} catch (Exception $e) {
    echo "⚠️  Could not clear products: " . $e->getMessage() . "\n";
}

// Sample products data
$products = [
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
        'brand' => 'Vosiz',
        'images' => ['/storage/images/products/beard-oil-1.jpg', '/storage/images/products/beard-oil-2.jpg'],
        'ingredients' => ['Jojoba Oil', 'Argan Oil', 'Sweet Almond Oil', 'Vitamin E', 'Essential Oils'],
        'benefits' => ['Promotes Growth', 'Reduces Itching', 'Adds Shine', 'Softens Hair'],
        'how_to_use' => 'Apply 3-5 drops to palm, rub hands together, and massage into beard and skin.',
        'tags' => ['natural', 'organic', 'beard', 'grooming', 'men'],
        'is_active' => true,
        'is_featured' => true,
        'sort_order' => 1,
        'created_at' => now(),
        'updated_at' => now()
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
        'brand' => 'Vosiz',
        'images' => ['/storage/images/products/charcoal-wash-1.jpg', '/storage/images/products/charcoal-wash-2.jpg'],
        'ingredients' => ['Activated Charcoal', 'Glycolic Acid', 'Tea Tree Oil', 'Aloe Vera'],
        'benefits' => ['Deep Cleansing', 'Oil Control', 'Pore Minimizing', 'Anti-Bacterial'],
        'how_to_use' => 'Wet face, apply small amount, massage gently, rinse thoroughly with water.',
        'tags' => ['charcoal', 'cleansing', 'skincare', 'men', 'deep-clean'],
        'is_active' => true,
        'is_featured' => true,
        'sort_order' => 2,
        'created_at' => now(),
        'updated_at' => now()
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
        'brand' => 'Vosiz',
        'images' => ['/storage/images/products/pomade-1.jpg', '/storage/images/products/pomade-2.jpg'],
        'ingredients' => ['Beeswax', 'Shea Butter', 'Coconut Oil', 'Lanolin', 'Castor Oil'],
        'benefits' => ['Strong Hold', 'Moisture Protection', 'Natural Shine', 'Easy Application'],
        'how_to_use' => 'Work small amount between hands, apply to damp or dry hair, style as desired.',
        'tags' => ['pomade', 'hair', 'styling', 'hold', 'moisture'],
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 3,
        'created_at' => now(),
        'updated_at' => now()
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
        'brand' => 'Vosiz',
        'images' => ['/storage/images/products/eye-cream-1.jpg'],
        'ingredients' => ['Retinol', 'Hyaluronic Acid', 'Peptides', 'Caffeine', 'Vitamin C'],
        'benefits' => ['Reduces Dark Circles', 'Minimizes Puffiness', 'Anti-Aging', 'Hydrating'],
        'how_to_use' => 'Gently pat small amount around eye area morning and evening.',
        'tags' => ['eye-cream', 'anti-aging', 'skincare', 'men', 'dark-circles'],
        'is_active' => true,
        'is_featured' => true,
        'sort_order' => 4,
        'created_at' => now(),
        'updated_at' => now()
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
        'brand' => 'Vosiz',
        'images' => ['/storage/images/products/deodorant-1.jpg'],
        'ingredients' => ['Coconut Oil', 'Shea Butter', 'Arrowroot Powder', 'Baking Soda', 'Essential Oils'],
        'benefits' => ['Aluminum-Free', 'Long-Lasting', 'Natural Ingredients', 'Sensitive Skin Friendly'],
        'how_to_use' => 'Apply to clean, dry underarms. Allow to absorb before dressing.',
        'tags' => ['natural', 'deodorant', 'aluminum-free', 'organic', 'sensitive-skin'],
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 5,
        'created_at' => now(),
        'updated_at' => now()
    ]
];

// Create products
$created = 0;
foreach ($products as $productData) {
    try {
        MongoDBProduct::create($productData);
        $created++;
        echo "✅ Created: " . $productData['name'] . "\n";
    } catch (Exception $e) {
        echo "❌ Failed to create: " . $productData['name'] . " - " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 Database population complete!\n";
echo "📊 Created {$created} products\n";
echo "📦 Total products in database: " . MongoDBProduct::count() . "\n";

// Display some statistics
try {
    $featured = MongoDBProduct::where('is_featured', true)->count();
    $active = MongoDBProduct::where('is_active', true)->count();
    $categories = MongoDBProduct::distinct('category')->count();
    
    echo "⭐ Featured products: {$featured}\n";
    echo "✅ Active products: {$active}\n";
    echo "📁 Categories: {$categories}\n";
} catch (Exception $e) {
    echo "⚠️  Could not get statistics: " . $e->getMessage() . "\n";
}

echo "\n🔗 You can now access:\n";
echo "   👤 Admin Panel: http://localhost:8000/admin/products\n";
echo "   🛍️  Shop: http://localhost:8000/products\n";
echo "   🏠 Homepage: http://localhost:8000/\n";
echo "\n✅ Setup complete!\n";