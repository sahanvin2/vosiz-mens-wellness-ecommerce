<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Search products or SKU..."
                   class="w-full px-4 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
        </div>
        
        <div>
            <select wire:model.live="selectedCategory" 
                    class="w-full px-4 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <select wire:model.live="statusFilter" 
                    class="w-full px-4 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                <option value="all">All Status</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
            </select>
        </div>
        
        <div>
            <button wire:click="openCreateModal" 
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 font-semibold">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <button wire:click="sortBy('name')" class="flex items-center text-white font-semibold hover:text-amber-400">
                                Product
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Category</th>
                        <th class="px-6 py-4 text-left">
                            <button wire:click="sortBy('price')" class="flex items-center text-white font-semibold hover:text-amber-400">
                                Price
                                @if($sortBy === 'price')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <button wire:click="sortBy('stock_quantity')" class="flex items-center text-white font-semibold hover:text-amber-400">
                                Stock
                                @if($sortBy === 'stock_quantity')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Status</th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-12 h-12 object-cover rounded-lg mr-4">
                                @else
                                    <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-white font-semibold">{{ $product->name }}</h3>
                                    <p class="text-gray-400 text-sm">SKU: {{ $product->sku }}</p>
                                    @if($product->is_featured)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-600 text-black">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $product->category_name }}</td>
                        <td class="px-6 py-4">
                            <div class="text-white font-semibold">${{ number_format($product->price, 2) }}</div>
                            @if($product->sale_price)
                                <div class="text-green-400 text-sm">
                                    Sale: ${{ number_format($product->sale_price, 2) }}
                                    <span class="text-xs text-gray-400">({{ $product->discount_percentage }}% off)</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white font-medium">{{ $product->stock_quantity }}</span>
                            @if($product->stock_quantity <= 5)
                                <span class="text-red-400 text-xs ml-1">Low Stock</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleStatus({{ $product->id }})" 
                                    class="px-3 py-1 rounded-full text-sm font-medium transition-colors duration-200 {{ $product->is_active ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button wire:click="openEditModal({{ $product->id }})" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                
                                <button wire:click="toggleFeatured({{ $product->id }})" 
                                        class="bg-amber-600 hover:bg-amber-700 text-black px-3 py-1 rounded text-sm transition-colors duration-200 {{ $product->is_featured ? 'bg-amber-700' : '' }}">
                                    <i class="fas fa-star mr-1"></i>{{ $product->is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                                
                                <button wire:click="deleteProduct({{ $product->id }})" 
                                        onclick="confirm('Are you sure you want to delete this product?') || event.stopImmediatePropagation()"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                                <h3 class="text-xl font-semibold text-white mb-2">No Products Found</h3>
                                <p class="text-gray-400 mb-4">Get started by adding your first product</p>
                                <button wire:click="openCreateModal" 
                                        class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 font-semibold">
                                    <i class="fas fa-plus mr-2"></i>Add Your First Product
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="px-6 py-4 bg-gray-900 border-t border-gray-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Add/Edit Product Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-700">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-white">
                        {{ $editMode ? 'Edit Product' : 'Add New Product' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form wire:submit.prevent="save" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-white border-b border-gray-700 pb-2">Basic Information</h4>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Product Name *</label>
                            <input type="text" wire:model.live="name" required
                                   class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                            @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Category *</label>
                            <select wire:model="category_id" required
                                    class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white font-semibold mb-2">Price ($) *</label>
                                <input type="number" wire:model="price" step="0.01" required
                                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                                @error('price') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-white font-semibold mb-2">Sale Price ($)</label>
                                <input type="number" wire:model="sale_price" step="0.01"
                                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white font-semibold mb-2">SKU *</label>
                                <input type="text" wire:model="sku" required
                                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                                @error('sku') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-white font-semibold mb-2">Stock Quantity *</label>
                                <input type="number" wire:model="stock_quantity" required
                                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                                @error('stock_quantity') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Images & Features -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-white border-b border-gray-700 pb-2">Images & Features</h4>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Product Images</label>
                            <input type="file" wire:model="images" multiple accept="image/*"
                                   class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-600 file:text-black">
                            
                            @if($existingImages)
                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    @foreach($existingImages as $image)
                                        <img src="{{ asset('storage/' . $image) }}" class="w-full h-16 object-cover rounded">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Features (one per line)</label>
                            <textarea wire:model="features" rows="4" placeholder="Enter product features..."
                                      class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-white font-semibold mb-2">Tags (comma separated)</label>
                            <input type="text" wire:model="tags" placeholder="tag1, tag2, tag3"
                                   class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
                        </div>
                        
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active"
                                       class="mr-2 w-4 h-4 text-amber-600 bg-gray-700 border-gray-600 rounded focus:ring-amber-500">
                                <span class="text-white">Active Product</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_featured"
                                       class="mr-2 w-4 h-4 text-amber-600 bg-gray-700 border-gray-600 rounded focus:ring-amber-500">
                                <span class="text-white">Featured Product</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-white font-semibold mb-2">Product Description *</label>
                    <textarea wire:model="description" rows="4" required
                              placeholder="Enter detailed product description..."
                              class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none"></textarea>
                    @error('description') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                    <button type="button" wire:click="$set('showModal', false)" 
                            class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 font-semibold">
                        {{ $editMode ? 'Update Product' : 'Create Product' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>