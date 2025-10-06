@extends('admin.layouts.app')

@section('title', 'Detail Tipe Ijin')
@section('breadcrumb', 'Detail Tipe Ijin')
@section('page_title', 'Detail Tipe Ijin')

@section('content')
<div class="container-fluid px-4 py-6">

    <!-- Header Actions -->
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('admin.ijin-type.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <div class="flex gap-2">
            <a href="{{ route('admin.ijin-type.edit', $ijinType->ijin_type_id) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            <!-- Toggle Status -->
            <form action="{{ route('admin.ijin-type.toggle-status', $ijinType->ijin_type_id) }}"
                method="POST" class="inline"
                onsubmit="return confirm('Yakin ingin mengubah status tipe ijin ini?')">
                @csrf
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 {{ $ijinType->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm font-medium rounded-lg transition-colors">
                    @if($ijinType->is_active)
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Nonaktifkan
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aktifkan
                    @endif
                </button>
            </form>
        </div>
    </div>

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-pink-500 to-rose-600 rounded-xl shadow-lg mb-6 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">{{ $ijinType->name }}</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                                Code: {{ $ijinType->code }}
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                ID: {{ $ijinType->ijin_type_id }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @if($ijinType->is_active)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm text-white border border-white/30">
                            <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm text-white border border-white/30">
                            <div class="w-2 h-2 bg-white rounded-full mr-2"></div>
                            Nonaktif
                        </span>
                    @endif
                    <div class="text-xs text-white/80 mt-2">
                        Dibuat: {{ $ijinType->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info (2/3) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Detail Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Detail</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Tipe Ijin</dt>
                                <dd class="mt-1 text-base font-semibold text-gray-900">{{ $ijinType->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kode</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold bg-blue-100 text-blue-800">
                                        {{ $ijinType->code }}
                                    </span>
                                    @if(in_array($ijinType->code, ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave']))
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                            Default
                                        </span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @if($ijinType->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                            Nonaktif
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Tipe Ijin</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $ijinType->ijin_type_id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $ijinType->created_at->format('d F Y, H:i') }} WIB
                                    <span class="text-gray-500">({{ $ijinType->created_at->diffForHumans() }})</span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $ijinType->updated_at->format('d F Y, H:i') }} WIB
                                    <span class="text-gray-500">({{ $ijinType->updated_at->diffForHumans() }})</span>
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($ijinType->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-2">Deskripsi</dt>
                        <dd class="text-sm text-gray-700 leading-relaxed">
                            {{ $ijinType->description }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik Penggunaan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Total Penggunaan</p>
                                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $ijinType->ijins_count ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-blue-600 mt-3">Semua pengajuan ijin</p>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600">Disetujui</p>
                                    <p class="text-3xl font-bold text-green-900 mt-2">
                                        {{ $ijinType->ijins()->where('status', 'approved')->count() }}
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-green-600 mt-3">Ijin yang disetujui</p>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-4 border border-yellow-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-yellow-600">Pending</p>
                                    <p class="text-3xl font-bold text-yellow-900 mt-2">
                                        {{ $ijinType->ijins()->where('status', 'pending')->count() }}
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-yellow-600 mt-3">Menunggu approval</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar (1/3) -->
        <div class="space-y-6">

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.ijin-type.edit', $ijinType->ijin_type_id) }}"
                       class="w-full flex items-center justify-center px-3 py-2.5 text-sm text-yellow-700 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Tipe Ijin
                    </a>

                    <a href="{{ route('admin.ijin-type.index') }}"
                       class="w-full flex items-center justify-center px-3 py-2.5 text-sm text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Lihat Semua Tipe
                    </a>

                    <a href="{{ route('admin.ijin-type.create') }}"
                       class="w-full flex items-center justify-center px-3 py-2.5 text-sm text-white bg-gradient-to-r from-pink-500 to-rose-600 rounded-lg hover:from-pink-600 hover:to-rose-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Tipe Baru
                    </a>
                </div>
            </div>

            <!-- System Info -->
            @if(in_array($ijinType->code, ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave']))
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-yellow-800">Tipe Default Sistem</h3>
                        <div class="mt-2 text-xs text-yellow-700 space-y-1">
                            <p>• Tidak dapat dihapus</p>
                            <p>• Kode tidak dapat diubah</p>
                            <p>• Digunakan oleh sistem</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tipe ijin ini sudah ada selama {{ $ijinType->created_at->diffForHumans(null, true) }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Digunakan {{ $ijinType->ijins_count ?? 0 }} kali</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Code: <code class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $ijinType->code }}</code></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
