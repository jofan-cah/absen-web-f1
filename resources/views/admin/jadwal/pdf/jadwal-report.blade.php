<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal {{ $monthName }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            color: #1f2937;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4b5563;
        }

        .header h1 {
            font-size: 18px;
            color: #111827;
            margin-bottom: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .header .department {
            font-size: 11px;
            color: #374151;
            font-weight: 600;
            margin-top: 5px;
        }

        .meta-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            font-size: 8px;
            color: #6b7280;
        }

        .meta-info .left {
            display: table-cell;
            text-align: left;
        }

        .meta-info .right {
            display: table-cell;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #374151;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #d1d5db;
            font-size: 8px;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-present {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-absent {
            background-color: #fef3c7;
            color: #92400e;
        }

        .shift-info {
            font-weight: 600;
            color: #374151;
        }

        .time-info {
            color: #6b7280;
            font-size: 7px;
            margin-top: 2px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
        }

        .summary {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #d1d5db;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }

        .summary-label {
            font-size: 7px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Jadwal Kerja</h1>
        <div class="subtitle">Periode: {{ $monthName }} {{ $year }}</div>
        <div class="department">{{ $departmentName }}</div>
    </div>

    <!-- Meta Info -->
    <div class="meta-info">
        <div class="left">
            Dicetak oleh: <strong>{{ $generatedBy }}</strong>
        </div>
        <div class="right">
            Tanggal Cetak: <strong>{{ $generatedAt }}</strong>
        </div>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Jadwal</div>
                <div class="summary-value">{{ $jadwals->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Aktif</div>
                <div class="summary-value">{{ $jadwals->where('is_active', 1)->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Tidak Aktif</div>
                <div class="summary-value">{{ $jadwals->where('is_active', 0)->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Sudah Absen</div>
                <div class="summary-value">{{ $jadwals->filter(function($j) { return $j->absen !== null; })->count() }}</div>
            </div>
        </div>
    </div>

    @if($jadwals->count() > 0)
    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 8%;">NIP</th>
                <th style="width: 20%;">Nama Karyawan</th>
                <th style="width: 15%;">Department</th>
                <th style="width: 15%;">Shift</th>
                <th style="width: 12%;">Jam Kerja</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 7%;">Absen</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $index => $jadwal)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($jadwal->date)->format('d/m/Y') }}</td>
                <td>{{ $jadwal->karyawan->nip ?? '-' }}</td>
                <td>{{ $jadwal->karyawan->full_name ?? '-' }}</td>
                <td>{{ $jadwal->karyawan->department->name ?? '-' }}</td>
                <td>
                    <div class="shift-info">{{ $jadwal->shift->name ?? '-' }}</div>
                </td>
                <td>
                    <div class="time-info">
                        {{ $jadwal->shift ? $jadwal->shift->start_time . ' - ' . $jadwal->shift->end_time : '-' }}
                    </div>
                </td>
                <td style="text-align: center;">
                    <span class="badge {{ $jadwal->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $jadwal->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </td>
                <td style="text-align: center;">
                    @if($jadwal->absen)
                        <span class="badge badge-present">✓ Hadir</span>
                    @else
                        <span class="badge badge-absent">- Belum</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>Tidak ada data jadwal untuk periode ini</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem PT. Jaringan Fiberone Indonesia</p>
        <p>© {{ date('Y') }} PT. Jaringan Fiberone Indonesia. All rights reserved.</p>
    </div>
</body>
</html>
