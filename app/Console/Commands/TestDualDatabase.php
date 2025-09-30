<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;
use Illuminate\Support\Facades\DB;

class TestDualDatabase extends Command
{
    protected $signature = 'test:dual-db';
    protected $description = 'Test both MySQL and MongoDB connections';

    public function handle()
    {
        $this->info('🔄 Testing Dual Database System (MySQL + MongoDB)');
        $this->newLine();

        // Test MySQL Connection
        $this->info('📊 MySQL Database Tests:');
        try {
            $mysqlConnection = DB::connection('mysql');
            $this->line('   ✅ MySQL Connection: OK');
            
            $userCount = User::count();
            $categoryCount = Category::count();
            $productCount = Product::count();
            
            $this->line("   👥 Users: {$userCount}");
            $this->line("   📁 Categories: {$categoryCount}");
            $this->line("   🛍️  Products: {$productCount}");
            
            // Test user roles
            $adminCount = User::where('role', 'admin')->count();
            $supplierCount = User::where('role', 'supplier')->count();
            $this->line("   🔑 Admins: {$adminCount}");
            $this->line("   🏪 Suppliers: {$supplierCount}");
            
        } catch (\Exception $e) {
            $this->error('   ❌ MySQL Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test MongoDB Connection
        $this->info('📊 MongoDB Database Tests:');
        try {
            $mongoConnection = DB::connection('mongodb');
            $this->line('   ✅ MongoDB Connection: OK');
            
            $mongoProductCount = MongoDBProduct::count();
            $mongoCategoryCount = MongoCategory::count();
            
            $this->line("   🛍️  Products: {$mongoProductCount}");
            $this->line("   📁 Categories: {$mongoCategoryCount}");
            
            // Test MongoDB queries
            $activeProducts = MongoDBProduct::where('status', 'active')->count();
            $featuredProducts = MongoDBProduct::where('is_featured', true)->count();
            
            $this->line("   ✅ Active Products: {$activeProducts}");
            $this->line("   ⭐ Featured Products: {$featuredProducts}");
            
            // Test sample product details
            $sampleProduct = MongoDBProduct::first();
            if ($sampleProduct) {
                $this->line("   📝 Sample Product: {$sampleProduct->name}");
            }
            
        } catch (\Exception $e) {
            $this->error('   ❌ MongoDB Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test Authentication System
        $this->info('🔐 Authentication System Tests:');
        try {
            $adminUser = User::where('role', 'admin')->first();
            if ($adminUser) {
                $this->line('   ✅ Admin User Found: ' . $adminUser->email);
            } else {
                $this->error('   ❌ No Admin User Found');
            }
            
            $supplierUser = User::where('role', 'supplier')->first();
            if ($supplierUser) {
                $this->line('   ✅ Supplier User Found: ' . $supplierUser->email);
            }
            
        } catch (\Exception $e) {
            $this->error('   ❌ Auth Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test Route Access
        $this->info('🌐 Application Routes:');
        $this->line('   🏠 Homepage: http://127.0.0.1:8001/');
        $this->line('   🔐 Login: http://127.0.0.1:8001/login');
        $this->line('   👑 Admin Dashboard: http://127.0.0.1:8001/admin/dashboard');
        $this->line('   📦 Product Management: http://127.0.0.1:8001/admin/products/manage');
        $this->line('   🏪 Supplier Dashboard: http://127.0.0.1:8001/supplier/dashboard');

        $this->newLine();

        // Configuration Check
        $this->info('⚙️  Configuration Status:');
        $this->line('   📊 Default DB: ' . config('database.default'));
        $this->line('   🐬 MySQL Host: ' . config('database.connections.mysql.host'));
        $this->line('   🐬 MySQL Port: ' . config('database.connections.mysql.port'));
        $this->line('   🍃 MongoDB DSN: ' . config('database.connections.mongodb.dsn'));
        $this->line('   🍃 MongoDB Database: ' . config('database.connections.mongodb.database'));

        $this->newLine();

        // Final Status
        $mysqlWorking = false;
        $mongoWorking = false;
        
        try {
            User::count();
            $mysqlWorking = true;
        } catch (\Exception $e) {
            // MySQL not working
        }
        
        try {
            MongoDBProduct::count();
            $mongoWorking = true;
        } catch (\Exception $e) {
            // MongoDB not working
        }

        if ($mysqlWorking && $mongoWorking) {
            $this->info('🎉 SUCCESS: Both MySQL and MongoDB are working perfectly!');
            $this->comment('💡 You can now use both databases in your application:');
            $this->line('   • MySQL: Users, Orders, Categories, Products (traditional)');
            $this->line('   • MongoDB: Products, Categories (flexible document storage)');
        } elseif ($mysqlWorking) {
            $this->warn('⚠️  MySQL is working, but MongoDB has issues.');
        } elseif ($mongoWorking) {
            $this->warn('⚠️  MongoDB is working, but MySQL has issues.');
        } else {
            $this->error('❌ Both databases have connection issues.');
        }

        $this->newLine();
        $this->comment('🚀 Ready to develop your dual-database ecommerce platform!');

        return Command::SUCCESS;
    }
}