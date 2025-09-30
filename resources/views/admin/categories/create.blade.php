<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Create Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Create New Category</h1>
                            <p class="text-gray-400 mt-1">Add a new category to organize your products</p>
                        </div>
                        <a href="{{ route('admin.categories.manage') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Categories
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

                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-info-circle text-gray-400 mr-2"></i>Basic Information
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Category Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Category Name *</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                           placeholder="e.g., Skincare, Hair Care, Body Care">
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                                    <textarea id="description" name="description" rows="3"
                                              placeholder="Brief description of this category"
                                              class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Category Settings -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-cogs text-gray-400 mr-2"></i>Settings
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Sort Order -->
                                <div>
                                    <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-2">Sort Order</label>
                                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                                           min="0"
                                           class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                           placeholder="0">
                                    <p class="text-gray-400 text-sm mt-1">Lower numbers appear first in category lists</p>
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-center">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                           class="w-4 h-4 text-gray-500 bg-gray-700 border-gray-600 rounded focus:ring-gray-500 focus:ring-2">
                                    <label for="is_active" class="ml-3 text-gray-300">
                                        <span class="font-medium">Active</span>
                                        <p class="text-sm text-gray-400">Category is visible to users and can contain products</p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Category Image -->
                        <div class="bg-gray-900/30 rounded-lg p-6 border border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">
                                <i class="fas fa-image text-gray-400 mr-2"></i>Category Image
                            </h3>
                            
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-300 mb-2">Upload Image (Optional)</label>
                                <input type="file" id="image" name="image" accept="image/*"
                                       class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-600 file:text-white file:font-medium hover:file:bg-gray-500">
                                <p class="text-gray-400 text-sm mt-2">Recommended size: 400x300px. Max file size: 2MB.</p>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <a href="{{ route('admin.categories.manage') }}" 
                               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-gray-600 hover:bg-gray-500 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Create Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>