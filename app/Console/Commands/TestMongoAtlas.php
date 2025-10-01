<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;

class TestMongoAtlas extends Command
{
    protected $signature = 'vosiz:test-mongo-atlas';
    protected $description = 'Test MongoDB Atlas connection with detailed debugging';

    public function handle()
    {
        $this->info('🍃 Testing MongoDB Atlas Connection...');
        $this->newLine();

        // Get DSN from config
        $dsn = config('database.connections.mongodb.dsn');
        $database = config('database.connections.mongodb.database');

        $this->line("📝 Connection String: " . substr($dsn, 0, 50) . "...");
        $this->line("📝 Database: {$database}");
        $this->newLine();

        try {
            $this->info('Step 1: Creating MongoDB Client...');
            
            $client = new Client($dsn, [
                'typeMap' => [
                    'array' => 'array',
                    'document' => 'array',
                    'root' => 'array',
                ]
            ]);
            
            $this->line('   ✅ Client created successfully');

            $this->info('Step 2: Selecting Database...');
            $db = $client->selectDatabase($database);
            $this->line('   ✅ Database selected');

            $this->info('Step 3: Testing Ping Command...');
            $result = $db->command(['ping' => 1]);
            $response = $result->toArray()[0] ?? null;
            
            if ($response && isset($response['ok']) && $response['ok'] == 1) {
                $this->line('   ✅ Ping successful');
                $this->info('📊 Full ping response:');
                $this->line('   ' . json_encode($response, JSON_PRETTY_PRINT));
            } else {
                $this->error('   ❌ Ping failed - Invalid response');
                $this->line('   Response: ' . json_encode($response, JSON_PRETTY_PRINT));
            }

            $this->info('Step 4: Testing Database Stats...');
            try {
                $stats = $db->command(['dbStats' => 1]);
                $statsArray = $stats->toArray()[0] ?? null;
                
                if ($statsArray) {
                    $this->line('   ✅ Database stats retrieved');
                    $this->table(['Metric', 'Value'], [
                        ['Database Name', $statsArray['db'] ?? 'N/A'],
                        ['Collections', $statsArray['collections'] ?? 0],
                        ['Objects', $statsArray['objects'] ?? 0],
                        ['Data Size', $this->formatBytes($statsArray['dataSize'] ?? 0)],
                    ]);
                }
            } catch (\Exception $e) {
                $this->warn('   ⚠️  Database stats failed: ' . $e->getMessage());
            }

            $this->info('Step 5: Testing Collection Access...');
            try {
                $collections = $db->listCollections();
                $collectionNames = [];
                foreach ($collections as $collection) {
                    $collectionNames[] = $collection->getName();
                }
                
                if (!empty($collectionNames)) {
                    $this->line('   ✅ Collections found: ' . implode(', ', $collectionNames));
                } else {
                    $this->line('   ℹ️  No collections found (database might be empty)');
                }
            } catch (\Exception $e) {
                $this->warn('   ⚠️  Collection listing failed: ' . $e->getMessage());
            }

            $this->newLine();
            $this->info('🎉 MongoDB Atlas connection test completed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ MongoDB Exception: ' . $e->getMessage());
            $this->line('   Error Code: ' . $e->getCode());
        } catch (\Exception $e) {
            $this->error('❌ General Exception: ' . $e->getMessage());
            $this->line('   Exception Type: ' . get_class($e));
        }

        $this->newLine();
        $this->info('💡 Troubleshooting Tips:');
        $this->line('   1. Verify your MongoDB Atlas cluster is running');
        $this->line('   2. Check IP whitelist in MongoDB Atlas');
        $this->line('   3. Verify username and password');
        $this->line('   4. Ensure database name exists');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}