<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-white">Order Management</h1>
                            <p class="text-gray-400 mt-1">Track and fulfill orders for your products</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-400">Total Orders: {{ $orders->total() }}</span>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <select class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse($orders as $orderItem)
                                <tr class="hover:bg-gray-700/30">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">#{{ $orderItem->order->id }}</div>
                                        <div class="text-sm text-gray-400">{{ $orderItem->order->order_number ?? 'ORD-' . str_pad($orderItem->order->id, 6, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-300 text-sm"></i>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-white">{{ $orderItem->order->user->name ?? 'Guest' }}</div>
                                                <div class="text-sm text-gray-400">{{ $orderItem->order->user->email ?? 'No email' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                @if($orderItem->product->image_url)
                                                    <img src="{{ $orderItem->product->image_url }}" alt="{{ $orderItem->product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="fas fa-box text-gray-300 text-sm"></i>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-white">{{ $orderItem->product->name }}</div>
                                                <div class="text-sm text-gray-400">${{ number_format($orderItem->price, 2) }} each</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white font-medium">{{ $orderItem->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-400">${{ number_format($orderItem->price * $orderItem->quantity, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-500/20 text-yellow-400',
                                                'processing' => 'bg-blue-500/20 text-blue-400',
                                                'shipped' => 'bg-purple-500/20 text-purple-400',
                                                'delivered' => 'bg-green-500/20 text-green-400',
                                                'cancelled' => 'bg-red-500/20 text-red-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$orderItem->order->status] ?? 'bg-gray-500/20 text-gray-400' }}">
                                            {{ ucfirst($orderItem->order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $orderItem->order->created_at->format('M j, Y') }}
                                        <div class="text-xs text-gray-500">{{ $orderItem->order->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button data-order-id="{{ $orderItem->order->id }}" onclick="viewOrder(this.dataset.orderId)" class="text-green-400 hover:text-green-300 p-1" title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($orderItem->order->status === 'pending')
                                            <button data-order-id="{{ $orderItem->order->id }}" onclick="updateOrderStatus(this.dataset.orderId, 'processing')" class="text-blue-400 hover:text-blue-300 p-1" title="Mark as Processing">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            @endif
                                            @if($orderItem->order->status === 'processing')
                                            <button data-order-id="{{ $orderItem->order->id }}" onclick="updateOrderStatus(this.dataset.orderId, 'shipped')" class="text-purple-400 hover:text-purple-300 p-1" title="Mark as Shipped">
                                                <i class="fas fa-shipping-fast"></i>
                                            </button>
                                            @endif
                                            @if(in_array($orderItem->order->status, ['pending', 'processing']))
                                            <button data-order-id="{{ $orderItem->order->id }}" onclick="updateOrderStatus(this.dataset.orderId, 'cancelled')" class="text-red-400 hover:text-red-300 p-1" title="Cancel Order">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="text-gray-400">
                                            <i class="fas fa-shopping-cart text-6xl mb-4"></i>
                                            <p class="text-xl mb-2">No orders found</p>
                                            <p>Orders for your products will appear here</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order Management Scripts -->
    <script>
        function viewOrder(orderId) {
            // Implementation to show order details modal
            console.log('View order details:', orderId);
            // Could open a modal or redirect to order detail page
        }

        function updateOrderStatus(orderId, newStatus) {
            const statusMessages = {
                'processing': 'Mark this order as being processed?',
                'shipped': 'Mark this order as shipped?',
                'cancelled': 'Cancel this order? This action cannot be undone.'
            };

            if (confirm(statusMessages[newStatus])) {
                // AJAX call to update order status
                console.log('Update order', orderId, 'to status', newStatus);
                
                // Example AJAX call (uncomment and implement)
                /*
                fetch(`/supplier/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating order status');
                    }
                });
                */
            }
        }
    </script>
</x-app-layout>