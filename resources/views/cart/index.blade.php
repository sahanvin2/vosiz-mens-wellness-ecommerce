<x-app-layout>
    <div class="min-h-screen bg-black py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white">Shopping Cart</h1>
                <p class="text-gray-400 mt-2">Review your items before checkout</p>
            </div>

            @if($cartItems->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cartItems as $item)
                        <div class="bg-gray-900 rounded-xl p-6 flex items-center space-x-4">
                            <!-- Product Image -->
                            <div class="w-20 h-20 bg-gray-800 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-600 text-xl"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white">{{ $item->product->name }}</h3>
                                <p class="text-gray-400 text-sm">{{ $item->product->category->name ?? 'No Category' }}</p>
                                <p class="text-yellow-400 font-bold">${{ number_format($item->product->price, 2) }}</p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-3">
                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" 
                                           name="quantity" 
                                           value="{{ $item->quantity }}" 
                                           min="1" 
                                           max="{{ $item->product->stock_quantity }}"
                                           class="w-16 px-2 py-1 bg-gray-800 border border-gray-600 rounded text-white text-center"
                                           onchange="this.form.submit()">
                                </form>
                                
                                <div class="text-right">
                                    <p class="text-white font-semibold">${{ number_format($item->quantity * $item->product->price, 2) }}</p>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-400 hover:text-red-300 p-2"
                                        onclick="return confirm('Remove this item from cart?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach

                        <!-- Clear Cart Button -->
                        <div class="text-right">
                            <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-400 hover:text-red-300 underline"
                                        onclick="return confirm('Clear all items from cart?')">
                                    Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-900 rounded-xl p-6 h-fit">
                        <h2 class="text-xl font-bold text-white mb-6">Order Summary</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-300">
                                <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Tax</span>
                                <span>${{ number_format($total * 0.08, 2) }}</span>
                            </div>
                            <hr class="border-gray-700">
                            <div class="flex justify-between text-lg font-bold text-white">
                                <span>Total</span>
                                <span>${{ number_format($total * 1.08, 2) }}</span>
                            </div>
                        </div>

                        <button class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors duration-200 mb-3">
                            <i class="fas fa-credit-card mr-2"></i>
                            Proceed to Checkout
                        </button>
                        
                        <a href="{{ route('products.index') }}" 
                           class="block w-full text-center bg-gray-800 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 border border-gray-600">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-12">
                    <div class="w-32 h-32 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-gray-600 text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-4">Your Cart is Empty</h2>
                    <p class="text-gray-400 mb-8">Looks like you haven't added any items to your cart yet.</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Start Shopping
                    </a>
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
</x-app-layout>