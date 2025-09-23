@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .gradient-border {
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899) border-box;
        border: 2px solid transparent;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .stat-card:hover::before {
        left: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.33); opacity: 1; }
        80%, 100% { transform: scale(2.33); opacity: 0; }
    }

    .floating-animation {
        animation: floating 3s ease-in-out infinite;
    }

    @keyframes floating {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .activity-item {
        transition: all 0.2s ease;
    }

    .activity-item:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 197, 253, 0.05));
        transform: translateX(4px);
        border-left: 4px solid #3b82f6;
    }

    .quick-action {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .quick-action::after {
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

    .quick-action:hover::after {
        width: 300px;
        height: 300px;
    }

    .quick-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .department-card {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }

    .department-card:hover {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(168, 85, 247, 0.05));
        border-left-color: #6366f1;
        transform: translateX(2px);
    }

    .progress-bar {
        background: linear-gradient(90deg, #10b981, #34d399);
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
    }

    .number-counter {
        font-variant-numeric: tabular-nums;
    }

    .chart-progress {
        background: linear-gradient(90deg, #f3f4f6 0%, #f3f4f6 var(--progress, 0%), transparent var(--progress, 0%));
    }
</style>
@endpush

@section('content')
<div class="space-y-8">

    <!-- Enhanced Welcome Message -->
    <div class="relative overflow-hidden rounded-3xl">
        <!-- Background with animated gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-black/20"></div>

        <!-- Floating geometric shapes -->
        <div class="absolute top-4 right-4 w-16 h-16 bg-white/10 rounded-full floating-animation"></div>
        <div class="absolute bottom-6 right-16 w-8 h-8 bg-white/20 rounded-lg transform rotate-45"></div>
        <div class="absolute top-1/2 right-32 w-4 h-4 bg-white/30 rounded-full"></div>

        <div class="relative p-8 text-white">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold tracking-tight">Welcome back, {{ auth()->user()->name }}!</h2>
                    <p class="text-blue-100 text-lg">Here's what's happening with your team today.</p>
                    <div class="flex items-center space-x-4 mt-4">
                        <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full pulse-ring"></div>
                            <span class="text-sm font-medium">System Online</span>
                        </div>
                        <div class="text-sm text-blue-100">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ now()->format('l, F j, Y') }}
                        </div>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="relative">
                        <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-4xl text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Total Karyawan -->
        <div class="stat-card glass-card rounded-2xl p-6 shadow-lg border border-white/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Karyawan</p>
                        <p class="text-3xl font-bold text-gray-900 number-counter" data-target="{{ $stats['total_karyawan'] }}">0</p>
                        <p class="text-xs text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>Active employees
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Department -->
        <div class="stat-card glass-card rounded-2xl p-6 shadow-lg border border-white/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg">
                        <i class="fas fa-building text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Department</p>
                        <p class="text-3xl font-bold text-gray-900 number-counter" data-target="{{ $stats['total_department'] }}">0</p>
                        <p class="text-xs text-blue-600 font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Active departments
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="stat-card glass-card rounded-2xl p-6 shadow-lg border border-white/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg">
                        <i class="fas fa-check-circle text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Hadir Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900 number-counter" data-target="{{ $stats['today_present'] }}">0</p>
                        <p class="text-xs text-emerald-600 font-medium">
                            <i class="fas fa-clock mr-1"></i>Present today
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tidak Hadir -->
        <div class="stat-card glass-card rounded-2xl p-6 shadow-lg border border-white/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-4 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl shadow-lg">
                        <i class="fas fa-times-circle text-2xl text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Tidak Hadir</p>
                        <p class="text-3xl font-bold text-gray-900 number-counter" data-target="{{ $stats['today_absent'] }}">0</p>
                        <p class="text-xs text-red-600 font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Absent today
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Department Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Enhanced Attendance Chart -->
        <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/20">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Kehadiran 7 Hari Terakhir</h3>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full shadow-sm"></div>
                        <span class="font-medium">Hadir</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gradient-to-r from-red-500 to-pink-500 rounded-full shadow-sm"></div>
                        <span class="font-medium">Tidak Hadir</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($chartData as $data)
                <div class="flex items-center justify-between p-3 bg-gray-50/80 rounded-xl hover:bg-gray-100/80 transition-all duration-200">
                    <span class="text-sm font-semibold text-gray-700 w-16">{{ $data['date'] }}</span>
                    <div class="flex-1 mx-4">
                        <div class="flex items-center space-x-2">
                            @php
                                $total = $data['present'] + $data['absent'];
                                $presentPercent = $total > 0 ? ($data['present'] / $total) * 100 : 0;
                                $absentPercent = $total > 0 ? ($data['absent'] / $total) * 100 : 0;
                            @endphp

                            <div class="flex-1 bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="flex h-full rounded-full">
                                    @if($presentPercent > 0)
                                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 transition-all duration-1000 ease-out"
                                             style="width: {{ $presentPercent }}%"></div>
                                    @endif
                                    @if($absentPercent > 0)
                                        <div class="bg-gradient-to-r from-red-500 to-pink-500 transition-all duration-1000 ease-out"
                                             style="width: {{ $absentPercent }}%"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-sm font-semibold">
                        <span class="text-green-600 bg-green-50 px-2 py-1 rounded-lg">{{ $data['present'] }}</span>
                        <span class="text-red-600 bg-red-50 px-2 py-1 rounded-lg">{{ $data['absent'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Enhanced Department Stats -->
        <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/20">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Department Overview</h3>
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    Live Data
                </div>
            </div>

            <div class="space-y-4">
                @foreach($departmentStats as $dept)
                <div class="department-card flex items-center justify-between p-4 bg-gray-50/50 rounded-xl transition-all duration-200">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                            <i class="fas fa-building text-white text-lg"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $dept->name }}</p>
                            <p class="text-xs text-gray-500 font-medium">{{ $dept->code }}</p>
                            @php
                                $attendanceRate = $dept->total_karyawan > 0 ? ($dept->present_today / $dept->total_karyawan) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-2 mt-1">
                                <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-1000 ease-out"
                                         style="width: {{ $attendanceRate }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 font-medium">{{ number_format($attendanceRate, 0) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900 text-lg">
                            <span class="text-green-600">{{ $dept->present_today }}</span>/<span class="text-gray-600">{{ $dept->total_karyawan }}</span>
                        </p>
                        <p class="text-xs text-gray-500 font-medium">Hadir/Total</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Enhanced Recent Activity -->
    <div class="glass-card rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-blue-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Aktivitas Absensi Terbaru</h3>
                <a href="{{ route('admin.absen.index') }}"
                   class="text-sm text-blue-600 hover:text-blue-700 font-semibold bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-all duration-200">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($recentAbsens->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAbsens as $absen)
                    <div class="activity-item flex items-center space-x-4 p-4 bg-gray-50/50 rounded-xl transition-all duration-200">
                        <div class="flex-shrink-0">
                            @if($absen->status === 'present')
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-check text-white text-lg"></i>
                                </div>
                            @elseif($absen->status === 'late')
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-clock text-white text-lg"></i>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-user text-white text-lg"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate text-lg">
                                {{ $absen->karyawan->full_name }}
                            </p>
                            <p class="text-sm text-gray-500 font-medium">
                                {{ $absen->karyawan->position }} • {{ $absen->jadwal->shift->name }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="font-bold text-gray-900 text-lg">
                                {{ $absen->clock_in ? \Carbon\Carbon::parse($absen->clock_in)->format('H:i') : '--:--' }}
                            </p>
                            <p class="text-xs font-medium">
                                @if($absen->status === 'late')
                                    <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded-lg">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Terlambat {{ $absen->late_minutes }}m
                                    </span>
                                @elseif($absen->status === 'present')
                                    <span class="text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                                        <i class="fas fa-check-circle mr-1"></i>Tepat Waktu
                                    </span>
                                @else
                                    <span class="text-gray-500 bg-gray-50 px-2 py-1 rounded-lg">{{ ucfirst($absen->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada aktivitas absensi hari ini</p>
                    <p class="text-gray-400 text-sm mt-1">Data akan muncul ketika karyawan melakukan absensi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/20">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.karyawan.create') }}" class="quick-action flex items-center p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl hover:from-blue-100 hover:to-indigo-200 transition-all duration-300 group">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-plus text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-blue-900">Tambah Karyawan</p>
                    <p class="text-xs text-blue-700 mt-1">Daftarkan karyawan baru</p>
                </div>
            </a>

            <a href="{{ route('admin.jadwal.calendar') }}" class="quick-action flex items-center p-6 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl hover:from-green-100 hover:to-emerald-200 transition-all duration-300 group">
                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-plus text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-green-900">Buat Jadwal</p>
                    <p class="text-xs text-green-700 mt-1">Atur jadwal kerja</p>
                </div>
            </a>

            <a href="{{ route('admin.absen.report') }}" class="quick-action flex items-center p-6 bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl hover:from-purple-100 hover:to-pink-200 transition-all duration-300 group">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-purple-900">Lihat Laporan</p>
                    <p class="text-xs text-purple-700 mt-1">Analisis kehadiran</p>
                </div>
            </a>

            <a href="{{ route('admin.shift.create') }}" class="quick-action flex items-center p-6 bg-gradient-to-br from-orange-50 to-red-100 rounded-xl hover:from-orange-100 hover:to-red-200 transition-all duration-300 group">
                <div class="p-3 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-orange-900">Tambah Shift</p>
                    <p class="text-xs text-orange-700 mt-1">Buat shift baru</p>
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

    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats counter on load
        const counters = document.querySelectorAll('.number-counter');

        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000; // 2 seconds
            const step = target / (duration / 16); // 60fps
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current);
            }, 16);
        };

        // Use Intersection Observer to trigger animation when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });

        counters.forEach(counter => {
            observer.observe(counter);
        });

        // Animate progress bars
        const progressBars = document.querySelectorAll('[style*="width:"]');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });

        // Add loading states for quick actions
        const quickActions = document.querySelectorAll('.quick-action');
        quickActions.forEach(action => {
            action.addEventListener('click', function(e) {
                const icon = this.querySelector('i');
                const originalClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin text-white text-xl';

                setTimeout(() => {
                    icon.className = originalClass;
                }, 1000);
            });
        });
    });

    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
</script>
@endpush
