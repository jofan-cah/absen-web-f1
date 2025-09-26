<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Tunjangan {{ ucfirst($report_type ?? 'Universal') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 15px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .company-logo {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }

        .report-info {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: left;
        }

        .info-item {
            display: inline-block;
            margin-right: 25px;
            font-size: 9px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 4px 3px;
            text-align: center;
            vertical-align: middle;
        }

        .main-table th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }

        .main-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .employee-name {
            text-align: left;
            font-weight: bold;
            width: 120px;
        }

        .department {
            text-align: left;
            font-size: 7px;
            color: #666;
            width: 80px;
        }

        .week-cell {
            width: 50px;
            position: relative;
        }

        .week-header {
            background-color: #6b7280 !important;
            color: white;
            font-size: 7px;
            padding: 2px;
        }

        .status-taken {
            color: #22c55e;
            font-weight: bold;
            font-size: 12px;
        }

        .status-not-taken {
            color: #ef4444;
            font-weight: bold;
            font-size: 12px;
        }

        .status-no-data {
            color: #9ca3af;
            font-size: 10px;
        }

        .amount-text {
            font-size: 6px;
            color: #666;
            display: block;
            margin-top: 1px;
        }

        .jam-text {
            font-size: 6px;
            color: #0891b2;
            display: block;
            margin-top: 1px;
        }

        .legend {
            margin: 15px 0;
            text-align: center;
            font-size: 8px;
        }

        .legend-item {
            display: inline-block;
            margin: 0 15px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            right: 15px;
            font-size: 7px;
            color: #666;
        }

        .week-info {
            margin-bottom: 15px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 5px;
            padding: 8px;
            font-size: 8px;
        }

        .notice-box {
            margin-top: 15px;
            background: #fef2f2;
            border: 1px solid #ef4444;
            border-radius: 5px;
            padding: 10px;
        }

        .notice-title {
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 6px;
            font-size: 9px;
        }

        .notice-content {
            font-size: 8px;
            color: #dc2626;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="company-logo">PT. JARINGAN FIBERONE INDONESIA KOK</div>
        <div class="report-title">LAPORAN TUNJANGAN {{ strtoupper($report_type ?? 'UNIVERSAL') }} - SEMUA KARYAWAN</div>
        <div>{{ $month_name ?? 'Periode Tidak Diketahui' }}</div>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <div class="info-item"><strong>Jenis Tunjangan:</strong> {{ $tunjanganType->name ?? 'Tidak Diketahui' }}</div>
        <div class="info-item"><strong>Kategori:</strong> {{ ucfirst($tunjanganType->category ?? 'tidak diketahui') }}
        </div>
        <div class="info-item"><strong>Total Karyawan:</strong> {{ count($employees ?? []) }}</div>
        @if (($report_type ?? '') === 'mingguan' && isset($weeks))
            <div class="info-item"><strong>Total Minggu:</strong> {{ count($weeks) }}</div>
        @endif
    </div>

    <!-- Week Info untuk Mingguan -->
    @if (($report_type ?? '') === 'mingguan' && isset($weeks) && count($weeks) > 0)
        <div class="week-info">
            <strong>Breakdown Minggu:</strong>
            @foreach ($weeks as $week)
                <span style="margin-right: 15px;">
                    <strong>Minggu {{ $week['number'] ?? '' }}:</strong>
                    {{ isset($week['start']) ? $week['start']->format('d/m/Y') : '' }} -
                    {{ isset($week['end']) ? $week['end']->format('d/m/Y') : '' }}
                    @if (isset($week['start']) && isset($week['end']))
                        ({{ $week['start']->format('D') }} - {{ $week['end']->format('D') }})
                    @endif
                    @if (isset($week['is_full_week']) && !$week['is_full_week'])
                        <em style="color: #f59e0b;">*tidak full</em>
                    @endif
                </span>
            @endforeach
        </div>
    @endif

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item"><span class="status-taken">✓</span> Sudah Diambil</div>
        <div class="legend-item"><span class="status-not-taken">✗</span> Belum Diambil</div>
        <div class="legend-item"><span class="status-no-data">-</span> Tidak Ada Data</div>
        @if (isset($is_lembur) && $is_lembur)
            <div class="legend-item" style="color: #0891b2;"><strong>Jam:</strong> Total Jam Lembur</div>
        @endif
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            @if (($report_type ?? '') === 'mingguan' && isset($weeks))
                <!-- Header untuk Mingguan -->
                <tr>
                    <th rowspan="2" class="employee-name">Nama Karyawan</th>
                    <th rowspan="2" class="department">Departemen</th>
                    @foreach ($weeks as $week)
                        <th class="week-cell">
                            {{ isset($week['full_label']) ? $week['full_label'] : $week['label'] ?? 'Minggu ' . ($week['number'] ?? '') }}
                        </th>
                    @endforeach
                    <th rowspan="2" style="width: 50px;">Total<br>Diambil</th>
                    <th rowspan="2" style="width: 70px;">Total<br>Nominal</th>
                </tr>
                <tr>
                    @foreach ($weeks as $week)
                        <th class="week-header">
                            Minggu {{ $week['number'] ?? '' }}
                            @if (isset($week['is_full_week']) && !$week['is_full_week'])
                                <br><small>*parsial</small>
                            @endif
                        </th>
                    @endforeach
                </tr>
            @elseif(($report_type ?? '') === 'bulanan')
                <!-- Header untuk Bulanan -->
                <tr>
                    <th class="employee-name">Nama Karyawan</th>
                    <th class="department">Departemen</th>

                    @if (isset($is_lembur) && $is_lembur)
                        {{-- KHUSUS LEMBUR --}}
                        <th style="width: 70px;">Total<br>Lembur</th>
                        <th style="width: 60px;">Total<br>Jam</th>
                        <th style="width: 90px;">Total<br>Nominal</th>
                        <th style="width: 60px;">Status</th>
                    @else
                        {{-- UANG MAKAN / KUOTA --}}
                        <th style="width: 60px;">Status</th>
                        <th style="width: 80px;">Nominal</th>
                    @endif

                    <th style="width: 80px;">Tgl Disetujui</th>
                    <th style="width: 80px;">Tgl Diambil</th>
                    <th style="width: 100px;">Keterangan</th>
                </tr>
            @elseif(($report_type ?? '') === 'harian')
                <!-- Header untuk Harian -->
                <tr>
                    <th class="employee-name">Nama Karyawan</th>
                    <th class="department">Departemen</th>
                    <th style="width: 50px;">Total<br>Hari</th>
                    <th style="width: 50px;">Sudah<br>Diambil</th>
                    <th style="width: 50px;">Belum<br>Diambil</th>
                    <th style="width: 80px;">Total<br>Nominal</th>
                    <th style="width: 60px;">Status</th>
                    <th>Keterangan</th>
                </tr>
            @endif
        </thead>

        <tbody>
            @forelse(($employees ?? []) as $employee)
                <tr>
                    <!-- Kolom Nama dan Departemen (sama untuk semua) -->
                    <td class="employee-name">
                        {{ $employee['karyawan']->full_name ?? 'Nama Tidak Diketahui' }}
                        <div style="font-size: 7px; color: #666; font-weight: normal;">
                            {{ $employee['karyawan']->nip ?? '' }}
                        </div>
                    </td>
                    <td class="department">
                        {{ $employee['karyawan']->department->name ?? '-' }}
                        <div style="font-size: 6px; color: #999;">
                            {{ isset($employee['karyawan']->staff_status) ? ucfirst(str_replace('_', ' ', $employee['karyawan']->staff_status)) : '' }}
                        </div>
                    </td>

                    @if (($report_type ?? '') === 'mingguan' && isset($employee['weeks']))
                        <!-- Data Mingguan -->
                        @foreach ($employee['weeks'] as $weekData)
                            <td class="week-cell">
                                @if (($weekData['status'] ?? '') == 'no_data')
                                    <span class="status-no-data">-</span>
                                @elseif($weekData['is_taken'] ?? false)
                                    <span class="status-taken">✓</span>
                                    @if (($weekData['amount'] ?? 0) > 0)
                                        <span
                                            class="amount-text">{{ number_format($weekData['amount'] / 1000, 0) }}k</span>
                                    @endif
                                    @if (isset($is_lembur) && $is_lembur && isset($weekData['total_jam']))
                                        <span class="jam-text">{{ number_format($weekData['total_jam'], 1) }}j</span>
                                    @endif
                                @else
                                    <span class="status-not-taken">✗</span>
                                    @if (($weekData['amount'] ?? 0) > 0)
                                        <span
                                            class="amount-text">{{ number_format($weekData['amount'] / 1000, 0) }}k</span>
                                    @endif
                                @endif
                            </td>
                        @endforeach
                        <td style="font-weight: bold;">{{ $employee['total_taken'] ?? 0 }}/{{ count($weeks ?? []) }}
                        </td>
                        <td style="font-weight: bold;">{{ number_format($employee['total_amount'] ?? 0, 0, ',', '.') }}
                        </td>
                    @elseif(($report_type ?? '') === 'bulanan')
                        <!-- Data Bulanan -->
                        @if (isset($is_lembur) && $is_lembur)
                            {{-- KHUSUS LEMBUR --}}
                            <td style="font-weight: bold;">{{ $employee['total_lembur'] ?? 0 }}</td>
                            <td style="font-weight: bold; color: #0891b2;">
                                {{ number_format($employee['total_jam'] ?? 0, 1) }}</td>
                            <td style="font-weight: bold;">{{ number_format($employee['amount'] ?? 0, 0, ',', '.') }}
                            </td>
                            <td>
                                @if ($employee['is_taken'] ?? false)
                                    <span class="status-taken">✓</span>
                                @else
                                    <span class="status-not-taken">✗</span>
                                @endif
                            </td>
                        @else
                            {{-- UANG MAKAN / KUOTA --}}
                            <td>
                                @if ($employee['is_taken'] ?? false)
                                    <span class="status-taken">✓</span>
                                @else
                                    <span class="status-not-taken">✗</span>
                                @endif
                            </td>
                            <td>{{ ($employee['amount'] ?? 0) > 0 ? 'Rp ' . number_format($employee['amount'], 0, ',', '.') : '-' }}
                            </td>
                        @endif

                        <td>{{ $employee['approved_date'] ?? '-' }}</td>
                        <td>{{ $employee['received_date'] ?? '-' }}</td>
                        <td style="font-size: 7px;">
                            @if (isset($is_lembur) && $is_lembur)
                                @if (($employee['total_lembur'] ?? 0) > 0)
                                    {{ $employee['total_lembur'] }} kali lembur<br>
                                    <span style="color: #666;">({{ number_format($employee['total_jam'] ?? 0, 1) }} jam
                                        total)</span>
                                @else
                                    Tidak ada lembur
                                @endif
                            @else
                                @if ($employee['is_taken'] ?? false)
                                    Sudah Diambil
                                @elseif(($employee['amount'] ?? 0) > 0)
                                    Belum Diambil
                                @else
                                    Tidak Ada Data
                                @endif
                            @endif
                        </td>
                    @elseif(($report_type ?? '') === 'harian')
                        <!-- Data Harian -->
                        <td style="font-weight: bold;">{{ $employee['total_days'] ?? 0 }}</td>
                        <td class="status-taken">{{ $employee['taken_days'] ?? 0 }}</td>
                        <td class="status-not-taken">{{ $employee['pending_days'] ?? 0 }}</td>
                        <td style="font-weight: bold;">{{ number_format($employee['total_amount'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            @if (($employee['total_days'] ?? 0) == 0)
                                <span class="status-no-data">No Data</span>
                            @elseif(($employee['pending_days'] ?? 0) == 0)
                                <span class="status-taken">Complete</span>
                            @else
                                <span class="status-not-taken">Partial</span>
                            @endif
                        </td>
                        <td>
                            @if (($employee['total_days'] ?? 0) == 0)
                                Tidak ada data
                            @elseif(($employee['pending_days'] ?? 0) == 0)
                                Semua hari sudah diambil
                            @else
                                {{ $employee['pending_days'] ?? 0 }} hari belum diambil
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="100%" style="text-align: center; padding: 20px; color: #666;">
                        Tidak ada data karyawan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Report Info -->
    <div class="report-info">
        <div class="info-item"><strong>Jenis Tunjangan:</strong> {{ $tunjanganType->name ?? 'Tidak Diketahui' }}</div>
        <div class="info-item"><strong>Kategori:</strong> {{ ucfirst($tunjanganType->category ?? 'tidak diketahui') }}
        </div>
        <div class="info-item"><strong>Total Karyawan:</strong> {{ count($employees ?? []) }}</div>
        @if (($report_type ?? '') === 'mingguan' && isset($weeks))
            <div class="info-item"><strong>Total Minggu:</strong> {{ count($weeks) }}</div>
        @endif

        {{-- TAMBAHAN: Notes Sumber Data --}}
        @if (isset($is_lembur) && $is_lembur)
            <div class="info-item" style="color: #dc2626; font-weight: bold;">
                📌 Data dari: Laporan Lembur (bukan Absensi)
            </div>
        @else
            <div class="info-item" style="color: #059669; font-weight: bold;">
                📌 Data dari: Absensi Harian
            </div>
        @endif
    </div>

    <!-- Notice untuk yang belum lengkap -->
    @if (($report_type ?? '') === 'mingguan' && isset($employees) && isset($weeks))
        @php
            $notTakenEmployees = collect($employees)->filter(function ($emp) use ($weeks) {
                return ($emp['total_taken'] ?? 0) < count($weeks);
            });
        @endphp

        @if ($notTakenEmployees->count() > 0)
            <div class="notice-box">
                <div class="notice-title">⚠ KARYAWAN BELUM LENGKAP AMBIL TUNJANGAN:</div>
                <div class="notice-content">
                    @foreach ($notTakenEmployees->take(8) as $emp)
                        {{ $emp['karyawan']->full_name ?? '' }} ({{ $emp['total_taken'] ?? 0 }}/{{ count($weeks) }}),
                    @endforeach
                    @if ($notTakenEmployees->count() > 8)
                        dan {{ $notTakenEmployees->count() - 8 }} lainnya
                    @endif
                </div>
            </div>
        @endif
    @elseif(($report_type ?? '') === 'bulanan' && isset($employees))
        @php
            $notTakenEmployees = collect($employees)->filter(function ($emp) {
                return !($emp['is_taken'] ?? false) && ($emp['amount'] ?? 0) > 0;
            });
        @endphp

        @if ($notTakenEmployees->count() > 0)
            <div class="notice-box">
                <div class="notice-title">⚠ KARYAWAN BELUM AMBIL TUNJANGAN BULANAN:</div>
                <div class="notice-content">
                    @foreach ($notTakenEmployees->take(10) as $emp)
                        {{ $emp['karyawan']->full_name ?? '' }}
                        @if (isset($is_lembur) && $is_lembur && isset($emp['total_lembur']))
                            ({{ $emp['total_lembur'] }} lembur)
                        @endif,
                    @endforeach
                    @if ($notTakenEmployees->count() > 10)
                        dan {{ $notTakenEmployees->count() - 10 }} lainnya
                    @endif
                </div>
            </div>
        @endif
    @elseif(($report_type ?? '') === 'harian' && isset($employees))
        @php
            $incompleteEmployees = collect($employees)->filter(function ($emp) {
                return ($emp['pending_days'] ?? 0) > 0;
            });
        @endphp

        @if ($incompleteEmployees->count() > 0)
            <div class="notice-box">
                <div class="notice-title">⚠ KARYAWAN BELUM LENGKAP AMBIL TUNJANGAN HARIAN:</div>
                <div class="notice-content">
                    @foreach ($incompleteEmployees->take(8) as $emp)
                        {{ $emp['karyawan']->full_name ?? '' }} ({{ $emp['pending_days'] ?? 0 }} hari),
                    @endforeach
                    @if ($incompleteEmployees->count() > 8)
                        dan {{ $incompleteEmployees->count() - 8 }} lainnya
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        Dicetak: {{ $generated_at ?? date('d/m/Y H:i:s') }}
    </div>
</body>

</html>
