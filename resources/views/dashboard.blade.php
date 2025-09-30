<x-app-layout>
    <div class="min-h-screen bg-black py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-gray-400 mt-2">Manage your account and view your activity</p>
            </div>

            <!-- Quick Stats -->
            @php
                $cartCount = Auth::user()->cartItems()->count();
                $cartTotal = Auth::user()->cartItems()->with('product')->get()->sum(function($item) {
                    return $item->quantity * $item->product->price;
                });
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Cart Summary -->
                <div class="bg-gray-900 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Items in Cart</p>
                            <h3 class="text-2xl font-bold text-white">{{ $cartCount }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-yellow-400/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                    @if($cartCount > 0)
                        <p class="text-yellow-400 text-sm mt-2">${{ number_format($cartTotal, 2) }} total</p>
                    @endif
                </div>

                <!-- Profile Status -->
                <div class="bg-gray-900 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Profile Status</p>
                            <h3 class="text-lg font-bold text-white">
                                @if(Auth::user()->email_verified_at)
                                    <span class="text-green-400">Verified</span>
                                @else
                                    <span class="text-yellow-400">Unverified</span>
                                @endif
                            </h3>
                        </div>
                        <div class="w-12 h-12 bg-green-400/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Member Since -->
                <div class="bg-gray-900 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Member Since</p>
                            <h3 class="text-lg font-bold text-white">{{ Auth::user()->created_at->format('M Y') }}</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-400/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- View Cart -->
                <a href="{{ route('cart.index') }}" 
                   class="bg-gray-900 hover:bg-gray-800 rounded-xl p-6 transition-colors duration-200 group">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-shopping-cart text-yellow-400 text-2xl group-hover:scale-110 transition-transform"></i>
                        @if($cartCount > 0)
                            <span class="bg-yellow-400 text-black text-xs px-2 py-1 rounded-full font-bold">{{ $cartCount }}</span>
                        @endif
                    </div>
                    <h3 class="text-white font-semibold">My Cart</h3>
                    <p class="text-gray-400 text-sm">View and manage items</p>
                </a>

                <!-- Browse Products -->
                <a href="{{ route('products.index') }}" 
                   class="bg-gray-900 hover:bg-gray-800 rounded-xl p-6 transition-colors duration-200 group">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-shopping-bag text-blue-400 text-2xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h3 class="text-white font-semibold">Shop Products</h3>
                    <p class="text-gray-400 text-sm">Browse our catalog</p>
                </a>

                <!-- Profile Settings -->
                <a href="{{ route('profile.show') }}" 
                   class="bg-gray-900 hover:bg-gray-800 rounded-xl p-6 transition-colors duration-200 group">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-user-cog text-green-400 text-2xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h3 class="text-white font-semibold">Profile</h3>
                    <p class="text-gray-400 text-sm">Manage your account</p>
                </a>

                <!-- Order History -->
                <a href="#" 
                   class="bg-gray-900 hover:bg-gray-800 rounded-xl p-6 transition-colors duration-200 group opacity-75">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-history text-purple-400 text-2xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <h3 class="text-white font-semibold">Order History</h3>
                    <p class="text-gray-400 text-sm">Coming soon</p>
                </a>
            </div>

            <!-- Recent Cart Items -->
            @if($cartCount > 0)
                <div class="bg-gray-900 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-white">Recent Cart Items</h2>
                        <a href="{{ route('cart.index') }}" 
                           class="text-yellow-400 hover:text-yellow-300 text-sm font-semibold">
                            View All
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach(Auth::user()->cartItems()->with('product')->latest()->take(3)->get() as $item)
                        <div class="bg-gray-800 rounded-lg p-4 flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-500"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-white font-semibold text-sm">{{ $item->product->name }}</h4>
                                <p class="text-gray-400 text-xs">Qty: {{ $item->quantity }}</p>
                                <p class="text-yellow-400 font-bold text-sm">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Empty Cart CTA -->
                <div class="bg-gray-900 rounded-xl p-8 text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-gray-600 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">Your Cart is Empty</h2>
                    <p class="text-gray-400 mb-6">Start exploring our premium men's wellness products</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
