<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Add New Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Add New Product</h1>
                            <p class="text-gray-400 mt-1">Create a new product listing for your store</p>
                        </div>
                        <a href="{{ route('supplier.products') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Products
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <i class="fas fa-exclamation-circle text-red-400 text-lg mr-3 mt-1"></i>
                                <div>
                                    <h4 class="text-red-400 font-medium">Please fix the following errors:</h4>
                                    <ul class="text-red-300 text-sm mt-2 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-info-circle text-green-400 mr-2"></i>Basic Information
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Product Name -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Product Name *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <!-- Category -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category *</label>
                                    <select id="category_id" name="category_id" required
                                            class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- SKU -->
                                <div>
                                    <label for="sku" class="block text-sm font-medium text-gray-300 mb-2">SKU</label>
                                    <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                                           placeholder="Leave empty to auto-generate"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <!-- Short Description -->
                                <div class="md:col-span-2">
                                    <label for="short_description" class="block text-sm font-medium text-gray-300 mb-2">Short Description</label>
                                    <textarea id="short_description" name="short_description" rows="2"
                                              placeholder="Brief product description for listings"
                                              class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('short_description') }}</textarea>
                                </div>

                                <!-- Full Description -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Full Description *</label>
                                    <textarea id="description" name="description" rows="4" required
                                              placeholder="Detailed product description, benefits, and usage instructions"
                                              class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Inventory -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-dollar-sign text-green-400 mr-2"></i>Pricing & Inventory
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Price -->
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price ($) *</label>
                                    <input type="number" id="price" name="price" value="{{ old('price') }}" required
                                           step="0.01" min="0"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <!-- Compare Price -->
                                <div>
                                    <label for="compare_price" class="block text-sm font-medium text-gray-300 mb-2">Compare Price ($)</label>
                                    <input type="number" id="compare_price" name="compare_price" value="{{ old('compare_price') }}"
                                           step="0.01" min="0" placeholder="Original price for discount display"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <!-- Stock Quantity -->
                                <div>
                                    <label for="stock_quantity" class="block text-sm font-medium text-gray-300 mb-2">Stock Quantity *</label>
                                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" required
                                           min="0"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-list text-green-400 mr-2"></i>Product Details
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Ingredients -->
                                <div>
                                    <label for="ingredients" class="block text-sm font-medium text-gray-300 mb-2">Ingredients</label>
                                    <textarea id="ingredients" name="ingredients" rows="3"
                                              placeholder="Separate ingredients with commas"
                                              class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('ingredients') }}</textarea>
                                </div>

                                <!-- Benefits -->
                                <div>
                                    <label for="benefits" class="block text-sm font-medium text-gray-300 mb-2">Benefits</label>
                                    <textarea id="benefits" name="benefits" rows="3"
                                              placeholder="Separate benefits with commas"
                                              class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('benefits') }}</textarea>
                                </div>

                                <!-- Skin Type -->
                                <div>
                                    <label for="skin_type" class="block text-sm font-medium text-gray-300 mb-2">Skin Type</label>
                                    <input type="text" id="skin_type" name="skin_type" value="{{ old('skin_type') }}"
                                           placeholder="e.g., All skin types, Oily, Dry, Sensitive"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <!-- Weight -->
                                <div>
                                    <label for="weight" class="block text-sm font-medium text-gray-300 mb-2">Weight (oz)</label>
                                    <input type="number" id="weight" name="weight" value="{{ old('weight') }}"
                                           step="0.01" min="0"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>

                                <!-- Dimensions -->
                                <div class="md:col-span-2">
                                    <label for="dimensions" class="block text-sm font-medium text-gray-300 mb-2">Dimensions</label>
                                    <input type="text" id="dimensions" name="dimensions" value="{{ old('dimensions') }}"
                                           placeholder="e.g., 3 x 2 x 1 inches"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-images text-green-400 mr-2"></i>Product Images
                            </h3>
                            
                            <div>
                                <label for="images" class="block text-sm font-medium text-gray-300 mb-2">Upload Images</label>
                                <input type="file" id="images" name="images[]" multiple accept="image/*"
                                       class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:font-medium hover:file:bg-green-600">
                                <p class="text-gray-400 text-sm mt-2">Select multiple images. Recommended size: 800x800px. Max file size: 2MB per image.</p>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-cogs text-green-400 mr-2"></i>Settings
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Featured Product -->
                                <div class="flex items-center">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-500 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                                    <label for="is_featured" class="ml-3 text-gray-300">
                                        <span class="font-medium">Featured Product</span>
                                        <p class="text-sm text-gray-400">Show this product in featured sections</p>
                                    </label>
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-center">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-500 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                                    <label for="is_active" class="ml-3 text-gray-300">
                                        <span class="font-medium">Active</span>
                                        <p class="text-sm text-gray-400">Product is visible to customers</p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <a href="{{ route('supplier.products') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>