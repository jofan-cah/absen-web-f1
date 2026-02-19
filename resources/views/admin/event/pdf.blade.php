<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Event - {{ $event->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #1a1a1a; }
        .header { background: linear-gradient(135deg, #dc2626, #e11d48); color: white; padding: 20px 24px; }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: 0.85; }
        .meta { padding: 16px 24px; display: flex; gap: 40px; background: #f9f9f9; border-bottom: 1px solid #e5e5e5; }
        .meta-item label { font-size: 9px; font-weight: bold; color: #888; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .meta-item span { font-size: 11px; font-weight: 600; color: #111; }
        .stats { display: flex; gap: 0; border-bottom: 1px solid #e5e5e5; }
        .stat-box { flex: 1; padding: 12px 24px; text-align: center; border-right: 1px solid #e5e5e5; }
        .stat-box:last-child { border-right: none; }
        .stat-box .num { font-size: 24px; font-weight: bold; color: #dc2626; }
        .stat-box .lbl { font-size: 9px; color: #888; text-transform: uppercase; margin-top: 2px; }
        .content { padding: 16px 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { background: #1a1a1a; color: white; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f5f5f5; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 10px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-qr { background: #dcfce7; color: #16a34a; }
        .badge-manual { background: #f3f4f6; color: #6b7280; }
        .footer { margin-top: 20px; padding: 12px 24px; border-top: 1px solid #e5e5e5; text-align: center; color: #aaa; font-size: 9px; }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $event->title }}</h1>
    <p>Laporan Kehadiran Event • Dicetak: {{ now()->format('d M Y H:i') }}</p>
</div>

<div class="meta">
    <div class="meta-item"><label>ID Event</label><span>{{ $event->event_id }}</span></div>
    <div class="meta-item"><label>Tipe</label><span>{{ ucfirst($event->type) }}</span></div>
    <div class="meta-item"><label>Tanggal</label><span>{{ $event->start_date->format('d M Y') }}{{ $event->end_date ? ' - ' . $event->end_date->format('d M Y') : '' }}</span></div>
    <div class="meta-item"><label>Lokasi</label><span>{{ $event->location ?? '-' }}</span></div>
    <div class="meta-item"><label>Status</label><span>{{ ucfirst($event->status) }}</span></div>
</div>

<div class="stats">
    <div class="stat-box"><div class="num">{{ $attendances->count() }}</div><div class="lbl">Total Peserta</div></div>
    <div class="stat-box"><div class="num">{{ $totalOrang }}</div><div class="lbl">Total Orang</div></div>
    <div class="stat-box"><div class="num">{{ $attendances->where('method','qr_scan')->count() }}</div><div class="lbl">Via QR Scan</div></div>
    <div class="stat-box"><div class="num">{{ $attendances->where('method','manual')->count() }}</div><div class="lbl">Via Manual</div></div>
</div>

<div class="content">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Karyawan</th>
                <th>NIP</th>
                <th>Department</th>
                <th>Waktu Hadir</th>
                <th>Metode</th>
                @if($event->type === 'partnership') <th>Jml Orang</th> @endif
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $att->karyawan?->full_name }}</strong></td>
                <td>{{ $att->karyawan?->nip }}</td>
                <td>{{ $att->karyawan?->department?->name }}</td>
                <td>{{ $att->check_in_at->format('d/m/Y H:i') }}</td>
                <td><span class="badge {{ $att->method === 'qr_scan' ? 'badge-qr' : 'badge-manual' }}">{{ $att->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}</span></td>
                @if($event->type === 'partnership') <td>{{ $att->jumlah_orang }}</td> @endif
                <td>{{ $att->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ $event->type === 'partnership' ? 8 : 7 }}" style="text-align:center; color:#aaa; padding:20px">Belum ada data kehadiran</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">
    Sistem Absensi F1 • {{ config('app.name') }} • Laporan otomatis dibuat oleh sistem
</div>

</body>
</html>
