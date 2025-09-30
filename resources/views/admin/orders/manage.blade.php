<x-admin-layout>
    <div class="p-6 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Order Management</h1>
                    <p class="text-gray-400">Track and manage all customer orders</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-500/10 text-blue-400">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Orders</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-yellow-500/10 text-yellow-400">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Pending</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['pending_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-orange-500/10 text-orange-400">
                            <i class="fas fa-cog text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Processing</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['processing_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-500/10 text-green-400">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['completed_orders'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-emerald-500/10 text-emerald-400">
                            <i class="fas fa-dollar-sign text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-400">Total Revenue</p>
                            <p class="text-2xl font-bold text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700">
                <div class="p-6 border-b border-gray-700">
                    <h2 class="text-xl font-semibold text-white">All Orders</h2>
                </div>

                @if($orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Order #</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Customer</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Items</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Total</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Payment</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Date</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($orders as $order)
                                <tr class="hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-yellow-400 font-medium">{{ $order->order_number }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-white font-medium">{{ $order->user->name ?? 'Guest' }}</p>
                                            <p class="text-gray-400 text-sm">{{ $order->user->email ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-white">{{ $order->items->count() }} items</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-white font-medium">${{ number_format($order->total_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                                'processing' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                'shipped' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                                'delivered' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                                'completed' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                                'cancelled' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$order->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $paymentColors = [
                                                'paid' => 'text-green-400',
                                                'pending' => 'text-yellow-400',
                                                'failed' => 'text-red-400',
                                                'refunded' => 'text-purple-400',
                                            ];
                                        @endphp
                                        <span class="{{ $paymentColors[$order->payment_status] ?? 'text-gray-400' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-white text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                                            <p class="text-gray-400 text-xs">{{ $order->created_at->format('h:i A') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewOrder({{ $order->id }})"
                                                    class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                            <div class="relative group">
                                                <button class="p-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors text-sm">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <div class="absolute right-0 top-full mt-2 w-48 bg-gray-800 rounded-lg shadow-lg border border-gray-700 hidden group-hover:block z-10">
                                                    <div class="p-2">
                                                        @foreach(['pending', 'processing', 'shipped', 'delivered', 'completed'] as $status)
                                                            @if($status !== $order->status)
                                                            <button onclick="updateOrderStatus({{ $order->id }}, '{{ $status }}')"
                                                                    class="block w-full text-left px-3 py-2 text-white hover:bg-gray-700 rounded text-sm">
                                                                Mark as {{ ucfirst($status) }}
                                                            </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="px-6 py-4 border-t border-gray-700">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-white mb-2">No Orders Found</h3>
                        <p class="text-gray-400 mb-6">No orders have been placed yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript for Order Management -->
    <script>
        function viewOrder(orderId) {
            // You can implement order details modal or redirect to details page
            window.location.href = `/admin/orders/${orderId}/view`;
        }

        function updateOrderStatus(orderId, status) {
            if (confirm(`Are you sure you want to mark this order as ${status}?`)) {
                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating order status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order status');
                });
            }
        }
    </script>
</x-admin-layout>