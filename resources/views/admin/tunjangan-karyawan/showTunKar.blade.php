@extends('admin.layouts.app')

@section('title', 'Detail Tunjangan Karyawan')
@section('breadcrumb', 'Manajemen Tunjangan / Detail')
@section('page_title', 'Detail Tunjangan Karyawan')

@section('page_actions')
    <div class="flex flex-wrap gap-3">
        <!-- Workflow Actions -->
        @if ($tunjanganKaryawan->canRequest())
            <button onclick="requestTunjangan()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Request Tunjangan
            </button>
        @endif

        @if ($tunjanganKaryawan->canApprove())
            <button onclick="approveTunjangan()"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Approve Tunjangan
            </button>
        @endif

        @if ($tunjanganKaryawan->status == 'approved')
            <button onclick="confirmReceived()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Konfirmasi Diterima
            </button>
        @endif

        <!-- Other Actions -->
        <button onclick="printDetail()"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print
        </button>

        @if ($tunjanganKaryawan->status == 'pending')
            <button onclick="deleteTunjangan()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        @endif

        <a href="{{ route('admin.tunjangan-karyawan.index') }}"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>
@endsection

@section('content')

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div id="printable-area" class="space-y-6">

        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Status Header -->
            <div
                class="bg-gradient-to-r
        @if ($tunjanganKaryawan->status == 'pending') from-yellow-500 to-yellow-600
        @elseif($tunjanganKaryawan->status == 'requested') from-orange-500 to-orange-600
        @elseif($tunjanganKaryawan->status == 'approved') from-green-500 to-green-600
        @else from-blue-500 to-blue-600 @endif
        px-6 py-4">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ $tunjanganKaryawan->tunjangan_karyawan_id }}</h1>
                            <p class="text-white text-opacity-90">{{ $tunjanganKaryawan->tunjanganType->display_name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20">
                            <div
                                class="w-2 h-2 rounded-full mr-2
                        @if ($tunjanganKaryawan->status == 'pending') bg-yellow-300
                        @elseif($tunjanganKaryawan->status == 'requested') bg-orange-300 animate-pulse
                        @elseif($tunjanganKaryawan->status == 'approved') bg-green-300
                        @else bg-blue-300 @endif">
                            </div>
                            {{ ucfirst($tunjanganKaryawan->status) }}
                        </div>
                        <p class="text-xs text-white text-opacity-75 mt-1">
                            Periode {{ $tunjanganKaryawan->period_start->format('M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Karyawan Info -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informasi Karyawan
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-4">
                                        <div
                                            class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-xl font-bold text-gray-900">
                                            {{ $tunjanganKaryawan->karyawan->full_name }}</h4>
                                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <span class="text-gray-500">NIP:</span>
                                                <span
                                                    class="font-medium text-gray-900 ml-2">{{ $tunjanganKaryawan->karyawan->nip }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Departemen:</span>
                                                <span
                                                    class="font-medium text-gray-900 ml-2">{{ $tunjanganKaryawan->karyawan->department->name ?? '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Posisi:</span>
                                                <span
                                                    class="font-medium text-gray-900 ml-2">{{ $tunjanganKaryawan->karyawan->position }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Status Staff:</span>
                                                <span
                                                    class="font-medium text-gray-900 ml-2">{{ ucfirst(str_replace('_', ' ', $tunjanganKaryawan->karyawan->staff_status)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tunjangan Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                Detail Tunjangan
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                                    @if ($tunjanganKaryawan->tunjanganType->category == 'harian') bg-orange-100
                                    @elseif($tunjanganKaryawan->tunjanganType->category == 'mingguan') bg-blue-100
                                    @else bg-green-100 @endif">
                                            @if ($tunjanganKaryawan->tunjanganType->category == 'harian')
                                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            @elseif($tunjanganKaryawan->tunjanganType->category == 'mingguan')
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ $tunjanganKaryawan->tunjanganType->display_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $tunjanganKaryawan->tunjanganType->code }} •
                                                {{ ucfirst($tunjanganKaryawan->tunjanganType->category) }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600">
                                            Rp {{ number_format($tunjanganKaryawan->total_amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">Total Tunjangan</div>
                                    </div>
                                </div>

                                <!-- Calculation Breakdown -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <h5 class="font-medium text-gray-900 mb-3">Rincian Perhitungan</h5>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nominal per unit:</span>
                                            <span class="font-medium text-gray-900">Rp
                                                {{ number_format($tunjanganKaryawan->amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Hari kerja asli:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $tunjanganKaryawan->hari_kerja_asli ?? $tunjanganKaryawan->quantity }}
                                                hari</span>
                                        </div>
                                        @if ($tunjanganKaryawan->hari_potong_penalti > 0)
                                            <div class="flex justify-between">
                                                <span class="text-red-600">Hari potong penalti:</span>
                                                <span
                                                    class="font-medium text-red-600">-{{ $tunjanganKaryawan->hari_potong_penalti }}
                                                    hari</span>
                                            </div>
                                        @endif
                                        <div class="border-t border-gray-200 pt-2 flex justify-between">
                                            <span class="text-gray-600 font-medium">Hari kerja final:</span>
                                            <span
                                                class="font-bold text-green-600">{{ $tunjanganKaryawan->hari_kerja_final ?? ($tunjanganKaryawan->hari_kerja_asli ?? $tunjanganKaryawan->quantity) }}
                                                hari</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($tunjanganKaryawan->notes)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <div>
                                                <h6 class="font-medium text-blue-900">Catatan</h6>
                                                <p class="text-sm text-blue-800 mt-1">{{ $tunjanganKaryawan->notes }}</p>
                                            </div>
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
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Periode & Waktu
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Periode Tunjangan</label>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Mulai:</span>
                                        <span
                                            class="font-medium text-gray-900">{{ $tunjanganKaryawan->period_start->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm text-gray-600">Berakhir:</span>
                                        <span
                                            class="font-medium text-gray-900">{{ $tunjanganKaryawan->period_end->format('d M Y') }}</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-600">Durasi:</span>
                                            <span class="font-bold text-purple-600">
                                                {{ $tunjanganKaryawan->period_start->diffInDays($tunjanganKaryawan->period_end) + 1 }}
                                                hari
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Absensi Reference -->
                        @if ($tunjanganKaryawan->absen)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Referensi Absensi
                                </h3>
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-indigo-600 font-medium">ID Absensi:</span>
                                            <span
                                                class="text-indigo-900 ml-2">{{ $tunjanganKaryawan->absen->absen_id }}</span>
                                        </div>
                                        <div>
                                            <span class="text-indigo-600 font-medium">Tanggal:</span>
                                            <span
                                                class="text-indigo-900 ml-2">{{ $tunjanganKaryawan->absen->date->format('d M Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-indigo-600 font-medium">Jam Kerja:</span>
                                            <span
                                                class="text-indigo-900 ml-2">{{ $tunjanganKaryawan->absen->work_hours ?? 0 }}
                                                jam</span>
                                        </div>
                                        @if ($tunjanganKaryawan->quantity > 1)
                                            <div>
                                                <span class="text-indigo-600 font-medium">Jam Lembur:</span>
                                                <span class="text-indigo-900 ml-2">{{ $tunjanganKaryawan->quantity }}
                                                    jam</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Penalti Reference -->
                        @if ($tunjanganKaryawan->penalti)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    Penalti Terkait
                                </h3>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="grid grid-cols-1 gap-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-red-600 font-medium">ID Penalti:</span>
                                            <span
                                                class="text-red-900">{{ $tunjanganKaryawan->penalti->penalti_id }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-red-600 font-medium">Jenis:</span>
                                            <span
                                                class="text-red-900">{{ ucfirst(str_replace('_', ' ', $tunjanganKaryawan->penalti->jenis_penalti)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-red-600 font-medium">Hari Potong Insentif Kehadiran:</span>
                                            <span
                                                class="text-red-900 font-bold">{{ $tunjanganKaryawan->penalti->hari_potong_uang_makan }}
                                                hari</span>
                                        </div>
                                        <div class="pt-2 border-t border-red-200">
                                            <span class="text-red-600 font-medium">Deskripsi:</span>
                                            <p class="text-red-900 text-xs mt-1">
                                                {{ $tunjanganKaryawan->penalti->deskripsi }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Workflow Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Informasi Workflow
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="grid grid-cols-1 gap-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Dibuat:</span>
                                        <span
                                            class="font-medium text-gray-900">{{ $tunjanganKaryawan->created_at->format('d M Y H:i') }}</span>
                                    </div>

                                    @if ($tunjanganKaryawan->requested_at)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Di-request:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $tunjanganKaryawan->requested_at->format('d M Y H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Request via:</span>
                                            <span class="font-medium text-gray-900 flex items-center">
                                                @if ($tunjanganKaryawan->requested_via == 'mobile')
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 18h.01M8 21h8a1 1 0 001-1V4a1 1 0 00-1-1H8a1 1 0 00-1 1v16a1 1 0 001 1z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                @endif
                                                {{ ucfirst($tunjanganKaryawan->requested_via) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($tunjanganKaryawan->approved_at)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Disetujui:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $tunjanganKaryawan->approved_at->format('d M Y H:i') }}</span>
                                        </div>
                                        @if ($tunjanganKaryawan->approvedBy)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Disetujui oleh:</span>
                                                <span
                                                    class="font-medium text-gray-900">{{ $tunjanganKaryawan->approvedBy->name }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    @if ($tunjanganKaryawan->received_at)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Diterima:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $tunjanganKaryawan->received_at->format('d M Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Photo -->
                        @if ($tunjanganKaryawan->received_confirmation_photo)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Foto Konfirmasi
                                </h3>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <img src="{{ Storage::disk('s3')->url($tunjanganKaryawan->received_confirmation_photo) }}"
                                        alt="Foto konfirmasi penerimaan"
                                        class="w-full max-w-sm rounded-lg shadow-sm border border-gray-200"
                                        onclick="openImageModal(this.src)">
                                    <p class="text-xs text-green-600 mt-2">Klik untuk memperbesar</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline & History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Timeline & History
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @if (is_array($history) && count($history) > 0)
                        @foreach ($history as $item)
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-4
                        @if ($item['status'] == 'pending') bg-yellow-100
                        @elseif($item['status'] == 'requested') bg-orange-100
                        @elseif($item['status'] == 'approved') bg-green-100
                        @else bg-blue-100 @endif">
                                    @if ($item['status'] == 'pending')
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($item['status'] == 'requested')
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif($item['status'] == 'approved')
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ ucfirst($item['status']) }}</h4>
                                    <p class="text-sm text-gray-600">{{ $item['formatted_date'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['notes'] }}</p>
                                    @if (isset($item['user_name']) && $item['user_name'] != 'System')
                                        <p class="text-xs text-gray-400 mt-1">Oleh: {{ $item['user_name'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Default timeline based on status -->
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Tunjangan Dibuat</h4>
                                <p class="text-sm text-gray-600">{{ $tunjanganKaryawan->created_at->format('d M Y H:i') }}
                                </p>
                                <p class="text-sm text-gray-500">Tunjangan dibuat otomatis oleh sistem</p>
                            </div>
                        </div>

                        @if ($tunjanganKaryawan->requested_at)
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Tunjangan Di-request</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $tunjanganKaryawan->requested_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-500">Request via
                                        {{ ucfirst($tunjanganKaryawan->requested_via) }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($tunjanganKaryawan->approved_at)
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Tunjangan Disetujui</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $tunjanganKaryawan->approved_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-500">Oleh:
                                        {{ $tunjanganKaryawan->approvedBy->name ?? 'Admin' }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($tunjanganKaryawan->received_at)
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">Tunjangan Diterima</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $tunjanganKaryawan->received_at->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-gray-500">Konfirmasi penerimaan tunjangan</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Request Modal -->
    <div id="requestModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Tunjangan</h3>
            <form id="requestForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Via</label>
                    <select id="requestVia" name="via"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        required>
                        <option value="">Pilih Platform</option>
                        <option value="web">Web Admin</option>
                        <option value="mobile">Mobile App</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRequestModal()"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Approval (Opsional)</label>
                    <textarea id="approveNotes" name="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Konfirmasi (Opsional)</label>
                    <input type="file" id="confirmationPhoto" name="confirmation_photo"
                        accept="image/jpeg,image/png,image/jpg"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeConfirmModal()"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Konfirmasi Diterima
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center"
        onclick="closeImageModal()">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Full size image"
                class="max-w-full max-h-full rounded-lg shadow-lg">
        </div>
    </div>

@endsection

@push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12px;
            }

            .bg-gradient-to-r {
                background: #3b82f6 !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Request Tunjangan functionality
        function requestTunjangan() {
            document.getElementById('requestModal').classList.remove('hidden');
            document.getElementById('requestVia').value = '';
        }

        function closeRequestModal() {
            document.getElementById('requestModal').classList.add('hidden');
        }

        document.getElementById('requestForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const via = document.getElementById('requestVia').value;

            if (!via) {
                alert('Pilih platform request terlebih dahulu');
                return;
            }

            showLoading();

            fetch(`{{ route('admin.tunjangan-karyawan.show', $tunjanganKaryawan->tunjangan_karyawan_id) }}/request`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        via: via
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

            closeRequestModal();
        });

        // Approve Tunjangan functionality
        function approveTunjangan() {
            document.getElementById('approveModal').classList.remove('hidden');
            document.getElementById('approveNotes').value = '';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        document.getElementById('approveForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const notes = document.getElementById('approveNotes').value;

            showLoading();

            fetch(`{{ route('admin.tunjangan-karyawan.show', $tunjanganKaryawan->tunjangan_karyawan_id) }}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
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

            closeApproveModal();
        });

        // Confirm Received functionality
        function confirmReceived() {
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmationPhoto').value = '';
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        document.getElementById('confirmForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            const photoFile = document.getElementById('confirmationPhoto').files[0];

            if (photoFile) {
                formData.append('confirmation_photo', photoFile);
            }

            showLoading();

            fetch(`{{ route('admin.tunjangan-karyawan.show', $tunjanganKaryawan->tunjangan_karyawan_id) }}/confirm-received`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
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
        function deleteTunjangan() {
            if (confirm('Apakah Anda yakin ingin menghapus tunjangan ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
                showLoading();

                fetch(`{{ route('admin.tunjangan-karyawan.show', $tunjanganKaryawan->tunjangan_karyawan_id) }}`, {
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
                                window.location.href = '{{ route('admin.tunjangan-karyawan.index') }}';
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
            window.print();
        }

        // Image modal functionality
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
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

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape to close modals
            if (e.key === 'Escape') {
                ['requestModal', 'approveModal', 'confirmModal', 'imageModal'].forEach(modalId => {
                    document.getElementById(modalId).classList.add('hidden');
                });
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

        // Page animations
        document.addEventListener('DOMContentLoaded', function() {
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
