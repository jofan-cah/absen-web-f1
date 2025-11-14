@extends('admin.layouts.app')

@section('title', 'Data Absensi')
@section('breadcrumb', 'Data Absensi')
@section('page_title', 'Management Absensi')

@section('page_actions')
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <!-- Filter Section -->
        <div class="flex flex-wrap gap-3 items-center">
            <!-- Date Filter -->
            <div class="flex items-center gap-2">
                <label for="date-filter" class="text-sm font-medium text-gray-700">Tanggal:</label>
                <input type="date" id="date-filter" value="{{ request('date', today()->format('Y-m-d')) }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Department Filter -->
            <div class="flex items-center gap-2">
                <label for="department-filter" class="text-sm font-medium text-gray-700">Department:</label>
                <select id="department-filter"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->department_id }}"
                            {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <label for="status-filter" class="text-sm font-medium text-gray-700">Status:</label>
                <select id="status-filter"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="early_checkout" {{ request('status') == 'early_checkout' ? 'selected' : '' }}>Early
                        Checkout</option>
                </select>
            </div>

            <!-- Karyawan Filter -->
            <div class="flex items-center gap-2">
                <label for="karyawan-filter" class="text-sm font-medium text-gray-700">Karyawan:</label>
                <select id="karyawan-filter"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Karyawan</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->karyawan_id }}"
                            {{ request('karyawan_id') == $karyawan->karyawan_id ? 'selected' : '' }}>
                            {{ $karyawan->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <button onclick="applyFilters()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                Filter
            </button>

            <!-- Reset Button -->
            <button onclick="resetFilters()"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                Reset
            </button>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <a href="{{ route('admin.absen.daily-report') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Daily Report
            </a>
            <button onclick="showPdfExportModal()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Export PDF
            </button>
            <a href="{{ route('admin.absen.report') }}"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Monthly Report
            </a>
        </div>
    </div>

@endsection



@section('content')

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        @php
            $totalAbsens = $absens->total();
            $presentCount = $absens->where('status', 'present')->count() + $absens->where('status', 'late')->count();
            $lateCount = $absens->where('status', 'late')->count();
            $absentCount = $absens->where('status', 'absent')->count();
            $scheduledCount = $absens->where('status', 'scheduled')->count();
        @endphp

        <!-- Total -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalAbsens }}</p>
                </div>
            </div>
        </div>

        <!-- Present -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hadir</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $presentCount }}</p>
                </div>
            </div>
        </div>

        <!-- Late -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Terlambat</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $lateCount }}</p>
                </div>
            </div>
        </div>

        <!-- Absent -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Tidak Hadir</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $absentCount }}</p>
                </div>
            </div>
        </div>

        <!-- Scheduled -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Scheduled</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $scheduledCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button onclick="setView('card')" id="card-view-btn"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Card View
                </button>
                <button onclick="setView('table')" id="table-view-btn"
                    class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Table View
                </button>
            </div>
            <p class="text-sm text-gray-600">
                Total: <span class="font-semibold text-gray-900">{{ $absens->total() }}</span> data
            </p>
        </div>
    </div>

    <!-- Card View -->
    <div id="card-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @forelse($absens as $absen)
            @php
                $statusConfig = [
                    'scheduled' => [
                        'bg' => 'bg-blue-100',
                        'text' => 'text-blue-700',
                        'icon' => 'fa-calendar',
                        'label' => 'Dijadwalkan',
                    ],
                    'present' => [
                        'bg' => 'bg-green-100',
                        'text' => 'text-green-700',
                        'icon' => 'fa-check-circle',
                        'label' => 'Hadir',
                    ],
                    'late' => [
                        'bg' => 'bg-orange-100',
                        'text' => 'text-orange-700',
                        'icon' => 'fa-clock',
                        'label' => 'Terlambat',
                    ],
                    'absent' => [
                        'bg' => 'bg-red-100',
                        'text' => 'text-red-700',
                        'icon' => 'fa-times-circle',
                        'label' => 'Tidak Hadir',
                    ],
                    'early_checkout' => [
                        'bg' => 'bg-yellow-100',
                        'text' => 'text-yellow-700',
                        'icon' => 'fa-door-open',
                        'label' => 'Pulang Cepat',
                    ],
                ];

                $config = $statusConfig[$absen->status] ?? [
                    'bg' => 'bg-gray-100',
                    'text' => 'text-gray-700',
                    'icon' => 'fa-question',
                    'label' => ucfirst($absen->status),
                ];

                // Generate random gradient colors for avatar
                $colors = ['blue', 'purple', 'pink', 'indigo', 'teal', 'cyan', 'emerald', 'amber'];
                $randomColor = $colors[abs(crc32($absen->karyawan->nip)) % count($colors)];
            @endphp

            <div
                class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-300
                    {{ $absen->status === 'absent' ? 'opacity-75' : '' }}">

                <!-- Header dengan Avatar & Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        @if ($absen->karyawan->photo)
                            <img src="{{ asset('storage/' . $absen->karyawan->photo) }}"
                                alt="{{ $absen->karyawan->full_name }}"
                                class="w-12 h-12 rounded-xl object-cover ring-2 ring-{{ $randomColor }}-200">
                        @else
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-{{ $randomColor }}-500 to-{{ $randomColor }}-600
                                    rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr($absen->karyawan->full_name, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $absen->karyawan->full_name }}</h3>
                            <p class="text-xs text-gray-500">{{ $absen->karyawan->department->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $absen->karyawan->nip }}</p>
                        </div>
                    </div>
                    <span
                        class="px-3 py-1 {{ $config['bg'] }} {{ $config['text'] }} text-xs font-semibold rounded-full inline-flex items-center gap-1">
                        <i class="fas {{ $config['icon'] }}"></i>
                        {{ $config['label'] }}
                    </span>
                </div>

                <!-- Info Detail -->
                <div class="space-y-2 mb-4">
                    <!-- Tanggal -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <i class="fas fa-calendar-day text-gray-400 w-4"></i>
                            Tanggal
                        </span>
                        <span class="font-medium text-gray-900">
                            {{ $absen->date->format('d M Y') }}
                            <span class="text-xs text-gray-500">({{ $absen->date->format('l') }})</span>
                        </span>
                    </div>

                    <!-- Shift -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <i class="fas fa-moon text-gray-400 w-4"></i>
                            Shift
                        </span>
                        <span class="font-medium text-gray-900">
                            {{ $absen->jadwal->shift->name }}
                            <span class="text-xs text-gray-500 font-mono">
                                ({{ \Carbon\Carbon::parse($absen->jadwal->shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($absen->jadwal->shift->end_time)->format('H:i') }})
                            </span>
                        </span>
                    </div>

                    <!-- Clock In -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <i
                                class="fas fa-sign-in-alt {{ $absen->clock_in ? ($absen->status === 'late' ? 'text-orange-500' : 'text-green-500') : 'text-gray-400' }} w-4"></i>
                            Clock In
                        </span>
                        @if ($absen->clock_in)
                            <span
                                class="font-medium font-mono {{ $absen->status === 'late' ? 'text-orange-700' : 'text-green-700' }}">
                                {{ \Carbon\Carbon::parse($absen->clock_in)->format('H:i:s') }}
                                @if ($absen->late_minutes > 0)
                                    <span class="text-xs text-orange-600">(+{{ $absen->late_minutes }} mnt)</span>
                                @endif
                            </span>
                        @else
                            <span class="font-medium text-gray-400">-</span>
                        @endif
                    </div>

                    <!-- Clock Out -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <i
                                class="fas fa-sign-out-alt {{ $absen->clock_out ? 'text-blue-500' : 'text-gray-400' }} w-4"></i>
                            Clock Out
                        </span>
                        @if ($absen->clock_out)
                            <span class="font-medium font-mono text-blue-700">
                                {{ \Carbon\Carbon::parse($absen->clock_out)->format('H:i:s') }}
                            </span>
                        @else
                            <span class="font-medium text-gray-400">-</span>
                        @endif
                    </div>
                </div>

                <!-- Footer dengan Jam Kerja & Action -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        @if ($absen->work_hours)
                            <i class="fas fa-clock text-gray-400"></i>
                            <span class="text-sm text-gray-600">
                                Jam Kerja:
                                <span class="font-semibold text-gray-900">
                                    {{ number_format($absen->work_hours, 1) }} jam
                                </span>
                            </span>
                        @elseif($absen->status === 'absent')
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                            <span class="text-sm text-red-600 font-medium">Tidak ada kehadiran</span>
                        @elseif($absen->status === 'scheduled')
                            <i class="fas fa-info-circle text-blue-400"></i>
                            <span class="text-sm text-blue-600 font-medium">Menunggu clock in</span>
                        @else
                            <i class="fas fa-hourglass-start text-gray-400"></i>
                            <span class="text-sm text-gray-600 font-medium">Belum selesai</span>
                        @endif
                    </div>
                    <a href="{{ route('admin.absen.show', $absen->absen_id) }}"
                        class="text-blue-600 hover:text-blue-700 text-sm font-medium inline-flex items-center gap-1 hover:gap-2 transition-all">
                        Detail
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data absensi</h3>
                        <p class="text-gray-500">Belum ada data absensi yang sesuai dengan filter yang dipilih.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Table View (Hidden by default) -->
    <div id="table-view" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Data Absensi</h3>
            <p class="text-sm text-gray-600 mt-1">Daftar absensi karyawan berdasarkan filter yang dipilih</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock
                            Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Terlambat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam
                            Kerja</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absens as $absen)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- Karyawan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @php
                                        $colors = [
                                            'blue',
                                            'purple',
                                            'pink',
                                            'indigo',
                                            'teal',
                                            'cyan',
                                            'emerald',
                                            'amber',
                                        ];
                                        $randomColor = $colors[abs(crc32($absen->karyawan->nip)) % count($colors)];
                                    @endphp
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if ($absen->karyawan->photo)
                                            <img src="{{ asset('storage/' . $absen->karyawan->photo) }}"
                                                alt="{{ $absen->karyawan->full_name }}"
                                                class="h-10 w-10 rounded-full object-cover ring-2 ring-{{ $randomColor }}-200">
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-{{ $randomColor }}-500 to-{{ $randomColor }}-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($absen->karyawan->full_name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $absen->karyawan->full_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $absen->karyawan->nip }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Department -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $absen->karyawan->department->name ?? '-' }}</div>
                            </td>

                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $absen->date->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $absen->date->format('l') }}</div>
                            </td>

                            <!-- Shift -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $absen->jadwal->shift->name }}</div>
                                <div class="text-sm text-gray-500 font-mono">
                                    {{ \Carbon\Carbon::parse($absen->jadwal->shift->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($absen->jadwal->shift->end_time)->format('H:i') }}
                                </div>
                            </td>

                            <!-- Clock In -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono">
                                    {{ $absen->clock_in ? \Carbon\Carbon::parse($absen->clock_in)->format('H:i:s') : '-' }}
                                </div>
                            </td>

                            <!-- Clock Out -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono">
                                    {{ $absen->clock_out ? \Carbon\Carbon::parse($absen->clock_out)->format('H:i:s') : '-' }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'scheduled' => 'bg-gray-100 text-gray-800',
                                        'present' => 'bg-green-100 text-green-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'early_checkout' => 'bg-orange-100 text-orange-800',
                                    ];

                                    $statusNames = [
                                        'scheduled' => 'Scheduled',
                                        'present' => 'Present',
                                        'late' => 'Late',
                                        'absent' => 'Absent',
                                        'early_checkout' => 'Early Out',
                                    ];
                                @endphp
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$absen->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusNames[$absen->status] ?? ucfirst($absen->status) }}
                                </span>
                            </td>

                            <!-- Terlambat -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if ($absen->late_minutes > 0)
                                        <span class="text-red-600 font-medium">{{ $absen->late_minutes }} menit</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Jam Kerja -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $absen->work_hours ? number_format($absen->work_hours, 1) . ' jam' : '-' }}
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.absen.show', $absen->absen_id) }}"
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data absensi</h3>
                                    <p class="text-gray-500">Belum ada data absensi yang sesuai dengan filter yang dipilih.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($absens->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $absens->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Pagination for Card View -->
    @if ($absens->hasPages())
        <div id="card-pagination" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            {{ $absens->withQueryString()->links() }}
        </div>
    @endif



    <div id="pdf-export-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Export PDF Report</h3>
                    <button onclick="closePdfExportModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select id="pdf-month"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select id="pdf-year"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department (Opsional)</label>
                    <select id="pdf-department"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Department</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->department_id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan (Opsional)</label>
                    <select id="pdf-karyawan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Karyawan</option>
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->karyawan_id }}">{{ $karyawan->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="closePdfExportModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <button onclick="downloadPdfReport()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showPdfExportModal() {
            document.getElementById('pdf-export-modal').classList.remove('hidden');
        }

        function closePdfExportModal() {
            document.getElementById('pdf-export-modal').classList.add('hidden');
        }

        function downloadPdfReport() {
            const month = document.getElementById('pdf-month').value;
            const year = document.getElementById('pdf-year').value;
            const department = document.getElementById('pdf-department').value;
            const karyawan = document.getElementById('pdf-karyawan').value;

            let url = '{{ route('admin.absen.export-pdf-report') }}?month=' + month + '&year=' + year;

            if (department) {
                url += '&department_id=' + department;
            }

            if (karyawan) {
                url += '&karyawan_id=' + karyawan;
            }

            window.location.href = url;

            // Close modal after 1 second
            setTimeout(() => {
                closePdfExportModal();
            }, 1000);
        }

        // Close modal when clicking outside
        document.getElementById('pdf-export-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePdfExportModal();
            }
        });

        function applyFilters() {
            const date = document.getElementById('date-filter').value;
            const department = document.getElementById('department-filter').value;
            const status = document.getElementById('status-filter').value;
            const karyawan = document.getElementById('karyawan-filter').value;

            const params = new URLSearchParams();

            if (date) params.append('date', date);
            if (department) params.append('department_id', department);
            if (status) params.append('status', status);
            if (karyawan) params.append('karyawan_id', karyawan);

            const url = new URL(window.location.href);
            url.search = params.toString();

            window.location.href = url.toString();
        }

        function resetFilters() {
            const url = new URL(window.location.href);
            url.search = '';
            window.location.href = url.toString();
        }

        // View toggle function
        function setView(view) {
            const cardView = document.getElementById('card-view');
            const tableView = document.getElementById('table-view');
            const cardPagination = document.getElementById('card-pagination');
            const cardBtn = document.getElementById('card-view-btn');
            const tableBtn = document.getElementById('table-view-btn');

            if (view === 'card') {
                cardView.classList.remove('hidden');
                tableView.classList.add('hidden');
                if (cardPagination) cardPagination.classList.remove('hidden');

                cardBtn.classList.add('bg-blue-600', 'text-white');
                cardBtn.classList.remove('text-gray-600', 'hover:bg-gray-50');

                tableBtn.classList.remove('bg-blue-600', 'text-white');
                tableBtn.classList.add('text-gray-600', 'hover:bg-gray-50');
            } else {
                cardView.classList.add('hidden');
                tableView.classList.remove('hidden');
                if (cardPagination) cardPagination.classList.add('hidden');

                tableBtn.classList.add('bg-blue-600', 'text-white');
                tableBtn.classList.remove('text-gray-600', 'hover:bg-gray-50');

                cardBtn.classList.remove('bg-blue-600', 'text-white');
                cardBtn.classList.add('text-gray-600', 'hover:bg-gray-50');
            }

            // Save preference
            localStorage.setItem('absenViewPreference', view);
        }

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('absenViewPreference') || 'card';
            setView(savedView);
        });

        // Auto-apply filters when enter is pressed
        document.getElementById('date-filter').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // Quick filter buttons
        function filterByStatus(status) {
            document.getElementById('status-filter').value = status;
            applyFilters();
        }

        function filterByToday() {
            document.getElementById('date-filter').value = new Date().toISOString().split('T')[0];
            applyFilters();
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Card hover effect */
        .hover\:shadow-lg:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }

        /* Icon width consistency */
        .w-4 {
            min-width: 1rem;
        }

        /* Responsive grid */
        @media (max-width: 768px) {
            .grid.md\:grid-cols-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .grid.md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1025px) {
            .grid.lg\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Status badge improvements */
        .status-badge {
            transition: all 0.2s ease;
        }

        /* Filter section responsive */
        @media (max-width: 640px) {
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-section>div {
                width: 100%;
            }
        }
    </style>
@endpush
