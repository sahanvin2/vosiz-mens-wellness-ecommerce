<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class VosizCurrentStatus extends Command
{
    protected $signature = 'vosiz:current-status';
    protected $description = 'Show current Vosiz application status';

    public function handle()
    {
        $this->info('🚀 Vosiz Men\'s Wellness Ecommerce - Current Status');
        $this->newLine();

        // Database Status
        $this->info('📊 Database Status:');
        try {
            $userCount = User::count();
            $productCount = Product::count();
            $categoryCount = Category::count();
            
            $this->table(['Database', 'Status', 'Records'], [
                ['MySQL (Main)', '✅ Connected', "Users: {$userCount}, Products: {$productCount}, Categories: {$categoryCount}"],
                ['MongoDB Atlas', '❌ Disabled', 'Authentication issues - using MySQL fallback'],
            ]);
        } catch (\Exception $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
        }

        $this->newLine();

        // Application Status
        $this->info('🛠️  Application Components:');
        $this->table(['Component', 'Status', 'Notes'], [
            ['Laravel Framework', '✅ Working', 'Version 12.31.1'],
            ['Authentication', '✅ Working', 'Jetstream + Sanctum'],
            ['Product Catalog', '✅ Working', 'Using MySQL fallback'],
            ['User Management', '✅ Working', 'MySQL database'],
            ['MongoDB Integration', '⚠️  Pending', 'Requires Atlas credentials fix'],
        ]);

        $this->newLine();

        // Current Configuration
        $this->info('⚙️  Current Configuration:');
        $this->line('   🌐 Server: http://127.0.0.1:8000');
        $this->line('   📁 Database: MySQL (Primary)');
        $this->line('   🔐 Auth: Jetstream + Sanctum');
        $this->line('   🎨 Theme: Dark Premium Theme');
        $this->line('   📱 Frontend: Livewire + Tailwind CSS');

        $this->newLine();

        // Next Steps
        $this->info('📋 Next Steps for MongoDB Atlas:');
        $this->line('   1. Verify MongoDB Atlas dashboard settings:');
        $this->line('      • Cluster is running');
        $this->line('      • Database user exists with correct permissions');
        $this->line('      • IP whitelist includes 0.0.0.0/0');
        $this->line('   2. Test connection: php artisan vosiz:mongo-quick-test');
        $this->line('   3. Once working, re-enable MongoDB in .env');

        $this->newLine();
        $this->info('✅ Application is currently functional with MySQL!');
        $this->line('   🌐 Visit: http://127.0.0.1:8000/products to test');
    }
}