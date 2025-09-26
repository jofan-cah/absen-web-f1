@extends('admin.layouts.app')

@section('title', 'Laporan Tunjangan PDF')
@section('breadcrumb', 'Laporan / Tunjangan')
@section('page_title', 'Generate Laporan Tunjangan PDF')

@section('page_actions')
<div class="flex gap-3">
    <a href="{{ route('admin.tunjangan-karyawan.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
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
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
    <p class="font-medium mb-2">Terdapat kesalahan:</p>
    <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Info Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-orange-400 to-red-500 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Laporan Mingguan</h3>
                <p class="text-sm opacity-90">Tabel semua karyawan per minggu dalam bulan</p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Laporan Bulanan</h3>
                <p class="text-sm opacity-90">Daftar status pengambilan bulanan semua karyawan</p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Laporan Harian</h3>
                <p class="text-sm opacity-90">Detail pengambilan harian semua karyawan</p>
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Generate Laporan PDF</h2>
                <p class="text-sm text-gray-600 mt-1">Buat laporan tunjangan untuk semua karyawan dalam format PDF</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.tunjangan-karyawan.all-employee-report') }}" method="POST" target="_blank" id="reportForm" class="p-6">
        @csrf

        <div class="space-y-6">
            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="tunjangan_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Jenis Tunjangan
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <select name="tunjangan_type_id" id="tunjangan_type_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Jenis Tunjangan</option>
                        @foreach($tunjanganTypes as $type)
                            <option value="{{ $type->tunjangan_type_id }}" data-category="{{ $type->category }}">
                                {{ $type->name }} ({{ ucfirst($type->category) }})
                            </option>
                        @endforeach
                    </select>
                    @error('tunjangan_type_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Bulan
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <select name="month" id="month" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                    @error('month')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Tahun
                            <span class="text-red-500 ml-1">*</span>
                        </span>
                    </label>
                    <select name="year" id="year" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Preview Info -->
            <div id="preview-info" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Preview Laporan
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jenis Laporan:</span>
                        <span id="preview-type" class="font-medium text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Periode:</span>
                        <span id="preview-period" class="font-medium text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Format:</span>
                        <span class="font-medium text-gray-900">PDF - Semua Karyawan Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Info berdasarkan kategori -->
            <div id="category-info" class="hidden">
                <!-- Info Mingguan -->
                <div id="info-mingguan" class="category-info-item hidden p-4 bg-orange-50 border border-orange-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-orange-800">
                            <p class="font-medium">Laporan Mingguan:</p>
                            <p>Tabel semua karyawan dengan kolom minggu 1-4 dalam bulan yang dipilih. Status pengambilan ditampilkan dengan ✓ (sudah diambil) atau ✗ (belum diambil). Format landscape untuk tabel yang lebar.</p>
                        </div>
                    </div>
                </div>

                <!-- Info Bulanan -->
                <div id="info-bulanan" class="category-info-item hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Laporan Bulanan:</p>
                            <p>Daftar semua karyawan dengan status pengambilan tunjangan bulanan, tanggal approval, dan tanggal pengambilan. Includes notice untuk yang belum mengambil.</p>
                        </div>
                    </div>
                </div>

                <!-- Info Harian -->
                <div id="info-harian" class="category-info-item hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-green-800">
                            <p class="font-medium">Laporan Harian:</p>
                            <p>Detail pengambilan tunjangan harian untuk semua karyawan dalam bulan yang dipilih, dengan breakdown per hari kerja dan status masing-masing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.tunjangan-karyawan.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Generate PDF Laporan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tunjanganSelect = document.getElementById('tunjangan_type_id');
    const monthSelect = document.getElementById('month');
    const yearSelect = document.getElementById('year');
    const previewInfo = document.getElementById('preview-info');
    const categoryInfo = document.getElementById('category-info');
    const categoryInfoItems = document.querySelectorAll('.category-info-item');

    function updatePreview() {
        const selectedOption = tunjanganSelect.options[tunjanganSelect.selectedIndex];
        const month = monthSelect.value;
        const year = yearSelect.value;

        if (selectedOption.value && month && year) {
            const category = selectedOption.dataset.category;
            const monthName = monthSelect.options[monthSelect.selectedIndex].text;

            document.getElementById('preview-type').textContent =
                selectedOption.text + ' - ' + (category === 'mingguan' ? 'Per Minggu' :
                category === 'bulanan' ? 'Per Bulan' : 'Per Hari');
            document.getElementById('preview-period').textContent = monthName + ' ' + year;

            previewInfo.classList.remove('hidden');

            // Show category info
            categoryInfo.classList.remove('hidden');
            categoryInfoItems.forEach(item => item.classList.add('hidden'));
            document.getElementById('info-' + category)?.classList.remove('hidden');
        } else {
            previewInfo.classList.add('hidden');
            categoryInfo.classList.add('hidden');
        }
    }

    tunjanganSelect.addEventListener('change', updatePreview);
    monthSelect.addEventListener('change', updatePreview);
    yearSelect.addEventListener('change', updatePreview);

    // Form validation
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        if (!tunjanganSelect.value) {
            e.preventDefault();
            alert('Pilih jenis tunjangan terlebih dahulu!');
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
            Generating PDF...
        `;

        // Re-enable after timeout
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 5000);
    });

    // Initialize
    updatePreview();
});
</script>

@endsection
