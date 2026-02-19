<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran – {{ $event->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            padding: 24px;
        }

        /* Header */
        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding-bottom: 16px;
            border-bottom: 2px solid #e11d48;
            margin-bottom: 18px;
        }
        .header-brand { display: flex; flex-direction: column; }
        .brand-name {
            font-size: 18px;
            font-weight: 700;
            color: #e11d48;
            letter-spacing: -0.3px;
        }
        .brand-sub {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .header-meta {
            text-align: right;
            font-size: 9px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* Title Section */
        .title-section {
            background: linear-gradient(135deg, #fff1f2 0%, #fef2f2 100%);
            border: 1px solid #fecdd3;
            border-left: 4px solid #e11d48;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }
        .event-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 9.5px;
            color: #475569;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .meta-label { color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 8.5px; letter-spacing: 0.5px; }

        /* Status badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-completed { background: #e0e7ff; color: #3730a3; }
        .badge-active    { background: #dcfce7; color: #166534; }
        .badge-draft     { background: #f1f5f9; color: #475569; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-ongoing   { background: #dbeafe; color: #1e40af; }
        .badge-internal     { background: #dbeafe; color: #1e40af; }
        .badge-partnership  { background: #f3e8ff; color: #6b21a8; }

        /* Stats Row */
        .stats-row {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }
        .stat-card {
            flex: 1;
            text-align: center;
            padding: 12px 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }
        .stat-label {
            font-size: 8.5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
            font-weight: 600;
        }
        .stat-card.blue  { border-color: #bfdbfe; }
        .stat-card.blue .stat-value { color: #2563eb; }
        .stat-card.purple { border-color: #ddd6fe; }
        .stat-card.purple .stat-value { color: #7c3aed; }
        .stat-card.green  { border-color: #bbf7d0; }
        .stat-card.green .stat-value { color: #16a34a; }

        /* Table */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #1e293b;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 8.5px;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td {
            padding: 7px 10px;
            font-size: 9.5px;
            color: #374151;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tbody td.center { text-align: center; }
        .name { font-weight: 600; color: #1e293b; }
        .sub  { font-size: 8.5px; color: #94a3b8; margin-top: 1px; }
        .method-qr     { background: #dcfce7; color: #166534; padding: 1px 6px; border-radius: 10px; font-size: 8px; font-weight: 700; }
        .method-manual { background: #f1f5f9; color: #475569; padding: 1px 6px; border-radius: 10px; font-size: 8px; font-weight: 700; }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-left { font-size: 8px; color: #94a3b8; line-height: 1.8; }
        .footer-sign { text-align: center; }
        .sign-line  { width: 130px; border-bottom: 1px solid #374151; margin-bottom: 4px; margin: 40px auto 4px; }
        .sign-title { font-size: 8.5px; color: #374151; font-weight: 600; }
        .sign-name  { font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-brand">
            <div class="brand-name">F1 HR System</div>
            <div class="brand-sub">Laporan Kehadiran Event</div>
        </div>
        <div class="header-meta">
            Dicetak: {{ now()->format('d M Y, H:i') }} WIB<br>
            Oleh: {{ auth()->user()->name ?? 'System' }}<br>
            Dok: {{ $event->event_id }}
        </div>
    </div>

    {{-- Event Info --}}
    <div class="title-section">
        <div class="event-title">{{ $event->title }}</div>
        <div class="event-meta">
            <div class="meta-item">
                <span class="meta-label">Status</span>
                <span class="badge badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Tipe</span>
                <span class="badge badge-{{ $event->type }}">{{ ucfirst($event->type) }}</span>
            </div>
            @if($event->location)
            <div class="meta-item">
                <span class="meta-label">Lokasi</span>
                <span>{{ $event->location }}</span>
            </div>
            @endif
            <div class="meta-item">
                <span class="meta-label">Tanggal</span>
                <span>{{ $event->start_date->format('d M Y') }}{{ $event->end_date && $event->end_date->ne($event->start_date) ? ' – ' . $event->end_date->format('d M Y') : '' }}</span>
            </div>
            @if($event->start_time)
            <div class="meta-item">
                <span class="meta-label">Waktu</span>
                <span>{{ $event->start_time }}{{ $event->end_time ? ' – ' . $event->end_time : '' }}</span>
            </div>
            @endif
            @if($event->department)
            <div class="meta-item">
                <span class="meta-label">Dept</span>
                <span>{{ $event->department->name }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card blue">
            <div class="stat-value">{{ $attendances->count() }}</div>
            <div class="stat-label">Total Peserta</div>
        </div>
        @if($event->type === 'partnership')
        <div class="stat-card purple">
            <div class="stat-value">{{ $totalOrang }}</div>
            <div class="stat-label">Total Orang</div>
        </div>
        @endif
        <div class="stat-card green">
            <div class="stat-value">{{ $attendances->where('method', 'qr_scan')->count() }}</div>
            <div class="stat-label">QR Scan</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $attendances->where('method', 'manual')->count() }}</div>
            <div class="stat-label">Manual</div>
        </div>
        @if($event->max_participants)
        <div class="stat-card">
            <div class="stat-value">{{ $event->max_participants }}</div>
            <div class="stat-label">Kuota</div>
        </div>
        @endif
    </div>

    {{-- Attendance Table --}}
    <div class="section-title">Daftar Kehadiran</div>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Nama Karyawan</th>
                <th>NIP</th>
                <th>Department</th>
                <th>Waktu</th>
                <th class="center">Metode</th>
                @if($event->type === 'partnership')
                    <th class="center">Jml Orang</th>
                @endif
                @if($event->type === 'partnership')
                    <th>Keterangan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $i => $att)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>
                    <div class="name">{{ $att->karyawan?->full_name ?? '—' }}</div>
                </td>
                <td>{{ $att->karyawan?->nip ?? '—' }}</td>
                <td>{{ $att->karyawan?->department?->name ?? '—' }}</td>
                <td>{{ $att->check_in_at?->format('d/m/Y H:i') ?? '—' }}</td>
                <td class="center">
                    @if($att->method === 'qr_scan')
                        <span class="method-qr">QR Scan</span>
                    @else
                        <span class="method-manual">Manual</span>
                    @endif
                </td>
                @if($event->type === 'partnership')
                    <td class="center">{{ $att->jumlah_orang }}</td>
                @endif
                @if($event->type === 'partnership')
                    <td>{{ $att->keterangan ?? '—' }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $event->type === 'partnership' ? 8 : 6 }}" style="text-align:center;padding:20px;color:#94a3b8;">
                    Belum ada data kehadiran
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">
            * Dokumen ini digenerate secara otomatis oleh sistem F1 HR.<br>
            * Data kehadiran valid per tanggal cetak.
        </div>
        <div class="footer-sign">
            <div class="sign-line"></div>
            <div class="sign-title">Mengetahui</div>
            <div class="sign-name">Admin HR</div>
        </div>
    </div>

</body>
</html>
