@extends('layouts.admin')

@section('title', 'Manage Suppliers')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">Manage Suppliers</h1>
        <button 
            onclick="document.getElementById('addSupplierModal').classList.remove('hidden')" 
            class="bg-gradient-to-r from-amber-500 to-yellow-600 text-black px-6 py-3 rounded-lg hover:from-amber-600 hover:to-yellow-700 transition-all duration-300 font-semibold">
            <i class="fas fa-plus mr-2"></i>Add Supplier
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-left text-white font-semibold">Name</th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Email</th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Products</th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Status</th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Joined</th>
                        <th class="px-6 py-4 text-left text-white font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4 text-white">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-black font-bold">{{ substr($supplier->name, 0, 1) }}</span>
                                </div>
                                {{ $supplier->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $supplier->email }}</td>
                        <td class="px-6 py-4 text-gray-300">
                            <span class="bg-amber-600 text-black px-2 py-1 rounded-full text-sm font-semibold">
                                0 products
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $supplier->is_active ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-300">{{ $supplier->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <form action="{{ route('admin.suppliers.toggle', $supplier->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded text-sm {{ $supplier->is_active ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }} transition-colors duration-200">
                                        {{ $supplier->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <a href="{{ route('supplier.dashboard', $supplier->id) }}" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-black rounded text-sm font-medium transition-colors duration-200">
                                    View Dashboard
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-users text-6xl mb-4"></i>
                            <p class="text-xl">No suppliers found</p>
                            <p class="text-gray-500 mt-2">Add your first supplier to get started</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="px-6 py-4 bg-gray-900 border-t border-gray-700">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Supplier Modal -->
<div id="addSupplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-gray-800 rounded-xl p-8 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Add New Supplier</h2>
            <button onclick="document.getElementById('addSupplierModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.suppliers.create') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-white font-semibold mb-2">Name</label>
                <input type="text" id="name" name="name" required
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-white font-semibold mb-2">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-white font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-white font-semibold mb-2">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-4 py-3 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-amber-500 focus:outline-none">
            </div>

            <div class="flex space-x-4">
                <button type="button" onclick="document.getElementById('addSupplierModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-amber-500 to-yellow-600 text-black rounded-lg hover:from-amber-600 hover:to-yellow-700 transition-all duration-300 font-semibold">
                    Create Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection