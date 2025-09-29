<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tunjangan 1 Minggu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .company-logo {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .report-title {
            font-size: 12px;
            font-weight: bold;
            margin: 4px 0;
        }

        .week-info {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 8px;
        }

        .week-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 6px;
        }

        .week-item {
            display: flex;
            gap: 5px;
        }

        .days-row {
            text-align: center;
            margin-top: 6px;
        }

        .day-item {
            display: inline-block;
            margin: 0 4px;
            padding: 1px 4px;
            background: #e0f2fe;
            border-radius: 2px;
            font-size: 7px;
        }

        .weekend {
            background: #fef3c7;
            color: #f59e0b;
        }

        .data-source-notice {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            color: #92400e;
        }

        .data-source-lembur {
            background: #e0f2fe;
            border: 1px solid #0891b2;
            color: #164e63;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
            table-layout: fixed;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }

        .main-table th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
            font-size: 6px;
        }

        .main-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Fixed column widths */
        .col-name { width: 90px; }
        .col-dept { width: 70px; }
        .col-day { width: 35px; }
        .col-work { width: 30px; }
        .col-jam { width: 30px; }
        .col-status { width: 25px; }
        .col-amount { width: 50px; }

        .employee-name {
            text-align: left;
            font-weight: bold;
            font-size: 7px;
        }

        .department {
            text-align: left;
            font-size: 6px;
            color: #666;
        }

        .day-header {
            background-color: #6b7280 !important;
            color: white;
            font-size: 6px;
        }

        .weekend-header {
            background-color: #f59e0b !important;
            color: white;
        }

        .present {
            color: #22c55e;
            font-weight: bold;
            font-size: 9px;
        }

        .absent {
            color: #ef4444;
            font-weight: bold;
            font-size: 9px;
        }

        .lembur-icon {
            color: #0891b2;
            font-weight: bold;
            font-size: 9px;
        }

        .time-text {
            font-size: 5px;
            color: #666;
            display: block;
            margin-top: 1px;
        }

        .jam-text {
            font-size: 5px;
            color: #0891b2;
            display: block;
            margin-top: 1px;
        }

        .kategori-text {
            font-size: 4px;
            color: #f59e0b;
            display: block;
            margin-top: 1px;
        }

        .summary-section {
            margin-top: 15px;
            background: #e0f2fe;
            border: 1px solid #0891b2;
            border-radius: 4px;
            padding: 10px;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #0891b2;
            font-size: 10px;
        }

        .summary-grid {
            display: flex;
            justify-content: space-around;
            gap: 8px;
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #0891b2;
        }

        .summary-label {
            font-size: 7px;
            color: #666;
            margin-top: 2px;
        }

        .notice-box {
            margin-top: 12px;
            background: #fef2f2;
            border: 1px solid #ef4444;
            border-radius: 4px;
            padding: 8px;
        }

        .notice-title {
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 4px;
            font-size: 8px;
        }

        .notice-content {
            font-size: 7px;
            color: #dc2626;
        }

        .approval-section {
            margin-top: 12px;
            background: #dcfce7;
            border: 1px solid #22c55e;
            border-radius: 4px;
            padding: 8px;
        }

        .approval-title {
            font-weight: bold;
            color: #15803d;
            margin-bottom: 6px;
            font-size: 8px;
        }

        .approval-content {
            font-size: 7px;
            color: #15803d;
        }

        .approval-item {
            margin-bottom: 2px;
        }

        .footer {
            position: fixed;
            bottom: 8px;
            right: 10px;
            font-size: 6px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-logo">PT. JARINGAN FIBERONE INDONESIA</div>
        <div class="report-title">LAPORAN TUNJANGAN 1 MINGGU - SEMUA KARYAWAN</div>
        <div>{{ $week_info['full_label'] ?? 'Periode Tidak Diketahui' }}</div>
    </div>

    <!-- Data Source Notice -->
    @if(isset($is_lembur) && $is_lembur)
        <div class="data-source-notice data-source-lembur">
            📋 DATA SUMBER: LAPORAN LEMBUR (Bukan dari Absensi Harian)
        </div>
    @else
        <div class="data-source-notice">
            📋 DATA SUMBER: ABSENSI HARIAN (Kehadiran Kerja)
        </div>
    @endif

    <!-- Week Info -->
    <div class="week-info">
        <div class="week-details">
            <div class="week-item">
                <strong>Jenis Tunjangan:</strong>
                <span>{{ $tunjanganType->name ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="week-item">
                <strong>Total Karyawan:</strong>
                <span>{{ count($employees ?? []) }} orang</span>
            </div>
            <div class="week-item">
                <strong>Periode:</strong>
                <span>{{ isset($week_info['start']) ? $week_info['start']->format('d/m/Y') : '' }} - {{ isset($week_info['end']) ? $week_info['end']->format('d/m/Y') : '' }}</span>
            </div>
            <div class="week-item">
                @if(isset($is_lembur) && $is_lembur)
                    <strong>Total Lembur:</strong>
                    <span>{{ $summary['total_work_days'] ?? 0 }} kali</span>
                @else
                    <strong>Total Hari Kerja:</strong>
                    <span>{{ $summary['total_work_days'] ?? 0 }} hari</span>
                @endif
            </div>
            @if(isset($is_lembur) && $is_lembur)
                <div class="week-item">
                    <strong>Total Jam Lembur:</strong>
                    <span>{{ number_format($summary['total_jam_lembur'] ?? 0, 1) }} jam</span>
                </div>
            @endif
        </div>

        <div class="days-row">
            <strong>Hari dalam Minggu:</strong>
            @if(isset($week_info['days']))
                @foreach($week_info['days'] as $day)
                    <span class="day-item {{ $day['is_weekend'] ? 'weekend' : '' }}">
                        {{ $day['day_short'] }} {{ $day['date_num'] }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Main Table dengan Fixed Layout -->
    <table class="main-table">
        <thead>
            <tr>
                <th class="col-name">Nama Karyawan</th>
                <th class="col-dept">Departemen</th>
                @if(isset($week_info['days']))
                    @foreach($week_info['days'] as $day)
                        <th class="col-day {{ $day['is_weekend'] ? 'weekend-header' : 'day-header' }}">
                            {{ $day['day_short'] }}<br>{{ $day['date_num'] }}
                        </th>
                    @endforeach
                @endif
                <th class="col-work">
                    @if(isset($is_lembur) && $is_lembur)
                        Total<br>Lembur
                    @else
                        Hari<br>Kerja
                    @endif
                </th>
                @if(isset($is_lembur) && $is_lembur)
                    <th class="col-jam">Total<br>Jam</th>
                @endif
                <th class="col-status">Status</th>
                <th class="col-amount">Total<br>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($employees ?? []) as $employee)
            <tr>
                <td class="employee-name col-name">
                    {{ $employee['karyawan']->full_name ?? 'Tidak Diketahui' }}
                    <div style="font-size: 6px; color: #666; font-weight: normal;">
                        {{ $employee['karyawan']->nip ?? '' }}
                    </div>
                </td>
                <td class="department col-dept">
                    {{ $employee['karyawan']->department->name ?? '-' }}
                    <div style="font-size: 5px; color: #999;">
                        {{ isset($employee['karyawan']->staff_status) ? ucfirst(str_replace('_', ' ', $employee['karyawan']->staff_status)) : '' }}
                    </div>
                </td>

                {{-- TAMPILAN PER HARI: LEMBUR vs ABSEN --}}
                @if(isset($is_lembur) && $is_lembur && isset($employee['daily_lembur']))
                    {{-- TAMPILKAN DATA LEMBUR PER HARI --}}
                    @foreach($employee['daily_lembur'] as $daily)
                        <td class="col-day">
                            @if($daily['has_lembur'] ?? false)
                                <span class="lembur-icon">⏰</span>
                                <span class="jam-text">{{ number_format($daily['total_jam'], 1) }}j</span>
                                @if($daily['kategori'] ?? false)
                                    <span class="kategori-text">{{ substr($daily['kategori'], 0, 3) }}</span>
                                @endif
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                    @endforeach
                @elseif(isset($employee['daily_attendance']))
                    {{-- TAMPILKAN DATA ABSEN PER HARI --}}
                    @foreach($employee['daily_attendance'] as $daily)
                        <td class="col-day">
                            @if($daily['is_present'] ?? false)
                                <span class="present">✓</span>
                                @if($daily['clock_in'] ?? false)
                                    <span class="time-text">{{ date('H:i', strtotime($daily['clock_in'])) }}</span>
                                @endif
                            @else
                                <span class="absent">✗</span>
                            @endif
                        </td>
                    @endforeach
                @else
                    {{-- Fallback jika tidak ada data --}}
                    @for($i = 0; $i < 7; $i++)
                        <td class="col-day">
                            <span style="color: #9ca3af;">-</span>
                        </td>
                    @endfor
                @endif

                {{-- TOTAL HARI/LEMBUR --}}
                <td class="col-work" style="font-weight: bold;">
                    @if(isset($is_lembur) && $is_lembur)
                        {{ $employee['total_lembur'] ?? 0 }}
                    @else
                        {{ $employee['work_days'] ?? 0 }}
                    @endif
                </td>

                {{-- KHUSUS LEMBUR: TOTAL JAM --}}
                @if(isset($is_lembur) && $is_lembur)
                    <td class="col-jam" style="font-weight: bold; color: #0891b2;">
                        {{ number_format($employee['total_jam'] ?? 0, 1) }}
                    </td>
                @endif

                {{-- STATUS --}}
                <td class="col-status">
                    @if($employee['is_taken'] ?? false)
                        <span class="present">✓</span>
                    @elseif(($employee['status'] ?? '') === 'no_data')
                        <span style="color: #9ca3af;">-</span>
                    @else
                        <span class="absent">✗</span>
                    @endif
                </td>

                {{-- TOTAL NOMINAL --}}
                <td class="col-amount" style="font-weight: bold;">
                    {{ number_format($employee['amount'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="20" style="text-align: center; padding: 15px; color: #666;">
                    Tidak ada data karyawan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">RINGKASAN LAPORAN</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ count($employees ?? []) }}</div>
                <div class="summary-label">Total Karyawan</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">{{ $summary['total_work_days'] ?? 0 }}</div>
                <div class="summary-label">
                    @if(isset($is_lembur) && $is_lembur)
                        Total Lembur
                    @else
                        Total Hari Kerja
                    @endif
                </div>
            </div>

            @if(isset($is_lembur) && $is_lembur)
                <div class="summary-item">
                    <div class="summary-value">{{ number_format($summary['total_jam_lembur'] ?? 0, 1) }}</div>
                    <div class="summary-label">Total Jam</div>
                </div>
            @endif

            <div class="summary-item">
                <div class="summary-value">{{ $summary['taken_count'] ?? 0 }}</div>
                <div class="summary-label">Sudah Diambil</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">{{ $summary['not_taken_count'] ?? 0 }}</div>
                <div class="summary-label">Belum Diambil</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format(($summary['total_amount'] ?? 0)) }}</div>
                <div class="summary-label">Total Nominal</div>
            </div>
        </div>
    </div>

    <!-- Detail Approval (jika ada yang sudah diambil) -->
    {{-- @if(isset($employees) && collect($employees)->where('is_taken', true)->count() > 0)
        @php
            $takenEmployees = collect($employees)->where('is_taken', true);
        @endphp

        <div class="approval-section">
            <div class="approval-title">✓ DETAIL PENGAMBILAN TUNJANGAN:</div>
            <div class="approval-content">
                @foreach($takenEmployees as $emp)
                    <div class="approval-item">
                        <strong>{{ $emp['karyawan']->full_name ?? '' }}:</strong>
                        @if(isset($is_lembur) && $is_lembur)
                            {{ $emp['total_lembur'] ?? 0 }} kali lembur ({{ number_format($emp['total_jam'] ?? 0, 1) }} jam),
                        @else
                            {{ $emp['work_days'] ?? 0 }} hari kerja,
                        @endif
                        Rp {{ number_format($emp['amount'] ?? 0, 0, ',', '.') }}
                        @if($emp['approved_date'] ?? false)
                            - Disetujui: {{ is_string($emp['approved_date']) ? $emp['approved_date'] : $emp['approved_date']->format('d/m/Y') }}
                        @endif
                        @if($emp['received_date'] ?? false)
                            - Diambil: {{ is_string($emp['received_date']) ? $emp['received_date'] : $emp['received_date']->format('d/m/Y') }}
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Notice untuk yang belum diambil -->
    @if(isset($employees) && collect($employees)->where('is_taken', false)->where('status', '!=', 'no_data')->count() > 0)
        @php
            $notTakenEmployees = collect($employees)->where('is_taken', false)->where('status', '!=', 'no_data');
        @endphp

        <div class="notice-box">
            <div class="notice-title">⚠ KARYAWAN BELUM AMBIL TUNJANGAN:</div>
            <div class="notice-content">
                @foreach($notTakenEmployees->take(10) as $emp)
                    {{ $emp['karyawan']->full_name ?? '' }}
                    @if(isset($is_lembur) && $is_lembur)
                        ({{ $emp['total_lembur'] ?? 0 }} lembur, {{ number_format($emp['total_jam'] ?? 0, 1) }}j)
                    @else
                        ({{ $emp['work_days'] ?? 0 }} hari)
                    @endif,
                @endforeach
                @if($notTakenEmployees->count() > 10)
                    dan {{ $notTakenEmployees->count() - 10 }} lainnya
                @endif
            </div>
        </div>
    @endif --}}

    <!-- Footer -->
    <div class="footer">
        Dicetak: {{ $generated_at ?? date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
