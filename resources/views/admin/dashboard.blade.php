<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-purple-900 via-blue-900 to-indigo-900 overflow-hidden shadow-xl sm:rounded-lg mb-8 border border-purple-500/30">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-yellow-400 text-4xl mr-4 animate-pulse"></i>
                                <div>
                                    <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-yellow-300 bg-clip-text text-transparent">Welcome back, {{ Auth::user()->name }}!</h1>
                                    <p class="text-purple-200 mt-2 text-lg">Manage your VOSIZ platform from this premium admin dashboard</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-6 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-blue-100 text-sm font-medium">Total Users</p>
                            <p class="text-white text-3xl font-bold">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Products -->
                <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-6 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-green-100 text-sm font-medium">Total Products</p>
                            <p class="text-white text-3xl font-bold">{{ $stats['total_products'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-6 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-purple-100 text-sm font-medium">Total Orders</p>
                            <p class="text-white text-3xl font-bold">{{ $stats['total_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl p-6 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm">
                            <i class="fas fa-dollar-sign text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-yellow-100 text-sm font-medium">Monthly Revenue</p>
                            <p class="text-white text-3xl font-bold">${{ number_format($stats['monthly_revenue'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Manage Products -->
                <a href="{{ route('admin.products') }}" class="block bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl p-6 hover:from-indigo-500 hover:to-purple-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-bold text-lg">Products</h3>
                            <p class="text-indigo-100 text-sm">Manage product catalog</p>
                        </div>
                    </div>
                </a>

                <!-- Manage Users -->
                <a href="{{ route('admin.users') }}" class="block bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl p-6 hover:from-blue-500 hover:to-cyan-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-bold text-lg">Users</h3>
                            <p class="text-blue-100 text-sm">Manage customers & suppliers</p>
                        </div>
                    </div>
                </a>

                <!-- View Orders -->
                <a href="{{ route('admin.orders') }}" class="block bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl p-6 hover:from-green-500 hover:to-emerald-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-bold text-lg">Orders</h3>
                            <p class="text-green-100 text-sm">Track & process orders</p>
                        </div>
                    </div>
                </a>

                <!-- Advanced Products -->
                <a href="{{ route('admin.mongo-products') }}" class="block bg-gradient-to-br from-red-600 to-pink-600 rounded-xl p-6 hover:from-red-500 hover:to-pink-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-database text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-bold text-lg">Advanced Products</h3>
                            <p class="text-red-100 text-sm">MongoDB product management</p>
                        </div>
                    </div>
                </a>

                <!-- Video Management -->
                <a href="{{ route('admin.videos') }}" class="block bg-gradient-to-br from-yellow-600 to-orange-600 rounded-xl p-6 hover:from-yellow-500 hover:to-orange-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-video text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-bold text-lg">Videos</h3>
                            <p class="text-yellow-100 text-sm">Manage promotional videos</p>
                        </div>
                    </div>
                </a>

                <!-- Supplier Management -->
                <a href="{{ route('admin.suppliers') }}" class="block bg-gradient-to-br from-teal-600 to-blue-600 rounded-xl p-6 hover:from-teal-500 hover:to-blue-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-white/20 backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-store text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-white font-bold text-lg">Suppliers</h3>
                            <p class="text-teal-100 text-sm">Manage supplier accounts</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Users -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-purple-500/30 rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-users text-blue-400 mr-3"></i>
                            Recent Users
                        </h3>
                        <a href="{{ route('admin.users') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['recent_users'] as $user)
                        <div class="flex items-center justify-between py-3 px-4 bg-slate-700/50 rounded-lg border border-slate-600/50 hover:bg-slate-700/70 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-white font-semibold">{{ $user->name }}</p>
                                    <p class="text-slate-300 text-sm">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-slate-400 text-xs bg-slate-600/50 px-2 py-1 rounded">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-users text-slate-600 text-4xl mb-3"></i>
                            <p class="text-slate-400">No recent users</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-purple-500/30 rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-shopping-cart text-green-400 mr-3"></i>
                            Recent Orders
                        </h3>
                        <a href="{{ route('admin.orders') }}" class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['recent_orders'] as $order)
                        <div class="flex items-center justify-between py-3 px-4 bg-slate-700/50 rounded-lg border border-slate-600/50 hover:bg-slate-700/70 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-bag text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-white font-semibold">#{{ $order->id }}</p>
                                    <p class="text-slate-300 text-sm">{{ $order->user->name ?? 'Guest' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-yellow-400 font-bold">${{ number_format($order->total_amount, 2) }}</span>
                                <p class="text-slate-400 text-xs">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-slate-600 text-4xl mb-3"></i>
                            <p class="text-slate-400">No recent orders</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>