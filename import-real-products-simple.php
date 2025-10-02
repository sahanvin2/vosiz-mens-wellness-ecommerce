<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "🗑️ Clearing existing placeholder products from MongoDB...\n\n";

try {
    $client = new Client($_ENV['MONGODB_DSN']);
    $database = $client->selectDatabase('sahannawarathne2004_db_user');
    $collection = $database->selectCollection('products');
    
    // Clear existing products
    $deleteResult = $collection->deleteMany([]);
    echo "✅ Deleted {$deleteResult->getDeletedCount()} existing products\n\n";
    
    // Define real products directly in PHP
    $products = [
        [
            'name' => 'HIMS The Shampoo - Hair Loss Prevention',
            'slug' => 'hims-shampoo-hair-loss-prevention',
            'description' => 'Clinically formulated shampoo with ketoconazole and saw palmetto to help prevent hair loss and promote healthy hair growth. This gentle daily-use formula cleanses while targeting DHT production at the scalp level.',
            'short_description' => 'Clinical-grade shampoo for hair loss prevention with ketoconazole',
            'price' => 29.99,
            'sale_price' => 24.99,
            'discount_percentage' => 17,
            'sku' => 'HIMS-SHAMP-001',
            'category_id' => 3,
            'category_name' => 'Hair Care',
            'images' => ['/images/products/hims-shampoo.jpg'],
            'features' => ['DHT blocking formula', 'Ketoconazole 1%', 'Saw palmetto extract', 'Gentle daily use', 'Clinically tested'],
            'ingredients' => ['Ketoconazole', 'Saw Palmetto Extract', 'Biotin', 'Caffeine', 'Zinc Pyrithione'],
            'usage_instructions' => 'Apply to wet hair, massage into scalp for 2 minutes, rinse thoroughly. Use daily for best results.',
            'tags' => ['hair loss', 'shampoo', 'DHT blocker', 'clinical', 'HIMS'],
            'status' => 'active',
            'is_featured' => true,
            'stock_quantity' => 50,
            'weight' => 8.0,
            'rating_average' => 4.6,
            'rating_count' => 124,
            'views_count' => 0,
            'sales_count' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ],
        [
            'name' => 'ManSkin Advanced Face Moisturizer',
            'slug' => 'manskin-advanced-face-moisturizer',
            'description' => 'Lightweight, fast-absorbing moisturizer specifically formulated for men\'s skin. Contains hyaluronic acid, vitamin E, and green tea extract to hydrate and protect against environmental damage.',
            'short_description' => 'Advanced moisturizer for men with hyaluronic acid and vitamin E',
            'price' => 34.99,
            'sale_price' => 29.99,
            'discount_percentage' => 14,
            'sku' => 'MSKN-MOIST-001',
            'category_id' => 1,
            'category_name' => 'Skincare',
            'images' => ['/images/products/manskin-moisturizer.jpg'],
            'features' => ['Fast-absorbing formula', 'Hyaluronic acid hydration', 'Vitamin E protection', 'Non-greasy finish', 'For all skin types'],
            'ingredients' => ['Hyaluronic Acid', 'Vitamin E', 'Green Tea Extract', 'Aloe Vera', 'Shea Butter'],
            'usage_instructions' => 'Apply to clean face and neck morning and evening. Massage gently until absorbed.',
            'tags' => ['moisturizer', 'skincare', 'men', 'hyaluronic acid', 'vitamin E'],
            'status' => 'active',
            'is_featured' => true,
            'stock_quantity' => 35,
            'weight' => 3.5,
            'rating_average' => 4.8,
            'rating_count' => 89,
            'views_count' => 0,
            'sales_count' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ],
        [
            'name' => 'MARLOWE Complete Grooming Set',
            'slug' => 'marlowe-complete-grooming-set',
            'description' => 'Complete grooming essentials in one premium set. Includes face wash, moisturizer, deodorant, and body wash. All products feature MARLOWE\'s signature sandalwood and eucalyptus scent.',
            'short_description' => '4-piece grooming essentials set with signature scent',
            'price' => 79.99,
            'sale_price' => 69.99,
            'discount_percentage' => 13,
            'sku' => 'MARL-GROOM-SET-001',
            'category_id' => 4,
            'category_name' => 'Body Care',
            'images' => ['/images/products/marlowe-grooming-set.jpg'],
            'features' => ['4-piece complete set', 'Signature sandalwood scent', 'Premium packaging', 'Travel-friendly sizes', 'All-natural ingredients'],
            'ingredients' => ['Sandalwood Oil', 'Eucalyptus Extract', 'Coconut Oil', 'Shea Butter', 'Vitamin E'],
            'usage_instructions' => 'Use products as part of daily grooming routine. Start with face wash, apply moisturizer, use deodorant and body wash as needed.',
            'tags' => ['grooming set', 'marlowe', 'sandalwood', 'complete care', 'premium'],
            'status' => 'active',
            'is_featured' => true,
            'stock_quantity' => 25,
            'weight' => 16.0,
            'rating_average' => 4.9,
            'rating_count' => 56,
            'views_count' => 0,
            'sales_count' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ],
        [
            'name' => 'Premium Beard Oil - Cedar & Sage',
            'slug' => 'premium-beard-oil-cedar-sage',
            'description' => 'Handcrafted beard oil with a blend of jojoba, argan, and sweet almond oils. Infused with cedar and sage essential oils for a masculine, woodsy scent. Softens beard hair and moisturizes skin underneath.',
            'short_description' => 'Handcrafted beard oil with cedar and sage essential oils',
            'price' => 24.99,
            'sale_price' => 19.99,
            'discount_percentage' => 20,
            'sku' => 'BEARD-OIL-CS-001',
            'category_id' => 2,
            'category_name' => 'Beard Care',
            'images' => ['/images/products/premium-beard-oil.jpg'],
            'features' => ['Handcrafted formula', 'Jojoba and argan oils', 'Cedar & sage scent', 'Softens and conditions', '1oz dropper bottle'],
            'ingredients' => ['Jojoba Oil', 'Argan Oil', 'Sweet Almond Oil', 'Cedar Essential Oil', 'Sage Essential Oil'],
            'usage_instructions' => 'Apply 3-5 drops to palm, rub hands together, and massage into beard and skin. Use daily for best results.',
            'tags' => ['beard oil', 'cedar', 'sage', 'handcrafted', 'essential oils'],
            'status' => 'active',
            'is_featured' => false,
            'stock_quantity' => 40,
            'weight' => 1.0,
            'rating_average' => 4.7,
            'rating_count' => 92,
            'views_count' => 0,
            'sales_count' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ],
        [
            'name' => 'Professional Beard Trimmer Kit',
            'slug' => 'professional-beard-trimmer-kit',
            'description' => 'Professional-grade beard trimmer with 20 length settings and titanium-coated blades. Includes multiple attachments, cleaning brush, and premium storage case. Cordless with 90-minute runtime.',
            'short_description' => 'Professional beard trimmer with 20 settings and titanium blades',
            'price' => 129.99,
            'sale_price' => 99.99,
            'discount_percentage' => 23,
            'sku' => 'TRIM-PRO-001',
            'category_id' => 5,
            'category_name' => 'Grooming Tools',
            'images' => ['/images/products/beard-trimmer-kit.jpg'],
            'features' => ['20 length settings', 'Titanium-coated blades', '90-minute cordless runtime', 'Multiple attachments', 'Premium storage case'],
            'ingredients' => [],
            'usage_instructions' => 'Charge fully before first use. Select desired length setting and trim in direction of hair growth. Clean after each use with included brush.',
            'tags' => ['beard trimmer', 'professional', 'titanium', 'cordless', 'grooming tools'],
            'status' => 'active',
            'is_featured' => true,
            'stock_quantity' => 15,
            'weight' => 12.0,
            'rating_average' => 4.5,
            'rating_count' => 73,
            'views_count' => 0,
            'sales_count' => 0,
            'created_at' => new DateTime(),
            'updated_at' => new DateTime()
        ]
    ];
    
    $productCount = count($products);
    echo "📦 Importing {$productCount} new products...\n\n";
    
    foreach ($products as $product) {
        try {
            $collection->insertOne($product);
            echo "✅ Imported: {$product['name']}\n";
            echo "   💰 Price: $" . number_format($product['price'], 2) . "\n";
            echo "   📂 Category: {$product['category_name']}\n";
            echo "   🖼️ Image: {$product['images'][0]}\n\n";
        } catch (Exception $e) {
            echo "❌ Failed to import {$product['name']}: " . $e->getMessage() . "\n\n";
        }
    }
    
    $finalCount = $collection->countDocuments();
    echo "🎉 Import completed! Total products in database: {$finalCount}\n\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "📋 NEXT STEPS:\n";
echo "==============\n";
echo "1. Add real product images to: /public/images/products/\n";
echo "2. Name your images exactly as referenced:\n";
echo "   - hims-shampoo.jpg\n";
echo "   - manskin-moisturizer.jpg\n";
echo "   - marlowe-grooming-set.jpg\n";
echo "   - premium-beard-oil.jpg\n";
echo "   - beard-trimmer-kit.jpg\n\n";
echo "3. Test your website: http://localhost:8000/products\n";
echo "4. Use admin panel to add more products: http://localhost:8000/admin/products\n\n";
echo "✨ Your real product database is now ready!\n";
?>