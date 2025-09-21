@extends('admin.layouts.app')

@section('title', 'Tambah Shift')
@section('breadcrumb', 'Tambah Shift')
@section('page_title', 'Tambah Shift Kerja')

@section('page_actions')
<div class="flex items-center space-x-3">
    <a href="{{ route('admin.shift.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-white">Formulir Shift Kerja Baru</h2>
                    <p class="text-blue-100 text-sm mt-1">Lengkapi informasi shift kerja yang akan ditambahkan</p>
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
        <form action="{{ route('admin.shift.store') }}" method="POST" class="p-6">
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
                    <p class="text-sm text-gray-600 mt-1">Masukkan informasi dasar shift kerja</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Shift -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Shift <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Shift Pagi">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kode Shift -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Shift <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror"
                               placeholder="Contoh: PAGI" maxlength="50" style="text-transform: uppercase;">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Kode akan otomatis dijadikan huruf besar</p>
                    </div>

                    <!-- Tipe Shift -->
                    <div>
                        <label for="is_overnight" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipe Shift
                        </label>
                        <select id="is_overnight" name="is_overnight"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('is_overnight') border-red-500 @enderror">
                            <option value="0" {{ old('is_overnight') == '0' ? 'selected' : '' }}>Shift Normal</option>
                            <option value="1" {{ old('is_overnight') == '1' ? 'selected' : '' }}>Shift Malam (Overnight)</option>
                        </select>
                        @error('is_overnight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Pilih shift malam jika shift melewati tengah malam</p>
                    </div>
                </div>
            </div>

            <!-- Working Hours Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Jam Kerja
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Tentukan waktu mulai dan selesai kerja</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Jam Mulai -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   id="start_time"
                                   name="start_time"
                                   value="{{ old('start_time') }}"
                                   required
                                   placeholder="08:00"
                                   pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                   maxlength="5"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_time') border-red-500 @enderror time-input-custom">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Format: HH:MM (24 jam) - contoh: 08:00, 14:30, 22:00</p>
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jam Selesai -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   id="end_time"
                                   name="end_time"
                                   value="{{ old('end_time') }}"
                                   required
                                   placeholder="16:00"
                                   pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                   maxlength="5"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_time') border-red-500 @enderror time-input-custom">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Format: HH:MM (24 jam) - contoh: 16:00, 18:30, 23:59</p>
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Work Duration Display -->
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Durasi Kerja: </span>
                        <span id="work-duration" class="text-sm text-blue-700 ml-1">-</span>
                    </div>
                </div>
            </div>

            <!-- Break Time Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Waktu Istirahat
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Atur waktu istirahat (opsional)</p>
                </div>

                <div class="space-y-4">
                    <!-- Enable Break Toggle -->
                    <div class="flex items-center">
                        <input type="checkbox" id="enable_break" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="enable_break" class="ml-2 text-sm font-medium text-gray-700">
                            Aktifkan waktu istirahat
                        </label>
                    </div>

                    <!-- Break Time Fields -->
                    <div id="break-fields" class="grid grid-cols-1 md:grid-cols-3 gap-6 hidden">
                        <!-- Jam Mulai Istirahat -->
                        <div>
                            <label for="break_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Mulai Istirahat
                            </label>
                            <div class="relative">
                                <input type="text"
                                       id="break_start"
                                       name="break_start"
                                       value="{{ old('break_start') }}"
                                       placeholder="12:00"
                                       pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                       maxlength="5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('break_start') border-red-500 @enderror time-input-custom">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Format: HH:MM (24 jam) - contoh: 12:00</p>
                            @error('break_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Selesai Istirahat -->
                        <div>
                            <label for="break_end" class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Selesai Istirahat
                            </label>
                            <div class="relative">
                                <input type="text"
                                       id="break_end"
                                       name="break_end"
                                       value="{{ old('break_end') }}"
                                       placeholder="13:00"
                                       pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                       maxlength="5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('break_end') border-red-500 @enderror time-input-custom">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Format: HH:MM (24 jam) - contoh: 13:00</p>
                            @error('break_end')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Durasi Istirahat -->
                        <div>
                            <label for="break_duration" class="block text-sm font-medium text-gray-700 mb-2">
                                Durasi Istirahat (menit) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="break_duration" name="break_duration" value="{{ old('break_duration', 60) }}" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('break_duration') border-red-500 @enderror">
                            @error('break_duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tolerance Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Toleransi Waktu
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Atur toleransi keterlambatan dan pulang lebih awal</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Toleransi Terlambat -->
                    <div>
                        <label for="late_tolerance" class="block text-sm font-medium text-gray-700 mb-2">
                            Toleransi Terlambat (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="late_tolerance" name="late_tolerance" value="{{ old('late_tolerance', 15) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('late_tolerance') border-red-500 @enderror">
                        @error('late_tolerance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Waktu toleransi sebelum dianggap terlambat</p>
                    </div>

                    <!-- Toleransi Pulang Awal -->
                    <div>
                        <label for="early_checkout_tolerance" class="block text-sm font-medium text-gray-700 mb-2">
                            Toleransi Pulang Awal (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="early_checkout_tolerance" name="early_checkout_tolerance" value="{{ old('early_checkout_tolerance', 15) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('early_checkout_tolerance') border-red-500 @enderror">
                        @error('early_checkout_tolerance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Waktu toleransi sebelum dianggap pulang lebih awal</p>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Shift
                </h4>
                <div class="flex items-center space-x-4">
                    <div id="preview-icon" class="h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">??</span>
                    </div>
                    <div class="flex-1">
                        <h3 id="preview-name" class="text-lg font-semibold text-gray-900">Nama Shift</h3>
                        <p id="preview-code" class="text-sm text-gray-500 font-mono">KODE</p>
                        <p id="preview-time" class="text-sm text-gray-600">00:00 - 00:00</p>
                        <p id="preview-type" class="text-sm text-gray-600">Shift Normal</p>
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
                    <a href="{{ route('admin.shift.index') }}"
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
                        Simpan Shift
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
                        <li>Gunakan nama shift yang jelas dan mudah dipahami</li>
                        <li>Kode shift sebaiknya singkat dan unik (2-5 karakter)</li>
                        <li>Pilih shift malam jika jadwal kerja melewati tengah malam</li>
                        <li>Jam akhir istirahat harus lebih besar dari jam mulai istirahat</li>
                        <li>Toleransi waktu membantu dalam manajemen kehadiran</li>
                        <li>Shift akan otomatis berstatus aktif setelah dibuat</li>
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
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const isOvernightSelect = document.getElementById('is_overnight');
    const enableBreakCheckbox = document.getElementById('enable_break');
    const breakFields = document.getElementById('break-fields');
    const breakStartInput = document.getElementById('break_start');
    const breakEndInput = document.getElementById('break_end');
    const breakDurationInput = document.getElementById('break_duration');

    // Preview elements
    const previewIcon = document.getElementById('preview-icon');
    const previewName = document.getElementById('preview-name');
    const previewCode = document.getElementById('preview-code');
    const previewTime = document.getElementById('preview-time');
    const previewType = document.getElementById('preview-type');
    const workDurationSpan = document.getElementById('work-duration');

    // Time input formatting and validation
    function formatTimeInput(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d:]/g, '');

            // Auto-add colon
            if (value.length === 2 && !value.includes(':')) {
                value = value + ':';
            }

            // Limit to HH:MM format
            if (value.length > 5) {
                value = value.substring(0, 5);
            }

            e.target.value = value;
            validateTimeInput(e.target);
        });

        input.addEventListener('blur', function(e) {
            let value = e.target.value;
            if (value && value.match(/^\d{1,2}:\d{2}$/)) {
                // Pad hour with zero if needed
                const parts = value.split(':');
                const hour = parts[0].padStart(2, '0');
                const minute = parts[1];
                e.target.value = `${hour}:${minute}`;
            }
            validateTimeInput(e.target);
            updatePreview();
        });

        input.addEventListener('keydown', function(e) {
            // Allow backspace, delete, tab, escape, enter
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Allow numbers and colon
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
                (e.keyCode < 96 || e.keyCode > 105) &&
                e.keyCode !== 186) {
                e.preventDefault();
            }
        });
    }

    function validateTimeInput(input) {
        const value = input.value;
        const isValid = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(value);

        if (value && !isValid) {
            input.classList.add('border-red-500');
            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('error-message')) {
                const errorMsg = document.createElement('p');
                errorMsg.className = 'mt-1 text-sm text-red-600 error-message';
                errorMsg.textContent = 'Format harus HH:MM (24 jam), contoh: 08:00, 14:30, 23:59';
                input.parentNode.insertBefore(errorMsg, input.nextElementSibling);
            }
        } else {
            input.classList.remove('border-red-500');
            const errorMsg = input.parentNode.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        }

        return isValid;
    }

    // Apply time formatting to all time inputs
    formatTimeInput(startTimeInput);
    formatTimeInput(endTimeInput);
    formatTimeInput(breakStartInput);
    formatTimeInput(breakEndInput);

    // Update preview when inputs change
    function updatePreview() {
        // Update name
        const name = nameInput.value.trim() || 'Nama Shift';
        previewName.textContent = name;

        // Update code
        const code = codeInput.value.trim().toUpperCase() || 'KODE';
        previewCode.textContent = code;

        // Update icon (first 2 characters of code or name)
        const iconText = code !== 'KODE'
            ? code.substring(0, 2)
            : (name !== 'Nama Shift' ? name.substring(0, 2).toUpperCase() : '??');
        previewIcon.querySelector('span').textContent = iconText;

        // Update time
        const startTime = startTimeInput.value || '00:00';
        const endTime = endTimeInput.value || '00:00';
        previewTime.textContent = `${startTime} - ${endTime}`;

        // Update type and icon color
        const isOvernight = isOvernightSelect.value === '1';
        previewType.textContent = isOvernight ? 'Shift Malam (Overnight)' : 'Shift Normal';

        // Update icon gradient
        if (isOvernight) {
            previewIcon.className = 'h-12 w-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center';
        } else {
            previewIcon.className = 'h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center';
        }

        // Update work duration
        updateWorkDuration();
    }

    // Calculate work duration
    function updateWorkDuration() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (startTime && endTime && validateTimeInput(startTimeInput) && validateTimeInput(endTimeInput)) {
            const [startHour, startMin] = startTime.split(':').map(Number);
            const [endHour, endMin] = endTime.split(':').map(Number);

            let startMinutes = startHour * 60 + startMin;
            let endMinutes = endHour * 60 + endMin;

            // Handle overnight shift
            if (endMinutes <= startMinutes) {
                endMinutes += 24 * 60; // Add 24 hours
            }

            const diffMinutes = endMinutes - startMinutes;
            const hours = Math.floor(diffMinutes / 60);
            const minutes = diffMinutes % 60;

            workDurationSpan.textContent = `${hours} jam ${minutes} menit`;
        } else {
            workDurationSpan.textContent = '-';
        }
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

    // Update preview when time inputs change
    startTimeInput.addEventListener('input', updatePreview);
    endTimeInput.addEventListener('input', updatePreview);
    startTimeInput.addEventListener('blur', updatePreview);
    endTimeInput.addEventListener('blur', updatePreview);
    isOvernightSelect.addEventListener('change', updatePreview);

    // Break time toggle
    enableBreakCheckbox.addEventListener('change', function() {
        if (this.checked) {
            breakFields.classList.remove('hidden');
            breakStartInput.required = true;
            breakEndInput.required = true;
        } else {
            breakFields.classList.add('hidden');
            breakStartInput.required = false;
            breakEndInput.required = false;
            breakStartInput.value = '';
            breakEndInput.value = '';
        }
    });

    // Break duration auto-calculation
    function calculateBreakDuration() {
        const breakStart = breakStartInput.value;
        const breakEnd = breakEndInput.value;

        if (breakStart && breakEnd && validateTimeInput(breakStartInput) && validateTimeInput(breakEndInput)) {
            const [startHour, startMin] = breakStart.split(':').map(Number);
            const [endHour, endMin] = breakEnd.split(':').map(Number);

            const startMinutes = startHour * 60 + startMin;
            const endMinutes = endHour * 60 + endMin;

            if (endMinutes > startMinutes) {
                const diffMinutes = endMinutes - startMinutes;
                breakDurationInput.value = diffMinutes;
            }
        }
    }

    breakStartInput.addEventListener('blur', calculateBreakDuration);
    breakEndInput.addEventListener('blur', calculateBreakDuration);

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Validate required fields
        const requiredFields = ['name', 'code', 'start_time', 'end_time', 'break_duration', 'late_tolerance', 'early_checkout_tolerance'];
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                errors.push(`Field ${fieldName} wajib diisi`);
            } else {
                field.classList.remove('border-red-500');
            }
        });

        // Validate time format
        const timeFields = [startTimeInput, endTimeInput];
        timeFields.forEach(field => {
            if (field.value && !validateTimeInput(field)) {
                isValid = false;
                errors.push(`Format waktu ${field.getAttribute('placeholder')} tidak valid`);
            }
        });

        // Validate break time if enabled
        if (enableBreakCheckbox.checked) {
            const breakStart = breakStartInput.value;
            const breakEnd = breakEndInput.value;

            if (!breakStart || !breakEnd) {
                isValid = false;
                if (!breakStart) breakStartInput.classList.add('border-red-500');
                if (!breakEnd) breakEndInput.classList.add('border-red-500');
                errors.push('Jam istirahat harus diisi jika waktu istirahat diaktifkan');
            } else if (!validateTimeInput(breakStartInput) || !validateTimeInput(breakEndInput)) {
                isValid = false;
                errors.push('Format jam istirahat tidak valid');
            } else {
                const [startHour, startMin] = breakStart.split(':').map(Number);
                const [endHour, endMin] = breakEnd.split(':').map(Number);
                const startMinutes = startHour * 60 + startMin;
                const endMinutes = endHour * 60 + endMin;

                if (endMinutes <= startMinutes) {
                    isValid = false;
                    breakEndInput.classList.add('border-red-500');
                    errors.push('Jam akhir istirahat harus lebih besar dari jam mulai istirahat');
                }
            }
        }

        // Validate code length
        if (codeInput.value.trim().length < 2) {
            isValid = false;
            codeInput.classList.add('border-red-500');
            errors.push('Kode shift minimal 2 karakter');
        }

        // Validate time logic for overnight shifts
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const isOvernight = isOvernightSelect.value === '1';

        if (startTime && endTime && validateTimeInput(startTimeInput) && validateTimeInput(endTimeInput)) {
            const [startHour, startMin] = startTime.split(':').map(Number);
            const [endHour, endMin] = endTime.split(':').map(Number);
            const startMinutes = startHour * 60 + startMin;
            const endMinutes = endHour * 60 + endMin;

            if (!isOvernight && endMinutes <= startMinutes) {
                isValid = false;
                endTimeInput.classList.add('border-red-500');
                errors.push('Untuk shift normal, jam selesai harus lebih besar dari jam mulai. Gunakan shift malam jika melewati tengah malam.');
            }
        }

        if (!isValid) {
            e.preventDefault();
            alert('Mohon perbaiki kesalahan berikut:\n' + errors.join('\n'));
        }
    });

    // Initialize
    updatePreview();

    // Set default break duration if old value exists
    if ({{ old('break_start') ? 'true' : 'false' }} || {{ old('break_end') ? 'true' : 'false' }}) {
        enableBreakCheckbox.checked = true;
        breakFields.classList.remove('hidden');
        breakStartInput.required = true;
        breakEndInput.required = true;
    }
}); code;

        // Update icon (first 2 characters of code or name)
        const iconText = code !== 'KODE'
            ? code.substring(0, 2)
            : (name !== 'Nama Shift' ? name.substring(0, 2).toUpperCase() : '??');
        previewIcon.querySelector('span').textContent = iconText;

        // Update time
        const startTime = startTimeInput.value || '00:00';
        const endTime = endTimeInput.value || '00:00';
        previewTime.textContent = `${startTime} - ${endTime}`;

        // Update type and icon color
        const isOvernight = isOvernightSelect.value === '1';
        previewType.textContent = isOvernight ? 'Shift Malam (Overnight)' : 'Shift Normal';

        // Update icon gradient
        if (isOvernight) {
            previewIcon.className = 'h-12 w-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center';
        } else {
            previewIcon.className = 'h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center';
        }

        // Update work duration
        updateWorkDuration();
    }

    // Calculate work duration
    function updateWorkDuration() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (startTime && endTime) {
            const start = new Date(`2000-01-01 ${startTime}`);
            let end = new Date(`2000-01-01 ${endTime}`);

            // Handle overnight shift
            if (end <= start) {
                end.setDate(end.getDate() + 1);
            }

            const diffMs = end - start;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            workDurationSpan.textContent = `${diffHours} jam ${diffMinutes} menit`;
        } else {
            workDurationSpan.textContent = '-';
        }
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

    // Update preview when time inputs change
    startTimeInput.addEventListener('change', updatePreview);
    endTimeInput.addEventListener('change', updatePreview);
    isOvernightSelect.addEventListener('change', updatePreview);

    // Break time toggle
    enableBreakCheckbox.addEventListener('change', function() {
        if (this.checked) {
            breakFields.classList.remove('hidden');
            breakStartInput.required = true;
            breakEndInput.required = true;
        } else {
            breakFields.classList.add('hidden');
            breakStartInput.required = false;
            breakEndInput.required = false;
            breakStartInput.value = '';
            breakEndInput.value = '';
        }
    });

    // Break duration auto-calculation
    function calculateBreakDuration() {
        const breakStart = breakStartInput.value;
        const breakEnd = breakEndInput.value;

        if (breakStart && breakEnd) {
            const start = new Date(`2000-01-01 ${breakStart}`);
            const end = new Date(`2000-01-01 ${breakEnd}`);

            if (end > start) {
                const diffMs = end - start;
                const diffMinutes = Math.floor(diffMs / (1000 * 60));
                breakDurationInput.value = diffMinutes;
            }
        }
    }

    breakStartInput.addEventListener('change', calculateBreakDuration);
    breakEndInput.addEventListener('change', calculateBreakDuration);

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];

        // Validate required fields
        const requiredFields = ['name', 'code', 'start_time', 'end_time', 'break_duration', 'late_tolerance', 'early_checkout_tolerance'];
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                errors.push(`Field ${fieldName} wajib diisi`);
            } else {
                field.classList.remove('border-red-500');
            }
        });

        // Validate break time if enabled
        if (enableBreakCheckbox.checked) {
            const breakStart = breakStartInput.value;
            const breakEnd = breakEndInput.value;

            if (!breakStart || !breakEnd) {
                isValid = false;
                if (!breakStart) breakStartInput.classList.add('border-red-500');
                if (!breakEnd) breakEndInput.classList.add('border-red-500');
                errors.push('Jam istirahat harus diisi jika waktu istirahat diaktifkan');
            } else if (breakStart >= breakEnd) {
                isValid = false;
                breakEndInput.classList.add('border-red-500');
                errors.push('Jam akhir istirahat harus lebih besar dari jam mulai istirahat');
            }
        }

        // Validate code length
        if (codeInput.value.trim().length < 2) {
            isValid = false;
            codeInput.classList.add('border-red-500');
            errors.push('Kode shift minimal 2 karakter');
        }

        // Validate time logic for overnight shifts
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const isOvernight = isOvernightSelect.value === '1';

        if (startTime && endTime && !isOvernight && endTime <= startTime) {
            isValid = false;
            endTimeInput.classList.add('border-red-500');
            errors.push('Untuk shift normal, jam selesai harus lebih besar dari jam mulai. Gunakan shift malam jika melewati tengah malam.');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Mohon perbaiki kesalahan berikut:\n' + errors.join('\n'));
        }
    });

    // Initialize
    updatePreview();

    // Set default break duration if old value exists
    if ({{ old('break_start') ? 'true' : 'false' }} || {{ old('break_end') ? 'true' : 'false' }}) {
        enableBreakCheckbox.checked = true;
        breakFields.classList.remove('hidden');
        breakStartInput.required = true;
        breakEndInput.required = true;
    }
});

