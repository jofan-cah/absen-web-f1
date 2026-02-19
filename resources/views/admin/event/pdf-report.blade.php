<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Event - {{ $event->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
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

        .header p {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 5px;
        }

        .info-section {
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #475569;
            width: 130px;
        }

        .info-value {
            color: #1e293b;
        }

        .summary-boxes {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-boxes td {
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .summary-number {
            font-size: 22px;
            font-weight: bold;
            display: block;
        }

        .summary-label {
            font-size: 9px;
            color: #64748b;
            display: block;
            margin-top: 3px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #3b82f6;
        }

        .section-title.absent {
            border-bottom-color: #ef4444;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th {
            background: #334155;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }

        table.data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }

        table.data-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-qr {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-manual {
            background: #f3f4f6;
            color: #374151;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }

        .page-break {
            page-break-before: always;
        }

        .text-green { color: #16a34a; }
        .text-red   { color: #dc2626; }
        .text-blue  { color: #2563eb; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>PT FIBERONE</h1>
        <h2>Laporan Kehadiran Event</h2>
        <p>Digenerate pada {{ $generatedAt }}</p>
    </div>

    {{-- Info Event --}}
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Nama Event</td>
                <td class="info-value">: {{ $event->title }}</td>
                <td class="info-label">Tipe</td>
                <td class="info-value">: {{ $event->type === 'internal' ? 'Internal' : 'Partnership' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal</td>
                <td class="info-value">: {{ $event->start_date->format('d M Y') }}{{ $event->end_date && $event->end_date->ne($event->start_date) ? ' s/d ' . $event->end_date->format('d M Y') : '' }}</td>
                <td class="info-label">Lokasi</td>
                <td class="info-value">: {{ $event->location ?? '-' }}</td>
            </tr>
            @if($event->start_time)
            <tr>
                <td class="info-label">Jam</td>
                <td class="info-value">: {{ $event->start_time }}{{ $event->end_time ? ' - ' . $event->end_time : '' }} WIB</td>
                <td class="info-label">Status</td>
                <td class="info-value">: {{ ucfirst($event->status) }}</td>
            </tr>
            @endif
            @if($event->department)
            <tr>
                <td class="info-label">Department</td>
                <td class="info-value" colspan="3">: {{ $event->department->name }}</td>
            </tr>
            @endif
            @if($event->description)
            <tr>
                <td class="info-label">Deskripsi</td>
                <td class="info-value" colspan="3">: {{ $event->description }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Summary --}}
    <table class="summary-boxes">
        <tr>
            <td>
                <span class="summary-number text-blue">{{ $totalKaryawan }}</span>
                <span class="summary-label">Total Karyawan</span>
            </td>
            <td>
                <span class="summary-number text-green">{{ $totalHadir }}</span>
                <span class="summary-label">Hadir</span>
            </td>
            <td>
                <span class="summary-number text-red">{{ $totalAbsen }}</span>
                <span class="summary-label">Tidak Hadir</span>
            </td>
            <td>
                <span class="summary-number">{{ $totalKaryawan > 0 ? round(($totalHadir / $totalKaryawan) * 100, 1) : 0 }}%</span>
                <span class="summary-label">Persentase Kehadiran</span>
            </td>
        </tr>
    </table>

    {{-- Daftar Hadir --}}
    <div class="section-title">Daftar Hadir ({{ $totalHadir }} karyawan)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Department</th>
                <th>Waktu Check-in</th>
                @if($event->type === 'partnership')
                <th>Jml Orang</th>
                <th>Keterangan</th>
                @endif
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $att->karyawan?->full_name ?? '—' }}</strong></td>
                <td>{{ $att->karyawan?->nip ?? '—' }}</td>
                <td>{{ $att->karyawan?->department?->name ?? '-' }}</td>
                <td>{{ $att->check_in_at?->format('d M Y H:i') ?? '—' }}</td>
                @if($event->type === 'partnership')
                <td style="text-align: center;">{{ $att->jumlah_orang }}</td>
                <td>{{ $att->keterangan ?? '-' }}</td>
                @endif
                <td>
                    <span class="badge {{ $att->method === 'qr_scan' ? 'badge-qr' : 'badge-manual' }}">
                        {{ $att->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $event->type === 'partnership' ? 8 : 6 }}" style="text-align: center; padding: 15px; color: #94a3b8;">
                    Belum ada peserta yang hadir
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Partnership total orang --}}
    @if($event->type === 'partnership' && $totalHadir > 0)
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Total Karyawan Hadir</td>
                <td class="info-value">: {{ $totalHadir }} orang</td>
            </tr>
            <tr>
                <td class="info-label">Total Orang (incl. keluarga)</td>
                <td class="info-value">: {{ $totalOrang }} orang</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Daftar Tidak Hadir --}}
    @if($absentKaryawans->count() > 0)
    <div class="section-title absent">Daftar Tidak Hadir ({{ $totalAbsen }} karyawan)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absentKaryawans as $i => $k)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $k->full_name }}</strong></td>
                <td>{{ $k->nip }}</td>
                <td>{{ $k->department?->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>PT FIBERONE &mdash; Laporan digenerate otomatis oleh sistem pada {{ $generatedAt }}</p>
    </div>

</body>
</html>
