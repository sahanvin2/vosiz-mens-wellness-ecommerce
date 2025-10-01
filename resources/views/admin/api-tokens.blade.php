<x-admin-layout>
    <x-slot name="title">API Token Management</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">API Token Management</h2>
                <p class="text-gray-400">Create and manage API tokens for external integrations</p>
            </div>

            @if(session('token'))
            <!-- Token Display (shown once after creation) -->
            <div class="bg-green-900/50 border border-green-600 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-green-400 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    API Token Created Successfully
                </h3>
                <p class="text-gray-300 mb-3">Copy this token now - it won't be shown again:</p>
                <div class="bg-gray-800 rounded-lg p-4 font-mono text-sm break-all">
                    <code class="text-green-400">{{ session('token') }}</code>
                </div>
                <button onclick="copyToken()" class="mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-copy mr-2"></i>Copy Token
                </button>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Create New Token -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                        <h3 class="text-xl font-semibold text-white flex items-center">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Create New API Token
                        </h3>
                    </div>

                    <form action="{{ route('admin.api.tokens.create') }}" method="POST" class="p-6">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Token Name</label>
                            <input type="text" name="name" id="name" required
                                   class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                   placeholder="e.g., Mobile App, External API, etc.">
                            @error('name')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-300 mb-3">Token Abilities</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="abilities[]" value="*" checked
                                           class="rounded border-gray-600 text-blue-600 focus:ring-blue-400 bg-gray-800">
                                    <span class="ml-2 text-gray-300">All Permissions</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="abilities[]" value="products:read"
                                           class="rounded border-gray-600 text-blue-600 focus:ring-blue-400 bg-gray-800">
                                    <span class="ml-2 text-gray-300">Read Products</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="abilities[]" value="products:create"
                                           class="rounded border-gray-600 text-blue-600 focus:ring-blue-400 bg-gray-800">
                                    <span class="ml-2 text-gray-300">Create Products</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="abilities[]" value="orders:read"
                                           class="rounded border-gray-600 text-blue-600 focus:ring-blue-400 bg-gray-800">
                                    <span class="ml-2 text-gray-300">Read Orders</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="abilities[]" value="users:read"
                                           class="rounded border-gray-600 text-blue-600 focus:ring-blue-400 bg-gray-800">
                                    <span class="ml-2 text-gray-300">Read Users</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-key mr-2"></i>
                            Create API Token
                        </button>
                    </form>
                </div>

                <!-- Existing Tokens -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h3 class="text-xl font-semibold text-white flex items-center">
                            <i class="fas fa-list mr-3"></i>
                            Existing API Tokens
                        </h3>
                    </div>

                    <div class="p-6">
                        @if(auth()->user()->tokens->count() > 0)
                            <div class="space-y-4">
                                @foreach(auth()->user()->tokens as $token)
                                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-white">{{ $token->name }}</h4>
                                            <p class="text-sm text-gray-400">
                                                Created: {{ $token->created_at->format('M d, Y H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-400">
                                                Last used: {{ $token->last_used_at ? $token->last_used_at->format('M d, Y H:i') : 'Never' }}
                                            </p>
                                            @if($token->abilities)
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500">Abilities:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($token->abilities as $ability)
                                                            <span class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded">
                                                                {{ $ability }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <form action="{{ route('admin.api.tokens.revoke', $token->id) }}" method="POST" class="ml-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to revoke this token?')"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-key text-gray-600 text-4xl mb-4"></i>
                                <p class="text-gray-400">No API tokens created yet</p>
                                <p class="text-sm text-gray-500">Create your first token to get started</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- API Documentation -->
            <div class="mt-8 bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                    <h3 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-book mr-3"></i>
                        API Usage Examples
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-white mb-3">JavaScript/AJAX</h4>
                            <pre class="bg-gray-900 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-green-400">// Get products
fetch('/api/products/featured', {
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN_HERE',
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data));</code></pre>
                        </div>

                        <div>
                            <h4 class="font-semibold text-white mb-3">cURL</h4>
                            <pre class="bg-gray-900 rounded-lg p-4 text-sm overflow-x-auto"><code class="text-green-400">curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     -H "Accept: application/json" \
     {{ url('/api/user') }}</code></pre>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="font-semibold text-white mb-3">Available Endpoints</h4>
                        <div class="bg-gray-900 rounded-lg p-4">
                            <div class="space-y-2 text-sm">
                                <div><span class="text-blue-400">GET</span> <span class="text-gray-300">/api/user</span> - Get authenticated user</div>
                                <div><span class="text-blue-400">GET</span> <span class="text-gray-300">/api/products/featured</span> - Get featured products</div>
                                <div><span class="text-blue-400">GET</span> <span class="text-gray-300">/api/categories</span> - Get categories</div>
                                <div><span class="text-blue-400">GET</span> <span class="text-gray-300">/api/orders</span> - Get user orders</div>
                                <div><span class="text-orange-400">POST</span> <span class="text-gray-300">/api/admin/products</span> - Create product (admin only)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToken() {
            const tokenText = document.querySelector('code').textContent;
            navigator.clipboard.writeText(tokenText).then(() => {
                alert('Token copied to clipboard!');
            });
        }

        // Handle checkbox logic for abilities
        document.querySelector('input[value="*"]').addEventListener('change', function() {
            const otherCheckboxes = document.querySelectorAll('input[name="abilities[]"]:not([value="*"])');
            if (this.checked) {
                otherCheckboxes.forEach(cb => cb.disabled = true);
            } else {
                otherCheckboxes.forEach(cb => cb.disabled = false);
            }
        });
    </script>
</x-admin-layout>