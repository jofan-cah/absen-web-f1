@extends('admin.layouts.app')

@section('title', 'Calendar Jadwal')
@section('breadcrumb', 'Calendar Jadwal')
@section('page_title', 'Management Jadwal - Calendar View')

@section('page_actions')
    <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center justify-between">
        <!-- Month/Year Navigation -->
        <div class="flex items-center gap-2">
            <button onclick="navigateMonth(-1)"
                class="p-1.5 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <div class="flex gap-1.5">
                <select id="month-selector"
                    class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-primary-500">
                    @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'] as $value => $name)
                        <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>{{ $name }}
                        </option>
                    @endforeach
                </select>
                <select id="year-selector"
                    class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-primary-500">
                    @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button onclick="navigateMonth(1)"
                class="p-1.5 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <button onclick="goToToday()"
                class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-xs">
                Hari Ini
            </button>
        </div>

        <!-- Actions -->
        <div class="flex gap-1.5">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.jadwal.index') }}"
                    class="px-2.5 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors flex items-center gap-1 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    List
                </a>
            @endif
            <button onclick="saveAllChanges()"
                class="px-2.5 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors flex items-center gap-1 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan
            </button>
        </div>
    </div>
@endsection

@section('content')

    <!-- Current Month Display - Compact -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-3">
        <h2 class="text-lg font-bold text-gray-900 mb-0.5">
            {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
        </h2>
        <p class="text-xs text-gray-600">
            <span class="font-semibold text-blue-600">1.</span> Pilih Shift →
            <span class="font-semibold text-blue-600">2.</span> Pilih Karyawan →
            <span class="font-semibold text-blue-600">3.</span> Klik Tanggal
        </p>
    </div>

    <!-- Main Container -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-3">

        <!-- Control Panel Sidebar - Compact -->
        <div class="lg:col-span-1 space-y-3">

            <!-- Shift Selection - Compact -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-2.5 py-2 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <div class="w-5 h-5 bg-green-500 rounded flex items-center justify-center mr-1.5">
                            <span class="text-white font-bold text-xs">1</span>
                        </div>
                        Shift
                    </h3>
                </div>
                <div class="p-2">
                    <div id="shift-list" class="space-y-1.5">
                        @foreach ($shifts as $index => $shift)
                            @php
                                $colorSchemes = [
                                    [
                                        'bg' => 'bg-blue-50',
                                        'border' => 'border-blue-200',
                                        'hover' => 'hover:bg-blue-100',
                                        'active' => 'bg-blue-500 text-white border-blue-600',
                                    ],
                                    [
                                        'bg' => 'bg-green-50',
                                        'border' => 'border-green-200',
                                        'hover' => 'hover:bg-green-100',
                                        'active' => 'bg-green-500 text-white border-green-600',
                                    ],
                                    [
                                        'bg' => 'bg-purple-50',
                                        'border' => 'border-purple-200',
                                        'hover' => 'hover:bg-purple-100',
                                        'active' => 'bg-purple-500 text-white border-purple-600',
                                    ],
                                    [
                                        'bg' => 'bg-orange-50',
                                        'border' => 'border-orange-200',
                                        'hover' => 'hover:bg-orange-100',
                                        'active' => 'bg-orange-500 text-white border-orange-600',
                                    ],
                                ];
                                $color = $colorSchemes[$index % 4];
                            @endphp

                            <div class="shift-option {{ $color['bg'] }} {{ $color['border'] }} {{ $color['hover'] }} border rounded p-2 cursor-pointer transition-all"
                                data-shift-id="{{ $shift->shift_id }}" data-shift-name="{{ $shift->name }}"
                                data-active-class="{{ $color['active'] }}"
                                onclick="selectShift('{{ $shift->shift_id }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 truncate">{{ $shift->name }}</p>
                                        <p class="text-xs text-gray-600 font-mono">
                                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                        </p>
                                    </div>
                                    <div class="checkmark hidden">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Karyawan Selection - Compact -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-2.5 py-2 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center">
                        <div class="w-5 h-5 bg-blue-500 rounded flex items-center justify-center mr-1.5">
                            <span class="text-white font-bold text-xs">2</span>
                        </div>
                        Karyawan
                    </h3>
                </div>
                <div class="p-2">
                    <!-- Search - Compact -->
                    <div class="relative mb-2">
                        <input type="text" id="karyawan-search" placeholder="Cari..."
                            class="w-full pl-6 pr-2 py-1 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500">
                        <svg class="absolute left-2 top-1.5 w-3 h-3 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Department Filter - Compact -->
                    <select id="department-filter"
                        class="w-full mb-2 px-2 py-1 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500">
                        <option value="">Semua Dept</option>
                        @foreach ($karyawans->unique('department_id')->pluck('department') as $department)
                            @if ($department)
                                <option value="{{ $department->department_id }}">{{ $department->name }}</option>
                            @endif
                        @endforeach
                    </select>

                    <!-- Karyawan List - Compact -->
                    <div id="karyawan-list" class="space-y-1 max-h-56 overflow-y-auto">
                        @foreach ($karyawans as $karyawan)
                            <div class="karyawan-option bg-gray-50 hover:bg-blue-50 border border-gray-200 hover:border-blue-300 rounded p-1.5 cursor-pointer transition-all"
                                data-karyawan-id="{{ $karyawan->karyawan_id }}"
                                data-karyawan-name="{{ $karyawan->full_name }}"
                                data-department-id="{{ $karyawan->department_id }}"
                                data-search="{{ strtolower($karyawan->full_name) }}"
                                onclick="selectKaryawan('{{ $karyawan->karyawan_id }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div
                                            class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2 flex-shrink-0">
                                            {{ substr($karyawan->full_name, 0, 2) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate">
                                                {{ $karyawan->full_name }}</p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ $karyawan->department->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="checkmark hidden ml-1 flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Auto Generate - Compact -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-2.5 py-1.5 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-900">Auto Generate</h3>
                </div>
                <div class="p-2 space-y-1">
                    <button onclick="autoGenerateMonth('weekdays')"
                        class="w-full text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Sen-Sab
                    </button>
                    <button onclick="autoGenerateMonth('all')"
                        class="w-full text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                        Full Bulan
                    </button>
                    <button onclick="showCustomDayModal()"
                        class="w-full text-xs px-2 py-1 bg-orange-600 text-white rounded hover:bg-orange-700">
                        Custom
                    </button>
                </div>
            </div>

        </div>

        <!-- Calendar - Compact -->
        <div class="lg:col-span-4">
            <!-- Selected Info Bar - Compact -->
            <div id="selected-info-bar"
                class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-md border border-indigo-200 p-2.5 mb-3 hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 text-white text-xs">
                        <div class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-xs opacity-75">Shift:</p>
                                <p id="selected-shift-display" class="font-semibold">-</p>
                            </div>
                        </div>
                        <div class="w-px h-8 bg-white/30"></div>
                        <div class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <p class="text-xs opacity-75">Karyawan:</p>
                                <p id="selected-karyawan-display" class="font-semibold">-</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="clearSelection()" class="text-white hover:text-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Calendar Header - Compact -->
                <div class="px-3 py-2 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-7 gap-2 text-center">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                            <div class="text-xs font-semibold text-gray-700">{{ $day }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- Calendar Body - Compact -->
                <div class="p-1.5">
                    <div id="calendar-grid" class="grid grid-cols-7 gap-1.5">
                        @php
                            $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1);
                            $endOfMonth = $startOfMonth->copy()->endOfMonth();
                            $startDate = $startOfMonth->copy()->startOfWeek();
                            $endDate = $endOfMonth->copy()->endOfWeek();
                            $currentDate = $startDate->copy();
                        @endphp

                        @while ($currentDate <= $endDate)
                            @php
                                $dateStr = $currentDate->format('Y-m-d');
                                $isCurrentMonth = $currentDate->month == $month;
                                $isToday = $currentDate->isToday();
                                $dayJadwals = collect($calendarData[$dateStr] ?? []);
                                // Cek apakah ada jadwal dengan ijin_id = true
                                $hasIjin = $dayJadwals->contains('ijin_id', true);
                            @endphp

                            <div class="calendar-day min-h-24 border rounded p-1.5 transition-all
        {{ $isCurrentMonth ? 'bg-white hover:border-blue-400 hover:shadow cursor-pointer' : 'bg-gray-50' }}
        {{ $hasIjin ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                                data-date="{{ $dateStr }}"
                                onclick="{{ $isCurrentMonth ? "assignToDate('$dateStr')" : '' }}">

                                <!-- Date Header - Compact -->
                                <div class="flex items-center justify-between mb-1">
                                    <span
                                        class="text-xs font-medium
                {{ $isToday
                    ? 'bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs'
                    : ($hasIjin
                        ? 'text-red-700 font-bold'
                        : ($isCurrentMonth
                            ? 'text-gray-900'
                            : 'text-gray-400')) }}">
                                        {{ $currentDate->day }}
                                    </span>

                                    @if ($dayJadwals->count() > 0)
                                        <span
                                            class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                    {{ $hasIjin ? 'bg-red-500 text-white' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $hasIjin ? 'IJIN' : $dayJadwals->count() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Jadwal Items - Compact -->
                                <div class="space-y-1 jadwal-container" data-date="{{ $dateStr }}">
                                    @foreach ($dayJadwals as $jadwal)
                                        @php
                                            // Cek apakah jadwal ini ijin
                                            $isIjin = isset($jadwal['ijin_id']) && $jadwal['ijin_id'] == true;

                                            // Jika ijin, gunakan warna merah, jika tidak gunakan warna shift biasa
                                            if ($isIjin) {
                                                $currentColor = [
                                                    'bg' => 'bg-red-100',
                                                    'border' => 'border-red-400',
                                                    'text' => 'text-red-900',
                                                    'textSecondary' => 'text-red-700',
                                                    'hover' => 'hover:bg-red-200',
                                                ];
                                            } else {
                                                $shiftIndex = $shifts->search(function ($shift) use ($jadwal) {
                                                    return $shift->shift_id === $jadwal['shift_id'];
                                                });
                                                $colorIndex = $shiftIndex !== false ? $shiftIndex % 8 : 0;

                                                $colorSchemes = [
                                                    [
                                                        'bg' => 'bg-blue-50',
                                                        'border' => 'border-blue-200',
                                                        'text' => 'text-blue-900',
                                                        'textSecondary' => 'text-blue-700',
                                                        'hover' => 'hover:bg-blue-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-green-50',
                                                        'border' => 'border-green-200',
                                                        'text' => 'text-green-900',
                                                        'textSecondary' => 'text-green-700',
                                                        'hover' => 'hover:bg-green-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-purple-50',
                                                        'border' => 'border-purple-200',
                                                        'text' => 'text-purple-900',
                                                        'textSecondary' => 'text-purple-700',
                                                        'hover' => 'hover:bg-purple-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-orange-50',
                                                        'border' => 'border-orange-200',
                                                        'text' => 'text-orange-900',
                                                        'textSecondary' => 'text-orange-700',
                                                        'hover' => 'hover:bg-orange-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-pink-50',
                                                        'border' => 'border-pink-200',
                                                        'text' => 'text-pink-900',
                                                        'textSecondary' => 'text-pink-700',
                                                        'hover' => 'hover:bg-pink-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-indigo-50',
                                                        'border' => 'border-indigo-200',
                                                        'text' => 'text-indigo-900',
                                                        'textSecondary' => 'text-indigo-700',
                                                        'hover' => 'hover:bg-indigo-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-teal-50',
                                                        'border' => 'border-teal-200',
                                                        'text' => 'text-teal-900',
                                                        'textSecondary' => 'text-teal-700',
                                                        'hover' => 'hover:bg-teal-100',
                                                    ],
                                                    [
                                                        'bg' => 'bg-red-50',
                                                        'border' => 'border-red-200',
                                                        'text' => 'text-red-900',
                                                        'textSecondary' => 'text-red-700',
                                                        'hover' => 'hover:bg-red-100',
                                                    ],
                                                ];

                                                $currentColor = $colorSchemes[$colorIndex];
                                            }
                                        @endphp

                                        <div class="jadwal-item {{ $currentColor['bg'] }} {{ $currentColor['border'] }} rounded p-1 text-xs transition-colors border
                    {{ $isIjin ? 'cursor-not-allowed opacity-90' : 'cursor-pointer ' . $currentColor['hover'] }}"
                                            data-jadwal-id="{{ $jadwal['jadwal_id'] }}"
                                            onclick="{{ $isIjin ? '' : "event.stopPropagation(); editJadwalItem('" . $jadwal['jadwal_id'] . "')" }}"
                                            title="{{ $isIjin ? 'Tidak dapat diedit karena sudah ada ijin' : 'Klik untuk edit/hapus' }}">

                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <!-- Nama Karyawan dengan Icon Ijin -->
                                                    <div class="flex items-center gap-1">
                                                        <p
                                                            class="font-medium {{ $currentColor['text'] }} truncate text-xs leading-tight">
                                                            {{ $jadwal['karyawan_name'] }}
                                                        </p>
                                                        @if ($isIjin)
                                                            <svg class="w-3 h-3 text-red-600 flex-shrink-0"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        @endif
                                                    </div>

                                                    <!-- Shift Name -->
                                                    <p
                                                        class="{{ $currentColor['textSecondary'] }} truncate text-xs leading-tight">
                                                        {{ $jadwal['shift_name'] }}
                                                    </p>

                                                    <!-- Keterangan Ijin (jika ada) -->
                                                    @if ($isIjin && !empty($jadwal['keterangan_ijin']))
                                                        <p
                                                            class="text-red-700 font-semibold text-[10px] leading-tight mt-0.5 italic">
                                                            📋 {{ $jadwal['keterangan_ijin'] }}
                                                        </p>
                                                    @elseif($isIjin)
                                                        <p
                                                            class="text-red-700 font-semibold text-[10px] leading-tight mt-0.5 italic">
                                                            📋 Sedang Ijin
                                                        </p>
                                                    @endif
                                                </div>

                                                @if ($isIjin)
                                                    <!-- Tombol disabled jika ada ijin -->
                                                    <button disabled title="Tidak dapat dihapus karena sudah ada ijin"
                                                        class="text-gray-400 cursor-not-allowed p-0.5">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <!-- Tombol normal jika tidak ada ijin -->
                                                    <button
                                                        onclick="event.stopPropagation(); deleteJadwalItem('{{ $jadwal['jadwal_id'] }}')"
                                                        class="text-red-500 hover:text-red-700 p-0.5">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            @php $currentDate->addDay(); @endphp
                        @endwhile
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal - Sama, script tetap sama -->
    <div id="edit-jadwal-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-4 border w-80 shadow-lg rounded-lg bg-white">
            <div class="mt-2">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-900">Edit Jadwal</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="edit-jadwal-form">
                    <input type="hidden" id="edit-jadwal-id">

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Karyawan</label>
                        <input type="text" id="edit-karyawan-name" readonly
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-50">
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="text" id="edit-date" readonly
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded bg-gray-50">
                    </div>

                    <div class="mb-3">
                        <label for="edit-shift-select" class="block text-xs font-medium text-gray-700 mb-1">Shift</label>
                        <select id="edit-shift-select"
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->shift_id }}">
                                    {{ $shift->name }}
                                    ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-3 py-1.5 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="button" onclick="deleteCurrentJadwal()"
                            class="px-3 py-1.5 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                            Hapus
                        </button>
                        <button type="submit"
                            class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- JavaScript tetap sama seperti sebelumnya, tidak ada perubahan -->
    <script>
        // Copy semua JavaScript dari response sebelumnya, tidak ada perubahan
        let currentMonth = {{ $month }};
        let currentYear = {{ $year }};
        let selectedShiftId = null;
        let selectedShiftName = null;
        let selectedKaryawanId = null;
        let selectedKaryawanName = null;
        let pendingChanges = [];
        let currentEditingJadwal = null;

        const karyawanData = @json($karyawans);
        const shiftData = @json($shifts);
        const calendarData = @json($calendarData);

        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
            initializeEventListeners();
        });

        function selectShift(shiftId) {
            selectedShiftId = shiftId;
            const shiftElement = document.querySelector(`[data-shift-id="${shiftId}"]`);
            selectedShiftName = shiftElement.dataset.shiftName;

            // Update UI
            document.querySelectorAll('.shift-option').forEach(el => {
                el.classList.remove('ring-4', 'ring-blue-500');
                el.querySelector('.checkmark').classList.add('hidden');
                const activeClass = el.dataset.activeClass;
                activeClass.split(' ').forEach(cls => el.classList.remove(cls));
            });

            const activeClass = shiftElement.dataset.activeClass;
            activeClass.split(' ').forEach(cls => shiftElement.classList.add(cls));
            shiftElement.classList.add('ring-4', 'ring-blue-500');
            shiftElement.querySelector('.checkmark').classList.remove('hidden');

            updateSelectedInfo();
        }

        function selectKaryawan(karyawanId) {
            selectedKaryawanId = karyawanId;
            const karyawanElement = document.querySelector(`.karyawan-option[data-karyawan-id="${karyawanId}"]`);
            selectedKaryawanName = karyawanElement.dataset.karyawanName;

            // Update UI
            document.querySelectorAll('.karyawan-option').forEach(el => {
                el.classList.remove('bg-blue-500', 'text-white', 'border-blue-600');
                el.querySelector('.checkmark').classList.add('hidden');
            });

            karyawanElement.classList.add('bg-blue-500', 'text-white', 'border-blue-600');
            karyawanElement.querySelector('.checkmark').classList.remove('hidden');

            updateSelectedInfo();
        }

        function updateSelectedInfo() {
            const infoBar = document.getElementById('selected-info-bar');
            const shiftDisplay = document.getElementById('selected-shift-display');
            const karyawanDisplay = document.getElementById('selected-karyawan-display');

            if (selectedShiftId && selectedKaryawanId) {
                infoBar.classList.remove('hidden');
                shiftDisplay.textContent = selectedShiftName;
                karyawanDisplay.textContent = selectedKaryawanName;
            } else if (selectedShiftId) {
                infoBar.classList.remove('hidden');
                shiftDisplay.textContent = selectedShiftName;
                karyawanDisplay.textContent = 'Belum dipilih';
            } else {
                infoBar.classList.add('hidden');
            }
        }

        function clearSelection() {
            selectedShiftId = null;
            selectedShiftName = null;
            selectedKaryawanId = null;
            selectedKaryawanName = null;

            document.querySelectorAll('.shift-option').forEach(el => {
                el.classList.remove('ring-4', 'ring-blue-500');
                el.querySelector('.checkmark').classList.add('hidden');
                const activeClass = el.dataset.activeClass;
                activeClass.split(' ').forEach(cls => el.classList.remove(cls));
            });

            document.querySelectorAll('.karyawan-option').forEach(el => {
                el.classList.remove('bg-blue-500', 'text-white', 'border-blue-600');
                el.querySelector('.checkmark').classList.add('hidden');
            });

            updateSelectedInfo();
        }

        function assignToDate(date) {
            if (!selectedShiftId) {
                showNotification('Pilih shift terlebih dahulu (Langkah 1)', 'error');
                return;
            }

            if (!selectedKaryawanId) {
                showNotification('Pilih karyawan terlebih dahulu (Langkah 2)', 'error');
                return;
            }

            const dayElement = document.querySelector(`[data-date="${date}"]`);

            // Check if already exists
            const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${selectedKaryawanId}"]`);
            if (existingJadwal) {
                showNotification('Karyawan sudah memiliki jadwal di tanggal ini', 'error');
                return;
            }

            createJadwalItem(date, selectedKaryawanId, selectedShiftId, dayElement);
            showNotification('Jadwal ditambahkan! Klik tanggal lain untuk lanjut', 'success');
        }

        function createJadwalItem(date, karyawanId, shiftId, dayElement) {
            const karyawan = karyawanData.find(k => k.karyawan_id === karyawanId);
            const shift = shiftData.find(s => s.shift_id === shiftId);

            if (!karyawan || !shift) {
                alert('Data tidak ditemukan');
                return;
            }

            const shiftColor = getShiftColor(shiftId);
            const jadwalContainer = dayElement.querySelector('.jadwal-container');
            const jadwalItem = document.createElement('div');

            jadwalItem.className =
                `jadwal-item ${shiftColor.bg} ${shiftColor.border} rounded p-2 text-xs cursor-pointer ${shiftColor.hover} transition-all new-jadwal`;
            jadwalItem.dataset.karyawanId = karyawanId;
            jadwalItem.dataset.shiftId = shiftId;
            jadwalItem.dataset.date = date;
            jadwalItem.dataset.isNew = 'true';

            jadwalItem.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="font-medium ${shiftColor.text} truncate text-xs">${karyawan.full_name}</p>
                    <p class="${shiftColor.textSecondary} truncate text-xs">${shift.name}</p>
                </div>
                <button onclick="event.stopPropagation(); removeNewJadwal(this)" class="text-red-500 hover:text-red-700 p-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

            jadwalContainer.appendChild(jadwalItem);

            pendingChanges.push({
                action: 'create',
                karyawan_id: karyawanId,
                shift_id: shiftId,
                date: date,
                element: jadwalItem
            });

            updatePendingIndicator();
        }

        function getShiftColor(shiftId) {
            const shiftIndex = shiftData.findIndex(s => s.shift_id === shiftId);
            const colorIndex = shiftIndex % 8;

            const colorSchemes = [{
                    bg: 'bg-blue-100',
                    border: 'border-blue-200',
                    hover: 'hover:bg-blue-200',
                    text: 'text-blue-900',
                    textSecondary: 'text-blue-700'
                },
                {
                    bg: 'bg-green-100',
                    border: 'border-green-200',
                    hover: 'hover:bg-green-200',
                    text: 'text-green-900',
                    textSecondary: 'text-green-700'
                },
                {
                    bg: 'bg-purple-100',
                    border: 'border-purple-200',
                    hover: 'hover:bg-purple-200',
                    text: 'text-purple-900',
                    textSecondary: 'text-purple-700'
                },
                {
                    bg: 'bg-orange-100',
                    border: 'border-orange-200',
                    hover: 'hover:bg-orange-200',
                    text: 'text-orange-900',
                    textSecondary: 'text-orange-700'
                },
                {
                    bg: 'bg-pink-100',
                    border: 'border-pink-200',
                    hover: 'hover:bg-pink-200',
                    text: 'text-pink-900',
                    textSecondary: 'text-pink-700'
                },
                {
                    bg: 'bg-indigo-100',
                    border: 'border-indigo-200',
                    hover: 'hover:bg-indigo-200',
                    text: 'text-indigo-900',
                    textSecondary: 'text-indigo-700'
                },
                {
                    bg: 'bg-teal-100',
                    border: 'border-teal-200',
                    hover: 'hover:bg-teal-200',
                    text: 'text-teal-900',
                    textSecondary: 'text-teal-700'
                },
                {
                    bg: 'bg-red-100',
                    border: 'border-red-200',
                    hover: 'hover:bg-red-200',
                    text: 'text-red-900',
                    textSecondary: 'text-red-700'
                }
            ];

            return colorSchemes[colorIndex];
        }

        function removeNewJadwal(button) {
            const jadwalItem = button.closest('.jadwal-item');
            const karyawanId = jadwalItem.dataset.karyawanId;
            const date = jadwalItem.dataset.date;

            pendingChanges = pendingChanges.filter(change =>
                !(change.action === 'create' && change.karyawan_id === karyawanId && change.date === date)
            );

            jadwalItem.remove();
            updatePendingIndicator();
        }

        function initializeSearch() {
            const searchInput = document.getElementById('karyawan-search');
            const departmentFilter = document.getElementById('department-filter');

            function filterKaryawan() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDepartment = departmentFilter.value;

                document.querySelectorAll('.karyawan-option').forEach(item => {
                    const searchData = item.dataset.search;
                    const departmentId = item.dataset.departmentId;

                    const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                    const matchesDepartment = !selectedDepartment || departmentId === selectedDepartment;

                    if (matchesSearch && matchesDepartment) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterKaryawan);
            departmentFilter.addEventListener('change', filterKaryawan);
        }

        function initializeEventListeners() {
            document.getElementById('edit-jadwal-form').addEventListener('submit', function(e) {
                e.preventDefault();
                updateJadwal();
            });

            document.getElementById('month-selector').addEventListener('change', navigateToSelectedMonth);
            document.getElementById('year-selector').addEventListener('change', navigateToSelectedMonth);
        }

        function navigateToSelectedMonth() {
            const month = document.getElementById('month-selector').value;
            const year = document.getElementById('year-selector').value;
            window.location.href = `{{ route('admin.jadwal.calendar') }}?month=${month}&year=${year}`;
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
                `{{ route('admin.jadwal.calendar') }}?month=${newMonth.toString().padStart(2, '0')}&year=${newYear}`;
        }

        function goToToday() {
            const today = new Date();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const year = today.getFullYear();
            window.location.href = `{{ route('admin.jadwal.calendar') }}?month=${month}&year=${year}`;
        }

        function editJadwalItem(jadwalId) {
            let jadwalData = null;
            for (const date in calendarData) {
                const dayJadwals = calendarData[date];
                jadwalData = dayJadwals.find(j => j.jadwal_id === jadwalId);
                if (jadwalData) break;
            }

            if (!jadwalData) {
                alert('Data jadwal tidak ditemukan');
                return;
            }

            if (!jadwalData.is_editable) {
                alert('Jadwal tidak dapat diedit karena karyawan sudah absensi');
                return;
            }

            currentEditingJadwal = jadwalId;

            document.getElementById('edit-jadwal-id').value = jadwalId;
            document.getElementById('edit-karyawan-name').value = jadwalData.karyawan_name;
            document.getElementById('edit-date').value = formatDate(getJadwalDate(jadwalId));
            document.getElementById('edit-shift-select').value = jadwalData.shift_id;

            document.getElementById('edit-jadwal-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-jadwal-modal').classList.add('hidden');
            currentEditingJadwal = null;
        }

        function updateJadwal() {
            if (!currentEditingJadwal) return;

            const shiftId = document.getElementById('edit-shift-select').value;

            fetch(`{{ route('admin.jadwal.index') }}/${currentEditingJadwal}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        shift_id: shiftId,
                        notes: ''
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeEditModal();
                        showNotification('Jadwal berhasil diperbarui', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    showNotification(error.message || 'Terjadi kesalahan', 'error');
                });
        }

        function deleteCurrentJadwal() {
            if (!currentEditingJadwal) return;
            if (confirm('Hapus jadwal ini?')) {
                deleteJadwalItem(currentEditingJadwal);
            }
        }

        function deleteJadwalItem(jadwalId) {
            fetch(`{{ route('admin.jadwal.index') }}/${jadwalId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const jadwalElement = document.querySelector(`[data-jadwal-id="${jadwalId}"]`);
                        if (jadwalElement) jadwalElement.remove();
                        closeEditModal();
                        showNotification('Jadwal berhasil dihapus', 'success');
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    showNotification(error.message || 'Terjadi kesalahan', 'error');
                });
        }

        function saveAllChanges() {
            if (pendingChanges.length === 0) {
                alert('Tidak ada perubahan untuk disimpan');
                return;
            }

            const jadwalsToCreate = pendingChanges.filter(change => change.action === 'create');

            const bulkData = {
                jadwals: jadwalsToCreate.map(change => ({
                    karyawan_id: change.karyawan_id,
                    shift_id: change.shift_id,
                    date: change.date
                }))
            };

            fetch('{{ route('admin.jadwal.bulk-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(bulkData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`${data.data.length} jadwal berhasil disimpan`, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan saat menyimpan');
                });
        }

        function updatePendingIndicator() {
            const count = pendingChanges.length;
            const saveButton = document.querySelector('[onclick="saveAllChanges()"]');

            if (count > 0) {
                saveButton.classList.add('animate-pulse');
                saveButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan (${count})
            `;
            } else {
                saveButton.classList.remove('animate-pulse');
                saveButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Semua
            `;
            }
        }

        function autoGenerateMonth(type) {
            if (!selectedShiftId || !selectedKaryawanId) {
                alert('Pilih shift dan karyawan terlebih dahulu');
                return;
            }

            const year = currentYear;
            const month = currentMonth;
            const daysInMonth = new Date(year, month, 0).getDate();
            const generatedDates = [];

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const date = new Date(dateStr + 'T12:00:00');
                const dayOfWeek = date.getDay();

                let shouldGenerate = false;

                switch (type) {
                    case 'all':
                        shouldGenerate = true;
                        break;
                    case 'weekdays':
                        shouldGenerate = dayOfWeek >= 1 && dayOfWeek <= 6;
                        break;
                    case 'weekends':
                        shouldGenerate = dayOfWeek === 0;
                        break;
                }

                if (shouldGenerate) {
                    const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
                    if (dayElement) {
                        const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${selectedKaryawanId}"]`);
                        if (!existingJadwal) {
                            createJadwalItem(dateStr, selectedKaryawanId, selectedShiftId, dayElement);
                            generatedDates.push(dateStr);
                        }
                    }
                }
            }

            if (generatedDates.length > 0) {
                showNotification(`${generatedDates.length} jadwal berhasil digenerate`, 'success');
                updatePendingIndicator();
            } else {
                showNotification('Tidak ada jadwal baru yang digenerate', 'info');
            }
        }

        function showCustomDayModal() {
            if (!selectedShiftId || !selectedKaryawanId) {
                alert('Pilih shift dan karyawan terlebih dahulu');
                return;
            }

            const modal = document.createElement('div');
            modal.id = 'custom-day-modal';
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';

            modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Pilih Hari</h3>
                        <button onclick="closeCustomDayModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-3">Pilih hari dalam seminggu:</p>
                        <div class="space-y-2">
                            ${['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'].map((day, idx) => `
                                        <label class="flex items-center">
                                            <input type="checkbox" value="${idx}" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                                            <span class="ml-2 text-sm">${day}</span>
                                        </label>
                                    `).join('')}
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeCustomDayModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Batal</button>
                        <button onclick="generateCustomDays()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Generate</button>
                    </div>
                </div>
            </div>
        `;

            document.body.appendChild(modal);
        }

        function closeCustomDayModal() {
            const modal = document.getElementById('custom-day-modal');
            if (modal) modal.remove();
        }

        function generateCustomDays() {
            const selectedDays = Array.from(document.querySelectorAll('.custom-day-checkbox:checked')).map(cb => parseInt(cb
                .value));

            if (selectedDays.length === 0) {
                alert('Pilih minimal satu hari');
                return;
            }

            const year = currentYear;
            const month = currentMonth;
            const daysInMonth = new Date(year, month, 0).getDate();
            const generatedDates = [];

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const date = new Date(dateStr + 'T12:00:00');
                const dayOfWeek = date.getDay();

                if (selectedDays.includes(dayOfWeek)) {
                    const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
                    if (dayElement) {
                        const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${selectedKaryawanId}"]`);
                        if (!existingJadwal) {
                            createJadwalItem(dateStr, selectedKaryawanId, selectedShiftId, dayElement);
                            generatedDates.push(dateStr);
                        }
                    }
                }
            }

            closeCustomDayModal();

            if (generatedDates.length > 0) {
                showNotification(`${generatedDates.length} jadwal berhasil digenerate`, 'success');
                updatePendingIndicator();
            }
        }

        function showNotification(message, type = 'info') {
            const existingNotification = document.getElementById('notification');
            if (existingNotification) existingNotification.remove();

            const notification = document.createElement('div');
            notification.id = 'notification';
            notification.className =
                `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg border max-w-sm transition-all duration-300 transform translate-x-full`;

            if (type === 'success') {
                notification.className += ' bg-green-100 border-green-400 text-green-700';
            } else if (type === 'error') {
                notification.className += ' bg-red-100 border-red-400 text-red-700';
            } else {
                notification.className += ' bg-blue-100 border-blue-400 text-blue-700';
            }

            notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' ?
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :
                        type === 'error' ?
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' :
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto pl-3 text-current hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;

            document.body.appendChild(notification);

            setTimeout(() => notification.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function getJadwalDate(jadwalId) {
            for (const date in calendarData) {
                if (calendarData[date].some(j => j.jadwal_id === jadwalId)) {
                    return date;
                }
            }
            return null;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSelection();
                closeEditModal();
            }

            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAllChanges();
            }
        });

        window.addEventListener('beforeunload', function(e) {
            if (pendingChanges.length > 0) {
                e.preventDefault();
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan.';
            }
        });
    </script>
@endpush


@push('styles')
    <style>
        .new-jadwal {
            animation: slideIn 0.2s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .calendar-day:hover {
            transform: translateY(-1px);
        }

        #karyawan-list::-webkit-scrollbar {
            width: 4px;
        }

        #karyawan-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
    </style>
@endpush
