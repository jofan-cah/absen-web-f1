@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.libur.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Hari Libur</h1>
                <p class="mt-2 text-sm text-gray-600">Lengkapi form di bawah untuk menambahkan hari libur baru</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Form Tambah Hari Libur</h3>
                    <p class="text-sm text-gray-600">Lengkapi informasi hari libur dengan benar</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6">
            <form action="{{ route('admin.libur.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Nama Libur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Hari Libur <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                       placeholder="Contoh: Tahun Baru, Idul Fitri, dll" required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Nama hari libur yang akan ditampilkan di kalender</p>
                        </div>

                        <!-- Tanggal Libur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Libur <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" name="date" value="{{ old('date') }}"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date') border-red-500 @enderror"
                                       required>
                            </div>
                            @error('date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Tanggal harus unik (satu tanggal hanya satu libur)</p>
                        </div>

                        <!-- Tipe Libur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Libur
                            </label>
                            <div class="relative">
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <select name="type"
                                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                                    <option value="">Pilih Tipe (Opsional)</option>
                                    <option value="nasional" {{ old('type') == 'nasional' ? 'selected' : '' }}>Libur Nasional</option>
                                    <option value="cuti_bersama" {{ old('type') == 'cuti_bersama' ? 'selected' : '' }}>Cuti Bersama</option>
                                    <option value="perusahaan" {{ old('type') == 'perusahaan' ? 'selected' : '' }}>Libur Perusahaan</option>
                                </select>
                            </div>
                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Kategori jenis hari libur (opsional)</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Warna Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Warna Display
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color" value="{{ old('color', '#FFD700') }}"
                                       class="h-10 w-16 border border-gray-300 rounded-lg cursor-pointer @error('color') border-red-500 @enderror">
                                <input type="text" name="color_text" value="{{ old('color', '#FFD700') }}"
                                       class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="#FFD700" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$"
                                       onchange="document.querySelector('input[name=color]').value = this.value">
                            </div>
                            @error('color')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Warna yang akan ditampilkan di kalender (default: #FFD700 - Gold)</p>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Keterangan
                            </label>
                            <textarea name="description" rows="4"
                                      class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                      placeholder="Keterangan tambahan tentang hari libur ini (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Keterangan atau catatan tambahan (opsional)</p>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-800 mb-2">Informasi Penting</h3>
                                    <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                                        <li>Hari libur berlaku untuk seluruh karyawan (company-wide)</li>
                                        <li>Satu tanggal hanya bisa memiliki satu hari libur</li>
                                        <li>Libur akan ditampilkan di kalender jadwal</li>
                                        <li>Libur bersifat informational, tidak mempengaruhi absensi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.libur.index') }}"
                       class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Simpan Hari Libur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Sync color picker with text input
document.querySelector('input[name=color]').addEventListener('input', function() {
    document.querySelector('input[name=color_text]').value = this.value;
});

document.querySelector('input[name=color_text]').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.querySelector('input[name=color]').value = this.value;
    }
});
</script>
@endsection
