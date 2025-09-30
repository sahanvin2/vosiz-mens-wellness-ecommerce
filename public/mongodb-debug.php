<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MongoDB Products Debug - Vosiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #333;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            color: #4ade80;
            font-weight: bold;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: #2d2d2d;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #444;
        }
        .product-name {
            font-size: 1.2em;
            font-weight: bold;
            color: #fbbf24;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 1.4em;
            color: #10b981;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-meta {
            font-size: 0.9em;
            color: #999;
            margin-bottom: 15px;
        }
        .product-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
        }
        .tag {
            background: #1e40af;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
        .featured {
            border-left: 4px solid #fbbf24;
        }
        .error {
            background: #dc2626;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success {
            background: #059669;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 MongoDB Products Debug Page</h1>
            <p>This page shows MongoDB products directly without authentication</p>
        </div>

        <?php
        try {
            // Include Laravel autoloader
            require_once __DIR__ . '/../vendor/autoload.php';
            
            // Bootstrap Laravel
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
            $kernel->bootstrap();

            // Get products from MongoDB
            $products = \App\Models\MongoDBProduct::all();
            $total = $products->count();
            $featured = $products->where('is_featured', true)->count();
            $active = $products->where('is_active', true)->count();
            
            echo '<div class="success">✅ MongoDB Connection: Working | Found ' . $total . ' products</div>';
            
            echo '<div class="stats">';
            echo '<div class="stat-card"><div class="stat-number">' . $total . '</div><div>Total Products</div></div>';
            echo '<div class="stat-card"><div class="stat-number">' . $featured . '</div><div>Featured Products</div></div>';
            echo '<div class="stat-card"><div class="stat-number">' . $active . '</div><div>Active Products</div></div>';
            echo '</div>';
            
            if ($total > 0) {
                echo '<h2>📦 All MongoDB Products:</h2>';
                echo '<div class="products-grid">';
                
                foreach ($products as $product) {
                    $featuredClass = $product->is_featured ? 'featured' : '';
                    echo '<div class="product-card ' . $featuredClass . '">';
                    echo '<div class="product-name">' . htmlspecialchars($product->name) . '</div>';
                    echo '<div class="product-price">$' . number_format(floatval($product->price), 2) . '</div>';
                    echo '<div class="product-meta">';
                    echo 'SKU: ' . htmlspecialchars($product->sku ?? 'N/A') . '<br>';
                    echo 'Category: ' . htmlspecialchars($product->category_name ?? 'N/A') . '<br>';
                    echo 'Stock: ' . ($product->stock_quantity ?? 0) . '<br>';
                    echo 'Status: ' . ($product->is_active ? '✅ Active' : '❌ Inactive');
                    if ($product->is_featured) echo ' | ⭐ Featured';
                    echo '</div>';
                    
                    if (isset($product->tags) && is_array($product->tags)) {
                        echo '<div class="product-tags">';
                        foreach ($product->tags as $tag) {
                            echo '<span class="tag">' . htmlspecialchars($tag) . '</span>';
                        }
                        echo '</div>';
                    }
                    
                    if (!empty($product->description)) {
                        echo '<p style="font-size:0.9em;color:#ccc;margin-top:10px;">' . 
                             htmlspecialchars(substr($product->description, 0, 150)) . 
                             (strlen($product->description) > 150 ? '...' : '') . '</p>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<div class="error">❌ No products found in MongoDB</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<h3>❌ Error connecting to MongoDB:</h3>';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 40px; padding: 20px; background: #333; border-radius: 10px;">
            <h3>🔗 Quick Links:</h3>
            <ul>
                <li><a href="/login" style="color: #fbbf24;">Login as Admin</a></li>
                <li><a href="/admin/products/manage" style="color: #fbbf24;">Admin Products (requires login)</a></li>
                <li><a href="/mongo-test" style="color: #fbbf24;">JSON API Test</a></li>
                <li><a href="/auth-check" style="color: #fbbf24;">Check Authentication</a></li>
            </ul>
            
            <h4>🔑 Admin Login Credentials:</h4>
            <p><strong>Email:</strong> admin@vosiz.com</p>
            <p><strong>Password:</strong> admin123</p>
        </div>
    </div>
</body>
</html>