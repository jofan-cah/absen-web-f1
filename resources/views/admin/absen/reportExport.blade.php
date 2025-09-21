@extends('admin.layouts.app')

@section('title', 'Laporan Bulanan Absensi')
@section('breadcrumb', 'Laporan Bulanan Absensi')
@section('page_title', 'Laporan Absensi Bulanan - ' . DateTime::createFromFormat('!m', $month)->format('F') . ' ' . $year)

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <!-- Month/Year Navigation -->
    <div class="flex items-center gap-3">
        <button onclick="navigateMonth(-1)" class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="flex gap-2">
            <select id="month-selector" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $value => $name)
                    <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select id="year-selector" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <button onclick="navigateMonth(1)" class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <button onclick="goToCurrentMonth()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
            Bulan Ini
        </button>
    </div>

    <!-- Department Filter -->
    <div class="flex items-center gap-2">
        <label for="department-filter" class="text-sm font-medium text-gray-700">Department:</label>
        <select id="department-filter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="filterByDepartment()">
            <option value="">Semua Department</option>
            @foreach($departments as $department)
                <option value="{{ $department->department_id }}" {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2">
        <a href="{{ route('admin.absen.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            List Absen
        </a>

        <button onclick="exportMonthlyReport()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
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

<!-- Month Header -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
            </h2>
            <p class="text-gray-600">Laporan Absensi Bulanan{{ request('department_id') ? ' - ' . $departments->firstWhere('department_id', request('department_id'))->name : '' }}</p>
        </div>

        <!-- Month Stats -->
        <div class="mt-4 lg:mt-0 flex flex-wrap gap-4">
            @php
                $totalKaryawan = count($reportData);
                $totalJadwal = array_sum(array_column($reportData, 'total_jadwal'));
                $totalHadir = array_sum(array_column($reportData, 'hadir')) + array_sum(array_column($reportData, 'terlambat'));
                $avgKehadiran = $totalJadwal > 0 ? round(($totalHadir / $totalJadwal) * 100, 1) : 0;
            @endphp

            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $totalKaryawan }}</p>
                <p class="text-xs text-gray-500">Karyawan</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $totalJadwal }}</p>
                <p class="text-xs text-gray-500">Total Jadwal</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $avgKehadiran }}%</p>
                <p class="text-xs text-gray-500">Avg Kehadiran</p>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Kehadiran</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalHadir }}</p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-green-600 font-medium">{{ $avgKehadiran }}%</span>
                <span class="text-gray-500 ml-1">dari total jadwal</span>
            </div>
            <div class="progress-bar mt-2">
                <div class="progress-fill green" style="width: {{ $avgKehadiran }}%"></div>
            </div>
        </div>
    </div>

    <!-- Total Terlambat -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Terlambat</p>
                <p class="text-2xl font-semibold text-gray-900">{{ array_sum(array_column($reportData, 'terlambat')) }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $totalTerlambatMenit = array_sum(array_column($reportData, 'total_terlambat_menit'));
                $avgTerlambat = array_sum(array_column($reportData, 'terlambat')) > 0 ? round($totalTerlambatMenit / array_sum(array_column($reportData, 'terlambat')), 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-yellow-600 font-medium">{{ $avgTerlambat }} menit</span>
                <span class="text-gray-500 ml-1">rata-rata</span>
            </div>
        </div>
    </div>

    <!-- Total Tidak Hadir -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tidak Hadir</p>
                <p class="text-2xl font-semibold text-gray-900">{{ array_sum(array_column($reportData, 'tidak_hadir')) }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $absentPercentage = $totalJadwal > 0 ? round((array_sum(array_column($reportData, 'tidak_hadir')) / $totalJadwal) * 100, 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-red-600 font-medium">{{ $absentPercentage }}%</span>
                <span class="text-gray-500 ml-1">tingkat absen</span>
            </div>
        </div>
    </div>

    <!-- Total Jam Kerja -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Jam Kerja</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format(array_sum(array_column($reportData, 'total_jam_kerja')), 0) }}</p>
            </div>
        </div>
        <div class="mt-4">
            @php
                $avgJamKerja = $totalKaryawan > 0 ? round(array_sum(array_column($reportData, 'total_jam_kerja')) / $totalKaryawan, 1) : 0;
            @endphp
            <div class="flex items-center text-sm">
                <span class="text-blue-600 font-medium">{{ $avgJamKerja }} jam</span>
                <span class="text-gray-500 ml-1">per karyawan</span>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Report Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Laporan Absensi Per Karyawan</h3>
                <p class="text-sm text-gray-600 mt-1">Ringkasan kehadiran karyawan bulan {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</p>
            </div>

            <!-- Sort Options -->
            <div class="mt-4 sm:mt-0 flex items-center gap-2">
                <label for="sort-by" class="text-sm font-medium text-gray-700">Urutkan:</label>
                <select id="sort-by" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="sortTable()">
                    <option value="name">Nama</option>
                    <option value="attendance">Kehadiran</option>
                    <option value="late">Terlambat</option>
                    <option value="absent">Tidak Hadir</option>
                    <option value="work_hours">Jam Kerja</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="report-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jadwal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pulang Cepat</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tidak Hadir</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">% Kehadiran</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider no-print">Performance</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reportData as $index => $data)
                    <tr class="hover:bg-gray-50 transition-colors report-row"
                        data-name="{{ strtolower($data['karyawan']->full_name) }}"
                        data-attendance="{{ $data['hadir'] + $data['terlambat'] }}"
                        data-late="{{ $data['terlambat'] }}"
                        data-absent="{{ $data['tidak_hadir'] }}"
                        data-work-hours="{{ $data['total_jam_kerja'] }}"
                        data-department="{{ $data['karyawan']->department->department_id ?? '' }}">

                        <!-- No -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
A
                        </td>

                        <!-- Karyawan -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700">
                                            {{ substr($data['karyawan']->full_name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $data['karyawan']->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $data['karyawan']->nip }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Department -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $data['karyawan']->department->name ?? 'No Department' }}</div>
                        </td>

                        <!-- Total Jam Kerja -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-medium text-gray-900">
                                {{ number_format($data['total_jam_kerja'], 1) }}
                            </span>
                            <div class="text-xs text-gray-500">jam</div>
                        </td>

                        <!-- Persentase Kehadiran -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $attendanceRate = $data['total_jadwal'] > 0 ? round((($data['hadir'] + $data['terlambat']) / $data['total_jadwal']) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center justify-center">
                                <span class="text-sm font-medium {{ $attendanceRate >= 90 ? 'text-green-600' : ($attendanceRate >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $attendanceRate }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="h-2 rounded-full {{ $attendanceRate >= 90 ? 'bg-green-500' : ($attendanceRate >= 75 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                     style="width: {{ $attendanceRate }}%"></div>
                            </div>
                        </td>

                        <!-- Performance Rating -->
                        <td class="px-6 py-4 whitespace-nowrap text-center no-print">
                            @php
                                $performance = 'Poor';
                                $performanceColor = 'bg-red-100 text-red-800';

                                if ($attendanceRate >= 95) {
                                    $performance = 'Excellent';
                                    $performanceColor = 'bg-green-100 text-green-800';
                                } elseif ($attendanceRate >= 85) {
                                    $performance = 'Good';
                                    $performanceColor = 'bg-blue-100 text-blue-800';
                                } elseif ($attendanceRate >= 75) {
                                    $performance = 'Fair';
                                    $performanceColor = 'bg-yellow-100 text-yellow-800';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $performanceColor }}">
                                {{ $performance }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada data</h3>
                                <p class="text-gray-500">Belum ada data absensi untuk periode yang dipilih.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer Summary -->
    @if(count($reportData) > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="text-center">
                    <span class="font-medium text-gray-600">Total Karyawan:</span>
                    <span class="font-semibold text-gray-900">{{ count($reportData) }}</span>
                </div>
                <div class="text-center">
                    <span class="font-medium text-gray-600">Total Jadwal:</span>
                    <span class="font-semibold text-gray-900">{{ array_sum(array_column($reportData, 'total_jadwal')) }}</span>
                </div>
                <div class="text-center">
                    <span class="font-medium text-gray-600">Total Kehadiran:</span>
                    <span class="font-semibold text-gray-900">{{ array_sum(array_column($reportData, 'hadir')) + array_sum(array_column($reportData, 'terlambat')) }}</span>
                </div>
                <div class="text-center">
                    <span class="font-medium text-gray-600">Rata-rata Kehadiran:</span>
                    <span class="font-semibold text-gray-900">{{ $avgKehadiran }}%</span>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
let currentMonth = {{ $month }};
let currentYear = {{ $year }};

function navigateMonth(direction) {
    let newMonth = currentMonth + direction;
    let newYear = currentYear;

    if (newMonth > 12) {
        newMonth = 1;
        newYear++;
    } else if (newMonth < 1) {
        newMonth = 12;
        newYear--;
    }

    const departmentId = document.getElementById('department-filter').value;
    const url = new URL(window.location.href);
    url.searchParams.set('month', newMonth.toString().padStart(2, '0'));
    url.searchParams.set('year', newYear);
    if (departmentId) {
        url.searchParams.set('department_id', departmentId);
    } else {
        url.searchParams.delete('department_id');
    }

    window.location.href = url.toString();
}

function goToCurrentMonth() {
    const now = new Date();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const year = now.getFullYear();

    const departmentId = document.getElementById('department-filter').value;
    const url = new URL(window.location.href);
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    if (departmentId) {
        url.searchParams.set('department_id', departmentId);
    } else {
        url.searchParams.delete('department_id');
    }

    window.location.href = url.toString();
}

function filterByDepartment() {
    const month = document.getElementById('month-selector').value;
    const year = document.getElementById('year-selector').value;
    const departmentId = document.getElementById('department-filter').value;

    const url = new URL(window.location.href);
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    if (departmentId) {
        url.searchParams.set('department_id', departmentId);
    } else {
        url.searchParams.delete('department_id');
    }

    window.location.href = url.toString();
}

function sortTable() {
    const sortBy = document.getElementById('sort-by').value;
    const tbody = document.querySelector('#report-table tbody');
    const rows = Array.from(tbody.querySelectorAll('.report-row'));

    rows.sort((a, b) => {
        let aValue, bValue;

        switch(sortBy) {
            case 'name':
                aValue = a.dataset.name;
                bValue = b.dataset.name;
                return aValue.localeCompare(bValue);
            case 'attendance':
                aValue = parseInt(a.dataset.attendance);
                bValue = parseInt(b.dataset.attendance);
                return bValue - aValue; // Descending
            case 'late':
                aValue = parseInt(a.dataset.late);
                bValue = parseInt(b.dataset.late);
                return bValue - aValue; // Descending
            case 'absent':
                aValue = parseInt(a.dataset.absent);
                bValue = parseInt(b.dataset.absent);
                return bValue - aValue; // Descending
            case 'work_hours':
                aValue = parseFloat(a.dataset.workHours);
                bValue = parseFloat(b.dataset.workHours);
                return bValue - aValue; // Descending
            default:
                return 0;
        }
    });

    // Re-append sorted rows
    rows.forEach((row, index) => {
        // Update row number
        row.querySelector('td:first-child').textContent = index + 1;
        tbody.appendChild(row);
    });
}

function exportMonthlyReport() {
    const month = document.getElementById('month-selector').value;
    const year = document.getElementById('year-selector').value;
    const departmentId = document.getElementById('department-filter').value;

    const url = new URL('{{ route("admin.absen.export-report") }}', window.location.origin);
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    if (departmentId) {
        url.searchParams.set('department_id', departmentId);
    }

    window.open(url.toString(), '_blank');
}

// Month/Year selector change handlers
document.getElementById('month-selector').addEventListener('change', function() {
    const month = this.value;
    const year = document.getElementById('year-selector').value;
    const departmentId = document.getElementById('department-filter').value;

    const url = new URL(window.location.href);
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    if (departmentId) {
        url.searchParams.set('department_id', departmentId);
    } else {
        url.searchParams.delete('department_id');
    }

    window.location.href = url.toString();
});

document.getElementById('year-selector').addEventListener('change', function() {
    const month = document.getElementById('month-selector').value;
    const year = this.value;
    const departmentId = document.getElementById('department-filter').value;

    const url = new URL(window.location.href);
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    if (departmentId) {
        url.searchParams.set('department_id', departmentId);
    } else {
        url.searchParams.delete('department_id');
    }

    window.location.href = url.toString();
});

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
                navigateMonth(-1);
                break;
            case 'ArrowRight':
                e.preventDefault();
                navigateMonth(1);
                break;
        }
    }
});

// Performance indicators animation
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });

    // Animate attendance rate bars
    const attendanceBars = document.querySelectorAll('[style*="width:"]');
    attendanceBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 200);
    });
});

