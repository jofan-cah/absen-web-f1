@extends('admin.layouts.app')

@section('title', 'Laporan Harian Absensi')
@section('breadcrumb', 'Laporan Harian Absensi')
@section('page_title', 'Laporan Absensi Harian - ' . \Carbon\Carbon::parse($date)->format('d F Y'))

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <!-- Date Navigation -->
    <div class="flex items-center gap-3">
        <button onclick="navigateDate(-1)" class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="flex items-center gap-2">
            <label for="date-picker" class="text-sm font-medium text-gray-700">Tanggal:</label>
            <input type="date" id="date-picker" value="{{ $date }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   onchange="changeDate()">
        </div>

        <button onclick="navigateDate(1)" class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <button onclick="goToToday()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
            Hari Ini
        </button>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2">
        <a href="{{ route('admin.absen.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            List Absen
        </a>

        <button onclick="exportDailyReport()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </button>

        <button onclick="window.print()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
    </div>
</div>
@endsection

@section('content')

<!-- Date Header -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
            </h2>
            <p class="text-gray-600">{{ \Carbon\Carbon::parse($date)->format('l') }} • Laporan Absensi Harian</p>
        </div>

        <!-- Quick Stats Summary -->
        <div class="mt-4 lg:mt-0 flex flex-wrap gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_jadwal'] }}</p>
                <p class="text-xs text-gray-500">Total Jadwal</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $stats['hadir'] }}</p>
                <p class="text-xs text-gray-500">Hadir</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">{{ $stats['tidak_hadir'] }}</p>
                <p class="text-xs text-gray-500">Tidak Hadir</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <!-- Total Jadwal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Jadwal</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_jadwal'] }}</p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm text-gray-500">
                <span>100% dari total karyawan</span>
            </div>
        </div>
    </div>

    <!-- Hadir -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Hadir</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['hadir'] }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $hadirPercentage = $stats['total_jadwal'] > 0 ? round(($stats['hadir'] / $stats['total_jadwal']) * 100, 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $hadirPercentage }}%</span>
                <span class="text-gray-500 ml-1">tingkat kehadiran</span>
            </div>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Terlambat</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['terlambat'] }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $latePercentage = $stats['total_jadwal'] > 0 ? round(($stats['terlambat'] / $stats['total_jadwal']) * 100, 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-yellow-600 font-medium">{{ $latePercentage }}%</span>
                <span class="text-gray-500 ml-1">dari total jadwal</span>
            </div>
        </div>
    </div>

    <!-- Tidak Hadir -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tidak Hadir</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['tidak_hadir'] }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $absentPercentage = $stats['total_jadwal'] > 0 ? round(($stats['tidak_hadir'] / $stats['total_jadwal']) * 100, 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-red-600 font-medium">{{ $absentPercentage }}%</span>
                <span class="text-gray-500 ml-1">tingkat absen</span>
            </div>
        </div>
    </div>

    <!-- Belum Absen -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-gray-100 rounded-lg">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Belum Absen</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['belum_absen'] }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $pendingPercentage = $stats['total_jadwal'] > 0 ? round(($stats['belum_absen'] / $stats['total_jadwal']) * 100, 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-gray-600 font-medium">{{ $pendingPercentage }}%</span>
                <span class="text-gray-500 ml-1">masih scheduled</span>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Detail Absensi Harian</h3>
                <p class="text-sm text-gray-600 mt-1">Daftar lengkap absensi karyawan untuk tanggal {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
            </div>

            <!-- Filter Status -->
            <div class="mt-4 sm:mt-0">
                <select id="status-filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="filterByStatus()">
                    <option value="">Semua Status</option>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="early_checkout">Early Checkout</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Kerja</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider no-print">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($absens as $index => $absen)
                    <tr class="hover:bg-gray-50 transition-colors absen-row" data-status="{{ $absen->status }}">
                        <!-- No -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>

                        <!-- Karyawan -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700">
                                            {{ substr($absen->karyawan->full_name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $absen->karyawan->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $absen->karyawan->nip }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Department -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $absen->karyawan->department->name ?? '-' }}</div>
                        </td>

                        <!-- Shift -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $absen->jadwal->shift->name }}</div>
                            <div class="text-xs text-gray-500 font-mono">
                                {{ \Carbon\Carbon::parse($absen->jadwal->shift->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($absen->jadwal->shift->end_time)->format('H:i') }}
                            </div>
                        </td>

                        <!-- Clock In -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($absen->clock_in)
                                <div class="text-sm text-gray-900 font-mono">
                                    {{ \Carbon\Carbon::parse($absen->clock_in)->format('H:i:s') }}
                                </div>
                                @if($absen->late_minutes > 0)
                                    <div class="text-xs text-red-500">
                                        +{{ $absen->late_minutes }}m terlambat
                                    </div>
                                @endif
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Clock Out -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($absen->clock_out)
                                <div class="text-sm text-gray-900 font-mono">
                                    {{ \Carbon\Carbon::parse($absen->clock_out)->format('H:i:s') }}
                                </div>
                                @if($absen->early_checkout_minutes > 0)
                                    <div class="text-xs text-orange-500">
                                        -{{ $absen->early_checkout_minutes }}m cepat
                                    </div>
                                @endif
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'scheduled' => 'bg-gray-100 text-gray-800',
                                    'present' => 'bg-green-100 text-green-800',
                                    'late' => 'bg-yellow-100 text-yellow-800',
                                    'absent' => 'bg-red-100 text-red-800',
                                    'early_checkout' => 'bg-orange-100 text-orange-800'
                                ];

                                $statusNames = [
                                    'scheduled' => 'Scheduled',
                                    'present' => 'Present',
                                    'late' => 'Late',
                                    'absent' => 'Absent',
                                    'early_checkout' => 'Early Out'
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$absen->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusNames[$absen->status] ?? ucfirst($absen->status) }}
                            </span>
                        </td>

                        <!-- Terlambat -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($absen->late_minutes > 0)
                                <span class="text-red-600 font-medium">{{ $absen->late_minutes }} menit</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Jam Kerja -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($absen->work_hours)
                                <span class="text-gray-900 font-medium">{{ number_format($absen->work_hours, 1) }} jam</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 whitespace-nowrap text-center no-print">
                            <a href="{{ route('admin.absen.show', $absen->absen_id) }}"
                               class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data absensi</h3>
                                <p class="text-gray-500">Belum ada jadwal atau absensi untuk tanggal {{ \Carbon\Carbon::parse($date)->format('d F Y') }}.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary Footer -->
    @if($absens->count() > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-600">
                <div>
                    Total: {{ $absens->count() }} karyawan terjadwal
                </div>
                <div class="mt-2 sm:mt-0">
                    Hadir: {{ $stats['hadir'] }} • Terlambat: {{ $stats['terlambat'] }} • Tidak Hadir: {{ $stats['tidak_hadir'] }} • Belum Absen: {{ $stats['belum_absen'] }}
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
let currentDate = '{{ $date }}';

function navigateDate(direction) {
    const date = new Date(currentDate);
    date.setDate(date.getDate() + direction);
    const newDate = date.toISOString().split('T')[0];

    window.location.href = `{{ route('admin.absen.daily-report') }}?date=${newDate}`;
}

function changeDate() {
    const selectedDate = document.getElementById('date-picker').value;
    if (selectedDate) {
        window.location.href = `{{ route('admin.absen.daily-report') }}?date=${selectedDate}`;
    }
}

function goToToday() {
    const today = new Date().toISOString().split('T')[0];
    window.location.href = `{{ route('admin.absen.daily-report') }}?date=${today}`;
}

function filterByStatus() {
    const selectedStatus = document.getElementById('status-filter').value;
    const rows = document.querySelectorAll('.absen-row');

    rows.forEach(row => {
        const rowStatus = row.dataset.status;
        if (!selectedStatus || rowStatus === selectedStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update visible count
    updateVisibleCount();
}

function updateVisibleCount() {
    const visibleRows = document.querySelectorAll('.absen-row:not([style*="display: none"])');
    const totalRows = document.querySelectorAll('.absen-row');

    // You can add a counter display here if needed
    console.log(`Showing ${visibleRows.length} of ${totalRows.length} records`);
}

function exportDailyReport() {
    const currentUrl = new URL(window.location.href);
    currentUrl.pathname = '{{ route("admin.absen.export-report") }}';
    currentUrl.searchParams.set('date', currentDate);
    currentUrl.searchParams.set('type', 'daily');

    window.open(currentUrl.toString(), '_blank');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'p':
                e.preventDefault();
                window.print();
                break;
            case 'ArrowLeft':
                e.preventDefault();
                navigateDate(-1);
                break;
            case 'ArrowRight':
                e.preventDefault();
                navigateDate(1);
                break;
        }
    }
});

// Auto-refresh every 5 minutes for real-time updates
setInterval(function() {
    if (currentDate === new Date().toISOString().split('T')[0]) {
        location.reload();
    }
}, 300000); // 5 minutes
</script>
@endpush

@push('styles')
<style>
/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .bg-gray-50 {
        background-color: #f9fafb !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }

    .border {
        border: 1px solid #e5e7eb !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    .page-break {
        page-break-before: always;
    }
}

/* Status filter animation */
.fade-out {
    opacity: 0.3;
    transition: opacity 0.3s ease;
}

.fade-in {
    opacity: 1;
    transition: opacity 0.3s ease;
}

/* Loading animation */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Real-time update indicator */
.update-indicator {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #10b981;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    z-index: 1000;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 8px 4px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .stats-card {
        padding: 16px;
    }
}

