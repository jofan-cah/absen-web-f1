@extends('admin.layouts.app')

@section('title', 'Tambah Karyawan')
@section('breadcrumb', 'Tambah Karyawan')
@section('page_title', 'Tambah Karyawan')

@section('page_actions')
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.karyawan.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Formulir Tambah Karyawan</h2>
                <p class="text-primary-100 text-sm mt-1">Lengkapi informasi karyawan baru</p>
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
            <form action="{{ route('admin.karyawan.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                1</div>
                            <span class="ml-2 text-sm font-medium text-primary-600">Data Personal</span>
                        </div>
                        <div class="h-px bg-gray-300 flex-1 mx-4"></div>
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">
                                2</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Data Pekerjaan</span>
                        </div>
                        <div class="h-px bg-gray-300 flex-1 mx-4"></div>
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">
                                3</div>
                            <span class="ml-2 text-sm font-medium text-gray-500">Akun & Foto</span>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Data Personal -->
                <div id="step-1" class="step-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informasi Personal
                            </h3>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('full_name') border-red-500 @enderror">
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NIP -->
                        {{-- <div>
                            <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">
                                NIP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('nip') border-red-500 @enderror">
                            @error('nip')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> --}}

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                No. Telepon
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir
                            </label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('birth_date') border-red-500 @enderror">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('gender') border-red-500 @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="nextStep(1)"
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- Step 2: Data Pekerjaan -->
                <div id="step-2" class="step-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                </svg>
                                Informasi Pekerjaan
                            </h3>
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select id="department_id" name="department_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('department_id') border-red-500 @enderror">
                                <option value="">Pilih Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->department_id }}"
                                        {{ old('department_id') == $department->department_id ? 'selected' : '' }}>
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
                            <input type="text" id="position" name="position" value="{{ old('position') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('position') border-red-500 @enderror">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('staff_status') border-red-500 @enderror">
                                <option value="">Pilih Status Staff</option>
                                <option value="staff" {{ old('staff_status') == 'staff' ? 'selected' : '' }}>Staff
                                </option>
                                <option value="koordinator" {{ old('staff_status') == 'koordinator' ? 'selected' : '' }}>
                                    Koordinator</option>
                                <option value="pkwtt"
                                    {{ old('staff_status') == 'pkwtt' ? 'selected' : '' }}>PKWTT
                                </option>
                            </select>
                            @error('staff_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hire Date -->
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Bergabung <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="hire_date" name="hire_date"
                                value="{{ old('hire_date', date('Y-m-d')) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shift Normal Section -->
                        <div class="md:col-span-2 mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pengaturan Jadwal Otomatis
                            </h4>

                            <!-- Is Shift Normal Checkbox -->
                            <div class="mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_shift_normal" name="is_shift_normal" value="1"
                                        {{ old('is_shift_normal') ? 'checked' : '' }}
                                        onchange="toggleDefaultShift()"
                                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                    <label for="is_shift_normal" class="ml-2 text-sm font-medium text-gray-700">
                                        Jadwal Shift Normal (Senin-Sabtu)
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ml-6">
                                    Centang jika karyawan ini memiliki jadwal kerja tetap Senin-Sabtu.
                                    Jadwal akan di-generate otomatis setiap awal bulan.
                                </p>
                            </div>

                            <!-- Default Shift Dropdown -->
                            <div id="default_shift_container" class="hidden">
                                <label for="default_shift_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Shift Default <span class="text-red-500">*</span>
                                </label>
                                <select id="default_shift_id" name="default_shift_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('default_shift_id') border-red-500 @enderror">
                                    <option value="">Pilih Shift Default</option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->shift_id }}"
                                            {{ old('default_shift_id') == $shift->shift_id ? 'selected' : '' }}>
                                            {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('default_shift_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    Shift ini akan digunakan untuk generate jadwal otomatis setiap bulan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="prevStep(2)"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Sebelumnya
                        </button>
                        <button type="button" onclick="nextStep(2)"
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- Step 3: Akun & Foto -->
                <div id="step-3" class="step-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Akun Login & Foto Profil
                            </h3>
                        </div>

                        <!-- Username (auto filled from email) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Username Login <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                                placeholder="Username untuk login">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Username yang akan digunakan untuk login sistem</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 px-3 py-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
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
                                        class="h-16 w-16 object-cover rounded-full border-2 border-gray-300"
                                        src="https://via.placeholder.com/64/e5e7eb/6b7280?text=Foto" alt="Preview">
                                </div>
                                <label for="photo" class="cursor-pointer">
                                    <div
                                        class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-primary-500">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Pilih Foto
                                    </div>
                                    <input id="photo" name="photo" type="file" class="sr-only"
                                        accept="image/*" onchange="previewPhoto(this)">
                                </label>
                            </div>
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">JPG, JPEG, PNG. Maksimal 2MB.</p>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="prevStep(3)"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Sebelumnya
                        </button>
                        <button type="submit"
                            class="px-8 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Karyawan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentStep = 1;

        function updateStepIndicator(step) {
            // Reset all steps
            for (let i = 1; i <= 3; i++) {
                const stepDiv = document.querySelector(`.flex:nth-child(${i*2-1}) .w-8`);
                const stepText = document.querySelector(`.flex:nth-child(${i*2-1}) span`);

                if (i <= step) {
                    stepDiv.classList.remove('bg-gray-300', 'text-gray-500');
                    stepDiv.classList.add('bg-primary-600', 'text-white');
                    stepText.classList.remove('text-gray-500');
                    stepText.classList.add('text-primary-600');
                } else {
                    stepDiv.classList.remove('bg-primary-600', 'text-white');
                    stepDiv.classList.add('bg-gray-300', 'text-gray-500');
                    stepText.classList.remove('text-primary-600');
                    stepText.classList.add('text-gray-500');
                }
            }
        }

        function nextStep(step) {
            if (validateStep(step)) {
                document.getElementById(`step-${step}`).classList.add('hidden');
                document.getElementById(`step-${step + 1}`).classList.remove('hidden');
                currentStep = step + 1;
                updateStepIndicator(currentStep);
            }
        }

        function prevStep(step) {
            document.getElementById(`step-${step}`).classList.add('hidden');
            document.getElementById(`step-${step - 1}`).classList.remove('hidden');
            currentStep = step - 1;
            updateStepIndicator(currentStep);
        }

        function validateStep(step) {
            let isValid = true;

            if (step === 1) {
                const requiredFields = ['full_name',  'email', 'gender'];
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        input.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });
            } else if (step === 2) {
                const requiredFields = ['department_id', 'position', 'staff_status', 'hire_date'];
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        input.classList.add('border-red-500');
                        isValid = false;
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });
            }

            if (!isValid) {
                alert('Mohon lengkapi field yang wajib diisi');
            }

            return isValid;
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

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }

        // Auto-fill username from full name
        document.getElementById('full_name').addEventListener('input', function() {
            const fullName = this.value;
            const username = fullName.toLowerCase().replace(/\s+/g, '');
            document.getElementById('name').value = username;
        });

        // Toggle default shift dropdown
        function toggleDefaultShift() {
            const isShiftNormal = document.getElementById('is_shift_normal').checked;
            const container = document.getElementById('default_shift_container');
            const shiftSelect = document.getElementById('default_shift_id');

            if (isShiftNormal) {
                container.classList.remove('hidden');
                shiftSelect.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                shiftSelect.removeAttribute('required');
                shiftSelect.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleDefaultShift();
        });
    </script>
@endpush
