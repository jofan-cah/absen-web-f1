<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi - {{ $monthName }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #64748b;
        }

        .header h1 {
            font-size: 18px;
            color: #334155;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }

        .info-section {
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            color: #475569;
        }

        .info-value {
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-header {
            background: #475569;
            color: white;
        }

        .table-header th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #334155;
        }

        .employee-row {
            background: #cbd5e1;
            font-weight: bold;
        }

        .employee-row td {
            padding: 8px 5px;
            border: 1px solid #94a3b8;
        }

        .detail-row td {
            padding: 6px 5px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }

        .detail-row:nth-child(odd) {
            background: #ffffff;
        }

        .detail-row:nth-child(even) {
            background: #f8fafc;
        }

        .summary-row {
            background: #fef3c7;
            font-weight: bold;
        }

        .summary-row td {
            padding: 8px 5px;
            border: 1px solid #fbbf24;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
        }

        .status-present {
            background: #dcfce7;
            color: #166534;
        }

        .status-late {
            background: #fef3c7;
            color: #92400e;
        }

        .status-absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-scheduled {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-early {
            background: #fed7aa;
            color: #9a3412;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #64748b;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PT. JARINGAN FIBERONE INDONESIA</h1>
        <h2>Laporan Absensi Karyawan</h2>
        <p style="font-size: 11px; margin-top: 5px;">
            Periode: <strong>{{ $monthName }} {{ $year }}</strong>
            @if($department)
                | Department: <strong>{{ $department->name }}</strong>
            @endif
        </p>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Total Karyawan:</span>
            <span class="info-value">{{ $totalKaryawan }} orang</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dicetak pada:</span>
            <span class="info-value">{{ $generatedAt }}</span>
        </div>
    </div>

    @if(count($reportData) > 0)
        @foreach($reportData as $karyawanId => $data)
            @php
                $karyawan = $data['karyawan'];
                $totalPersentaseKehadiran = $data['total_jadwal'] > 0
                    ? round((($data['hadir'] + $data['terlambat']) / $data['total_jadwal']) * 100, 1)
                    : 0;
            @endphp

            <!-- Employee Header & Summary -->
            <table style="margin-bottom: 10px;">
                <tr class="employee-row">
                    <td colspan="8" style="background: #475569; color: white; padding: 8px 5px;">
                        <strong>{{ $karyawan->full_name }}</strong>
                        ({{ $karyawan->nip }}) -
                        {{ $karyawan->department->name ?? 'No Department' }}
                    </td>
                </tr>
                <tr style="background: #f1f5f9; font-weight: bold; text-align: center;">
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #dcfce7; color: #166534; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Hadir</div>
                        <div style="font-size: 14px;">{{ $data['hadir'] }}</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #fef3c7; color: #92400e; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Terlambat</div>
                        <div style="font-size: 14px;">{{ $data['terlambat'] }}</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #fee2e2; color: #991b1b; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Alpha</div>
                        <div style="font-size: 14px;">{{ $data['tidak_hadir'] }}</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #fed7aa; color: #9a3412; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Pulang Cepat</div>
                        <div style="font-size: 14px;">{{ $data['pulang_cepat'] }}</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #e2e8f0; color: #475569; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Total Jadwal</div>
                        <div style="font-size: 14px;">{{ $data['total_jadwal'] }}</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #ddd6fe; color: #5b21b6; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Jam Kerja</div>
                        <div style="font-size: 14px;">{{ number_format($data['total_jam_kerja'], 1) }}</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #fef3c7; color: #92400e; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Keterlambatan</div>
                        <div style="font-size: 14px;">{{ $data['total_terlambat_menit'] }} mnt</div>
                    </td>
                    <td style="padding: 6px 5px; border: 1px solid #cbd5e1; background: #d1fae5; color: #065f46; width: 12.5%;">
                        <div style="font-size: 8px; margin-bottom: 2px;">Kehadiran</div>
                        <div style="font-size: 14px;">{{ $totalPersentaseKehadiran }}%</div>
                    </td>
                </tr>
            </table>

            <!-- Detail Table -->
            <table>
                <thead class="table-header">
                    <tr>
                        <th style="width: 8%;">Tanggal</th>
                        <th style="width: 8%;">Hari</th>
                        <th style="width: 12%;">Shift</th>
                        <th style="width: 10%;">Clock In</th>
                        <th style="width: 10%;">Clock Out</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 8%; text-align: right;">Terlambat</th>
                        <th style="width: 8%; text-align: right;">Jam Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['detail_absens'] as $absen)
                        <tr class="detail-row">
                            <td>{{ $absen->date->format('d/m/Y') }}</td>
                            <td>{{ $absen->date->format('D') }}</td>
                            <td>
                                {{ $absen->jadwal->shift->name }}<br>
                                <span style="font-size: 8px; color: #64748b;" class="font-mono">
                                    {{ \Carbon\Carbon::parse($absen->jadwal->shift->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($absen->jadwal->shift->end_time)->format('H:i') }}
                                </span>
                            </td>
                            <td class="font-mono">
                                {{ $absen->clock_in ? \Carbon\Carbon::parse($absen->clock_in)->format('H:i:s') : '-' }}
                            </td>
                            <td class="font-mono">
                                {{ $absen->clock_out ? \Carbon\Carbon::parse($absen->clock_out)->format('H:i:s') : '-' }}
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'present' => 'status-present',
                                        'late' => 'status-late',
                                        'absent' => 'status-absent',
                                        'scheduled' => 'status-scheduled',
                                        'early_checkout' => 'status-early'
                                    ];

                                    $statusLabel = [
                                        'present' => 'Hadir',
                                        'late' => 'Terlambat',
                                        'absent' => 'Alpha',
                                        'scheduled' => 'Scheduled',
                                        'early_checkout' => 'Pulang Cepat'
                                    ];
                                @endphp
                                <span class="status-badge {{ $statusClass[$absen->status] ?? 'status-scheduled' }}">
                                    {{ $statusLabel[$absen->status] ?? ucfirst($absen->status) }}
                                </span>
                            </td>
                            <td class="text-right">
                                @if($absen->late_minutes > 0)
                                    <span style="color: #dc2626; font-weight: bold;">{{ $absen->late_minutes }} mnt</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($absen->work_hours)
                                    <strong>{{ number_format($absen->work_hours, 1) }}</strong> jam
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="no-data">Tidak ada data absensi</td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- Summary Row -->
                <tfoot>
                    <tr class="summary-row">
                        <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL:</td>
                        <td style="font-weight: bold;">
                            H: {{ $data['hadir'] }} |
                            T: {{ $data['terlambat'] }} |
                            A: {{ $data['tidak_hadir'] }}
                        </td>
                        <td class="text-right" style="color: #dc2626;">
                            {{ $data['total_terlambat_menit'] }} mnt
                        </td>
                        <td class="text-right" style="color: #475569; font-weight: bold;">
                            {{ number_format($data['total_jam_kerja'], 1) }} jam
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if(!$loop->last)
                <div style="margin-bottom: 20px; border-bottom: 2px dashed #cbd5e1;"></div>
            @endif
        @endforeach
    @else
        <div class="no-data">
            <p>Tidak ada data absensi untuk periode yang dipilih.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem pada {{ $generatedAt }}</p>
        <p>PT. Jaringan Fiberone Indonesia - Sistem Manajemen Absensi</p>
    </div>
</body>
</html>
