<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Supplier Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <div class="flex items-center">
                                <i class="fas fa-store text-green-400 text-3xl mr-4"></i>
                                <div>
                                    <h1 class="text-2xl font-bold text-white">Welcome back, {{ Auth::user()->name }}!</h1>
                                    <p class="text-gray-300 mt-2">Manage your products and orders on VOSIZ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- MySQL Products -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500/20">
                            <i class="fas fa-box text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Your Products</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['mysql_products'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500/20">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Active Products</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['mysql_active'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500/20">
                            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Low Stock Items</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['low_stock'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- MongoDB Products -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-500/20">
                            <i class="fas fa-database text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">MongoDB Products</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['mongo_products'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Recent Products -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Recent Products</h3>
                        <a href="{{ route('supplier.products') }}" class="text-green-400 hover:text-green-300 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse($stats['recent_products'] as $product)
                        <div class="flex items-center justify-between py-2 border-b border-gray-700">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center overflow-hidden">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-box text-gray-300 text-sm"></i>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-white font-medium">{{ $product->name }}</p>
                                    <p class="text-gray-400 text-sm">${{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-gray-300 text-sm">Stock: {{ $product->stock_quantity }}</span>
                                <p class="text-gray-400 text-xs">{{ $product->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-gray-500 text-4xl mb-4"></i>
                            <p class="text-gray-400">No products yet</p>
                            <a href="{{ route('supplier.products.create') }}" class="text-green-400 hover:text-green-300 text-sm font-medium mt-2 inline-block">
                                Add Your First Product <i class="fas fa-plus ml-1"></i>
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Stats & Actions -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                    <div class="space-y-4">
                        <!-- Add Product -->
                        <a href="{{ route('supplier.products.create') }}" class="flex items-center p-4 bg-green-500/10 border border-green-500/20 rounded-lg hover:bg-green-500/20 transition-colors group">
                            <div class="p-2 rounded-lg bg-green-500/20 group-hover:bg-green-500/30 transition-colors">
                                <i class="fas fa-plus text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">Add New Product</h4>
                                <p class="text-gray-400 text-sm">Create a new product listing</p>
                            </div>
                        </a>

                        <!-- Manage Inventory -->
                        <a href="{{ route('supplier.products') }}" class="flex items-center p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg hover:bg-blue-500/20 transition-colors group">
                            <div class="p-2 rounded-lg bg-blue-500/20 group-hover:bg-blue-500/30 transition-colors">
                                <i class="fas fa-warehouse text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">Manage Inventory</h4>
                                <p class="text-gray-400 text-sm">Update stock and pricing</p>
                            </div>
                        </a>

                        <!-- View Orders -->
                        <a href="{{ route('supplier.orders') }}" class="flex items-center p-4 bg-purple-500/10 border border-purple-500/20 rounded-lg hover:bg-purple-500/20 transition-colors group">
                            <div class="p-2 rounded-lg bg-purple-500/20 group-hover:bg-purple-500/30 transition-colors">
                                <i class="fas fa-shopping-cart text-purple-400 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">View Orders</h4>
                                <p class="text-gray-400 text-sm">Track and fulfill orders</p>
                            </div>
                        </a>

                        <!-- Performance Analytics -->
                        <div class="flex items-center p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                            <div class="p-2 rounded-lg bg-yellow-500/20">
                                <i class="fas fa-chart-line text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-white font-medium">Performance</h4>
                                <p class="text-gray-400 text-sm">Coming soon - sales analytics</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert Section -->
            @if($stats['low_stock'] > 0)
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6 mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-white">Low Stock Alert</h3>
                </div>
                <p class="text-gray-300 mb-4">You have {{ $stats['low_stock'] }} product(s) with low stock (less than 10 units).</p>
                <a href="{{ route('supplier.products') }}?filter=low_stock" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black font-medium rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    View Low Stock Items
                </a>
            </div>
            @endif

            <!-- Tips for Suppliers -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                    Tips for Success
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <i class="fas fa-camera text-green-400 text-lg mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-white font-medium">High-Quality Images</h4>
                            <p class="text-gray-400 text-sm">Use clear, well-lit product photos to increase sales</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-edit text-blue-400 text-lg mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-white font-medium">Detailed Descriptions</h4>
                            <p class="text-gray-400 text-sm">Include benefits, ingredients, and usage instructions</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-tags text-purple-400 text-lg mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-white font-medium">Competitive Pricing</h4>
                            <p class="text-gray-400 text-sm">Research market prices to stay competitive</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-shipping-fast text-orange-400 text-lg mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-white font-medium">Fast Shipping</h4>
                            <p class="text-gray-400 text-sm">Quick fulfillment leads to better customer reviews</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>