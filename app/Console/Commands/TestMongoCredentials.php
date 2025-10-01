<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;

class TestMongoCredentials extends Command
{
    protected $signature = 'vosiz:test-mongo-creds';
    protected $description = 'Test different MongoDB credential encodings';

    public function handle()
    {
        $this->info('🔐 Testing MongoDB Atlas Credential Encodings...');
        $this->newLine();

        $username = 'sahannawarathne2004_db_user';
        $password = '@20040301Sa';
        $host = 'cluster0.2m8hhzb.mongodb.net';
        $database = 'vosiz_products';

        // Test different password encodings
        $encodings = [
            'URL Encoded (@)' => '%40' . substr($password, 1),
            'Double URL Encoded' => urlencode($password),
            'Raw Password' => $password,
            'Manual Encode' => '%4020040301Sa'
        ];

        foreach ($encodings as $label => $encodedPassword) {
            $this->info("Testing: {$label}");
            $this->line("   Encoded password: {$encodedPassword}");

            $dsn = "mongodb+srv://{$username}:{$encodedPassword}@{$host}/{$database}?retryWrites=true&w=majority&appName=Cluster0";
            
            try {
                $client = new Client($dsn);
                $db = $client->selectDatabase($database);
                $result = $db->command(['ping' => 1]);
                $response = $result->toArray()[0] ?? null;
                
                if ($response && isset($response['ok']) && $response['ok'] == 1) {
                    $this->line("   ✅ SUCCESS!");
                    $this->info("🎉 Working DSN: {$dsn}");
                    return;
                } else {
                    $this->line("   ❌ Ping failed");
                }
            } catch (\Exception $e) {
                $this->line("   ❌ Error: " . $e->getMessage());
            }
            $this->newLine();
        }

        $this->newLine();
        $this->warn('⚠️  All credential encodings failed. Please check:');
        $this->line('   1. MongoDB Atlas dashboard - user exists and has correct permissions');
        $this->line('   2. IP whitelist includes your current IP or 0.0.0.0/0');
        $this->line('   3. Database user has read/write access to the database');
        $this->line('   4. Cluster is running and accessible');
    }
}