/* Status badges hover effects */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Date navigation buttons */
.date-nav-btn {
    transition: all 0.2s ease;
}

.date-nav-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Table row hover with scale effect */
.table-row-hover {
    transition: all 0.2s ease;
}

.table-row-hover:hover {
    transform: scale(1.01);
    z-index: 10;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Progress bars for statistics */
.progress-bar {
    height: 4px;
    background-color: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 8px;
}

.progress-fill {
    height: 100%;
    transition: width 0.8s ease;
}

.progress-fill.green {
    background-color: #10b981;
}

.progress-fill.yellow {
    background-color: #f59e0b;
}

.progress-fill.red {
    background-color: #ef4444;
}

.progress-fill.gray {
    background-color: #6b7280;
}

/* Tooltip styles */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: #1f2937;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -60px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* Export button animation */
.export-btn {
    position: relative;
    overflow: hidden;
}

.export-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.export-btn:active::after {
    width: 300px;
    height: 300px;
}

/* Custom scrollbar for table */
.table-container::-webkit-scrollbar {
    height: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Dark mode styles (if needed) */
@media (prefers-color-scheme: dark) {
    .dark-mode {
        background-color: #1f2937;
        color: #f9fafb;
    }

    .dark-mode .bg-white {
        background-color: #374151;
    }

    .dark-mode .text-gray-900 {
        color: #f9fafb;
    }

    .dark-mode .border-gray-200 {
        border-color: #4b5563;
    }
}
</style>
@endpush
