<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerifyMongoAtlasSetup extends Command
{
    protected $signature = 'vosiz:verify-atlas-setup';
    protected $description = 'Verify MongoDB Atlas setup requirements';

    public function handle()
    {
        $this->info('📋 MongoDB Atlas Setup Verification Checklist');
        $this->newLine();

        $this->line('Please verify the following in your MongoDB Atlas dashboard:');
        $this->newLine();

        // Step 1: Cluster Status
        $this->info('1. 🏗️  Cluster Status');
        $this->line('   ✓ Go to MongoDB Atlas dashboard');
        $this->line('   ✓ Check that cluster "Cluster0" is running (green status)');
        $this->line('   ✓ Cluster should show "cluster0.2m8hhzb.mongodb.net" as connection string');
        $this->newLine();

        // Step 2: Database User
        $this->info('2. 👤 Database User');
        $this->line('   ✓ Go to Database Access → Database Users');
        $this->line('   ✓ Verify user "sahannawarathne2004_db_user" exists');
        $this->line('   ✓ User should have "Read and write to any database" role');
        $this->line('   ✓ Password should be exactly: @20040301Sa');
        $this->line('   ✓ Authentication method should be "Password"');
        $this->newLine();

        // Step 3: Network Access
        $this->info('3. 🌐 Network Access (IP Whitelist)');
        $this->line('   ✓ Go to Network Access → IP Access List');
        $this->line('   ✓ Either add your current IP address, or');
        $this->line('   ✓ Add 0.0.0.0/0 (allow access from anywhere) for testing');
        $this->newLine();

        // Step 4: Database
        $this->info('4. 🗄️  Database');
        $this->line('   ✓ Database name should be "vosiz_products" (case sensitive)');
        $this->line('   ✓ You can create it by inserting a test document');
        $this->newLine();

        // Current Configuration
        $this->info('5. 🔧 Current Configuration');
        $dsn = config('database.connections.mongodb.dsn');
        $database = config('database.connections.mongodb.database');
        
        $this->table(['Setting', 'Value'], [
            ['Connection String', substr($dsn, 0, 80) . '...'],
            ['Database Name', $database],
            ['Username', 'sahannawarathne2004_db_user'],
            ['Password', 'Hidden (should be @20040301Sa)'],
        ]);
        
        $this->newLine();
        
        // Recommendations
        $this->info('💡 Quick Setup Recommendations:');
        $this->line('   1. Create a new database user with a simpler password (no special chars)');
        $this->line('   2. Ensure IP whitelist includes 0.0.0.0/0 for testing');
        $this->line('   3. Create the "vosiz_products" database manually if it doesn\'t exist');
        $this->line('   4. Test connection using MongoDB Compass first');
        
        $this->newLine();
        $this->warn('If issues persist, try creating a new user with password: "vosiz123" and test again.');
    }
}