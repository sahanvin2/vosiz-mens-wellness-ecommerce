<x-app-layout>
    <x-slot name="title">Categories</x-slot>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-black via-gray-900 to-black py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                    <span class="bg-gradient-to-r from-white via-vosiz-silver to-vosiz-gold bg-clip-text text-transparent">
                        Categories
                    </span>
                </h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Explore our premium collections designed for every aspect of men's grooming and wellness.
                </p>
            </div>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-20 bg-gradient-to-b from-gray-900 to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($categories as $category)
                <div class="group relative overflow-hidden rounded-xl bg-gray-800/50 backdrop-blur-sm border border-gray-700 hover:border-vosiz-gold/50 transition-all duration-300 transform hover:scale-105">
                    <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-gray-800 to-gray-900 relative">
                        <!-- Category Image Placeholder -->
                        <div class="absolute inset-0 bg-gradient-to-br from-vosiz-gold/20 to-transparent"></div>
                        <div class="flex items-center justify-center h-48">
                            <i class="fas fa-star text-vosiz-gold text-6xl opacity-60"></i>
                        </div>
                        
                        <!-- Product Count Badge -->
                        <div class="absolute top-4 right-4 bg-vosiz-gold text-black px-3 py-1 rounded-full text-sm font-bold">
                            {{ $category->active_products_count }} Products
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-vosiz-gold transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-gray-400 mb-6 line-clamp-3">{{ $category->description }}</p>
                        
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="inline-flex items-center bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-vosiz-gold transition-colors transform hover:scale-105">
                            Explore Category
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Call to Action -->
            <div class="text-center mt-16">
                <h2 class="text-3xl font-bold text-white mb-4">Can't Find What You're Looking For?</h2>
                <p class="text-xl text-gray-400 mb-8">Browse all our premium products or contact our experts for personalized recommendations.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('products.index') }}" class="bg-vosiz-gold text-black px-8 py-4 rounded-lg font-semibold hover:bg-vosiz-gold/80 transition-colors">
                        View All Products
                    </a>
                    <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-black transition-colors">
                        Contact Expert
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>