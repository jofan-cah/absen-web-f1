@extends('admin.layouts.app')

@section('title', 'Detail User')
@section('breadcrumb', 'Detail User')
@section('page_title', 'Detail User Login')

@section('page_actions')
<div class="flex items-center gap-3">
    <a href="{{ route('admin.user.edit', $user->user_id) }}"
       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit User
    </a>
    <button onclick="resetPassword('{{ $user->user_id }}', '{{ $user->name }}')"
            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        Reset Password
    </button>
    <button onclick="toggleStatus('{{ $user->user_id }}', {{ $user->is_active ? 'false' : 'true' }})"
            class="px-4 py-2 bg-{{ $user->is_active ? 'red' : 'green' }}-600 text-white rounded-lg hover:bg-{{ $user->is_active ? 'red' : 'green' }}-700 transition-colors flex items-center gap-2">
        @if($user->is_active)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
            </svg>
            Nonaktifkan
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Aktifkan
        @endif
    </button>
    <a href="{{ route('admin.user.index') }}"
       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Header Profile Section -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-3xl p-8 mb-8 text-white relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>

        <div class="relative">
            <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                <!-- Avatar -->
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-3xl flex items-center justify-center border-4 border-white/30 shadow-xl">
                    <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                </div>

                <!-- User Info -->
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                            <p class="text-blue-100 text-lg mb-3">{{ $user->email }}</p>

                            <div class="flex flex-wrap items-center gap-3">
                                <!-- User ID -->
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2 border border-white/30">
                                    <span class="text-sm font-medium text-blue-100">ID:</span>
                                    <span class="font-mono text-white ml-1">{{ $user->user_id }}</span>
                                </div>

                                <!-- NIP -->
                                @if($user->nip)
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-3 py-2 border border-white/30">
                                    <span class="text-sm font-medium text-blue-100">NIP:</span>
                                    <span class="font-mono text-white ml-1">{{ $user->nip }}</span>
                                </div>
                                @endif

                                <!-- Role Badge -->
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-purple-500/30 text-purple-100 border border-purple-300/30">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.586-3.414A2 2 0 0118.414 8l-1.707-1.707A1 1 0 0115.293 6h-2.586a1 1 0 01-.707-.293L10.293 4a1 1 0 00-.707-.293H7a1 1 0 00-1 1v14a1 1 0 001 1h10a1 1 0 001-1V10.414a1 1 0 00-.293-.707z"/>
                                        </svg>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-blue-500/30 text-blue-100 border border-blue-300/30">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        User
                                    </span>
                                @endif

                                <!-- Status -->
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-green-500/30 text-green-100 border border-green-300/30">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-red-500/30 text-red-100 border border-red-300/30">
                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Account Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informasi Akun
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nama Lengkap</label>
                                <p class="text-base font-semibold text-gray-900 mt-1">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-base text-gray-900 mt-1">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Role</label>
                                <p class="text-base text-gray-900 mt-1 capitalize">{{ $user->role }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">User ID</label>
                                <p class="text-base font-mono bg-gray-100 px-3 py-2 rounded-lg text-gray-900 mt-1">{{ $user->user_id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">NIP</label>
                                <p class="text-base text-gray-900 mt-1">
                                    @if($user->nip)
                                        <span class="font-mono bg-gray-100 px-3 py-2 rounded-lg">{{ $user->nip }}</span>
                                    @else
                                        <span class="text-gray-400">Tidak ada</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status Akun</label>
                                <div class="mt-1">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                            Aktif - Dapat login
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                            Tidak Aktif - Tidak dapat login
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Profile (if exists) -->
            @if($user->karyawan)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-green-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Profil Karyawan Terkait
                    </h3>
                </div>

                <div class="p-6">
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">{{ $user->karyawan->full_name }}</h4>
                            <p class="text-gray-600">{{ $user->karyawan->position }}</p>
                            <p class="text-sm text-gray-500">{{ $user->karyawan->department->name ?? 'Department tidak ditemukan' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">ID Karyawan</label>
                                <p class="text-base font-mono bg-gray-100 px-3 py-2 rounded-lg text-gray-900 mt-1">{{ $user->karyawan->karyawan_id }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Posisi</label>
                                <p class="text-base text-gray-900 mt-1">{{ $user->karyawan->position }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Department</label>
                                <p class="text-base text-gray-900 mt-1">{{ $user->karyawan->department->name ?? 'Tidak ada' }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Tanggal Masuk</label>
                                <p class="text-base text-gray-900 mt-1">
                                    {{ $user->karyawan->hire_date ? $user->karyawan->hire_date->format('d M Y') : 'Tidak ada' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status Pekerjaan</label>
                                <p class="text-base text-gray-900 mt-1 capitalize">{{ $user->karyawan->employment_status }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">No. Telepon</label>
                                <p class="text-base text-gray-900 mt-1">{{ $user->karyawan->phone ?? 'Tidak ada' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Aksi Karyawan:</span>
                            <a href="{{ route('admin.karyawan.show', $user->karyawan->karyawan_id) }}"
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Detail Karyawan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- No Employee Profile -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-yellow-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Profil Karyawan
                    </h3>
                </div>

                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Profil Karyawan</h4>
                    <p class="text-gray-600 mb-6">User ini belum memiliki profil karyawan yang terkait.</p>
                    <a href="{{ route('admin.karyawan.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Buat Profil Karyawan
                    </a>
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column - Stats & Actions -->
        <div class="space-y-6">

            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Statistik
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Dibuat</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Last Update</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $user->updated_at->format('d M Y') }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Aktif Sejak</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Aksi Cepat
                </h3>

                <div class="space-y-3">
                    <button onclick="resetPassword('{{ $user->user_id }}', '{{ $user->name }}')"
                            class="w-full flex items-center justify-center px-4 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Reset Password
                    </button>

                    <button onclick="toggleStatus('{{ $user->user_id }}', {{ $user->is_active ? 'false' : 'true' }})"
                            class="w-full flex items-center justify-center px-4 py-3 bg-{{ $user->is_active ? 'red' : 'green' }}-600 text-white rounded-xl hover:bg-{{ $user->is_active ? 'red' : 'green' }}-700 transition-colors">
                        @if($user->is_active)
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                            </svg>
                            Nonaktifkan User
                        @else
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Aktifkan User
                        @endif
                    </button>

                    <a href="{{ route('admin.user.edit', $user->user_id) }}"
                       class="w-full flex items-center justify-center px-4 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit User
                    </a>

                    <button onclick="deleteUser('{{ $user->user_id }}', '{{ $user->name }}')"
                            class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus User
                    </button>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Informasi Sistem
                </h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Dibuat pada:</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Terakhir diubah:</span>
                        <span class="font-medium text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Aktif sejak:</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Role Level:</span>
                        <span class="font-medium {{ $user->role === 'admin' ? 'text-purple-600' : 'text-blue-600' }}">
                            {{ $user->role === 'admin' ? 'Administrator' : 'Standard User' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Security Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Keamanan
                </h3>

                <div class="space-y-4">
                    <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-900">Password Aman</p>
                                <p class="text-xs text-green-700">Password ter-enkripsi dengan baik</p>
                            </div>
                        </div>
                    </div>

                    @if($user->is_active)
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Akses Aktif</p>
                                <p class="text-xs text-blue-700">User dapat login ke sistem</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-red-50 rounded-xl border border-red-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-red-900">Akses Diblokir</p>
                                <p class="text-xs text-red-700">User tidak dapat login</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to action buttons
    const actionButtons = document.querySelectorAll('button[onclick]');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Add loading state
            const originalContent = this.innerHTML;
            this.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            `;
            this.disabled = true;

            // Restore after 2 seconds if no redirect happens
            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        });
    });
});

// Reset password functionality
function resetPassword(id, name) {
    if (confirm(`Apakah Anda yakin ingin reset password user "${name}"?\n\nPassword akan direset ke: password123`)) {
        showLoading();

        fetch(`/admin/user/${id}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                alert(`Success: ${data.message}`);
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Terjadi kesalahan saat reset password');
        });
    }
}

// Toggle status functionality
function toggleStatus(id, newStatus) {
    const statusText = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';
    const user = {!! json_encode($user) !!};

    // Prevent self-deactivation
    if (user.user_id === '{{ auth()->user()->user_id }}' && newStatus === 'false') {
        alert('Anda tidak dapat menonaktifkan akun sendiri!');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin ${statusText} user ini?`)) {
        showLoading();

        fetch(`/admin/user/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                alert(`Success: ${data.message}`);
                // Reload page to update status display
                window.location.reload();
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
}

// Delete user functionality
function deleteUser(id, name) {
    const user = {!! json_encode($user) !!};

    // Prevent self-deletion
    if (user.user_id === '{{ auth()->user()->user_id }}') {
        alert('Anda tidak dapat menghapus akun sendiri!');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus user "${name}"?\n\nPerhatian: User yang masih memiliki data karyawan terkait tidak dapat dihapus.\n\nTindakan ini tidak dapat dibatalkan!`)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/user/${id}`;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto refresh every 5 minutes to keep data current
setInterval(function() {
    // Only refresh if no modal is open
    const hasModal = document.querySelector('.modal:not(.hidden)');
    if (!hasModal) {
        console.log('Refreshing user data...');
        window.location.reload();
    }
}, 300000); // 5 minutes

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + E to edit
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        window.location.href = '{{ route("admin.user.edit", $user->user_id) }}';
    }

    // Ctrl/Cmd + R to reset password
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        resetPassword('{{ $user->user_id }}', '{{ $user->name }}');
    }

    // Escape to go back
    if (e.key === 'Escape') {
        window.location.href = '{{ route("admin.user.index") }}';
    }
});
</script>
@endpush

@push('styles')
<style>
/* Profile header animations */
.profile-header {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card animations */
.info-card {
    animation: slideInLeft 0.5s ease-out;
    animation-fill-mode: both;
}

.stats-card {
    animation: slideInRight 0.5s ease-out;
    animation-fill-mode: both;
}

.info-card:nth-child(1) { animation-delay: 0.1s; }
.info-card:nth-child(2) { animation-delay: 0.2s; }
.stats-card:nth-child(1) { animation-delay: 0.3s; }
.stats-card:nth-child(2) { animation-delay: 0.4s; }
.stats-card:nth-child(3) { animation-delay: 0.5s; }

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Enhanced hover effects */
.action-button {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.3s, height 0.3s;
}

.action-button:hover::before {
    width: 200px;
    height: 200px;
}

.action-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Status badges pulse effect */
.status-badge {
    position: relative;
}

.status-badge.active::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 8px;
    width: 8px;
    height: 8px;
    background: currentColor;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }
    50% {
        opacity: 0.5;
        transform: translateY(-50%) scale(1.2);
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .profile-header {
        padding: 1.5rem;
    }

    .profile-avatar {
        width: 4rem;
        height: 4rem;
    }

    .profile-title {
        font-size: 1.5rem;
    }

    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-buttons > * {
        width: 100%;
    }
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Security indicators */
.security-indicator {
    transition: all 0.2s ease;
}

.security-indicator:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Print styles */
@media print {
    .no-print,
    .page-actions,
    .action-button {
        display: none !important;
    }

    .profile-header {
        background: white !important;
        color: black !important;
        border: 1px solid #ccc;
    }

    .info-card,
    .stats-card {
        break-inside: avoid;
        border: 1px solid #ccc;
    }
}
</style>
@endpush
