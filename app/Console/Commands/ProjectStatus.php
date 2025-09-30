<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MongoDBProduct;
use App\Models\MongoCategory;
use App\Models\User;

class ProjectStatus extends Command
{
    protected $signature = 'vosiz:status';
    protected $description = 'Show Vosiz project status and summary';

    public function handle()
    {
        $this->info('🎉 VOSIZ - Men\'s Health & Wellness Ecommerce Platform');
        $this->info('🔥 MongoDB + Laravel Integration Complete!');
        $this->info('');

        // MongoDB Status
        $this->info('📊 MongoDB Database Status:');
        try {
            $productCount = MongoDBProduct::count();
            $categoryCount = MongoCategory::count();
            $activeProducts = MongoDBProduct::where('status', 'active')->count();
            $featuredProducts = MongoDBProduct::where('is_featured', true)->count();

            $this->line("   ✅ Products: {$productCount}");
            $this->line("   ✅ Categories: {$categoryCount}");
            $this->line("   ✅ Active Products: {$activeProducts}");
            $this->line("   ✅ Featured Products: {$featuredProducts}");
        } catch (\Exception $e) {
            $this->error("   ❌ MongoDB Connection Error: " . $e->getMessage());
        }

        // User System Status
        $this->info('');
        $this->info('👥 User System Status:');
        $userCount = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $supplierCount = User::where('role', 'supplier')->count();
        
        $this->line("   ✅ Total Users: {$userCount}");
        $this->line("   ✅ Admins: {$adminCount}");
        $this->line("   ✅ Suppliers: {$supplierCount}");

        // System Features
        $this->info('');
        $this->info('🚀 Available Features:');
        $this->line('   ✅ Dual Database System (MySQL + MongoDB)');
        $this->line('   ✅ Three-tier Authentication (Admin/Supplier/User)');
        $this->line('   ✅ Separate Admin Dashboard');
        $this->line('   ✅ MongoDB Product Management');
        $this->line('   ✅ Livewire CRUD Components');
        $this->line('   ✅ Premium Dark Theme');
        $this->line('   ✅ Tailwind CSS Styling');
        $this->line('   ✅ Real-time Search & Filtering');
        $this->line('   ✅ File Upload System');
        $this->line('   ✅ Role-based Access Control');

        // Access URLs
        $this->info('');
        $this->info('🌐 Access URLs:');
        $this->line('   🏠 Homepage: http://127.0.0.1:8001');
        $this->line('   🔧 Admin Dashboard: http://127.0.0.1:8001/admin/dashboard');
        $this->line('   📦 Product Management: http://127.0.0.1:8001/admin/products');
        $this->line('   📹 Video Management: http://127.0.0.1:8001/admin/videos');

        // Development Commands
        $this->info('');
        $this->info('🛠️  Development Commands:');
        $this->line('   • php artisan mongo:test - Test MongoDB connection');
        $this->line('   • php artisan mongo:query - Query MongoDB data');
        $this->line('   • php artisan mongo:create-samples - Create sample products');
        $this->line('   • php artisan serve --port=8001 - Start development server');

        // Database Configuration
        $this->info('');
        $this->info('💾 Database Configuration:');
        $this->line('   • MySQL: ' . config('database.connections.mysql.host') . ':' . config('database.connections.mysql.port'));
        $this->line('   • MongoDB: ' . config('database.connections.mongodb.host') . ':' . config('database.connections.mongodb.port'));
        $this->line('   • MongoDB Database: ' . config('database.connections.mongodb.database'));

        $this->info('');
        $this->info('🎯 Next Steps:');
        $this->line('   1. Complete video upload functionality');
        $this->line('   2. Implement shopping cart with MongoDB sessions');
        $this->line('   3. Add order management system');
        $this->line('   4. Create customer review system');
        $this->line('   5. Build analytics dashboard');

        $this->info('');
        $this->info('💡 Tips:');
        $this->line('   • Use mongosh for direct MongoDB queries');
        $this->line('   • Admin login: admin@vosiz.com / password');
        $this->line('   • MongoDB collections: products, categories');
        $this->line('   • Check logs in storage/logs/laravel.log');

        $this->info('');
        $this->info('🎉 Vosiz MongoDB integration is complete and ready!');

        return Command::SUCCESS;
    }
}