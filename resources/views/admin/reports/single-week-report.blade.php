<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tunjangan 1 Minggu</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            background: #ffffff;
            padding: 10px;
            color: #000000;
        }

        /* HEADER */
        .header {
            background: #000000;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 10px;
            border: 2px solid #000000;
        }

        .company-logo {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 11px;
            font-weight: bold;
            margin: 3px 0;
        }

        .header > div:last-child {
            font-size: 8px;
            margin-top: 3px;
        }

        /* DATA SOURCE */
        .data-source-notice {
            background: #e0e0e0;
            border: 1px solid #000000;
            padding: 4px 8px;
            margin-bottom: 10px;
            display: inline-block;
            font-weight: bold;
            font-size: 7px;
        }

        /* WEEK INFO */
        .week-info {
            background: #ffffff;
            border: 1px solid #000000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .week-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .week-info td {
            padding: 4px 8px;
            border: 1px solid #cccccc;
            font-size: 7px;
        }

        .week-info td:first-child {
            background: #f0f0f0;
            font-weight: bold;
            width: 120px;
        }

        .week-info td:last-child {
            font-weight: bold;
        }

        .days-row {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #cccccc;
            text-align: center;
        }

        .days-row strong {
            font-size: 7px;
            margin-right: 5px;
        }

        .day-item {
            display: inline-block;
            margin: 2px;
            padding: 3px 6px;
            background: #ffffff;
            border: 1px solid #000000;
            font-size: 6px;
            font-weight: bold;
        }

        .weekend {
            background: #cccccc;
        }

        /* MAIN TABLE */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
            background: white;
            border: 1px solid #000000;
            margin-bottom: 10px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #cccccc;
            padding: 5px 3px;
            text-align: center;
        }

        .main-table thead {
            background: #000000;
        }

        .main-table th {
            color: white;
            font-weight: bold;
            font-size: 6px;
            padding: 6px 3px;
        }

        .main-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .col-name { width: 90px; text-align: left !important; }
        .col-dept { width: 70px; text-align: left !important; }
        .col-day { width: 30px; }
        .col-work { width: 28px; }
        .col-jam { width: 28px; }
        .col-status { width: 25px; }
        .col-amount { width: 60px; }

        .employee-name {
            text-align: left;
            font-weight: bold;
            font-size: 7px;
            padding-left: 5px;
        }

        .employee-name > div {
            font-size: 5px;
            color: #666666;
            font-weight: normal;
            margin-top: 1px;
        }

        .department {
            text-align: left;
            font-size: 6px;
            padding-left: 5px;
        }

        .department > div {
            font-size: 5px;
            color: #888888;
            margin-top: 1px;
        }

        .weekend-header {
            background: #555555 !important;
        }

        .time-text {
            font-size: 5px;
            color: #666666;
            display: block;
            margin-top: 1px;
        }

        .jam-text {
            font-size: 5px;
            display: block;
            margin-top: 1px;
        }

        /* SUMMARY */
        .summary-section {
            background: #ffffff;
            border: 1px solid #000000;
            padding: 8px;
            margin-bottom: 5px;
        }

        .summary-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-section td {
            padding: 5px 8px;
            border: 1px solid #cccccc;
            text-align: center;
            font-size: 7px;
        }

        .summary-section td:first-child {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }

        .summary-section td:last-child {
            font-weight: bold;
            font-size: 9px;
        }

        /* NOTICE */
        .notice-box {
            background: #f5f5f5;
            border: 1px solid #000000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .notice-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 7px;
        }

        .notice-content {
            font-size: 6px;
            line-height: 1.4;
        }

        /* APPROVAL */
        .approval-section {
            background: #f9f9f9;
            border: 1px solid #000000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .approval-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 7px;
        }

        .approval-content {
            font-size: 6px;
            line-height: 1.4;
        }

        .approval-item {
            margin-bottom: 4px;
            padding: 4px;
            background: white;
            border-left: 2px solid #000000;
        }

        /* FOOTER */
        .footer {
            background: #f0f0f0;
            padding: 2px;
            text-align: center;
            font-size: 6px;
            color: #666666;
            border-top: 1px solid #000000;
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
        <div class="data-source-notice">SUMBER: LAPORAN LEMBUR</div>
    @endif

    <!-- Week Info -->
    <div class="week-info">
        <table>
            <tr>
                <td>Jenis Tunjangan</td>
                <td>{{ $tunjanganType->display_name ?? 'Tidak Diketahui' }}</td>
                <td>Total Karyawan</td>
                <td>{{ count($employees ?? []) }} orang</td>
                <td>Periode</td>
                <td>{{ isset($week_info['start']) ? $week_info['start']->format('d/m/Y') : '' }} - {{ isset($week_info['end']) ? $week_info['end']->format('d/m/Y') : '' }}</td>
            </tr>

        </table>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th class="col-name">Nama Karyawan</th>
                <th class="col-dept">Departemen</th>
                @if(isset($week_info['days']))
                    @foreach($week_info['days'] as $day)
                        <th class="col-day {{ $day['is_weekend'] ? 'weekend-header' : '' }}">
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
                    <div>{{ $employee['karyawan']->nip ?? '' }}</div>
                </td>
                <td class="department col-dept">
                    {{ $employee['karyawan']->department->name ?? '-' }}
                    <div>{{ isset($employee['karyawan']->staff_status) ? ucfirst(str_replace('_', ' ', $employee['karyawan']->staff_status)) : '' }}</div>
                </td>

                @if(isset($is_lembur) && $is_lembur && isset($employee['daily_lembur']))
                    @foreach($employee['daily_lembur'] as $daily)
                        <td class="col-day">
                            @if($daily['has_lembur'] ?? false)
                                <strong>V</strong>
                                <span class="jam-text">{{ number_format($daily['total_jam'], 1) }}j</span>
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                @elseif(isset($employee['daily_attendance']))
                    @foreach($employee['daily_attendance'] as $daily)
                        <td class="col-day">
                            @if($daily['is_present'] ?? false)
                                <strong>V</strong>
                                @if($daily['clock_in'] ?? false)
                                    <span class="time-text">{{ date('H:i', strtotime($daily['clock_in'])) }}</span>
                                @endif
                            @else
                                X
                            @endif
                        </td>
                    @endforeach
                @else
                    @for($i = 0; $i < 7; $i++)
                        <td class="col-day">-</td>
                    @endfor
                @endif

                <td class="col-work" style="font-weight: bold;">
                    @if(isset($is_lembur) && $is_lembur)
                        {{ $employee['total_lembur'] ?? 0 }}
                    @else
                        {{ $employee['work_days'] ?? 0 }}
                    @endif
                </td>

                @if(isset($is_lembur) && $is_lembur)
                    <td class="col-jam" style="font-weight: bold;">
                        {{ number_format($employee['total_jam'] ?? 0, 1) }}
                    </td>
                @endif

                <td class="col-status">
                    @if($employee['is_taken'] ?? false)
                        <strong>V</strong>
                    @elseif(($employee['status'] ?? '') === 'no_data')
                        -
                    @else
                        X
                    @endif
                </td>

                <td class="col-amount" style="font-weight: bold;">
                    {{ number_format($employee['amount'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="20" style="text-align: center; padding: 15px;">
                    Tidak ada data karyawan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <table>
            <tr>
                <td>Total Karyawan</td>
                <td>{{ count($employees ?? []) }}</td>

                @if(isset($is_lembur) && $is_lembur)
                    <td>Total Jam</td>
                    <td>{{ number_format($summary['total_jam_lembur'] ?? 0, 1) }}</td>
                @endif
                <td>Sudah Diambil</td>
                <td>{{ $summary['taken_count'] ?? 0 }}</td>
                <td>Belum Diambil</td>
                <td>{{ $summary['not_taken_count'] ?? 0 }}</td>
                <td>Total Nominal</td>
                <td>Rp {{ number_format(($summary['total_amount'] ?? 0)) }}</td>
            </tr>
        </table>
    </div>

    <!-- Detail Approval -->
    {{-- @if(isset($employees) && collect($employees)->where('is_taken', true)->count() > 0)
        @php
            $takenEmployees = collect($employees)->where('is_taken', true);
        @endphp
        <div class="approval-section">
            <div class="approval-title">DETAIL PENGAMBILAN TUNJANGAN:</div>
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
    @endif --}}

    <!-- Notice -->
    {{-- @if(isset($employees) && collect($employees)->where('is_taken', false)->where('status', '!=', 'no_data')->count() > 0)
        @php
            $notTakenEmployees = collect($employees)->where('is_taken', false)->where('status', '!=', 'no_data');
        @endphp
        <div class="notice-box">
            <div class="notice-title">KARYAWAN BELUM AMBIL TUNJANGAN:</div>
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
        Dicetak: {{ $generated_at ?? date('d/m/Y H:i:s') }} | PT. Jaringan Fiberone Indonesia
    </div>
</body>
</html>
