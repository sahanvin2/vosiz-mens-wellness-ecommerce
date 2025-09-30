<x-admin-layout>
    <x-slot:title>User Management</x-slot:title>
    
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">User Management</h2>
            <p class="text-gray-400 mt-1">Manage customer accounts, roles, and permissions</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-users text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Users</p>
                    <p class="text-white text-2xl font-bold">{{ $users->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-user-check text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Active Users</p>
                    <p class="text-white text-2xl font-bold">{{ $users->where('is_active', true)->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-amber-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-alt text-amber-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">This Month</p>
                    <p class="text-white text-2xl font-bold">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-envelope text-purple-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Verified</p>
                    <p class="text-white text-2xl font-bold">{{ $users->whereNotNull('email_verified_at')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 rounded-2xl p-6">
        <form method="GET" action="{{ route('admin.users.manage') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-300 mb-2">Search Users</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by name or email..." 
                           class="w-full bg-gray-700 border border-gray-600 rounded-xl px-4 py-3 pl-10 text-white placeholder-gray-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status Filter</label>
                <select name="status" class="bg-gray-700 border border-gray-600 rounded-xl px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-black px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.users.manage') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-white">All Users</h3>
                    <p class="text-gray-400 text-sm mt-1">Complete list of registered users</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800 bg-opacity-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($users ?? [] as $user)
                    <tr class="hover:bg-gray-800 hover:bg-opacity-30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                                    <span class="text-white font-bold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-white font-semibold">{{ $user->name }}</p>
                                    <p class="text-gray-400 text-sm">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-white">{{ $user->email }}</p>
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2 py-1 bg-green-500 bg-opacity-20 text-green-400 text-xs rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-red-500 bg-opacity-20 text-red-400 text-xs rounded-full">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Unverified
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active ?? true)
                                <span class="inline-flex px-3 py-1 bg-green-500 bg-opacity-20 text-green-400 text-sm rounded-full font-semibold">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 bg-red-500 bg-opacity-20 text-red-400 text-sm rounded-full font-semibold">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white">{{ $user->created_at->format('M d, Y') }}</p>
                            <p class="text-gray-400 text-sm">{{ $user->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white font-semibold">{{ $user->orders_count ?? 0 }}</p>
                            <p class="text-gray-400 text-sm">${{ number_format($user->orders->sum('total_amount') ?? 0, 2) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <!-- View User -->
                                <a href="{{ route('admin.users.view', $user) }}" 
                                   class="p-2 bg-blue-500 bg-opacity-20 text-blue-400 rounded-lg hover:bg-opacity-30 transition-colors"
                                   title="View User Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Toggle Status -->
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active ?? true)
                                        <button type="submit" 
                                                class="p-2 bg-red-500 bg-opacity-20 text-red-400 rounded-lg hover:bg-opacity-30 transition-colors"
                                                title="Deactivate User"
                                                onclick="return confirm('Are you sure you want to deactivate this user?')">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    @else
                                        <button type="submit" 
                                                class="p-2 bg-green-500 bg-opacity-20 text-green-400 rounded-lg hover:bg-opacity-30 transition-colors"
                                                title="Activate User"
                                                onclick="return confirm('Are you sure you want to activate this user?')">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    @endif
                                </form>
                                
                                <!-- Delete User -->
                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 bg-red-500 bg-opacity-20 text-red-400 rounded-lg hover:bg-opacity-30 transition-colors"
                                            title="Delete User"
                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-users text-6xl mb-4"></i>
                                <p class="text-xl font-semibold mb-2">No Users Found</p>
                                <p class="text-sm">Users will appear here once they register</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($users) && $users->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
</x-admin-layout>