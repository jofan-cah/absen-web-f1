@extends('admin.layouts.app')

@section('title', 'Data Shift')
@section('breadcrumb', 'Data Shift')
@section('page_title', 'Data Shift Kerja')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <input type="text" id="search" placeholder="Cari shift..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Filter Status -->
        <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
        </select>

        <!-- Filter Type -->
        <select id="filter-type" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Tipe</option>
            <option value="normal">Normal</option>
            <option value="overnight">Shift Malam</option>
        </select>
    </div>

    <!-- Actions -->
    <div class="flex gap-2">
        <button onclick="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export
        </button>
        <a href="{{ route('admin.shift.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Shift
        </a>
    </div>
</div>
@endsection

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Shift</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $shifts->total() ?? 0 }}</p>
                <p class="text-sm text-blue-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Shift kerja
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Shift Aktif</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $shifts->where('is_active', true)->count() ?? 0 }}</p>
                <p class="text-sm text-green-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sedang beroperasi
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Shift Malam</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $shifts->where('is_overnight', true)->count() ?? 0 }}</p>
                <p class="text-sm text-purple-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    Shift overnight
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Jadwal</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $shifts->sum('total_jadwal') ?? 0 }}</p>
                <p class="text-sm text-orange-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Menggunakan shift
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Shift Kerja</h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Total: <span id="total-records">{{ $shifts->total() ?? 0 }}</span> data</span>

                <!-- View Toggle -->
                <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                    <button onclick="setView('table')" id="table-view-btn" class="px-3 py-1 text-sm bg-primary-600 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </button>
                    <button onclick="setView('card')" id="card-view-btn" class="px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
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
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Istirahat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toleransi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="shift-table-body">
                @forelse($shifts ?? [] as $shift)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="selected_shifts[]" value="{{ $shift->shift_id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br {{ $shift->is_overnight ? 'from-purple-500 to-indigo-600' : 'from-blue-500 to-cyan-600' }} flex items-center justify-center">
                                    @if($shift->is_overnight)
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $shift->name }}</div>
                                <div class="text-sm text-gray-500 font-mono">{{ $shift->code }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        </div>
                        @if($shift->is_overnight)
                            <div class="text-xs text-purple-600">Lintas hari</div>
                        @else
                            @php
                                $start = \Carbon\Carbon::parse($shift->start_time);
                                $end = \Carbon\Carbon::parse($shift->end_time);
                                $duration = $start->diffInHours($end);
                            @endphp
                            <div class="text-xs text-gray-500">{{ $duration }} jam</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($shift->break_start && $shift->break_end)
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($shift->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->break_end)->format('H:i') }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $shift->break_duration }} menit</div>
                        @else
                            <span class="text-gray-400 text-sm">Tidak ada istirahat</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <div class="flex items-center mb-1">
                                <svg class="w-3 h-3 text-yellow-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                                </svg>
                                Telat: {{ $shift->late_tolerance }} mnt
                            </div>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                                </svg>
                                Pulang: {{ $shift->early_checkout_tolerance }} mnt
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($shift->is_overnight)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                Shift Malam
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3"/>
                                </svg>
                                Normal
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($shift->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $shift->jadwals_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500">jadwal</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.shift.show', $shift->shift_id) }}"
                               class="text-primary-600 hover:text-primary-700 p-1 rounded hover:bg-primary-50 transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.shift.edit', $shift->shift_id) }}"
                               class="text-yellow-600 hover:text-yellow-700 p-1 rounded hover:bg-yellow-50 transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button onclick="toggleStatus('{{ $shift->shift_id }}', {{ $shift->is_active ? 'false' : 'true' }})"
                                    class="text-{{ $shift->is_active ? 'red' : 'green' }}-600 hover:text-{{ $shift->is_active ? 'red' : 'green' }}-700 p-1 rounded hover:bg-{{ $shift->is_active ? 'red' : 'green' }}-50 transition-colors"
                                    title="{{ $shift->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                @if($shift->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                            <button onclick="deleteShift('{{ $shift->shift_id }}', '{{ $shift->name }}')"
                                    class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors"
                                    title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data shift</h3>
                            <p class="text-gray-500 mb-4">Mulai dengan menambahkan shift pertama</p>
                            <a href="{{ route('admin.shift.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                Tambah Shift
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Card View (Hidden by default) -->
    <div id="card-view" class="hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="shift-card-container">
            @forelse($shifts ?? [] as $shift)
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br {{ $shift->is_overnight ? 'from-purple-500 to-indigo-600' : 'from-blue-500 to-cyan-600' }} flex items-center justify-center">
                            @if($shift->is_overnight)
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $shift->name }}</h3>
                            <p class="text-sm text-gray-500 font-mono">{{ $shift->code }}</p>
                        </div>
                    </div>
                    @if($shift->is_active)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Jam Kerja:</span>
                        <span class="text-sm font-mono text-gray-900">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Istirahat:</span>
                        @if($shift->break_start && $shift->break_end)
                            <span class="text-sm font-mono text-gray-900">
                                {{ \Carbon\Carbon::parse($shift->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->break_end)->format('H:i') }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400">Tidak ada</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Toleransi Telat:</span>
                        <span class="text-sm text-gray-900">{{ $shift->late_tolerance }} menit</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Tipe:</span>
                        @if($shift->is_overnight)
                            <span class="text-sm font-medium text-purple-600">Shift Malam</span>
                        @else
                            <span class="text-sm font-medium text-blue-600">Normal</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Jadwal:</span>
                        <span class="text-sm font-bold text-gray-900">{{ $shift->jadwals_count ?? 0 }} jadwal</span>
                    </div>
                </div>

                <div class="flex justify-between gap-2">
                    <a href="{{ route('admin.shift.show', $shift->shift_id) }}"
                       class="flex-1 px-3 py-2 text-sm text-primary-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-colors text-center">
                        Detail
                    </a>
                    <a href="{{ route('admin.shift.edit', $shift->shift_id) }}"
                       class="flex-1 px-3 py-2 text-sm text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg transition-colors text-center">
                        Edit
                    </a>
                    <button onclick="deleteShift('{{ $shift->shift_id }}', '{{ $shift->name }}')"
                            class="flex-1 px-3 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data shift</h3>
                <p class="text-gray-500 mb-4">Mulai dengan menambahkan shift pertama</p>
                <a href="{{ route('admin.shift.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Tambah Shift
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($shifts) && $shifts->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $shifts->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions (when items selected) -->
<div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-3 hidden">
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600"><span id="selected-count">0</span> item dipilih</span>
        <div class="flex gap-2">
            <button onclick="bulkToggleStatus()" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Toggle Status
            </button>
            <button onclick="bulkDelete()" class="px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Hapus Terpilih
            </button>
            <button onclick="clearSelection()" class="px-3 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('filter-status');
    const typeFilter = document.getElementById('filter-type');

    let searchTimeout;

    function performSearch() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;
        const type = typeFilter.value;

        // Build URL with parameters
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (status !== '') params.append('status', status);
        if (type !== '') params.append('type', type);

        // Redirect with filters
        window.location.href = `{{ route('admin.shift.index') }}?${params.toString()}`;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    statusFilter.addEventListener('change', performSearch);
    typeFilter.addEventListener('change', performSearch);

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_shifts[]"]');
    const bulkActionsDiv = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('input[name="selected_shifts[]"]:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActionsDiv.classList.remove('hidden');
            selectedCountSpan.textContent = count;
        } else {
            bulkActionsDiv.classList.add('hidden');
        }

        // Update select all checkbox state
        if (count === individualCheckboxes.length && count > 0) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (count > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            individualCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox functionality
    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Initialize
    updateBulkActions();
});

