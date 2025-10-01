<x-admin-layout>
    <div class="p-6 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Order Details</h1>
                    <p class="text-gray-400">Order #{{ $order->order_number ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('orders.manage') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h2 class="text-xl font-semibold text-white mb-4">Order Summary</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-400">Order Number</p>
                                <p class="text-white font-mono">{{ $order->order_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Order Date</p>
                                <p class="text-white">{{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Status</p>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                        'processing' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                        'shipped' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                        'delivered' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                        'completed' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                        'cancelled' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                    ];
                                    $status = $order->status ?? 'pending';
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Payment Status</p>
                                @php
                                    $paymentColors = [
                                        'paid' => 'text-green-400',
                                        'pending' => 'text-yellow-400',
                                        'failed' => 'text-red-400',
                                        'refunded' => 'text-purple-400',
                                    ];
                                    $paymentStatus = $order->payment_status ?? 'pending';
                                @endphp
                                <span class="{{ $paymentColors[$paymentStatus] ?? 'text-gray-400' }}">
                                    {{ ucfirst($paymentStatus) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h2 class="text-xl font-semibold text-white mb-4">Customer Information</h2>
                        @if($order->user)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-400">Name</p>
                                    <p class="text-white">{{ $order->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Email</p>
                                    <p class="text-white">{{ $order->user->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400">Guest Order</p>
                        @endif
                    </div>

                    <!-- Shipping Information -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h2 class="text-xl font-semibold text-white mb-4">Shipping Information</h2>
                        @if($order->shipping_address)
                            <div class="space-y-2">
                                <p class="text-white">{{ $order->shipping_address['street'] ?? 'No street address' }}</p>
                                <p class="text-gray-400">
                                    {{ $order->shipping_address['city'] ?? '' }}{{ isset($order->shipping_address['state']) ? ', ' . $order->shipping_address['state'] : '' }} {{ $order->shipping_address['zip'] ?? '' }}
                                </p>
                                @if(isset($order->shipping_address['country']))
                                    <p class="text-gray-400">{{ $order->shipping_address['country'] }}</p>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-400">No shipping address provided</p>
                        @endif
                    </div>

                    <!-- Order Items -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h2 class="text-xl font-semibold text-white mb-4">Order Items</h2>
                        @if($order->items && $order->items->count() > 0)
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between py-4 border-b border-gray-700 last:border-b-0">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-16 h-16 bg-gray-700 rounded-lg flex items-center justify-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ Storage::url($item->product->image) }}" 
                                                         alt="{{ $item->product->name ?? 'Product' }}" 
                                                         class="w-full h-full object-cover rounded-lg">
                                                @else
                                                    <i class="fas fa-box text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="text-white font-medium">{{ $item->product->name ?? 'Product Not Found' }}</h3>
                                                <p class="text-gray-400 text-sm">Qty: {{ $item->quantity ?? 0 }}</p>
                                                <p class="text-gray-400 text-sm">Unit Price: ${{ number_format($item->price ?? 0, 2) }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-white font-medium">${{ number_format($item->total ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400">No items found for this order.</p>
                        @endif
                    </div>
                </div>

                <!-- Order Actions & Summary -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                            <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
                            <div class="space-y-2">
                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'completed'] as $status)
                                    @if($status !== $order->status)
                                        <button data-order-id="{{ $order->id }}" data-status="{{ $status }}" onclick="updateOrderStatus(this.dataset.orderId, this.dataset.status)"
                                                class="w-full text-left px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                            Mark as {{ ucfirst($status) }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Order Total -->
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <h2 class="text-xl font-semibold text-white mb-4">Order Total</h2>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal</span>
                                <span class="text-white">${{ number_format(($order->total_amount ?? 0) - ($order->tax_amount ?? 0) - ($order->shipping_cost ?? 0), 2) }}</span>
                            </div>
                            @if($order->shipping_cost && $order->shipping_cost > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Shipping</span>
                                    <span class="text-white">${{ number_format($order->shipping_cost, 2) }}</span>
                                </div>
                            @endif
                            @if($order->tax_amount && $order->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Tax</span>
                                    <span class="text-white">${{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-700 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-white font-semibold">Total</span>
                                    <span class="text-white font-semibold">${{ number_format($order->total_amount ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    @if($order->notes)
                        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                            <h2 class="text-xl font-semibold text-white mb-4">Order Notes</h2>
                            <p class="text-gray-300">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- JavaScript for Order Management -->
    <script>
        function updateOrderStatus(orderId, status) {
            if (confirm(`Are you sure you want to mark this order as ${status}?`)) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                
                if (!csrfToken) {
                    alert('CSRF token not found. Please refresh the page.');
                    return;
                }

                fetch(`/admin/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error updating order status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order status. Please try again.');
                });
            }
        }
    </script>
</x-admin-layout>