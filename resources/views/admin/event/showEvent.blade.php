@extends('admin.layouts.app')

@section('title', $event->title)

@section('content')
<div class="p-4 sm:p-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start gap-4 mb-6">
        <div class="flex items-center gap-3 flex-1">
            <a href="{{ route('admin.event.index') }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-gray-900">{{ $event->title }}</h1>
                    @php
                        $statusColor = match($event->status) {
                            'draft'     => 'bg-gray-100 text-gray-600',
                            'active'    => 'bg-green-100 text-green-700',
                            'ongoing'   => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-indigo-100 text-indigo-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="px-2 py-0.5 {{ $statusColor }} text-xs font-semibold rounded-full">{{ ucfirst($event->status) }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">{{ $event->event_id }} • {{ $event->type === 'partnership' ? 'Partnership' : 'Internal' }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(in_array($event->status, ['active','ongoing']))
                <a href="{{ route('admin.event.qr', $event->event_id) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs font-semibold rounded-xl shadow hover:from-red-600 hover:to-rose-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    QR Display
                </a>
                <a href="{{ route('admin.event.scan-page', $event->event_id) }}"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold rounded-xl shadow hover:from-green-600 hover:to-emerald-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Scanner
                </a>
                <a href="{{ route('admin.event.manual-page', $event->event_id) }}"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-semibold rounded-xl shadow hover:from-blue-600 hover:to-indigo-700 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Manual
                </a>
            @endif
            <a href="{{ route('admin.event.preview-pdf', $event->event_id) }}" target="_blank"
                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                PDF
            </a>
            <a href="{{ route('admin.event.edit', $event->event_id) }}"
                class="inline-flex items-center gap-2 px-3 py-2 bg-amber-100 text-amber-700 text-xs font-semibold rounded-xl hover:bg-amber-200 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detail Event --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-3">Detail Event</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Lokasi</dt>
                        <dd class="font-medium text-gray-800 text-right">{{ $event->location ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal</dt>
                        <dd class="font-medium text-gray-800">{{ $event->start_date->format('d M Y') }}{{ $event->end_date ? ' - ' . $event->end_date->format('d M Y') : '' }}</dd>
                    </div>
                    @if($event->start_time)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Waktu</dt>
                        <dd class="font-medium text-gray-800">{{ $event->start_time }}{{ $event->end_time ? ' - ' . $event->end_time : '' }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Department</dt>
                        <dd class="font-medium text-gray-800">{{ $event->department?->name ?? 'Semua' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">QR Refresh</dt>
                        <dd class="font-medium text-gray-800">{{ $event->qr_refresh_seconds }}s</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Multi Scan</dt>
                        <dd class="font-medium text-gray-800">{{ $event->allow_multi_scan ? 'Ya' : 'Tidak' }}</dd>
                    </div>
                    @if($event->max_participants)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Maks Peserta</dt>
                        <dd class="font-medium text-gray-800">{{ $event->max_participants }}</dd>
                    </div>
                    @endif
                </dl>
                @if($event->description)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">{{ $event->description }}</p>
                    </div>
                @endif
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-100">
                    <div class="text-2xl font-bold text-blue-700">{{ $attendances->count() }}</div>
                    <div class="text-xs text-blue-600 mt-0.5">Total Peserta</div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-4 border border-purple-100">
                    <div class="text-2xl font-bold text-purple-700">{{ $totalOrang }}</div>
                    <div class="text-xs text-purple-600 mt-0.5">Total Orang</div>
                </div>
            </div>

            {{-- Ubah Status --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-3">Ubah Status</h2>
                <form method="POST" action="{{ route('admin.event.update-status', $event->event_id) }}">
                    @csrf
                    <div class="flex gap-2">
                        <select name="status" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                            @foreach(['draft','active','ongoing','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $event->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900 transition-colors">Ubah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar Hadir --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-700">Daftar Hadir</h2>
                    <span class="text-xs text-gray-400">{{ $attendances->count() }} orang</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Karyawan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Metode</th>
                                @if($event->type === 'partnership')
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Jml Orang</th>
                                @endif
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($attendances as $i => $att)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-400">{{ $i+1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $att->karyawan?->full_name }}</div>
                                    <div class="text-xs text-gray-400">{{ $att->karyawan?->nip }} • {{ $att->karyawan?->department?->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $att->check_in_at->format('d/m H:i') }}</td>
                                <td class="px-4 py-3">
                                    @if($att->method === 'qr_scan')
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">QR Scan</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">Manual</span>
                                    @endif
                                </td>
                                @if($event->type === 'partnership')
                                    <td class="px-4 py-3 text-gray-700 font-semibold">{{ $att->jumlah_orang }}</td>
                                @endif
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.event.remove-attendance', [$event->event_id, $att->attendance_id]) }}"
                                        onsubmit="return confirm('Hapus data kehadiran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $event->type === 'partnership' ? 6 : 5 }}" class="px-4 py-10 text-center text-gray-400 text-sm">
                                    Belum ada peserta yang hadir
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
