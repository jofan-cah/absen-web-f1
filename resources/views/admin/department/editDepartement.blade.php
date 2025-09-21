@extends('admin.layouts.app')

@section('title', 'Edit Department')
@section('breadcrumb', 'Edit Department')
@section('page_title', 'Edit Department')

@section('page_actions')
<div class="flex items-center space-x-3">
    <a href="{{ route('admin.department.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('admin.department.show', $department->department_id) }}"
       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Lihat Detail
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header with Current Info -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-white">Edit Department</h2>
                        <p class="text-yellow-100 text-sm mt-1">Update informasi {{ $department->name }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-yellow-100 text-sm">ID: {{ $department->department_id }}</p>
                    <p class="text-yellow-100 text-sm">Kode: {{ $department->code }}</p>
                </div>
            </div>
        </div>

        <!-- Current Department Info -->
        <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($department->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $department->name }}</h3>
                        <p class="text-gray-600 font-mono">{{ $department->code }}</p>
                        <div class="flex items-center mt-1">
                            @if($department->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                    Tidak Aktif
                                </span>
                            @endif
                            <span class="ml-3 text-sm text-gray-500">{{ $department->karyawans_count ?? 0 }} karyawan</span>
                        </div>
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Dibuat: {{ $department->created_at->format('d M Y') }}</p>
                    <p>Update: {{ $department->updated_at->diffForHumans() }}</p>
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
        <form action="{{ route('admin.department.update', $department->department_id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Tab Navigation -->
            <div class="mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button type="button" onclick="showTab('basic')" id="basic-tab"
                                class="tab-button active py-4 px-1 border-b-2 font-medium text-sm">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informasi Dasar
                        </button>
                        <button type="button" onclick="showTab('settings')" id="settings-tab"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab 1: Basic Information -->
            <div id="basic-content" class="tab-content">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar Department</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Department -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Department <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('name') border-red-500 @enderror"
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
                            <input type="text" id="code" name="code" value="{{ old('code', $department->code) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('code') border-red-500 @enderror"
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('manager_user_id') border-red-500 @enderror">
                                <option value="">Pilih Manager (Opsional)</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->user_id }}" {{ old('manager_user_id', $department->manager_user_id) == $manager->user_id ? 'selected' : '' }}>
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
                            @if($department->manager)
                                <p class="mt-1 text-xs text-green-600">Manager saat ini: {{ $department->manager->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Department
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('description') border-red-500 @enderror"
                              placeholder="Deskripsikan fungsi, tanggung jawab, dan scope kerja department ini...">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Deskripsi membantu dalam pemahaman struktur organisasi</p>
                </div>
            </div>

            <!-- Tab 2: Settings -->
            <div id="settings-content" class="tab-content hidden">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Department</h3>

                    <!-- Status Department -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status Department <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="active" name="is_active" type="radio" value="1" {{ old('is_active', $department->is_active) == 1 ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                <label for="active" class="ml-3 block text-sm font-medium text-gray-700">
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                    <p class="text-xs text-gray-500">Department dapat beroperasi normal dan menerima karyawan baru</p>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="inactive" name="is_active" type="radio" value="0" {{ old('is_active', $department->is_active) == 0 ? 'checked' : '' }}
                                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                <label for="inactive" class="ml-3 block text-sm font-medium text-gray-700">
                                    <span class="text-red-600 font-semibold">Tidak Aktif</span>
                                    <p class="text-xs text-gray-500">Department tidak beroperasi, namun data karyawan tetap tersimpan</p>
                                </label>
                            </div>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department Statistics -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Statistik Department</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $department->karyawans_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Total Karyawan</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $department->karyawans()->where('employment_status', 'aktif')->count() }}</div>
                                <div class="text-sm text-gray-500">Karyawan Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning for Deactivation -->
                @if($department->is_active && $department->karyawans_count > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="flex-shrink-0 w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Department ini memiliki {{ $department->karyawans_count }} karyawan. Menonaktifkan department akan:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Mencegah penambahan karyawan baru</li>
                                    <li>Karyawan existing tetap bisa bekerja</li>
                                    <li>Data karyawan tetap tersimpan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Preview Section -->
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Perubahan
                </h4>
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <span id="preview-icon" class="text-white font-bold text-lg">{{ strtoupper(substr($department->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 id="preview-name" class="text-lg font-semibold text-gray-900">{{ $department->name }}</h3>
                        <p id="preview-code" class="text-sm text-gray-500 font-mono">{{ $department->code }}</p>
                        <p id="preview-manager" class="text-sm text-gray-600">Manager: {{ $department->manager->name ?? 'Belum dipilih' }}</p>
                    </div>
                    <span id="preview-status" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <div class="w-2 h-2 {{ $department->is_active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }} rounded-full mr-1"></div>
                        {{ $department->is_active ? 'Aktif' : 'Tidak Aktif' }}
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
                    <a href="{{ route('admin.department.show', $department->department_id) }}"
                       class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Lihat Detail
                    </a>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button" onclick="resetForm()"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Reset
                    </button>
                    <button type="submit" class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Department
                    </button>
                </div>
            </div>

        </form>
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
    const activeRadio = document.getElementById('active');
    const inactiveRadio = document.getElementById('inactive');

    // Preview elements
    const previewIcon = document.getElementById('preview-icon');
    const previewName = document.getElementById('preview-name');
    const previewCode = document.getElementById('preview-code');
    const previewManager = document.getElementById('preview-manager');
    const previewStatus = document.getElementById('preview-status');

    // Original values
    const originalValues = {
        name: '{{ $department->name }}',
        code: '{{ $department->code }}',
        manager_user_id: '{{ $department->manager_user_id ?? '' }}',
        is_active: {{ $department->is_active ? 'true' : 'false' }}
    };

    // Update preview when inputs change
    function updatePreview() {
        // Update name
        const name = nameInput.value.trim() || originalValues.name;
        previewName.textContent = name;

        // Update code
        const code = codeInput.value.trim().toUpperCase() || originalValues.code;
        previewCode.textContent = code;

        // Update icon (first 2 characters of name or code)
        const iconText = name.substring(0, 2).toUpperCase();
        previewIcon.textContent = iconText;

        // Update manager
        const selectedManager = managerSelect.options[managerSelect.selectedIndex];
        const managerText = selectedManager.value
            ? `Manager: ${selectedManager.text}`
            : 'Manager: Belum dipilih';
        previewManager.textContent = managerText;

        // Update status
        const isActive = activeRadio.checked;
        if (isActive) {
            previewStatus.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            previewStatus.innerHTML = '<div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>Aktif';
        } else {
            previewStatus.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
            previewStatus.innerHTML = '<div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>Tidak Aktif';
        }
    }

    // Auto-uppercase code input
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        updatePreview();
    });

    // Update preview on input changes
    nameInput.addEventListener('input', updatePreview);
    managerSelect.addEventListener('change', updatePreview);
    activeRadio.addEventListener('change', updatePreview);
    inactiveRadio.addEventListener('change', updatePreview);

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

function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-yellow-500', 'text-yellow-600');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-yellow-500', 'text-yellow-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
}

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form ke nilai awal?')) {
        // Reset to original values
        document.getElementById('name').value = '{{ $department->name }}';
        document.getElementById('code').value = '{{ $department->code }}';
        document.getElementById('manager_user_id').value = '{{ $department->manager_user_id ?? '' }}';
        document.getElementById('description').value = '{{ $department->description ?? '' }}';

        // Reset radio buttons
        @if($department->is_active)
            document.getElementById('active').checked = true;
        @else
            document.getElementById('inactive').checked = true;
        @endif

        // Update preview
        updatePreview();

        // Remove validation errors
        document.querySelectorAll('.border-red-500').forEach(element => {
            element.classList.remove('border-red-500');
        });
    }
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

// Initialize tabs
document.addEventListener('DOMContentLoaded', function() {
    // Set initial tab styles
    document.querySelectorAll('.tab-button').forEach(button => {
        if (!button.classList.contains('active')) {
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }
    });

    // Set active tab style
    document.getElementById('basic-tab').classList.add('border-yellow-500', 'text-yellow-600');
});

// Track form changes
let formChanged = false;
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('change', function() {
        formChanged = true;
    });
});

// Warn before leaving if form changed
window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Remove warning when form submitted
document.querySelector('form').addEventListener('submit', function() {
    formChanged = false;
});
</script>
@endpush

@push('styles')
<style>
.tab-button.active {
    border-color: #eab308;
    color: #ca8a04;
}

.tab-button:not(.active) {
    border-color: transparent;
    color: #6b7280;
}

.tab-button:not(.active):hover {
    color: #374151;
    border-color: #d1d5db;
}

/* Auto-uppercase for code input */
#code {
    text-transform: uppercase;
}

/* Smooth focus transitions */
input:focus, select:focus, textarea:focus {
    transition: all 0.2s ease-in-out;
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

/* Radio button styling */
input[type="radio"]:checked {
    background-color: #eab308;
    border-color: #eab308;
}

input[type="radio"]:focus {
    box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.1);
}

/* Smooth animations */
.tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Status indicator animations */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}
</style>
@endpush
