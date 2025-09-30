<x-app-layout>
    <x-slot name="title">{{ $category->name }} - Premium Men's Products</x-slot>

    <!-- Category Header -->
    <section class="relative bg-gradient-to-br from-black via-gray-900 to-black py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex items-center space-x-2 text-gray-400 mb-4">
                        <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <a href="{{ route('categories.index') }}" class="hover:text-white transition">Categories</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-vosiz-gold">{{ $category->name }}</span>
                    </nav>
                    
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                        <span class="bg-gradient-to-r from-white via-vosiz-silver to-vosiz-gold bg-clip-text text-transparent">
                            {{ $category->name }}
                        </span>
                    </h1>
                    <p class="text-xl text-gray-300 max-w-2xl">{{ $category->description }}</p>
                </div>
                
                <!-- Category Stats -->
                <div class="hidden md:block text-right">
                    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6">
                        <div class="text-3xl font-bold text-vosiz-gold">{{ $products->total() }}</div>
                        <div class="text-gray-400">Premium Products</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters & Sort -->
    <section class="bg-black/50 backdrop-blur-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                <!-- Filter Options -->
                <div class="flex items-center space-x-4">
                    <span class="text-gray-400 text-sm">Filter by:</span>
                    <select class="bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:ring-vosiz-gold focus:border-vosiz-gold">
                        <option>All Prices</option>
                        <option>Under $25</option>
                        <option>$25 - $50</option>
                        <option>$50 - $100</option>
                        <option>Over $100</option>
                    </select>
                    
                    <select class="bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:ring-vosiz-gold focus:border-vosiz-gold">
                        <option>All Skin Types</option>
                        <option>Dry</option>
                        <option>Oily</option>
                        <option>Combination</option>
                        <option>Sensitive</option>
                    </select>
                </div>
                
                <!-- Sort Options -->
                <div class="flex items-center space-x-4">
                    <span class="text-gray-400 text-sm">Sort by:</span>
                    <select class="bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:ring-vosiz-gold focus:border-vosiz-gold">
                        <option>Featured</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Newest</option>
                        <option>Best Sellers</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-12 bg-gradient-to-b from-gray-900 to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($products as $product)
                    <div class="group bg-gray-800/30 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden hover:border-vosiz-silver/50 transition-all duration-300 transform hover:scale-105">
                        <div class="aspect-w-1 aspect-h-1 bg-gradient-to-br from-gray-700 to-gray-800 relative">
                            <div class="w-full h-64 bg-gray-600 flex items-center justify-center">
                                @if(!empty($product->images) && isset($product->images[0]))
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                @endif
                            </div>
                            
                            @if(!empty($product->sale_price) && $product->sale_price < $product->price)
                            <div class="absolute top-4 right-4 bg-vosiz-gold text-black px-3 py-1 rounded-full text-sm font-bold">
                                SALE
                            </div>
                            @endif
                            
                            @if($product->is_featured)
                            <div class="absolute top-4 left-4 bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">
                                FEATURED
                            </div>
                            @endif
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-vosiz-silver transition-colors line-clamp-2">
                                {{ $product->name }}
                            </h3>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $product->short_description }}</p>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    @if(!empty($product->sale_price) && $product->sale_price < $product->price)
                                        <span class="text-xl font-bold text-vosiz-gold">${{ number_format(floatval($product->sale_price), 2) }}</span>
                                        <span class="text-sm text-gray-500 line-through">${{ number_format(floatval($product->price), 2) }}</span>
                                    @else
                                        <span class="text-xl font-bold text-white">${{ number_format(floatval($product->price), 2) }}</span>
                                    @endif
                                </div>
                                
                                @if(($product->stock_quantity ?? 0) > 0)
                                    <span class="text-green-400 text-xs">In Stock</span>
                                @else
                                    <span class="text-red-400 text-xs">Out of Stock</span>
                                @endif
                            </div>
                            
                            @auth
                                @if(($product->stock_quantity ?? 0) > 0)
                                    <a href="{{ route('products.show', $product->_id) }}" class="block w-full bg-vosiz-gold text-black py-3 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors text-center transform hover:scale-105">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Details
                                    </a>
                                @else
                                    <button disabled class="w-full bg-gray-600 text-gray-400 py-3 rounded-lg font-semibold opacity-50 cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-gray-700 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors text-center">
                                    Login to View Details
                                </a>
                            @endauth
                            
                            @if($product->ingredients)
                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <div class="text-xs text-gray-400 mb-2">Key Ingredients:</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($product->ingredients, 0, 3) as $ingredient)
                                    <span class="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded">{{ $ingredient }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <i class="fas fa-box-open text-gray-600 text-6xl mb-4"></i>
                    <h3 class="text-2xl font-bold text-white mb-2">No Products Found</h3>
                    <p class="text-gray-400 mb-8">We're working on adding more products to this category.</p>
                    <a href="{{ route('categories.index') }}" class="bg-vosiz-gold text-black px-8 py-4 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors">
                        Browse Other Categories
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Related Categories -->
    <section class="py-16 bg-black border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-8 text-center">Explore Related Categories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $relatedCategories = \App\Models\Category::where('id', '!=', $category->id)
                        ->where('is_active', true)
                        ->limit(4)
                        ->get();
                @endphp
                
                @foreach($relatedCategories as $relatedCategory)
                <a href="{{ route('categories.show', $relatedCategory->slug) }}" 
                   class="group bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-6 text-center hover:border-vosiz-gold/50 transition-all duration-300">
                    <div class="w-12 h-12 bg-vosiz-gold/20 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-vosiz-gold/30 transition-colors">
                        <i class="fas fa-star text-vosiz-gold"></i>
                    </div>
                    <h4 class="font-semibold text-white group-hover:text-vosiz-gold transition-colors">{{ $relatedCategory->name }}</h4>
                </a>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>