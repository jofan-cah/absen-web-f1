@extends('admin.layouts.app')

@section('title', 'Edit Tipe Ijin')
@section('breadcrumb', 'Edit Tipe Ijin')
@section('page_title', 'Edit Tipe Ijin')

@section('content')
<div class="container-fluid px-4 py-6">

    <!-- Header Actions -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.ijin-type.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <a href="{{ route('admin.ijin-type.show', $ijinType->ijin_type_id) }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Lihat Detail
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Form Edit Tipe Ijin</h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui informasi tipe ijin</p>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="p-6">
            <form action="{{ route('admin.ijin-type.update', $ijinType->ijin_type_id) }}" method="POST" id="ijinTypeEditForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- ID Info (Read-only) -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">ID Tipe Ijin</p>
                                    <p class="text-sm font-mono font-semibold text-gray-900">{{ $ijinType->ijin_type_id }}</p>
                                </div>
                            </div>
                        </div>

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
                                    value="{{ old('name', $ijinType->name) }}"
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
                                    value="{{ old('code', $ijinType->code) }}"
                                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('code') border-red-500 @enderror {{ in_array($ijinType->code, ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave']) ? 'bg-gray-100' : '' }}"
                                    placeholder="Contoh: sick, annual, personal"
                                    {{ in_array($ijinType->code, ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave']) ? 'readonly' : '' }}
                                    required>
                            </div>
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if(in_array($ijinType->code, ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave']))
                                <p class="mt-2 text-xs text-yellow-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Kode default sistem tidak dapat diubah
                                </p>
                            @else
                                <p class="mt-2 text-xs text-gray-500">Kode unik untuk sistem (huruf kecil, tanpa spasi)</p>
                            @endif
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
                                        {{ old('is_active', $ijinType->is_active) ? 'checked' : '' }}>
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
                                placeholder="Deskripsi detail tentang tipe ijin ini...">{{ old('description', $ijinType->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Penjelasan lengkap tentang tipe ijin (opsional)</p>
                        </div>

                        <!-- Usage Stats -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-blue-900">Statistik Penggunaan</h4>
                                    <div class="mt-3 space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-blue-700">Total Penggunaan</span>
                                            <span class="text-lg font-bold text-blue-900">{{ $ijinType->ijins_count ?? 0 }}</span>
                                        </div>
                                        <div class="text-xs text-blue-600">
                                            Tipe ijin ini telah digunakan dalam {{ $ijinType->ijins_count ?? 0 }} pengajuan ijin
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Box -->
                        @if(in_array($ijinType->code, ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave']))
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Tipe Ijin Default Sistem</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Ini adalah tipe ijin default sistem yang tidak dapat dihapus. Kode tidak dapat diubah untuk menjaga integritas sistem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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
                                    {{ $ijinType->name }}
                                </div>
                                <div class="flex items-center mt-1 space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800" id="preview-code">
                                        {{ $ijinType->code }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" id="preview-status">
                                        <div class="w-1.5 h-1.5 rounded-full mr-2"></div>
                                        {{ $ijinType->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600" id="preview-description">
                            {{ $ijinType->description ?? 'Deskripsi akan muncul di sini...' }}
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
                            Update Tipe Ijin
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

    // Event listeners
    nameInput.addEventListener('input', updatePreview);
    codeInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    isActiveInput.addEventListener('change', updatePreview);

    // Format code input (only if not readonly)
    if (!codeInput.hasAttribute('readonly')) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        });
    }

    // Initial preview
    updatePreview();

    // Form validation
    const form = document.getElementById('ijinTypeEditForm');
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
