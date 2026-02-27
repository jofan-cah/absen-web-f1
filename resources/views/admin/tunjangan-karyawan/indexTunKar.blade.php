@extends('admin.layouts.app')

@section('title', 'Manajemen Tunjangan Karyawan')
@section('breadcrumb', 'Manajemen Tunjangan Karyawan')
@section('page_title', 'Manajemen Tunjangan Karyawan')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <input type="text" id="search" name="search" value="{{ request('search') }}"
                placeholder="Cari karyawan, tunjangan..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Filter Karyawan -->
        <select id="filter-karyawan" name="karyawan_id" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Karyawan</option>
            @foreach($karyawans as $karyawan)
                <option value="{{ $karyawan->karyawan_id }}" {{ request('karyawan_id') == $karyawan->karyawan_id ? 'selected' : '' }}>
                    {{ $karyawan->full_name }} - {{ $karyawan->nip }}
                </option>
            @endforeach
        </select>

        <!-- Filter Jenis Tunjangan -->
        <select id="filter-tunjangan-type" name="tunjangan_type_id" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Jenis</option>
            @foreach($tunjanganTypes as $type)
                <option value="{{ $type->tunjangan_type_id }}" {{ request('tunjangan_type_id') == $type->tunjangan_type_id ? 'selected' : '' }}>
                    {{ $type->display_name }}
                </option>
            @endforeach
        </select>

        <!-- Filter Status -->
        <select id="filter-status" name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Status</option>
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <!-- Date Range -->
        <div class="flex gap-2">
            <input type="date" id="periode-dari" name="periode_dari" value="{{ request('periode_dari') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <input type="date" id="periode-sampai" name="periode_sampai" value="{{ request('periode_sampai') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <!-- Request Via Filter -->
        <select id="filter-requested-via" name="requested_via" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Via</option>
            <option value="mobile" {{ request('requested_via') == 'mobile' ? 'selected' : '' }}>Mobile</option>
            <option value="web" {{ request('requested_via') == 'web' ? 'selected' : '' }}>Web</option>
        </select>
    </div>

    <!-- Actions -->
    <div class="flex gap-2">
        <a href="{{ route('admin.tunjangan-karyawan.report') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Laporan
        </a>
        <button onclick="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export
        </button>
        <a href="{{ route('admin.tunjangan-karyawan.generate.form') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Generate Tunjangan
        </a>
    </div>
</div>
@endsection

@section('content')

<!-- Flash Messages -->
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('warning'))
<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-6" role="alert">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        {{ session('warning') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        {{ session('error') }}
    </div>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Tunjangan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tunjanganKaryawan->total() ?? 0 }}</p>
                <p class="text-sm text-blue-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Semua tunjangan
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tunjanganKaryawan->where('status', 'pending')->count() ?? 0 }}</p>
                <p class="text-sm text-yellow-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Menunggu request
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Requested</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tunjanganKaryawan->where('status', 'requested')->count() ?? 0 }}</p>
                <p class="text-sm text-orange-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Menunggu approve
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Approved</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tunjanganKaryawan->where('status', 'approved')->count() ?? 0 }}</p>
                <p class="text-sm text-green-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Siap diambil
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
                <p class="text-sm font-medium text-gray-600">Received</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $tunjanganKaryawan->where('status', 'received')->count() ?? 0 }}</p>
                <p class="text-sm text-indigo-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sudah diterima
                </p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
            <h3 class="text-lg font-semibold text-gray-900">Daftar Tunjangan Karyawan</h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Total: <span id="total-records">{{ $tunjanganKaryawan->total() ?? 0 }}</span> data</span>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Tunjangan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal & Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="tunjangan-table-body">
                @forelse($tunjanganKaryawan ?? [] as $tunjangan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="selected_tunjangan[]" value="{{ $tunjangan->tunjangan_karyawan_id }}"
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $tunjangan->karyawan->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $tunjangan->karyawan->nip }} • {{ $tunjangan->karyawan->department->name ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                    @if($tunjangan->tunjanganType->category == 'harian') bg-orange-100
                                    @elseif($tunjangan->tunjanganType->category == 'mingguan') bg-blue-100
                                    @else bg-green-100
                                    @endif">
                                    @if($tunjangan->tunjanganType->category == 'harian')
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    @elseif($tunjangan->tunjanganType->category == 'mingguan')
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $tunjangan->tunjanganType->display_name }}</div>
                                <div class="text-xs text-gray-500">{{ $tunjangan->tunjanganType->code }} • {{ ucfirst($tunjangan->tunjanganType->category) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <div class="font-medium">{{ $tunjangan->period_start->format('M Y') }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $tunjangan->period_start->format('d M') }} - {{ $tunjangan->period_end->format('d M Y') }}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <div class="text-lg font-bold text-gray-900">
                                Rp {{ number_format($tunjangan->total_amount, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $tunjangan->hari_kerja_final ?? $tunjangan->quantity }} × Rp {{ number_format($tunjangan->amount, 0, ',', '.') }}
                                @if($tunjangan->hari_potong_penalti > 0)
                                    <br><span class="text-red-500">Potong: {{ $tunjangan->hari_potong_penalti }} hari</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col space-y-2">
                            <!-- Status Badge -->
                            @if($tunjangan->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
                                    Pending
                                </span>
                            @elseif($tunjangan->status == 'requested')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full mr-1 animate-pulse"></div>
                                    Requested
                                </span>
                            @elseif($tunjangan->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-1"></div>
                                    Received
                                </span>
                            @endif

                            <!-- Request Via -->
                            @if($tunjangan->requested_via)
                                <div class="flex items-center text-xs text-gray-500">
                                  @if($tunjangan->requested_via == 'mobile')
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                    Via {{ ucfirst($tunjangan->requested_via) }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.tunjangan-karyawan.show', $tunjangan->tunjangan_karyawan_id) }}"
                               class="text-primary-600 hover:text-primary-700 p-1 rounded hover:bg-primary-50 transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            <!-- Request Button -->
                            @if($tunjangan->canRequest())
                            <button onclick="requestTunjangan('{{ $tunjangan->tunjangan_karyawan_id }}', '{{ $tunjangan->karyawan->full_name }}')"
                                    class="text-blue-600 hover:text-blue-700 p-1 rounded hover:bg-blue-50 transition-colors"
                                    title="Request Tunjangan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            @endif

                            <!-- Approve Button -->
                            @if($tunjangan->canApprove())
                            <button onclick="approveTunjangan('{{ $tunjangan->tunjangan_karyawan_id }}', '{{ $tunjangan->karyawan->full_name }}')"
                                    class="text-green-600 hover:text-green-700 p-1 rounded hover:bg-green-50 transition-colors"
                                    title="Approve Tunjangan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            @endif

                            <!-- Confirm Received Button -->
                            @if($tunjangan->status == 'approved')
                            <button onclick="confirmReceived('{{ $tunjangan->tunjangan_karyawan_id }}', '{{ $tunjangan->karyawan->full_name }}')"
                                    class="text-indigo-600 hover:text-indigo-700 p-1 rounded hover:bg-indigo-50 transition-colors"
                                    title="Konfirmasi Diterima">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                            @endif

                            <!-- Delete Button -->
                            @if($tunjangan->status == 'pending')
                            <button onclick="deleteTunjangan('{{ $tunjangan->tunjangan_karyawan_id }}', '{{ $tunjangan->karyawan->full_name }} - {{ $tunjangan->tunjanganType->name }}')"
                                    class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors"
                                    title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada tunjangan</h3>
                            <p class="text-gray-500 mb-4">Belum ada data tunjangan karyawan yang tercatat</p>
                            <a href="{{ route('admin.tunjangan-karyawan.generate.form') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                Generate Tunjangan
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($tunjanganKaryawan) && $tunjanganKaryawan->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $tunjanganKaryawan->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions (when items selected) -->
<div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-3 hidden">
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600"><span id="selected-count">0</span> item dipilih</span>
        <div class="flex gap-2">
            <button onclick="bulkApprove()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Approve Terpilih
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

<!-- Request Modal -->
<div id="requestModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Tunjangan</h3>
        <form id="requestForm">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                    Request tunjangan untuk: <span id="request-karyawan-name" class="font-medium text-gray-900"></span>
                </p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Request Via</label>
                <select id="requestVia" name="via" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Pilih Platform</option>
                    <option value="web">Web Admin</option>
                    <option value="mobile">Mobile App</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRequestModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Request Tunjangan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approve Tunjangan</h3>
        <form id="approveForm">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                    Approve tunjangan untuk: <span id="approve-karyawan-name" class="font-medium text-gray-900"></span>
                </p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Approval (Opsional)</label>
                <textarea id="approveNotes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Approve
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirm Received Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Penerimaan</h3>
        <form id="confirmForm" enctype="multipart/form-data">
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">
                    Konfirmasi penerimaan untuk: <span id="confirm-karyawan-name" class="font-medium text-gray-900"></span>
                </p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Konfirmasi (Opsional)</label>
                <input type="file" id="confirmationPhoto" name="confirmation_photo" accept="image/jpeg,image/png,image/jpg" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Konfirmasi Diterima
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search');
    const karyawanFilter = document.getElementById('filter-karyawan');
    const tunjanganTypeFilter = document.getElementById('filter-tunjangan-type');
    const statusFilter = document.getElementById('filter-status');
    const periodeDari = document.getElementById('periode-dari');
    const periodeSampai = document.getElementById('periode-sampai');
    const requestedViaFilter = document.getElementById('filter-requested-via');

    let searchTimeout;

    function performSearch() {
        const searchTerm = searchInput.value;
        const karyawan = karyawanFilter.value;
        const tunjanganType = tunjanganTypeFilter.value;
        const status = statusFilter.value;
        const dari = periodeDari.value;
        const sampai = periodeSampai.value;
        const requestedVia = requestedViaFilter.value;

        // Build URL with parameters
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (karyawan !== '') params.append('karyawan_id', karyawan);
        if (tunjanganType !== '') params.append('tunjangan_type_id', tunjanganType);
        if (status !== '') params.append('status', status);
        if (dari) params.append('periode_dari', dari);
        if (sampai) params.append('periode_sampai', sampai);
        if (requestedVia !== '') params.append('requested_via', requestedVia);

        // Redirect with filters
        window.location.href = `{{ route('admin.tunjangan-karyawan.index') }}?${params.toString()}`;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    karyawanFilter.addEventListener('change', performSearch);
    tunjanganTypeFilter.addEventListener('change', performSearch);
    statusFilter.addEventListener('change', performSearch);
    periodeDari.addEventListener('change', performSearch);
    periodeSampai.addEventListener('change', performSearch);
    requestedViaFilter.addEventListener('change', performSearch);

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_tunjangan[]"]');
    const bulkActionsDiv = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('input[name="selected_tunjangan[]"]:checked');
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

let currentTunjanganId = null;

// Request Tunjangan functionality
function requestTunjangan(id, karyawanName) {
    currentTunjanganId = id;
    document.getElementById('request-karyawan-name').textContent = karyawanName;
    document.getElementById('requestModal').classList.remove('hidden');
    document.getElementById('requestVia').value = '';
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
    currentTunjanganId = null;
}

document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const via = document.getElementById('requestVia').value;

    if (!via) {
        alert('Pilih platform request terlebih dahulu');
        return;
    }

    showLoading();

    fetch(`{{ route('admin.tunjangan-karyawan.index') }}/${currentTunjanganId}/request`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ via: via })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    });

    closeRequestModal();
});

// Approve Tunjangan functionality
function approveTunjangan(id, karyawanName) {
    currentTunjanganId = id;
    document.getElementById('approve-karyawan-name').textContent = karyawanName;
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveNotes').value = '';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    currentTunjanganId = null;
}

document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const notes = document.getElementById('approveNotes').value;

    showLoading();

    fetch(`{{ route('admin.tunjangan-karyawan.index') }}/${currentTunjanganId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    });

    closeApproveModal();
});

// Confirm Received functionality
function confirmReceived(id, karyawanName) {
    currentTunjanganId = id;
    document.getElementById('confirm-karyawan-name').textContent = karyawanName;
    document.getElementById('confirmModal').classList.remove('hidden');
    document.getElementById('confirmationPhoto').value = '';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentTunjanganId = null;
}

document.getElementById('confirmForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    const photoFile = document.getElementById('confirmationPhoto').files[0];

    if (photoFile) {
        formData.append('confirmation_photo', photoFile);
    }

    showLoading();

    fetch(`{{ route('admin.tunjangan-karyawan.index') }}/${currentTunjanganId}/confirm-received`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    });

    closeConfirmModal();
});

// Delete functionality
function deleteTunjangan(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus tunjangan "${name}"?`)) {
        showLoading();

        fetch(`{{ route('admin.tunjangan-karyawan.index') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', 'error');
        });
    }
}

// Bulk approve functionality
function bulkApprove() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_tunjangan[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih tunjangan yang ingin diapprove');
        return;
    }

    const notes = prompt(`Approve ${count} tunjangan terpilih.\n\nTambahkan catatan (opsional):`);
    if (notes === null) return; // User cancelled

    showLoading();

    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    fetch('{{ route("admin.tunjangan-karyawan.bulk-approve") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            ids: ids,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    });
}

// Bulk delete functionality
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_tunjangan[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih tunjangan yang ingin dihapus');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus ${count} tunjangan terpilih?`)) {
        showLoading();

        const ids = Array.from(checkedBoxes).map(cb => cb.value);

        fetch('{{ route("admin.tunjangan-karyawan.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', 'error');
        });
    }
}

