<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel' }} - {{ config('app.name', 'Vosiz') }} Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Admin Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iOCIgZmlsbD0iIzAwMDAwMCIvPgo8cGF0aCBkPSJNMTYgOEMxMiA4IDggMTIgOCAxNkM4IDIwIDEyIDI0IDE2IDI0QzIwIDI0IDI0IDIwIDI0IDE2QzI0IDEyIDIwIDggMTYgOFoiIGZpbGw9IiNGRkJGMDAiLz4KPHN2Zz4K">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Admin Dashboard Styles -->
    <style>
        :root {
            --admin-primary: #f59e0b;
            --admin-primary-dark: #d97706;
            --admin-bg: #0f0f0f;
            --admin-surface: #1a1a1a;
            --admin-card: #262626;
        }

        body {
            background: var(--admin-bg);
            font-family: 'Inter', sans-serif;
        }

        .admin-card {
            background: var(--admin-card);
            border: 1px solid #404040;
        }

        .admin-sidebar {
            background: linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
        }

        .admin-nav-link {
            transition: all 0.3s ease;
        }

        .admin-nav-link:hover {
            background: rgba(245, 158, 11, 0.1);
            border-left: 4px solid #f59e0b;
        }

        .admin-nav-link.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #000;
            border-left: 4px solid #f59e0b;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #404040;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>

<body class="font-sans antialiased min-h-screen bg-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 admin-sidebar shadow-2xl" x-data="{ collapsed: false }">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between p-6 border-b border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center">
                            <span class="text-black font-bold text-lg">V</span>
                        </div>
                        <div>
                            <h1 class="text-white font-bold text-xl">VOSIZ</h1>
                            <p class="text-gray-400 text-xs">Admin Panel</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2">
                    <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-4">Main</div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-home text-lg mr-4"></i>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('admin.users') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-users text-lg mr-4"></i>
                        <span class="font-semibold">Users</span>
                    </a>
                    
                    <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-4 mt-8">Catalog</div>
                    
                    <a href="{{ route('admin.products.manage') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.products*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-box-open text-lg mr-4"></i>
                        <span class="font-semibold">Products</span>
                        <span class="ml-auto bg-amber-500 text-black text-xs px-2 py-1 rounded-full">CRUD</span>
                    </a>
                    
                    <a href="{{ route('admin.categories.manage') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.categories*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-tags text-lg mr-4"></i>
                        <span class="font-semibold">Categories</span>
                        <span class="ml-auto bg-amber-500 text-black text-xs px-2 py-1 rounded-full">CRUD</span>
                    </a>
                    
                    <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-4 mt-8">Management</div>
                    
                    <a href="{{ route('admin.suppliers') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.suppliers*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-store text-lg mr-4"></i>
                        <span class="font-semibold">Suppliers</span>
                    </a>
                    
                    <a href="{{ route('admin.orders') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.orders*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-shopping-cart text-lg mr-4"></i>
                        <span class="font-semibold">Orders</span>
                    </a>
                </nav>

                <!-- User Menu -->
                <div class="p-4 border-t border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <span class="text-black font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ Auth::user()->name }}</p>
                                <p class="text-gray-400 text-xs">Administrator</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden">
            <!-- Top Bar -->
            <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $title ?? 'Admin Panel' }}</h2>
                        <p class="text-gray-400 text-sm">{{ $subtitle ?? 'Manage your application' }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Back to Site -->
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-globe mr-2"></i>
                            View Site
                        </a>
                        
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif
</body>
</html>