function resetPreview() {
    // Reset preview to default values
    document.getElementById('preview-icon').querySelector('span').textContent = '??';
    document.getElementById('preview-name').textContent = 'Nama Shift';
    document.getElementById('preview-code').textContent = 'KODE';
    document.getElementById('preview-time').textContent = '00:00 - 00:00';
    document.getElementById('preview-type').textContent = 'Shift Normal';
    document.getElementById('work-duration').textContent = '-';

    // Reset icon color
    document.getElementById('preview-icon').className = 'h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center';

    // Reset break fields
    document.getElementById('enable_break').checked = false;
    document.getElementById('break-fields').classList.add('hidden');
    document.getElementById('break_start').required = false;
    document.getElementById('break_end').required = false;
}

// Preset shift templates
function applyShiftTemplate(template) {
    const templates = {
        'pagi': {
            name: 'Shift Pagi',
            code: 'PAGI',
            start_time: '08:00',
            end_time: '16:00',
            break_start: '12:00',
            break_end: '13:00',
            break_duration: 60,
            is_overnight: false
        },
        'siang': {
            name: 'Shift Siang',
            code: 'SIANG',
            start_time: '14:00',
            end_time: '22:00',
            break_start: '18:00',
            break_end: '19:00',
            break_duration: 60,
            is_overnight: false
        },
        'malam': {
            name: 'Shift Malam',
            code: 'MALAM',
            start_time: '22:00',
            end_time: '06:00',
            break_start: '02:00',
            break_end: '03:00',
            break_duration: 60,
            is_overnight: true
        }
    };

    if (templates[template]) {
        const data = templates[template];

        document.getElementById('name').value = data.name;
        document.getElementById('code').value = data.code;
        document.getElementById('start_time').value = data.start_time;
        document.getElementById('end_time').value = data.end_time;
        document.getElementById('is_overnight').value = data.is_overnight ? '1' : '0';

        if (data.break_start) {
            document.getElementById('enable_break').checked = true;
            document.getElementById('break-fields').classList.remove('hidden');
            document.getElementById('break_start').value = data.break_start;
            document.getElementById('break_end').value = data.break_end;
            document.getElementById('break_start').required = true;
            document.getElementById('break_end').required = true;
        }

        document.getElementById('break_duration').value = data.break_duration;

        // Trigger update
        document.getElementById('name').dispatchEvent(new Event('input'));
    }
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').submit();
    }

    // Ctrl/Cmd + R to reset
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        document.querySelector('button[type="reset"]').click();
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

