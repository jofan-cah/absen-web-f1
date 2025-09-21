@extends('admin.layouts.app')

@section('title', 'Edit Shift')
@section('breadcrumb', 'Edit Shift')
@section('page_title', 'Edit Shift Kerja')

@section('page_actions')
<div class="flex items-center space-x-3">
    <a href="{{ route('admin.shift.show', $shift->shift_id) }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        Detail
    </a>
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
        <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-white">Edit Shift Kerja</h2>
                    <p class="text-yellow-100 text-sm mt-1">Perbarui informasi shift "{{ $shift->name }}"</p>
                </div>
            </div>
        </div>

        <!-- Current Shift Info -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br {{ $shift->is_overnight ? 'from-purple-500 to-indigo-600' : 'from-blue-500 to-cyan-600' }} flex items-center justify-center">
                    @if($shift->is_overnight)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $shift->name }}</h3>
                    <p class="text-sm text-gray-500 font-mono">{{ $shift->code }}</p>
                    <p class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        @if($shift->is_overnight)
                            <span class="text-purple-600">(Shift Malam)</span>
                        @endif
                    </p>
                </div>
                @if($shift->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                        Tidak Aktif
                    </span>
                @endif
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
        <form action="{{ route('admin.shift.update', $shift->shift_id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Basic Information Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Dasar
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui informasi dasar shift kerja</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Shift -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Shift <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $shift->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('name') border-red-500 @enderror"
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
                        <input type="text" id="code" name="code" value="{{ old('code', $shift->code) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('code') border-red-500 @enderror"
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('is_overnight') border-red-500 @enderror">
                            <option value="0" {{ old('is_overnight', $shift->is_overnight ? '1' : '0') == '0' ? 'selected' : '' }}>Shift Normal</option>
                            <option value="1" {{ old('is_overnight', $shift->is_overnight ? '1' : '0') == '1' ? 'selected' : '' }}>Shift Malam (Overnight)</option>
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
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Jam Kerja
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui waktu mulai dan selesai kerja</p>
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
                                   value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}"
                                   required
                                   placeholder="08:00"
                                   pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                   maxlength="5"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('start_time') border-red-500 @enderror time-input-custom">
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
                                   value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}"
                                   required
                                   placeholder="16:00"
                                   pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                   maxlength="5"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('end_time') border-red-500 @enderror time-input-custom">
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
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-yellow-800">Durasi Kerja: </span>
                        <span id="work-duration" class="text-sm text-yellow-700 ml-1">-</span>
                    </div>
                </div>
            </div>

            <!-- Break Time Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Waktu Istirahat
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui waktu istirahat (opsional)</p>
                </div>

                <div class="space-y-4">
                    <!-- Enable Break Toggle -->
                    <div class="flex items-center">
                        <input type="checkbox" id="enable_break" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
                               {{ ($shift->break_start && $shift->break_end) ? 'checked' : '' }}>
                        <label for="enable_break" class="ml-2 text-sm font-medium text-gray-700">
                            Aktifkan waktu istirahat
                        </label>
                    </div>

                    <!-- Break Time Fields -->
                    <div id="break-fields" class="grid grid-cols-1 md:grid-cols-3 gap-6 {{ ($shift->break_start && $shift->break_end) ? '' : 'hidden' }}">
                        <!-- Jam Mulai Istirahat -->
                        <div>
                            <label for="break_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Jam Mulai Istirahat
                            </label>
                            <div class="relative">
                                <input type="text"
                                       id="break_start"
                                       name="break_start"
                                       value="{{ old('break_start', $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '') }}"
                                       placeholder="12:00"
                                       pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                       maxlength="5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('break_start') border-red-500 @enderror time-input-custom">
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
                                       value="{{ old('break_end', $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '') }}"
                                       placeholder="13:00"
                                       pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$"
                                       maxlength="5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('break_end') border-red-500 @enderror time-input-custom">
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
                            <input type="number" id="break_duration" name="break_duration" value="{{ old('break_duration', $shift->break_duration) }}" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('break_duration') border-red-500 @enderror">
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
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Toleransi Waktu
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Perbarui toleransi keterlambatan dan pulang lebih awal</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Toleransi Terlambat -->
                    <div>
                        <label for="late_tolerance" class="block text-sm font-medium text-gray-700 mb-2">
                            Toleransi Terlambat (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="late_tolerance" name="late_tolerance" value="{{ old('late_tolerance', $shift->late_tolerance) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('late_tolerance') border-red-500 @enderror">
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
                        <input type="number" id="early_checkout_tolerance" name="early_checkout_tolerance" value="{{ old('early_checkout_tolerance', $shift->early_checkout_tolerance) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 @error('early_checkout_tolerance') border-red-500 @enderror">
                        @error('early_checkout_tolerance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Waktu toleransi sebelum dianggap pulang lebih awal</p>
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="mb-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Status Shift
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Kelola status aktif shift</p>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $shift->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                            Shift Aktif
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">Hanya shift aktif yang dapat digunakan dalam penjadwalan</p>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="mb-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Perubahan
                </h4>
                <div class="flex items-center space-x-4">
                    <div id="preview-icon" class="h-12 w-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ substr($shift->code, 0, 2) }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 id="preview-name" class="text-lg font-semibold text-gray-900">{{ $shift->name }}</h3>
                        <p id="preview-code" class="text-sm text-gray-500 font-mono">{{ $shift->code }}</p>
                        <p id="preview-time" class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</p>
                        <p id="preview-type" class="text-sm text-gray-600">{{ $shift->is_overnight ? 'Shift Malam (Overnight)' : 'Shift Normal' }}</p>
                    </div>
                    <span id="preview-status" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $shift->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <div class="w-2 h-2 {{ $shift->is_active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }} rounded-full mr-1"></div>
                        {{ $shift->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>

            <!-- Usage Warning -->
            @if($shift->jadwals_count > 0)
            <div class="mb-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Peringatan Shift Sedang Digunakan</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Shift ini sedang digunakan dalam <strong>{{ $shift->jadwals_count }} jadwal</strong>. Perubahan pada jam kerja akan mempengaruhi jadwal yang sudah ada.</p>
                            <ul class="mt-2 list-disc list-inside">
                                <li>Perubahan jam kerja akan berlaku untuk jadwal yang belum dimulai</li>
                                <li>Menonaktifkan shift akan membuat jadwal yang menggunakan shift ini tidak dapat digunakan</li>
                                <li>Pastikan perubahan tidak mengganggu operasional yang sedang berjalan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.shift.show', $shift->shift_id) }}"
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="reset" onclick="resetToOriginal()"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Reset
                    </button>
                    <button type="submit" class="px-8 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Shift
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Tips Card -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Tips Update Shift</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Periksa jadwal yang menggunakan shift ini sebelum melakukan perubahan besar</li>
                        <li>Perubahan jam kerja akan mempengaruhi perhitungan absensi</li>
                        <li>Jika shift sedang digunakan, koordinasikan dengan tim terkait</li>
                        <li>Backup data penting sebelum melakukan perubahan kriteria</li>
                        <li>Test perubahan di environment staging jika memungkinkan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Store original values
const originalValues = {
    name: '{{ $shift->name }}',
    code: '{{ $shift->code }}',
    start_time: '{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}',
    end_time: '{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}',
    break_start: '{{ $shift->break_start ? \Carbon\Carbon::parse($shift->break_start)->format('H:i') : '' }}',
    break_end: '{{ $shift->break_end ? \Carbon\Carbon::parse($shift->break_end)->format('H:i') : '' }}',
    break_duration: '{{ $shift->break_duration }}',
    late_tolerance: '{{ $shift->late_tolerance }}',
    early_checkout_tolerance: '{{ $shift->early_checkout_tolerance }}',
    is_overnight: '{{ $shift->is_overnight ? '1' : '0' }}',
    is_active: {{ $shift->is_active ? 'true' : 'false' }}
};

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const isOvernightSelect = document.getElementById('is_overnight');
    const isActiveCheckbox = document.getElementById('is_active');
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
    const previewStatus = document.getElementById('preview-status');
    const workDurationSpan = document.getElementById('work-duration');

    // Time input formatting and validation (same as create form)
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
        const name = nameInput.value.trim() || originalValues.name;
        previewName.textContent = name;

        // Update code
        const code = codeInput.value.trim().toUpperCase() || originalValues.code;
        previewCode.textContent = code;

        // Update icon
        const iconText = code.substring(0, 2);
        previewIcon.querySelector('span').textContent = iconText;

        // Update time
        const startTime = startTimeInput.value || originalValues.start_time;
        const endTime = endTimeInput.value || originalValues.end_time;
        previewTime.textContent = `${startTime} - ${endTime}`;

        // Update type and icon color
        const isOvernight = isOvernightSelect.value === '1';
        previewType.textContent = isOvernight ? 'Shift Malam (Overnight)' : 'Shift Normal';

        // Update icon gradient
        if (isOvernight) {
            previewIcon.className = 'h-12 w-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center';
        } else {
            previewIcon.className = 'h-12 w-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center';
        }

        // Update status
        const isActive = isActiveCheckbox.checked;
        if (isActive) {
            previewStatus.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            previewStatus.innerHTML = '<div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>Aktif';
        } else {
            previewStatus.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
            previewStatus.innerHTML = '<div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>Tidak Aktif';
        }

        // Update work duration
        updateWorkDuration();
    }

    // Calculate work duration (same logic as create)
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

    // Event listeners for preview updates
    nameInput.addEventListener('input', updatePreview);
    startTimeInput.addEventListener('input', updatePreview);
    endTimeInput.addEventListener('input', updatePreview);
    startTimeInput.addEventListener('blur', updatePreview);
    endTimeInput.addEventListener('blur', updatePreview);
    isOvernightSelect.addEventListener('change', updatePreview);
    isActiveCheckbox.addEventListener('change', updatePreview);

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

    // Form validation (same as create form)
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
});

// Reset to original values
function resetToOriginal() {
    document.getElementById('name').value = originalValues.name;
    document.getElementById('code').value = originalValues.code;
    document.getElementById('start_time').value = originalValues.start_time;
    document.getElementById('end_time').value = originalValues.end_time;
    document.getElementById('break_start').value = originalValues.break_start;
    document.getElementById('break_end').value = originalValues.break_end;
    document.getElementById('break_duration').value = originalValues.break_duration;
    document.getElementById('late_tolerance').value = originalValues.late_tolerance;
    document.getElementById('early_checkout_tolerance').value = originalValues.early_checkout_tolerance;
    document.getElementById('is_overnight').value = originalValues.is_overnight;
    document.getElementById('is_active').checked = originalValues.is_active;

    // Reset break fields visibility
    const hasBreak = originalValues.break_start && originalValues.break_end;
    document.getElementById('enable_break').checked = hasBreak;
    if (hasBreak) {
        document.getElementById('break-fields').classList.remove('hidden');
        document.getElementById('break_start').required = true;
        document.getElementById('break_end').required = true;
    } else {
        document.getElementById('break-fields').classList.add('hidden');
        document.getElementById('break_start').required = false;
        document.getElementById('break_end').required = false;
    }

    // Trigger preview update
    document.getElementById('name').dispatchEvent(new Event('input'));
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + S to save
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        document.querySelector('form').submit();
    }

    // Ctrl/Cmd + R to reset
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        resetToOriginal();
    }
});
</script>
@endpush

