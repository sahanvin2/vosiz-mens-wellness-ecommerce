<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔁 Sync MongoDB products -> MySQL products\n";
echo "====================================\n";

use App\Models\MongoDBProduct;
use App\Models\Product;
use App\Models\Category;


$mongoCount = 0;
$synced = 0;

try {
    $mongoProducts = MongoDBProduct::all();
    $mongoCount = $mongoProducts->count();

    // Ensure there's a fallback 'Uncategorized' category in MySQL
    $defaultCategory = Category::firstOrCreate(
        ['slug' => 'uncategorized'],
        ['name' => 'Uncategorized']
    );

    foreach ($mongoProducts as $m) {
        // Resolve category_id from category_name when possible
        $categoryId = null;
        if (!empty($m->category_name)) {
            $cat = Category::where('name', $m->category_name)->first();
            if ($cat) {
                $categoryId = $cat->id;
            }
        }

        if (is_null($categoryId)) {
            // fall back to default category id
            $categoryId = $defaultCategory->id;
        }

        $data = [
            'name' => $m->name ?? 'Untitled',
            'slug' => $m->slug ?? (strtolower(str_replace(' ', '-', $m->name ?? ''))),
            'description' => $m->description ?? null,
            'short_description' => $m->short_description ?? null,
            'price' => isset($m->price) ? (float)$m->price : 0,
            'compare_price' => isset($m->compare_price) ? (float)$m->compare_price : null,
            'stock_quantity' => isset($m->stock_quantity) ? (int)$m->stock_quantity : 0,
            'sku' => $m->sku ?? null,
            'images' => isset($m->images) ? $m->images : [],
            'ingredients' => isset($m->ingredients) ? $m->ingredients : [],
            'benefits' => isset($m->benefits) ? $m->benefits : [],
            'is_featured' => isset($m->is_featured) ? (bool)$m->is_featured : false,
            'is_active' => isset($m->is_active) ? (bool)$m->is_active : (isset($m->status) && $m->status === 'active' ? true : false),
            'weight' => isset($m->weight) ? (float)$m->weight : 0,
            'category_id' => $categoryId,
        ];

        // Upsert by SKU when available, otherwise by slug
        $match = [];
        if (!empty($m->sku)) {
            $match['sku'] = $m->sku;
        } else {
            $match['slug'] = $m->slug ?? $data['slug'];
        }

        $product = Product::updateOrCreate($match, $data);
        $synced++;
        echo "Synced: " . ($m->sku ?? $product->id) . "\n";
    }

    echo "\nDone. MongoDB products found: {$mongoCount}. Synced to MySQL products: {$synced}.\n";
} catch (Exception $e) {
    echo "Error during sync: " . $e->getMessage() . "\n";
}

echo "\nTip: You can inspect MySQL products table or use phpMyAdmin to view rows.\n";
