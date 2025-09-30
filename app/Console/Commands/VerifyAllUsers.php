<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class VerifyAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all users email addresses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying all users...');
        
        $count = User::whereNull('email_verified_at')->update([
            'email_verified_at' => now()
        ]);
        
        $this->info("✅ {$count} users verified successfully!");
        
        // Also create test accounts
        $this->info('Creating test accounts...');
        
        // Create test customer
        User::firstOrCreate([
            'email' => 'customer@vosiz.com'
        ], [
            'name' => 'Test Customer',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        
        // Create test supplier
        User::firstOrCreate([
            'email' => 'supplier@vosiz.com'
        ], [
            'name' => 'Test Supplier',
            'password' => bcrypt('password123'),
            'role' => 'supplier',
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        
        $this->info('✅ Test accounts created:');
        $this->info('👤 Customer: customer@vosiz.com / password123');
        $this->info('🏪 Supplier: supplier@vosiz.com / password123');
        $this->info('👨‍💼 Admin: admin@vosiz.com / admin123');
        
        return 0;
    }
}
