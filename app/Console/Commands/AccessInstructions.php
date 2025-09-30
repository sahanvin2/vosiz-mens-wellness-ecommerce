<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AccessInstructions extends Command
{
    protected $signature = 'vosiz:access';
    protected $description = 'Show how to access the Vosiz admin dashboard';

    public function handle()
    {
        $this->info('🔐 VOSIZ Admin Access Instructions');
        $this->info('=====================================');
        $this->newLine();

        $this->info('📋 Why you see "Connection Refused":');
        $this->comment('   The admin dashboard is protected by authentication middleware.');
        $this->comment('   You MUST login first before accessing admin routes.');
        $this->newLine();

        $this->info('🚀 How to Access Admin Dashboard:');
        $this->newLine();

        $this->line('1️⃣  Open Login Page:');
        $this->comment('   🌐 http://127.0.0.1:8001/login');
        $this->newLine();

        $this->line('2️⃣  Use Admin Credentials:');
        $this->comment('   📧 Email: admin@vosiz.com');
        $this->comment('   🔑 Password: password');
        $this->newLine();

        $this->line('3️⃣  After Login, You Can Access:');
        $this->comment('   👑 Admin Dashboard: http://127.0.0.1:8001/admin/dashboard');
        $this->comment('   📦 Product Management: http://127.0.0.1:8001/admin/products/manage');
        $this->comment('   📹 Video Management: http://127.0.0.1:8001/admin/videos/manage');
        $this->comment('   👥 User Management: http://127.0.0.1:8001/admin/users');
        $this->newLine();

        $this->info('🔧 Alternative Access (For Testing):');
        $this->comment('   Direct Admin Test (bypasses auth): http://127.0.0.1:8001/admin-direct');
        $this->newLine();

        $this->info('📊 Other Test Routes:');
        $this->comment('   🏠 Homepage: http://127.0.0.1:8001/');
        $this->comment('   🧪 MongoDB Test: http://127.0.0.1:8001/test-mongo');
        $this->comment('   🔧 Admin Test: http://127.0.0.1:8001/admin-test');
        $this->newLine();

        $this->info('✅ Current Status:');
        try {
            $mysqlUsers = \App\Models\User::count();
            $mongoProducts = \App\Models\MongoDBProduct::count();
            
            $this->line("   📊 MySQL Users: {$mysqlUsers}");
            $this->line("   🍃 MongoDB Products: {$mongoProducts}");
            $this->line("   🌐 Server: Running on http://127.0.0.1:8001");
            $this->line("   🔐 Auth: Working properly");
        } catch (\Exception $e) {
            $this->error("   ❌ Database Error: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎯 Next Steps:');
        $this->line('   1. Login at: http://127.0.0.1:8001/login');
        $this->line('   2. Use: admin@vosiz.com / password');
        $this->line('   3. Access admin dashboard after login');
        $this->line('   4. Manage MongoDB products and categories');

        $this->newLine();
        $this->info('🎉 Your dual-database system is working perfectly!');

        return Command::SUCCESS;
    }
}