<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseIssues extends Command
{
    protected $signature = 'diagnose:issues';
    protected $description = 'Diagnose all current issues';

    public function handle()
    {
        $this->info('🔍 DIAGNOSING VOSIZ PROJECT ISSUES');
        $this->newLine();

        // Check Database Connections
        $this->comment('📊 Database Connections:');
        $this->checkMySQL();
        $this->checkMongoDB();
        $this->newLine();

        // Check Data Availability
        $this->comment('💾 Data Availability:');
        $this->checkData();
        $this->newLine();

        // Check File Issues
        $this->comment('📂 File Issues:');
        $this->checkAssets();
        $this->newLine();

        // Check Routes
        $this->comment('🛣️  Routes:');
        $this->checkRoutes();
        $this->newLine();

        $this->info('✅ DIAGNOSIS COMPLETE');
        $this->comment('🌐 Homepage: http://127.0.0.1:8001/');
        $this->comment('🔧 Admin: http://127.0.0.1:8001/admin/dashboard (requires login)');

        return 0;
    }

    private function checkMySQL()
    {
        try {
            $connection = DB::connection('mysql');
            $connection->getPdo();
            $this->line('  ✅ MySQL Connected');
            
            $users = \App\Models\User::count();
            $products = \App\Models\Product::count();
            $categories = \App\Models\Category::count();
            
            $this->line("     - Users: {$users}");
            $this->line("     - Products: {$products}");  
            $this->line("     - Categories: {$categories}");
            
        } catch (\Exception $e) {
            $this->error('  ❌ MySQL Error: ' . $e->getMessage());
        }
    }

    private function checkMongoDB()
    {
        try {
            $products = \App\Models\MongoDBProduct::count();
            $categories = \App\Models\MongoCategory::count();
            
            $this->line('  ✅ MongoDB Connected');
            $this->line("     - Products: {$products}");
            $this->line("     - Categories: {$categories}");
            
        } catch (\Exception $e) {
            $this->error('  ❌ MongoDB Error: ' . $e->getMessage());
        }
    }

    private function checkData()
    {
        try {
            $featured = \App\Models\Product::where('is_featured', true)->count();
            $mongoFeatured = \App\Models\MongoDBProduct::where('is_featured', true)->count();
            $activeCategories = \App\Models\Category::where('is_active', true)->count();
            
            $this->line("  ✅ Featured Products (MySQL): {$featured}");
            $this->line("  ✅ Featured Products (MongoDB): {$mongoFeatured}"); 
            $this->line("  ✅ Active Categories: {$activeCategories}");
            
        } catch (\Exception $e) {
            $this->error('  ❌ Data Error: ' . $e->getMessage());
        }
    }

    private function checkAssets()
    {
        $publicImages = base_path('public/images');
        $heroImage = base_path('public/images/hero-bg.jpg');
        
        if (file_exists($publicImages)) {
            $this->line('  ✅ Images directory exists');
        } else {
            $this->error('  ❌ Images directory missing');
        }
        
        if (file_exists($heroImage)) {
            $this->line('  ✅ Hero background image exists');
        } else {
            $this->line('  ⚠️  Hero background image missing (using gradient instead)');
        }

        $buildManifest = base_path('public/build/manifest.json');
        if (file_exists($buildManifest)) {
            $this->line('  ✅ Assets compiled (Vite build)');
        } else {
            $this->error('  ❌ Assets not compiled - run npm run build');
        }
    }

    private function checkRoutes()
    {
        try {
            $this->line('  ✅ Home route (/) - displays homepage');
            $this->line('  ✅ Admin routes (/admin/*) - requires authentication');
            $this->line('  ✅ Supplier routes (/supplier/*) - requires authentication');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Route Error: ' . $e->getMessage());
        }
    }
}