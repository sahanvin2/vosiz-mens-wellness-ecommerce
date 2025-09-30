<x-admin-layout>
    <div class="p-6 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Supplier Details</h1>
                        <p class="text-gray-400">Detailed information about {{ $supplier->name }}</p>
                    </div>
                    <a href="{{ route('admin.suppliers.manage') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Suppliers
                    </a>
                </div>
            </div>

            <!-- Supplier Profile Card -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 mb-8">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-r from-amber-400 to-amber-600 flex items-center justify-center">
                                <span class="text-xl font-bold text-white">{{ strtoupper(substr($supplier->name, 0, 2)) }}</span>
                            </div>
                            <div class="ml-6">
                                <h2 class="text-2xl font-bold text-white">{{ $supplier->name }}</h2>
                                <p class="text-gray-400">{{ $supplier->email }}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    @if($supplier->status)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <span class="w-2 h-2 mr-2 bg-red-400 rounded-full"></span>
                                            Inactive
                                        </span>
                                    @endif
                                    
                                    @if($supplier->email_verified_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Email Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Email Unverified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <form method="POST" action="{{ route('admin.suppliers.toggle', $supplier) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 {{ $supplier->status ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors"
                                        onclick="return confirm('Are you sure you want to change this supplier status?')">
                                    {{ $supplier->status ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.suppliers.delete', $supplier) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                        onclick="return confirm('Are you sure you want to delete this supplier? This action cannot be undone.')">
                                    Delete Supplier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 mb-2">Supplier ID</h3>
                            <p class="text-lg text-white">{{ $supplier->id }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 mb-2">Joined Date</h3>
                            <p class="text-lg text-white">{{ $supplier->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 mb-2">Last Updated</h3>
                            <p class="text-lg text-white">{{ $supplier->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-amber-500/10 text-amber-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Products</p>
                            <p class="text-2xl font-bold text-white">{{ $supplier->products->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-500/10 text-green-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Available Products</p>
                            <p class="text-2xl font-bold text-white">{{ $supplier->products->where('stock_quantity', '>', 0)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-500/10 text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                            <p class="text-2xl font-bold text-white">${{ number_format($supplier->products->sum('price'), 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-purple-500/10 text-purple-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Avg. Product Price</p>
                            <p class="text-2xl font-bold text-white">
                                ${{ $supplier->products->count() > 0 ? number_format($supplier->products->avg('price'), 2) : '0.00' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products List -->
            <div class="bg-gray-800 rounded-xl border border-gray-700">
                <div class="p-6 border-b border-gray-700">
                    <h2 class="text-xl font-semibold text-white">Supplier Products</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($supplier->products as $product)
                            <tr class="hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            @if($product->main_image)
                                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $product->main_image) }}" alt="{{ $product->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-amber-400 to-amber-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">{{ strtoupper(substr($product->name, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-400">ID: {{ $product->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ $product->category->name ?? 'No Category' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    ${{ number_format($product->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->stock_quantity > 10)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $product->stock_quantity }} in stock
                                        </span>
                                    @elseif($product->stock_quantity > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $product->stock_quantity }} left
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Out of stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $product->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-300 mb-2">No products found</h3>
                                        <p class="text-gray-500">This supplier hasn't added any products yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ session('error') }}
    </div>
    @endif
</x-admin-layout>