// Export functionality
function exportData() {
    showLoading();

    // Get current filters
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("admin.tunjangan-karyawan.export") }}?' + searchParams.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'tunjangan-karyawan.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Hide loading after a delay since this is a download
    setTimeout(hideLoading, 2000);
}

// Clear selection functionality
function clearSelection() {
    document.querySelectorAll('input[name="selected_tunjangan[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    document.getElementById('bulk-actions').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    ['requestModal', 'approveModal', 'confirmModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;

    // Animation
    toast.style.transform = 'translateX(100%)';
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Loading functions
function showLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape to close modals
    if (e.key === 'Escape') {
        ['requestModal', 'approveModal', 'confirmModal'].forEach(modalId => {
            document.getElementById(modalId).classList.add('hidden');
        });
        currentTunjanganId = null;
    }
});

// Auto refresh every 5 minutes to keep data current
setInterval(() => {
    // Only refresh if no modals are open
    const modalsOpen = ['requestModal', 'approveModal', 'confirmModal'].some(modalId =>
        !document.getElementById(modalId).classList.contains('hidden')
    );

    if (!modalsOpen) {
        // Soft refresh - just update the stats without full page reload
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update stats cards only
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newStats = newDoc.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5');
            const currentStats = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5');

            if (newStats && currentStats) {
                currentStats.innerHTML = newStats.innerHTML;
            }
        })
        .catch(error => {
            console.log('Auto refresh failed:', error);
        });
    }
}, 300000); // 5 minutes

