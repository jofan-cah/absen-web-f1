@extends('admin.layouts.app')

@section('title', 'Laporan Tunjangan Karyawan')
@section('breadcrumb', 'Manajemen Tunjangan / Laporan')
@section('page_title', 'Laporan Tunjangan Karyawan')

@section('page_actions')
<div class="flex flex-wrap gap-3">
    <button onclick="exportReport()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export Laporan
    </button>

    <a href="{{ route('admin.tunjangan-karyawan.report-form') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Report PDF Bulanan
    </a>
     <a href="{{ route('admin.tunjangan-karyawan.single-week-form') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Report PDF Mingguan
    </a>
    <a href="{{ route('admin.tunjangan-karyawan.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>

</div>
@endsection

@section('content')

<div id="printable-area">

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Filter Periode Laporan</h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('admin.tunjangan-karyawan.report') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Update Laporan
                </button>
            </div>
            <div>
                <button type="button" onclick="setQuickPeriod('thisMonth')" class="px-3 py-2 text-sm bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors">
                    Bulan Ini
                </button>
            </div>
            <div>
                <button type="button" onclick="setQuickPeriod('lastMonth')" class="px-3 py-2 text-sm bg-indigo-100 text-indigo-800 rounded-lg hover:bg-indigo-200 transition-colors">
                    Bulan Lalu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Report Header -->
<div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-6 text-white mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold mb-2">Laporan Tunjangan Karyawan</h1>
            <p class="text-primary-100">
                Periode: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}
            </p>
        </div>
        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Tunjangan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($summary['total_tunjangan']) }}</p>
                <p class="text-sm text-blue-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Data tunjangan
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Nominal</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</p>
                <p class="text-sm text-green-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Nilai tunjangan
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Diterima</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($summary['received']) }}</p>
                <p class="text-sm text-indigo-600 mt-1">
                    <div class="flex items-center">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $summary['total_tunjangan'] > 0 ? round(($summary['received'] / $summary['total_tunjangan']) * 100, 1) : 0 }}% selesai
                    </div>
                </p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($summary['pending']) }}</p>
                <p class="text-sm text-yellow-600 mt-1">
                    <div class="flex items-center">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Perlu tindakan
                    </div>
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $summary['pending'] }}</p>
            </div>
            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Requested</p>
                <p class="text-2xl font-bold text-orange-600">{{ $summary['requested'] }}</p>
            </div>
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ $summary['approved'] }}</p>
            </div>
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Received</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $summary['received'] }}</p>
            </div>
            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analysis -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Tunjangan by Type -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tunjangan berdasarkan Jenis</h3>
        </div>
        <div class="p-6">
            @if($byType->count() > 0)
            <div class="space-y-4">
                @foreach($byType as $type)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                            @if($type->tunjanganType->category == 'harian') bg-orange-100
                            @elseif($type->tunjanganType->category == 'mingguan') bg-blue-100
                            @else bg-green-100
                            @endif">
                            @if($type->tunjanganType->category == 'harian')
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            @elseif($type->tunjanganType->category == 'mingguan')
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $type->tunjanganType->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $type->count }} tunjangan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">
                            Rp {{ number_format($type->total, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $summary['total_nominal'] > 0 ? round(($type->total / $summary['total_nominal']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p>Tidak ada data untuk periode ini</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Top 10 Karyawan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top 10 Karyawan</h3>
        </div>
        <div class="p-6">
            @if($byKaryawan->count() > 0)
            <div class="space-y-4">
                @foreach($byKaryawan as $index => $karyawan)
                <div class="flex items-center p-3 {{ $index < 3 ? 'bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200' : 'bg-gray-50' }} rounded-lg">
                    <div class="flex items-center flex-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3
                            {{ $index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-gray-400' : ($index == 2 ? 'bg-orange-600' : 'bg-gray-300')) }} text-white font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $karyawan->karyawan->full_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $karyawan->count }} tunjangan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-900">
                            Rp {{ number_format($karyawan->total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p>Tidak ada data karyawan untuk periode ini</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detailed Analysis -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Analisis Detail</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Average per Employee -->
            <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-blue-900 mb-1">Rata-rata per Karyawan</h4>
                @php
                    $uniqueKaryawan = $byKaryawan->count();
                    $avgPerKaryawan = $uniqueKaryawan > 0 ? $summary['total_nominal'] / $uniqueKaryawan : 0;
                @endphp
                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($avgPerKaryawan, 0, ',', '.') }}</p>
            </div>

            <!-- Completion Rate -->
            <div class="text-center p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-green-900 mb-1">Tingkat Selesai</h4>
                @php
                    $completionRate = $summary['total_tunjangan'] > 0 ? (($summary['approved'] + $summary['received']) / $summary['total_tunjangan']) * 100 : 0;
                @endphp
                <p class="text-2xl font-bold text-green-600">{{ number_format($completionRate, 1) }}%</p>
            </div>

            <!-- Pending Actions -->
            <div class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h4 class="font-semibold text-yellow-900 mb-1">Perlu Tindakan</h4>
                <p class="text-2xl font-bold text-yellow-600">{{ number_format($summary['pending'] + $summary['requested']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Report Summary Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Laporan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metrik</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div class="font-medium text-gray-900">Total Tunjangan</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-gray-900">
                        {{ number_format($summary['total_tunjangan']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500">100%</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div class="font-medium text-gray-900">Total Nominal</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-gray-900">
                        Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500">100%</td>
                </tr>
                <tr class="bg-yellow-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-gray-900">Status Pending</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-yellow-600">
                        {{ number_format($summary['pending']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-yellow-600">
                        {{ $summary['total_tunjangan'] > 0 ? number_format(($summary['pending'] / $summary['total_tunjangan']) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr class="bg-orange-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-gray-900">Status Requested</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-orange-600">
                        {{ number_format($summary['requested']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-orange-600">
                        {{ $summary['total_tunjangan'] > 0 ? number_format(($summary['requested'] / $summary['total_tunjangan']) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr class="bg-green-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="font-medium text-gray-900">Status Approved</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-green-600">
                        {{ number_format($summary['approved']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-green-600">
                        {{ $summary['total_tunjangan'] > 0 ? number_format(($summary['approved'] / $summary['total_tunjangan']) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr class="bg-indigo-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="font-medium text-gray-900">Status Received</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-indigo-600">
                        {{ number_format($summary['received']) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-indigo-600">
                        {{ $summary['total_tunjangan'] > 0 ? number_format(($summary['received'] / $summary['total_tunjangan']) * 100, 1) : 0 }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Report Footer -->
<div class="mt-8 text-center text-sm text-gray-500">
    <p>Laporan dibuat pada: {{ now()->format('d M Y H:i:s') }}</p>
    <p>Data periode: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>
</div>

</div>

@endsection

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    body { font-size: 12px; }
    .bg-gradient-to-r { background: #3b82f6 !important; -webkit-print-color-adjust: exact; }
    .shadow-sm { box-shadow: none !important; }
    .border { border-width: 1px !important; }
}

.chart-container {
    position: relative;
    height: 300px;
    margin: 20px 0;
}
</style>
@endpush

@push('scripts')
<script>
// Quick period setter
function setQuickPeriod(type) {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const today = new Date();

    if (type === 'thisMonth') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        startDateInput.value = firstDay.toISOString().split('T')[0];
        endDateInput.value = lastDay.toISOString().split('T')[0];
    } else if (type === 'lastMonth') {
        const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);

        startDateInput.value = firstDay.toISOString().split('T')[0];
        endDateInput.value = lastDay.toISOString().split('T')[0];
    }
}

// Export report functionality
function exportReport() {
    showLoading();

    // Get current filters
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    const exportUrl = '{{ route("admin.tunjangan-karyawan.export") }}?' + params.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `laporan-tunjangan-${startDate}-${endDate}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Hide loading after a delay since this is a download
    setTimeout(hideLoading, 2000);
}

// Print report functionality
function printReport() {
    window.print();
}

// Loading functions
function showLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }
}

// Initialize chart if needed (you can add Chart.js or other charting library here)
document.addEventListener('DOMContentLoaded', function() {
    // Add any chart initialization here
    console.log('Report loaded successfully');

    // Add smooth animations to cards
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Validate date range
document.getElementById('start_date').addEventListener('change', validateDateRange);
document.getElementById('end_date').addEventListener('change', validateDateRange);

function validateDateRange() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);

    if (startDate && endDate && startDate > endDate) {
        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir!');
        document.getElementById('end_date').value = document.getElementById('start_date').value;
    }
}

// Auto-submit form when dates change (with debounce)
let dateChangeTimeout;
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('#start_date, #end_date');

    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            clearTimeout(dateChangeTimeout);
            dateChangeTimeout = setTimeout(() => {
                // Auto-submit form after 1 second delay
                if (document.getElementById('start_date').value && document.getElementById('end_date').value) {
                    // Uncomment the line below if you want auto-refresh
                    // document.querySelector('form').submit();
                }
            }, 1000);
        });
    });
});

// Add tooltips for better UX
document.addEventListener('DOMContentLoaded', function() {
    const tooltipElements = document.querySelectorAll('[title]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-xs bg-gray-800 text-white rounded shadow-lg pointer-events-none';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.top = (e.pageY - 30) + 'px';
            tooltip.style.left = (e.pageX + 10) + 'px';

            document.body.appendChild(tooltip);
            this._tooltip = tooltip;
        });

        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                delete this._tooltip;
            }
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + P to print
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printReport();
    }

    // Ctrl/Cmd + E to export
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportReport();
    }
});
</script>
@endpush
