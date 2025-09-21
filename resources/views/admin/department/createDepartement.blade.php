@extends('admin.layouts.app')

@section('title', 'Tambah Department')
@section('breadcrumb', 'Tambah Department')
@section('page_title', 'Tambah Department')

@section('page_actions')
<div class="flex items-center space-x-3">
    <a href="{{ route('admin.department.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-white">Formulir Department Baru</h2>
                    <p class="text-blue-100 text-sm mt-1">Lengkapi informasi department yang akan ditambahkan</p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-6 mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-6 mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                        <h4 class="font-medium">Terdapat kesalahan pada form:</h4>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.department.store') }}" method="POST" class="p-6">
            @csrf

            <!-- Basic Information Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Dasar
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Masukkan informasi dasar department</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Department -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Department <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Information Technology">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kode Department -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Department <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror"
                               placeholder="Contoh: IT" maxlength="50" style="text-transform: uppercase;">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kode akan otomatis dijadikan huruf besar</p>
                    </div>

                    <!-- Manager -->
                    <div>
                        <label for="manager_user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Manager Department
                        </label>
                        <select id="manager_user_id" name="manager_user_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('manager_user_id') border-red-500 @enderror">
                            <option value="">Pilih Manager (Opsional)</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->user_id }}" {{ old('manager_user_id') == $manager->user_id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                    @if($manager->karyawan)
                                        ({{ $manager->karyawan->position }})
                                    @else
                                        (Admin)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('manager_user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Manager dapat diatur kemudian jika belum ada</p>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                        Deskripsi
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Jelaskan fungsi dan tanggung jawab department</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Department
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Deskripsikan fungsi, tanggung jawab, dan scope kerja department ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Opsional - Deskripsi akan membantu dalam pemahaman struktur organisasi</p>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Department
                </h4>
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span id="preview-icon" class="text-white font-bold text-lg">??</span>
                    </div>
                    <div class="flex-1">
                        <h3 id="preview-name" class="text-lg font-semibold text-gray-900">Nama Department</h3>
                        <p id="preview-code" class="text-sm text-gray-500 font-mono">KODE</p>
                        <p id="preview-manager" class="text-sm text-gray-600">Manager: Belum dipilih</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.department.index') }}"
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="reset" onclick="resetPreview()"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Reset Form
                    </button>
                    <button type="submit" class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Department
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Tips Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Tips Pengisian Form</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Gunakan nama department yang jelas dan mudah dipahami</li>
                        <li>Kode department sebaiknya singkat dan unik (2-5 karakter)</li>
                        <li>Manager dapat dipilih dari admin atau karyawan dengan level koordinator</li>
                        <li>Deskripsi membantu karyawan memahami struktur organisasi</li>
                        <li>Department akan otomatis berstatus aktif setelah dibuat</li>
                    </ul>
                </div>
            </div>
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
    const managerSelect = document.getElementById('manager_user_id');

    // Preview elements
    const previewIcon = document.getElementById('preview-icon');
    const previewName = document.getElementById('preview-name');
    const previewCode = document.getElementById('preview-code');
    const previewManager = document.getElementById('preview-manager');

    // Update preview when inputs change
    function updatePreview() {
        // Update name
        const name = nameInput.value.trim() || 'Nama Department';
        previewName.textContent = name;

        // Update code
        const code = codeInput.value.trim().toUpperCase() || 'KODE';
        previewCode.textContent = code;

        // Update icon (first 2 characters of name or code)
        const iconText = name !== 'Nama Department'
            ? name.substring(0, 2).toUpperCase()
            : (code !== 'KODE' ? code.substring(0, 2) : '??');
        previewIcon.textContent = iconText;

        // Update manager
        const selectedManager = managerSelect.options[managerSelect.selectedIndex];
        const managerText = selectedManager.value
            ? `Manager: ${selectedManager.text}`
            : 'Manager: Belum dipilih';
        previewManager.textContent = managerText;
    }

    // Auto-uppercase code input
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        updatePreview();
    });

    // Auto-generate code from name
    nameInput.addEventListener('input', function() {
        const name = this.value.trim();
        if (name && !codeInput.value.trim()) {
            // Generate code from first letters of words
            const words = name.split(' ').filter(word => word.length > 0);
            const autoCode = words.map(word => word.charAt(0).toUpperCase()).join('').substring(0, 5);
            codeInput.value = autoCode;
        }
        updatePreview();
    });

    // Update preview when manager changes
    managerSelect.addEventListener('change', updatePreview);

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;

        // Validate required fields
        const requiredFields = ['name', 'code'];
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                field.focus();
            } else {
                field.classList.remove('border-red-500');
            }
        });

        // Validate code length
        if (codeInput.value.trim().length < 2) {
            isValid = false;
            codeInput.classList.add('border-red-500');
            alert('Kode department minimal 2 karakter');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi field yang wajib diisi');
        }
    });

    // Initialize preview
    updatePreview();
});

function resetPreview() {
    // Reset preview to default values
    document.getElementById('preview-icon').textContent = '??';
    document.getElementById('preview-name').textContent = 'Nama Department';
    document.getElementById('preview-code').textContent = 'KODE';
    document.getElementById('preview-manager').textContent = 'Manager: Belum dipilih';
}

// Character counter for description
document.getElementById('description').addEventListener('input', function() {
    const maxLength = 500;
    const currentLength = this.value.length;

    // Create or update character counter
    let counter = document.getElementById('description-counter');
    if (!counter) {
        counter = document.createElement('p');
        counter.id = 'description-counter';
        counter.className = 'mt-1 text-xs text-gray-500';
        this.parentNode.appendChild(counter);
    }

    counter.textContent = `${currentLength}/${maxLength} karakter`;

    if (currentLength > maxLength) {
        counter.className = 'mt-1 text-xs text-red-500';
        this.value = this.value.substring(0, maxLength);
    } else {
        counter.className = 'mt-1 text-xs text-gray-500';
    }
});
</script>
@endpush

@push('styles')
<style>
/* Auto-uppercase for code input */
#code {
    text-transform: uppercase;
}

/* Smooth focus transitions */
input:focus, select:focus, textarea:focus {
    transition: all 0.2s ease-in-out;
}

/* Preview card animation */
.preview-card {
    transition: all 0.3s ease;
}

/* Form validation styles */
.border-red-500 {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Button hover effects */
button, .btn {
    transition: all 0.2s ease-in-out;
}

button:hover, .btn:hover {
    transform: translateY(-1px);
}

button:active, .btn:active {
    transform: translateY(0);
}
</style>
@endpush