// Initialize tooltips for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add tooltips to action buttons
    const actionButtons = document.querySelectorAll('[title]');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            // Create simple tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-xs bg-gray-800 text-white rounded shadow-lg';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.bottom = '100%';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            tooltip.style.marginBottom = '5px';

            this.style.position = 'relative';
            this.appendChild(tooltip);
        });

        button.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('div');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });

    // Add loading states to form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                const originalText = submitButton.textContent;
                submitButton.textContent = 'Loading...';

                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }, 3000);
            }
        });
    });
});

// Utility function to format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Utility function to format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Add visual feedback for better UX
function addButtonFeedback() {
    const buttons = document.querySelectorAll('button, .btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    addButtonFeedback();

    // Smooth scroll to top when clicking pagination
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        });
    });
});

// Error handling for network issues
window.addEventListener('online', function() {
    showToast('Koneksi internet tersambung kembali', 'success');
});

window.addEventListener('offline', function() {
    showToast('Koneksi internet terputus', 'warning');
});

// Handle browser back button
window.addEventListener('popstate', function() {
    // Close any open modals when user navigates back
    ['requestModal', 'approveModal', 'confirmModal'].forEach(modalId => {
        document.getElementById(modalId).classList.add('hidden');
    });
    currentTunjanganId = null;
});
</script>
@endpush
