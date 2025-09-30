<nav x-data="{ open: false }" class="bg-black/95 backdrop-blur-sm border-b border-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="text-2xl font-bold bg-gradient-to-r from-white via-vosiz-silver to-vosiz-gold bg-clip-text text-transparent">VOSIZ</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')">
                        {{ __('Products') }}
                    </x-nav-link>
                    
                    <!-- Categories Dropdown -->
                    <div class="relative dropdown" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @keydown.escape.window="open = false"
                                data-dropdown-toggle
                                class="inline-flex items-center px-1 pt-1 border-b-2 dropdown-trigger {{ request()->routeIs('categories.*') ? 'border-yellow-400 text-white' : 'border-transparent text-gray-300 hover:text-white hover:border-gray-500' }} text-sm font-medium leading-5 focus:outline-none focus:text-white focus:border-gray-500 transition duration-150 ease-in-out">
                            Categories
                            <svg class="ml-1 h-4 w-4 transition-transform duration-200" 
                                 :class="{ 'rotate-180': open }" 
                                 xmlns="http://www.w3.org/2000/svg" 
                                 viewBox="0 0 20 20" 
                                 fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="open = false"
                             class="dropdown-menu absolute left-0 top-full mt-1 w-80 bg-gray-900/98 backdrop-blur-md border border-gray-700 rounded-xl shadow-2xl z-[9999] overflow-hidden"
                             x-cloak
                             style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05); display: none;">
                            <div class="py-4">
                                <div class="px-6 py-2 border-b border-gray-700/50">
                                    <div class="flex items-center">
                                        <i class="fas fa-th-large mr-3 text-yellow-400"></i>
                                        <h3 class="font-semibold text-white">Product Categories</h3>
                                    </div>
                                </div>
                                
                                <div class="py-2">
                                    <a href="{{ route('products.index') }}" 
                                       class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-500 hover:text-black transition-all duration-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-grid-3x3 mr-3 text-yellow-400"></i>
                                            <span>All Products</span>
                                        </div>
                                    </a>

                                    @if(isset($categories) && $categories->count() > 0)
                                        @foreach($categories as $category)
                                            <a href="{{ route('categories.show', ['category' => $category->slug ?? \Illuminate\Support\Str::slug($category->name ?? '')]) }}" 
                                               class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-500 hover:text-black transition-all duration-200">
                                                <div class="flex items-center">
                                                    <i class="fas fa-tag mr-3 text-yellow-400"></i>
                                                    <span>{{ $category->name ?? 'Unnamed Category' }}</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="px-6 py-4 text-center">
                                            <div class="text-gray-500 mb-2">
                                                <i class="fas fa-inbox text-2xl"></i>
                                            </div>
                                            <p class="text-gray-400 text-sm">No categories available</p>
                                            <p class="text-gray-500 text-xs mt-1">Categories will appear here when added</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Dashboard Link (only for admin users) -->
                @auth
                    @if(Auth::user()->isAdmin())
                        <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.*')">
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>
                    @endif
                @endauth
            </div>

            <!-- Right Side Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Guest Navigation Links -->
                @guest
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-transparent border border-gray-300 rounded-lg text-gray-300 hover:text-white hover:border-yellow-400 hover:bg-yellow-400/10 transition-all duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-yellow-400 text-black rounded-lg font-semibold hover:bg-yellow-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-user-plus mr-2"></i>
                            Sign Up
                        </a>
                    </div>
                @endguest

                <!-- Teams Dropdown -->
                @auth
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <span class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ Auth::user()->currentTeam->name ?? Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    @if(Auth::user()->currentTeam)
                                        <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                            {{ __('Team Settings') }}
                                        </x-dropdown-link>
                                    @endif

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif

                    <!-- Cart Icon -->
                    <div class="ms-3 relative">
                        <a href="{{ route('cart.index') }}" 
                           class="flex items-center justify-center w-10 h-10 bg-gray-800/50 border border-gray-700 rounded-xl hover:bg-gray-700/70 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all duration-200">
                            <i class="fas fa-shopping-cart text-gray-300 hover:text-yellow-400 transition-colors"></i>
                            @php $cartCount = Auth::user()->cartItems()->sum('quantity') ?? 0; @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-black bg-yellow-400 rounded-full">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative account-dropdown" x-data="{ open: false }">
                        <button @click="open = !open" 
                                @keydown.escape.window="open = false"
                                data-dropdown-toggle
                                class="flex items-center text-sm bg-gray-800/50 border border-gray-700 rounded-xl px-3 py-2 hover:bg-gray-700/70 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition-all duration-200">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="w-8 h-8 rounded-lg object-cover mr-2" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @endif
                            <span class="text-white font-medium">{{ Str::limit(Auth::user()->name, 12) }}</span>
                            <svg class="ml-2 h-4 w-4 text-gray-400 transition-transform duration-200" 
                                 :class="{ 'rotate-180': open }" 
                                 xmlns="http://www.w3.org/2000/svg" 
                                 viewBox="0 0 20 20" 
                                 fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="open = false"
                             class="account-dropdown-menu absolute right-0 top-full mt-1 w-64 bg-gray-900/98 backdrop-blur-md border border-gray-700 rounded-xl shadow-2xl z-[9999] overflow-hidden"
                             x-cloak
                             style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05); display: none;">
                            <div class="py-2">
                                <!-- Account Header -->
                                <div class="px-6 py-4 border-b border-gray-700/50">
                                    <div class="flex items-center">
                                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                            <img class="w-10 h-10 rounded-lg object-cover mr-3" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                        @else
                                            <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center mr-3">
                                                <span class="text-black font-bold text-lg">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-white">{{ Auth::user()->name }}</div>
                                            <div class="text-sm text-gray-400">{{ Auth::user()->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Management -->
                                <div class="py-2">
                                    <div class="px-6 py-2 text-xs uppercase tracking-wide text-gray-500 font-semibold">
                                        Manage Account
                                    </div>

                                    <a href="{{ route('profile.show') }}" class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-500 hover:text-black transition-all duration-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-user-circle mr-3 text-yellow-400"></i>
                                            Profile Settings
                                        </div>
                                    </a>

                                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                        <a href="{{ route('api-tokens.index') }}" class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-500 hover:text-black transition-all duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-key mr-3 text-yellow-400"></i>
                                                API Tokens
                                            </div>
                                        </a>
                                    @endif

                                    <div class="border-t border-gray-700/50 my-2"></div>

                                    <!-- Role-specific Links -->
                                    @if(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-600 hover:text-white transition-all duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-shield-alt mr-3 text-blue-400"></i>
                                                Admin Dashboard
                                            </div>
                                        </a>
                                    @elseif(Auth::user()->isSupplier())
                                        <a href="{{ route('supplier.dashboard') }}" class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-green-500 hover:to-green-600 hover:text-white transition-all duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-store mr-3 text-green-400"></i>
                                                Supplier Dashboard
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ route('dashboard') }}" class="block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-purple-500 hover:to-purple-600 hover:text-white transition-all duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-tachometer-alt mr-3 text-purple-400"></i>
                                                Dashboard
                                            </div>
                                        </a>
                                    @endif

                                    <div class="border-t border-gray-700/50 my-2"></div>

                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <button type="submit" class="w-full text-left block px-6 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-red-500 hover:to-red-600 hover:text-white transition-all duration-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                                Log Out
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-900 transition duration-200 ease-in-out">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-900/98 backdrop-blur-md border-t border-gray-700">
        <div class="pt-4 pb-3 space-y-2 px-4">
            <!-- Home Link -->
            <a href="{{ route('home') }}" 
               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-500 hover:text-black rounded-lg transition-all duration-200 {{ request()->routeIs('home') ? 'bg-yellow-400 text-black' : '' }}">
                <i class="fas fa-home mr-3"></i>
                Home
            </a>
            
            <!-- Products Link -->
            <a href="{{ route('products.index') }}" 
               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-500 hover:text-black rounded-lg transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-yellow-400 text-black' : '' }}">
                <i class="fas fa-box mr-3"></i>
                Products
            </a>
            
            <!-- Mobile Categories Dropdown -->
            <div x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 text-gray-300 hover:bg-gray-700/50 rounded-lg transition-all duration-200">
                    <div class="flex items-center">
                        <i class="fas fa-th-large mr-3"></i>
                        <span>Categories</span>
                    </div>
                    <svg class="ml-2 h-4 w-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="ml-8 mt-2 space-y-1 bg-gray-800/50 rounded-lg p-2">
                    <a href="{{ route('products.index') }}" 
                       class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-700/50 hover:text-yellow-400 rounded-md transition-colors duration-200">
                        All Products
                    </a>
                    
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <a href="{{ route('categories.show', ['category' => $category->slug ?? \Illuminate\Support\Str::slug($category->name ?? '')]) }}" 
                               class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-700/50 hover:text-yellow-400 rounded-md transition-colors duration-200">
                                {{ $category->name ?? 'Unnamed Category' }}
                            </a>
                        @endforeach
                    @else
                        <div class="px-4 py-2 text-sm text-gray-500">
                            No categories available
                        </div>
                    @endif
                </div>
            </div>
            
            @auth
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    @if(Auth::user()->currentTeam)
                        <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                            {{ __('Team Settings') }}
                        </x-responsive-nav-link>
                    @endif

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        @else
            <!-- Responsive Guest Navigation -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('login') }}">
                        {{ __('Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('register') }}">
                        {{ __('Sign Up') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
        </div>
    </div>
</nav>
