<x-admin-layout>
    <x-slot name="title">Edit Product</x-slot>
    
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Edit Product</h1>
                        <p class="mt-2 text-gray-400">Update product information</p>
                    </div>
                    <a href="{{ route('admin.products.manage') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Products
                    </a>
                </div>
            </div>

            <!-- Product Form -->
            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <form action="{{ route('admin.products.update', $product->_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                                <input type="text" name="name" id="name" required
                                       class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                       placeholder="Enter product name"
                                       value="{{ old('name', $product->name) }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-300 mb-2">SKU *</label>
                                <input type="text" name="sku" id="sku" required
                                       class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                       placeholder="e.g., VOSIZ-PROD-001"
                                       value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category *</label>
                                <select name="category_id" id="category_id" required
                                        class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-300 mb-2">Supplier *</label>
                                <select name="supplier_id" id="supplier_id" required
                                        class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                                    <option value="">Select a supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                                {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Weight -->
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-300 mb-2">Weight (oz)</label>
                                <input type="number" name="weight" id="weight" step="0.1" min="0"
                                       class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                       placeholder="0.0"
                                       value="{{ old('weight', $product->weight) }}">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description *</label>
                            <textarea name="description" id="description" rows="4" required
                                      class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                      placeholder="Detailed product description">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="mt-6">
                            <label for="short_description" class="block text-sm font-medium text-gray-300 mb-2">Short Description</label>
                            <textarea name="short_description" id="short_description" rows="2"
                                      class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                      placeholder="Brief product summary">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div class="bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Pricing & Inventory</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400">$</span>
                                    <input type="number" name="price" id="price" step="0.01" min="0" required
                                           class="w-full pl-8 pr-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                           placeholder="0.00"
                                           value="{{ old('price', $product->price) }}">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Compare Price -->
                            <div>
                                <label for="compare_price" class="block text-sm font-medium text-gray-300 mb-2">Compare Price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400">$</span>
                                    <input type="number" name="compare_price" id="compare_price" step="0.01" min="0"
                                           class="w-full pl-8 pr-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                           placeholder="0.00"
                                           value="{{ old('compare_price', $product->compare_price) }}">
                                </div>
                                @error('compare_price')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Stock Quantity -->
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-300 mb-2">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" min="0" required
                                       class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                       placeholder="0"
                                       value="{{ old('stock_quantity', $product->stock_quantity) }}">
                                @error('stock_quantity')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="bg-gray-700/50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Additional Information</h3>
                        
                        <!-- Tags -->
                        <div class="mb-6">
                            <label for="tags" class="block text-sm font-medium text-gray-300 mb-2">Tags</label>
                            <input type="text" name="tags" id="tags"
                                   class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                   placeholder="skincare, moisturizer, anti-aging (comma separated)"
                                   value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : $product->tags) }}">
                            <p class="mt-1 text-sm text-gray-400">Separate tags with commas</p>
                            @error('tags')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ingredients -->
                        <div class="mb-6">
                            <label for="ingredients" class="block text-sm font-medium text-gray-300 mb-2">Ingredients</label>
                            <textarea name="ingredients" id="ingredients" rows="3"
                                      class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                                      placeholder="Hyaluronic Acid, Vitamin E, Retinol (comma separated)">{{ old('ingredients', is_array($product->ingredients) ? implode(', ', $product->ingredients) : $product->ingredients) }}</textarea>
                            <p class="mt-1 text-sm text-gray-400">Separate ingredients with commas</p>
                            @error('ingredients')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Checkboxes -->
                        <div class="flex space-x-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       class="w-4 h-4 text-yellow-400 bg-gray-800 border-gray-600 rounded focus:ring-yellow-400"
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 text-sm text-gray-300">Active</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                       class="w-4 h-4 text-yellow-400 bg-gray-800 border-gray-600 rounded focus:ring-yellow-400"
                                       {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label for="is_featured" class="ml-2 text-sm text-gray-300">Featured</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-700">
                        <!-- Delete Button -->
                        <form action="{{ route('admin.products.delete', $product->_id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Product
                            </button>
                        </form>

                        <!-- Save/Cancel -->
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.products.manage') }}" 
                               class="px-6 py-2 text-gray-300 hover:text-white transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>