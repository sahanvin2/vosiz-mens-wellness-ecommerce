<x-app-layout>
    <x-slot name="title">Shop All Products</x-slot>
    
    <!-- Products Header -->
    <section class="bg-gradient-to-r from-gray-900 via-black to-gray-900 py-16 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold bg-gradient-to-r from-white via-vosiz-silver to-vosiz-gold bg-clip-text text-transparent mb-4">
                    Premium Men's Products
                </h1>
                <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                    Discover our curated collection of high-quality men's health & wellness products
                </p>
            </div>
        </div>
    </section>

    <!-- Filter Bar -->
    <section class="bg-black/50 backdrop-blur-lg border-b border-gray-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <!-- Category Filter -->
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-gray-400 text-sm font-medium">Categories:</span>
                    <a href="{{ route('products.index') }}" 
                       class="px-4 py-2 rounded-full text-sm {{ !request('category') ? 'bg-vosiz-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} transition-colors">
                        All Products
                    </a>
                    @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                       class="px-4 py-2 rounded-full text-sm {{ request('category') == $category->id ? 'bg-vosiz-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }} transition-colors">
                        {{ $category->name }}
                    </a>
                    @endforeach
                </div>
                
                <!-- Search & Sort Options -->
                <div class="flex items-center space-x-4">
                    <form method="GET" action="{{ route('products.index') }}" class="flex items-center space-x-2">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search products..."
                               class="bg-gray-800 border border-gray-600 rounded-lg px-4 py-2 text-white text-sm focus:ring-vosiz-gold focus:border-vosiz-gold">
                        <select name="sort" class="bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm focus:ring-vosiz-gold focus:border-vosiz-gold">
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        </select>
                        <button type="submit" class="bg-vosiz-gold text-black px-4 py-2 rounded-lg font-medium hover:bg-vosiz-gold/80 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="py-12 bg-gradient-to-b from-gray-900 to-black min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($products->count() > 0)
                <!-- Results Info -->
                <div class="flex justify-between items-center mb-8">
                    <p class="text-gray-400">
                        Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} products
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($products as $product)
                    <div class="group bg-gray-800/30 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden hover:border-vosiz-silver/50 transition-all duration-300 transform hover:scale-105">
                        <a href="{{ route('products.show', $product->_id) }}" class="block">
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
                                
                                <!-- Badges -->
                                <div class="absolute top-4 left-4 flex flex-col space-y-2">
                                    @if($product->is_featured)
                                    <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">FEATURED</span>
                                    @endif
                                    @if($product->discount_percentage > 0)
                                    <span class="bg-vosiz-gold text-black px-2 py-1 rounded text-xs font-bold">-{{ $product->discount_percentage }}%</span>
                                    @endif
                                </div>
                                
                                <!-- Quick Action Buttons -->
                                <div class="absolute top-4 right-4 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="bg-black/80 text-white p-2 rounded-full hover:bg-vosiz-gold hover:text-black transition-colors">
                                        <i class="fas fa-heart text-sm"></i>
                                    </button>
                                    <button class="bg-black/80 text-white p-2 rounded-full hover:bg-vosiz-gold hover:text-black transition-colors">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                        
                        <div class="p-6">
                            <div class="text-xs text-vosiz-gold mb-2">{{ $product->category_name ?? 'Uncategorized' }}</div>
                            <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-vosiz-silver transition-colors line-clamp-2">
                                <a href="{{ route('products.show', $product->_id) }}" class="hover:text-vosiz-gold">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                                {{ \Illuminate\Support\Str::limit($product->description, 100) }}
                            </p>
                            
                            <!-- Rating (placeholder) -->
                            <div class="flex items-center mb-3">
                                <div class="flex text-vosiz-gold text-xs">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-gray-500 text-xs ml-2">({{ rand(10, 150) }} reviews)</span>
                            </div>
                            
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-bold text-white">${{ number_format(floatval($product->price), 2) }}</span>
                                    @if($product->compare_price)
                                    <span class="text-sm text-gray-500 line-through">${{ number_format(floatval($product->compare_price), 2) }}</span>
                                    @endif
                                </div>
                                
                                @if(($product->stock_quantity ?? 0) > 0)
                                    <span class="text-green-400 text-xs">{{ $product->stock_quantity }} left</span>
                                @else
                                    <span class="text-red-400 text-xs">Out of Stock</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('products.show', $product->_id) }}" 
                                   class="flex-1 bg-vosiz-gold text-black py-3 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors transform hover:scale-105 text-center">
                                    View Details
                                </a>
                                
                                @auth
                                    @if(($product->stock_quantity ?? 0) > 0)
                                        <button data-product-id="{{ $product->_id }}" onclick="addToCart(this.dataset.productId)" class="bg-transparent border border-vosiz-gold text-vosiz-gold px-4 py-3 rounded-lg font-semibold hover:bg-vosiz-gold hover:text-black transition-colors transform hover:scale-105">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    @else
                                        <button disabled class="bg-gray-600 text-gray-400 px-4 py-3 rounded-lg font-semibold opacity-50 cursor-not-allowed">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="bg-gray-700 text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-user"></i>
                                    </a>
                                @endauth
                            </div>
                            
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
                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <i class="fas fa-search text-gray-600 text-6xl mb-4"></i>
                    <h3 class="text-2xl font-bold text-white mb-2">No Products Found</h3>
                    <p class="text-gray-400 mb-8">Try adjusting your filters or browse our categories.</p>
                    <a href="{{ route('products.index') }}" class="bg-vosiz-gold text-black px-8 py-4 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors">
                        View All Products
                    </a>
                </div>
            @endif
        </div>
    </section>

    <script>
        function addToCart(productId) {
            // Simple cart functionality - you can expand this
            alert('Product added to cart! Product ID: ' + productId);
            // TODO: Implement proper cart functionality
        }
    </script>
</x-app-layout>