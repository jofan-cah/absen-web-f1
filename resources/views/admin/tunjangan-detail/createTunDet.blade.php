@extends('admin.layouts.app')

@section('title', 'Tambah Detail Nominal')
@section('breadcrumb', 'Tambah Detail Nominal')
@section('page_title', 'Tambah Detail Nominal Tunjangan')

@section('page_actions')
<div class="flex gap-2">
    @if(request('tunjangan_type_id'))
        <a href="{{ route('admin.tunjangan-type.show', request('tunjangan_type_id')) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Lihat Jenis Tunjangan
        </a>
    @endif
    <a href="{{ route('admin.tunjangan-detail.index') }}"
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
        <h3 class="text-lg font-semibold text-gray-900">Form Tambah Detail Nominal</h3>
        <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah untuk menambahkan detail nominal per staff status</p>
    </div>

    <div class="p-6">
        <form action="{{ route('admin.tunjangan-detail.store') }}" method="POST" id="tunjanganDetailForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Jenis Tunjangan -->
                    <div>
                        <label for="tunjangan_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Tunjangan <span class="text-red-500">*</span>
                        </label>
                        <select id="tunjangan_type_id"
                                name="tunjangan_type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('tunjangan_type_id') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Jenis Tunjangan</option>
                            @foreach($tunjanganTypes as $type)
                                <option value="{{ $type->tunjangan_type_id }}"
                                        data-category="{{ $type->category }}"
                                        data-base-amount="{{ $type->base_amount }}"
                                        {{ old('tunjangan_type_id', request('tunjangan_type_id')) == $type->tunjangan_type_id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->code }}) - {{ ucfirst($type->category) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tunjangan_type_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Pilih jenis tunjangan yang akan dibuat detail nominalnya</p>
                    </div>

                    <!-- Staff Status -->
                    <div>
                        <label for="staff_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Karyawan <span class="text-red-500">*</span>
                        </label>
                        <select id="staff_status"
                                name="staff_status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('staff_status') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Status Karyawan</option>
                            <option value="pkwtt" {{ old('staff_status') == 'pkwtt' ? 'selected' : '' }}>
                                pkwtt - Karyawan dalam masa pelatihan
                            </option>
                            <option value="staff" {{ old('staff_status') == 'staff' ? 'selected' : '' }}>
                                Karyawan - Karyawan
                            </option>
                            <option value="koordinator" {{ old('staff_status') == 'koordinator' ? 'selected' : '' }}>
                                Koordinator - Level koordinator
                            </option>
                            <option value="wakil_koordinator" {{ old('staff_status') == 'wakil_koordinator' ? 'selected' : '' }}>
                                Wakil Koordinator - Level wakil koordinator
                            </option>
                        </select>
                        @error('staff_status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Status karyawan yang akan menerima nominal ini</p>
                    </div>

                    <!-- Nominal -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('amount') border-red-500 @enderror"
                                   placeholder="25000"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="amount-suggestion" class="mt-1 text-sm text-blue-600 hidden">
                            💡 Saran: Nominal dasar untuk jenis tunjangan ini adalah <span id="base-amount-display"></span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Nominal yang akan diterima karyawan dengan status ini</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Tanggal Berlaku -->
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai Berlaku <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="effective_date"
                               name="effective_date"
                               value="{{ old('effective_date', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('effective_date') border-red-500 @enderror"
                               required>
                        @error('effective_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Tanggal mulai berlakunya nominal ini</p>
                    </div>

                    <!-- Tanggal Berakhir -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Berakhir
                        </label>
                        <input type="date"
                               id="end_date"
                               name="end_date"
                               value="{{ old('end_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika berlaku selamanya</p>
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
                                Aktifkan detail nominal ini
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Detail nominal yang tidak aktif tidak akan digunakan dalam perhitungan</p>
                    </div>

                    <!-- Preview Card -->
                    <div id="preview-card" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div id="preview-icon" class="h-12 w-12 rounded-xl flex items-center justify-center">
                                <!-- Icon akan diupdate oleh JavaScript -->
                            </div>
                            <div class="flex-1">
                                <div id="preview-title" class="font-medium text-gray-900"></div>
                                <div id="preview-staff" class="text-sm text-gray-600"></div>
                                <div id="preview-amount" class="text-sm font-bold text-blue-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Boxes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Validation Info -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Perhatian Validasi</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Sistem akan memvalidasi tidak ada detail nominal aktif yang overlap periode untuk kombinasi yang sama</li>
                                    <li>Jika ada detail nominal lama yang masih aktif, nonaktifkan terlebih dahulu atau atur tanggal berakhir</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi Penggunaan</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Detail nominal ini akan digunakan saat generate tunjangan otomatis</li>
                                    <li>Sistem akan mengambil nominal sesuai staff status karyawan</li>
                                    <li>Nominal dapat berbeda untuk setiap level karyawan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Details Check -->
            <div id="existing-details" class="hidden">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Detail Nominal yang Sudah Ada</h3>
                            <div id="existing-details-content" class="mt-2 text-sm text-yellow-700">
                                <!-- Content akan diisi oleh JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.tunjangan-detail.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="button"
                        onclick="checkExistingDetails()"
                        class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-lg hover:bg-blue-100 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Cek Konflik
                    </span>
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Detail Nominal
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
    // Elements
    const tunjanganTypeSelect = document.getElementById('tunjangan_type_id');
    const staffStatusSelect = document.getElementById('staff_status');
    const amountInput = document.getElementById('amount');
    const previewCard = document.getElementById('preview-card');
    const previewIcon = document.getElementById('preview-icon');
    const previewTitle = document.getElementById('preview-title');
    const previewStaff = document.getElementById('preview-staff');
    const previewAmount = document.getElementById('preview-amount');
    const amountSuggestion = document.getElementById('amount-suggestion');
    const baseAmountDisplay = document.getElementById('base-amount-display');

    // Update preview when selections change
    function updatePreview() {
        const selectedType = tunjanganTypeSelect.options[tunjanganTypeSelect.selectedIndex];
        const selectedStaff = staffStatusSelect.value;
        const amount = amountInput.value;

        if (selectedType.value && selectedStaff && amount) {
            previewCard.classList.remove('hidden');

            const category = selectedType.dataset.category;
            const typeName = selectedType.textContent.split(' (')[0];

            // Update icon based on category
            previewIcon.className = 'h-12 w-12 rounded-xl flex items-center justify-center';
            let iconHtml = '';

            switch(category) {
                case 'harian':
                    previewIcon.classList.add('bg-gradient-to-br', 'from-orange-400', 'to-red-500');
                    iconHtml = `
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    `;
                    break;
                case 'mingguan':
                    previewIcon.classList.add('bg-gradient-to-br', 'from-blue-400', 'to-cyan-500');
                    iconHtml = `
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    `;
                    break;
                case 'bulanan':
                    previewIcon.classList.add('bg-gradient-to-br', 'from-green-400', 'to-emerald-500');
                    iconHtml = `
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    `;
                    break;
            }

            previewIcon.innerHTML = iconHtml;
            previewTitle.textContent = typeName;
            previewStaff.textContent = staffStatusSelect.options[staffStatusSelect.selectedIndex].textContent.split(' - ')[0];
            previewAmount.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount) + ' per ' + category;
        } else {
            previewCard.classList.add('hidden');
        }
    }

    // Show amount suggestion based on base amount
    function showAmountSuggestion() {
        const selectedType = tunjanganTypeSelect.options[tunjanganTypeSelect.selectedIndex];

        if (selectedType.value && selectedType.dataset.baseAmount) {
            const baseAmount = parseInt(selectedType.dataset.baseAmount);
            baseAmountDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(baseAmount);
            amountSuggestion.classList.remove('hidden');
        } else {
            amountSuggestion.classList.add('hidden');
        }
    }

    // Event listeners
    tunjanganTypeSelect.addEventListener('change', function() {
        updatePreview();
        showAmountSuggestion();
    });

    staffStatusSelect.addEventListener('change', updatePreview);
    amountInput.addEventListener('input', updatePreview);

    // Format currency input
    amountInput.addEventListener('input', function() {
        // Remove any non-digit characters
        let value = this.value.replace(/[^\d]/g, '');

        if (value === '') {
            this.value = '';
            return;
        }

        this.value = value;
        updatePreview();
    });

    // Initial load
    if (tunjanganTypeSelect.value) {
        showAmountSuggestion();
        updatePreview();
    }

    // Form validation
    const form = document.getElementById('tunjanganDetailForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = ['tunjangan_type_id', 'staff_status', 'amount', 'effective_date'];
        let hasError = false;

        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                hasError = true;
                field.classList.add('border-red-500');

                if (!field.parentNode.querySelector('.error-message')) {
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'mt-2 text-sm text-red-600 error-message';
                    errorMsg.textContent = 'Field ini wajib diisi';
                    field.parentNode.appendChild(errorMsg);
                }
            } else {
                field.classList.remove('border-red-500');
                const errorMsg = field.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });

        // Validate end date if provided
        const effectiveDate = document.getElementById('effective_date').value;
        const endDate = document.getElementById('end_date').value;

        if (endDate && effectiveDate && endDate <= effectiveDate) {
            hasError = true;
            const endDateField = document.getElementById('end_date');
            endDateField.classList.add('border-red-500');

            if (!endDateField.parentNode.querySelector('.error-message')) {
                const errorMsg = document.createElement('p');
                errorMsg.className = 'mt-2 text-sm text-red-600 error-message';
                errorMsg.textContent = 'Tanggal berakhir harus setelah tanggal mulai berlaku';
                endDateField.parentNode.appendChild(errorMsg);
            }
        }

        if (hasError) {
            e.preventDefault();

            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
});

// Function to check existing details (API call)
function checkExistingDetails() {
    const tunjanganTypeId = document.getElementById('tunjangan_type_id').value;
    const staffStatus = document.getElementById('staff_status').value;

    if (!tunjanganTypeId) {
        alert('Pilih jenis tunjangan terlebih dahulu');
        return;
    }

    // Show loading
    const existingDetailsDiv = document.getElementById('existing-details');
    const contentDiv = document.getElementById('existing-details-content');

    existingDetailsDiv.classList.remove('hidden');
    contentDiv.innerHTML = '<div class="flex items-center"><svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Mengecek detail nominal yang sudah ada...</div>';

    // Simulate API call - replace with actual API call
    setTimeout(() => {
        // Mock data - replace with actual API response
        const existingData = [
            { staff_status: 'pkwtt', amount: 15000, effective_date: '2024-01-01', is_active: true },
            { staff_status: 'karyawan', amount: 20000, effective_date: '2024-01-01', is_active: true }
        ];

        if (existingData.length > 0) {
            let html = '<p class="mb-2">Detail nominal yang sudah ada untuk jenis tunjangan ini:</p><ul class="list-disc pl-5 space-y-1">';
            existingData.forEach(detail => {
                const statusText = detail.staff_status.replace('_', ' ');
                const statusBadge = detail.is_active ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>' : '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>';
                html += `<li>${statusText.charAt(0).toUpperCase() + statusText.slice(1)}: Rp ${new Intl.NumberFormat('id-ID').format(detail.amount)} (berlaku ${detail.effective_date}) ${statusBadge}</li>`;
            });
            html += '</ul>';
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = '<p>Belum ada detail nominal untuk jenis tunjangan ini.</p>';
        }
    }, 1500);
}
</script>
@endpush
