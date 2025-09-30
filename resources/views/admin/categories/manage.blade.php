<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manage Categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Category Management</h1>
                            <p class="text-gray-400 mt-1">Organize and manage product categories</p>
                        </div>
                        <a href="{{ route('admin.categories.create') }}" class="bg-gray-600 hover:bg-gray-500 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Category
                        </a>
                    </div>

                    <!-- Category Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gray-900/30 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gray-600">
                                    <i class="fas fa-tags text-white text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-gray-400 text-sm">Total Categories</p>
                                    <p class="text-white text-xl font-bold">{{ $stats['total_categories'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900/30 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gray-600">
                                    <i class="fas fa-eye text-white text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-gray-400 text-sm">Active Categories</p>
                                    <p class="text-white text-xl font-bold">{{ $stats['active_categories'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900/30 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-gray-600">
                                    <i class="fas fa-eye-slash text-white text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-gray-400 text-sm">Inactive Categories</p>
                                    <p class="text-white text-xl font-bold">{{ $stats['total_categories'] - $stats['active_categories'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Products</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Sort Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse($categories as $category)
                                <tr class="hover:bg-gray-700/30">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 rounded-lg object-cover mr-3">
                                            @else
                                                <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-tag text-gray-300"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ $category->name }}</div>
                                                <div class="text-sm text-gray-400">{{ $category->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-300 max-w-xs">
                                            {{ Str::limit($category->description, 60) ?: 'No description' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white font-medium">{{ $category->products_count }}</div>
                                        <div class="text-sm text-gray-400">products</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $category->sort_order ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-gray-400 hover:text-white p-1" title="Edit Category">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.toggle', $category) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-gray-400 hover:text-white p-1" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas {{ $category->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            @if($category->products_count == 0)
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-300 p-1" title="Delete Category">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-gray-600 p-1" title="Cannot delete category with products">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-400">
                                            <i class="fas fa-tags text-6xl mb-4"></i>
                                            <p class="text-xl mb-2">No categories found</p>
                                            <p class="mb-6">Start organizing your products by creating categories</p>
                                            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-500 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                                                <i class="fas fa-plus mr-2"></i>Create First Category
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($categories->hasPages())
                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500/20 border border-green-500/30 text-green-400 px-6 py-3 rounded-lg shadow-lg z-50" id="success-message">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                const message = document.getElementById('success-message');
                if (message) message.remove();
            }, 5000);
        </script>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500/20 border border-red-500/30 text-red-400 px-6 py-3 rounded-lg shadow-lg z-50" id="error-message">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                const message = document.getElementById('error-message');
                if (message) message.remove();
            }, 5000);
        </script>
    @endif
</x-app-layout>