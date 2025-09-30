<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-600 text-white p-4 rounded-xl shadow-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                {{ session('message') }}
            </div>
        </div>
    @endif

    <!-- Video Management Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Video Library</h2>
            <p class="text-gray-400">Manage hero videos, product intros, and promotional content</p>
        </div>
        <button wire:click="openModal" class="bg-amber-500 hover:bg-amber-600 text-black px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Add New Video
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 bg-opacity-50 rounded-xl p-6">
        <div class="flex items-center space-x-4">
            <label class="text-white font-semibold">Filter by Type:</label>
            <select wire:model.live="typeFilter" class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                <option value="">All Types</option>
                <option value="hero">Hero Videos</option>
                <option value="product_intro">Product Intros</option>
                <option value="category_intro">Category Intros</option>
            </select>
            @if($typeFilter)
                <button wire:click="$set('typeFilter', '')" class="text-amber-400 hover:text-amber-300">
                    <i class="fas fa-times"></i> Clear
                </button>
            @endif
        </div>
    </div>

    <!-- Videos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($videos as $video)
        <div class="bg-gray-800 bg-opacity-50 rounded-xl overflow-hidden hover:bg-opacity-70 transition-all duration-300 group">
            <!-- Video Thumbnail/Preview -->
            <div class="aspect-video bg-gray-700 relative overflow-hidden">
                @if($video->thumbnail)
                    <img src="{{ Storage::url($video->thumbnail) }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                @elseif($video->video_file)
                    <video class="w-full h-full object-cover" preload="metadata">
                        <source src="{{ Storage::url($video->video_file) }}" type="video/mp4">
                    </video>
                @else
                    <div class="flex items-center justify-center h-full">
                        <i class="fas fa-video text-gray-500 text-4xl"></i>
                    </div>
                @endif
                
                <!-- Video Type Badge -->
                <div class="absolute top-3 left-3">
                    <span class="px-2 py-1 bg-{{ $video->type === 'hero' ? 'red' : ($video->type === 'product_intro' ? 'blue' : 'green') }}-500 bg-opacity-90 text-white text-xs rounded-full font-semibold">
                        {{ ucfirst(str_replace('_', ' ', $video->type)) }}
                    </span>
                </div>

                <!-- Status Badge -->
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-1 bg-{{ $video->is_active ? 'green' : 'gray' }}-500 bg-opacity-90 text-white text-xs rounded-full font-semibold">
                        {{ $video->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Overlay Actions -->
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <div class="flex items-center space-x-3">
                        @if($video->video_file)
                            <button class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-3 rounded-full">
                                <i class="fas fa-play"></i>
                            </button>
                        @endif
                        <button wire:click="edit({{ $video->id }})" class="bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-full">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Video Info -->
            <div class="p-4">
                <h3 class="text-white font-semibold mb-2">{{ $video->title }}</h3>
                @if($video->description)
                    <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ $video->description }}</p>
                @endif

                <!-- Video Details -->
                <div class="space-y-2">
                    @if($video->category_id && isset($categories) && $categories->find($video->category_id))
                        <div class="flex items-center text-sm">
                            <i class="fas fa-tag text-amber-400 mr-2"></i>
                            <span class="text-gray-300">{{ $categories->find($video->category_id)->name }}</span>
                        </div>
                    @endif
                    
                    @if($video->file_size)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-hdd text-blue-400 mr-2"></i>
                            <span class="text-gray-300">{{ number_format($video->file_size / 1024 / 1024, 1) }} MB</span>
                        </div>
                    @endif

                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar text-green-400 mr-2"></i>
                        <span class="text-gray-300">{{ $video->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-700">
                    <div class="flex items-center space-x-2">
                        <button wire:click="toggleStatus({{ $video->id }})" class="text-{{ $video->is_active ? 'green' : 'gray' }}-400 hover:text-{{ $video->is_active ? 'green' : 'gray' }}-300">
                            <i class="fas fa-{{ $video->is_active ? 'eye' : 'eye-slash' }}"></i>
                        </button>
                        @if($video->type === 'hero')
                            <button wire:click="setAsHeroVideo({{ $video->id }})" class="text-red-400 hover:text-red-300" title="Set as Hero Video">
                                <i class="fas fa-star"></i>
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button wire:click="edit({{ $video->id }})" class="text-amber-400 hover:text-amber-300">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="delete({{ $video->id }})" onclick="return confirm('Are you sure you want to delete this video?')" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-400">
                <i class="fas fa-video text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No Videos Found</h3>
                <p class="mb-4">Start by uploading your first video</p>
                <button wire:click="openModal" class="bg-amber-500 hover:bg-amber-600 text-black px-6 py-3 rounded-xl font-semibold">
                    <i class="fas fa-plus mr-2"></i>Add First Video
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Add/Edit Video Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">
                        {{ $editingVideo ? 'Edit Video' : 'Add New Video' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-white font-semibold mb-2">Title</label>
                        <input type="text" wire:model="title" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="Enter video title">
                        @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-white font-semibold mb-2">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="Enter video description"></textarea>
                        @error('description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Type and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Type</label>
                            <select wire:model="type" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                <option value="hero">Hero Video</option>
                                <option value="product_intro">Product Intro</option>
                                <option value="category_intro">Category Intro</option>
                            </select>
                            @error('type') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($type === 'category_intro')
                        <div>
                            <label class="block text-white font-semibold mb-2">Category</label>
                            <select wire:model="category_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>

                    <!-- Video File Upload -->
                    <div>
                        <label class="block text-white font-semibold mb-2">Video File</label>
                        <input type="file" wire:model="video_file" accept="video/*" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white file:bg-amber-500 file:border-0 file:text-black file:px-4 file:py-2 file:rounded-lg file:mr-4 file:font-semibold hover:file:bg-amber-600">
                        @error('video_file') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-gray-400 text-sm mt-1">Max file size: 50MB. Supported formats: MP4, AVI, MOV, WebM</p>
                    </div>

                    <!-- Video URL (Alternative) -->
                    <div>
                        <label class="block text-white font-semibold mb-2">Video URL (Alternative)</label>
                        <input type="url" wire:model="video_url" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="https://youtube.com/watch?v=...">
                        @error('video_url') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-gray-400 text-sm mt-1">Use video URL if not uploading a file</p>
                    </div>

                    <!-- Thumbnail Upload -->
                    <div>
                        <label class="block text-white font-semibold mb-2">Thumbnail Image</label>
                        <input type="file" wire:model="thumbnail" accept="image/*" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white file:bg-amber-500 file:border-0 file:text-black file:px-4 file:py-2 file:rounded-lg file:mr-4 file:font-semibold hover:file:bg-amber-600">
                        @error('thumbnail') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Order and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white font-semibold mb-2">Display Order</label>
                            <input type="number" wire:model="order" min="0" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500" placeholder="0">
                            @error('order') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center text-white">
                                <input type="checkbox" wire:model="is_active" class="mr-2 text-amber-500 bg-gray-700 border-gray-600 rounded focus:ring-amber-500">
                                Active Video
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700">
                        <button type="button" wire:click="closeModal" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-black rounded-lg font-semibold transition-colors">
                            {{ $editingVideo ? 'Update Video' : 'Create Video' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
