@extends('admin.layouts.app')

@section('title', 'Detail OnCall')

@section('content')
<div class="compact-content">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.oncall.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Detail OnCall</h1>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap tentang OnCall {{ $lembur->karyawan->full_name }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                @if($lembur->status === 'waiting_checkin')
                    <a href="{{ route('admin.oncall.edit', $lembur->lembur_id) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>

                    <form action="{{ route('admin.oncall.destroy', $lembur->lembur_id) }}" method="POST" class="inline-block"
                          onsubmit="return confirm('Yakin ingin membatalkan OnCall ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Status OnCall</h3>
                    @php
                        $statusConfig = [
                            'waiting_checkin' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Waiting Check-in', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'in_progress' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'In Progress', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                            'submitted' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Submitted', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Rejected', 'icon' => 'M6 18L18 6M6 6l12 12'],
                        ];
                        $config = $statusConfig[$lembur->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($lembur->status), 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                        </svg>
                        {{ $config['label'] }}
                    </span>
                </div>

                <!-- Timeline -->
                <div class="space-y-4">
                    @foreach($timeline as $item)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            @if($item['completed'])
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 {{ !$item['completed'] ? 'opacity-50' : '' }}">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</h4>
                                @if($item['datetime'])
                                    <span class="text-xs text-gray-500">{{ $item['datetime']->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">{{ $item['user'] }}</p>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="ml-5 w-0.5 h-4 {{ $item['completed'] ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                    @endif
                    @endforeach
                </div>
            </div>

            <!-- Informasi OnCall -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi OnCall</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- ID OnCall -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">ID OnCall</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $lembur->lembur_id }}</p>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal</label>
                        <p class="text-sm text-gray-900">{{ $lembur->tanggal_lembur->format('d/m/Y (l)') }}</p>
                    </div>

                    <!-- Jam Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jam Mulai (Estimasi)</label>
                        <p class="text-sm text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }}</p>
                    </div>

                    <!-- Total Jam -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Total Jam</label>
                        @if($lembur->total_jam && $lembur->total_jam > 0)
                            <p class="text-sm text-gray-900 font-semibold">{{ number_format($lembur->total_jam, 1) }} jam</p>
                        @else
                            <p class="text-sm text-gray-400 italic">Belum selesai</p>
                        @endif
                    </div>

                    <!-- Di-assign oleh -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Di-assign oleh</label>
                        <p class="text-sm text-gray-900">{{ $lembur->createdBy->name ?? '-' }}</p>
                    </div>

                    <!-- Submitted Via -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Submitted Via</label>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ strtoupper($lembur->submitted_via) }}
                        </span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Deskripsi Pekerjaan</label>
                    <p class="text-sm text-gray-900 leading-relaxed">{{ $lembur->deskripsi_pekerjaan }}</p>
                </div>

                <!-- Bukti Foto -->
                @if($lembur->bukti_foto)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Bukti Foto</label>
                    <div class="mt-2">
                        <img src="{{ $lembur->bukti_foto_url }}" alt="Bukti OnCall"
                             class="rounded-lg border border-gray-200 max-w-md cursor-pointer hover:opacity-90 transition-opacity"
                             onclick="window.open(this.src, '_blank')">
                        <p class="text-xs text-gray-500 mt-2">Klik untuk memperbesar</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tracking Info (jika sudah check in) -->
            @if($lembur->started_at || $lembur->completed_at)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tracking Waktu</h3>
                <div class="space-y-3">
                    @if($lembur->started_at)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Check In</span>
                        </div>
                        <span class="text-sm text-gray-600">{{ $lembur->started_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif

                    @if($lembur->completed_at)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Check Out</span>
                        </div>
                        <span class="text-sm text-gray-600">{{ $lembur->completed_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif

                    @if($lembur->started_at && $lembur->completed_at)
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Durasi</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $lembur->started_at->diffInHours($lembur->completed_at) }} jam
                            {{ $lembur->started_at->diffInMinutes($lembur->completed_at) % 60 }} menit
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Karyawan Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Karyawan</h3>
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold text-2xl shadow-md mr-4">
                        {{ substr($lembur->karyawan->full_name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $lembur->karyawan->full_name }}</p>
                        <p class="text-sm text-gray-600">{{ $lembur->karyawan->nip }}</p>
                        <p class="text-sm text-gray-600">{{ $lembur->karyawan->department->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Jadwal Info -->
            @if($lembur->jadwalOnCall)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Jadwal OnCall</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">ID Jadwal</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $lembur->jadwalOnCall->jadwal_id }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Shift</label>
                        <p class="text-sm text-gray-900">{{ $lembur->jadwalOnCall->shift->nama ?? 'OnCall' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($lembur->jadwalOnCall->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tunjangan Info (jika sudah approved) -->
            @if($lembur->status === 'approved' && $lembur->tunjanganKaryawan)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Tunjangan</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nominal</label>
                        <p class="text-lg font-bold text-green-600">Rp {{ number_format($lembur->tunjanganKaryawan->nominal, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.oncall.index') }}"
                       class="flex items-center justify-center w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Lihat Semua OnCall
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Timeline connector line */
.timeline-line {
    position: relative;
}

.timeline-line::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    bottom: -20px;
    width: 2px;
    background: #e5e7eb;
}

/* Image zoom on hover */
img:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease-in-out;
}

/* Status badge animation */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endpush
