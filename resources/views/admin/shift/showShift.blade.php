@extends('admin.layouts.app')

@section('title', 'Detail Shift')
@section('breadcrumb', 'Detail Shift')
@section('page_title', 'Detail Shift Kerja')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
    <!-- Quick Actions -->
    <div class="flex gap-2">
        <a href="{{ route('admin.shift.edit', $shift->shift_id) }}"
           class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm font-medium transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Shift
        </a>

        <button onclick="toggleStatus('{{ $shift->shift_id }}', {{ $shift->is_active ? 'false' : 'true' }})"
                class="inline-flex items-center px-4 py-2 bg-{{ $shift->is_active ? 'red' : 'green' }}-600 text-white rounded-lg hover:bg-{{ $shift->is_active ? 'red' : 'green' }}-700 text-sm font-medium transition-colors duration-200">
            @if($shift->is_active)
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                Nonaktifkan
            @else
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Aktifkan
            @endif
        </button>

        <div class="relative">
            <button onclick="toggleDropdown('actions-dropdown')" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
                Aksi
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                </svg>
            </button>
            <div id="actions-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                <div class="py-1">
                    <a href="#" onclick="exportShiftData()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Data
                    </a>
                    <a href="#" onclick="duplicateShift()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplikasi Shift
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="#" onclick="deleteShift('{{ $shift->shift_id }}', '{{ $shift->name }}')" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Shift
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <a href="{{ route('admin.shift.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')

<!-- Flash Messages -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
@endif

