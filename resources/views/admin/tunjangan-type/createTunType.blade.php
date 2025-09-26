@extends('admin.layouts.app')

@section('title', 'Tambah Jenis Tunjangan')
@section('breadcrumb', 'Tambah Jenis Tunjangan')
@section('page_title', 'Tambah Jenis Tunjangan Baru')

@section('page_actions')
<div class="flex gap-2">
    <a href="{{ route('admin.tunjangan-type.index') }}"
       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Form Tambah Jenis Tunjangan</h3>
        <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah untuk menambahkan jenis tunjangan baru</p>
    </div>

    <div class="p-6">
        <form action="{{ route('admin.tunjangan-type.store') }}" method="POST" id="tunjanganTypeForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Nama Jenis Tunjangan -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Jenis Tunjangan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Uang Makan"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Nama jenis tunjangan yang mudah dipahami</p>
                    </div>

                    <!-- Kode Jenis Tunjangan -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Jenis Tunjangan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="code"
                               name="code"
                               value="{{ old('code') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-500 @enderror"
                               placeholder="Contoh: UANG_MAKAN"
                               maxlength="50"
                               required>
                        @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Kode unik untuk sistem (akan diubah ke huruf besar)</p>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori Frekuensi <span class="text-red-500">*</span>
                        </label>
                        <select id="category"
                                name="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('category') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="harian" {{ old('category') == 'harian' ? 'selected' : '' }}>
                                Harian - Tunjangan per hari (misal: lembur)
                            </option>
                            <option value="mingguan" {{ old('category') == 'mingguan' ? 'selected' : '' }}>
                                Mingguan - Tunjangan per minggu (misal: uang makan)
                            </option>
                            <option value="bulanan" {{ old('category') == 'bulanan' ? 'selected' : '' }}>
                                Bulanan - Tunjangan per bulan (misal: kuota internet)
                            </option>
                        </select>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Frekuensi pemberian tunjangan</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Nominal Dasar -->
                    <div>
                        <label for="base_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal Dasar <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number"
                                   id="base_amount"
                                   name="base_amount"
                                   value="{{ old('base_amount') }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('base_amount') border-red-500 @enderror"
                                   placeholder="50000"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                        @error('base_amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Nominal dasar sebagai referensi (akan ada detail per staff status)</p>
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Status</label>
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-3 block text-sm text-gray-900">
                                Aktifkan jenis tunjangan ini
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Jenis tunjangan yang tidak aktif tidak bisa digunakan untuk transaksi</p>
                    </div>

                    <!-- Preview Icon -->
                    <div id="icon-preview" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Preview Icon</label>
                        <div class="flex items-center space-x-3">
                            <div id="preview-icon" class="h-12 w-12 rounded-xl flex items-center justify-center">
                                <!-- Icon will be updated by JavaScript -->
                            </div>
                            <div>
                                <div id="preview-category" class="text-sm font-medium text-gray-900"></div>
                                <div class="text-xs text-gray-500">Icon berdasarkan kategori yang dipilih</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror"
                          placeholder="Deskripsi singkat tentang jenis tunjangan ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Penjelasan detail tentang jenis tunjangan ini (opsional)</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Setelah membuat jenis tunjangan, Anda perlu menambahkan <strong>Detail Nominal</strong> untuk setiap status karyawan (PKWTT, karyawan, koordinator, dll.)</li>
                                <li>Nominal dasar hanya sebagai referensi, nominal aktual akan diambil dari detail nominal</li>
                                <li>Kode jenis tunjangan harus unik dan akan digunakan untuk identifikasi sistem</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.tunjangan-type.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Jenis Tunjangan
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category change handler for icon preview
    const categorySelect = document.getElementById('category');
    const iconPreview = document.getElementById('icon-preview');
    const previewIcon = document.getElementById('preview-icon');
    const previewCategory = document.getElementById('preview-category');

    function updateIconPreview(category) {
        if (!category) {
            iconPreview.classList.add('hidden');
            return;
        }

        iconPreview.classList.remove('hidden');

        // Clear existing classes and set base classes
        previewIcon.className = 'h-12 w-12 rounded-xl flex items-center justify-center';

        let iconHtml = '';
        let categoryText = '';

        switch(category) {
            case 'harian':
                previewIcon.classList.add('bg-gradient-to-br', 'from-orange-400', 'to-red-500');
                categoryText = 'Tunjangan Harian';
                iconHtml = `
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                `;
                break;
            case 'mingguan':
                previewIcon.classList.add('bg-gradient-to-br', 'from-blue-400', 'to-cyan-500');
                categoryText = 'Tunjangan Mingguan';
                iconHtml = `
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                `;
                break;
            case 'bulanan':
                previewIcon.classList.add('bg-gradient-to-br', 'from-green-400', 'to-emerald-500');
                categoryText = 'Tunjangan Bulanan';
                iconHtml = `
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                `;
                break;
        }

        previewIcon.innerHTML = iconHtml;
        previewCategory.textContent = categoryText;
    }

    // Initial load
    updateIconPreview(categorySelect.value);

    // Listen for changes
    categorySelect.addEventListener('change', function() {
        updateIconPreview(this.value);
    });

    // Auto uppercase for code field
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
    });

    // Auto generate code from name
    const nameInput = document.getElementById('name');
    nameInput.addEventListener('input', function() {
        if (!codeInput.value || codeInput.dataset.autoGenerated === 'true') {
            const generatedCode = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .substring(0, 50);

            codeInput.value = generatedCode;
            codeInput.dataset.autoGenerated = 'true';
        }
    });

    // Mark code as manually edited
    codeInput.addEventListener('keydown', function() {
        this.dataset.autoGenerated = 'false';
    });

    // Format currency input
    const baseAmountInput = document.getElementById('base_amount');
    baseAmountInput.addEventListener('input', function() {
        // Remove any non-digit characters except decimal point
        let value = this.value.replace(/[^\d]/g, '');

        // Ensure it's a valid number
        if (value === '') {
            this.value = '';
            return;
        }

        // Update the value
        this.value = value;
    });

    // Form validation
    const form = document.getElementById('tunjanganTypeForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = ['name', 'code', 'category', 'base_amount'];
        let hasError = false;

        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                hasError = true;
                field.classList.add('border-red-500');

                // Show error message if not exists
                if (!field.parentNode.querySelector('.error-message')) {
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'mt-2 text-sm text-red-600 error-message';
                    errorMsg.textContent = 'Field ini wajib diisi';
                    field.parentNode.appendChild(errorMsg);
                }
            } else {
                field.classList.remove('border-red-500');
                // Remove error message
                const errorMsg = field.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });

        if (hasError) {
            e.preventDefault();

            // Scroll to first error
            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    // Real-time validation
    const requiredInputs = document.querySelectorAll('input[required], select[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });

        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
});
</script>
@endpush
