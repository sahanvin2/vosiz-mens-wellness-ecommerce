<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Vosiz') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Additional Admin Styles -->
    <style>
        .admin-sidebar {
            background: linear-gradient(180deg, #000000 0%, #1a1a1a 100%);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
        }
        .admin-main {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }
        .admin-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="bg-gray-900 font-['Inter'] overflow-x-hidden">
    <div id="admin-app" class="flex min-h-screen">
        <!-- Sidebar -->
        <nav class="admin-sidebar w-72 shadow-2xl fixed h-full z-50" id="admin-sidebar">
            <div class="p-6 h-full flex flex-col">
                <div class="flex items-center mb-8">
                    <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                        <i class="fas fa-shield-alt text-black text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">VOSIZ</h1>
                        <p class="text-sm text-amber-400 font-medium">Admin Control</p>
                    </div>
                </div>

                <div class="space-y-3 flex-1">
                    <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-4">Main Menu</div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-tachometer-alt text-lg mr-4"></i>
                        <span class="font-semibold">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('admin.products.manage') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.products*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-box-open text-lg mr-4"></i>
                        <span class="font-semibold">Products</span>
                        <span class="ml-auto bg-amber-500 text-black text-xs px-2 py-1 rounded-full">CRUD</span>
                    </a>
                    
                    <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-4 mt-8">Management</div>
                    
                    <a href="{{ route('admin.suppliers.manage') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.suppliers*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-store text-lg mr-4"></i>
                        <span class="font-semibold">Suppliers</span>
                    </a>
                    
                    <a href="{{ route('admin.users.manage') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.users*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-users text-lg mr-4"></i>
                        <span class="font-semibold">Users</span>
                    </a>
                    
                    <a href="{{ route('admin.orders') }}" class="group flex items-center p-4 rounded-xl {{ request()->routeIs('admin.orders*') ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-black shadow-lg' : 'text-gray-300 hover:bg-white hover:bg-opacity-10' }} transition-all duration-300">
                        <i class="fas fa-shopping-cart text-lg mr-4"></i>
                        <span class="font-semibold">Orders</span>
                    </a>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-800">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center p-3 rounded-lg text-red-400 hover:bg-red-900 hover:text-red-300 transition-colors duration-200 w-full text-left">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="admin-main flex-1 ml-72">
            <!-- Top Bar -->
            <header class="bg-black bg-opacity-50 backdrop-filter backdrop-blur-lg border-b border-gray-700 sticky top-0 z-40">
                <div class="flex items-center justify-between px-8 py-6">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="lg:hidden text-white mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-white">@yield('title', 'Dashboard')</h1>
                            <p class="text-amber-400 text-sm font-medium">@yield('subtitle', 'Admin Control Panel')</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('home') }}" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded-lg font-semibold transition-all duration-300 shadow-lg" target="_blank">
                            <i class="fas fa-external-link-alt mr-2"></i>View Site
                        </a>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-black font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-semibold">{{ auth()->user()->name }}</p>
                                <p class="text-gray-400 text-sm">Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                @if(session('success'))
                    <div class="bg-green-600 text-white p-4 rounded-lg mb-6 shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-600 text-white p-4 rounded-lg mb-6 shadow-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    
    <!-- Admin Dashboard Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('admin-sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                });
            }
            
            // Auto-hide sidebar on mobile
            function handleResize() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                }
            }
            
            window.addEventListener('resize', handleResize);
            handleResize();
        });
    </script>
</body>
</html>