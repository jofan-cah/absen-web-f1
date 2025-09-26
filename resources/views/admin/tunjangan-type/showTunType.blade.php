@extends('admin.layouts.app')

@section('title', 'Detail Nominal Tunjangan')
@section('breadcrumb', 'Detail Nominal Tunjangan')
@section('page_title', 'Detail Nominal Tunjangan')

@section('page_actions')
<div class="flex gap-2">
    <a href="{{ route('admin.tunjangan-detail.edit', $tunjanganDetail->tunjangan_detail_id) }}"
       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
    </a>
    <button onclick="toggleStatus('{{ $tunjanganDetail->tunjangan_detail_id }}', {{ $tunjanganDetail->is_active ? 'false' : 'true' }})"
            class="px-4 py-2 bg-{{ $tunjanganDetail->is_active ? 'red' : 'green' }}-600 text-white rounded-lg hover:bg-{{ $tunjanganDetail->is_active ? 'red' : 'green' }}-700 transition-colors flex items-center gap-2">
        @if($tunjanganDetail->is_active)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
            Nonaktifkan
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Aktifkan
        @endif
    </button>
    <a href="{{ route('admin.tunjangan-type.show', $tunjanganDetail->tunjangan_type_id) }}"
       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Jenis Tunjangan
    </a>
    <a href="{{ route('admin.tunjangan-detail.index') }}"
       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')

