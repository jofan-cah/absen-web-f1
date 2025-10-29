<!-- COMPACT MODERN SIDEBAR -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-56 bg-white/95 backdrop-blur-xl shadow-2xl border-r border-gray-200/50
           lg:translate-x-0 sidebar-transition">
    <div class="flex flex-col h-full">

        <!-- Compact Header -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            <div class="relative flex items-center justify-between h-12 px-3 text-white">
                <div class="flex items-center space-x-2">
                    <div
                        class="w-7 h-7 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/20 shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-sm font-bold truncate tracking-wide">ABSENSI F1</h1>
                        <p class="text-xs text-white/80 truncate -mt-0.5">Sistem ISP</p>
                    </div>
                </div>
                <!-- Mobile close -->
                <button @click="sidebarOpen = false" class="lg:hidden p-1 hover:bg-white/10 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Compact Navigation -->
        <nav class="flex-1 px-2.5 py-2.5 overflow-y-auto sidebar-scroll">

            <!-- Dashboard -->
            <div class="mb-1.5">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} relative flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-300 group hover:scale-[1.02]"
                    @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                    <div
                        class="flex items-center justify-center w-7 h-7 mr-2 rounded-lg nav-icon bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                        </svg>
                    </div>
                    <span class="truncate text-gray-700 group-hover:text-blue-700">Dashboard</span>
                </a>
            </div>

            @php
                $userRole = auth()->user()->role;
                $isAdmin = $userRole === 'admin';
                $coordinatorRoles = ['coordinator', 'koordinator', 'wakil_coordinator', 'wakil_koordinator'];
                $isCoordinator = in_array($userRole, $coordinatorRoles);
            @endphp

            <!-- Karyawan - ADMIN ONLY -->
            @if ($isAdmin)
                <div class="mb-1.5">
                    <a href="{{ route('admin.karyawan.index') }}"
                        class="nav-item {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }} relative flex items-center px-2.5 py-2 text-xs font-semibold rounded-xl transition-all duration-300 group hover:scale-[1.02]"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-7 h-7 mr-2 rounded-lg nav-icon bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-md">
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <span class="truncate text-gray-700 group-hover:text-green-700">Karyawan</span>
                    </a>
                </div>
            @endif

            <!-- Master Data - ADMIN ONLY -->
            @if ($isAdmin)
                <div class="mb-1.5">
                    <div class="flex items-center px-2.5 py-1.5 mb-1">
                        <div class="w-1.5 h-1.5 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mr-2"></div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Master</h3>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.department.index') }}"
                            class="nav-item {{ request()->routeIs('admin.department.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-violet-700">Department</span>
                        </a>

                        <a href="{{ route('admin.shift.index') }}"
                            class="nav-item {{ request()->routeIs('admin.shift.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-orange-400 to-red-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-orange-700">Shift Kerja</span>
                        </a>

                        <a href="{{ route('admin.user.index') }}"
                            class="nav-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-indigo-400 to-blue-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-indigo-700">User</span>
                            <span
                                class="text-xs bg-indigo-100 text-indigo-700 font-bold px-1.5 py-0.5 rounded-full">{{ App\Models\User::count() }}</span>
                        </a>

                    </div>
                </div>
            @endif

            <!-- Ijin Section -->
            <div class="mb-1.5">
                <div class="flex items-center px-2.5 py-1.5 mb-1">
                    <div
                        class="w-1.5 h-1.5 bg-gradient-to-r from-pink-500 to-rose-500 rounded-full mr-2 animate-pulse">
                    </div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ijin</h3>
                </div>
                <div class="space-y-1">

                    <!-- Daftar Ijin - Semua user bisa lihat -->
                    <a href="{{ route('admin.ijin.index') }}"
                        class="nav-item {{ request()->routeIs('admin.ijin.index') || request()->routeIs('admin.ijin.show') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-indigo-400 to-purple-500 text-white shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-indigo-700">Daftar Ijin</span>
                    </a>

                    <!-- Coordinator Review - Hanya Coordinator -->
                    @if (auth()->user()->role === 'coordinator' || auth()->user()->role === 'admin')
                        <a href="{{ route('admin.ijin.coordinator-pending') }}"
                            class="nav-item {{ request()->routeIs('admin.ijin.coordinator-pending') || request()->routeIs('admin.ijin.coordinator-review-form') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-yellow-400 to-orange-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-orange-700">Review Ijin</span>
                            @php
                                $coordPending = \App\Models\Ijin::where('coordinator_id', auth()->user()->user_id)
                                    ->where('coordinator_status', 'pending')
                                    ->where('status', 'pending')
                                    ->count();
                            @endphp
                            @if ($coordPending > 0)
                                <span
                                    class="text-xs bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded-full animate-pulse">{{ $coordPending }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Admin Review - Hanya Admin -->
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.ijin.admin-pending') }}"
                            class="nav-item {{ request()->routeIs('admin.ijin.admin-pending') || request()->routeIs('admin.ijin.admin-review-form') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-green-400 to-emerald-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-green-700">Approval Ijin</span>
                            @php
                                $adminPending = \App\Models\Ijin::where('status', 'pending')->count();
                            @endphp
                            @if ($adminPending > 0)
                                <span
                                    class="text-xs bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded-full animate-pulse">{{ $adminPending }}</span>
                            @endif
                        </a>

                        <!-- Tipe Ijin - Hanya Admin -->
                        <a href="{{ route('admin.ijin-type.index') }}"
                            class="nav-item {{ request()->routeIs('admin.ijin-type.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-pink-400 to-rose-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-pink-700">Tipe Ijin</span>
                        </a>

                        <!-- Statistik - Hanya Admin -->
                        <a href="{{ route('admin.ijin.statistics') }}"
                            class="nav-item {{ request()->routeIs('admin.ijin.statistics') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-blue-400 to-cyan-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-blue-700">Statistik</span>
                        </a>
                    @endif

                </div>
            </div>

            <!-- Jadwal Section -->
            <div class="mb-1.5">
                <div class="flex items-center px-2.5 py-1.5 mb-1">
                    <div
                        class="w-1.5 h-1.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mr-2 animate-pulse">
                    </div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jadwal</h3>
                </div>
                <div class="space-y-1">
                    @if ($isAdmin)
                        <a href="{{ route('admin.jadwal.index') }}"
                            class="nav-item {{ request()->routeIs('admin.jadwal.index') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-blue-400 to-cyan-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-blue-700">Data Jadwal</span>
                        </a>
                    @endif

                    <a href="{{ route('admin.jadwal.calendar') }}"
                        class="nav-item {{ request()->routeIs('admin.jadwal.calendar') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-indigo-400 to-purple-500 text-white shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-indigo-700">Calendar</span>
                        @if ($isCoordinator)
                            <span
                                class="text-xs bg-green-100 text-green-700 font-bold px-1.5 py-0.5 rounded-full">Dept</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Lembur Section - BARU untuk Koordinator -->
            @if (auth()->user()->role === 'koordinator'))
                <div class="mb-1.5">
                    <div class="flex items-center px-2.5 py-1.5 mb-1">
                        <div
                            class="w-1.5 h-1.5 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mr-2 animate-pulse">
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Lembur</h3>
                    </div>
                    <div class="space-y-1">

                        <!-- Menu Lembur Department -->
                        <a href="{{ route('koordinator.lembur.index') }}"
                            class="nav-item {{ request()->routeIs('koordinator.lembur.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-orange-400 to-red-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-orange-700">Lembur Dept</span>
                            @php
                                $koordinator = auth()->user()->karyawan;
                                $pendingLemburCount = 0;
                                if ($koordinator) {
                                    $pendingLemburCount = \App\Models\Lembur::where('status', 'submitted')
                                        ->whereHas('karyawan', function ($q) use ($koordinator) {
                                            $q->where('department_id', $koordinator->department_id)->whereHas(
                                                'department',
                                                function ($dept) {
                                                    $dept->where(function ($d) {
                                                        $d->where('name', 'LIKE', '%teknis%')->orWhere(
                                                            'code',
                                                            'LIKE',
                                                            '%teknis%',
                                                        );
                                                    });
                                                },
                                            );
                                        })
                                        ->count();
                                }
                            @endphp
                            @if ($pendingLemburCount > 0)
                                <span
                                    class="text-xs bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded-full">{{ $pendingLemburCount }}</span>
                            @endif
                        </a>

                    </div>
                </div>
            @endif
            @if ($isAdmin || $isCoordinator)
                <a href="{{ route('admin.oncall.index') }}"
                    class="nav-item {{ request()->routeIs('oncall.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                    @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                    <div
                        class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-purple-400 to-pink-500 text-white shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="flex-1 truncate text-gray-700 group-hover:text-purple-700">OnCall</span>
                    @php
                        // Count pending oncall (waiting_checkin)
                        $pendingOnCallCount = 0;
                        if ($isCoordinator) {
                            $pendingOnCallCount = \App\Models\Lembur::where('jenis_lembur', 'oncall')
                                ->where('created_by_user_id', auth()->id())
                                ->where('status', 'waiting_checkin')
                                ->count();
                        }
                    @endphp
                    @if ($pendingOnCallCount > 0)
                        <span
                            class="text-xs bg-yellow-100 text-yellow-700 font-bold px-1.5 py-0.5 rounded-full">{{ $pendingOnCallCount }}</span>
                    @endif
                </a>
            @endif
            <!-- Transaksi Section - Admin + Koordinator -->
            @if ($isAdmin)
                <div class="mb-1.5">
                    <div class="flex items-center px-2.5 py-1.5 mb-1">
                        <div
                            class="w-1.5 h-1.5 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mr-2 animate-pulse">
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Transaksi</h3>
                    </div>
                    <div class="space-y-1">
                        @if ($isAdmin)
                            <a href="{{ route('admin.tunjangan-karyawan.index') }}"
                                class="nav-item {{ request()->routeIs('admin.tunjangan-karyawan.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                                @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                                <div
                                    class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-indigo-400 to-purple-500 text-white shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="flex-1 truncate text-gray-700 group-hover:text-indigo-700">Karyawan</span>
                            </a>
                        @endif
                        @if ($isAdmin)
                            <!-- Menu Lembur - Admin & Koordinator bisa lihat -->
                            <a href="{{ route('admin.lembur.index') }}"
                                class="nav-item {{ request()->routeIs('admin.lembur.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                                @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                                <div
                                    class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-orange-400 to-red-500 text-white shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="flex-1 truncate text-gray-700 group-hover:text-orange-700">Lembur</span>
                                @php
                                    $pendingCount = \App\Models\Lembur::where('status', 'submitted')->count();
                                @endphp
                                @if ($pendingCount > 0)
                                    <span
                                        class="text-xs bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        @endif
                        @if ($isAdmin)
                            <!-- Menu Tunjangan Generate & Report (Admin only) -->
                            <a href="{{ route('admin.tunjangan-karyawan.generate.form') }}"
                                class="nav-item {{ request()->routeIs('admin.tunjangan-karyawan.generate.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                                @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                                <div
                                    class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-cyan-400 to-blue-500 text-white shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <span class="flex-1 truncate text-gray-700 group-hover:text-cyan-700">Generate</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Monitoring - ADMIN ONLY -->
            @if ($isAdmin)
                <div class="mb-1.5">
                    <div class="flex items-center px-2.5 py-1.5 mb-1">
                        <div
                            class="w-1.5 h-1.5 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mr-2 animate-pulse">
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Monitor</h3>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.absen.index') }}"
                            class="nav-item {{ request()->routeIs('admin.absen.*') && !request()->routeIs('admin.absen.report') && !request()->routeIs('admin.absen.daily-report') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-green-400 to-emerald-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-green-700">Absensi</span>
                        </a>

                        <a href="{{ route('admin.absen.report') }}"
                            class="nav-item {{ request()->routeIs('admin.absen.report') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-cyan-400 to-blue-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-cyan-700">Laporan</span>
                        </a>

                        <a href="{{ route('admin.absen.daily-report') }}"
                            class="nav-item {{ request()->routeIs('admin.absen.daily-report') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-violet-700">Harian</span>
                        </a>
                    </div>
                </div>

                <!-- Tunjangan - ADMIN ONLY -->
                <div class="mb-4">
                    <div class="flex items-center px-2.5 py-1.5 mb-1">
                        <div class="w-1.5 h-1.5 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full mr-2">
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tunjangan</h3>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.tunjangan-type.index') }}"
                            class="nav-item {{ request()->routeIs('admin.tunjangan-type.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-yellow-400 to-orange-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-yellow-700">Jenis</span>
                            <span
                                class="text-xs bg-yellow-100 text-yellow-700 font-bold px-1.5 py-0.5 rounded-full">{{ \App\Models\TunjanganType::count() }}</span>
                        </a>

                        <a href="{{ route('admin.tunjangan-detail.index') }}"
                            class="nav-item {{ request()->routeIs('admin.tunjangan-detail.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-emerald-400 to-green-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-emerald-700">Nominal</span>
                        </a>

                        <a href="{{ route('admin.penalti.index') }}"
                            class="nav-item {{ request()->routeIs('admin.penalti.*') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-red-400 to-pink-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-red-700">Penalti</span>
                            @php $activePenalties = \App\Models\Penalti::where('status', 'active')->count(); @endphp
                            @if ($activePenalties > 0)
                                <span
                                    class="text-xs bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded-full">{{ $activePenalties }}</span>
                            @endif
                        </a>



                        <a href="{{ route('admin.tunjangan-karyawan.report') }}"
                            class="nav-item {{ request()->routeIs('admin.tunjangan-karyawan.report') ? 'active' : '' }} flex items-center px-2.5 py-2 text-xs font-medium rounded-lg transition-all duration-300 group hover:scale-[1.02]"
                            @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                            <div
                                class="flex items-center justify-center w-6 h-6 mr-2 rounded-md nav-icon bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="flex-1 truncate text-gray-700 group-hover:text-violet-700">Laporan</span>
                        </a>
                    </div>
                </div>
            @endif

        </nav>

        <!-- Compact User Info & Logout -->
        <div
            class="p-2.5 bg-gradient-to-r from-gray-50/50 to-slate-100/50 backdrop-blur-sm border-t border-gray-200/50">
            <!-- User Profile Card - Compact -->
            <div class="mb-2 p-2 bg-white/70 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-md">
                <div class="flex items-center space-x-2">
                    <div
                        class="w-7 h-7 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate -mt-0.5 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-sm"></div>
                </div>
            </div>

            <!-- Compact Logout Button -->
            <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center px-3 py-2 text-xs font-bold text-white bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                    <svg class="mr-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="truncate">Sign Out</span>
                </button>
            </form>
        </div>

    </div>
</aside>

<!-- Compact Styles -->
<style>
    .nav-item {
        position: relative;
        background: transparent;
        border: 1px solid transparent;
    }

    .nav-item:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 197, 253, 0.05));
        border-color: rgba(59, 130, 246, 0.1);
        transform: translateX(2px);
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
        border-color: rgba(59, 130, 246, 0.2);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
    }

    .nav-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        border-radius: 0 4px 4px 0;
    }

    .nav-icon {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-item:hover .nav-icon {
        transform: scale(1.08) rotate(3deg);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .nav-item.active .nav-icon {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, rgba(156, 163, 175, 0.3), rgba(156, 163, 175, 0.6));
        border-radius: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, rgba(156, 163, 175, 0.5), rgba(156, 163, 175, 0.8));
    }

    @keyframes slideInFromLeft {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .nav-item {
        animation: slideInFromLeft 0.2s ease-out forwards;
    }
</style>
