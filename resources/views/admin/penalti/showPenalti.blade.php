@extends('admin.layouts.app')

@section('title', 'Detail Penalti')
@section('breadcrumb', 'Manajemen Penalti / Detail')
@section('page_title', 'Detail Penalti Karyawan')

@section('page_actions')
<div class="flex flex-wrap gap-3">
    @if($penalti->status == 'active')
    <a href="{{ route('admin.penalti.edit', $penalti->penalti_id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Penalti
    </a>
    @endif

    <button onclick="changeStatus()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        Ubah Status
    </button>

    <button onclick="printDetail()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Print
    </button>

    @if(!$penalti->tunjanganKaryawan()->exists())
    <button onclick="deletePenalti()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Hapus
    </button>
    @endif

    <a href="{{ route('admin.penalti.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar
    </a>
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

<div id="printable-area" class="space-y-6">

<!-- Header Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header with Status -->
    <div class="bg-gradient-to-r
        @if($penalti->status == 'active') from-red-500 to-red-600
        @elseif($penalti->status == 'completed') from-green-500 to-green-600
        @else from-gray-500 to-gray-600
        @endif
        px-6 py-4">
        <div class="flex items-center justify-between text-white">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ $penalti->penalti_id }}</h1>
                    <p class="text-white text-opacity-90">Penalti {{ ucfirst(str_replace('_', ' ', $penalti->jenis_penalti)) }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20">
                    <div class="w-2 h-2 rounded-full mr-2
                        @if($penalti->status == 'active') bg-red-300 animate-pulse
                        @elseif($penalti->status == 'completed') bg-green-300
                        @else bg-gray-300
                        @endif"></div>
                    {{ ucfirst($penalti->status) }}
                </div>
                <p class="text-xs text-white text-opacity-75 mt-1">
                    {{ $penalti->tanggal_penalti->format('d M Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Info -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Karyawan Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Karyawan
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-bold text-gray-900">{{ $penalti->karyawan->full_name }}</h4>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-500">NIP:</span>
                                        <span class="font-medium text-gray-900 ml-2">{{ $penalti->karyawan->nip }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Departemen:</span>
                                        <span class="font-medium text-gray-900 ml-2">{{ $penalti->karyawan->department->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Posisi:</span>
                                        <span class="font-medium text-gray-900 ml-2">{{ $penalti->karyawan->position }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Status Staff:</span>
                                        <span class="font-medium text-gray-900 ml-2">{{ ucfirst(str_replace('_', ' ', $penalti->karyawan->staff_status)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penalti Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Detail Penalti
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Penalti</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3
                                        @if($penalti->jenis_penalti == 'telat') bg-yellow-100
                                        @elseif($penalti->jenis_penalti == 'tidak_masuk') bg-red-100
                                        @elseif($penalti->jenis_penalti == 'pelanggaran') bg-orange-100
                                        @else bg-gray-100
                                        @endif">
                                        @if($penalti->jenis_penalti == 'telat')
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @elseif($penalti->jenis_penalti == 'tidak_masuk')
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @elseif($penalti->jenis_penalti == 'pelanggaran')
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $penalti->jenis_penalti) }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Hari Potong</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </div>
                                    <span class="text-2xl font-bold text-red-600">{{ $penalti->hari_potong_uang_makan }}</span>
                                    <span class="text-gray-600 ml-1">hari</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Deskripsi</label>
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <p class="text-gray-900 leading-relaxed">{{ $penalti->deskripsi }}</p>
                            </div>
                        </div>

                        @if($penalti->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Catatan</label>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-blue-900 leading-relaxed">{{ $penalti->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Periode & Timeline -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Periode & Timeline
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Penalti</label>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ $penalti->tanggal_penalti->format('d M Y') }}</span>
                                    <span class="text-gray-500 ml-2">({{ $penalti->tanggal_penalti->diffForHumans() }})</span>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Periode Berlaku Potongan</label>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Mulai:</span>
                                        <span class="font-medium text-gray-900">{{ $penalti->periode_berlaku_mulai->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Berakhir:</span>
                                        <span class="font-medium text-gray-900">{{ $penalti->periode_berlaku_akhir->format('d M Y') }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-600">Durasi:</span>
                                            <span class="font-bold text-blue-600">
                                                {{ $penalti->periode_berlaku_mulai->diffInDays($penalti->periode_berlaku_akhir) + 1 }} hari
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Period Indicator -->
                        @php
                            $now = now();
                            $isActive = $now >= $penalti->periode_berlaku_mulai && $now <= $penalti->periode_berlaku_akhir;
                        @endphp
                        <div class="p-3 rounded-lg border-2 border-dashed
                            @if($isActive && $penalti->status == 'active') border-red-300 bg-red-50
                            @elseif($now < $penalti->periode_berlaku_mulai) border-yellow-300 bg-yellow-50
                            @else border-gray-300 bg-gray-50
                            @endif">
                            <div class="flex items-center">
                                @if($isActive && $penalti->status == 'active')
                                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-red-800">Sedang berlaku potongan</span>
                                @elseif($now < $penalti->periode_berlaku_mulai)
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-800">Belum berlaku</span>
                                @else
                                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-800">Periode berakhir</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Absensi Reference -->
                @if($penalti->absen)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Referensi Absensi
                    </h3>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-purple-600 font-medium">ID Absensi:</span>
                                <span class="text-purple-900 ml-2">{{ $penalti->absen->absen_id }}</span>
                            </div>
                            <div>
                                <span class="text-purple-600 font-medium">Tanggal:</span>
                                <span class="text-purple-900 ml-2">{{ $penalti->absen->date->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-purple-600 font-medium">Status Absen:</span>
                                <span class="text-purple-900 ml-2">{{ ucfirst($penalti->absen->status) }}</span>
                            </div>
                            @if($penalti->absen->late_minutes > 0)
                            <div>
                                <span class="text-purple-600 font-medium">Keterlambatan:</span>
                                <span class="text-purple-900 ml-2">{{ $penalti->absen->late_minutes }} menit</span>
                            </div>
                            @endif
                        </div>
                        @if($penalti->absen->clock_in && $penalti->absen->clock_out)
                        <div class="mt-3 pt-3 border-t border-purple-200">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-purple-600 font-medium">Clock In:</span>
                                    <span class="text-purple-900 ml-2">{{ $penalti->absen->clock_in }}</span>
                                </div>
                                <div>
                                    <span class="text-purple-600 font-medium">Clock Out:</span>
                                    <span class="text-purple-900 ml-2">{{ $penalti->absen->clock_out }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Management Info -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Informasi Manajemen
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="grid grid-cols-1 gap-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dibuat oleh:</span>
                                <span class="font-medium text-gray-900">{{ $penalti->createdBy->name ?? 'System' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal dibuat:</span>
                                <span class="font-medium text-gray-900">{{ $penalti->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($penalti->updated_at != $penalti->created_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir diubah:</span>
                                <span class="font-medium text-gray-900">{{ $penalti->updated_at->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                            @if($penalti->approvedBy)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Disetujui oleh:</span>
                                <span class="font-medium text-gray-900">{{ $penalti->approvedBy->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal persetujuan:</span>
                                <span class="font-medium text-gray-900">{{ $penalti->approved_at->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Impact & Related Data -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Tunjangan yang Terpengaruh -->
    @if($penalti->tunjanganKaryawan()->exists())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Tunjangan yang Terpengaruh
                <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                    {{ $penalti->tunjanganKaryawan()->count() }}
                </span>
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($penalti->tunjanganKaryawan as $tunjangan)
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">{{ $tunjangan->tunjanganType->display_name }}</h4>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            @if($tunjangan->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($tunjangan->status == 'approved') bg-green-100 text-green-800
                            @elseif($tunjangan->status == 'received') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($tunjangan->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Periode:</span>
                            <span class="font-medium text-gray-900 ml-2">
                                {{ $tunjangan->period_start->format('M Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Hari Potong:</span>
                            <span class="font-medium text-red-600 ml-2">{{ $tunjangan->hari_potong_penalti ?? 0 }} hari</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Hari Kerja Asli:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $tunjangan->hari_kerja_asli ?? 0 }} hari</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Hari Kerja Final:</span>
                            <span class="font-medium text-green-600 ml-2">{{ $tunjangan->hari_kerja_final ?? 0 }} hari</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="font-bold text-gray-900 ml-2">Rp {{ number_format($tunjangan->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Dampak Tunjangan
            </h3>
        </div>
        <div class="p-6">
            <div class="text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                <p>Penalti ini belum mempengaruhi tunjangan karyawan</p>
                <p class="text-sm mt-1">Dampak akan muncul saat tunjangan diproses</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Activity Timeline -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Timeline Aktivitas
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Created -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">Penalti Dibuat</h4>
                        <p class="text-sm text-gray-600">{{ $penalti->created_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-gray-500">Oleh: {{ $penalti->createdBy->name ?? 'System' }}</p>
                    </div>
                </div>

                <!-- Updated -->
                @if($penalti->updated_at != $penalti->created_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">Penalti Diubah</h4>
                        <p class="text-sm text-gray-600">{{ $penalti->updated_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-gray-500">Terakhir diperbarui</p>
                    </div>
                </div>
                @endif

                <!-- Approved -->
                @if($penalti->approvedBy)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">Penalti Disetujui</h4>
                        <p class="text-sm text-gray-600">{{ $penalti->approved_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-gray-500">Oleh: {{ $penalti->approvedBy->name }}</p>
                    </div>
                </div>
                @endif

                <!-- Status Current -->
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-4
                        @if($penalti->status == 'active') bg-red-100
                        @elseif($penalti->status == 'completed') bg-green-100
                        @else bg-gray-100
                        @endif">
                        @if($penalti->status == 'active')
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($penalti->status == 'completed')
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">Status: {{ ucfirst($penalti->status) }}</h4>
                        <p class="text-sm text-gray-600">Saat ini</p>
                        <p class="text-sm text-gray-500">
                            @if($penalti->status == 'active')
                                Penalti sedang aktif dan berlaku
                            @elseif($penalti->status == 'completed')
                                Penalti telah selesai dijalankan
                            @else
                                Penalti dibatalkan
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Change Status Modal -->
<div id="statusModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ubah Status Penalti</h3>
        <form id="statusForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Saat Ini</label>
                <div class="p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                    {{ ucfirst($penalti->status) }} - {{ $penalti->penalti_id }}
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                <select id="newStatus" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    <option value="">Pilih Status</option>
                    <option value="active" {{ $penalti->status == 'active' ? 'disabled' : '' }}>Aktif</option>
                    <option value="completed" {{ $penalti->status == 'completed' ? 'disabled' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $penalti->status == 'cancelled' ? 'disabled' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Perubahan</label>
                <textarea id="statusNotes" name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Alasan perubahan status..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    body { font-size: 12px; }
    .bg-gradient-to-r { background: #dc2626 !important; -webkit-print-color-adjust: exact; }
}
</style>
@endpush

@push('scripts')
<script>
// Change Status functionality
function changeStatus() {
    document.getElementById('statusModal').classList.remove('hidden');
    document.getElementById('newStatus').value = '';
    document.getElementById('statusNotes').value = '';
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Handle status form submission
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const status = document.getElementById('newStatus').value;
    const notes = document.getElementById('statusNotes').value;

    if (!status) {
        alert('Pilih status terlebih dahulu');
        return;
    }

    if (!notes.trim()) {
        if (!confirm('Anda tidak memberikan catatan perubahan. Lanjutkan?')) {
            return;
        }
    }

    showLoading();

    fetch(`{{ route('admin.penalti.show', $penalti->penalti_id) }}/change-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: status,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('Terjadi kesalahan sistem', 'error');
    });

    closeStatusModal();
});

// Delete functionality
function deletePenalti() {
    if (confirm(`Apakah Anda yakin ingin menghapus penalti ${document.querySelector('h1').textContent}?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        showLoading();

        fetch(`{{ route('admin.penalti.show', $penalti->penalti_id) }}`, {
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
                setTimeout(() => {
                    window.location.href = '{{ route("admin.penalti.index") }}';
                }, 1500);
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

// Print functionality
function printDetail() {
    // Hide action buttons and other non-printable elements
    document.querySelectorAll('.no-print, button, .fixed').forEach(el => {
        el.style.display = 'none';
    });

    // Print
    window.print();

    // Restore elements after printing
    setTimeout(() => {
        document.querySelectorAll('.no-print, button, .fixed').forEach(el => {
            el.style.display = '';
        });
    }, 1000);
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape to close modal
    if (e.key === 'Escape') {
        closeStatusModal();
    }

    // Ctrl/Cmd + P to print
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printDetail();
    }
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

// Page load animations
document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
