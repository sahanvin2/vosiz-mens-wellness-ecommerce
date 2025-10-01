<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\MongoDBAtlasService;
use App\Models\User;
use App\Models\MongoDBProduct;

class TestDatabaseConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vosiz:test-connections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MySQL and MongoDB Atlas connections for Vosiz';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Testing Vosiz Database Connections...');
        $this->newLine();

        // Test MySQL Connection
        $this->testMySQLConnection();
        $this->newLine();

        // Test MongoDB Atlas Connection
        $this->testMongoDBAtlasConnection();
        $this->newLine();

        // Display summary
        $this->displaySummary();
    }

    /**
     * Test MySQL database connection
     */
    private function testMySQLConnection()
    {
        $this->info('📊 Testing MySQL Connection (Users, Orders, Categories)...');
        
        try {
            // Test basic connection
            DB::connection('mysql')->getPdo();
            $this->line('   ✅ MySQL connection: <fg=green>SUCCESS</fg=green>');

            // Test database operations
            $userCount = User::count();
            $this->line("   📊 Total users: <fg=yellow>{$userCount}</fg=yellow>");

            // Test table structure
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            $this->line("   🗃️  Total tables: <fg=yellow>{$tableCount}</fg=yellow>");

            // Display some table names
            $importantTables = ['users', 'orders', 'order_items', 'categories', 'products'];
            foreach ($importantTables as $table) {
                $exists = DB::getSchemaBuilder()->hasTable($table);
                $status = $exists ? '<fg=green>EXISTS</fg=green>' : '<fg=red>MISSING</fg=red>';
                $this->line("   📋 Table '{$table}': {$status}");
            }

            // Test specific queries
            $adminCount = User::where('role', 'admin')->count();
            $this->line("   👑 Admin users: <fg=yellow>{$adminCount}</fg=yellow>");

        } catch (\Exception $e) {
            $this->error('   ❌ MySQL connection failed: ' . $e->getMessage());
            $this->line('   💡 Check your .env file MySQL configuration');
        }
    }

    /**
     * Test MongoDB Atlas connection
     */
    private function testMongoDBAtlasConnection()
    {
        $this->info('🍃 Testing MongoDB Atlas Connection (Products, Reviews)...');
        
        try {
            // Test using MongoDB service
            $mongoService = new MongoDBAtlasService();
            
            if ($mongoService->testConnection()) {
                $this->line('   ✅ MongoDB Atlas connection: <fg=green>SUCCESS</fg=green>');

                // Get database stats
                $stats = $mongoService->getConnectionStats();
                if (!empty($stats)) {
                    $this->line("   📊 Database: <fg=yellow>{$stats['database']}</fg=yellow>");
                    $this->line("   📁 Collections: <fg=yellow>{$stats['collections']}</fg=yellow>");
                    $this->line("   📄 Objects: <fg=yellow>{$stats['objects']}</fg=yellow>");
                    $this->line("   💾 Data Size: <fg=yellow>" . $this->formatBytes($stats['dataSize']) . "</fg=yellow>");
                }

                // Test MongoDB Product model
                try {
                    $productCount = MongoDBProduct::count();
                    $this->line("   🛍️  Total products: <fg=yellow>{$productCount}</fg=yellow>");

                    // Test product operations
                    $firstProduct = MongoDBProduct::first();
                    if ($firstProduct) {
                        $this->line("   📦 Sample product: <fg=yellow>{$firstProduct->name}</fg=yellow>");
                        $this->line("   💰 Price: <fg=yellow>\${$firstProduct->price}</fg=yellow>");
                    }

                    // Test aggregation
                    $avgPrice = MongoDBProduct::avg('price');
                    if ($avgPrice) {
                        $this->line("   📈 Average price: <fg=yellow>\$" . number_format($avgPrice, 2) . "</fg=yellow>");
                    }

                } catch (\Exception $e) {
                    $this->warn('   ⚠️  MongoDB Product model test failed: ' . $e->getMessage());
                }

                // Test collections
                $database = $mongoService->getDatabase();
                $collections = $database->listCollections();
                $collectionNames = [];
                foreach ($collections as $collection) {
                    $collectionNames[] = $collection->getName();
                }
                
                if (!empty($collectionNames)) {
                    $this->line('   📚 Collections: <fg=yellow>' . implode(', ', $collectionNames) . '</fg=yellow>');
                }

            } else {
                $this->error('   ❌ MongoDB Atlas ping test failed');
            }

        } catch (\Exception $e) {
            $this->error('   ❌ MongoDB Atlas connection failed: ' . $e->getMessage());
            $this->line('   💡 Check your MongoDB Atlas configuration in .env file');
            $this->line('   📝 Required: MONGODB_DSN with proper Atlas connection string');
        }
    }

    /**
     * Display connection summary
     */
    private function displaySummary()
    {
        $this->info('📋 Database Architecture Summary:');
        
        $this->table(
            ['Database', 'Purpose', 'Status'],
            [
                [
                    'MySQL', 
                    'Users, Authentication, Orders, Categories', 
                    $this->testMySQLStatus() ? '✅ Connected' : '❌ Failed'
                ],
                [
                    'MongoDB Atlas', 
                    'Products, Reviews, Analytics', 
                    $this->testMongoDBStatus() ? '✅ Connected' : '❌ Failed'
                ]
            ]
        );

        $this->newLine();
        $this->info('🔍 Configuration Check:');
        $this->line('   📁 MySQL Config: .env (DB_* variables)');
        $this->line('   🍃 MongoDB Atlas Config: .env (MONGODB_DSN)');
        $this->line('   🔧 Models: app/Models/ (User.php, MongoDBProduct.php)');
        $this->line('   ⚙️  Service: app/Services/MongoDBAtlasService.php');
        
        $this->newLine();
        $this->info('✅ Database connection test completed!');
    }

    /**
     * Quick MySQL status check
     */
    private function testMySQLStatus()
    {
        try {
            DB::connection('mysql')->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Quick MongoDB status check
     */
    private function testMongoDBStatus()
    {
        try {
            $mongoService = new MongoDBAtlasService();
            return $mongoService->testConnection();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}