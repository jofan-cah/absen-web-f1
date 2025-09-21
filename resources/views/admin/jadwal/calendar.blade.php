@extends('admin.layouts.app')

@section('title', 'Calendar Jadwal')
@section('breadcrumb', 'Calendar Jadwal')
@section('page_title', 'Management Jadwal - Calendar View')

@section('page_actions')
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <!-- Month/Year Navigation -->
        <div class="flex items-center gap-3">
            <button onclick="navigateMonth(-1)"
                class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

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
                    @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button onclick="navigateMonth(1)"
                class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <button onclick="goToToday()"
                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                Hari Ini
            </button>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
            <a href="{{ route('admin.jadwal.index') }}"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                List View
            </a>
            <button onclick="saveAllChanges()"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Semua
            </button>
        </div>
    </div>
@endsection

@section('content')

    <!-- Current Month Display -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">
            {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
        </h2>
        <p class="text-gray-600">Drag karyawan ke tanggal dan pilih shift untuk membuat jadwal</p>
    </div>

    <!-- Sidebar dan Calendar Container -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Sidebar: Karyawan dan Shift -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Karyawan Pool -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Karyawan
                    </h3>
                </div>
                <div class="p-4">
                    <!-- Search Karyawan -->
                    <div class="relative mb-4">
                        <input type="text" id="karyawan-search" placeholder="Cari karyawan..."
                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <svg class="absolute left-2.5 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Filter Department -->
                    <select id="department-filter"
                        class="w-full mb-4 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Department</option>
                        @foreach ($karyawans->unique('department_id')->pluck('department') as $department)
                            @if ($department)
                                <option value="{{ $department->department_id }}">{{ $department->name }}</option>
                            @endif
                        @endforeach
                    </select>

                    <!-- Karyawan List -->
                    <div id="karyawan-pool" class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach ($karyawans as $karyawan)
                            <div class="karyawan-item bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg p-3 cursor-move transition-colors"
                                draggable="true" data-karyawan-id="{{ $karyawan->karyawan_id }}"
                                data-department-id="{{ $karyawan->department_id }}"
                                data-search="{{ strtolower($karyawan->full_name) }}">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                        {{ substr($karyawan->full_name, 0, 2) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $karyawan->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">{{ $karyawan->department->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Shift Templates -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Shift Template
                    </h3>
                </div>
                <div class="p-4">
                    <div id="shift-list" class="space-y-2">
                        @foreach ($shifts as $index => $shift)
                            @php
                                $colorIndex = $index % 8;
                                $colorSchemes = [
                                    ['bg' => 'bg-blue-100', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                                    ['bg' => 'bg-green-100', 'border' => 'border-green-200', 'dot' => 'bg-green-500'],
                                    [
                                        'bg' => 'bg-purple-100',
                                        'border' => 'border-purple-200',
                                        'dot' => 'bg-purple-500',
                                    ],
                                    [
                                        'bg' => 'bg-orange-100',
                                        'border' => 'border-orange-200',
                                        'dot' => 'bg-orange-500',
                                    ],
                                    ['bg' => 'bg-pink-100', 'border' => 'border-pink-200', 'dot' => 'bg-pink-500'],
                                    [
                                        'bg' => 'bg-indigo-100',
                                        'border' => 'border-indigo-200',
                                        'dot' => 'bg-indigo-500',
                                    ],
                                    ['bg' => 'bg-teal-100', 'border' => 'border-teal-200', 'dot' => 'bg-teal-500'],
                                    ['bg' => 'bg-red-100', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
                                ];
                                $currentColor = $colorSchemes[$colorIndex];
                            @endphp

                            <div class="shift-template {{ $currentColor['bg'] }} hover:bg-gray-100 {{ $currentColor['border'] }} rounded-lg p-3 cursor-pointer transition-colors"
                                data-shift-id="{{ $shift->shift_id }}" onclick="selectShift('{{ $shift->shift_id }}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <!-- Color indicator dot -->
                                        <div class="w-3 h-3 {{ $currentColor['dot'] }} rounded-full mr-3 flex-shrink-0">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $shift->name }}</p>
                                            <p class="text-xs text-gray-500 font-mono">
                                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="w-6 h-6 rounded-lg bg-gradient-to-br {{ $shift->is_overnight ? 'from-purple-500 to-indigo-600' : 'from-green-500 to-emerald-600' }} flex items-center justify-center">
                                        @if ($shift->is_overnight)
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Color Legend -->
                    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <h4 class="text-xs font-semibold text-gray-700 mb-2">Color Legend:</h4>
                        <div class="grid grid-cols-2 gap-1 text-xs">
                            @php
                                $colorNames = ['Blue', 'Green', 'Purple', 'Orange', 'Pink', 'Indigo', 'Teal', 'Red'];
                                $colorClasses = [
                                    'bg-blue-500',
                                    'bg-green-500',
                                    'bg-purple-500',
                                    'bg-orange-500',
                                    'bg-pink-500',
                                    'bg-indigo-500',
                                    'bg-teal-500',
                                    'bg-red-500',
                                ];
                            @endphp
                            @for ($i = 0; $i < min(count($shifts), 8); $i++)
                                <div class="flex items-center">
                                    <div class="w-2 h-2 {{ $colorClasses[$i] }} rounded-full mr-1"></div>
                                    <span class="text-gray-600 truncate">{{ $shifts[$i]->name }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-700 mb-3">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pilih shift lalu drag karyawan ke tanggal
                        </p>

                        <!-- Auto Generate Section -->
                        <div class="border-t border-blue-200 pt-3">
                            <h5 class="text-xs font-semibold text-blue-800 mb-2">Auto Generate:</h5>
                            <div class="space-y-2">
                                <select id="auto-karyawan-select"
                                    class="w-full text-xs px-2 py-1 border border-blue-300 rounded focus:ring-1 focus:ring-blue-500">
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->karyawan_id }}">{{ $karyawan->full_name }}</option>
                                    @endforeach
                                </select>

                                <div class="grid grid-cols-2 gap-1">
                                    <button onclick="autoGenerateMonth('weekdays')"
                                        class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                        title="Senin-Jumat">
                                        Hari Kerja
                                    </button>
                                    <button onclick="autoGenerateMonth('all')"
                                        class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                        title="Semua hari dalam bulan">
                                        Full Bulan
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-1">
                                    <button onclick="autoGenerateMonth('weekends')"
                                        class="text-xs px-2 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
                                        title="Sabtu-Minggu">
                                        Weekend
                                    </button>
                                    <button onclick="showCustomDayModal()"
                                        class="text-xs px-2 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors"
                                        title="Pilih hari tertentu">
                                        Custom
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Calendar -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <!-- Calendar Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-7 gap-4 text-center">
                        @foreach ([ 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu','Minggu'] as $day)
                            <div class="text-sm font-semibold text-gray-700 py-2">{{ $day }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- Calendar Body -->
                <div class="p-2">
                    <div id="calendar-grid" class="grid grid-cols-7 gap-2">
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
                            @endphp

                            <div class="calendar-day min-h-32 border border-gray-200 rounded-lg p-2 {{ $isCurrentMonth ? 'bg-white hover:bg-gray-50' : 'bg-gray-100' }} transition-colors"
                                data-date="{{ $dateStr }}" ondrop="dropKaryawan(event)"
                                ondragover="allowDrop(event)" ondragenter="dragEnter(event)"
                                ondragleave="dragLeave(event)">

                                <!-- Date Header -->
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="text-sm font-medium {{ $isToday ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : ($isCurrentMonth ? 'text-gray-900' : 'text-gray-400') }}">
                                        {{ $currentDate->day }}
                                    </span>
                                    @if ($dayJadwals->count() > 0)
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                            {{ $dayJadwals->count() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Jadwal Items -->
                                <div class="space-y-1 jadwal-container" data-date="{{ $dateStr }}">
                                    @foreach ($dayJadwals as $jadwal)
                                        @php
                                            $shiftIndex = $shifts->search(function ($shift) use ($jadwal) {
                                                return $shift->shift_id === $jadwal['shift_id'];
                                            });
                                            $colorIndex = $shiftIndex !== false ? $shiftIndex % 8 : 0;

                                            $colorSchemes = [
                                                [
                                                    'bg' => 'bg-blue-100',
                                                    'border' => 'border-blue-200',
                                                    'text' => 'text-blue-900',
                                                    'textSecondary' => 'text-blue-700',
                                                    'textTertiary' => 'text-blue-600',
                                                    'hover' => 'hover:bg-blue-200',
                                                ],
                                                [
                                                    'bg' => 'bg-green-100',
                                                    'border' => 'border-green-200',
                                                    'text' => 'text-green-900',
                                                    'textSecondary' => 'text-green-700',
                                                    'textTertiary' => 'text-green-600',
                                                    'hover' => 'hover:bg-green-200',
                                                ],
                                                [
                                                    'bg' => 'bg-purple-100',
                                                    'border' => 'border-purple-200',
                                                    'text' => 'text-purple-900',
                                                    'textSecondary' => 'text-purple-700',
                                                    'textTertiary' => 'text-purple-600',
                                                    'hover' => 'hover:bg-purple-200',
                                                ],
                                                [
                                                    'bg' => 'bg-orange-100',
                                                    'border' => 'border-orange-200',
                                                    'text' => 'text-orange-900',
                                                    'textSecondary' => 'text-orange-700',
                                                    'textTertiary' => 'text-orange-600',
                                                    'hover' => 'hover:bg-orange-200',
                                                ],
                                                [
                                                    'bg' => 'bg-pink-100',
                                                    'border' => 'border-pink-200',
                                                    'text' => 'text-pink-900',
                                                    'textSecondary' => 'text-pink-700',
                                                    'textTertiary' => 'text-pink-600',
                                                    'hover' => 'hover:bg-pink-200',
                                                ],
                                                [
                                                    'bg' => 'bg-indigo-100',
                                                    'border' => 'border-indigo-200',
                                                    'text' => 'text-indigo-900',
                                                    'textSecondary' => 'text-indigo-700',
                                                    'textTertiary' => 'text-indigo-600',
                                                    'hover' => 'hover:bg-indigo-200',
                                                ],
                                                [
                                                    'bg' => 'bg-teal-100',
                                                    'border' => 'border-teal-200',
                                                    'text' => 'text-teal-900',
                                                    'textSecondary' => 'text-teal-700',
                                                    'textTertiary' => 'text-teal-600',
                                                    'hover' => 'hover:bg-teal-200',
                                                ],
                                                [
                                                    'bg' => 'bg-red-100',
                                                    'border' => 'border-red-200',
                                                    'text' => 'text-red-900',
                                                    'textSecondary' => 'text-red-700',
                                                    'textTertiary' => 'text-red-600',
                                                    'hover' => 'hover:bg-red-200',
                                                ],
                                            ];

                                            $currentColor = $colorSchemes[$colorIndex];
                                        @endphp

                                        <div class="jadwal-item {{ $currentColor['bg'] }} {{ $currentColor['border'] }} rounded p-2 text-xs cursor-pointer {{ $currentColor['hover'] }} transition-colors"
                                            data-jadwal-id="{{ $jadwal['jadwal_id'] }}"
                                            onclick="editJadwalItem('{{ $jadwal['jadwal_id'] }}')"
                                            title="Klik untuk edit atau hapus">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-medium {{ $currentColor['text'] }} truncate">
                                                        {{ $jadwal['karyawan_name'] }}</p>
                                                    <p class="{{ $currentColor['textSecondary'] }} truncate">
                                                        {{ $jadwal['shift_name'] }}</p>
                                                    <p class="{{ $currentColor['textTertiary'] }} font-mono">
                                                        {{ $jadwal['shift_time'] }}</p>
                                                </div>
                                                <button
                                                    onclick="event.stopPropagation(); deleteJadwalItem('{{ $jadwal['jadwal_id'] }}')"
                                                    class="text-red-500 hover:text-red-700 p-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Drop Zone Indicator -->
                                <div
                                    class="drop-zone-indicator hidden mt-2 p-2 border-2 border-dashed border-blue-400 bg-blue-50 rounded text-center text-xs text-blue-600">
                                    Drop di sini
                                </div>

                            </div>

                            @php $currentDate->addDay(); @endphp
                        @endwhile
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Shift Indicator -->
    <div id="selected-shift-indicator"
        class="fixed bottom-6 right-6 bg-white rounded-lg shadow-lg border border-gray-200 p-4 hidden">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">Shift Terpilih:</p>
                <p id="selected-shift-name" class="text-sm text-gray-600">-</p>
                <p id="selected-shift-time" class="text-xs text-gray-500 font-mono">-</p>
            </div>
            <button onclick="clearSelectedShift()" class="ml-4 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Edit Jadwal Modal -->
    <div id="edit-jadwal-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Jadwal</h3>
                    <button onclick="closeEditJadwalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="edit-jadwal-form">
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
                        <label for="edit-shift-select" class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                        <select id="edit-shift-select"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->shift_id }}">
                                    {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="edit-notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea id="edit-notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeEditJadwalModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <button type="button" onclick="deleteCurrentJadwal()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Hapus
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
        let selectedShiftId = null;
        let selectedShiftData = null;
        let pendingChanges = [];
        let currentEditingJadwal = null;

        // Karyawan data
        const karyawanData = @json($karyawans);
        const shiftData = @json($shifts);
        const calendarData = @json($calendarData);

        document.addEventListener('DOMContentLoaded', function() {
            initializeDragAndDrop();
            initializeSearch();
            initializeEventListeners();
        });

        function initializeDragAndDrop() {
            // Karyawan drag start
            document.querySelectorAll('.karyawan-item').forEach(item => {
                item.addEventListener('dragstart', function(e) {
                    if (!selectedShiftId) {
                        e.preventDefault();
                        alert('Pilih shift terlebih dahulu');
                        return;
                    }

                    const karyawanId = this.dataset.karyawanId;
                    e.dataTransfer.setData('text/plain', JSON.stringify({
                        karyawanId: karyawanId,
                        shiftId: selectedShiftId
                    }));

                    this.style.opacity = '0.5';
                });

                item.addEventListener('dragend', function(e) {
                    this.style.opacity = '1';
                });
            });
        }

        function allowDrop(e) {
            e.preventDefault();
        }

        function dragEnter(e) {
            e.preventDefault();
            const dayElement = e.currentTarget;
            dayElement.classList.add('bg-blue-50', 'border-blue-400');
            dayElement.querySelector('.drop-zone-indicator').classList.remove('hidden');
        }

        function dragLeave(e) {
            const dayElement = e.currentTarget;
            dayElement.classList.remove('bg-blue-50', 'border-blue-400');
            dayElement.querySelector('.drop-zone-indicator').classList.add('hidden');
        }

        function dropKaryawan(e) {
            e.preventDefault();

            const dayElement = e.currentTarget;
            dayElement.classList.remove('bg-blue-50', 'border-blue-400');
            dayElement.querySelector('.drop-zone-indicator').classList.add('hidden');

            const dropData = JSON.parse(e.dataTransfer.getData('text/plain'));
            const date = dayElement.dataset.date;

            // Check if already exists
            const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${dropData.karyawanId}"]`);
            if (existingJadwal) {
                alert('Karyawan sudah memiliki jadwal di tanggal ini');
                return;
            }

            createJadwalItem(date, dropData.karyawanId, dropData.shiftId, dayElement);
        }

        function createJadwalItem(date, karyawanId, shiftId, dayElement) {
            const karyawan = karyawanData.find(k => k.karyawan_id === karyawanId);
            const shift = shiftData.find(s => s.shift_id === shiftId);

            if (!karyawan || !shift) {
                alert('Data karyawan atau shift tidak ditemukan');
                return;
            }

            // Get shift color
            const shiftColor = getShiftColor(shiftId);

            // Create temporary jadwal item
            const jadwalContainer = dayElement.querySelector('.jadwal-container');
            const jadwalItem = document.createElement('div');
            jadwalItem.className =
                `jadwal-item ${shiftColor.bg} ${shiftColor.border} rounded p-2 text-xs cursor-pointer hover:${shiftColor.hover} transition-colors new-jadwal`;
            jadwalItem.dataset.karyawanId = karyawanId;
            jadwalItem.dataset.shiftId = shiftId;
            jadwalItem.dataset.date = date;
            jadwalItem.dataset.isNew = 'true';

            jadwalItem.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="font-medium ${shiftColor.text} truncate">${karyawan.full_name}</p>
                <p class="${shiftColor.textSecondary} truncate">${shift.name}</p>
                <p class="${shiftColor.textTertiary} font-mono">${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}</p>
            </div>
            <button onclick="removeNewJadwal(this)" class="text-red-500 hover:text-red-700 p-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;

            jadwalContainer.appendChild(jadwalItem);

            // Add to pending changes
            pendingChanges.push({
                action: 'create',
                karyawan_id: karyawanId,
                shift_id: shiftId,
                date: date,
                element: jadwalItem
            });

            updatePendingIndicator();
        }

        // Function to get color scheme for each shift
        function getShiftColor(shiftId) {
            const shift = shiftData.find(s => s.shift_id === shiftId);
            if (!shift) return getDefaultShiftColor();

            // Color schemes for different shifts
            const colorSchemes = [{
                    // Blue - Shift Pagi
                    bg: 'bg-blue-100',
                    border: 'border-blue-200',
                    hover: 'bg-blue-200',
                    text: 'text-blue-900',
                    textSecondary: 'text-blue-700',
                    textTertiary: 'text-blue-600'
                },
                {
                    // Green - Shift Siang
                    bg: 'bg-green-100',
                    border: 'border-green-200',
                    hover: 'bg-green-200',
                    text: 'text-green-900',
                    textSecondary: 'text-green-700',
                    textTertiary: 'text-green-600'
                },
                {
                    // Purple - Shift Malam
                    bg: 'bg-purple-100',
                    border: 'border-purple-200',
                    hover: 'bg-purple-200',
                    text: 'text-purple-900',
                    textSecondary: 'text-purple-700',
                    textTertiary: 'text-purple-600'
                },
                {
                    // Orange - Shift Sore
                    bg: 'bg-orange-100',
                    border: 'border-orange-200',
                    hover: 'bg-orange-200',
                    text: 'text-orange-900',
                    textSecondary: 'text-orange-700',
                    textTertiary: 'text-orange-600'
                },
                {
                    // Pink - Shift Weekend
                    bg: 'bg-pink-100',
                    border: 'border-pink-200',
                    hover: 'bg-pink-200',
                    text: 'text-pink-900',
                    textSecondary: 'text-pink-700',
                    textTertiary: 'text-pink-600'
                },
                {
                    // Indigo - Shift Special
                    bg: 'bg-indigo-100',
                    border: 'border-indigo-200',
                    hover: 'bg-indigo-200',
                    text: 'text-indigo-900',
                    textSecondary: 'text-indigo-700',
                    textTertiary: 'text-indigo-600'
                },
                {
                    // Teal - Shift Flexible
                    bg: 'bg-teal-100',
                    border: 'border-teal-200',
                    hover: 'bg-teal-200',
                    text: 'text-teal-900',
                    textSecondary: 'text-teal-700',
                    textTertiary: 'text-teal-600'
                },
                {
                    // Red - Shift Emergency
                    bg: 'bg-red-100',
                    border: 'border-red-200',
                    hover: 'bg-red-200',
                    text: 'text-red-900',
                    textSecondary: 'text-red-700',
                    textTertiary: 'text-red-600'
                }
            ];

            // Get shift index to determine color
            const shiftIndex = shiftData.findIndex(s => s.shift_id === shiftId);
            const colorIndex = shiftIndex % colorSchemes.length;

            return colorSchemes[colorIndex];
        }

        function getDefaultShiftColor() {
            return {
                bg: 'bg-gray-100',
                border: 'border-gray-200',
                hover: 'bg-gray-200',
                text: 'text-gray-900',
                textSecondary: 'text-gray-700',
                textTertiary: 'text-gray-600'
            };
        }

        function removeNewJadwal(button) {
            const jadwalItem = button.closest('.jadwal-item');
            const karyawanId = jadwalItem.dataset.karyawanId;
            const date = jadwalItem.dataset.date;

            // Remove from pending changes
            pendingChanges = pendingChanges.filter(change =>
                !(change.action === 'create' && change.karyawan_id === karyawanId && change.date === date)
            );

            jadwalItem.remove();
            updatePendingIndicator();
        }

        function selectShift(shiftId) {
            selectedShiftId = shiftId;
            selectedShiftData = shiftData.find(s => s.shift_id === shiftId);

            // Update UI
            document.querySelectorAll('.shift-template').forEach(template => {
                template.classList.remove('bg-green-200', 'border-green-400');
                template.classList.add('bg-gray-50', 'border-gray-200');
            });

            const selectedTemplate = document.querySelector(`[data-shift-id="${shiftId}"]`);
            selectedTemplate.classList.remove('bg-gray-50', 'border-gray-200');
            selectedTemplate.classList.add('bg-green-200', 'border-green-400');

            // Show selected shift indicator
            document.getElementById('selected-shift-indicator').classList.remove('hidden');
            document.getElementById('selected-shift-name').textContent = selectedShiftData.name;
            document.getElementById('selected-shift-time').textContent =
                `${formatTime(selectedShiftData.start_time)} - ${formatTime(selectedShiftData.end_time)}`;
        }

        function clearSelectedShift() {
            selectedShiftId = null;
            selectedShiftData = null;

            document.querySelectorAll('.shift-template').forEach(template => {
                template.classList.remove('bg-green-200', 'border-green-400');
                template.classList.add('bg-gray-50', 'border-gray-200');
            });

            document.getElementById('selected-shift-indicator').classList.add('hidden');
        }

        function initializeSearch() {
            const searchInput = document.getElementById('karyawan-search');
            const departmentFilter = document.getElementById('department-filter');

            function filterKaryawan() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDepartment = departmentFilter.value;

                document.querySelectorAll('.karyawan-item').forEach(item => {
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
            // Edit jadwal form submission
            document.getElementById('edit-jadwal-form').addEventListener('submit', function(e) {
                e.preventDefault();
                updateJadwal();
            });

            // Month/year selector changes
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
            // Find jadwal data from calendar data
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

            // Check if editable
            if (!jadwalData.is_editable) {
                alert('Jadwal tidak dapat diedit karena karyawan sudah melakukan absensi');
                return;
            }

            currentEditingJadwal = jadwalId;

            // Populate modal
            document.getElementById('edit-jadwal-id').value = jadwalId;
            document.getElementById('edit-karyawan-name').value = jadwalData.karyawan_name;
            document.getElementById('edit-date').value = formatDate(getJadwalDate(jadwalId));
            document.getElementById('edit-shift-select').value = jadwalData.shift_id;
            document.getElementById('edit-notes').value = '';

            // Show modal
            document.getElementById('edit-jadwal-modal').classList.remove('hidden');
        }

        function closeEditJadwalModal() {
            document.getElementById('edit-jadwal-modal').classList.add('hidden');
            currentEditingJadwal = null;
        }

        function updateJadwal() {
            if (!currentEditingJadwal) return;

            const shiftId = document.getElementById('edit-shift-select').value;
            const notes = document.getElementById('edit-notes').value;

            fetch(`{{ route('admin.jadwal.index') }}/${currentEditingJadwal}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        shift_id: shiftId,
                        notes: notes
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeEditJadwalModal();
                        showNotification('Jadwal berhasil diperbarui', 'success');
                        // Refresh the specific jadwal item instead of full reload
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Terjadi kesalahan saat menyimpan', 'error');
                });
        }

        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotification = document.getElementById('notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create notification element
            const notification = document.createElement('div');
            notification.id = 'notification';
            notification.className =
                `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg border max-w-sm transition-all duration-300 transform translate-x-full`;

            // Set colors based on type
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
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>' :
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex text-current hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }

        function deleteCurrentJadwal() {
            if (!currentEditingJadwal) return;

            if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
                deleteJadwalItem(currentEditingJadwal);
            }
        }

        function deleteJadwalItem(jadwalId) {
            fetch(`{{ route('admin.jadwal.index') }}/${jadwalId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove the jadwal item from DOM immediately
                        const jadwalElement = document.querySelector(`[data-jadwal-id="${jadwalId}"]`);
                        if (jadwalElement) {
                            jadwalElement.style.animation = 'fadeOut 0.3s ease-out';
                            setTimeout(() => jadwalElement.remove(), 300);
                        }

                        // Close modal if open
                        closeEditJadwalModal();

                        // Show success message
                        showNotification('Jadwal berhasil dihapus', 'success');
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'Terjadi kesalahan saat menghapus', 'error');
                });
        }

        function saveAllChanges() {
            if (pendingChanges.length === 0) {
                alert('Tidak ada perubahan untuk disimpan');
                return;
            }

            const jadwalsToCreate = pendingChanges.filter(change => change.action === 'create');

            if (jadwalsToCreate.length === 0) {
                alert('Tidak ada jadwal baru untuk disimpan');
                return;
            }

            // Prepare data for bulk creation
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
                        alert(`${data.data.length} jadwal berhasil disimpan`);
                        if (data.errors && data.errors.length > 0) {
                            alert('Beberapa jadwal gagal disimpan:\n' + data.errors.join('\n'));
                        }
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

        function updatePendingIndicator() {
            const count = pendingChanges.length;
            const saveButton = document.querySelector('[onclick="saveAllChanges()"]');

            if (count > 0) {
                saveButton.classList.add('animate-pulse');
                saveButton.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan (${count})
        `;
            } else {
                saveButton.classList.remove('animate-pulse');
                saveButton.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Semua
        `;
            }
        }

        // Fungsi debug khusus untuk menganalisis hari Jumat yang kosong
        function debugFridayIssue() {
            console.log('=== DEBUG FRIDAY ISSUE ===');
            console.log('Current Month:', currentMonth, 'Year:', currentYear);

            const year = currentYear;
            const month = currentMonth;
            const daysInMonth = new Date(year, month, 0).getDate();

            console.log('\n=== ANALISIS SEMUA HARI JUMAT ===');

            // Loop semua hari di bulan ini dan cari yang hari Jumat
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const dateStr = date.toISOString().split('T')[0];
                const dayOfWeek = date.getDay();
                const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                // Fokus pada hari Jumat (dayOfWeek === 5)
                if (dayOfWeek === 5) {
                    console.log(`\n--- JUMAT TANGGAL ${day} ---`);
                    console.log(`Date string: ${dateStr}`);
                    console.log(`Day of week: ${dayOfWeek} (${dayNames[dayOfWeek]})`);

                    // Check apakah element calendar ada
                    const calendarElement = document.querySelector(`[data-date="${dateStr}"]`);
                    console.log(`Calendar element exists: ${calendarElement ? 'YES' : 'NO'}`);

                    if (calendarElement) {
                        // Check apakah ada jadwal existing
                        const existingJadwals = calendarElement.querySelectorAll('.jadwal-item');
                        console.log(`Existing jadwals: ${existingJadwals.length}`);

                        if (existingJadwals.length > 0) {
                            existingJadwals.forEach((jadwal, index) => {
                                const karyawanId = jadwal.dataset.karyawanId;
                                const shiftId = jadwal.dataset.shiftId;
                                console.log(`  Jadwal ${index + 1}: Karyawan ${karyawanId}, Shift ${shiftId}`);
                            });
                        }

                        // Check parent container
                        const jadwalContainer = calendarElement.querySelector('.jadwal-container');
                        console.log(`Jadwal container exists: ${jadwalContainer ? 'YES' : 'NO'}`);

                        // Check apakah element ini untuk current month
                        const elementDate = new Date(dateStr);
                        const isCurrentMonth = elementDate.getMonth() === (month - 1) && elementDate.getFullYear() === year;
                        console.log(`Is current month: ${isCurrentMonth}`);

                        // Check class pada calendar day
                        console.log(`Calendar day classes: ${calendarElement.className}`);

                        // Simulate auto-generate untuk hari ini
                        console.log('\n--- SIMULATION AUTO-GENERATE ---');
                        const shouldGenerateWeekdays = dayOfWeek >= 1 && dayOfWeek <= 6;
                        console.log(`Should generate for weekdays: ${shouldGenerateWeekdays}`);

                        // Test dengan karyawan yang sedang dipilih
                        const selectedKaryawanId = document.getElementById('auto-karyawan-select').value;
                        if (selectedKaryawanId) {
                            const existingForSelectedKaryawan = calendarElement.querySelector(
                                `[data-karyawan-id="${selectedKaryawanId}"]`);
                            console.log(`Selected karyawan ID: ${selectedKaryawanId}`);
                            console.log(
                                `Already has jadwal for selected karyawan: ${existingForSelectedKaryawan ? 'YES' : 'NO'}`
                            );
                        }
                    }
                }
            }

            console.log('\n=== ANALISIS KALENDER GRID ===');

            // Check semua element di kalender
            const allCalendarDays = document.querySelectorAll('.calendar-day[data-date]');
            console.log(`Total calendar elements: ${allCalendarDays.length}`);

            let fridayElements = 0;
            let fridayInCurrentMonth = 0;

            allCalendarDays.forEach(element => {
                const elementDate = element.dataset.date;
                const date = new Date(elementDate);
                const dayOfWeek = date.getDay();

                if (dayOfWeek === 5) { // Jumat
                    fridayElements++;
                    const isCurrentMonth = date.getMonth() === (month - 1) && date.getFullYear() === year;
                    if (isCurrentMonth) {
                        fridayInCurrentMonth++;
                        console.log(`Friday in current month: ${elementDate}`);
                    } else {
                        console.log(`Friday in other month: ${elementDate}`);
                    }
                }
            });

            console.log(`Total Friday elements in calendar: ${fridayElements}`);
            console.log(`Friday elements in current month: ${fridayInCurrentMonth}`);
        }

        // Fungsi untuk test auto-generate hanya pada hari Jumat
        function testGenerateOnlyFridays() {
            if (!selectedShiftId) {
                alert('Pilih shift terlebih dahulu');
                return;
            }

            const karyawanId = document.getElementById('auto-karyawan-select').value;
            if (!karyawanId) {
                alert('Pilih karyawan terlebih dahulu');
                return;
            }

            const karyawan = karyawanData.find(k => k.karyawan_id === karyawanId);
            const shift = shiftData.find(s => s.shift_id === selectedShiftId);

            if (!karyawan || !shift) {
                alert('Data karyawan atau shift tidak valid');
                return;
            }

            console.log('=== TEST GENERATE ONLY FRIDAYS ===');

            const year = currentYear;
            const month = currentMonth;
            const daysInMonth = new Date(year, month, 0).getDate();
            const generatedDates = [];

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const dateStr = date.toISOString().split('T')[0];
                const dayOfWeek = date.getDay();

                // Hanya proses hari Jumat
                if (dayOfWeek === 5) {
                    console.log(`Processing Friday: ${dateStr}`);

                    const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
                    if (dayElement) {
                        const elementDate = new Date(dateStr);
                        const isCurrentMonth = elementDate.getMonth() === (month - 1) && elementDate.getFullYear() === year;

                        if (isCurrentMonth) {
                            const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${karyawanId}"]`);
                            if (!existingJadwal) {
                                console.log(`Creating jadwal for ${dateStr}`);
                                createJadwalItem(dateStr, karyawanId, selectedShiftId, dayElement);
                                generatedDates.push(dateStr);
                            } else {
                                console.log(`Jadwal already exists for ${dateStr}`);
                            }
                        } else {
                            console.log(`${dateStr} is not in current month`);
                        }
                    } else {
                        console.log(`Element not found for ${dateStr}`);
                    }
                }
            }

            console.log('Generated Friday dates:', generatedDates);

            if (generatedDates.length > 0) {
                showNotification(`${generatedDates.length} jadwal Jumat berhasil digenerate`, 'success');
                updatePendingIndicator();
            } else {
                showNotification('Tidak ada jadwal Jumat yang digenerate', 'info');
            }
        }

        // Fungsi untuk menampilkan analisis hari kerja yang detail
        function analyzeWorkingDaysPattern() {
            console.log('=== ANALISIS POLA HARI KERJA ===');

            const year = currentYear;
            const month = currentMonth;
            const daysInMonth = new Date(year, month, 0).getDate();

            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const weekdayCount = [0, 0, 0, 0, 0, 0, 0]; // Index 0=Minggu, 1=Senin, dst

            console.log(`\nBulan ${month}/${year} - Total hari: ${daysInMonth}`);
            console.log('\nDetail per hari:');

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const dayOfWeek = date.getDay();
                weekdayCount[dayOfWeek]++;

                const isWorkingDay = dayOfWeek >= 1 && dayOfWeek <= 6; // Senin-Sabtu
                console.log(
                    `${day.toString().padStart(2, '0')}: ${dayNames[dayOfWeek]} ${isWorkingDay ? '(KERJA)' : '(LIBUR)'}`
                );
            }

            console.log('\n=== RINGKASAN ===');
            dayNames.forEach((name, index) => {
                const isWorkingDay = index >= 1 && index <= 6;
                console.log(`${name}: ${weekdayCount[index]} hari ${isWorkingDay ? '(KERJA)' : '(LIBUR)'}`);
            });

            const totalWorkingDays = weekdayCount.slice(1, 7).reduce((a, b) => a + b, 0);
            console.log(`\nTotal hari kerja (Senin-Sabtu): ${totalWorkingDays}`);
            console.log(`Total hari libur (Minggu): ${weekdayCount[0]}`);
        }

        // Tambahkan tombol debug untuk development
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
                const debugContainer = document.querySelector('.space-y-2') || document.body;

                // Debug Friday button
                const debugFridayBtn = document.createElement('button');
                debugFridayBtn.innerHTML = 'Debug Friday';
                debugFridayBtn.onclick = debugFridayIssue;
                debugFridayBtn.className = 'text-xs px-2 py-1 bg-red-600 text-white rounded';
                debugContainer.appendChild(debugFridayBtn);

                // Test Friday generate button
                const testFridayBtn = document.createElement('button');
                testFridayBtn.innerHTML = 'Test Friday';
                testFridayBtn.onclick = testGenerateOnlyFridays;
                testFridayBtn.className = 'text-xs px-2 py-1 bg-orange-600 text-white rounded';
                debugContainer.appendChild(testFridayBtn);

                // Analyze pattern button
                const analyzeBtn = document.createElement('button');
                analyzeBtn.innerHTML = 'Analyze Pattern';
                analyzeBtn.onclick = analyzeWorkingDaysPattern;
                analyzeBtn.className = 'text-xs px-2 py-1 bg-purple-600 text-white rounded';
                debugContainer.appendChild(analyzeBtn);
            }
        });



        // Debug function untuk mengidentifikasi masalah date creation
        function debugDateCreationIssue() {
            console.log('=== DEBUG DATE CREATION ISSUE ===');

            // Test dengan nilai yang sama seperti di log Anda
            const month = 8; // Agustus
            const year = 2025;

            console.log('Input values:');
            console.log('month:', month);
            console.log('year:', year);
            console.log('month - 1:', month - 1);

            // Test date creation untuk beberapa hari pertama
            for (let day = 1; day <= 5; day++) {
                const date = new Date(year, month - 1, day);
                const dateStr = date.toISOString().split('T')[0];

                console.log(`new Date(${year}, ${month - 1}, ${day}) = ${dateStr}`);

                // Verify components
                console.log(`  getFullYear(): ${date.getFullYear()}`);
                console.log(`  getMonth(): ${date.getMonth()} (should be ${month - 1})`);
                console.log(`  getDate(): ${date.getDate()}`);
                console.log('');
            }

            // Test alternative date creation methods
            console.log('=== ALTERNATIVE DATE CREATION ===');

            // Method 1: String construction
            const dateStr1 = `${year}-${month.toString().padStart(2, '0')}-01`;
            const date1 = new Date(dateStr1);
            console.log(`String method: "${dateStr1}" -> ${date1.toISOString().split('T')[0]}`);

            // Method 2: setFullYear method
            const date2 = new Date();
            date2.setFullYear(year, month - 1, 1);
            console.log(`setFullYear method: -> ${date2.toISOString().split('T')[0]}`);

            // Check timezone
            console.log('Timezone offset:', new Date().getTimezoneOffset());
            console.log('Current timezone:', Intl.DateTimeFormat().resolvedOptions().timeZone);
        }

        // Fixed autoGenerateMonth dengan date creation yang lebih robust
        function autoGenerateMonth(type) {
            if (!selectedShiftId) {
                alert('Pilih shift terlebih dahulu');
                return;
            }

            const karyawanId = document.getElementById('auto-karyawan-select').value;
            if (!karyawanId) {
                alert('Pilih karyawan terlebih dahulu');
                return;
            }

            const karyawan = karyawanData.find(k => k.karyawan_id === karyawanId);
            const shift = shiftData.find(s => s.shift_id === selectedShiftId);

            if (!karyawan || !shift) {
                alert('Data karyawan atau shift tidak valid');
                return;
            }

            console.log('=== FIXED AUTO GENERATE ===');
            console.log('Current Month:', currentMonth, 'Year:', currentYear);

            const year = currentYear;
            const month = currentMonth;

            // PERBAIKAN: Gunakan method yang lebih robust untuk date creation
            const firstDay = new Date();
            firstDay.setFullYear(year, month - 1, 1);
            firstDay.setHours(12, 0, 0, 0); // Set to noon to avoid timezone issues

            const lastDay = new Date();
            lastDay.setFullYear(year, month, 0); // Last day of month
            lastDay.setHours(12, 0, 0, 0);

            const daysInMonth = lastDay.getDate();

            console.log('Date range validation:');
            console.log(`First day: ${firstDay.toISOString().split('T')[0]}`);
            console.log(`Last day: ${lastDay.toISOString().split('T')[0]}`);
            console.log(`Days in month: ${daysInMonth}`);

            const generatedDates = [];
            const skippedDates = [];
            const errorDates = [];

            // PERBAIKAN: Loop dengan date creation yang lebih aman
            for (let day = 1; day <= daysInMonth; day++) {
                try {
                    // Method 1: String construction (paling reliable)
                    const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    const date = new Date(dateStr + 'T12:00:00'); // Add time to avoid timezone issues

                    // Fallback method jika string construction gagal
                    if (isNaN(date.getTime())) {
                        const fallbackDate = new Date();
                        fallbackDate.setFullYear(year, month - 1, day);
                        fallbackDate.setHours(12, 0, 0, 0);
                        date = fallbackDate;
                    }

                    const finalDateStr = date.toISOString().split('T')[0];
                    const dayOfWeek = date.getDay();

                    // Debug untuk 5 hari pertama
                    if (day <= 5) {
                        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        console.log(`Day ${day}: ${finalDateStr} (${dayNames[dayOfWeek]}) - dayOfWeek: ${dayOfWeek}`);
                    }

                    // Validate date is correct
                    if (date.getFullYear() !== year || date.getMonth() !== (month - 1) || date.getDate() !== day) {
                        errorDates.push({
                            date: finalDateStr,
                            reason: 'date validation failed'
                        });
                        console.error(`Date validation failed for day ${day}`);
                        continue;
                    }

                    let shouldGenerate = false;

                    switch (type) {
                        case 'all':
                            shouldGenerate = true;
                            break;
                        case 'weekdays':
                            // Hari kerja Senin-Sabtu (6 hari kerja)
                            shouldGenerate = dayOfWeek >= 1 && dayOfWeek <= 6;
                            break;
                        case 'weekdays-5':
                            // Hari kerja Senin-Jumat (5 hari kerja)
                            shouldGenerate = dayOfWeek >= 1 && dayOfWeek <= 5;
                            break;
                        case 'weekends':
                            // Weekend: Hanya Minggu untuk sistem 6 hari kerja
                            shouldGenerate = dayOfWeek === 0;
                            break;
                        case 'custom':
                            shouldGenerate = dayOfWeek >= 1 && dayOfWeek <= 6;
                            break;
                    }

                    if (shouldGenerate) {
                        const dayElement = document.querySelector(`[data-date="${finalDateStr}"]`);
                        if (dayElement) {
                            // Check if karyawan already has jadwal on this date
                            const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${karyawanId}"]`);
                            if (!existingJadwal) {
                                createJadwalItem(finalDateStr, karyawanId, selectedShiftId, dayElement);
                                generatedDates.push(finalDateStr);

                                if (day <= 5) {
                                    console.log(`✅ Generated: ${finalDateStr}`);
                                }
                            } else {
                                skippedDates.push({
                                    date: finalDateStr,
                                    reason: 'existing jadwal'
                                });
                                if (day <= 5) {
                                    console.log(`⚠️ Skipped ${finalDateStr} - existing jadwal`);
                                }
                            }
                        } else {
                            errorDates.push({
                                date: finalDateStr,
                                reason: 'element not found'
                            });
                            if (day <= 5) {
                                console.log(`❌ Element not found for ${finalDateStr}`);
                            }
                        }
                    } else {
                        if (day <= 5) {
                            console.log(`⏭️ Not a workday: ${finalDateStr}`);
                        }
                    }
                } catch (error) {
                    errorDates.push({
                        date: `day-${day}`,
                        reason: error.message
                    });
                    console.error(`Error processing day ${day}:`, error);
                }
            }

            // Debug hasil detail
            console.log('\n=== HASIL GENERATE ===');
            console.log('Generated dates:', generatedDates);
            console.log('Total generated:', generatedDates.length);
            console.log('Total skipped:', skippedDates.length);
            console.log('Total errors:', errorDates.length);

            if (generatedDates.length > 0) {
                showNotification(`${generatedDates.length} jadwal berhasil digenerate untuk ${karyawan.name} (${type})`,
                    'success');
                updatePendingIndicator();
            } else {
                showNotification('Tidak ada jadwal baru yang digenerate', 'info');
            }
        }

        // Test function untuk memverifikasi date creation
        function testDateCreation() {
            console.log('=== TEST DATE CREATION METHODS ===');

            const year = 2025;
            const month = 8; // Agustus

            console.log(`Testing for ${month}/${year}:`);

            // Test different methods for creating August 1, 2025
            console.log('\nMethod 1: new Date(year, month-1, day)');
            const method1 = new Date(year, month - 1, 1);
            console.log(`Result: ${method1.toISOString().split('T')[0]}`);

            console.log('\nMethod 2: String construction');
            const dateStr = `${year}-${month.toString().padStart(2, '0')}-01`;
            const method2 = new Date(dateStr);
            console.log(`Result: ${method2.toISOString().split('T')[0]}`);

            console.log('\nMethod 3: String with time');
            const method3 = new Date(dateStr + 'T12:00:00');
            console.log(`Result: ${method3.toISOString().split('T')[0]}`);

            console.log('\nMethod 4: setFullYear');
            const method4 = new Date();
            method4.setFullYear(year, month - 1, 1);
            method4.setHours(12, 0, 0, 0);
            console.log(`Result: ${method4.toISOString().split('T')[0]}`);

            // Test semua hari pertama
            console.log('\nTesting first 5 days with Method 2 (string):');
            for (let day = 1; day <= 5; day++) {
                const testDateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const testDate = new Date(testDateStr + 'T12:00:00');
                console.log(`Day ${day}: ${testDate.toISOString().split('T')[0]}`);
            }
        }

        // Add debug buttons
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
                const debugContainer = document.querySelector('.space-y-2') || document.body;

                // Debug date creation button
                const debugDateBtn = document.createElement('button');
                debugDateBtn.innerHTML = 'Debug Dates';
                debugDateBtn.onclick = debugDateCreationIssue;
                debugDateBtn.className = 'text-xs px-2 py-1 bg-red-600 text-white rounded';
                debugContainer.appendChild(debugDateBtn);

                // Test date creation button
                const testDateBtn = document.createElement('button');
                testDateBtn.innerHTML = 'Test Dates';
                testDateBtn.onclick = testDateCreation;
                testDateBtn.className = 'text-xs px-2 py-1 bg-yellow-600 text-white rounded';
                debugContainer.appendChild(testDateBtn);

                // Fixed generate button
                const fixedGenBtn = document.createElement('button');
                fixedGenBtn.innerHTML = 'Fixed Generate';
                fixedGenBtn.onclick = () => autoGenerateMonthFixed('weekdays');
                fixedGenBtn.className = 'text-xs px-2 py-1 bg-green-600 text-white rounded';
                debugContainer.appendChild(fixedGenBtn);
            }
        });
        // Enhanced version with custom day selection modal

        function showCustomDayModal() {
            const modal = document.createElement('div');
            modal.id = 'custom-day-modal';
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full';

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
                        <label class="flex items-center">
                            <input type="checkbox" value="1" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Senin</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="2" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Selasa</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="3" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Rabu</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="4" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Kamis</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="5" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Jumat</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="6" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Sabtu</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="0" class="custom-day-checkbox rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm">Minggu</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button onclick="closeCustomDayModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </button>
                    <button onclick="generateCustomDays()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Generate
                    </button>
                </div>
            </div>
        </div>
    `;

            document.body.appendChild(modal);
        }

        function closeCustomDayModal() {
            const modal = document.getElementById('custom-day-modal');
            if (modal) {
                modal.remove();
            }
        }

        function generateCustomDays() {
            const selectedDays = Array.from(document.querySelectorAll('.custom-day-checkbox:checked')).map(cb => parseInt(cb
                .value));

            if (selectedDays.length === 0) {
                alert('Pilih minimal satu hari');
                return;
            }

            const karyawanId = document.getElementById('auto-karyawan-select').value;
            if (!karyawanId || !selectedShiftId) {
                alert('Pilih karyawan dan shift terlebih dahulu');
                return;
            }

            const karyawan = karyawanData.find(k => k.karyawan_id === karyawanId);
            const shift = shiftData.find(s => s.shift_id === selectedShiftId);

            // Generate for selected days
            const year = currentYear;
            const month = currentMonth;
            const daysInMonth = new Date(year, month, 0).getDate();
            const generatedDates = [];

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                const dateStr = date.toISOString().split('T')[0];
                const dayOfWeek = date.getDay();

                if (selectedDays.includes(dayOfWeek)) {
                    const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
                    if (dayElement) {
                        const existingJadwal = dayElement.querySelector(`[data-karyawan-id="${karyawanId}"]`);
                        if (!existingJadwal) {
                            createJadwalItem(dateStr, karyawanId, selectedShiftId, dayElement);
                            generatedDates.push(dateStr);
                        }
                    }
                }
            }

            closeCustomDayModal();

            if (generatedDates.length > 0) {
                showNotification(`${generatedDates.length} jadwal berhasil digenerate untuk ${karyawan.full_name}`,
                    'success');
                updatePendingIndicator();
            } else {
                showNotification('Tidak ada jadwal baru yang digenerate', 'info');
            }
        }

        // Helper functions
        function formatTime(timeString) {
            return new Date('2000-01-01 ' + timeString).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
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
                const dayJadwals = calendarData[date];
                if (dayJadwals.some(j => j.jadwal_id === jadwalId)) {
                    return date;
                }
            }
            return null;
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape to clear selection
            if (e.key === 'Escape') {
                clearSelectedShift();
                closeEditJadwalModal();
            }

            // Ctrl+S to save all changes
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAllChanges();
            }
        });

        // Auto-save on page unload
        window.addEventListener('beforeunload', function(e) {
            if (pendingChanges.length > 0) {
                e.preventDefault();
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Notification styles */
        #notification {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        /* Drag and drop styles */
        .karyawan-item {
            transition: all 0.2s ease;
        }

        .karyawan-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .karyawan-item.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }

        /* Calendar styles */
        .calendar-day {
            transition: all 0.2s ease;
        }

        .calendar-day.drag-over {
            background-color: #dbeafe !important;
            border-color: #3b82f6 !important;
            transform: scale(1.02);
        }

        .jadwal-item {
            transition: all 0.2s ease;
        }

        .jadwal-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .new-jadwal {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Shift selection styles */
        .shift-template {
            transition: all 0.2s ease;
        }

        .shift-template:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .shift-template.selected {
            background-color: #dcfce7 !important;
            border-color: #16a34a !important;
        }

        /* Selected shift indicator */
        #selected-shift-indicator {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(100%);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Calendar grid responsive */
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 80px;
            }

            .jadwal-item {
                padding: 4px;
            }

            .jadwal-item p {
                font-size: 10px;
            }
        }

        /* Custom scrollbar */
        .max-h-96::-webkit-scrollbar {
            width: 6px;
        }

        .max-h-96::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .max-h-96::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .max-h-96::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Modal backdrop */
        #edit-jadwal-modal {
            backdrop-filter: blur(4px);
        }

        /* Drop zone animation */
        .drop-zone-indicator {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }
        }

        /* Loading state */
        .loading {
            pointer-events: none;
            opacity: 0.6;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .calendar-day {
                border: 1px solid #000 !important;
            }

            .jadwal-item {
                background-color: #f0f0f0 !important;
                border: 1px solid #ccc !important;
            }
        }

        /* Accessibility improvements */
        .karyawan-item:focus,
        .shift-template:focus,
        .jadwal-item:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Today indicator pulse */
        .bg-blue-600 {
            animation: todayPulse 2s infinite;
        }

        @keyframes todayPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
        }
    </style>
@endpush
