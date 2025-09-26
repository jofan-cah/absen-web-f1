@extends('admin.layouts.app')

@section('title', 'Tambah Penalti')
@section('breadcrumb', 'Manajemen Penalti / Tambah')
@section('page_title', 'Tambah Penalti Karyawan')

@section('page_actions')
<div class="flex gap-3">
    <a href="{{ route('admin.penalti.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
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

<!-- Info Card jika dari absen -->
@if($absen)
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Penalti dari Absensi</h3>
            <div class="mt-2 text-sm text-blue-700">
                <p><strong>Karyawan:</strong> {{ $absen->karyawan->full_name }}</p>
                <p><strong>Tanggal:</strong> {{ $absen->date->format('d M Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($absen->status) }}</p>
                @if($absen->late_minutes > 0)
                    <p><strong>Terlambat:</strong> {{ $absen->late_minutes }} menit</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Main Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Form Penalti Karyawan</h2>
                <p class="text-sm text-gray-600 mt-1">Isi informasi penalti yang akan diberikan kepada karyawan</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.penalti.store') }}" method="POST" id="penaltiForm" class="p-6">
        @csrf

        <!-- Hidden field jika dari absen -->
        @if($absen)
            <input type="hidden" name="absen_id" value="{{ $absen->absen_id }}">
        @endif

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
                    <select name="karyawan_id" id="karyawan_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('karyawan_id') border-red-300 @enderror" required {{ $absen ? 'disabled' : '' }}>
                        <option value="">Pilih Karyawan</option>
                        @foreach($karyawans as $karyawan)
                            <option value="{{ $karyawan->karyawan_id }}"
                                {{ (old('karyawan_id') == $karyawan->karyawan_id || ($absen && $absen->karyawan_id == $karyawan->karyawan_id)) ? 'selected' : '' }}
                                data-nip="{{ $karyawan->nip }}"
                                data-department="{{ $karyawan->department->name ?? '' }}"
                                data-position="{{ $karyawan->position }}">
                                {{ $karyawan->full_name }} - {{ $karyawan->nip }}
                            </option>
                        @endforeach
                    </select>
                    @if($absen)
                        <input type="hidden" name="karyawan_id" value="{{ $absen->karyawan_id }}">
                    @endif
                    @error('karyawan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Karyawan Info Display -->
                    <div id="karyawan-info" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="mr-4">
                                <span class="font-medium">NIP:</span>
                                <span id="display-nip">-</span>
                            </div>
                            <div class="mr-4">
                                <span class="font-medium">Departemen:</span>
                                <span id="display-department">-</span>
                            </div>
                            <div>
                                <span class="font-medium">Posisi:</span>
                                <span id="display-position">-</span>
                            </div>
                        </div>
                    </div>
                </div>

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
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors jenis-option">
                                <input type="radio" name="jenis_penalti" value="{{ $jenis }}"
                                    class="text-primary-600 focus:ring-primary-500"
                                    {{ old('jenis_penalti') == $jenis ? 'checked' : '' }} required>
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
                        placeholder="Jelaskan detail penalti yang diberikan..." required>{{ old('deskripsi') }}</textarea>
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
                            Hari Potong Uang Makan
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <div class="relative">
                        <input type="number" name="hari_potong_uang_makan" id="hari_potong_uang_makan" min="0" max="31"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-16 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('hari_potong_uang_makan') border-red-300 @enderror"
                            placeholder="0" value="{{ old('hari_potong_uang_makan') }}" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">hari</span>
                        </div>
                    </div>
                    @error('hari_potong_uang_makan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Jumlah hari uang makan yang akan dipotong (0-31 hari)</p>
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
                        value="{{ old('tanggal_penalti', ($absen ? $absen->date->format('Y-m-d') : date('Y-m-d'))) }}" required>
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
                            value="{{ old('periode_berlaku_mulai', date('Y-m-d')) }}" required>
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
                            value="{{ old('periode_berlaku_akhir', date('Y-m-t')) }}" required>
                        @error('periode_berlaku_akhir')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Period Info -->
                <div id="period-info" class="p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Informasi Periode:</p>
                            <p id="period-duration">-</p>
                            <p class="text-xs text-blue-600 mt-1">Potongan uang makan akan berlaku selama periode ini</p>
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
                            Catatan (Opsional)
                        </span>
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('notes') border-red-300 @enderror"
                        placeholder="Tambahkan catatan tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Summary Card -->
                <div id="summary-card" class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ringkasan Penalti
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Karyawan:</span>
                            <span id="summary-karyawan" class="font-medium text-gray-900">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis Penalti:</span>
                            <span id="summary-jenis" class="font-medium text-gray-900">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hari Potong:</span>
                            <span id="summary-hari" class="font-medium text-red-600">0 hari</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Periode:</span>
                            <span id="summary-periode" class="font-medium text-gray-900">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
            <button type="button" onclick="resetForm()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Reset Form
            </button>
            <a href="{{ route('admin.penalti.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Penalti
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const karyawanSelect = document.getElementById('karyawan_id');
    const karyawanInfo = document.getElementById('karyawan-info');
    const jenisRadios = document.querySelectorAll('input[name="jenis_penalti"]');
    const hariPotongInput = document.getElementById('hari_potong_uang_makan');
    const periodeMultiInput = document.getElementById('periode_berlaku_mulai');
    const periodeAkhirInput = document.getElementById('periode_berlaku_akhir');
    const periodInfo = document.getElementById('period-info');
    const periodDuration = document.getElementById('period-duration');

    // Summary elements
    const summaryKaryawan = document.getElementById('summary-karyawan');
    const summaryJenis = document.getElementById('summary-jenis');
    const summaryHari = document.getElementById('summary-hari');
    const summaryPeriode = document.getElementById('summary-periode');

    // Karyawan selection handler
    karyawanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            karyawanInfo.classList.remove('hidden');
            document.getElementById('display-nip').textContent = selectedOption.dataset.nip || '-';
            document.getElementById('display-department').textContent = selectedOption.dataset.department || '-';
            document.getElementById('display-position').textContent = selectedOption.dataset.position || '-';

            summaryKaryawan.textContent = selectedOption.text;
        } else {
            karyawanInfo.classList.add('hidden');
            summaryKaryawan.textContent = '-';
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

                // Update summary
                summaryJenis.textContent = this.value.replace('_', ' ').charAt(0).toUpperCase() + this.value.replace('_', ' ').slice(1);

                // Auto-fill suggestions based on jenis penalti
                const deskripsiInput = document.getElementById('deskripsi');
                let suggestion = '';

                switch(this.value) {
                    case 'telat':
                        suggestion = 'Terlambat masuk kerja ';
                        break;
                    case 'tidak_masuk':
                        suggestion = 'Tidak masuk kerja tanpa keterangan pada tanggal ';
                        break;
                    case 'pelanggaran':
                        suggestion = 'Melanggar peraturan perusahaan: ';
                        break;
                    case 'custom':
                        suggestion = '';
                        break;
                }

                if (suggestion && !deskripsiInput.value) {
                    deskripsiInput.value = suggestion;
                }
            }
        });
    });

    // Hari potong handler
    hariPotongInput.addEventListener('input', function() {
        const value = parseInt(this.value) || 0;
        summaryHari.textContent = value + ' hari';
        summaryHari.className = value > 0 ? 'font-medium text-red-600' : 'font-medium text-gray-900';
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

            periodInfo.classList.remove('hidden');
            periodDuration.textContent = `Durasi: ${diffDays} hari (${formatDate(mulai)} s/d ${formatDate(akhir)})`;
            summaryPeriode.textContent = `${diffDays} hari`;
        } else {
            periodInfo.classList.add('hidden');
            summaryPeriode.textContent = '-';
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
    document.getElementById('penaltiForm').addEventListener('submit', function(e) {
        const mulai = new Date(periodeMultiInput.value);
        const akhir = new Date(periodeAkhirInput.value);

        if (akhir < mulai) {
            e.preventDefault();
            alert('Tanggal akhir periode tidak boleh lebih awal dari tanggal mulai');
            periodeAkhirInput.focus();
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
            Menyimpan...
        `;

        // Re-enable after 3 seconds in case of error
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 3000);
    });

    // Auto-set periode akhir when periode mulai changes
    periodeMultiInput.addEventListener('change', function() {
        if (this.value && !periodeAkhirInput.value) {
            const startDate = new Date(this.value);
            const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0); // Last day of month
            periodeAkhirInput.value = endDate.toISOString().split('T')[0];
            updatePeriodInfo();
        }
    });

    // Initialize if there's existing data
    if (karyawanSelect.value) {
        karyawanSelect.dispatchEvent(new Event('change'));
    }

    const checkedJenis = document.querySelector('input[name="jenis_penalti"]:checked');
    if (checkedJenis) {
        checkedJenis.dispatchEvent(new Event('change'));
    }

    if (hariPotongInput.value) {
        hariPotongInput.dispatchEvent(new Event('input'));
    }

    updatePeriodInfo();
});

// Reset form function
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang telah diisi akan hilang.')) {
        document.getElementById('penaltiForm').reset();

        // Reset visual states
        document.querySelectorAll('.jenis-option').forEach(option => {
            option.classList.remove('border-primary-500', 'bg-primary-50');
            option.classList.add('border-gray-300');
        });

        document.getElementById('karyawan-info').classList.add('hidden');
        document.getElementById('period-info').classList.add('hidden');

        // Reset summary
        document.getElementById('summary-karyawan').textContent = '-';
        document.getElementById('summary-jenis').textContent = '-';
        document.getElementById('summary-hari').textContent = '0 hari';
        document.getElementById('summary-periode').textContent = '-';

        // Set default dates
        document.getElementById('tanggal_penalti').value = new Date().toISOString().split('T')[0];
        document.getElementById('periode_berlaku_mulai').value = new Date().toISOString().split('T')[0];

        const endOfMonth = new Date();
        endOfMonth.setMonth(endOfMonth.getMonth() + 1, 0);
        document.getElementById('periode_berlaku_akhir').value = endOfMonth.toISOString().split('T')[0];
    }
}

// Auto-suggestions based on jenis penalti
function getJenisSuggestions(jenis) {
    const suggestions = {
        'telat': [
            'Terlambat masuk kerja lebih dari 15 menit',
            'Terlambat masuk kerja tanpa pemberitahuan',
            'Sering terlambat dalam 1 minggu terakhir'
        ],
        'tidak_masuk': [
            'Tidak masuk kerja tanpa keterangan',
            'Tidak masuk kerja tanpa izin',
            'Alpha dari pekerjaan'
        ],
        'pelanggaran': [
            'Melanggar dress code perusahaan',
            'Tidak mematuhi protokol keselamatan',
            'Melanggar peraturan area kerja'
        ],
        'custom': [
            'Penalti khusus sesuai kebijakan'
        ]
    };

    return suggestions[jenis] || [];
}

// Add suggestion dropdown (optional enhancement)
function showSuggestions(jenis) {
    const deskripsiInput = document.getElementById('deskripsi');
    const suggestions = getJenisSuggestions(jenis);

    if (suggestions.length > 0 && !deskripsiInput.value) {
        // Create suggestion dropdown
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg';
        suggestionDiv.innerHTML = suggestions.map(suggestion =>
            `<div class="px-4 py-2 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100 last:border-b-0" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`
        ).join('');

        // Remove existing suggestions
        const existing = document.querySelector('.suggestion-dropdown');
        if (existing) existing.remove();

        suggestionDiv.className += ' suggestion-dropdown';
        deskripsiInput.parentNode.style.position = 'relative';
        deskripsiInput.parentNode.appendChild(suggestionDiv);

        // Hide suggestions after 5 seconds
        setTimeout(() => {
            if (suggestionDiv.parentNode) {
                suggestionDiv.remove();
            }
        }, 5000);
    }
}

function selectSuggestion(suggestion) {
    document.getElementById('deskripsi').value = suggestion;
    const suggestionDiv = document.querySelector('.suggestion-dropdown');
    if (suggestionDiv) suggestionDiv.remove();
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    const suggestionDiv = document.querySelector('.suggestion-dropdown');
    if (suggestionDiv && !suggestionDiv.contains(e.target) && e.target.id !== 'deskripsi') {
        suggestionDiv.remove();
    }
});
</script>
@endpush
