<x-app-layout>
    <x-slot name="title">Home</x-slot>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-black via-gray-900 to-black">
        <!-- Background Video/Image Container -->
        <div class="absolute inset-0">
            <!-- Video Background (uncomment to use) -->
            {{-- 
            <video autoplay muted loop class="w-full h-full object-cover opacity-30">
                <source src="{{ asset('videos/hero-background.mp4') }}" type="video/mp4">
                <source src="{{ asset('videos/hero-background.webm') }}" type="video/webm">
                Your browser does not support the video tag.
            </video>
            --}}
            
            <!-- Gradient Background (no image needed) -->
            <div class="w-full h-full bg-gradient-to-br from-gray-800/50 via-gray-900/50 to-black/50">
            </div>
            
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 z-10">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 animate-fade-in">
                    <span class="bg-gradient-to-r from-white via-vosiz-silver to-white bg-clip-text text-transparent">
                        VOSIZ
                    </span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto animate-slide-up">
                    Premium Men's Health & Wellness
                </p>
                <p class="text-lg text-gray-400 mb-12 max-w-2xl mx-auto animate-slide-up">
                    Discover our luxury collection of skincare, body care, and grooming essentials designed for the modern gentleman.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up">
                    <a href="#featured" class="bg-white text-black px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 transform hover:scale-105">
                        Shop Collection
                    </a>
                    <a href="#categories" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-black transition duration-300 transform hover:scale-105">
                        Explore Categories
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-20 h-20 bg-vosiz-gold/10 rounded-full blur-xl"></div>
            <div class="absolute bottom-20 right-20 w-32 h-32 bg-vosiz-silver/10 rounded-full blur-2xl"></div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-20 bg-gradient-to-b from-black to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Premium Categories</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Explore our carefully curated collections designed for every aspect of men's grooming and wellness.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($categories as $category)
                <div class="group relative overflow-hidden rounded-xl bg-gray-800/50 backdrop-blur-sm border border-gray-700 hover:border-vosiz-silver/50 transition-all duration-300 transform hover:scale-105">
                    <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-gray-800 to-gray-900">
                        <div class="p-8 flex flex-col justify-center items-center text-center">
                            <div class="w-16 h-16 bg-vosiz-gold/20 rounded-full flex items-center justify-center mb-4 group-hover:bg-vosiz-gold/30 transition-colors">
                                <i class="fas fa-star text-vosiz-gold text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-semibold text-white mb-3">{{ $category->name }}</h3>
                            <p class="text-gray-400 mb-6">{{ $category->description }}</p>
                            <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center text-vosiz-gold hover:text-white transition-colors">
                                Shop Now
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-20 bg-gradient-to-b from-gray-900 to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Featured Products</h2>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto">
                    Our premium selection of best-selling products, chosen by skincare experts.
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredProducts as $product)
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
                            @if($product->is_featured)
                            <div class="absolute top-4 right-4 bg-vosiz-gold text-black px-3 py-1 rounded-full text-sm font-bold">
                                <i class="fas fa-star mr-1"></i>Featured
                            </div>
                            @endif
                        </div>
                    </a>
                    
                    <div class="p-6">
                        <div class="text-sm text-vosiz-gold mb-2">
                            {{ $product->category_name ?? 'Uncategorized' }}
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-3 group-hover:text-vosiz-silver transition-colors">
                            <a href="{{ route('products.show', $product->_id) }}" class="hover:text-vosiz-gold">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit($product->description ?? 'Premium men\'s wellness product', 100) }}
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl font-bold text-white">${{ number_format(floatval($product->price ?? 0), 2) }}</span>
                                @if(!empty($product->compare_price) && $product->compare_price > $product->price)
                                <span class="text-sm text-gray-500 line-through">${{ number_format(floatval($product->compare_price), 2) }}</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('products.show', $product->_id) }}" 
                                   class="bg-vosiz-gold text-black px-4 py-2 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors transform hover:scale-105">
                                    View Details
                                </a>
                                @auth
                                    @if(($product->stock_quantity ?? 0) > 0)
                                        <button data-product-id="{{ $product->_id }}" onclick="addToCart(this.dataset.productId)"
                                                class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition-colors">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    @else
                                        <button disabled class="bg-gray-600 text-gray-400 px-3 py-2 rounded-lg font-semibold opacity-50 cursor-not-allowed">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="bg-gray-700 text-white px-3 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-user"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                        
                        @if(!empty($product->tags) && is_array($product->tags))
                        <div class="mt-4 pt-4 border-t border-gray-700">
                            <div class="text-xs text-gray-400 mb-1">Tags:</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($product->tags, 0, 3) as $tag)
                                <span class="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('products.index') }}" class="bg-white text-black px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 transform hover:scale-105">
                    View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-20 bg-black">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Stay Updated</h2>
            <p class="text-xl text-gray-400 mb-8">
                Get the latest updates on new products, exclusive offers, and grooming tips.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg bg-gray-800 border border-gray-600 text-white placeholder-gray-400 focus:border-vosiz-gold focus:ring-1 focus:ring-vosiz-gold">
                <button class="bg-vosiz-gold text-black px-6 py-3 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors">
                    Subscribe
                </button>
            </div>
        </div>
    </section>

    <script>
        function addToCart(productId) {
            // Simple cart functionality - you can expand this
            alert('Product added to cart! Product ID: ' + productId);
            // TODO: Implement proper cart functionality with AJAX
            
            // You can add AJAX call here to actually add to cart
            // fetch('/api/cart/add', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            //     },
            //     body: JSON.stringify({ product_id: productId, quantity: 1 })
            // }).then(response => response.json()).then(data => {
            //     console.log('Added to cart:', data);
            // });
        }
    </script>
</x-app-layout>