<!-- Header Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-xl bg-gradient-to-br
                    @if($tunjanganDetail->tunjanganType->category == 'harian') from-orange-400 to-red-500
                    @elseif($tunjanganDetail->tunjanganType->category == 'mingguan') from-blue-400 to-cyan-500
                    @else from-green-400 to-emerald-500
                    @endif
                    flex items-center justify-center">
                    @if($tunjanganDetail->tunjanganType->category == 'harian')
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    @elseif($tunjanganDetail->tunjanganType->category == 'mingguan')
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $tunjanganDetail->tunjanganType->name }}</h1>
                    <div class="flex items-center space-x-4 mt-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ ucfirst(str_replace('_', ' ', $tunjanganDetail->staff_status)) }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            ID: {{ $tunjanganDetail->tunjangan_detail_id }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                @if($tunjanganDetail->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                        Tidak Aktif
                    </span>
                @endif
                <div class="text-xs text-gray-500 mt-1">
                    Dibuat: {{ $tunjanganDetail->created_at->format('d M Y H:i') }}
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
                <h3 class="text-lg font-semibold text-gray-900">Informasi Detail Nominal</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Tunjangan</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium">{{ $tunjanganDetail->tunjanganType->name }}</span>
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                                        {{ $tunjanganDetail->tunjanganType->code }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Kategori: {{ ucfirst($tunjanganDetail->tunjanganType->category) }}
                                </div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Karyawan</dt>
                            <dd class="mt-1">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $tunjanganDetail->staff_status)) }}
                                    </span>
                                </div>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nominal</dt>
                            <dd class="mt-1">
                                <div class="text-2xl font-bold text-gray-900">
                                    Rp {{ number_format($tunjanganDetail->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Per {{ $tunjanganDetail->tunjanganType->category }}
                                </div>
                            </dd>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Periode Berlaku</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <div class="font-medium">{{ $tunjanganDetail->effective_date->format('d M Y') }}</div>
                                @if($tunjanganDetail->end_date)
                                    <div class="text-xs text-red-600">s/d {{ $tunjanganDetail->end_date->format('d M Y') }}</div>
                                @else
                                    <div class="text-xs text-green-600">Berlaku selamanya</div>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($tunjanganDetail->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nominal Dasar Jenis Tunjangan</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                Rp {{ number_format($tunjanganDetail->tunjanganType->base_amount, 0, ',', '.') }}
                                @php
                                    $percentage = ($tunjanganDetail->amount / $tunjanganDetail->tunjanganType->base_amount) * 100;
                                @endphp
                                <div class="text-xs {{ $percentage > 100 ? 'text-green-600' : ($percentage < 100 ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ number_format($percentage, 1) }}% dari nominal dasar
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison with Other Staff Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Perbandingan dengan Status Lain</h3>
                <p class="text-sm text-gray-600 mt-1">Nominal untuk jenis tunjangan {{ $tunjanganDetail->tunjanganType->name }} berdasarkan status karyawan</p>
            </div>
            <div class="p-6">
                @php
                    $otherDetails = $tunjanganDetail->tunjanganType->tunjanganDetails()
                        ->where('is_active', true)
                        ->orderBy('amount', 'desc')
                        ->get();
                @endphp

                @if($otherDetails->count() > 0)
                    <div class="space-y-3">
                        @foreach($otherDetails as $detail)
                        <div class="flex items-center justify-between p-3 rounded-lg border {{ $detail->tunjangan_detail_id == $tunjanganDetail->tunjangan_detail_id ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 {{ $detail->tunjangan_detail_id == $tunjanganDetail->tunjangan_detail_id ? 'text-blue-900' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $detail->staff_status)) }}
                                        @if($detail->tunjangan_detail_id == $tunjanganDetail->tunjangan_detail_id)
                                            <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded">Saat Ini</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Berlaku: {{ $detail->effective_date->format('d M Y') }}
                                        @if($detail->end_date)
                                            - {{ $detail->end_date->format('d M Y') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-gray-900 {{ $detail->tunjangan_detail_id == $tunjanganDetail->tunjangan_detail_id ? 'text-blue-900' : '' }}">
                                    Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                </div>
                                @if($detail->tunjangan_detail_id != $tunjanganDetail->tunjangan_detail_id)
                                    @php
                                        $diff = $detail->amount - $tunjanganDetail->amount;
                                        $diffPercent = (($detail->amount - $tunjanganDetail->amount) / $tunjanganDetail->amount) * 100;
                                    @endphp
                                    <div class="text-xs {{ $diff > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $diff > 0 ? '+' : '' }}Rp {{ number_format(abs($diff), 0, ',', '.') }}
                                        ({{ $diff > 0 ? '+' : '' }}{{ number_format($diffPercent, 1) }}%)
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-500">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <p>Belum ada detail nominal lain untuk jenis tunjangan ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar (1/3) -->
    <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Nominal</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($tunjanganDetail->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">vs Nominal Dasar</span>
                    <span class="font-semibold {{ $percentage > 100 ? 'text-green-600' : ($percentage < 100 ? 'text-red-600' : 'text-gray-900') }}">
                        {{ number_format($percentage, 1) }}%
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Kategori</span>
                    <span class="font-semibold text-gray-900">{{ ucfirst($tunjanganDetail->tunjanganType->category) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="font-semibold {{ $tunjanganDetail->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $tunjanganDetail->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Detail Nominal Dibuat</div>
                        <div class="text-xs text-gray-500">{{ $tunjanganDetail->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>

                @if($tunjanganDetail->updated_at != $tunjanganDetail->created_at)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Terakhir Diubah</div>
                        <div class="text-xs text-gray-500">{{ $tunjanganDetail->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                @endif

                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Mulai Berlaku</div>
                        <div class="text-xs text-gray-500">{{ $tunjanganDetail->effective_date->format('d M Y') }}</div>
                    </div>
                </div>

                @if($tunjanganDetail->end_date)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">Berakhir</div>
                        <div class="text-xs text-gray-500">{{ $tunjanganDetail->end_date->format('d M Y') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.tunjangan-detail.edit', $tunjanganDetail->tunjangan_detail_id) }}"
                   class="w-full flex items-center justify-center px-3 py-2 text-sm text-yellow-700 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Detail
                </a>
                <a href="{{ route('admin.tunjangan-type.show', $tunjanganDetail->tunjangan_type_id) }}"
                   class="w-full flex items-center justify-center px-3 py-2 text-sm text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Lihat Jenis Tunjangan
                </a>
                <a href="{{ route('admin.tunjangan-detail.index') }}?tunjangan_type_id={{ $tunjanganDetail->tunjangan_type_id }}"
                   class="w-full flex items-center justify-center px-3 py-2 text-sm text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Detail Nominal Lain
                </a>
                <a href="{{ route('admin.tunjangan-detail.create') }}?tunjangan_type_id={{ $tunjanganDetail->tunjangan_type_id }}"
                   class="w-full flex items-center justify-center px-3 py-2 text-sm text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Detail Baru
                </a>
            </div>
        </div>

        <!-- Related Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Terkait</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total Detail untuk Jenis Ini</span>
                    <span class="font-medium">{{ $tunjanganDetail->tunjanganType->tunjanganDetails->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Detail Aktif</span>
                    <span class="font-medium text-green-600">{{ $tunjanganDetail->tunjanganType->tunjanganDetails->where('is_active', true)->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Nominal Terendah</span>
                    <span class="font-medium">Rp {{ number_format($tunjanganDetail->tunjanganType->tunjanganDetails->min('amount'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Nominal Tertinggi</span>
                    <span class="font-medium">Rp {{ number_format($tunjanganDetail->tunjanganType->tunjanganDetails->max('amount'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Rata-rata</span>
                    <span class="font-medium">Rp {{ number_format($tunjanganDetail->tunjanganType->tunjanganDetails->avg('amount'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle status functionality
function toggleStatus(id, newStatus) {
    const statusText = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';

    if (confirm(`Apakah Anda yakin ingin ${statusText} detail nominal ini?`)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.tunjangan-detail.index') }}/${id}/toggle-status`;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Loading functions (assuming these exist in your main layout)
function showLoading() {
    console.log('Loading...');
}

function hideLoading() {
    console.log('Loading complete');
}
</script>
@endpush