// Search functionality
function searchEmployee() {
    const searchTerm = document.getElementById('employee-search').value.toLowerCase();
    const rows = document.querySelectorAll('.report-row');

    rows.forEach(row => {
        const employeeName = row.dataset.name;
        if (employeeName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Department filter for table
function filterTableByDepartment() {
    const selectedDepartment = document.getElementById('department-filter').value;
    const rows = document.querySelectorAll('.report-row');

    rows.forEach(row => {
        const departmentId = row.dataset.department;
        if (!selectedDepartment || departmentId === selectedDepartment) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
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

    .bg-gray-50,
    .bg-green-100,
    .bg-yellow-100,
    .bg-red-100,
    .bg-blue-100,
    .bg-orange-100 {
        background-color: #f9fafb !important;
        border: 1px solid #dee2e6 !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }

    .page-break {
        page-break-before: always;
    }
}

/* Progress bar animations */
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
    border-radius: 2px;
}

.progress-fill.green {
    background: linear-gradient(90deg, #10b981, #059669);
}

.progress-fill.yellow {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.progress-fill.red {
    background: linear-gradient(90deg, #ef4444, #dc2626);
}

.progress-fill.blue {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
}

/* Performance badge animations */
.performance-badge {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Table row hover effects */
.report-row {
    transition: all 0.2s ease;
}

.report-row:hover {
    background-color: #f8fafc;
    transform: translateX(2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Attendance rate bar */
.attendance-bar {
    transition: width 0.8s ease;
}

/* Sort indicator */
.sort-indicator {
    transition: transform 0.2s ease;
}

.sort-indicator.active {
    transform: rotate(180deg);
}

/* Statistics cards animation */
.stats-card {
    animation: slideInUp 0.6s ease;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Navigation buttons */
.nav-btn {
    transition: all 0.2s ease;
}

.nav-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Loading states */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .table-responsive {
        font-size: 12px;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 8px 4px;
    }

    .mobile-hidden {
        display: none;
    }
}

/* Custom scrollbar */
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

/* Excellence indicators */
.excellent {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.good {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.fair {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.poor {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

/* Tooltip for performance indicators */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 160px;
    background-color: #1f2937;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -80px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
</style>
@endpush
