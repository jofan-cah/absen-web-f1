<!-- COMPACT RESPONSIVE HEADER -->
<header class="bg-white/80 glass-effect shadow-sm border-b border-gray-200/50 sticky top-0 z-30 backdrop-blur-md">
    <div class="flex items-center justify-between h-12 px-3 lg:px-4">

        <!-- Left Section: Mobile Menu + Breadcrumb -->
        <div class="flex items-center space-x-2 flex-1 min-w-0">
            <!-- Mobile Menu Button -->
            <button
                @click="sidebarOpen = true"
                class="lg:hidden p-1.5 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Breadcrumb Navigation - Hidden on mobile -->
            <nav class="hidden md:flex items-center space-x-1.5 min-w-0" aria-label="Breadcrumb">
                <div class="flex items-center space-x-1.5 min-w-0">
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-gray-500 hover:text-gray-700 text-xs font-medium transition-colors duration-200 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        <span class="truncate">Dashboard</span>
                    </a>
                    @hasSection('breadcrumb')
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-gray-900 text-xs font-semibold truncate">@yield('breadcrumb')</span>
                    @endif
                </div>
            </nav>

            <!-- Page Title (Mobile Only) -->
            <div class="md:hidden flex-1 min-w-0">
                <h1 class="text-sm font-bold text-gray-900 truncate">
                    @yield('page_title', 'Dashboard')
                </h1>
            </div>
        </div>

        <!-- Right Section: Profile Only -->
        <div class="flex items-center flex-shrink-0">
            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center space-x-1.5 p-1.5 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all duration-200">
                    <div class="w-7 h-7 bg-gradient-to-r from-primary-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-semibold text-xs shadow-md flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden lg:block text-left min-w-0">
                        <p class="text-xs font-semibold text-gray-700 truncate">
                            {{ auth()->user()->name ?? 'Admin User' }}
                        </p>
                        <p class="text-xs text-gray-500 capitalize truncate -mt-0.5">
                            {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'admin')) }}
                        </p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-400 hidden lg:block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Profile Dropdown -->
                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-1 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-1 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                     style="display: none;">
                    <!-- User Info -->
                    <div class="px-3 py-2 border-b border-gray-200">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md flex-shrink-0">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">
                                    {{ auth()->user()->name ?? 'Admin User' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                        <a href="{{ route('admin.profile') }}"
                            class="flex items-center px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="truncate">Profile</span>
                        </a>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="truncate">Settings</span>
                        </a>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-3.405-3.405A2.032 2.032 0 0117 11.5V9a5 5 0 10-10 0v2.5c0 .47-.5.7-1.095 1.095L3 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span class="truncate">Notifications</span>
                        </a>
                        <a href="#"
                            class="flex items-center px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <span class="truncate">Help</span>
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="py-1 border-t border-gray-200">
                        <button onclick="confirmLogout()"
                            class="flex items-center w-full px-3 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span class="truncate">Sign Out</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Actions Bar (optional) - Compact -->
    @hasSection('page_actions')
        <div class="border-t border-gray-200 px-3 lg:px-4 py-2 bg-gray-50/50">
            @yield('page_actions')
        </div>
    @endif

</header>
