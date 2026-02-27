@extends('admin.layouts.app')

@section('title', 'Edit Penalti')
@section('breadcrumb', 'Manajemen Penalti / Edit')
@section('page_title', 'Edit Penalti Karyawan')

@section('page_actions')
<div class="flex gap-3">
    <a href="{{ route('admin.penalti.show', $penalti->penalti_id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Lihat Detail
    </a>
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

<!-- Validation Errors -->
@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
    <div class="flex items-start">
        <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <div class="flex-1">
            <p class="font-medium mb-2">Terdapat kesalahan dalam pengisian form:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Status Check -->
@if($penalti->status !== 'active')
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Peringatan Status Penalti</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Penalti ini memiliki status <strong>{{ ucfirst($penalti->status) }}</strong>. Hanya penalti dengan status <strong>Aktif</strong> yang dapat diedit.</p>
                <p class="mt-1">Silakan ubah status penalti ke "Aktif" terlebih dahulu jika ingin melakukan perubahan.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Current Penalti Info -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-blue-800">Informasi Penalti Saat Ini</h3>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700">
                <div>
                    <p><strong>ID Penalti:</strong> {{ $penalti->penalti_id }}</p>
                    <p><strong>Karyawan:</strong> {{ $penalti->karyawan->full_name }}</p>
                    <p><strong>Jenis:</strong> {{ ucfirst(str_replace('_', ' ', $penalti->jenis_penalti)) }}</p>
                </div>
                <div>
                    <p><strong>Status:</strong> {{ ucfirst($penalti->status) }}</p>
                    <p><strong>Hari Potong:</strong> {{ $penalti->hari_potong_uang_makan }} hari</p>
                    <p><strong>Dibuat:</strong> {{ $penalti->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Edit Form Penalti</h2>
                <p class="text-sm text-gray-600 mt-1">Perbarui informasi penalti karyawan</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.penalti.update', $penalti->penalti_id) }}" method="POST" id="penaltiEditForm" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Karyawan Selection -->
                <div>
                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Karyawan
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <select name="karyawan_id" id="karyawan_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('karyawan_id') border-red-300 @enderror" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->karyawan_id }}"
                                {{ (old('karyawan_id', $penalti->karyawan_id) == $karyawan->karyawan_id) ? 'selected' : '' }}
                                data-nip="{{ $karyawan->nip }}"
                                data-department="{{ $karyawan->department->name ?? '' }}"
                                data-position="{{ $karyawan->position }}">
                                {{ $karyawan->full_name }} - {{ $karyawan->nip }}
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Karyawan Info Display -->
                    <div id="karyawan-info" class="mt-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="mr-4">
                                <span class="font-medium">NIP:</span>
                                <span id="display-nip">{{ $penalti->karyawan->nip }}</span>
                            </div>
                            <div class="mr-4">
                                <span class="font-medium">Departemen:</span>
                                <span id="display-department">{{ $penalti->karyawan->department->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Posisi:</span>
                                <span id="display-position">{{ $penalti->karyawan->position }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Absen Reference (if exists) -->
                @if($penalti->absen)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Referensi Absensi
                        </span>
                    </label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                        <p><strong>Tanggal:</strong> {{ $penalti->absen->date->format('d M Y') }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($penalti->absen->status) }}</p>
                        @if($penalti->absen->late_minutes > 0)
                            <p><strong>Terlambat:</strong> {{ $penalti->absen->late_minutes }} menit</p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Jenis Penalti -->
                <div>
                    <label for="jenis_penalti" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Jenis Penalti
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($jenisOptions as $jenis)
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors jenis-option {{ old('jenis_penalti', $penalti->jenis_penalti) == $jenis ? 'border-primary-500 bg-primary-50' : '' }}">
                                <input type="radio" name="jenis_penalti" value="{{ $jenis }}"
                                    class="text-primary-600 focus:ring-primary-500"
                                    {{ old('jenis_penalti', $penalti->jenis_penalti) == $jenis ? 'checked' : '' }} required>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 capitalize">
                                        {{ str_replace('_', ' ', $jenis) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($jenis == 'telat')
                                            Untuk keterlambatan masuk
                                        @elseif($jenis == 'tidak_masuk')
                                            Untuk tidak hadir tanpa keterangan
                                        @elseif($jenis == 'pelanggaran')
                                            Untuk pelanggaran aturan
                                        @else
                                            Penalti khusus lainnya
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis_penalti')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Deskripsi Penalti
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('deskripsi') border-red-300 @enderror"
                        placeholder="Jelaskan detail penalti yang diberikan..." required>{{ old('deskripsi', $penalti->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hari Potong -->
                <div>
                    <label for="hari_potong_uang_makan" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            Hari Potong Insentif Kehadiran
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <div class="relative">
                        <input type="number" name="hari_potong_uang_makan" id="hari_potong_uang_makan" min="0" max="31"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-16 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('hari_potong_uang_makan') border-red-300 @enderror"
                            placeholder="0" value="{{ old('hari_potong_uang_makan', $penalti->hari_potong_uang_makan) }}" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">hari</span>
                        </div>
                    </div>
                    @error('hari_potong_uang_makan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Jumlah hari insentif kehadiran yang akan dipotong (0-31 hari)</p>
                    @if($penalti->hari_potong_uang_makan != old('hari_potong_uang_makan', $penalti->hari_potong_uang_makan))
                        <p class="text-xs text-blue-600 mt-1">Nilai lama: {{ $penalti->hari_potong_uang_makan }} hari</p>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Tanggal Penalti -->
                <div>
                    <label for="tanggal_penalti" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Tanggal Penalti
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <input type="date" name="tanggal_penalti" id="tanggal_penalti"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('tanggal_penalti') border-red-300 @enderror"
                        value="{{ old('tanggal_penalti', $penalti->tanggal_penalti->format('Y-m-d')) }}" required>
                    @error('tanggal_penalti')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Periode Berlaku -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="periode_berlaku_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Berlaku Mulai
                                <span class="text-red-500 ml-1">*</span>
                            </span>
                        </label>
                        <input type="date" name="periode_berlaku_mulai" id="periode_berlaku_mulai"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('periode_berlaku_mulai') border-red-300 @enderror"
                            value="{{ old('periode_berlaku_mulai', $penalti->periode_berlaku_mulai->format('Y-m-d')) }}" required>
                        @error('periode_berlaku_mulai')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="periode_berlaku_akhir" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Berlaku Sampai
                                <span class="text-red-500 ml-1">*</span>
                            </span>
                        </label>
                        <input type="date" name="periode_berlaku_akhir" id="periode_berlaku_akhir"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('periode_berlaku_akhir') border-red-300 @enderror"
                            value="{{ old('periode_berlaku_akhir', $penalti->periode_berlaku_akhir->format('Y-m-d')) }}" required>
                        @error('periode_berlaku_akhir')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Period Info -->
                <div id="period-info" class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Informasi Periode:</p>
                            <p id="period-duration">-</p>
                            <p class="text-xs text-blue-600 mt-1">Potongan insentif kehadiran akan berlaku selama periode ini</p>
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Catatan
                        </span>
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('notes') border-red-300 @enderror"
                        placeholder="Tambahkan catatan tambahan jika diperlukan...">{{ old('notes', $penalti->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Change Log Display -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Riwayat Penalti
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dibuat:</span>
                            <span class="font-medium text-gray-900">{{ $penalti->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($penalti->createdBy)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dibuat oleh:</span>
                            <span class="font-medium text-gray-900">{{ $penalti->createdBy->name }}</span>
                        </div>
                        @endif
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
                        @endif
                    </div>
                </div>

                <!-- Impact Summary -->
                @if($penalti->tunjanganKaryawan()->exists())
                <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <h4 class="text-sm font-medium text-orange-800 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Dampak Penalti
                    </h4>
                    <div class="text-sm text-orange-700">
                        <p class="mb-2">Penalti ini telah mempengaruhi {{ $penalti->tunjanganKaryawan()->count() }} tunjangan karyawan.</p>
                        <p class="text-xs">Perubahan pada penalti akan mempengaruhi perhitungan tunjangan yang terkait.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="button" onclick="resetToOriginal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Reset ke Asli
            </button>
            <a href="{{ route('admin.penalti.show', $penalti->penalti_id) }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2" {{ $penalti->status !== 'active' ? 'disabled' : '' }}>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Penalti
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// Original values for reset functionality
const originalValues = {
    karyawan_id: '{{ $penalti->karyawan_id }}',
    jenis_penalti: '{{ $penalti->jenis_penalti }}',
    deskripsi: `{{ $penalti->deskripsi }}`,
    hari_potong_uang_makan: '{{ $penalti->hari_potong_uang_makan }}',
    tanggal_penalti: '{{ $penalti->tanggal_penalti->format('Y-m-d') }}',
    periode_berlaku_mulai: '{{ $penalti->periode_berlaku_mulai->format('Y-m-d') }}',
    periode_berlaku_akhir: '{{ $penalti->periode_berlaku_akhir->format('Y-m-d') }}',
    notes: `{{ $penalti->notes ?? '' }}`
};

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const karyawanSelect = document.getElementById('karyawan_id');
    const karyawanInfo = document.getElementById('karyawan-info');
    const jenisRadios = document.querySelectorAll('input[name="jenis_penalti"]');
    const periodeMultiInput = document.getElementById('periode_berlaku_mulai');
    const periodeAkhirInput = document.getElementById('periode_berlaku_akhir');
    const periodInfo = document.getElementById('period-info');
    const periodDuration = document.getElementById('period-duration');

    // Karyawan selection handler
    karyawanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            document.getElementById('display-nip').textContent = selectedOption.dataset.nip || '-';
            document.getElementById('display-department').textContent = selectedOption.dataset.department || '-';
            document.getElementById('display-position').textContent = selectedOption.dataset.position || '-';
        }
    });

    // Jenis penalti handler
    jenisRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                // Update visual
                document.querySelectorAll('.jenis-option').forEach(option => {
                    option.classList.remove('border-primary-500', 'bg-primary-50');
                    option.classList.add('border-gray-300');
                });

                this.closest('.jenis-option').classList.remove('border-gray-300');
                this.closest('.jenis-option').classList.add('border-primary-500', 'bg-primary-50');
            }
        });
    });

    // Period handler
    function updatePeriodInfo() {
        const mulai = periodeMultiInput.value;
        const akhir = periodeAkhirInput.value;

        if (mulai && akhir) {
            const startDate = new Date(mulai);
            const endDate = new Date(akhir);
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            periodDuration.textContent = `Durasi: ${diffDays} hari (${formatDate(mulai)} s/d ${formatDate(akhir)})`;
        }
    }

    periodeMultiInput.addEventListener('change', updatePeriodInfo);
    periodeAkhirInput.addEventListener('change', updatePeriodInfo);

    // Format date helper
    function formatDate(dateString) {
        const date = new Date(dateString);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }

    // Form validation
    document.getElementById('penaltiEditForm').addEventListener('submit', function(e) {
        const mulai = new Date(periodeMultiInput.value);
        const akhir = new Date(periodeAkhirInput.value);

        if (akhir < mulai) {
            e.preventDefault();
            alert('Tanggal akhir periode tidak boleh lebih awal dari tanggal mulai');
            periodeAkhirInput.focus();
            return;
        }

        // Confirm if status is not active
        @if($penalti->status !== 'active')
        e.preventDefault();
        alert('Penalti dengan status "{{ ucfirst($penalti->status) }}" tidak dapat diubah!');
        return;
        @endif

        // Check for significant changes
        const currentValues = {
            karyawan_id: document.getElementById('karyawan_id').value,
            jenis_penalti: document.querySelector('input[name="jenis_penalti"]:checked')?.value,
            deskripsi: document.getElementById('deskripsi').value,
            hari_potong_uang_makan: document.getElementById('hari_potong_uang_makan').value,
            tanggal_penalti: document.getElementById('tanggal_penalti').value,
            periode_berlaku_mulai: document.getElementById('periode_berlaku_mulai').value,
            periode_berlaku_akhir: document.getElementById('periode_berlaku_akhir').value,
            notes: document.getElementById('notes').value
        };

        let hasChanges = false;
        for (const key in currentValues) {
            if (currentValues[key] !== originalValues[key]) {
                hasChanges = true;
                break;
            }
        }

        if (!hasChanges) {
            e.preventDefault();
            alert('Tidak ada perubahan yang dibuat pada form ini.');
            return;
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Memperbarui...
        `;

        // Re-enable after 3 seconds in case of error
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 3000);
    });

    // Initialize
    updatePeriodInfo();

    // Trigger change event for selected jenis
    const selectedJenis = document.querySelector('input[name="jenis_penalti"]:checked');
    if (selectedJenis) {
        selectedJenis.dispatchEvent(new Event('change'));
    }
});

// Reset to original function
function resetToOriginal() {
    if (confirm('Apakah Anda yakin ingin mengembalikan semua nilai ke data asli? Perubahan yang belum disimpan akan hilang.')) {
        // Reset form values
        document.getElementById('karyawan_id').value = originalValues.karyawan_id;
        document.getElementById('deskripsi').value = originalValues.deskripsi;
        document.getElementById('hari_potong_uang_makan').value = originalValues.hari_potong_uang_makan;
        document.getElementById('tanggal_penalti').value = originalValues.tanggal_penalti;
        document.getElementById('periode_berlaku_mulai').value = originalValues.periode_berlaku_mulai;
        document.getElementById('periode_berlaku_akhir').value = originalValues.periode_berlaku_akhir;
        document.getElementById('notes').value = originalValues.notes;

        // Reset jenis penalti
        document.querySelector(`input[name="jenis_penalti"][value="${originalValues.jenis_penalti}"]`).checked = true;

        // Reset visual states
        document.querySelectorAll('.jenis-option').forEach(option => {
            option.classList.remove('border-primary-500', 'bg-primary-50');
            option.classList.add('border-gray-300');
        });

        const selectedOption = document.querySelector(`input[name="jenis_penalti"][value="${originalValues.jenis_penalti}"]`);
        if (selectedOption) {
            selectedOption.closest('.jenis-option').classList.remove('border-gray-300');
            selectedOption.closest('.jenis-option').classList.add('border-primary-500', 'bg-primary-50');
        }

        // Trigger change events
        document.getElementById('karyawan_id').dispatchEvent(new Event('change'));
        document.getElementById('periode_berlaku_mulai').dispatchEvent(new Event('change'));

        showToast('Form telah direset ke nilai asli', 'success');
    }
}

// Track form changes for unsaved changes warning
let formChanged = false;
const form = document.getElementById('penaltiEditForm');
const inputs = form.querySelectorAll('input, textarea, select');

inputs.forEach(input => {
    input.addEventListener('change', function() {
        formChanged = true;
    });
});

// Warn before leaving page if there are unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

// Clear flag when form is submitted
form.addEventListener('submit', function() {
    formChanged = false;
});

// Show changes indicator
function showChangeIndicator(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field && !field.closest('.field-container').querySelector('.change-indicator')) {
        const indicator = document.createElement('span');
        indicator.className = 'change-indicator text-xs text-blue-600 ml-2';
        indicator.innerHTML = '• Modified';
        field.closest('.field-container').appendChild(indicator);
    }
}

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

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        if (document.getElementById('penaltiEditForm').checkValidity()) {
            document.getElementById('penaltiEditForm').submit();
        }
    }

    // Escape to cancel
    if (e.key === 'Escape') {
        if (confirm('Batalkan perubahan dan kembali ke daftar penalti?')) {
            window.location.href = '{{ route("admin.penalti.index") }}';
        }
    }
});
</script>
@endpush
