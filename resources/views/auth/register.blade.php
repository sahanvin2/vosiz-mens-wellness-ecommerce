<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-black to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-block">
                    <span class="text-4xl font-bold bg-gradient-to-r from-white via-gray-300 to-yellow-400 bg-clip-text text-transparent">VOSIZ</span>
                </a>
                <h2 class="mt-6 text-3xl font-extrabold text-white">Join VOSIZ</h2>
                <p class="mt-2 text-sm text-gray-400">Create your account and start your wellness journey</p>
            </div>

            <!-- Registration Form -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl shadow-2xl p-8">
                <x-validation-errors class="mb-6" />

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-2 text-yellow-400"></i>Full Name
                        </label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               required 
                               autofocus 
                               autocomplete="name"
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                               placeholder="Enter your full name">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-yellow-400"></i>Email Address
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required 
                               autocomplete="username"
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                               placeholder="Enter your email address">
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-yellow-400"></i>Password
                        </label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                               placeholder="Create a strong password">
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-yellow-400"></i>Confirm Password
                        </label>
                        <input id="password_confirmation" 
                               type="password" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                               placeholder="Confirm your password">
                    </div>

                    <!-- User Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-users mr-2 text-yellow-400"></i>Account Type
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative">
                                <input type="radio" name="user_type" value="user" checked 
                                       class="sr-only peer">
                                <div class="p-4 bg-gray-900/50 border border-gray-600 rounded-xl cursor-pointer transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-400/10 hover:border-gray-500">
                                    <div class="text-center">
                                        <i class="fas fa-user text-2xl text-gray-400 mb-2"></i>
                                        <div class="text-sm font-medium text-gray-300">Customer</div>
                                        <div class="text-xs text-gray-500">Shop & Browse Products</div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="user_type" value="supplier" 
                                       class="sr-only peer">
                                <div class="p-4 bg-gray-900/50 border border-gray-600 rounded-xl cursor-pointer transition-all peer-checked:border-yellow-400 peer-checked:bg-yellow-400/10 hover:border-gray-500">
                                    <div class="text-center">
                                        <i class="fas fa-store text-2xl text-gray-400 mb-2"></i>
                                        <div class="text-sm font-medium text-gray-300">Supplier</div>
                                        <div class="text-xs text-gray-500">Manage Products</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 text-center">
                            Note: Admin accounts are managed by the system administrator
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <!-- Terms & Privacy -->
                    <div class="flex items-start">
                        <input id="terms" 
                               name="terms" 
                               type="checkbox" 
                               required
                               class="mt-1 h-4 w-4 bg-gray-900 border-gray-600 rounded text-yellow-400 focus:ring-yellow-400 focus:ring-2">
                        <div class="ml-3 text-sm text-gray-300">
                            I agree to the 
                            <a href="{{ route('terms.show') }}" target="_blank" 
                               class="text-yellow-400 hover:text-yellow-300 font-medium">Terms of Service</a>
                            and 
                            <a href="{{ route('policy.show') }}" target="_blank" 
                               class="text-yellow-400 hover:text-yellow-300 font-medium">Privacy Policy</a>
                        </div>
                    </div>
                    @endif

                    <!-- Register Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-black font-bold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-800">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-800 text-gray-400">Already have an account?</span>
                    </div>
                </div>

                <!-- Sign In Link -->
                <div class="text-center">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-6 py-3 border border-yellow-400 text-yellow-400 font-medium rounded-xl hover:bg-yellow-400 hover:text-black transition-all duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In Instead
                    </a>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-6">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center text-gray-400 hover:text-white text-sm transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
