<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Vosiz') }} Admin</title>

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
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #0f0f0f 100%);
            font-family: 'Inter', sans-serif;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, #000000 0%, #1a1a1a 50%, #000000 100%);
            border-right: 1px solid rgba(245, 158, 11, 0.1);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.5);
            transition: width 0.3s ease;
        }

        .admin-sidebar.collapsed {
            width: 80px;
        }

        .admin-sidebar.collapsed .sidebar-text,
        .admin-sidebar.collapsed .badge {
            opacity: 0;
            pointer-events: none;
        }

        .admin-sidebar.collapsed .admin-brand {
            justify-content: center;
        }

        .admin-sidebar.collapsed .admin-brand h1,
        .admin-sidebar.collapsed .admin-brand p {
            display: none;
        }

        .admin-main {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 25%, #262626 75%, #1a1a1a 100%);
            min-height: 100vh;
            margin-left: 320px;
            transition: margin-left 0.3s ease;
        }

        .admin-main.collapsed {
            margin-left: 80px;
        }

        .admin-card {
            background: rgba(38, 38, 38, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(245, 158, 11, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .admin-nav-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-nav-item:hover {
            background: rgba(245, 158, 11, 0.1);
            border-left: 4px solid var(--admin-primary);
            transform: translateX(4px);
        }

        .admin-nav-item.active {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0.05) 100%);
            border-left: 4px solid var(--admin-primary);
            color: var(--admin-primary);
        }

        .admin-topbar {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(245, 158, 11, 0.1);
        }

        .admin-stat-card {
            background: linear-gradient(135deg, rgba(38, 38, 38, 0.9) 0%, rgba(26, 26, 26, 0.9) 100%);
            border: 1px solid rgba(245, 158, 11, 0.1);
            transition: all 0.3s ease;
        }

        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .sidebar-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            background: var(--admin-primary);
            border-radius: 0.5rem;
            padding: 0.5rem;
            display: none;
        }

        @media (max-width: 1024px) {
            .sidebar-toggle {
                display: block;
            }
            
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.open {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0 !important;
            }
        }

        .loading-shimmer {
            background: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.1), transparent);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .sidebar-text {
            transition: opacity 0.3s ease;
        }

        .toggle-sidebar {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 60;
            background: rgba(245, 158, 11, 0.9);
            color: black;
            border: none;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .toggle-sidebar:hover {
            background: rgba(245, 158, 11, 1);
            transform: scale(1.05);
        }

        .toggle-sidebar.collapsed {
            left: 100px;
        }
    </style>
</head>

<body class="font-['Inter'] overflow-x-hidden text-white">
    <!-- Sidebar Toggle Button -->
    <button class="toggle-sidebar" id="sidebar-toggle">
        <i class="fas fa-angle-left text-lg" id="toggle-icon"></i>
    </button>

    <div id="admin-app" class="flex min-h-screen">
        <!-- Sidebar -->
        <nav class="admin-sidebar w-80 fixed h-full z-50" id="admin-sidebar">
            <div class="p-6 h-full flex flex-col">
                <!-- Admin Header -->
                <div class="admin-brand flex items-center mb-10 pb-6 border-b border-gray-800">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center mr-4 shadow-2xl">
                        <i class="fas fa-crown text-black text-xl"></i>
                    </div>
                    <div class="sidebar-text">
                        <h1 class="text-2xl font-bold text-white tracking-wide">VOSIZ</h1>
                        <p class="text-amber-400 text-sm font-semibold uppercase tracking-widest">Admin Panel</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <div class="space-y-2 flex-1 overflow-y-auto">
                    <!-- Dashboard Section -->
                    <div class="mb-6">
                        <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 px-4">Dashboard</div>
                        
                        <a href="{{ route('admin.dashboard') }}" class="admin-nav-item flex items-center p-4 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-gray-300' }}">
                            <i class="fas fa-tachometer-alt text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Overview</span>
                            @if(request()->routeIs('admin.dashboard'))
                                <div class="ml-auto w-2 h-2 bg-amber-400 rounded-full animate-pulse sidebar-text"></div>
                            @endif
                        </a>
                    </div>

                    <!-- Products Section -->
                    <div class="mb-6">
                        <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 px-4 sidebar-text">Products</div>
                        
                        <a href="{{ route('admin.products.manage') }}" class="admin-nav-item flex items-center p-4 rounded-xl {{ request()->routeIs('admin.products*') ? 'active' : 'text-gray-300' }}">
                            <i class="fas fa-box-open text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Manage Products</span>
                            <span class="ml-auto bg-amber-500 text-black text-xs px-2 py-1 rounded-full font-bold badge sidebar-text">CRUD</span>
                        </a>
                    </div>

                    <!-- User Management -->
                    <div class="mb-6">
                        <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 px-4 sidebar-text">User Management</div>
                        
                        <a href="{{ route('admin.users') }}" class="admin-nav-item flex items-center p-4 rounded-xl {{ request()->routeIs('admin.users*') ? 'active' : 'text-gray-300' }}">
                            <i class="fas fa-users text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Customers</span>
                        </a>
                        
                        <a href="{{ route('admin.suppliers') }}" class="admin-nav-item flex items-center p-4 rounded-xl {{ request()->routeIs('admin.suppliers*') ? 'active' : 'text-gray-300' }}">
                            <i class="fas fa-store text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Suppliers</span>
                        </a>
                    </div>

                    <!-- Sales & Orders -->
                    <div class="mb-6">
                        <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 px-4 sidebar-text">Sales & Orders</div>
                        
                        <a href="{{ route('admin.orders') }}" class="admin-nav-item flex items-center p-4 rounded-xl {{ request()->routeIs('admin.orders*') ? 'active' : 'text-gray-300' }}">
                            <i class="fas fa-shopping-cart text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Orders</span>
                        </a>
                        
                        <a href="#" class="admin-nav-item flex items-center p-4 rounded-xl text-gray-300">
                            <i class="fas fa-chart-line text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Analytics</span>
                            <span class="ml-auto text-xs text-gray-500 sidebar-text">Soon</span>
                        </a>
                    </div>

                    <!-- System -->
                    <div class="mb-6">
                        <div class="text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 px-4 sidebar-text">System</div>
                        
                        <a href="#" class="admin-nav-item flex items-center p-4 rounded-xl text-gray-300">
                            <i class="fas fa-cog text-lg mr-4 w-5"></i>
                            <span class="font-semibold sidebar-text">Settings</span>
                            <span class="ml-auto text-xs text-gray-500 sidebar-text">Soon</span>
                        </a>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="mt-8 pt-6 border-t border-gray-800 space-y-3">
                    <!-- View Site Button -->
                    <a href="{{ route('home') }}" target="_blank" 
                       class="flex items-center p-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-black font-semibold hover:from-amber-600 hover:to-amber-700 transition-all duration-300 shadow-lg">
                        <i class="fas fa-external-link-alt mr-3"></i>
                        <span class="sidebar-text">View Live Site</span>
                    </a>
                    
                    <!-- User Info -->
                    <div class="flex items-center p-3 rounded-xl bg-gray-800 bg-opacity-50">
                        <div class="w-10 h-10 bg-gradient-to-r from-amber-400 to-amber-600 rounded-xl flex items-center justify-center mr-3">
                            <span class="text-black font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 sidebar-text">
                            <p class="text-white font-semibold text-sm">{{ auth()->user()->name }}</p>
                            <p class="text-gray-400 text-xs uppercase tracking-wide">Administrator</p>
                        </div>
                    </div>
                    
                    <!-- Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="flex items-center p-3 rounded-xl text-red-400 hover:bg-red-900 hover:bg-opacity-20 hover:text-red-300 transition-all duration-200 w-full text-left font-semibold">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span class="sidebar-text">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="admin-main flex-1" id="admin-main">
            <!-- Top Navigation Bar -->
            <header class="admin-topbar sticky top-0 z-40">
                <div class="flex items-center justify-between px-8 py-6">
                    <div class="flex items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-white">@yield('title', 'Dashboard Overview')</h1>
                            <p class="text-amber-400 text-sm font-medium mt-1">@yield('subtitle', 'Manage your Vosiz platform')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Quick Stats -->
                        <div class="hidden lg:flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <p class="text-gray-400">Server Time</p>
                                <p class="text-white font-semibold" id="current-time">{{ now()->format('H:i:s') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-400">Status</p>
                                <p class="text-green-400 font-semibold flex items-center">
                                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                                    Online
                                </p>
                            </div>
                        </div>

                        <!-- Admin Profile -->
                        <div class="flex items-center space-x-3 bg-gray-800 bg-opacity-30 rounded-xl p-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-black font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-white font-semibold">{{ auth()->user()->name }}</p>
                                <p class="text-gray-400 text-xs">ID: {{ auth()->user()->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-4 rounded-xl mb-6 shadow-lg border border-green-500">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold">Success!</p>
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 rounded-xl mb-6 shadow-lg border border-red-500">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold">Error!</p>
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white p-4 rounded-xl mb-6 shadow-lg border border-yellow-500">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                            <div>
                                <p class="font-semibold">Warning!</p>
                                <p class="text-sm">{{ session('warning') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Main Content -->
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    
    <!-- Admin Dashboard JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('admin-sidebar');
            const mainContent = document.getElementById('admin-main');
            const toggleIcon = document.getElementById('toggle-icon');
            
            let isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            
            // Initialize sidebar state
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                sidebarToggle.classList.add('collapsed');
                toggleIcon.classList.replace('fa-angle-left', 'fa-angle-right');
            }
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    isCollapsed = !isCollapsed;
                    
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                    sidebarToggle.classList.toggle('collapsed');
                    
                    if (isCollapsed) {
                        toggleIcon.classList.replace('fa-angle-left', 'fa-angle-right');
                    } else {
                        toggleIcon.classList.replace('fa-angle-right', 'fa-angle-left');
                    }
                    
                    // Save state
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                });
            }
            
            // Update current time
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { 
                    hour12: false, 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
                const timeElement = document.getElementById('current-time');
                if (timeElement) {
                    timeElement.textContent = timeString;
                }
            }
            
            // Update time every second
            setInterval(updateTime, 1000);
            
            // Smooth scroll for internal links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });

            // Add loading states for navigation
            document.querySelectorAll('.admin-nav-item').forEach(link => {
                link.addEventListener('click', function() {
                    if (this.href && this.href !== '#') {
                        this.classList.add('loading-shimmer');
                    }
                });
            });

            // Auto-hide flash messages after 5 seconds
            const flashMessages = document.querySelectorAll('.bg-gradient-to-r');
            flashMessages.forEach(message => {
                if (message.parentElement.tagName === 'MAIN') {
                    setTimeout(() => {
                        message.style.transition = 'all 0.5s ease';
                        message.style.transform = 'translateX(100%)';
                        message.style.opacity = '0';
                        setTimeout(() => message.remove(), 500);
                    }, 5000);
                }
            });
        });

        // Performance monitoring
        window.addEventListener('load', function() {
            console.log('🚀 Vosiz Admin Panel loaded in:', performance.now().toFixed(2) + 'ms');
        });
    </script>
</body>
</html>