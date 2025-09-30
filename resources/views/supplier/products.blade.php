<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('My Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-white">My Products</h1>
                            <p class="text-gray-400 mt-1">Manage your product listings and inventory</p>
                        </div>
                        <a href="{{ route('supplier.products.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-xl transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add New Product
                        </a>
                    </div>

                    <!-- Filter Options -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <select class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Categories</option>
                            <option value="skincare">Skincare</option>
                            <option value="haircare">Hair Care</option>
                            <option value="bodycare">Body Care</option>
                        </select>
                        <select class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <select class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Stock</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($products as $product)
                        <div class="bg-gray-900/50 border border-gray-700 rounded-xl overflow-hidden hover:border-green-400 transition-colors">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-800">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                @elseif($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold text-lg mb-2">{{ $product->name }}</h3>
                                <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ Str::limit($product->description, 100) }}</p>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-green-400 font-bold text-lg">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-sm px-2 py-1 rounded {{ $product->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between mb-3 text-sm text-gray-400">
                                    <span>Category: {{ $product->category->name ?? 'N/A' }}</span>
                                    <span class="flex items-center">
                                        <i class="fas fa-boxes mr-1"></i>
                                        Stock: {{ $product->stock_quantity ?? 0 }}
                                    </span>
                                </div>

                                <!-- Stock Status Indicator -->
                                @if(($product->stock_quantity ?? 0) == 0)
                                    <div class="mb-3 text-center">
                                        <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Out of Stock
                                        </span>
                                    </div>
                                @elseif(($product->stock_quantity ?? 0) < 10)
                                    <div class="mb-3 text-center">
                                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Low Stock
                                        </span>
                                    </div>
                                @endif

                                <div class="flex space-x-2">
                                    <a href="{{ route('supplier.products.edit', $product->id) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-lg text-sm transition-colors text-center">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form action="{{ route('supplier.products.toggle', $product->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full {{ $product->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600' }} text-white py-2 px-3 rounded-lg text-sm transition-colors">
                                            <i class="fas {{ $product->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                                            {{ $product->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('supplier.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-lg text-sm transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <div class="text-gray-400">
                                <i class="fas fa-box-open text-6xl mb-4"></i>
                                <p class="text-xl mb-2">No products found</p>
                                <p class="mb-6">Start building your catalog by adding your first product</p>
                                <a href="{{ route('supplier.products.create') }}" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add Your First Product
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-3 rounded-lg shadow-lg z-50" id="success-message">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                const message = document.getElementById('success-message');
                if (message) message.remove();
            }, 5000);
        </script>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-3 rounded-lg shadow-lg z-50" id="error-message">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                const message = document.getElementById('error-message');
                if (message) message.remove();
            }, 5000);
        </script>
    @endif
</x-app-layout>