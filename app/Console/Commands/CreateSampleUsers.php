<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateSampleUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-samples';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample users for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create suppliers
        $suppliers = [
            [
                'name' => 'John Supplier',
                'email' => 'supplier@vosiz.com',
                'password' => bcrypt('supplier123'),
                'role' => 'supplier',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Beauty Co.',
                'email' => 'sarah@beautyco.com',
                'password' => bcrypt('supplier123'),
                'role' => 'supplier',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mike Wellness',
                'email' => 'mike@wellness.com',
                'password' => bcrypt('supplier123'),
                'role' => 'supplier',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($suppliers as $supplierData) {
            $user = User::where('email', $supplierData['email'])->first();
            if (!$user) {
                User::create($supplierData);
                $this->info("Created supplier: {$supplierData['name']}");
            } else {
                $this->info("Supplier {$supplierData['name']} already exists");
            }
        }

        // Create regular users
        $users = [
            [
                'name' => 'Regular User',
                'email' => 'user@vosiz.com',
                'password' => bcrypt('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Customer',
                'email' => 'david@customer.com',
                'password' => bcrypt('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::where('email', $userData['email'])->first();
            if (!$user) {
                User::create($userData);
                $this->info("Created user: {$userData['name']}");
            } else {
                $this->info("User {$userData['name']} already exists");
            }
        }

        $this->info('Sample users created successfully!');
        $this->info('');
        $this->info('Login credentials:');
        $this->info('Admin: admin@vosiz.com / admin123');
        $this->info('Supplier: supplier@vosiz.com / supplier123');
        $this->info('User: user@vosiz.com / user123');
    }
}
