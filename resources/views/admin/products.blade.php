<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manage Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Product Management</h1>
                            <p class="text-gray-400 mt-1">Manage your product catalog and inventory</p>
                        </div>
                        <button class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded-xl transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Product
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($products as $product)
                        <div class="bg-gray-900/50 border border-gray-700 rounded-xl overflow-hidden hover:border-yellow-400 transition-colors">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-800">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
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
                                    <span class="text-yellow-400 font-bold text-lg">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-sm px-2 py-1 rounded {{ $product->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-400 mb-3">
                                    Category: {{ $product->category->name }}
                                </div>
                                <div class="flex space-x-2">
                                    <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-lg text-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-lg text-sm transition-colors">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <div class="text-gray-400">
                                <i class="fas fa-box-open text-6xl mb-4"></i>
                                <p class="text-xl mb-2">No products found</p>
                                <p>Start by adding your first product to the catalog</p>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>