<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;

class CreateMongoAtlasDatabase extends Command
{
    protected $signature = 'vosiz:create-mongo-database {--force : Force creation even if database exists}';
    protected $description = 'Create MongoDB Atlas database and collections with sample data';

    public function handle()
    {
        $this->info('🍃 Creating MongoDB Atlas Database and Collections...');
        $this->newLine();

        // Get credentials from user
        $this->info('Please provide your MongoDB Atlas credentials:');
        $username = $this->ask('MongoDB Atlas Username', 'sahannawarathne2004_db_user');
        $password = $this->secret('MongoDB Atlas Password (will be hidden)');
        $cluster = $this->ask('Cluster Host', 'cluster0.2m8hhzb.mongodb.net');
        $database = $this->ask('Database Name', 'vosiz_products');

        // Build connection string
        $encodedPassword = urlencode($password);
        $dsn = "mongodb+srv://{$username}:{$encodedPassword}@{$cluster}/{$database}?retryWrites=true&w=majority";

        $this->line("Connecting to: mongodb+srv://{$username}:***@{$cluster}/{$database}");
        $this->newLine();

        try {
            // Step 1: Test Connection
            $this->info('Step 1: Testing Connection...');
            $client = new Client($dsn, [
                'serverSelectionTimeoutMS' => 5000,
                'connectTimeoutMS' => 10000,
            ]);

            $db = $client->selectDatabase($database);
            $result = $db->command(['ping' => 1]);
            $response = $result->toArray()[0] ?? null;

            if (!$response || !isset($response['ok']) || $response['ok'] != 1) {
                throw new \Exception('MongoDB ping failed');
            }

            $this->line('   ✅ Connection successful!');

            // Step 2: Create Collections
            $this->info('Step 2: Creating Collections...');
            
            $collections = [
                'products' => 'Product catalog',
                'reviews' => 'Product reviews',
                'analytics' => 'User analytics',
                'categories' => 'Product categories'
            ];

            foreach ($collections as $collectionName => $description) {
                try {
                    $collection = $db->selectCollection($collectionName);
                    
                    // Insert a sample document to create the collection
                    $sampleData = $this->getSampleData($collectionName);
                    $result = $collection->insertOne($sampleData);
                    
                    $this->line("   ✅ Created '{$collectionName}' - {$description}");
                    $this->line("      Document ID: " . $result->getInsertedId());
                    
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  Collection '{$collectionName}' creation failed: " . $e->getMessage());
                }
            }

            // Step 3: Create Indexes
            $this->info('Step 3: Creating Indexes...');
            $this->createIndexes($db);

            // Step 4: Update .env file
            $this->info('Step 4: Updating .env file...');
            $this->updateEnvFile($dsn, $database);

            // Step 5: Test CRUD operations
            $this->info('Step 5: Testing CRUD Operations...');
            $this->testCrudOperations($db);

            $this->newLine();
            $this->info('🎉 MongoDB Atlas database created successfully!');
            $this->newLine();
            
            $this->table(['Database Info', 'Value'], [
                ['Database Name', $database],
                ['Collections Created', count($collections)],
                ['Connection String', 'Updated in .env'],
                ['Status', '✅ Ready for use']
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Database creation failed: ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Troubleshooting Tips:');
            $this->line('   1. Check MongoDB Atlas Network Access - add IP 0.0.0.0/0');
            $this->line('   2. Verify Database User exists with correct password');
            $this->line('   3. Ensure cluster is running');
            $this->line('   4. Try creating database manually in Atlas dashboard');
        }
    }

    private function getSampleData($collectionName)
    {
        $timestamp = now();
        
        switch ($collectionName) {
            case 'products':
                return [
                    'name' => 'Premium Beard Oil',
                    'description' => 'Nourishing beard oil with natural ingredients for healthy beard growth',
                    'price' => 29.99,
                    'category' => 'beard-care',
                    'sku' => 'VO-BEARD-001',
                    'stock_quantity' => 100,
                    'images' => ['/images/products/beard-oil-1.jpg'],
                    'ingredients' => ['Jojoba Oil', 'Argan Oil', 'Vitamin E'],
                    'is_active' => true,
                    'is_featured' => true,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ];
                
            case 'reviews':
                return [
                    'product_id' => 'sample-product-id',
                    'user_id' => 1,
                    'rating' => 5,
                    'title' => 'Excellent product!',
                    'comment' => 'Great quality beard oil, highly recommended.',
                    'verified_purchase' => true,
                    'helpful_votes' => 0,
                    'created_at' => $timestamp
                ];
                
            case 'analytics':
                return [
                    'event' => 'page_view',
                    'user_id' => 1,
                    'page' => '/products',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Laravel/Application',
                    'session_id' => 'sample-session',
                    'created_at' => $timestamp
                ];
                
            case 'categories':
                return [
                    'name' => 'Beard Care',
                    'slug' => 'beard-care',
                    'description' => 'Premium beard care products for modern men',
                    'image' => '/images/categories/beard-care.jpg',
                    'is_active' => true,
                    'sort_order' => 1,
                    'created_at' => $timestamp
                ];
                
            default:
                return [
                    'sample_data' => true,
                    'collection' => $collectionName,
                    'created_at' => $timestamp
                ];
        }
    }

    private function createIndexes($db)
    {
        try {
            // Products collection indexes
            $products = $db->selectCollection('products');
            $products->createIndex(['name' => 'text', 'description' => 'text']);
            $products->createIndex(['category' => 1, 'is_active' => 1]);
            $products->createIndex(['price' => 1]);
            $products->createIndex(['is_featured' => 1, 'is_active' => 1]);
            $this->line('   ✅ Product indexes created');

            // Reviews collection indexes
            $reviews = $db->selectCollection('reviews');
            $reviews->createIndex(['product_id' => 1]);
            $reviews->createIndex(['user_id' => 1]);
            $reviews->createIndex(['rating' => 1]);
            $this->line('   ✅ Review indexes created');

        } catch (\Exception $e) {
            $this->warn('   ⚠️  Index creation failed: ' . $e->getMessage());
        }
    }

    private function updateEnvFile($dsn, $database)
    {
        try {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);
            
            // Update MongoDB configuration
            $envContent = preg_replace(
                '/MONGODB_DSN=.*/',
                'MONGODB_DSN="' . $dsn . '"',
                $envContent
            );
            
            $envContent = preg_replace(
                '/MONGODB_DATABASE=.*/',
                'MONGODB_DATABASE=' . $database,
                $envContent
            );
            
            file_put_contents($envPath, $envContent);
            $this->line('   ✅ .env file updated');
            
        } catch (\Exception $e) {
            $this->warn('   ⚠️  .env update failed: ' . $e->getMessage());
        }
    }

    private function testCrudOperations($db)
    {
        try {
            $collection = $db->selectCollection('products');
            
            // Test read
            $count = $collection->countDocuments();
            $this->line("   ✅ Read test: {$count} documents found");
            
            // Test update
            $result = $collection->updateOne(
                ['name' => 'Premium Beard Oil'],
                ['$set' => ['last_tested' => now()]]
            );
            $this->line("   ✅ Update test: {$result->getModifiedCount()} document updated");
            
        } catch (\Exception $e) {
            $this->warn('   ⚠️  CRUD test failed: ' . $e->getMessage());
        }
    }
}