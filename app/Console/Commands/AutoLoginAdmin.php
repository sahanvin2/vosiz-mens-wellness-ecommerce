<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AutoLoginAdmin extends Command
{
    protected $signature = 'admin:auto-login';
    protected $description = 'Auto login as admin and show session info';

    public function handle()
    {
        try {
            $admin = User::where('email', 'admin@vosiz.com')->first();
            
            if (!$admin) {
                $this->error('Admin user not found!');
                return 1;
            }
            
            $this->info('Admin user found:');
            $this->line('  Name: ' . $admin->name);
            $this->line('  Email: ' . $admin->email);
            $this->line('  Role: ' . $admin->role);
            $this->line('  Active: ' . ($admin->is_active ? 'Yes' : 'No'));
            $this->line('  Verified: ' . ($admin->email_verified_at ? 'Yes' : 'No'));
            
            // Create a session token for easy login
            $token = $admin->createToken('admin-access')->plainTextToken;
            
            $this->newLine();
            $this->info('🔑 Auto-login URLs created:');
            $this->line('1. Direct login link: http://127.0.0.1:8000/auto-login/' . $admin->id);
            $this->line('2. Admin panel: http://127.0.0.1:8000/admin/products/manage');
            $this->line('3. Products debug: http://127.0.0.1:8000/show-products-now');
            
            $this->newLine();
            $this->comment('💡 If you still can\'t see products, the issue might be:');
            $this->line('   • Browser cache (try incognito mode)');
            $this->line('   • Session issues (clear browser data)');
            $this->line('   • View caching (run: php artisan view:clear)');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}