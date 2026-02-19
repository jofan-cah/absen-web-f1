@extends('admin.layouts.app')

@section('title', 'Daftar Event')

@section('content')
<div class="p-4 sm:p-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Event</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola event dan absensi QR Code</p>
        </div>
        <a href="{{ route('admin.event.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-xl shadow hover:from-red-600 hover:to-rose-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Event
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul event..."
            class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            <option value="">Semua Status</option>
            @foreach(['draft','active','ongoing','completed','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            <option value="">Semua Tipe</option>
            <option value="internal" {{ request('type') === 'internal' ? 'selected' : '' }}>Internal</option>
            <option value="partnership" {{ request('type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
        </select>
        <button type="submit" class="px-4 py-1.5 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">Filter</button>
        <a href="{{ route('admin.event.index') }}" class="px-4 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peserta</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $event->title }}</div>
                            <div class="text-xs text-gray-400">{{ $event->event_id }} • {{ $event->location }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($event->type === 'partnership')
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Partnership</span>
                            @else
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Internal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $event->start_date->format('d M Y') }}
                            @if($event->end_date && $event->end_date->ne($event->start_date))
                                <span class="text-gray-400">→ {{ $event->end_date->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
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
                        </td>
                        <td class="px-4 py-3 text-gray-700 font-semibold">{{ $event->attendances_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.event.show', $event->event_id) }}"
                                    class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors" title="Detail">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(in_array($event->status, ['active','ongoing']))
                                    <a href="{{ route('admin.event.qr', $event->event_id) }}" target="_blank"
                                        class="p-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-colors" title="QR Display">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.event.scan-page', $event->event_id) }}"
                                        class="p-1.5 rounded-lg bg-green-100 hover:bg-green-200 text-green-600 transition-colors" title="Scanner">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('admin.event.edit', $event->event_id) }}"
                                    class="p-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.event.destroy', $event->event_id) }}"
                                    onsubmit="return confirm('Hapus event ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition-colors" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">
                            Belum ada event. <a href="{{ route('admin.event.create') }}" class="text-red-500 font-medium">Buat event baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
