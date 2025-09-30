<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AdminDashboardController;

class TestAdminDashboard extends Command
{
    protected $signature = 'test:admin-dashboard';
    protected $description = 'Test admin dashboard data loading';

    public function handle()
    {
        $this->info('🏢 TESTING ADMIN DASHBOARD DATA');
        $this->newLine();

        try {
            // Create controller instance and call index method
            $controller = new AdminDashboardController();
            $response = $controller->index();
            
            // Get the data passed to the view
            $stats = $response->getData()['stats'];
            
            $this->line('✅ Dashboard Statistics:');
            $this->line("   - Total Users: {$stats['total_users']}");
            $this->line("   - Total Products: {$stats['total_products']}");
            $this->line("   - Total Orders: {$stats['total_orders']}");
            $this->line("   - Monthly Revenue: $" . number_format($stats['monthly_revenue'], 2));
            
            $this->newLine();
            $this->line('✅ Recent Data:');
            $this->line("   - Recent Users: {$stats['recent_users']->count()}");
            $this->line("   - Recent Suppliers: {$stats['recent_suppliers']->count()}");
            $this->line("   - Recent Orders: {$stats['recent_orders']->count()}");
            
            if ($stats['recent_orders']->isNotEmpty()) {
                $firstOrder = $stats['recent_orders']->first();
                $this->line("   - Latest Order: #{$firstOrder->id} - $" . number_format($firstOrder->total_amount, 2));
            }

            $this->newLine();
            $this->info('🎉 ALL ADMIN DASHBOARD DATA LOADED SUCCESSFULLY!');
            $this->comment('🔧 Admin Dashboard: http://127.0.0.1:8080/admin/dashboard');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}