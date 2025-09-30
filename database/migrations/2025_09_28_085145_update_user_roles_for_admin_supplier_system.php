<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the role column to support admin/supplier/user
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'supplier', 'admin') DEFAULT 'user'");
        
        // Create a single admin user if it doesn't exist
        if (!DB::table('users')->where('role', 'admin')->exists()) {
            DB::table('users')->insert([
                'name' => 'System Administrator',
                'email' => 'admin@vosiz.com',
                'email_verified_at' => now(),
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Convert existing admin users to suppliers (except the main admin)
        DB::table('users')
            ->where('role', 'admin')
            ->where('email', '!=', 'admin@vosiz.com')
            ->update(['role' => 'supplier']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
    }
};