/* Time input styling untuk format 24 jam */
input[type="time"] {
    cursor: pointer;
}

/* Memastikan format 24 jam di semua browser */
.time-input {
    -webkit-appearance: none;
    -moz-appearance: textfield;
}

/* Styling khusus untuk input time 24 jam */
.time-input::-webkit-datetime-edit {
    padding: 0;
}

.time-input::-webkit-datetime-edit-fields-wrapper {
    background: transparent;
}

.time-input::-webkit-datetime-edit-text {
    color: #374151;
    padding: 0 0.25rem;
}

.time-input::-webkit-datetime-edit-month-field,
.time-input::-webkit-datetime-edit-day-field,
.time-input::-webkit-datetime-edit-year-field,
.time-input::-webkit-datetime-edit-hour-field,
.time-input::-webkit-datetime-edit-minute-field {
    color: #374151;
    background: transparent;
    border: none;
    padding: 0;
}

.time-input::-webkit-datetime-edit-ampm-field {
    display: none; /* Hide AM/PM untuk memaksa format 24 jam */
}

/* Styling untuk calendar dan clock icons */
.time-input::-webkit-calendar-picker-indicator {
    background: transparent;
    color: #6B7280;
    cursor: pointer;
    width: 20px;
    height: 20px;
    opacity: 0.7;
}

.time-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Firefox specific styling */
.time-input::-moz-placeholder {
    color: #9CA3AF;
    opacity: 1;
}

/* Break fields animation */
#break-fields {
    transition: all 0.3s ease-in-out;
}

#break-fields.hidden {
    opacity: 0;
    max-height: 0;
    overflow: hidden;
}

#break-fields:not(.hidden) {
    opacity: 1;
    max-height: 200px;
}

/* Gradient background for different shift types */
.shift-normal {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
}

.shift-overnight {
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
}

/* Template buttons */
.template-btn {
    @apply px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors cursor-pointer;
}

.template-btn:hover {
    @apply bg-blue-50 text-blue-700;
}

/* Floating label effect */
.floating-label {
    position: relative;
}

.floating-label input:focus + label,
.floating-label input:not(:placeholder-shown) + label {
    transform: translateY(-1.5rem) scale(0.875);
    color: #3b82f6;
}

.floating-label label {
    position: absolute;
    left: 0.75rem;
    top: 0.5rem;
    transition: all 0.2s ease;
    pointer-events: none;
    background: white;
    padding: 0 0.25rem;
}
</style>
@endpush
