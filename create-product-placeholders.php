<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🖼️ Creating Product Image Placeholders and Testing Display...\n\n";

use App\Models\MongoDBProduct;

// Create a simple CSS/HTML placeholder for each product type
$placeholderTemplates = [
    'skincare' => [
        'icon' => '🧴',
        'color' => '#3B82F6', // Blue
        'bg' => 'linear-gradient(135deg, #3B82F6, #1D4ED8)'
    ],
    'beard' => [
        'icon' => '🧔',
        'color' => '#F59E0B', // Amber
        'bg' => 'linear-gradient(135deg, #F59E0B, #D97706)'
    ],
    'hair' => [
        'icon' => '💇‍♂️',
        'color' => '#8B5CF6', // Purple
        'bg' => 'linear-gradient(135deg, #8B5CF6, #7C3AED)'
    ],
    'body' => [
        'icon' => '🚿',
        'color' => '#10B981', // Green
        'bg' => 'linear-gradient(135deg, #10B981, #059669)'
    ],
    'tools' => [
        'icon' => '🔧',
        'color' => '#EF4444', // Red
        'bg' => 'linear-gradient(135deg, #EF4444, #DC2626)'
    ]
];

function createPlaceholderSVG($productName, $category, $template) {
    $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:' . $template['color'] . ';stop-opacity:1" />
      <stop offset="100%" style="stop-color:#1F2937;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="400" height="400" fill="url(#grad1)" rx="20"/>
  <text x="200" y="160" font-family="Arial, sans-serif" font-size="60" text-anchor="middle" fill="white">' . $template['icon'] . '</text>
  <text x="200" y="220" font-family="Arial, sans-serif" font-size="18" font-weight="bold" text-anchor="middle" fill="#FFD700">' . htmlspecialchars(substr($productName, 0, 20)) . '</text>
  <text x="200" y="250" font-family="Arial, sans-serif" font-size="14" text-anchor="middle" fill="#9CA3AF">' . ucfirst($category) . ' Product</text>
  <text x="200" y="320" font-family="Arial, sans-serif" font-size="12" text-anchor="middle" fill="#6B7280">VOSIZ PREMIUM</text>
</svg>';
    return $svg;
}

// Get all products and create placeholders
$products = MongoDBProduct::all();
$updated = 0;

foreach ($products as $product) {
    $categoryKey = 'skincare'; // default
    
    if (str_contains(strtolower($product->category_name), 'beard')) {
        $categoryKey = 'beard';
    } elseif (str_contains(strtolower($product->category_name), 'hair')) {
        $categoryKey = 'hair';
    } elseif (str_contains(strtolower($product->category_name), 'body')) {
        $categoryKey = 'body';
    } elseif (str_contains(strtolower($product->category_name), 'tool')) {
        $categoryKey = 'tools';
    }
    
    $template = $placeholderTemplates[$categoryKey];
    
    // Create SVG placeholder
    $svg = createPlaceholderSVG($product->name, $product->category_name, $template);
    $filename = 'product-' . $product->_id . '.svg';
    $filepath = public_path('images/products/' . $filename);
    
    file_put_contents($filepath, $svg);
    
    // Update product with new image path
    $product->update([
        'images' => ['/images/products/' . $filename]
    ]);
    
    echo "✅ Created placeholder for: {$product->name}\n";
    echo "   📁 Category: {$product->category_name} ({$categoryKey})\n";
    echo "   🖼️ Image: /images/products/{$filename}\n\n";
    
    $updated++;
}

echo "📊 Summary:\n";
echo "✅ Placeholders created: {$updated}\n";
echo "📦 Total products: " . MongoDBProduct::count() . "\n\n";

echo "🎨 Image Categories Created:\n";
foreach ($placeholderTemplates as $key => $template) {
    echo "- " . ucfirst($key) . ": {$template['icon']} ({$template['color']})\n";
}

echo "\n🔗 Test your products:\n";
echo "🛍️ Products page: http://localhost:8000/products\n";
echo "👤 Admin panel: http://localhost:8000/admin/products\n\n";

echo "✅ All product images are now ready for display!\n";