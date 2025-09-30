<x-admin-layout>
    <x-slot:title>User Details</x-slot:title>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">User Details</h2>
            <p class="text-gray-400 mt-1">View and manage user account information</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.manage') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 rounded-2xl p-6">
        <div class="flex items-start space-x-6">
            <!-- Avatar -->
            <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                <span class="text-white text-3xl font-bold">{{ substr($user->name, 0, 1) }}</span>
            </div>
            
            <!-- User Info -->
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $user->name }}</h3>
                        <p class="text-gray-400">{{ $user->email }}</p>
                        <p class="text-gray-500 text-sm">User ID: {{ $user->id }}</p>
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="text-right">
                        @if($user->is_active ?? true)
                            <span class="inline-flex px-4 py-2 bg-green-500 bg-opacity-20 text-green-400 text-sm rounded-full font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>Active
                            </span>
                        @else
                            <span class="inline-flex px-4 py-2 bg-red-500 bg-opacity-20 text-red-400 text-sm rounded-full font-semibold">
                                <i class="fas fa-times-circle mr-2"></i>Inactive
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- User Details -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div>
                        <p class="text-gray-400 text-sm">Joined</p>
                        <p class="text-white font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Email Status</p>
                        @if($user->email_verified_at)
                            <p class="text-green-400 font-semibold">Verified</p>
                        @else
                            <p class="text-red-400 font-semibold">Unverified</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Last Activity</p>
                        <p class="text-white font-semibold">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Role</p>
                        <p class="text-white font-semibold capitalize">{{ $user->role }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-shopping-bag text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Orders</p>
                    <p class="text-white text-2xl font-bold">{{ $userStats['total_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-dollar-sign text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Spent</p>
                    <p class="text-white text-2xl font-bold">${{ number_format($userStats['total_spent'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-amber-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-shopping-cart text-amber-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Cart Items</p>
                    <p class="text-white text-2xl font-bold">{{ $userStats['cart_items'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 p-6 rounded-2xl">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-500 bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-star text-purple-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Avg Order</p>
                    <p class="text-white text-2xl font-bold">
                        ${{ $userStats['total_orders'] > 0 ? number_format($userStats['total_spent'] / $userStats['total_orders'], 2) : '0.00' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 rounded-2xl p-6">
        <h3 class="text-xl font-bold text-white mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <!-- Toggle Status -->
            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline-block">
                @csrf
                @method('PATCH')
                @if($user->is_active ?? true)
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300"
                            onclick="return confirm('Are you sure you want to deactivate this user?')">
                        <i class="fas fa-user-slash mr-2"></i>Deactivate User
                    </button>
                @else
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300"
                            onclick="return confirm('Are you sure you want to activate this user?')">
                        <i class="fas fa-user-check mr-2"></i>Activate User
                    </button>
                @endif
            </form>
            
            <!-- Delete User -->
            @if($user->role !== 'admin')
            <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300"
                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">
                    <i class="fas fa-trash mr-2"></i>Delete User
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    @if(isset($userStats['recent_orders']) && $userStats['recent_orders']->count() > 0)
    <div class="bg-gray-800 bg-opacity-50 backdrop-blur-sm border border-gray-700 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-xl font-bold text-white">Recent Orders</h3>
            <p class="text-gray-400 text-sm mt-1">Latest orders from this user</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800 bg-opacity-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($userStats['recent_orders'] as $order)
                    <tr class="hover:bg-gray-800 hover:bg-opacity-30 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-white font-semibold">#{{ $order->id }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white">{{ $order->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 bg-green-500 bg-opacity-20 text-green-400 text-sm rounded-full font-semibold">
                                {{ ucfirst($order->status ?? 'completed') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
</x-admin-layout>