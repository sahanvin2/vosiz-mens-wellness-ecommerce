<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;

class MongoAtlasQuickTest extends Command
{
    protected $signature = 'vosiz:mongo-quick-test {--user=} {--pass=} {--db=vosiz_products}';
    protected $description = 'Quick MongoDB Atlas connection test with custom credentials';

    public function handle()
    {
        $this->info('🚀 Quick MongoDB Atlas Connection Test');
        $this->newLine();

        // Get credentials
        $user = $this->option('user') ?: 'sahannawarathne2004_db_user';
        $pass = $this->option('pass') ?: '@20040301Sa';
        $database = $this->option('db') ?: 'vosiz_products';

        $this->line("Testing with user: {$user}");
        $this->line("Database: {$database}");
        $this->newLine();

        // Try different password encodings
        $passwords = [
            'Original' => $pass,
            'URL Encoded' => urlencode($pass),
            'Manual @ encode' => str_replace('@', '%40', $pass),
        ];

        foreach ($passwords as $label => $encodedPass) {
            $this->info("Trying: {$label} encoding");
            
            $dsn = "mongodb+srv://{$user}:{$encodedPass}@cluster0.2m8hhzb.mongodb.net/{$database}?retryWrites=true&w=majority";
            
            try {
                $client = new Client($dsn, [
                    'serverSelectionTimeoutMS' => 3000,
                ]);
                
                $db = $client->selectDatabase($database);
                $result = $db->command(['ping' => 1]);
                $response = $result->toArray()[0] ?? null;
                
                if ($response && isset($response['ok']) && $response['ok'] == 1) {
                    $this->line("   ✅ SUCCESS!");
                    
                    // Update .env file with working credentials
                    $this->info('🎉 Connection successful! Updating .env file...');
                    $this->updateEnvFile($dsn, $database);
                    
                    // Test basic operations
                    $this->testBasicOperations($db);
                    return;
                }
            } catch (\Exception $e) {
                $this->line("   ❌ Failed: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->error('❌ All connection attempts failed.');
        $this->newLine();
        $this->warn('Please check:');
        $this->line('1. MongoDB Atlas dashboard → Network Access → Add IP 0.0.0.0/0');
        $this->line('2. Database Access → Verify user exists with correct password');
        $this->line('3. Cluster is running and accessible');
        $this->newLine();
        $this->info('Or try with a simpler password:');
        $this->line('php artisan vosiz:mongo-quick-test --pass=vosiz123');
    }

    private function updateEnvFile($dsn, $database)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        // Update MongoDB DSN
        $envContent = preg_replace(
            '/MONGODB_DSN=.*/',
            'MONGODB_DSN="' . $dsn . '"',
            $envContent
        );
        
        file_put_contents($envPath, $envContent);
        $this->line('   ✅ .env file updated with working connection string');
    }

    private function testBasicOperations($db)
    {
        $this->info('🧪 Testing basic operations...');
        
        try {
            // Test collection creation and insertion
            $collection = $db->selectCollection('test_products');
            
            $result = $collection->insertOne([
                'name' => 'Test Product',
                'price' => 19.99,
                'category' => 'test',
                'created_at' => now()
            ]);
            
            $this->line("   ✅ Document inserted with ID: " . $result->getInsertedId());
            
            // Test reading
            $document = $collection->findOne(['name' => 'Test Product']);
            if ($document) {
                $this->line("   ✅ Document retrieved successfully");
            }
            
            // Clean up test document
            $collection->deleteOne(['name' => 'Test Product']);
            $this->line("   ✅ Test document cleaned up");
            
        } catch (\Exception $e) {
            $this->warn("   ⚠️  Basic operations test failed: " . $e->getMessage());
        }
    }
}