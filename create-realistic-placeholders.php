<?php

$products = [
    'hims-shampoo.jpg' => [
        'title' => 'HIMS Shampoo',
        'price' => '$29.99',
        'emoji' => '🧴',
        'color' => '#3B82F6'
    ],
    'manskin-moisturizer.jpg' => [
        'title' => 'ManSkin Moisturizer',
        'price' => '$34.99',
        'emoji' => '🧴',
        'color' => '#10B981'
    ],
    'marlowe-grooming-set.jpg' => [
        'title' => 'MARLOWE Set',
        'price' => '$79.99',
        'emoji' => '🎁',
        'color' => '#8B5CF6'
    ],
    'premium-beard-oil.jpg' => [
        'title' => 'Beard Oil',
        'price' => '$24.99',
        'emoji' => '🧔',
        'color' => '#F59E0B'
    ],
    'beard-trimmer-kit.jpg' => [
        'title' => 'Trimmer Kit',
        'price' => '$129.99',
        'emoji' => '🔧',
        'color' => '#EF4444'
    ]
];

$imageDir = __DIR__ . '/public/images/products';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0755, true);
}

echo "🎨 Creating realistic product placeholders...\n\n";

foreach ($products as $filename => $product) {
    $svg = <<<SVG
<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad-{$filename}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$product['color']};stop-opacity:1" />
      <stop offset="100%" style="stop-color:#1F2937;stop-opacity:1" />
    </linearGradient>
  </defs>
  
  <!-- Background -->
  <rect width="400" height="400" fill="url(#grad-{$filename})"/>
  
  <!-- Product Icon -->
  <text x="200" y="180" font-family="Arial, sans-serif" font-size="80" text-anchor="middle" fill="white" opacity="0.9">
    {$product['emoji']}
  </text>
  
  <!-- Product Title -->
  <text x="200" y="240" font-family="Arial, sans-serif" font-size="20" font-weight="bold" text-anchor="middle" fill="white">
    {$product['title']}
  </text>
  
  <!-- Price -->
  <text x="200" y="270" font-family="Arial, sans-serif" font-size="18" text-anchor="middle" fill="#FEF3C7">
    {$product['price']}
  </text>
  
  <!-- Brand Label -->
  <rect x="20" y="20" width="80" height="25" fill="rgba(255,255,255,0.2)" rx="5"/>
  <text x="60" y="37" font-family="Arial, sans-serif" font-size="12" font-weight="bold" text-anchor="middle" fill="white">
    VOSIZ
  </text>
  
  <!-- New Badge -->
  <rect x="300" y="20" width="60" height="25" fill="#10B981" rx="5"/>
  <text x="330" y="37" font-family="Arial, sans-serif" font-size="12" font-weight="bold" text-anchor="middle" fill="white">
    NEW
  </text>
</svg>
SVG;

    $imagePath = $imageDir . '/' . $filename;
    file_put_contents($imagePath, $svg);
    
    echo "✅ Created: {$filename}\n";
    echo "   📦 Product: {$product['title']}\n";
    echo "   💰 Price: {$product['price']}\n";
    echo "   🎨 Color: {$product['color']}\n\n";
}

echo "🎉 Product placeholders created successfully!\n";
echo "🔄 Replace these files with your real product images when ready.\n";
echo "📍 Location: /public/images/products/\n\n";
echo "🌐 Test your products: http://localhost:8000/products\n";
?>