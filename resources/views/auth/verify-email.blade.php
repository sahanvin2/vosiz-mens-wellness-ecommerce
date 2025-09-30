<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verification - Vosiz</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-gradient-to-br from-gray-900 to-black border border-amber-500/20 rounded-2xl p-8 shadow-2xl">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-amber-400 to-amber-600 rounded-2xl mb-4">
                    <i class="fas fa-envelope text-black text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">VOSIZ</h1>
                <p class="text-amber-400 text-sm font-semibold uppercase tracking-widest">Email Verification</p>
            </div>

            <!-- Content -->
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-white mb-4">Verify Your Email Address</h2>
                <p class="text-gray-300 text-sm leading-relaxed mb-6">
                    Before continuing, please verify your email address by clicking on the link we just sent to <strong class="text-amber-400">{{ auth()->user()->email }}</strong>. 
                </p>
                <p class="text-gray-400 text-xs">
                    Didn't receive the email? We'll gladly send you another.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="bg-gradient-to-r from-green-600/20 to-green-700/20 border border-green-500/30 text-green-400 p-4 rounded-xl mb-6 text-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-sm">A new verification link has been sent to your email address!</span>
                </div>
            @endif

            <!-- Actions -->
            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-black font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-amber-500/25">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Resend Verification Email
                    </button>
                </form>

                <div class="flex items-center space-x-4 text-sm">
                    <a href="{{ route('profile.show') }}" 
                       class="flex-1 text-center text-amber-400 hover:text-amber-300 font-medium py-2 px-4 border border-amber-500/30 rounded-xl hover:border-amber-400/50 transition-all duration-200">
                        <i class="fas fa-user-edit mr-2"></i>
                        Edit Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full text-gray-400 hover:text-gray-300 font-medium py-2 px-4 border border-gray-600/30 rounded-xl hover:border-gray-500/50 transition-all duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 pt-6 border-t border-gray-800 text-center">
                <p class="text-gray-500 text-xs">
                    Check your spam/junk folder if you don't see the email in your inbox.
                </p>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-amber-400 text-sm font-medium transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Vosiz Store
            </a>
        </div>
    </div>
</body>
</html>
