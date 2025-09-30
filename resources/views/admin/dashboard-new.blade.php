@extends('layouts.admin-new')

@section('title', 'Dashboard Overview')
@section('subtitle', 'Monitor and manage your Vosiz platform')

@section('content')
<div class="space-y-8">
    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="admin-stat-card p-6 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total Users</p>
                    <p class="text-white text-3xl font-bold mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-green-400 text-sm mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $stats['recent_users']->count() }} this week
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="admin-stat-card p-6 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total Products</p>
                    <p class="text-white text-3xl font-bold mt-2">{{ $stats['total_products'] }}</p>
                    @if(isset($stats['total_mongo_products']))
                    <p class="text-amber-400 text-sm mt-1">
                        <i class="fas fa-database mr-1"></i>
                        +{{ $stats['total_mongo_products'] }} MongoDB
                    </p>
                    @endif
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-box-open text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="admin-stat-card p-6 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Total Orders</p>
                    <p class="text-white text-3xl font-bold mt-2">{{ $stats['total_orders'] }}</p>
                    <p class="text-blue-400 text-sm mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $stats['today_orders'] }} today
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-shopping-cart text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="admin-stat-card p-6 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Monthly Revenue</p>
                    <p class="text-white text-3xl font-bold mt-2">${{ number_format($stats['monthly_revenue'], 2) }}</p>
                    <p class="text-purple-400 text-sm mt-1">
                        <i class="fas fa-chart-line mr-1"></i>
                        {{ date('F Y') }}
                    </p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-dollar-sign text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Users -->
        <div class="admin-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-white">Recent Users</h3>
                    <p class="text-gray-400 text-sm">Latest customer registrations</p>
                </div>
                <a href="{{ route('admin.users') }}" class="text-amber-400 hover:text-amber-300 text-sm font-semibold transition-colors">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @forelse($stats['recent_users'] as $user)
                <div class="flex items-center p-4 bg-gray-800 bg-opacity-30 rounded-xl hover:bg-opacity-50 transition-colors">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <span class="text-white font-bold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-semibold">{{ $user->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                        <p class="text-gray-500 text-xs">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 bg-green-500 bg-opacity-20 text-green-400 text-xs rounded-full font-semibold">
                            Active
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-600 text-4xl mb-4"></i>
                    <p class="text-gray-400">No recent users</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="admin-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-white">Recent Orders</h3>
                    <p class="text-gray-400 text-sm">Latest customer orders</p>
                </div>
                <a href="{{ route('admin.orders') }}" class="text-amber-400 hover:text-amber-300 text-sm font-semibold transition-colors">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @forelse($stats['recent_orders'] as $order)
                <div class="flex items-center p-4 bg-gray-800 bg-opacity-30 rounded-xl hover:bg-opacity-50 transition-colors">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-shopping-bag text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-semibold">Order #{{ $order->id }}</p>
                        <p class="text-gray-400 text-sm">{{ $order->user->name ?? 'Guest Customer' }}</p>
                        <p class="text-gray-500 text-xs">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-green-400 font-bold">${{ number_format($order->total_amount, 2) }}</p>
                        <span class="inline-flex px-2 py-1 bg-blue-500 bg-opacity-20 text-blue-400 text-xs rounded-full font-semibold">
                            {{ ucfirst($order->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-600 text-4xl mb-4"></i>
                    <p class="text-gray-400">No orders yet</p>
                    <p class="text-gray-500 text-sm mt-2">Orders will appear here once customers start purchasing</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- System Status & Quick Actions -->
        <div class="admin-card rounded-2xl p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-white">System Status</h3>
                <p class="text-gray-400 text-sm">Platform health & quick actions</p>
            </div>
            
            <div class="space-y-4">
                <!-- Database Status -->
                <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-database text-green-400 mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">Database</p>
                                <p class="text-gray-400 text-sm">MySQL + MongoDB</p>
                            </div>
                        </div>
                        <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
                    </div>
                </div>

                <!-- Server Status -->
                <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-server text-blue-400 mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">Laravel Server</p>
                                <p class="text-gray-400 text-sm">{{ config('app.env') }} mode</p>
                            </div>
                        </div>
                        <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
                    </div>
                </div>

                <!-- Cache Status -->
                <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-memory text-purple-400 mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">Cache</p>
                                <p class="text-gray-400 text-sm">Application cache</p>
                            </div>
                        </div>
                        <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="border-t border-gray-800 pt-4 mt-6">
                    <p class="text-gray-400 text-sm font-semibold uppercase tracking-wide mb-3">Quick Actions</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.products.manage') }}" class="flex items-center p-3 bg-amber-500 bg-opacity-10 text-amber-400 rounded-lg hover:bg-opacity-20 transition-colors">
                            <i class="fas fa-plus mr-3"></i>
                            <span class="font-semibold">Add Product</span>
                        </a>
                        <a href="{{ route('admin.suppliers') }}" class="flex items-center p-3 bg-blue-500 bg-opacity-10 text-blue-400 rounded-lg hover:bg-opacity-20 transition-colors">
                            <i class="fas fa-user-plus mr-3"></i>
                            <span class="font-semibold">Add Supplier</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Suppliers -->
        <div class="admin-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-white">Recent Suppliers</h3>
                    <p class="text-gray-400 text-sm">Latest supplier registrations</p>
                </div>
                <a href="{{ route('admin.suppliers') }}" class="text-amber-400 hover:text-amber-300 text-sm font-semibold transition-colors">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @forelse($stats['recent_suppliers'] as $supplier)
                <div class="flex items-center p-4 bg-gray-800 bg-opacity-30 rounded-xl hover:bg-opacity-50 transition-colors">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-store text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-semibold">{{ $supplier->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $supplier->email }}</p>
                        <p class="text-gray-500 text-xs">{{ $supplier->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 bg-purple-500 bg-opacity-20 text-purple-400 text-xs rounded-full font-semibold">
                            Supplier
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-store text-gray-600 text-4xl mb-4"></i>
                    <p class="text-gray-400">No recent suppliers</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Platform Information -->
        <div class="admin-card rounded-2xl p-6">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-white">Platform Information</h3>
                <p class="text-gray-400 text-sm">System details and version info</p>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4 text-center">
                        <p class="text-gray-400 text-sm">Laravel Version</p>
                        <p class="text-white font-bold text-lg">{{ app()->version() }}</p>
                    </div>
                    <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4 text-center">
                        <p class="text-gray-400 text-sm">PHP Version</p>
                        <p class="text-white font-bold text-lg">{{ phpversion() }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4">
                    <p class="text-gray-400 text-sm mb-2">Environment</p>
                    <div class="flex items-center">
                        <span class="inline-flex px-3 py-1 bg-{{ config('app.env') === 'production' ? 'green' : 'amber' }}-500 bg-opacity-20 text-{{ config('app.env') === 'production' ? 'green' : 'amber' }}-400 text-sm rounded-full font-semibold">
                            {{ strtoupper(config('app.env')) }}
                        </span>
                        <span class="ml-auto text-white font-semibold">{{ config('app.name') }}</span>
                    </div>
                </div>

                <div class="bg-gray-800 bg-opacity-30 rounded-xl p-4">
                    <p class="text-gray-400 text-sm mb-2">Database Connections</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-white">MySQL</span>
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-white">MongoDB</span>
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection