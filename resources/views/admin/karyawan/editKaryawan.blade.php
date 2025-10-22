@extends('admin.layouts.app')

@section('title', 'Edit Karyawan')
@section('breadcrumb', 'Edit Karyawan')
@section('page_title', 'Edit Karyawan')

@section('page_actions')
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.karyawan.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <a href="{{ route('admin.karyawan.show', $karyawan->karyawan_id) }}"
            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm font-medium transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Lihat Detail
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            <!-- Header with Current Info -->
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white">Edit Data Karyawan</h2>
                        <p class="text-yellow-100 text-sm mt-1">Update informasi {{ $karyawan->full_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-yellow-100 text-sm">ID: {{ $karyawan->karyawan_id }}</p>
                        <p class="text-yellow-100 text-sm">NIP: {{ $karyawan->nip }}</p>
                    </div>
                </div>
            </div>

            <!-- Current Photo Display -->
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if ($karyawan->photo)
                            <img src="{{ Storage::disk('s3')->url($karyawan->photo) }}" alt="{{ $karyawan->full_name }}"
                                class="h-16 w-16 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div
                                class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-4 border-white shadow-lg">
                                <span
                                    class="text-white font-bold text-lg">{{ strtoupper(substr($karyawan->full_name, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $karyawan->full_name }}</h3>
                        <p class="text-gray-600">{{ $karyawan->position }} -
                            {{ $karyawan->department->name ?? 'Belum ada department' }}</p>
                        <div class="flex items-center mt-1">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $karyawan->employment_status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $karyawan->employment_status == 'inactive' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $karyawan->employment_status == 'terminated' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $karyawan->employment_status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <h4 class="font-medium">Terdapat kesalahan pada form:</h4>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.karyawan.update', $karyawan->karyawan_id) }}" method="POST"
                enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <!-- Tab Navigation -->
                <div class="mb-8">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button type="button" onclick="showTab('personal')" id="personal-tab"
                                class="tab-button active py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Data Personal
                            </button>
                            <button type="button" onclick="showTab('work')" id="work-tab"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                </svg>
                                Data Pekerjaan
                            </button>
                            <button type="button" onclick="showTab('account')" id="account-tab"
                                class="tab-button py-4 px-1 border-b-2 font-medium text-sm">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Akun & Foto
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab 1: Data Personal -->
                <div id="personal-content" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Personal</h3>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name"
                                value="{{ old('full_name', $karyawan->full_name) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('full_name') border-red-500 @enderror">
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NIP -->
                        {{-- <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                            NIP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip', $karyawan->nip) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('nip') border-red-500 @enderror">
                        @error('nip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                No. Telepon
                            </label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone', $karyawan->phone) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir
                            </label>
                            <input type="date" id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $karyawan->birth_date ? $karyawan->birth_date->format('Y-m-d') : '') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('birth_date') border-red-500 @enderror">
                            @error('birth_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select id="gender" name="gender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('gender') border-red-500 @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('gender', $karyawan->gender) == 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P" {{ old('gender', $karyawan->gender) == 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Bergabung -->
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Bergabung <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="hire_date" name="hire_date"
                                value="{{ old('hire_date', $karyawan->hire_date ? $karyawan->hire_date->format('Y-m-d') : '') }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('address') border-red-500 @enderror">{{ old('address', $karyawan->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Data Pekerjaan -->
                <div id="work-content" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pekerjaan</h3>
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select id="department_id" name="department_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('department_id') border-red-500 @enderror">
                                <option value="">Pilih Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->department_id }}"
                                        {{ old('department_id', $karyawan->department_id) == $department->department_id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                Posisi/Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="position" name="position"
                                value="{{ old('position', $karyawan->position) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('position') border-red-500 @enderror">
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Staff Status -->
                        <div>
                            <label for="staff_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Staff <span class="text-red-500">*</span>
                            </label>
                            <select id="staff_status" name="staff_status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('staff_status') border-red-500 @enderror">
                                <option value="">Pilih Status Staff</option>
                                <option value="staff"
                                    {{ old('staff_status', $karyawan->staff_status) == 'staff' ? 'selected' : '' }}>Staff
                                </option>
                                <option value="koordinator"
                                    {{ old('staff_status', $karyawan->staff_status) == 'koordinator' ? 'selected' : '' }}>
                                    Koordinator</option>
                                <option value="pkwtt"
                                    {{ old('staff_status', $karyawan->staff_status) == 'pkwtt' ? 'selected' : '' }}>PKWTT
                                </option>
                            </select>
                            @error('staff_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- TAMBAH INI di form edit -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="uang_kuota" name="uang_kuota" value="1"
                                    {{ old('uang_kuota', $karyawan->uang_kuota) ? 'checked' : '' }}
                                    class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                                <label for="uang_kuota" class="ml-2 text-sm font-medium text-gray-700">
                                    Dapat Uang Kuota
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Centang jika karyawan ini berhak mendapat uang kuota
                            </p>
                        </div>

                        <!-- Employment Status -->
                        <div>
                            <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Karyawan <span class="text-red-500">*</span>
                            </label>
                            <select id="employment_status" name="employment_status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('employment_status') border-red-500 @enderror">
                                <option value="">Pilih Status Karyawan</option>
                                <option value="active"
                                    {{ old('employment_status', $karyawan->employment_status) == 'active' ? 'selected' : '' }}>
                                    active</option>
                                <option value="inactive"
                                    {{ old('employment_status', $karyawan->employment_status) == 'inactive' ? 'selected' : '' }}>
                                    inactive</option>
                                <option value="terminated"
                                    {{ old('employment_status', $karyawan->employment_status) == 'terminated' ? 'selected' : '' }}>
                                    terminated</option>
                            </select>
                            @error('employment_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Akun & Foto -->
                <div id="account-content" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Akun Login & Foto Profil</h3>
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Username Login <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $karyawan->user->name ?? '') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $karyawan->user->email ?? '') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo Upload -->
                        <div class="md:col-span-2">
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Profil
                            </label>
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    <img id="photo-preview"
                                        class="h-20 w-20 object-cover rounded-full border-4 border-gray-300"
                                        src="{{ $karyawan->photo ? asset('storage/' . $karyawan->photo) : 'https://via.placeholder.com/80/e5e7eb/6b7280?text=Foto' }}"
                                        alt="Preview">
                                </div>
                                <div class="flex-1">
                                    <label for="photo" class="cursor-pointer">
                                        <div
                                            class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-yellow-500">
                                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $karyawan->photo ? 'Ganti Foto' : 'Pilih Foto' }}
                                        </div>
                                        <input id="photo" name="photo" type="file" class="sr-only"
                                            accept="image/*" onchange="previewPhoto(this)">
                                    </label>
                                    @if ($karyawan->photo)
                                        <p class="mt-2 text-xs text-gray-500">Foto saat ini:
                                            {{ basename($karyawan->photo) }}</p>
                                    @endif
                                    <p class="mt-1 text-xs text-gray-500">JPG, JPEG, PNG. Maksimal 2MB.</p>
                                </div>
                            </div>
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Reset Note -->
                        <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="flex-shrink-0 w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Informasi Password</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Password tidak dapat diubah melalui form ini. Gunakan tombol "Reset Password" di
                                            halaman detail karyawan untuk mengubah password.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.karyawan.index') }}"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Batal
                        </a>
                        <a href="{{ route('admin.karyawan.show', $karyawan->karyawan_id) }}"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            Lihat Detail
                        </a>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit"
                            class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Data
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-yellow-500', 'text-yellow-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                    'hover:border-gray-300');
            });

            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Add active class to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.add('active', 'border-yellow-500', 'text-yellow-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                'hover:border-gray-300');
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize tabs
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial tab styles
            document.querySelectorAll('.tab-button').forEach(button => {
                if (!button.classList.contains('active')) {
                    button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                        'hover:border-gray-300');
                }
            });

            // Set active tab style
            document.getElementById('personal-tab').classList.add('border-yellow-500', 'text-yellow-600');
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
    </style>
@endpush
