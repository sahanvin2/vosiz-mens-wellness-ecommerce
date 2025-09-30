<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - Vosiz' : 'Vosiz - Premium Men\'s Health & Wellness' }}</title>
        <meta name="description" content="Premium men's health and wellness products. Luxury skincare, body care, and grooming essentials for the modern man.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            
            /* Fallback dropdown styles */
            .dropdown-menu {
                display: none !important;
                position: absolute;
                top: 100%;
                left: 0;
                z-index: 9999;
                opacity: 0;
                transform: translateY(-10px);
                transition: all 0.3s ease;
            }
            
            .dropdown:hover .dropdown-menu,
            .dropdown.active .dropdown-menu {
                display: block !important;
                opacity: 1;
                transform: translateY(0);
            }
            
            /* Account dropdown hover */
            .account-dropdown-menu {
                display: none !important;
                position: absolute;
                top: 100%;
                right: 0;
                z-index: 9999;
                opacity: 0;
                transform: translateY(-10px);
                transition: all 0.3s ease;
            }
            
            .account-dropdown:hover .account-dropdown-menu,
            .account-dropdown.active .account-dropdown-menu {
                display: block !important;
                opacity: 1;
                transform: translateY(0);
            }
            
            /* Ensure dropdowns are visible */
            .dropdown-trigger {
                cursor: pointer;
            }
            
            /* Force visibility for testing */
            .force-visible {
                display: block !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            /* Ensure navigation is visible */
            nav {
                position: relative !important;
                z-index: 50 !important;
                display: block !important;
            }
            
            /* Debug navigation visibility */
            nav.bg-black\/95 {
                background-color: rgba(0, 0, 0, 0.95) !important;
                border-bottom: 1px solid rgba(75, 85, 99, 1) !important;
            }
        </style>
        
        <script>
            // Check if Alpine.js is loaded
            window.addEventListener('DOMContentLoaded', function() {
                console.log('Alpine.js loaded:', typeof window.Alpine !== 'undefined');
                
                // Fallback JavaScript for dropdowns if Alpine.js fails
                document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Dropdown button clicked');
                        const dropdown = this.nextElementSibling;
                        if (dropdown) {
                            const isVisible = dropdown.style.display === 'block';
                            dropdown.style.display = isVisible ? 'none' : 'block';
                            dropdown.style.opacity = isVisible ? '0' : '1';
                            dropdown.style.transform = isVisible ? 'translateY(-10px)' : 'translateY(0)';
                            console.log('Dropdown toggled:', !isVisible);
                        }
                    });
                });
                
                // Close dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown') && !e.target.closest('.account-dropdown')) {
                        document.querySelectorAll('.dropdown-menu, .account-dropdown-menu').forEach(menu => {
                            menu.style.display = 'none';
                            menu.style.opacity = '0';
                            menu.style.transform = 'translateY(-10px)';
                        });
                    }
                });
                
                // Also add hover functionality as backup
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (menu) {
                        dropdown.addEventListener('mouseenter', function() {
                            menu.style.display = 'block';
                            setTimeout(() => {
                                menu.style.opacity = '1';
                                menu.style.transform = 'translateY(0)';
                            }, 10);
                        });
                        
                        dropdown.addEventListener('mouseleave', function() {
                            menu.style.opacity = '0';
                            menu.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                menu.style.display = 'none';
                            }, 200);
                        });
                    }
                });
                
                // Same for account dropdown
                document.querySelectorAll('.account-dropdown').forEach(dropdown => {
                    const menu = dropdown.querySelector('.account-dropdown-menu');
                    if (menu) {
                        dropdown.addEventListener('mouseenter', function() {
                            menu.style.display = 'block';
                            setTimeout(() => {
                                menu.style.opacity = '1';
                                menu.style.transform = 'translateY(0)';
                            }, 10);
                        });
                        
                        dropdown.addEventListener('mouseleave', function() {
                            menu.style.opacity = '0';
                            menu.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                menu.style.display = 'none';
                            }, 200);
                        });
                    }
                });
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-black text-white">
        <x-banner />

        <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black">
            @include('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-black/50 backdrop-blur-sm shadow-lg border-b border-gray-800">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="text-white">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="relative">
                {{ $slot }}
            </main>
            
            <!-- Footer -->
            <footer class="bg-black border-t border-gray-800 mt-16">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-4">VOSIZ</h3>
                            <p class="text-gray-400">Premium men's health and wellness products for the modern gentleman.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white mb-4">Categories</h4>
                            <ul class="space-y-2 text-gray-400">
                                <li><a href="#" class="hover:text-white transition">Face Care</a></li>
                                <li><a href="#" class="hover:text-white transition">Body Care</a></li>
                                <li><a href="#" class="hover:text-white transition">Hair Care</a></li>
                                <li><a href="#" class="hover:text-white transition">Beard Care</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white mb-4">Support</h4>
                            <ul class="space-y-2 text-gray-400">
                                <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                                <li><a href="#" class="hover:text-white transition">Shipping Info</a></li>
                                <li><a href="#" class="hover:text-white transition">Returns</a></li>
                                <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white mb-4">Connect</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-400 hover:text-white transition">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-white transition">
                                    <i class="fab fa-facebook"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                        <p>&copy; 2025 Vosiz. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
