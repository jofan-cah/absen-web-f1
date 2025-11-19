@extends('admin.layouts.app')

@section('title', 'Data Jadwal')
@section('breadcrumb', 'Data Jadwal')
@section('page_title', 'Manajemen Jadwal Kerja')

@section('page_actions')
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <!-- Month/Year Selector -->
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex gap-2">
                <select id="month-selector"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $value => $name)
                        <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>{{ $name }}
                        </option>
                    @endforeach
                </select>
                <select id="year-selector"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @for ($y = now()->year - 2; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button onclick="filterByPeriod()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Filter
                </button>
            </div>

            <!-- Quick Navigation -->
            <div class="flex gap-2">
                <button onclick="navigateMonth(-1)"
                    class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button onclick="goToToday()"
                    class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    Hari Ini
                </button>
                <button onclick="navigateMonth(1)"
                    class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
            <a href="{{ route('admin.jadwal.calendar') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Calendar View
            </a>
            <button onclick="exportJadwal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
        </div>
    </div>
@endsection

@section('content')

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Jadwal</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $jadwals->count() }}</p>
                    <p class="text-sm text-blue-600 mt-1">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Bulan {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jadwal Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $jadwals->where('is_active', true)->count() }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Siap digunakan
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Karyawan Terjadwal</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $jadwals->unique('karyawan_id')->count() }}</p>
                    <p class="text-sm text-purple-600 mt-1">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Karyawan berbeda
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Shift Digunakan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $jadwals->unique('shift_id')->count() }}</p>
                    <p class="text-sm text-orange-600 mt-1">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Shift berbeda
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <input type="text" id="search-input" placeholder="Cari karyawan atau shift..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Department Filter -->
                <select id="department-filter"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Department</option>
                    @foreach ($jadwals->unique('karyawan.department_id')->pluck('karyawan.department') as $department)
                        @if ($department)
                            <option value="{{ $department->department_id }}">{{ $department->name }}</option>
                        @endif
                    @endforeach
                </select>

                <!-- Shift Filter -->
                <select id="shift-filter"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Shift</option>
                    @foreach ($jadwals->unique('shift_id')->pluck('shift') as $shift)
                        @if ($shift)
                            <option value="{{ $shift->shift_id }}">{{ $shift->name }}</option>
                        @endif
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select id="status-filter"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>

            <!-- Clear Filters -->
            <button onclick="clearFilters()"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                Reset Filter
            </button>


            <!-- Button Export PDF -->
            <button onclick="exportToPDF()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    Jadwal {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
                </h3>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Total: <span id="total-records">{{ $jadwals->count() }}</span>
                        jadwal</span>

                    <!-- View Toggle -->
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button onclick="setView('table')" id="table-view-btn"
                            class="px-3 py-1 text-sm bg-primary-600 text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </button>
                        <button onclick="setView('timeline')" id="timeline-view-btn"
                            class="px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h2m0-12h4m-4 0a2 2 0 002-2V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2zm4 0h6a2 2 0 012 2v10a2 2 0 01-2 2h-6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div id="table-view" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            onclick="sortBy('date')">
                            Tanggal
                            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam
                            Kerja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="jadwal-table-body">
                    @forelse($jadwals as $jadwal)
                        <tr class="hover:bg-gray-50 transition-colors jadwal-row"
                            data-department="{{ $jadwal->karyawan->department_id ?? '' }}"
                            data-shift="{{ $jadwal->shift_id }}" data-status="{{ $jadwal->is_active ? '1' : '0' }}"
                            data-search="{{ strtolower($jadwal->karyawan->name . ' ' . $jadwal->shift->name) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_jadwals[]" value="{{ $jadwal->jadwal_id }}"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $jadwal->date->format('d M Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $jadwal->date->format('l') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">
                                                {{ substr($jadwal->karyawan->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $jadwal->karyawan->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $jadwal->karyawan->employee_id }} •
                                            {{ $jadwal->karyawan->department->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-8 w-8 rounded-lg bg-gradient-to-br {{ $jadwal->shift->is_overnight ? 'from-purple-500 to-indigo-600' : 'from-green-500 to-emerald-600' }} flex items-center justify-center mr-3">
                                        @if ($jadwal->shift->is_overnight)
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $jadwal->shift->name }}</div>
                                        <div class="text-sm text-gray-500 font-mono">{{ $jadwal->shift->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">
                                    {{ \Carbon\Carbon::parse($jadwal->shift->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($jadwal->shift->end_time)->format('H:i') }}
                                </div>
                                @if ($jadwal->shift->is_overnight)
                                    <div class="text-xs text-purple-600">Lintas hari</div>
                                @else
                                    @php
                                        $start = \Carbon\Carbon::parse($jadwal->shift->start_time);
                                        $end = \Carbon\Carbon::parse($jadwal->shift->end_time);
                                        $duration = $start->diffInHours($end);
                                    @endphp
                                    <div class="text-xs text-gray-500">{{ $duration }} jam</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($jadwal->is_active)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $jadwal->notes }}">
                                    {{ $jadwal->notes ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="editJadwal('{{ $jadwal->jadwal_id }}')"
                                        class="text-yellow-600 hover:text-yellow-700 p-1 rounded hover:bg-yellow-50 transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        onclick="deleteJadwal('{{ $jadwal->jadwal_id }}', '{{ $jadwal->karyawan->name }} - {{ $jadwal->date->format('d M Y') }}')"
                                        class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada jadwal</h3>
                                    <p class="text-gray-500 mb-4">Belum ada jadwal untuk periode
                                        {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
                                    </p>
                                    <a href="{{ route('admin.jadwal.calendar') }}"
                                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                        Kelola Jadwal
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Timeline View (Hidden by default) -->
        <div id="timeline-view" class="hidden p-6">
            <div class="space-y-6" id="timeline-container">
                @php
                    $groupedJadwals = $jadwals
                        ->groupBy(function ($jadwal) {
                            return $jadwal->date->format('Y-m-d');
                        })
                        ->sortKeys();
                @endphp

                @forelse($groupedJadwals as $date => $dayJadwals)
                    <div class="timeline-day" data-date="{{ $date }}">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 w-20 text-right mr-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($date)->format('d') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($date)->format('M') }}
                                </div>
                            </div>
                            <div class="flex-shrink-0 w-3 h-3 bg-blue-600 rounded-full relative">
                                <div class="absolute top-3 left-1/2 transform -translate-x-0.5 w-0.5 h-full bg-gray-200">
                                </div>
                            </div>
                            <div class="flex-1 ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                                </h4>
                                <p class="text-sm text-gray-500">{{ $dayJadwals->count() }} jadwal</p>
                            </div>
                        </div>

                        <div class="ml-24 space-y-3">
                            @foreach ($dayJadwals->sortBy('shift.start_time') as $jadwal)
                                <div
                                    class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex-shrink-0 w-16 text-center">
                                        <div class="text-sm font-mono text-gray-900">
                                            {{ \Carbon\Carbon::parse($jadwal->shift->start_time)->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">-</div>
                                        <div class="text-sm font-mono text-gray-900">
                                            {{ \Carbon\Carbon::parse($jadwal->shift->end_time)->format('H:i') }}
                                        </div>
                                    </div>

                                    <div class="flex-1 ml-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h5 class="text-sm font-medium text-gray-900">
                                                    {{ $jadwal->karyawan->name }}
                                                </h5>
                                                <p class="text-sm text-gray-500">
                                                    {{ $jadwal->shift->name }} •
                                                    {{ $jadwal->karyawan->department->name ?? '-' }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if ($jadwal->is_active)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Tidak Aktif
                                                    </span>
                                                @endif

                                                <div class="flex gap-1">
                                                    <button onclick="editJadwal('{{ $jadwal->jadwal_id }}')"
                                                        class="text-yellow-600 hover:text-yellow-700 p-1 rounded hover:bg-yellow-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        onclick="deleteJadwal('{{ $jadwal->jadwal_id }}', '{{ $jadwal->karyawan->name }} - {{ $jadwal->date->format('d M Y') }}')"
                                                        class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($jadwal->notes)
                                            <p class="text-xs text-gray-600 mt-1">{{ $jadwal->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada jadwal</h3>
                        <p class="text-gray-500">Belum ada jadwal untuk periode ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Bulk Actions (when items selected) -->
    <div id="bulk-actions"
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-3 hidden">
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600"><span id="selected-count">0</span> item dipilih</span>
            <div class="flex gap-2">
                <button onclick="bulkDelete()"
                    class="px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Hapus Terpilih
                </button>
                <button onclick="clearSelection()"
                    class="px-3 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Jadwal</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="edit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-jadwal-id">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                        <input type="text" id="edit-karyawan-name" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="text" id="edit-date" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div class="mb-4">
                        <label for="edit-shift-id" class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                        <select id="edit-shift-id" name="shift_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="edit-notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea id="edit-notes" name="notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let currentMonth = {{ $month }};
        let currentYear = {{ $year }};
        let allJadwals = @json($jadwals);
        let allShifts = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Load shifts for edit modal
            loadShifts();

            // Initialize event listeners
            initializeEventListeners();

            // Initialize filters
            initializeFilters();
        });

        function exportToPDF() {
            // Ambil filter yang aktif
            const month = '{{ $month }}';
            const year = '{{ $year }}';
            const departmentId = document.getElementById('department-filter').value;
            const shiftId = document.getElementById('shift-filter').value;
            const status = document.getElementById('status-filter').value;

            // Build URL dengan parameter
            let url = `{{ route('admin.jadwal.export-pdf') }}?month=${month}&year=${year}`;

            // Coordinator: Department filter otomatis ter-apply di backend
            // Admin: Bisa pilih department atau all
            if (departmentId) {
                url += `&department_id=${departmentId}`;
            }

            if (shiftId) {
                url += `&shift_id=${shiftId}`;
            }

            if (status) {
                url += `&status=${status}`;
            }

            // Buka PDF di tab baru
            window.open(url, '_blank');
        }

        function loadShifts() {
            fetch('{{ route('admin.shift.active') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allShifts = data.data;
                        populateShiftOptions();
                    }
                })
                .catch(error => console.error('Error loading shifts:', error));
        }

        function populateShiftOptions() {
            const shiftSelect = document.getElementById('edit-shift-id');
            shiftSelect.innerHTML = '';

            allShifts.forEach(shift => {
                const option = document.createElement('option');
                option.value = shift.shift_id;
                option.textContent = `${shift.name} (${shift.start_time} - ${shift.end_time})`;
                shiftSelect.appendChild(option);
            });
        }

        function initializeEventListeners() {
            // Checkbox functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const individualCheckboxes = document.querySelectorAll('input[name="selected_jadwals[]"]');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    individualCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions();
                });
            }

            individualCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            // Edit form submission
            document.getElementById('edit-form').addEventListener('submit', function(e) {
                e.preventDefault();
                submitEditForm();
            });
        }

        function initializeFilters() {
            const searchInput = document.getElementById('search-input');
            const departmentFilter = document.getElementById('department-filter');
            const shiftFilter = document.getElementById('shift-filter');
            const statusFilter = document.getElementById('status-filter');

            [searchInput, departmentFilter, shiftFilter, statusFilter].forEach(element => {
                if (element) {
                    element.addEventListener('input', applyFilters);
                }
            });
        }

        function applyFilters() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const departmentFilter = document.getElementById('department-filter').value;
            const shiftFilter = document.getElementById('shift-filter').value;
            const statusFilter = document.getElementById('status-filter').value;

            const rows = document.querySelectorAll('.jadwal-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.dataset.search || '';
                const department = row.dataset.department || '';
                const shift = row.dataset.shift || '';
                const status = row.dataset.status || '';

                const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                const matchesDepartment = !departmentFilter || department === departmentFilter;
                const matchesShift = !shiftFilter || shift === shiftFilter;
                const matchesStatus = !statusFilter || status === statusFilter;

                if (matchesSearch && matchesDepartment && matchesShift && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('total-records').textContent = visibleCount;
        }

        function clearFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('department-filter').value = '';
            document.getElementById('shift-filter').value = '';
            document.getElementById('status-filter').value = '';
            applyFilters();
        }

        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('input[name="selected_jadwals[]"]:checked');
            const bulkActionsDiv = document.getElementById('bulk-actions');
            const selectedCountSpan = document.getElementById('selected-count');

            if (checkedBoxes.length > 0) {
                bulkActionsDiv.classList.remove('hidden');
                selectedCountSpan.textContent = checkedBoxes.length;
            } else {
                bulkActionsDiv.classList.add('hidden');
            }
        }

        function clearSelection() {
            document.querySelectorAll('input[name="selected_jadwals[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('select-all').checked = false;
            document.getElementById('bulk-actions').classList.add('hidden');
        }

        // Navigation functions
        function filterByPeriod() {
            const month = document.getElementById('month-selector').value;
            const year = document.getElementById('year-selector').value;
            window.location.href = `{{ route('admin.jadwal.index') }}?month=${month}&year=${year}`;
        }

        function navigateMonth(direction) {
            let newMonth = currentMonth + direction;
            let newYear = currentYear;

            if (newMonth > 12) {
                newMonth = 1;
                newYear++;
            } else if (newMonth < 1) {
                newMonth = 12;
                newYear--;
            }

            window.location.href =
                `{{ route('admin.jadwal.index') }}?month=${newMonth.toString().padStart(2, '0')}&year=${newYear}`;
        }

        function goToToday() {
            const today = new Date();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const year = today.getFullYear();
            window.location.href = `{{ route('admin.jadwal.index') }}?month=${month}&year=${year}`;
        }

        // View toggle
        function setView(view) {
            const tableView = document.getElementById('table-view');
            const timelineView = document.getElementById('timeline-view');
            const tableBtn = document.getElementById('table-view-btn');
            const timelineBtn = document.getElementById('timeline-view-btn');

            if (view === 'table') {
                tableView.classList.remove('hidden');
                timelineView.classList.add('hidden');
                tableBtn.classList.remove('bg-gray-100', 'text-gray-600');
                tableBtn.classList.add('bg-primary-600', 'text-white');
                timelineBtn.classList.remove('bg-primary-600', 'text-white');
                timelineBtn.classList.add('bg-gray-100', 'text-gray-600');
            } else {
                tableView.classList.add('hidden');
                timelineView.classList.remove('hidden');
                timelineBtn.classList.remove('bg-gray-100', 'text-gray-600');
                timelineBtn.classList.add('bg-primary-600', 'text-white');
                tableBtn.classList.remove('bg-primary-600', 'text-white');
                tableBtn.classList.add('bg-gray-100', 'text-gray-600');
            }

            localStorage.setItem('jadwalViewPreference', view);
        }

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('jadwalViewPreference');
            if (savedView && savedView !== 'table') {
                setView(savedView);
            }
        });

        // CRUD Operations
        function editJadwal(jadwalId) {
            // Find jadwal data
            const jadwal = allJadwals.find(j => j.jadwal_id === jadwalId);
            if (!jadwal) {
                alert('Data jadwal tidak ditemukan');
                return;
            }

            // Check if editable
            fetch(`{{ route('admin.jadwal.index') }}/${jadwalId}/check-editable`)
                .then(response => response.json())
                .then(data => {
                    if (!data.editable) {
                        alert(data.reason || 'Jadwal tidak dapat diedit');
                        return;
                    }

                    // Populate modal
                    document.getElementById('edit-jadwal-id').value = jadwal.jadwal_id;
                    document.getElementById('edit-karyawan-name').value = jadwal.karyawan.name;
                    document.getElementById('edit-date').value = new Date(jadwal.date).toLocaleDateString('id-ID');
                    document.getElementById('edit-shift-id').value = jadwal.shift_id;
                    document.getElementById('edit-notes').value = jadwal.notes || '';

                    // Show modal
                    document.getElementById('edit-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memeriksa jadwal');
                });
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        function submitEditForm() {
            const jadwalId = document.getElementById('edit-jadwal-id').value;
            const formData = new FormData(document.getElementById('edit-form'));

            fetch(`{{ route('admin.jadwal.index') }}/${jadwalId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan');
                });
        }

        function deleteJadwal(jadwalId, description) {
            if (confirm(`Apakah Anda yakin ingin menghapus jadwal "${description}"?`)) {
                fetch(`{{ route('admin.jadwal.index') }}/${jadwalId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus');
                    });
            }
        }

        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('input[name="selected_jadwals[]"]:checked');
            if (checkedBoxes.length === 0) {
                alert('Pilih jadwal yang ingin dihapus');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} jadwal terpilih?`)) {
                const jadwalIds = Array.from(checkedBoxes).map(cb => cb.value);

                // Note: You'll need to implement bulk delete endpoint in controller
                Promise.all(jadwalIds.map(id =>
                    fetch(`{{ route('admin.jadwal.index') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                        }
                    })
                )).then(() => {
                    location.reload();
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus beberapa jadwal');
                });
            }
        }

        function exportJadwal() {
            const month = document.getElementById('month-selector').value;
            const year = document.getElementById('year-selector').value;

            // Create download link
            const link = document.createElement('a');
            link.href = `{{ route('admin.jadwal.index') }}/export?month=${month}&year=${year}`;
            link.download = `jadwal-${year}-${month}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function sortBy(field) {
            // Implement sorting logic if needed
            console.log('Sort by:', field);
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Smooth transitions */
        .transition-colors {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        /* Table hover effects */
        tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Timeline styles */
        .timeline-day:not(:last-child) .bg-blue-600::after {
            content: '';
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background-color: #e5e7eb;
        }

        /* Modal backdrop */
        #edit-modal {
            backdrop-filter: blur(4px);
        }

        /* Custom scrollbar for timeline */
        #timeline-container {
            max-height: 600px;
            overflow-y: auto;
        }

        #timeline-container::-webkit-scrollbar {
            width: 6px;
        }

        #timeline-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        #timeline-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        #timeline-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Bulk actions animation */
        #bulk-actions {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(100%);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Loading state */
        .loading {
            pointer-events: none;
            opacity: 0.6;
        }

        /* Print styles */
        @media print {

            #bulk-actions,
            .no-print {
                display: none !important;
            }

            .bg-gradient-to-br {
                background: #6b7280 !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endpush
