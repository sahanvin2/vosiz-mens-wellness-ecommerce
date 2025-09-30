<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdmin extends Command
{
    protected $signature = 'admin:check';
    protected $description = 'Check if admin user exists';

    public function handle()
    {
        $admin = User::where('role', 'admin')->first();
        
        if ($admin) {
            $this->info('✅ Admin user exists: ' . $admin->email);
            $this->line('   Admin ID: ' . $admin->id);
            $this->line('   Admin Name: ' . $admin->name);
            
            // Create a test login URL
            $this->info('');
            $this->info('🔗 To access admin dashboard:');
            $this->line('1. Go to: http://127.0.0.1:8001/login');
            $this->line('2. Login with: ' . $admin->email . ' / password');
            $this->line('3. You will be redirected to admin dashboard');
        } else {
            $this->error('❌ No admin user found');
            $this->info('Creating admin user...');
            
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@vosiz.com',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]);
            
            $this->info('✅ Admin user created: ' . $admin->email);
        }

        return Command::SUCCESS;
    }
}