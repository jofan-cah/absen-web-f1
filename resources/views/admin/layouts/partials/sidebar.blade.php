<!-- MODERN SIDEBAR COMPONENT FOR ABSENSI SYSTEM -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white/95 backdrop-blur-xl shadow-2xl border-r border-gray-200/50
           lg:translate-x-0 sidebar-transition">
    <div class="flex flex-col h-full">

        <!-- Modern Header/Brand -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            <div class="relative flex items-center justify-between h-16 px-5 text-white">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold truncate tracking-wide">ABSENSI F1</h1>
                        <p class="text-xs text-white/80 truncate -mt-0.5 font-medium">Sistem Absensi ISP</p>
                    </div>
                </div>
                <!-- Mobile close button with enhanced styling -->
                <button @click="sidebarOpen = false"
                    class="lg:hidden p-2 hover:bg-white/10 rounded-xl transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Enhanced Navigation -->
        <nav class="flex-1 px-4 py-4 overflow-y-auto sidebar-scroll">

            <!-- Dashboard with modern styling -->
            <div class="mb-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} relative flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-300 group hover:scale-105 hover:shadow-lg"
                    @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                    <div
                        class="flex items-center justify-center w-10 h-10 mr-3 rounded-xl nav-icon bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                        </svg>
                    </div>
                    <span class="truncate text-gray-700 group-hover:text-blue-700">Dashboard</span>
                    <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Karyawan Management with enhanced design -->
            <div class="mb-2">
                <a href="{{ route('admin.karyawan.index') }}"
                    class="nav-item {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }} relative flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-300 group hover:scale-105 hover:shadow-lg"
                    @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                    <div
                        class="flex items-center justify-center w-10 h-10 mr-3 rounded-xl nav-icon bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <span class="truncate text-gray-700 group-hover:text-green-700">Data Karyawan</span>
                    <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Master Data Section -->
            <div class="mb-2">
                <div class="flex items-center px-4 py-2 mb-3">
                    <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Master Data</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.department.index') }}"
                        class="nav-item {{ request()->routeIs('admin.department.*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-violet-700">Department</span>
                        <span
                            class="nav-badge bg-violet-100 text-violet-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">7</span>
                    </a>

                    <a href="{{ route('admin.shift.index') }}"
                        class="nav-item {{ request()->routeIs('admin.shift.*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-orange-400 to-red-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-orange-700">Shift Kerja</span>
                        <span
                            class="nav-badge bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">4</span>
                    </a>
                </div>
            </div>

            <!-- Jadwal Management Section -->
            <div class="mb-2">
                <div class="flex items-center px-4 py-2 mb-3">
                    <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mr-3 animate-pulse">
                    </div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jadwal Kerja</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.jadwal.index') }}"
                        class="nav-item {{ request()->routeIs('admin.jadwal.index') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-blue-400 to-cyan-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-blue-700">Data Jadwal</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                            <span
                                class="nav-badge bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">Auto</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.jadwal.calendar') }}"
                        class="nav-item {{ request()->routeIs('admin.jadwal.calendar') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-indigo-400 to-purple-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-indigo-700">Calendar View</span>
                        <span
                            class="nav-badge bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">New</span>
                    </a>
                </div>
            </div>

            <!-- Monitoring Section -->
            <div class="mb-6">
                <div class="flex items-center px-4 py-2 mb-3">
                    <div
                        class="w-2 h-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full mr-3 animate-pulse">
                    </div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Monitoring</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.absen.index') }}"
                        class="nav-item {{ request()->routeIs('admin.absen.*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-green-400 to-emerald-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-green-700">Data Absensi</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                            <span
                                class="nav-badge bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">Live</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.absen.report') }}"
                        class="nav-item {{ request()->routeIs('admin.absen.report') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-cyan-400 to-blue-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-cyan-700">Laporan Absensi</span>
                        <span
                            class="nav-badge bg-cyan-100 text-cyan-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">Report</span>
                    </a>

                    <a href="{{ route('admin.absen.daily-report') }}"
                        class="nav-item {{ request()->routeIs('admin.absen.daily-report') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-violet-700">Laporan Harian</span>
                        <span
                            class="nav-badge bg-violet-100 text-violet-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">Daily</span>
                    </a>
                </div>
            </div>

            <!-- COMMENTED OUT SECTIONS - Routes not available yet -->
            {{--
            <!-- Permohonan Section -->
            <div class="mb-6">
                <div class="flex items-center px-4 py-2 mb-3">
                    <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-pink-500 rounded-full mr-3 animate-pulse">
                    </div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Permohonan</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.pengajuan-izin.index') }}"
                        class="nav-item {{ request()->routeIs('admin.pengajuan-izin.index') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div
                            class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-red-400 to-pink-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-red-700">Pengajuan Izin</span>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></div>
                            <span
                                class="nav-badge bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">5</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Tunjangan Section -->
            <div class="mb-6">
                <div class="flex items-center px-4 py-2 mb-3">
                    <div class="w-2 h-2 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tunjangan</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.tunjangan.assignment') }}"
                        class="nav-item {{ request()->routeIs('admin.tunjangan.assignment*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-indigo-400 to-purple-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-indigo-700">Assignment</span>
                    </a>

                    <a href="{{ route('admin.tunjangan.hitung') }}"
                        class="nav-item {{ request()->routeIs('admin.tunjangan.hitung*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-green-400 to-blue-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-green-700">Perhitungan</span>
                    </a>

                    <a href="{{ route('admin.tunjangan.histori') }}"
                        class="nav-item {{ request()->routeIs('admin.tunjangan.histori*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-purple-400 to-pink-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-purple-700">Histori</span>
                    </a>
                </div>
            </div>

            <!-- More Laporan Section -->
            <div class="mb-6">
                <div class="flex items-center px-4 py-2 mb-3">
                    <div class="w-2 h-2 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Laporan Lainnya</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.laporan.rekap-bulanan') }}"
                        class="nav-item {{ request()->routeIs('admin.laporan.rekap-bulanan') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-violet-400 to-purple-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-violet-700">Rekap Bulanan</span>
                    </a>

                    <a href="{{ route('admin.laporan.karyawan') }}"
                        class="nav-item {{ request()->routeIs('admin.laporan.karyawan') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-emerald-700">Laporan Karyawan</span>
                    </a>

                    <a href="{{ route('admin.laporan.tunjangan') }}"
                        class="nav-item {{ request()->routeIs('admin.laporan.tunjangan*') ? 'active' : '' }} flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 group hover:scale-105"
                        @click="window.innerWidth < 1024 && (sidebarOpen = false)">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 rounded-lg nav-icon bg-gradient-to-br from-yellow-400 to-orange-500 text-white shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <span class="flex-1 truncate text-gray-700 group-hover:text-yellow-700">Laporan Tunjangan</span>
                    </a>
                </div>
            </div>
            --}}

        </nav>

        <!-- Enhanced User Info & Logout -->
        <div class="p-4 bg-gradient-to-r from-gray-50/50 to-slate-100/50 backdrop-blur-sm border-t border-gray-200/50">
            <!-- User Profile Card -->
            <div class="mb-4 p-3 bg-white/70 backdrop-blur-sm rounded-2xl border border-gray-200/50 shadow-lg">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">Admin System</p>
                        <p class="text-xs text-gray-500 truncate font-medium">Administrator</p>
                    </div>
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-sm"></div>
                </div>
            </div>

            <!-- Enhanced Logout Button -->
            <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-white">
                    <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="truncate">Sign Out</span>
                </button>
            </form>
        </div>

    </div>
