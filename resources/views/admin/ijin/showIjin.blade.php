@extends('admin.layouts.app')

@section('title', 'Detail Ijin')
@section('page_title', 'Detail Ijin')
@section('breadcrumb', 'Ijin / Detail')

@section('content')
    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Detail Pengajuan Ijin</h1>
                        <p class="text-sm text-gray-600 mt-1">Informasi lengkap pengajuan ijin karyawan</p>
                    </div>
                </div>
                <a href="{{ route('admin.ijin.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    <div class="mb-6">
        @if ($ijin->status === 'pending')
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-yellow-800 font-medium">Ijin ini sedang dalam proses review</span>
                </div>
            </div>
        @elseif ($ijin->status === 'approved')
            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-green-800 font-medium">Ijin ini telah disetujui</span>
                </div>
            </div>
        @else
            <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-red-800 font-medium">Ijin ini telah ditolak</span>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Karyawan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                    Informasi Karyawan
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div
                            class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center mr-4">
                            <span
                                class="text-white font-bold text-xl">{{ strtoupper(substr($ijin->karyawan->full_name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $ijin->karyawan->full_name }}</h3>
                            <p class="text-sm text-gray-600">NIP: {{ $ijin->karyawan->nip }}</p>
                            <p class="text-sm text-gray-600">Department: {{ $ijin->karyawan->department->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Pengajuan Ijin -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd" />
                    </svg>
                    Detail Pengajuan Ijin
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Ijin</label>
                        <p class="text-gray-900 font-mono text-sm">{{ $ijin->ijin_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Ijin</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-pink-100 text-pink-800 font-medium">
                            {{ $ijin->ijinType->name }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengajuan</label>
                        <p class="text-gray-900">{{ $ijin->created_at->format('d F Y, H:i') }} WIB</p>
                        <p class="text-xs text-gray-500">{{ $ijin->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                        <p class="text-gray-900 font-semibold">{{ $ijin->total_days }} hari</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->date_from)->format('d F Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->date_to)->format('d F Y') }}</p>
                    </div>

                    @if ($ijin->ijinType->code === 'shift_swap')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shift Asli</label>
                            <p class="text-gray-900">
                                {{ \Carbon\Carbon::parse($ijin->original_shift_date)->format('d F Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shift Pengganti</label>
                            <p class="text-gray-900">
                                {{ \Carbon\Carbon::parse($ijin->replacement_shift_date)->format('d F Y') }}</p>
                        </div>
                    @endif

                    @if ($ijin->ijinType->code === 'compensation_leave' && $ijin->original_shift_date)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kompensasi Dari Piket</label>
                            <p class="text-gray-900">
                                {{ \Carbon\Carbon::parse($ijin->original_shift_date)->format('d F Y') }}</p>
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Pengajuan</label>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-gray-900 leading-relaxed">{{ $ijin->reason }}</p>
                        </div>
                    </div>
                       @if ($ijin->hasPhoto())
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Foto</label>
                            <div class="relative group cursor-pointer" onclick="openPhotoModal('{{ $ijin->photo_url }}')">
                                <img src="{{ $ijin->photo_url }}"
                                     alt="Lampiran Ijin"
                                     class="w-full max-w-md rounded-lg border-2 border-gray-200 hover:border-indigo-400 transition-all duration-300 shadow-sm hover:shadow-md">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-lg transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white rounded-full p-3 shadow-lg">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Klik untuk memperbesar</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Review Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    Timeline Review
                </h2>

                <div class="space-y-4">
                    <!-- Pengajuan -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-sm font-semibold text-gray-900">Pengajuan Dibuat</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $ijin->created_at->format('d F Y, H:i') }} WIB</p>
                            <p class="text-sm text-gray-600 mt-1">Diajukan oleh {{ $ijin->karyawan->full_name }}</p>
                        </div>
                    </div>

                    <!-- Coordinator Review -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 rounded-full {{ $ijin->coordinator_status === 'pending' ? 'bg-yellow-100' : ($ijin->coordinator_status === 'approved' ? 'bg-green-100' : 'bg-red-100') }} flex items-center justify-center">
                                @if ($ijin->coordinator_status === 'pending')
                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @elseif ($ijin->coordinator_status === 'approved')
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-sm font-semibold text-gray-900">Review Koordinator</h4>
                            @if ($ijin->coordinator_reviewed_at)
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $ijin->coordinator_reviewed_at->format('d F Y, H:i') }} WIB</p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ijin->coordinator_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $ijin->coordinator_status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </span>
                                    <span class="text-sm text-gray-600 ml-2">oleh
                                        {{ $ijin->coordinator->name ?? '-' }}</span>
                                </div>
                                @if ($ijin->coordinator_note)
                                    <div class="mt-2 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                        <p class="text-sm text-gray-700">{{ $ijin->coordinator_note }}</p>
                                    </div>
                                @endif
                            @else
                                <p class="text-xs text-yellow-600 mt-1">Menunggu review dari
                                    {{ $ijin->coordinator->name ?? '-' }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Review -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 rounded-full {{ $ijin->admin_status === 'pending' ? 'bg-gray-100' : ($ijin->admin_status === 'approved' ? 'bg-green-100' : 'bg-red-100') }} flex items-center justify-center">
                                @if ($ijin->admin_status === 'pending')
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @elseif ($ijin->admin_status === 'approved')
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-sm font-semibold text-gray-900">Review Admin</h4>
                            @if ($ijin->admin_reviewed_at)
                                <p class="text-xs text-gray-500 mt-1">{{ $ijin->admin_reviewed_at->format('d F Y, H:i') }}
                                    WIB</p>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ijin->admin_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $ijin->admin_status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </span>
                                    <span class="text-sm text-gray-600 ml-2">oleh {{ $ijin->admin->name ?? '-' }}</span>
                                </div>
                                @if ($ijin->admin_note)
                                    <div class="mt-2 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                        <p class="text-sm text-gray-700">{{ $ijin->admin_note }}</p>
                                    </div>
                                @endif
                            @else
                                <p class="text-xs text-gray-600 mt-1">
                                    @if ($ijin->coordinator_status === 'approved')
                                        Menunggu review admin
                                    @else
                                        Menunggu approval koordinator
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Jadwal/Absen (jika sudah approved) -->
            @if ($ijin->status === 'approved' && $ijin->jadwals->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                        Jadwal Terdampak
                    </h2>
                    <div class="space-y-2">
                        @foreach ($ijin->jadwals as $jadwal)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $jadwal->date->format('d F Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $jadwal->shift->name ?? '-' }} • Status:
                                        {{ ucfirst($jadwal->status) }}</p>
                                </div>
                                @if ($jadwal->absen)
                                    <span
                                        class="text-xs px-2 py-1 rounded-full {{ $jadwal->absen->status === 'scheduled' ? 'bg-gray-100 text-gray-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($jadwal->absen->status) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Quick Info & Actions -->
        <div class="lg:col-span-1">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Ijin</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Utama</label>
                        @if ($ijin->status === 'pending')
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 w-full justify-center">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                                Pending
                            </span>
                        @elseif ($ijin->status === 'approved')
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 w-full justify-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                Approved
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800 w-full justify-center">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                Rejected
                            </span>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Koordinator</span>
                            @if ($ijin->coordinator_status === 'pending')
                                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                            @elseif ($ijin->coordinator_status === 'approved')
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Approved</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">Rejected</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Admin</span>
                            @if ($ijin->admin_status === 'pending')
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">Pending</span>
                            @elseif ($ijin->admin_status === 'approved')
                                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Approved</span>
                            @else
                                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">Rejected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if ($ijin->status === 'pending')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if (auth()->user()->role === 'coordinator' &&
                                $ijin->coordinator_id === auth()->user()->user_id &&
                                $ijin->coordinator_status === 'pending')
                            <a href="{{ route('admin.ijin.coordinator-review-form', $ijin->ijin_id) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 text-white text-sm font-semibold rounded-lg hover:from-yellow-600 hover:to-orange-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                Review Sekarang
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('admin.ijin.admin-review-form', $ijin->ijin_id) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Review Sekarang
                            </a>
                        @endif

                        <a href="{{ route('admin.ijin.index') }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Lihat Semua Ijin
                        </a>
                    </div>
                </div>
            @endif

            <!-- Info Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    Informasi
                </h3>
                <ul class="text-xs text-blue-800 space-y-2">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>ID Ijin dapat digunakan untuk tracking</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Timeline menampilkan histori lengkap review</span>
                    </li>
                    @if ($ijin->status === 'approved')
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Jadwal dan absen sudah diupdate otomatis</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection
