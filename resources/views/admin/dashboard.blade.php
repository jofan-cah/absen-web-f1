@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome Message -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-blue-100 mt-1">Here's what's happening with your team today.</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-4xl text-blue-200"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Karyawan -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_karyawan'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Department -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-building text-xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Department</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_department'] }}</p>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i class="fas fa-check-circle text-xl text-emerald-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['today_present'] }}</p>
                </div>
            </div>
        </div>

        <!-- Tidak Hadir -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tidak Hadir</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['today_absent'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Attendance Chart -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Kehadiran 7 Hari Terakhir</h3>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
                        <span>Hadir</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                        <span>Tidak Hadir</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($chartData as $data)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 w-16">{{ $data['date'] }}</span>
                    <div class="flex-1 mx-4">
                        <div class="flex items-center space-x-1">
                            @php
                                $total = $data['present'] + $data['absent'];
                                $presentPercent = $total > 0 ? ($data['present'] / $total) * 100 : 0;
                                $absentPercent = $total > 0 ? ($data['absent'] / $total) * 100 : 0;
                            @endphp

                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div class="flex h-full rounded-full overflow-hidden">
                                    @if($presentPercent > 0)
                                        <div class="bg-green-500" style="width: {{ $presentPercent }}%"></div>
                                    @endif
                                    @if($absentPercent > 0)
                                        <div class="bg-red-500" style="width: {{ $absentPercent }}%"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm">
                        <span class="text-green-600 font-medium">{{ $data['present'] }}</span>
                        <span class="text-red-600 font-medium">{{ $data['absent'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Department Stats -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Overview</h3>

            <div class="space-y-4">
                @foreach($departmentStats as $dept)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-building text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $dept->name }}</p>
                            <p class="text-xs text-gray-500">{{ $dept->code }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">
                            {{ $dept->present_today }}/{{ $dept->total_karyawan }}
                        </p>
                        <p class="text-xs text-gray-500">Hadir/Total</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Absensi Terbaru</h3>
                <a href="{{ route('admin.absen.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($recentAbsens->count() > 0)
                <div class="space-y-4">
                    @foreach($recentAbsens as $absen)
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            @if($absen->status === 'present')
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                            @elseif($absen->status === 'late')
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $absen->karyawan->full_name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $absen->karyawan->position }} • {{ $absen->jadwal->shift->name }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $absen->clock_in ? \Carbon\Carbon::parse($absen->clock_in)->format('H:i') : '--:--' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                @if($absen->status === 'late')
                                    <span class="text-yellow-600">Terlambat {{ $absen->late_minutes }}m</span>
                                @elseif($absen->status === 'present')
                                    <span class="text-green-600">Tepat Waktu</span>
                                @else
                                    <span class="text-gray-500">{{ ucfirst($absen->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Belum ada aktivitas absensi hari ini</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.karyawan.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="p-2 bg-blue-500 rounded-lg">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-900">Tambah Karyawan</p>
                    <p class="text-xs text-blue-600">Daftarkan karyawan baru</p>
                </div>
            </a>

            <a href="{{ route('admin.jadwal.calendar') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="p-2 bg-green-500 rounded-lg">
                    <i class="fas fa-calendar-plus text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-900">Buat Jadwal</p>
                    <p class="text-xs text-green-600">Atur jadwal kerja</p>
                </div>
            </a>

            <a href="{{ route('admin.absen.report') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="p-2 bg-purple-500 rounded-lg">
                    <i class="fas fa-chart-bar text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-900">Lihat Laporan</p>
                    <p class="text-xs text-purple-600">Analisis kehadiran</p>
                </div>
            </a>

            <a href="{{ route('admin.shift.create') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                <div class="p-2 bg-orange-500 rounded-lg">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-orange-900">Tambah Shift</p>
                    <p class="text-xs text-orange-600">Buat shift baru</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Auto refresh page every 5 minutes for real-time data
    setTimeout(function(){
        location.reload();
    }, 300000); // 5 minutes

    // Add some interactivity
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats on load
        const statCards = document.querySelectorAll('[class*="text-2xl font-bold"]');
        statCards.forEach(card => {
            const finalValue = parseInt(card.textContent);
            let currentValue = 0;
            const increment = Math.ceil(finalValue / 20);

            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                card.textContent = currentValue;
            }, 50);
        });
    });
</script>
@endpush
