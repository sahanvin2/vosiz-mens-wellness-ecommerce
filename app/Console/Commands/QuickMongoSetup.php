<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;

class QuickMongoSetup extends Command
{
    protected $signature = 'vosiz:quick-mongo-setup {--user=vosiz_user} {--pass=vosiz123} {--db=vosiz_products}';
    protected $description = 'Quick MongoDB Atlas setup with simple credentials';

    public function handle()
    {
        $this->info('🚀 Quick MongoDB Atlas Setup');
        $this->newLine();

        $username = $this->option('user');
        $password = $this->option('pass');
        $database = $this->option('db');
        $cluster = 'cluster0.2m8hhzb.mongodb.net';

        $this->line("Using credentials: {$username} / {$password}");
        $this->line("Target database: {$database}");
        $this->newLine();

        // Build connection string
        $dsn = "mongodb+srv://{$username}:{$password}@{$cluster}/{$database}?retryWrites=true&w=majority";

        try {
            $this->info('🔗 Testing Connection...');
            $client = new Client($dsn, [
                'serverSelectionTimeoutMS' => 5000,
            ]);

            $db = $client->selectDatabase($database);
            $result = $db->command(['ping' => 1]);
            $response = $result->toArray()[0] ?? null;

            if ($response && isset($response['ok']) && $response['ok'] == 1) {
                $this->line('   ✅ Connection successful!');
                
                // Create sample collections
                $this->info('📁 Creating Collections...');
                $this->createCollections($db);
                
                // Update .env
                $this->info('⚙️  Updating Configuration...');
                $this->updateConfig($dsn, $database);
                
                $this->newLine();
                $this->info('🎉 MongoDB Atlas setup completed!');
                $this->line('   Database: ' . $database);
                $this->line('   Status: Ready for use');
                
            } else {
                throw new \Exception('Ping test failed');
            }

        } catch (\Exception $e) {
            $this->error('❌ Setup failed: ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Setup Instructions:');
            $this->line('1. Go to MongoDB Atlas Dashboard');
            $this->line('2. Create database user:');
            $this->line('   - Username: vosiz_user');
            $this->line('   - Password: vosiz123');
            $this->line('   - Privileges: Read and write to any database');
            $this->line('3. Add IP 0.0.0.0/0 to Network Access');
            $this->line('4. Run: php artisan vosiz:quick-mongo-setup');
        }
    }

    private function createCollections($db)
    {
        $collections = [
            'products' => [
                'name' => 'Premium Beard Oil',
                'price' => 29.99,
                'category' => 'beard-care',
                'is_active' => true,
                'created_at' => now()
            ],
            'reviews' => [
                'product_id' => 'sample',
                'rating' => 5,
                'comment' => 'Great product!',
                'created_at' => now()
            ],
            'categories' => [
                'name' => 'Beard Care',
                'slug' => 'beard-care',
                'is_active' => true,
                'created_at' => now()
            ]
        ];

        foreach ($collections as $name => $sampleData) {
            try {
                $collection = $db->selectCollection($name);
                $result = $collection->insertOne($sampleData);
                $this->line("   ✅ Created '{$name}' collection");
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Collection '{$name}' creation failed");
            }
        }
    }

    private function updateConfig($dsn, $database)
    {
        try {
            $envPath = base_path('.env');
            $content = file_get_contents($envPath);
            
            // Update MongoDB settings
            $content = preg_replace('/MONGODB_DSN=.*/', 'MONGODB_DSN="' . $dsn . '"', $content);
            $content = preg_replace('/MONGODB_DATABASE=.*/', 'MONGODB_DATABASE=' . $database, $content);
            
            file_put_contents($envPath, $content);
            $this->line('   ✅ Configuration updated');
            
        } catch (\Exception $e) {
            $this->warn('   ⚠️  Config update failed: ' . $e->getMessage());
        }
    }
}