<!-- Main Shift Info Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <div class="px-6 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br {{ $shift->is_overnight ? 'from-purple-500 to-indigo-600' : 'from-blue-500 to-cyan-600' }} flex items-center justify-center">
                    @if($shift->is_overnight)
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $shift->name }}</h1>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="text-lg font-mono text-gray-600">{{ $shift->code }}</span>
                        <div class="h-1 w-1 bg-gray-400 rounded-full"></div>
                        <span class="text-sm text-gray-500">ID: {{ $shift->shift_id }}</span>
                        <div class="h-1 w-1 bg-gray-400 rounded-full"></div>
                        <span class="text-sm text-gray-500">Dibuat: {{ $shift->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="text-right">
                @if($shift->is_active)
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        Shift Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        Tidak Aktif
                    </span>
                @endif

                <div class="mt-2">
                    @if($shift->is_overnight)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            Shift Malam
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3"/>
                            </svg>
                            Shift Normal
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Working Hours Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Jam Kerja -->
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">Jam Kerja</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-blue-700">Mulai:</span>
                        <span class="text-lg font-bold font-mono text-blue-900">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-blue-700">Selesai:</span>
                        <span class="text-lg font-bold font-mono text-blue-900">{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</span>
                    </div>
                    <div class="border-t border-blue-200 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-700">Durasi:</span>
                            @php
                                $start = \Carbon\Carbon::parse($shift->start_time);
                                $end = \Carbon\Carbon::parse($shift->end_time);
                                if ($shift->is_overnight && $end <= $start) {
                                    $end->addDay();
                                }
                                $duration = $start->diffInMinutes($end);
                                $hours = floor($duration / 60);
                                $minutes = $duration % 60;
                            @endphp
                            <span class="text-lg font-bold text-blue-900">{{ $hours }}j {{ $minutes }}m</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waktu Istirahat -->
            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-green-900">Waktu Istirahat</h3>
                </div>
                @if($shift->break_start && $shift->break_end)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700">Mulai:</span>
                            <span class="text-lg font-bold font-mono text-green-900">{{ \Carbon\Carbon::parse($shift->break_start)->format('H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700">Selesai:</span>
                            <span class="text-lg font-bold font-mono text-green-900">{{ \Carbon\Carbon::parse($shift->break_end)->format('H:i') }}</span>
                        </div>
                        <div class="border-t border-green-200 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Durasi:</span>
                                <span class="text-lg font-bold text-green-900">{{ $shift->break_duration }} menit</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-20">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <span class="text-sm text-green-600">Tidak ada waktu istirahat</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Toleransi -->
            <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-yellow-900">Toleransi</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-yellow-700">Terlambat:</span>
                        <span class="text-lg font-bold text-yellow-900">{{ $shift->late_tolerance }} menit</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-yellow-700">Pulang Awal:</span>
                        <span class="text-lg font-bold text-yellow-900">{{ $shift->early_checkout_tolerance }} menit</span>
                    </div>
                    <div class="border-t border-yellow-200 pt-3">
                        <div class="text-center">
                            <span class="text-xs text-yellow-600">Toleransi dalam manajemen absensi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics and Usage -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Statistik Penggunaan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistik Penggunaan
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $shift->total_jadwal ?? 0 }}</div>
                    <div class="text-sm text-blue-700 mt-1">Total Jadwal</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">
                        @php
                            $activeSchedules = $shift->jadwals()->where('is_active', true)->count();
                        @endphp
                        {{ $activeSchedules }}
                    </div>
                    <div class="text-sm text-green-700 mt-1">Jadwal Aktif</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $shift->jadwals()->whereDate('date', '>=', now())->count() }}
                    </div>
                    <div class="text-sm text-yellow-700 mt-1">Jadwal Mendatang</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ $shift->jadwals()->distinct('karyawan_id')->count() }}
                    </div>
                    <div class="text-sm text-purple-700 mt-1">Karyawan Terkait</div>
                </div>
            </div>

            @if($shift->total_jadwal > 0)
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Tingkat Penggunaan</span>
                        <span>{{ $activeSchedules }}/{{ $shift->total_jadwal }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $shift->total_jadwal > 0 ? ($activeSchedules / $shift->total_jadwal * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Timeline Informasi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Timeline & Informasi
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Shift Dibuat</div>
                        <div class="text-sm text-gray-500">{{ $shift->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                @if($shift->updated_at != $shift->created_at)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Terakhir Diupdate</div>
                        <div class="text-sm text-gray-500">{{ $shift->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @endif

                @if($shift->total_jadwal > 0)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Pertama Kali Digunakan</div>
                        <div class="text-sm text-gray-500">
                            @php
                                $firstUsage = $shift->jadwals()->orderBy('created_at')->first();
                            @endphp
                            {{ $firstUsage ? $firstUsage->created_at->format('d M Y, H:i') : 'Belum pernah digunakan' }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Additional Info -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Efektif Kerja:</span>
                            <div class="font-medium text-gray-900">
                                @if($shift->break_duration > 0)
                                    {{ $hours }}j {{ $minutes - $shift->break_duration }}m
                                @else
                                    {{ $hours }}j {{ $minutes }}m
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-500">Tipe Jadwal:</span>
                            <div class="font-medium text-gray-900">
                                {{ $shift->is_overnight ? 'Lintas Hari' : 'Dalam Hari' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Terkait -->
@if($shift->total_jadwal > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwal Menggunakan Shift Ini
            </h3>
            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ $shift->total_jadwal }} jadwal
            </span>
        </div>
    </div>
    <div class="p-6">
        @php
            $recentSchedules = $shift->jadwals()->with('karyawan')->orderByDesc('date')->limit(10)->get();
        @endphp

        @if($recentSchedules->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentSchedules as $jadwal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($jadwal->date)->format('d M Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($jadwal->date)->format('l') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-600">
                                            {{ substr($jadwal->karyawan->name ?? 'N/A', 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $jadwal->karyawan->name ?? 'Karyawan tidak ditemukan' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $jadwal->karyawan->employee_id ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                </div>
                                @if($shift->break_start && $shift->break_end)
                                    <div class="text-xs text-gray-500">
                                        Istirahat: {{ \Carbon\Carbon::parse($shift->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->break_end)->format('H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($jadwal->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($shift->total_jadwal > 10)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.jadwal.index', ['shift' => $shift->shift_id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Lihat Semua Jadwal ({{ $shift->total_jadwal }})
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Jadwal</h3>
                <p class="text-gray-500">Shift ini belum digunakan dalam jadwal apapun</p>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Warning untuk shift yang tidak aktif -->
@if(!$shift->is_active)
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Shift Tidak Aktif</h3>
            <div class="mt-2 text-sm text-red-700">
                <p>Shift ini sedang dalam status tidak aktif dan tidak dapat digunakan untuk penjadwalan baru. Aktifkan shift ini jika ingin menggunakannya kembali.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Action Suggestions -->
<div class="bg-gray-50 rounded-xl p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Aksi yang Tersedia
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('admin.shift.edit', $shift->shift_id) }}" class="group bg-white p-4 rounded-lg border border-gray-200 hover:border-yellow-300 hover:bg-yellow-50 transition-colors">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 group-hover:text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-gray-900">Edit Shift</h4>
                    <p class="text-sm text-gray-500">Ubah pengaturan shift</p>
                </div>
            </div>
        </a>

        <button onclick="duplicateShift()" class="group bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-colors text-left">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-gray-900">Duplikasi</h4>
                    <p class="text-sm text-gray-500">Buat shift serupa</p>
                </div>
            </div>
        </button>


    </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle dropdown
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('actions-dropdown');
    const button = event.target.closest('[onclick="toggleDropdown(\'actions-dropdown\')"]');

    if (!button && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

// Toggle status functionality
function toggleStatus(id, newStatus) {
    const statusText = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';

    if (confirm(`Apakah Anda yakin ingin ${statusText} shift ini?`)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.shift.index') }}/${id}/toggle-status`;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete functionality
function deleteShift(id, name) {
    const jadwalCount = {{ $shift->total_jadwal ?? 0 }};

    let confirmMessage = `Apakah Anda yakin ingin menghapus shift "${name}"?`;

    if (jadwalCount > 0) {
        confirmMessage += `\n\nPerhatian: Shift ini sedang digunakan dalam ${jadwalCount} jadwal. Menghapus shift akan mempengaruhi jadwal yang ada.`;
    }

    if (confirm(confirmMessage)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.shift.index') }}/${id}`;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Export shift data
function exportShiftData() {
    showLoading();

    // Create download link for shift data
    const exportUrl = `{{ route('admin.shift.export') }}?shift_id={{ $shift->shift_id }}`;

    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `shift-${$('#shift-code').text()}-data.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(hideLoading, 2000);
}

// Duplicate shift
function duplicateShift() {
    if (confirm('Duplikasi shift ini akan membuat shift baru dengan pengaturan yang sama. Lanjutkan?')) {
        showLoading();

        // Redirect to create page with prefilled data
        const createUrl = `{{ route('admin.shift.create') }}?duplicate={{ $shift->shift_id }}`;
        window.location.href = createUrl;
    }
}

// Loading functions (should be defined globally in your app)
function showLoading() {
    // Implementation depends on your loading component
    console.log('Loading...');
}

function hideLoading() {
    // Implementation depends on your loading component
    console.log('Loading complete');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // E key to edit
    if (e.key === 'e' && !e.ctrlKey && !e.metaKey && !e.altKey) {
        const activeElement = document.activeElement;
        if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            window.location.href = '{{ route("admin.shift.edit", $shift->shift_id) }}';
        }
    }

    // D key to duplicate
    if (e.key === 'd' && !e.ctrlKey && !e.metaKey && !e.altKey) {
        const activeElement = document.activeElement;
        if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            duplicateShift();
        }
    }

    // Backspace to go back
    if (e.key === 'Backspace' && !e.ctrlKey && !e.metaKey && !e.altKey) {
        const activeElement = document.activeElement;
        if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            window.location.href = '{{ route("admin.shift.index") }}';
        }
    }
});

// Auto-refresh statistics every 30 seconds
setInterval(function() {
    // You can implement AJAX call to refresh statistics
    // without reloading the entire page
}, 30000);
</script>
@endpush

@push('styles')
<style>
/* Smooth transitions for all interactive elements */
.transition-colors {
    transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

/* Hover effects for action cards */
.group:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Status badge animations */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Progress bar animation */
.progress-bar {
    transition: width 1s ease-in-out;
}

/* Table hover effects */
tbody tr:hover {
    background-color: #f9fafb;
    transition: background-color 0.15s ease;
}

/* Dropdown animation */
#actions-dropdown {
    transition: opacity 0.15s ease, transform 0.15s ease;
    transform-origin: top right;
}

#actions-dropdown.hidden {
    opacity: 0;
    transform: scale(0.95);
}

#actions-dropdown:not(.hidden) {
    opacity: 1;
    transform: scale(1);
}

/* Icon color transitions */
svg {
    transition: color 0.2s ease;
}

/* Card border hover effects */
.border-hover:hover {
    border-color: #3b82f6;
}

/* Loading state */
.loading {
    pointer-events: none;
    opacity: 0.6;
}

/* Responsive typography */
@media (max-width: 640px) {
    .text-2xl {
        font-size: 1.5rem;
    }

    .text-lg {
        font-size: 1rem;
    }
}

/* Print styles */
@media print {
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
