<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestEmailVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vosiz:test-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email verification setup and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Vosiz Email Verification Test');
        $this->line('');

        // Check email configuration
        $this->checkEmailConfig();
        $this->line('');

        // Test email sending
        $email = $this->argument('email');
        if (!$email) {
            $email = $this->ask('Enter test email address');
        }

        if ($email) {
            $this->testEmailSending($email);
        }

        $this->line('');
        $this->info('📋 Email Verification Status:');
        $this->showVerificationStats();
    }

    private function checkEmailConfig()
    {
        $this->info('📧 Email Configuration Check:');
        
        $mailer = config('mail.default');
        $this->line("   Mailer: {$mailer}");
        
        if ($mailer === 'smtp') {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            $encryption = config('mail.mailers.smtp.encryption');
            $username = config('mail.mailers.smtp.username');
            
            $this->line("   Host: {$host}");
            $this->line("   Port: {$port}");
            $this->line("   Encryption: {$encryption}");
            $this->line("   Username: " . ($username ? '✅ Set' : '❌ Not set'));
            $this->line("   Password: " . (config('mail.mailers.smtp.password') ? '✅ Set' : '❌ Not set'));
        }
        
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');
        
        $this->line("   From Address: {$fromAddress}");
        $this->line("   From Name: {$fromName}");
    }

    private function testEmailSending($email)
    {
        $this->info('🚀 Testing Email Sending...');
        
        try {
            Mail::raw('This is a test email from Vosiz Email Verification System.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Vosiz Email Test - ' . now()->format('Y-m-d H:i:s'));
            });
            
            $this->info("   ✅ Test email sent to: {$email}");
            $this->line("   📬 Check your inbox (and spam folder)");
        } catch (\Exception $e) {
            $this->error("   ❌ Failed to send email: " . $e->getMessage());
            $this->line("   💡 Check your SMTP configuration in .env file");
        }
    }

    private function showVerificationStats()
    {
        try {
            $totalUsers = User::count();
            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $unverifiedUsers = User::whereNull('email_verified_at')->count();
            
            $this->line("   Total Users: {$totalUsers}");
            $this->line("   Verified: {$verifiedUsers}");
            $this->line("   Unverified: {$unverifiedUsers}");
            
            if ($unverifiedUsers > 0) {
                $this->line('');
                $this->comment('   Unverified users:');
                User::whereNull('email_verified_at')->take(5)->get()->each(function ($user) {
                    $this->line("   - {$user->name} ({$user->email})");
                });
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Could not fetch user statistics: " . $e->getMessage());
        }
    }
}
