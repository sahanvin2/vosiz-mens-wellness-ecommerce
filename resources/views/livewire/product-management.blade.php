<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Product Management</h2>
            <p class="text-gray-400">Manage your product catalog with MongoDB storage</p>
        </div>
        <button wire:click="openModal" 
                class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded-xl transition-colors">
            <i class="fas fa-plus mr-2"></i>Add Product
        </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input wire:model.live="search" 
                   type="text" 
                   placeholder="Search products..." 
                   class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <select wire:model.live="categoryFilter" 
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="statusFilter" 
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="text-gray-400 flex items-center">
            Total: {{ $products->total() }} products
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
        @forelse($products as $product)
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden hover:border-yellow-400 transition-colors">
            <!-- Product Image -->
            <div class="aspect-square bg-gray-900 relative">
                @if($product->main_image)
                    <img src="{{ Storage::url($product->main_image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-500">
                        <i class="fas fa-image text-4xl"></i>
                    </div>
                @endif
                
                <!-- Status badges -->
                <div class="absolute top-2 left-2 space-y-1">
                    @if($product->is_featured)
                        <span class="inline-block px-2 py-1 text-xs bg-yellow-400 text-black rounded">Featured</span>
                    @endif
                    <span class="inline-block px-2 py-1 text-xs rounded {{ $product->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <!-- Action buttons -->
                <div class="absolute top-2 right-2 space-y-1">
                    <button wire:click="edit('{{ $product->_id }}')" 
                            class="w-8 h-8 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                    <button wire:click="toggleFeatured('{{ $product->_id }}')" 
                            class="w-8 h-8 bg-yellow-500 text-black rounded-full hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-star text-sm"></i>
                    </button>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="p-4">
                <h3 class="text-white font-semibold text-lg mb-2 line-clamp-1">{{ $product->name }}</h3>
                <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ $product->short_description }}</p>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="text-yellow-400 font-bold">
                        @if($product->sale_price)
                            <span class="text-lg">${{ $product->formatted_sale_price }}</span>
                            <span class="text-sm text-gray-400 line-through ml-1">${{ $product->formatted_price }}</span>
                        @else
                            <span class="text-lg">${{ $product->formatted_price }}</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-400">
                        Stock: {{ $product->stock_quantity }}
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <button wire:click="toggleStatus('{{ $product->_id }}')" 
                            class="flex-1 py-2 px-3 rounded-lg text-sm transition-colors {{ $product->is_active ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                        {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    <button wire:click="delete('{{ $product->_id }}')" 
                            onclick="return confirm('Are you sure?')"
                            class="py-2 px-3 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-400">
                <i class="fas fa-box-open text-6xl mb-4"></i>
                <p class="text-xl mb-2">No products found</p>
                <p>Start by adding your first product to MongoDB</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mb-6">
        {{ $products->links() }}
    </div>

    <!-- Product Form Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-2xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">
                        {{ $editingProduct ? 'Edit Product' : 'Add New Product' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-white border-b border-gray-700 pb-2">Basic Information</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Product Name</label>
                                <input wire:model="name" 
                                       type="text" 
                                       class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                                <select wire:model="category_id" 
                                        class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Short Description</label>
                                <textarea wire:model="short_description" 
                                          rows="3" 
                                          class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
                                @error('short_description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Price ($)</label>
                                    <input wire:model="price" 
                                           type="number" 
                                           step="0.01" 
                                           class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    @error('price') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Sale Price ($)</label>
                                    <input wire:model="sale_price" 
                                           type="number" 
                                           step="0.01" 
                                           class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    @error('sale_price') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">SKU</label>
                                    <input wire:model="sku" 
                                           type="text" 
                                           class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    @error('sku') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Stock Quantity</label>
                                    <input wire:model="stock_quantity" 
                                           type="number" 
                                           class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    @error('stock_quantity') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Media & Settings -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-white border-b border-gray-700 pb-2">Media & Settings</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Product Images</label>
                                <input wire:model="images" 
                                       type="file" 
                                       multiple 
                                       accept="image/*"
                                       class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                @error('images.*') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Introduction Video (720px recommended)</label>
                                <input wire:model="introduction_video" 
                                       type="file" 
                                       accept="video/*"
                                       class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                @error('introduction_video') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input wire:model="is_active" type="checkbox" class="mr-2">
                                    <span class="text-gray-300">Active</span>
                                </label>
                                <label class="flex items-center">
                                    <input wire:model="is_featured" type="checkbox" class="mr-2">
                                    <span class="text-gray-300">Featured</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tags (comma separated)</label>
                                <input wire:model="tags" 
                                       type="text" 
                                       placeholder="skincare, moisturizer, organic"
                                       class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Weight (oz)</label>
                                <input wire:model="weight" 
                                       type="number" 
                                       step="0.1" 
                                       class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Full Description</label>
                        <textarea wire:model="description" 
                                  rows="4" 
                                  class="w-full px-4 py-2 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
                        @error('description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                        <button type="button" 
                                wire:click="closeModal" 
                                class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-yellow-400 text-black font-bold rounded-lg hover:bg-yellow-500 transition-colors">
                            {{ $editingProduct ? 'Update Product' : 'Create Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
</div>
