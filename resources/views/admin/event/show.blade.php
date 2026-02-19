@extends('admin.layouts.app')

@section('title', $event->title)

@section('content')
<div class="p-4 sm:p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
        <div class="flex items-start gap-3 flex-1 min-w-0">
            <a href="{{ route('admin.event.index') }}"
                class="p-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-0.5">
                    <h1 class="text-xl font-bold text-gray-900 truncate">{{ $event->title }}</h1>
                    @php
                        $sc = match($event->status) {
                            'draft'     => 'bg-gray-100 text-gray-600 border-gray-200',
                            'active'    => 'bg-green-50 text-green-700 border-green-200',
                            'ongoing'   => 'bg-blue-50 text-blue-700 border-blue-200',
                            'completed' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                            default     => 'bg-gray-100 text-gray-600 border-gray-200',
                        };
                    @endphp
                    <span class="px-2.5 py-0.5 {{ $sc }} text-xs font-semibold rounded-full border flex-shrink-0">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500">
                    {{ $event->event_id }} •
                    {{ $event->type === 'partnership' ? 'Partnership' : 'Internal' }}
                    @if($event->department) • {{ $event->department->name }} @endif
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($event->isActive())
                <a href="{{ route('admin.event.qr', $event) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:from-red-600 hover:to-rose-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    QR Display
                </a>
                <a href="{{ route('admin.event.scan', $event) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:from-green-600 hover:to-emerald-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    </svg>
                    Scanner
                </a>
                <a href="{{ route('admin.event.manual', $event) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-semibold rounded-xl shadow-sm hover:from-blue-600 hover:to-indigo-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Manual
                </a>
            @endif
            <a href="{{ route('admin.event.pdf-preview', $event) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                PDF
            </a>
            <a href="{{ route('admin.event.edit', $event) }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold rounded-xl hover:bg-amber-100 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left Panel --}}
        <div class="space-y-4">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-4 text-white shadow-sm">
                    <div class="text-3xl font-bold">{{ $totalAttendees }}</div>
                    <div class="text-xs text-blue-100 mt-0.5 font-medium">Peserta Hadir</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-4 text-white shadow-sm">
                    <div class="text-3xl font-bold">{{ $totalOrang }}</div>
                    <div class="text-xs text-purple-100 mt-0.5 font-medium">Total Orang</div>
                </div>
            </div>

            {{-- Detail Event --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-4">Detail Event</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">Lokasi</dt>
                        <dd class="font-medium text-gray-800 text-xs">{{ $event->location ?? '—' }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">Tanggal</dt>
                        <dd class="font-medium text-gray-800 text-xs">
                            {{ $event->start_date->format('d M Y') }}
                            @if($event->end_date && $event->end_date->ne($event->start_date))
                                – {{ $event->end_date->format('d M Y') }}
                            @endif
                        </dd>
                    </div>
                    @if($event->start_time)
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">Waktu</dt>
                        <dd class="font-medium text-gray-800 text-xs">
                            {{ $event->start_time }}{{ $event->end_time ? ' – ' . $event->end_time : '' }}
                        </dd>
                    </div>
                    @endif
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">Department</dt>
                        <dd class="font-medium text-gray-800 text-xs">{{ $event->department?->name ?? 'Semua' }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">QR Refresh</dt>
                        <dd class="font-medium text-gray-800 text-xs">{{ $event->qr_refresh_seconds }}s</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">Multi Scan</dt>
                        <dd class="text-xs">
                            @if($event->allow_multi_scan)
                                <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded-full font-semibold border border-green-100">Ya</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full font-semibold">Tidak</span>
                            @endif
                        </dd>
                    </div>
                    @if($event->max_participants)
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">Maks Peserta</dt>
                        <dd class="font-medium text-gray-800 text-xs">{{ $event->max_participants }}</dd>
                    </div>
                    @endif
                    @if($event->latitude)
                    <div class="flex gap-2">
                        <dt class="text-gray-400 w-24 flex-shrink-0 text-xs font-medium pt-0.5">GPS</dt>
                        <dd class="font-medium text-gray-700 text-xs font-mono">{{ $event->latitude }}, {{ $event->longitude }} (r:{{ $event->radius }}m)</dd>
                    </div>
                    @endif
                </dl>
                @if($event->description)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $event->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Ubah Status --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-3">Ubah Status</h2>
                <form method="POST" action="{{ route('admin.event.update-status', $event) }}">
                    @csrf
                    <div class="space-y-2">
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-white transition">
                            @foreach(['draft' => 'Draft', 'active' => 'Active', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                                <option value="{{ $val }}" {{ $event->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full py-2.5 bg-gray-800 text-white text-sm font-semibold rounded-xl hover:bg-gray-900 transition-colors">
                            Ubah Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Panel: Daftar Hadir --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-700">Daftar Hadir</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $attendances->count() }} entri kehadiran</p>
                    </div>
                    @if($attendances->count() > 0)
                    <a href="{{ route('admin.event.pdf-preview', $event) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ekspor PDF
                    </a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Karyawan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 hidden sm:table-cell">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Metode</th>
                                @if($event->type === 'partnership')
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Orang</th>
                                @endif
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($attendances as $i => $att)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-400 font-medium">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 text-sm">{{ $att->karyawan?->full_name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $att->karyawan?->nip }} • {{ $att->karyawan?->department?->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 hidden sm:table-cell">
                                    {{ $att->check_in_at?->format('d/m/Y') }}<br>
                                    <span class="font-semibold text-gray-800">{{ $att->check_in_at?->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($att->method === 'qr_scan')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 text-xs font-semibold rounded-full border border-green-100">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4"/>
                                            </svg>
                                            QR
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">Manual</span>
                                    @endif
                                </td>
                                @if($event->type === 'partnership')
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm font-bold text-purple-700">{{ $att->jumlah_orang }}</span>
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-right">
                                    <form method="POST"
                                        action="{{ route('admin.event.remove-attendance', [$event, $att->attendance_id]) }}"
                                        onsubmit="return confirm('Hapus data kehadiran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $event->type === 'partnership' ? 6 : 5 }}" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500">Belum ada peserta hadir</p>
                                        @if($event->isActive())
                                            <div class="flex gap-2 mt-1">
                                                <a href="{{ route('admin.event.scan', $event) }}" class="text-xs text-green-600 font-semibold hover:underline">Gunakan Scanner</a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('admin.event.manual', $event) }}" class="text-xs text-blue-600 font-semibold hover:underline">Catat Manual</a>
                                            </div>
                                        @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