</aside>

<!-- Enhanced Styles -->
<style>
    /* Enhanced Navigation Items */
    .nav-item {
        position: relative;
        background: transparent;
        border: 1px solid transparent;
    }

    .nav-item:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 197, 253, 0.05));
        border-color: rgba(59, 130, 246, 0.1);
        transform: translateX(4px);
    }

    .nav-item.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
        border-color: rgba(59, 130, 246, 0.2);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
    }

    .nav-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        border-radius: 0 8px 8px 0;
    }

    /* Enhanced Icons */
    .nav-icon {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-item:hover .nav-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .nav-item.active .nav-icon {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    /* Enhanced Badges */
    .nav-badge {
        font-size: 0.6875rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0.025em;
        transition: all 0.2s ease;
    }

    .nav-item:hover .nav-badge {
        transform: scale(1.1);
    }

    /* Glass Effect Enhancement */
    .glass-effect {
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    /* Scrollbar Enhancement */
    .sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, rgba(156, 163, 175, 0.3), rgba(156, 163, 175, 0.6));
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, rgba(156, 163, 175, 0.5), rgba(156, 163, 175, 0.8));
    }

    /* Section Headers */
    .nav-section-header {
        position: relative;
        overflow: hidden;
    }

    .nav-section-header::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 1px;
        background: linear-gradient(to right, transparent, rgba(156, 163, 175, 0.3), transparent);
    }

    /* Smooth animations for page load */
    @keyframes slideInFromLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .nav-item {
        animation: slideInFromLeft 0.3s ease-out forwards;
    }

    .nav-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .nav-item:nth-child(2) {
        animation-delay: 0.15s;
    }

    .nav-item:nth-child(3) {
        animation-delay: 0.2s;
    }

    .nav-item:nth-child(4) {
        animation-delay: 0.25s;
    }

    .nav-item:nth-child(5) {
        animation-delay: 0.3s;
    }

    /* Focus states for accessibility */
    .nav-item:focus {
        outline: none;
        ring: 2px;
        ring-color: rgba(59, 130, 246, 0.5);
        ring-offset: 2px;
        ring-offset-color: white;
    }

    /* Enhanced mobile responsiveness */
    @media (max-width: 1023px) {
        .nav-item:hover {
            transform: none;
        }

        .nav-item:active {
            transform: scale(0.98);
            transition-duration: 0.1s;
        }
    }

    /* Pulse animation for live indicators */
    @keyframes pulse-glow {

        0%,
        100% {
            opacity: 1;
            box-shadow: 0 0 5px rgba(34, 197, 94, 0.5);
        }

        50% {
            opacity: 0.7;
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.8);
        }
    }

    .animate-pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }

    /* Custom gradient borders */
    .gradient-border {
        border: 2px solid;
        border-image: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899) 1;
        border-radius: 16px;
    }

    /* Notification badges enhancement */
    .notification-badge {
        position: relative;
        overflow: hidden;
    }

    .notification-badge::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transform: rotate(45deg);
        animation: shine 3s ease-in-out infinite;
    }

    @keyframes shine {
        0% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }

        50% {
            transform: translateX(100%) translateY(100%) rotate(45deg);
        }

        100% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }
    }
</style>