@push('styles')
<style>
/* Custom styling untuk input time text */
.time-input-custom {
    font-family: 'Courier New', monospace;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.time-input-custom:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.time-input-custom::placeholder {
    color: #9CA3AF;
    font-weight: normal;
}

/* Error state styling */
.time-input-custom.border-red-500 {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

/* Smooth focus transitions */
input:focus, select:focus, textarea:focus {
    transition: all 0.2s ease-in-out;
}

/* Preview card animation */
.preview-card {
    transition: all 0.3s ease;
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

/* Error message animation */
.error-message {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Auto-uppercase for code input */
#code {
    text-transform: uppercase;
}

/* Success state for valid time inputs */
.time-input-custom:valid:not(:placeholder-shown) {
    border-color: #10B981;
}

.time-input-custom:valid:not(:placeholder-shown) + div svg {
    color: #10B981;
}

/* Yellow theme for edit form */
.focus\:ring-yellow-500:focus {
    --tw-ring-color: rgb(245 158 11 / var(--tw-ring-opacity));
}

.focus\:border-yellow-500:focus {
    --tw-border-opacity: 1;
    border-color: rgb(245 158 11 / var(--tw-border-opacity));
}

/* Current shift info styling */
.current-shift-info {
    background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
}
</style>
@endpush
