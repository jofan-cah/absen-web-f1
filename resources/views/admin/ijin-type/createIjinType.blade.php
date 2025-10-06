@extends('admin.layouts.app')

@section('title', 'Tambah Tipe Ijin')
@section('breadcrumb', 'Tambah Tipe Ijin')
@section('page_title', 'Tambah Tipe Ijin')

@section('content')
<div class="container-fluid px-4 py-6">

    <!-- Header Actions -->
    <div class="mb-6">
        <a href="{{ route('admin.ijin-type.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Form Tambah Tipe Ijin</h3>
                    <p class="text-sm text-gray-600 mt-1">Lengkapi form di bawah untuk menambahkan tipe ijin baru</p>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6">
            <form action="{{ route('admin.ijin-type.store') }}" method="POST" id="ijinTypeForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Nama Tipe Ijin -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Tipe Ijin <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                    name="name"
                                    id="name"
                                    value="{{ old('name') }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('name') border-red-500 @enderror"
                                    placeholder="Contoh: Sakit, Cuti, dll"
                                    required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Nama tipe ijin yang akan ditampilkan</p>
                        </div>

                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Tipe Ijin <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                </div>
                                <input type="text"
                                    name="code"
                                    id="code"
                                    value="{{ old('code') }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('code') border-red-500 @enderror"
                                    placeholder="Contoh: sick, annual, personal"
                                    required>
                            </div>
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Kode unik untuk sistem (huruf kecil, tanpa spasi)</p>
                        </div>

                        <!-- Status Aktif -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Status
                            </label>
                            <div class="flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                        name="is_active"
                                        id="is_active"
                                        class="sr-only peer"
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Aktifkan tipe ijin</span>
                                </label>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Tipe ijin aktif dapat digunakan oleh karyawan</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="6"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('description') border-red-500 @enderror"
                                placeholder="Deskripsi detail tentang tipe ijin ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Penjelasan lengkap tentang tipe ijin (opsional)</p>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Kode harus unik dan tidak boleh sama dengan tipe ijin lain</li>
                                            <li>Gunakan kode yang mudah dipahami (contoh: sick, annual)</li>
                                            <li>Tipe ijin default sistem tidak dapat dihapus</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="mt-8 bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg border border-pink-200 p-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview Tipe Ijin
                    </h4>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="text-base font-semibold text-gray-900" id="preview-name">
                                    Nama Tipe Ijin
                                </div>
                                <div class="flex items-center mt-1 space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800" id="preview-code">
                                        code
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" id="preview-status">
                                        <div class="w-1.5 h-1.5 rounded-full mr-2"></div>
                                        Status
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600" id="preview-description">
                            Deskripsi akan muncul di sini...
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('admin.ijin-type.index') }}"
                        class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-pink-500 to-rose-600 rounded-lg hover:from-pink-600 hover:to-rose-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Tipe Ijin
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const descriptionInput = document.getElementById('description');
    const isActiveInput = document.getElementById('is_active');

    const previewName = document.getElementById('preview-name');
    const previewCode = document.getElementById('preview-code');
    const previewDescription = document.getElementById('preview-description');
    const previewStatus = document.getElementById('preview-status');

    // Update preview
    function updatePreview() {
        // Name
        previewName.textContent = nameInput.value || 'Nama Tipe Ijin';

        // Code
        previewCode.textContent = codeInput.value || 'code';

        // Description
        previewDescription.textContent = descriptionInput.value || 'Deskripsi akan muncul di sini...';

        // Status
        if (isActiveInput.checked) {
            previewStatus.innerHTML = '<div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></div>Aktif';
            previewStatus.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
        } else {
            previewStatus.innerHTML = '<div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></div>Nonaktif';
            previewStatus.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
        }
    }

    // Auto-generate code from name
    nameInput.addEventListener('input', function() {
        // Generate code suggestion
        const code = this.value
            .toLowerCase()
            .replace(/\s+/g, '_')
            .replace(/[^a-z0-9_]/g, '');

        // Only update if code is empty
        if (!codeInput.value) {
            codeInput.value = code;
        }

        updatePreview();
    });

    // Event listeners
    codeInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    isActiveInput.addEventListener('change', updatePreview);

    // Format code input
    codeInput.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
    });

    // Initial preview
    updatePreview();

    // Form validation
    const form = document.getElementById('ijinTypeForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = ['name', 'code'];
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

        if (hasError) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi');
        }
    });
});
</script>
@endpush
