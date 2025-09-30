<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-black to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-block">
                    <span class="text-4xl font-bold bg-gradient-to-r from-white via-gray-300 to-yellow-400 bg-clip-text text-transparent">VOSIZ</span>
                </a>
                <h2 class="mt-6 text-3xl font-extrabold text-white">Welcome Back</h2>
                <p class="mt-2 text-sm text-gray-400">Sign in to your account to continue</p>
            </div>

            <!-- Login Form -->
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-2xl shadow-2xl p-8">
                <x-validation-errors class="mb-6" />

                @session('status')
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <span class="text-green-300 text-sm font-medium">{{ $value }}</span>
                        </div>
                    </div>
                @endsession

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

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
                               autofocus 
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
                               autocomplete="current-password"
                               class="w-full px-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200"
                               placeholder="Enter your password">
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 bg-gray-900 border-gray-600 rounded text-yellow-400 focus:ring-yellow-400 focus:ring-2">
                            <span class="ml-2 text-sm text-gray-300">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" 
                               class="text-sm text-yellow-400 hover:text-yellow-300 font-medium transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-black font-bold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-800">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-800 text-gray-400">Don't have an account?</span>
                    </div>
                </div>

                <!-- Sign Up Link -->
                <div class="text-center">
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-6 py-3 border border-yellow-400 text-yellow-400 font-medium rounded-xl hover:bg-yellow-400 hover:text-black transition-all duration-200">
                        <i class="fas fa-user-plus mr-2"></i>Create New Account
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
