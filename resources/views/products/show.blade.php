<x-app-layout>
    <div class="min-h-screen bg-black">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            
            <!-- Breadcrumb -->
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-yellow-400 transition-colors">
                            <i class="fas fa-home mr-1"></i>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-600 text-xs mx-2"></i>
                            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-yellow-400 transition-colors">Products</a>
                        </div>
                    </li>
                    @if($product->category_name)
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-600 text-xs mx-2"></i>
                            <span class="text-gray-400">{{ $product->category_name }}</span>
                        </div>
                    </li>
                    @endif
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-600 text-xs mx-2"></i>
                            <span class="text-gray-500">{{ Str::limit($product->name, 40) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Main Product Section -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Side - Image Gallery -->
                <div class="lg:col-span-6">
                    <div class="flex gap-4">
                        <!-- Thumbnail Images (Left) -->
                        <div class="flex flex-col space-y-3 w-20">
                            @if(!empty($product->images) && isset($product->images[0]))
                                <div class="w-20 h-20 bg-gray-800 rounded-lg overflow-hidden cursor-pointer border-2 border-yellow-400 thumbnail-container" data-image="{{ asset('storage/' . $product->images[0]) }}" onclick="changeMainImage(this.dataset.image)">
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            @if(!empty($product->images))
                                @foreach(array_slice($product->images, 1, 4) as $image)
                                <div class="w-20 h-20 bg-gray-800 rounded-lg overflow-hidden cursor-pointer border border-gray-700 thumbnail-container" data-image="{{ asset('storage/' . $image) }}" onclick="changeMainImage(this.dataset.image)">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                @endforeach
                            @endif
                            @for($i = count($product->images ?? []); $i < 5; $i++)
                            <div class="w-20 h-20 bg-gray-800 rounded-lg flex items-center justify-center border border-gray-700 thumbnail-container opacity-50">
                                <i class="fas fa-image text-gray-600 text-sm"></i>
                            </div>
                            @endfor
                        </div>
                        
                        <!-- Main Image (Right) -->
                        <div class="flex-1">
                            <div class="aspect-square bg-gray-800 rounded-xl overflow-hidden max-w-lg mx-auto">
                                @if(!empty($product->images) && isset($product->images[0]))
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                         id="mainImage">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <div class="text-center">
                                            <i class="fas fa-image text-gray-600 text-6xl mb-3"></i>
                                            <p class="text-gray-500">No image available</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Product Details -->
                <div class="lg:col-span-6">
                    
                    <!-- Product Title -->
                    <h1 class="text-2xl lg:text-3xl font-bold text-white mb-4 leading-tight">{{ $product->name }}</h1>
                    
                    <!-- Rating & Reviews -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-600' }} text-sm"></i>
                            @endfor
                            <span class="text-yellow-400 font-semibold ml-2 text-lg">4.8</span>
                        </div>
                        <span class="text-gray-400">({{ rand(50, 500) }} reviews)</span>
                        <span class="text-gray-400">|</span>
                        <span class="text-green-400 font-medium">{{ rand(100, 1000) }}+ sold</span>
                        @if($product->category_name)
                            <span class="text-gray-400">|</span>
                            <span class="text-yellow-400 text-sm">{{ $product->category_name }}</span>
                        @endif
                    </div>

                    <!-- Price Section - AliExpress Style -->
                    <div class="bg-gray-900 rounded-xl p-6 mb-6 border border-gray-800">
                        <div class="mb-4">
                            <div class="flex items-baseline space-x-3">
                                <span class="text-4xl font-bold text-yellow-400">${{ number_format((float)$product->price, 2) }}</span>
                                @if($product->compare_price && $product->compare_price > $product->price)
                                    <span class="text-lg text-gray-400 line-through">${{ number_format((float)$product->compare_price, 2) }}</span>
                                @endif
                            </div>
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <div class="mt-2">
                                    <span class="inline-block px-3 py-1 bg-red-600 text-white text-sm font-bold rounded-md">
                                        -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}% OFF
                                    </span>
                                    <span class="text-gray-400 text-sm ml-2">Limited time offer</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Shipping Info -->
                        <div class="border-t border-gray-800 pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div class="flex items-center text-gray-300">
                                    <i class="fas fa-shipping-fast text-green-400 mr-2"></i>
                                    <span>Free shipping</span>
                                </div>
                                <div class="flex items-center text-gray-300">
                                    <i class="fas fa-undo text-blue-400 mr-2"></i>
                                    <span>30-day return</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock & Quantity -->
                    <div class="mb-6">
                        <!-- Stock Status -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @if($product->stock_quantity > 0)
                                    <div class="flex items-center text-green-400">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                        <span class="font-medium">In Stock</span>
                                        <span class="text-gray-400 ml-2">({{ $product->stock_quantity }} available)</span>
                                    </div>
                                @else
                                    <div class="flex items-center text-red-400">
                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                        <span class="font-medium">Out of Stock</span>
                                    </div>
                                @endif
                            </div>
                            @if($product->stock_quantity > 0 && $product->stock_quantity <= 10)
                                <span class="text-orange-400 text-sm font-medium animate-pulse">Only {{ $product->stock_quantity }} left!</span>
                            @endif
                        </div>
                        
                        <!-- Quantity Selector -->
                        @auth
                            @if($product->stock_quantity > 0)
                                <div class="mb-6">
                                    <label class="block text-white font-medium mb-3">Quantity:</label>
                                    <div class="flex items-center">
                                        <div class="flex items-center bg-gray-800 rounded-lg border border-gray-700 mr-4">
                                            <button type="button" class="px-4 py-3 text-white hover:bg-gray-700 rounded-l-lg transition-colors" onclick="decreaseQuantity()">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   id="quantity" 
                                                   min="1" 
                                                   max="{{ $product->stock_quantity }}" 
                                                   value="1"
                                                   class="w-16 px-3 py-3 bg-gray-800 text-white text-center border-none focus:outline-none">
                                            <button type="button" class="px-4 py-3 text-white hover:bg-gray-700 rounded-r-lg transition-colors" data-max-stock="{{ $product->stock_quantity }}" onclick="increaseQuantity(this.dataset.maxStock)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <span class="text-gray-400 text-sm">{{ $product->stock_quantity }} pieces available</span>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>

                    <!-- Action Buttons - AliExpress Style -->
                    @auth
                        @if($product->stock_quantity > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                                <!-- Buy Now Button -->
                                <form action="{{ route('cart.buy-now', $product) }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="quantity" id="buyNowQuantity" value="1">
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-black font-bold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                        <i class="fas fa-bolt mr-2"></i>
                                        Buy Now
                                    </button>
                                </form>
                                
                                <!-- Add to Cart Button -->
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="quantity" id="addToCartQuantity" value="1">
                                    <button type="submit" 
                                            class="w-full bg-gray-800 border-2 border-yellow-400 text-yellow-400 font-bold py-4 px-6 rounded-lg hover:bg-yellow-400 hover:text-black transition-all duration-200 transform hover:scale-[1.02]">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mb-8">
                                <button disabled class="w-full bg-gray-700 text-gray-400 font-bold py-4 px-6 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Currently Out of Stock
                                </button>
                                <p class="text-gray-400 text-sm text-center mt-2">Get notified when back in stock</p>
                            </div>
                        @endif
                    @else
                        <div class="mb-8 p-6 bg-gray-900 rounded-xl border border-gray-800">
                            <p class="text-white font-medium mb-4 text-center">Sign in to purchase this product</p>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('login') }}" 
                                   class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded-lg transition-colors text-center">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    Login
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg transition-colors text-center border border-gray-600">
                                    <i class="fas fa-user-plus mr-1"></i>
                                    Register
                                </a>
                            </div>
                        </div>
                    @endauth

                    <!-- Product Details Info -->
                    <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                        <h3 class="text-white font-semibold mb-4">Product Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">SKU:</span>
                                <span class="text-white">{{ $product->sku }}</span>
                            </div>
                            @if($product->weight)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Weight:</span>
                                <span class="text-white">{{ $product->weight }} oz</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-400">Condition:</span>
                                <span class="text-green-400">New</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Brand:</span>
                                <span class="text-yellow-400">VOSIZ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description & Tabs Section -->
            <div class="mt-12 bg-gray-900 rounded-2xl border border-gray-800 overflow-hidden">
                <div x-data="{ activeTab: 'description' }" class="w-full">
                    
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-800 bg-gray-900">
                        <button @click="activeTab = 'description'" 
                                :class="{ 'border-yellow-400 text-yellow-400 bg-black': activeTab === 'description', 'border-transparent text-gray-400': activeTab !== 'description' }"
                                class="px-8 py-4 font-medium border-b-2 transition-all hover:text-yellow-400">
                            Description
                        </button>
                        
                        @if($product->ingredients && count($product->ingredients) > 0)
                        <button @click="activeTab = 'ingredients'" 
                                :class="{ 'border-yellow-400 text-yellow-400 bg-black': activeTab === 'ingredients', 'border-transparent text-gray-400': activeTab !== 'ingredients' }"
                                class="px-8 py-4 font-medium border-b-2 transition-all hover:text-yellow-400">
                            Ingredients
                        </button>
                        @endif
                        
                        <button @click="activeTab = 'reviews'" 
                                :class="{ 'border-yellow-400 text-yellow-400 bg-black': activeTab === 'reviews', 'border-transparent text-gray-400': activeTab !== 'reviews' }"
                                class="px-8 py-4 font-medium border-b-2 transition-all hover:text-yellow-400">
                            Reviews ({{ rand(10, 100) }})
                        </button>
                        
                        <button @click="activeTab = 'shipping'" 
                                :class="{ 'border-yellow-400 text-yellow-400 bg-black': activeTab === 'shipping', 'border-transparent text-gray-400': activeTab !== 'shipping' }"
                                class="px-8 py-4 font-medium border-b-2 transition-all hover:text-yellow-400">
                            Shipping & Returns
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-8">
                        
                        <!-- Description Tab -->
                        <div x-show="activeTab === 'description'" x-transition>
                            <div class="prose prose-invert max-w-none">
                                <p class="text-gray-300 leading-relaxed text-lg mb-6">{{ $product->description }}</p>
                                
                                <!-- Product Benefits -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <h4 class="text-white font-semibold mb-4 text-lg">Key Benefits</h4>
                                        <ul class="space-y-3">
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                                                <span class="text-gray-300">Premium quality ingredients sourced globally</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                                                <span class="text-gray-300">Dermatologically tested and approved</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                                                <span class="text-gray-300">Suitable for all skin types</span>
                                            </li>
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                                                <span class="text-gray-300">Long-lasting formula with natural ingredients</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold mb-4 text-lg">How to Use</h4>
                                        <ol class="space-y-3 text-gray-300">
                                            <li class="flex items-start">
                                                <span class="bg-yellow-400 text-black rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                                                Clean your skin thoroughly with warm water
                                            </li>
                                            <li class="flex items-start">
                                                <span class="bg-yellow-400 text-black rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                                                Apply a small amount to the desired area
                                            </li>
                                            <li class="flex items-start">
                                                <span class="bg-yellow-400 text-black rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                                                Massage gently until fully absorbed
                                            </li>
                                            <li class="flex items-start">
                                                <span class="bg-yellow-400 text-black rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</span>
                                                Use daily for best results
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients Tab -->
                        @if($product->ingredients && count($product->ingredients) > 0)
                        <div x-show="activeTab === 'ingredients'" x-transition>
                            <h3 class="text-xl font-semibold text-white mb-6">Ingredients</h3>
                            <div class="bg-gray-800 rounded-lg p-6">
                                <p class="text-gray-300 leading-relaxed">{{ implode(', ', $product->ingredients) }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Reviews Tab -->
                        <div x-show="activeTab === 'reviews'" x-transition>
                            <div class="text-center py-16">
                                <i class="fas fa-comments text-gray-600 text-6xl mb-4"></i>
                                <h3 class="text-xl font-semibold text-white mb-3">Customer Reviews</h3>
                                <p class="text-gray-400">Reviews and ratings will be available soon.</p>
                            </div>
                        </div>

                        <!-- Shipping Tab -->
                        <div x-show="activeTab === 'shipping'" x-transition>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h4 class="text-white font-semibold mb-4 text-lg">Shipping Information</h4>
                                    <ul class="space-y-3 text-gray-300">
                                        <li class="flex items-center">
                                            <i class="fas fa-shipping-fast text-green-400 mr-3"></i>
                                            Free standard shipping on orders over $50
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-clock text-blue-400 mr-3"></i>
                                            2-5 business days delivery
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-truck text-yellow-400 mr-3"></i>
                                            Express shipping available
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold mb-4 text-lg">Return Policy</h4>
                                    <ul class="space-y-3 text-gray-300">
                                        <li class="flex items-center">
                                            <i class="fas fa-undo text-blue-400 mr-3"></i>
                                            30-day return policy
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-shield-alt text-green-400 mr-3"></i>
                                            100% satisfaction guarantee
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-money-bill-wave text-yellow-400 mr-3"></i>
                                            Full refund available
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-white">You Might Also Like</h2>
                    <a href="{{ route('products.index') }}" class="text-yellow-400 hover:text-yellow-300 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800 hover:border-yellow-400 transition-all duration-300">
                        <a href="{{ route('products.show', $relatedProduct->_id) }}" class="block">
                            <div class="aspect-square bg-gray-800 overflow-hidden">
                                @if(!empty($relatedProduct->images) && isset($relatedProduct->images[0]))
                                    <img src="{{ asset('storage/' . $relatedProduct->images[0]) }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-600 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="text-white text-sm font-medium mb-1 line-clamp-2">{{ Str::limit($relatedProduct->name, 40) }}</h3>
                                <div class="flex items-center mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-600' }} text-xs"></i>
                                    @endfor
                                </div>
                                <div class="text-yellow-400 font-bold text-sm">${{ number_format((float)$relatedProduct->price, 2) }}</div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- JavaScript for quantity controls and image gallery -->
    <script>
        function increaseQuantity(maxStock) {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue < maxStock) {
                const newValue = currentValue + 1;
                quantityInput.value = newValue;
                // Update hidden inputs
                document.getElementById('buyNowQuantity').value = newValue;
                document.getElementById('addToCartQuantity').value = newValue;
            }
        }

        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                const newValue = currentValue - 1;
                quantityInput.value = newValue;
                // Update hidden inputs
                document.getElementById('buyNowQuantity').value = newValue;
                document.getElementById('addToCartQuantity').value = newValue;
            }
        }

        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
            
            // Update thumbnail borders
            document.querySelectorAll('.thumbnail-container').forEach(container => {
                container.classList.remove('border-yellow-400');
                container.classList.add('border-gray-700');
            });
            
            // Add border to clicked thumbnail
            event.target.closest('.thumbnail-container').classList.remove('border-gray-700');
            event.target.closest('.thumbnail-container').classList.add('border-yellow-400');
        }

        // Update hidden inputs when quantity changes manually
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    const value = this.value;
                    const buyNowInput = document.getElementById('buyNowQuantity');
                    const addToCartInput = document.getElementById('addToCartQuantity');
                    if (buyNowInput) buyNowInput.value = value;
                    if (addToCartInput) addToCartInput.value = value;
                });
            }
        });
    </script>
</x-app-layout>