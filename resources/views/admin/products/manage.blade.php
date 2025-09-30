<x-admin-layout>
    <x-slot name="title">Product Management</x-slot>
    
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Product Management</h1>
                        <p class="mt-2 text-gray-400">Manage your product catalog</p>
                    </div>
                    <a href="{{ route('admin.products.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Product
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-400/20 rounded-lg">
                            <i class="fas fa-box text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-white">{{ $stats['total_products'] }}</h3>
                            <p class="text-gray-400">Total Products</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-400/20 rounded-lg">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-white">{{ $stats['active_products'] }}</h3>
                            <p class="text-gray-400">Active Products</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-400/20 rounded-lg">
                            <i class="fas fa-star text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-white">{{ $stats['featured_products'] }}</h3>
                            <p class="text-gray-400">Featured Products</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-400/20 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-white">{{ $stats['low_stock'] }}</h3>
                            <p class="text-gray-400">Low Stock</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="bg-gray-800 rounded-xl p-6 mb-8">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Search Products</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Product name or SKU"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                        <select name="category" 
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" 
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.products.manage') }}" 
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Products Table -->
            <div class="bg-gray-800 rounded-xl overflow-hidden">
                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Product</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Category</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Price</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Stock</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($products as $product)
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center mr-4">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-white">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-400">SKU: {{ $product->sku ?? 'N/A' }}</div>
                                                @if(($product->is_featured ?? false) || ($product->featured ?? false))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Featured
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gray-300">{{ $product->category_name ?? 'No Category' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium">${{ number_format($product->price ?? 0, 2) }}</div>
                                        @if(isset($product->compare_price) && $product->compare_price > ($product->price ?? 0))
                                            <div class="text-sm text-gray-400 line-through">${{ number_format($product->compare_price, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-white">{{ $product->stock_quantity ?? 0 }}</div>
                                        @if(($product->stock_quantity ?? 0) <= 10)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                                Low Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($product->status === 'active' || $product->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-2 h-2 bg-gray-400 rounded-full mr-1"></div>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- View Button -->
                                            <a href="#" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition-colors">
                                                <i class="fas fa-eye mr-1"></i>
                                                View
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.products.edit', $product->_id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-md transition-colors">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.products.delete', $product->_id) }}" method="POST" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('Are you sure you want to delete {{ $product->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Delete
                                                </button>
                                            </form>

                                            <!-- Quick Actions Dropdown -->
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" 
                                                        class="inline-flex items-center px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-md transition-colors">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>

                                                <div x-show="open" @click.away="open = false"
                                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10"
                                                     style="display: none;">
                                                    <div class="py-1">
                                                        <form action="{{ route('admin.products.update', $product) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="is_active" value="{{ $product->is_active ? '0' : '1' }}">
                                                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                                {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                                            </button>
                                                        </form>

                                                        <form action="{{ route('admin.products.update', $product) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="is_featured" value="{{ $product->is_featured ? '0' : '1' }}">
                                                            <button type="submit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                                {{ $product->is_featured ? 'Remove from Featured' : 'Add to Featured' }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="px-6 py-4 border-t border-gray-700">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-white mb-2">No products found</h3>
                        <p class="text-gray-400 mb-6">Get started by creating your first product.</p>
                        <a href="{{ route('admin.products.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add Your First Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>