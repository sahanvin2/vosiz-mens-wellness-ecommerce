<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SendTestVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vosiz:send-verification {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test verification email to see the content';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        
        $this->info('🧪 Creating test user and sending verification email...');
        
        // Delete existing test user if exists
        User::where('email', $email)->delete();
        
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => $email,
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);
        
        $this->line("✅ Test user created: {$user->name} ({$user->email})");
        
        // Send verification email
        $user->sendEmailVerificationNotification();
        
        $this->info('📧 Verification email sent!');
        $this->line('');
        $this->comment('Since MAIL_MAILER=log, check the email content in:');
        $this->line('   storage/logs/laravel.log');
        $this->line('');
        $this->comment('To see the latest email:');
        $this->line('   Get-Content storage/logs/laravel.log -Tail 50');
        
        return 0;
    }
}