// View toggle functionality
let currentView = 'table';

function setView(view) {
    currentView = view;
    const tableView = document.getElementById('table-view');
    const cardView = document.getElementById('card-view');
    const tableBtn = document.getElementById('table-view-btn');
    const cardBtn = document.getElementById('card-view-btn');

    if (view === 'table') {
        tableView.classList.remove('hidden');
        cardView.classList.add('hidden');
        tableBtn.classList.remove('bg-gray-100', 'text-gray-600');
        tableBtn.classList.add('bg-primary-600', 'text-white');
        cardBtn.classList.remove('bg-primary-600', 'text-white');
        cardBtn.classList.add('bg-gray-100', 'text-gray-600');
    } else {
        tableView.classList.add('hidden');
        cardView.classList.remove('hidden');
        cardBtn.classList.remove('bg-gray-100', 'text-gray-600');
        cardBtn.classList.add('bg-primary-600', 'text-white');
        tableBtn.classList.remove('bg-primary-600', 'text-white');
        tableBtn.classList.add('bg-gray-100', 'text-gray-600');
    }

    // Save preference to localStorage
    localStorage.setItem('shiftViewPreference', view);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('shiftViewPreference');
    if (savedView && savedView !== 'table') {
        setView(savedView);
    }
});

// Delete functionality
function deleteShift(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus shift "${name}"?\n\nPerhatian: Shift yang masih digunakan dalam jadwal tidak dapat dihapus.`)) {
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

// Bulk actions (functions need to be implemented in ShiftController)
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_shifts[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih shift yang ingin dihapus');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus ${count} shift terpilih?\n\nPerhatian: Shift yang masih digunakan dalam jadwal tidak dapat dihapus.`)) {
        showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.shift.bulk-delete") }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(tokenInput);

        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'shift_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function bulkToggleStatus() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_shifts[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih shift yang ingin diubah statusnya');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin mengubah status ${count} shift terpilih?`)) {
        showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.shift.bulk-toggle-status") }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(tokenInput);

        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'shift_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function exportData() {
    showLoading();

    // Get current filters
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("admin.shift.export") }}?' + searchParams.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'data-shift.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Hide loading after a delay since this is a download
    setTimeout(hideLoading, 2000);
}

function clearSelection() {
    document.querySelectorAll('input[name="selected_shifts[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    document.getElementById('bulk-actions').classList.add('hidden');
}
</script>
@endpush
