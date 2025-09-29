<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tunjangan Harian - Semua Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
        }

        .info-item {
            display: inline-block;
            margin-right: 30px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #ddd;
            padding: 6px;
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
            width: 150px;
        }

        .department {
            text-align: left;
            font-size: 8px;
            color: #666;
            width: 100px;
        }

        .status-taken {
            color: #22c55e;
            font-weight: bold;
        }

        .status-not-taken {
            color: #ef4444;
            font-weight: bold;
        }

        .status-no-data {
            color: #9ca3af;
        }

        .summary-section {
            margin-top: 20px;
            background: #e0f2fe;
            border: 1px solid #0891b2;
            border-radius: 5px;
            padding: 15px;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0891b2;
            font-size: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #0891b2;
        }

        .summary-label {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            right: 15px;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-logo">PT. NAMA PERUSAHAAN</div>
        <div class="report-title">LAPORAN TUNJANGAN HARIAN - SEMUA KARYAWAN</div>
        <div>{{ $month_name }}</div>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <div class="info-item"><strong>Jenis Tunjangan:</strong> {{ $tunjanganType->name }}</div>
        <div class="info-item"><strong>Kategori:</strong> {{ ucfirst($tunjanganType->category) }}</div>
        <div class="info-item"><strong>Total Karyawan:</strong> {{ $summary['total_employees'] }}</div>
        <div class="info-item"><strong>Total Hari:</strong> {{ $summary['total_days'] }}</div>
    </div>

    <!-- Main Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 150px;">Nama Karyawan</th>
                <th style="width: 100px;">Departemen</th>
                <th style="width: 60px;">Total<br>Hari</th>
                <th style="width: 60px;">Sudah<br>Diambil</th>
                <th style="width: 60px;">Belum<br>Diambil</th>
                <th style="width: 80px;">Total<br>Nominal</th>
                <th style="width: 60px;">Status<br>Terakhir</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td class="employee-name">
                    {{ $employee['karyawan']->full_name }}
                    <div style="font-size: 8px; color: #666; font-weight: normal;">
                        {{ $employee['karyawan']->nip }}
                    </div>
                </td>
                <td class="department">
                    {{ $employee['karyawan']->department->name ?? '-' }}
                    <div style="font-size: 7px; color: #999;">
                        {{ ucfirst(str_replace('_', ' ', $employee['karyawan']->staff_status)) }}
                    </div>
                </td>
                <td style="font-weight: bold;">{{ $employee['total_days'] }}</td>
                <td class="status-taken">{{ $employee['taken_days'] }}</td>
                <td class="status-not-taken">{{ $employee['pending_days'] }}</td>
                <td style="font-weight: bold;">{{ number_format($employee['total_amount'], 0, ',', '.') }}</td>
                <td>
                    @if($employee['total_days'] == 0)
                        <span class="status-no-data">No Data</span>
                    @elseif($employee['pending_days'] == 0)
                        <span class="status-taken">Complete</span>
                    @else
                        <span class="status-not-taken">Partial</span>
                    @endif
                </td>
                <td>
                    @if($employee['total_days'] == 0)
                        Tidak ada data tunjangan
                    @elseif($employee['pending_days'] == 0)
                        Semua hari sudah diambil
                    @else
                        {{ $employee['pending_days'] }} hari belum diambil
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">RINGKASAN LAPORAN</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $summary['total_employees'] }}</div>
                <div class="summary-label">Total Karyawan</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">{{ $summary['total_days'] }}</div>
                <div class="summary-label">Total Hari</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">{{ $summary['taken_days'] }}</div>
                <div class="summary-label">Sudah Diambil</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">{{ $summary['pending_days'] }}</div>
                <div class="summary-label">Belum Diambil</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format($summary['total_amount']) }} </div>
                <div class="summary-label">Total Nominal</div>
            </div>
        </div>
    </div>

    <!-- Notice untuk yang belum lengkap -->
    @php
        $incompleteEmployees = collect($employees)->filter(function($emp) {
            return $emp['pending_days'] > 0;
        });
    @endphp

    @if($incompleteEmployees->count() > 0)
    <div style="margin-top: 15px; background: #fef2f2; border: 1px solid #ef4444; border-radius: 5px; padding: 10px;">
        <div style="font-weight: bold; color: #dc2626; margin-bottom: 8px;">
            ⚠ KARYAWAN BELUM LENGKAP AMBIL TUNJANGAN HARIAN:
        </div>
        <div style="font-size: 9px; color: #dc2626;">
            @foreach($incompleteEmployees->take(8) as $emp)
                {{ $emp['karyawan']->full_name }} ({{ $emp['pending_days'] }} hari),
            @endforeach
            @if($incompleteEmployees->count() > 8)
                dan {{ $incompleteEmployees->count() - 8 }} lainnya
            @endif
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Dicetak: {{ $generated_at }}
    </div>
</body>
</html>
