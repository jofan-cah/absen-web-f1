@extends('admin.layouts.app')

@section('title', 'Detail Lembur')
@section('breadcrumb', 'Detail Lembur')
@section('page_title', 'Detail Lembur #' . $lembur->lembur_id)

@section('page_actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.lembur.index') }}"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        @if ($lembur->status == 'submitted')
            <button onclick="approveLembur()"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $lembur->koordinator_status == 'pending' ? 'Approve (Bypass Koordinator)' : 'Approve (Admin Final)' }}
            </button>
            <button onclick="rejectLembur()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Reject (Admin)
            </button>
        @endif
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Status Badge with Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $statusConfig = [
                        'draft' => [
                            'bg' => 'bg-gray-100',
                            'text' => 'text-gray-700',
                            'icon' =>
                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                        'submitted' => [
                            'bg' => 'bg-yellow-100',
                            'text' => 'text-yellow-700',
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'approved' => [
                            'bg' => 'bg-green-100',
                            'text' => 'text-green-700',
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'rejected' => [
                            'bg' => 'bg-red-100',
                            'text' => 'text-red-700',
                            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'processed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'M5 13l4 4L19 7'],
                    ];
                    $status = $statusConfig[$lembur->status] ?? [
                        'bg' => 'bg-gray-100',
                        'text' => 'text-gray-700',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ];
                @endphp

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status Lembur</h3>
                        <div class="flex items-center gap-3">
                            <span
                                class="px-4 py-2 inline-flex items-center gap-2 text-sm font-semibold rounded-lg {{ $status['bg'] }} {{ $status['text'] }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $status['icon'] }}" />
                                </svg>
                                {{ ucfirst($lembur->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Timeline Progress -->
                <div class="relative pt-4">
                    <div class="flex items-center justify-between">
                        <!-- Step 1: Submitted -->
                        <div class="flex flex-col items-center flex-1">
                            <div
                                class="w-10 h-10 rounded-full {{ $lembur->submitted_at ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center text-white mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-700">Submitted</p>
                            <p class="text-xs text-gray-500">
                                {{ $lembur->submitted_at ? $lembur->submitted_at->format('d/m H:i') : '-' }}</p>
                        </div>

                        <!-- Line -->
                        <div
                            class="flex-1 h-1 {{ $lembur->koordinator_approved_at ? 'bg-green-500' : 'bg-gray-300' }} mx-2">
                        </div>

                        <!-- Step 2: Koordinator Review -->
                        <div class="flex flex-col items-center flex-1">
                            <div
                                class="w-10 h-10 rounded-full {{ $lembur->koordinator_approved_at ? 'bg-green-500' : ($lembur->koordinator_rejected_at ? 'bg-red-500' : ($lembur->koordinator_status == 'pending' ? 'bg-yellow-500 animate-pulse' : 'bg-gray-300')) }} flex items-center justify-center text-white mb-2">
                                @if ($lembur->koordinator_approved_at)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($lembur->koordinator_rejected_at)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                @endif
                            </div>
                            <p class="text-xs font-medium text-gray-700">Koordinator</p>
                            <p class="text-xs text-gray-500">
                                @if ($lembur->koordinator_approved_at)
                                    {{ $lembur->koordinator_approved_at->format('d/m H:i') }}
                                @elseif($lembur->koordinator_rejected_at)
                                    Rejected
                                @else
                                    Pending
                                @endif
                            </p>
                        </div>

                        <!-- Line -->
                        <div class="flex-1 h-1 {{ $lembur->approved_at ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>

                        <!-- Step 3: Admin Approval -->
                        <div class="flex flex-col items-center flex-1">
                            <div
                                class="w-10 h-10 rounded-full {{ $lembur->approved_at ? 'bg-green-500' : ($lembur->rejected_at && $lembur->koordinator_status == 'approved' ? 'bg-red-500' : 'bg-gray-300') }} flex items-center justify-center text-white mb-2">
                                @if ($lembur->approved_at)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif($lembur->rejected_at && $lembur->koordinator_status == 'approved')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </div>
                            <p class="text-xs font-medium text-gray-700">Admin Final</p>
                            <p class="text-xs text-gray-500">
                                {{ $lembur->approved_at ? $lembur->approved_at->format('d/m H:i') : ($lembur->rejected_at && $lembur->koordinator_status == 'approved' ? 'Rejected' : 'Pending') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Lembur -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Detail Lembur
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Tanggal Lembur</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->tanggal_lembur->format('d F Y') }}</p>
                        <p class="text-sm text-gray-400">{{ $lembur->tanggal_lembur->format('l') }}</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Jam Mulai</p>
                        <p class="text-base font-medium text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }}</p>
                        <p class="text-xs text-gray-400">Otomatis dari shift end</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Jam Selesai</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $lembur->jam_selesai ? substr($lembur->jam_selesai, 0, 5) : '-' }}</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Total Jam</p>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-2xl font-bold text-blue-600">{{ number_format($lembur->total_jam, 1) }}</span>
                            <span class="text-sm text-gray-500">jam</span>
                        </div>
                    </div>

                    {{-- Tracking Info --}}
                    @if ($lembur->started_at || $lembur->completed_at)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500 mb-2">⏱️ Tracking Waktu</p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 space-y-1">
                                @if ($lembur->started_at)
                                    <p class="text-sm text-blue-700">
                                        <strong>Started:</strong> {{ $lembur->started_at->format('d/m/Y H:i:s') }}
                                    </p>
                                @endif
                                @if ($lembur->completed_at)
                                    <p class="text-sm text-blue-700">
                                        <strong>Completed:</strong> {{ $lembur->completed_at->format('d/m/Y H:i:s') }}
                                    </p>
                                    @if ($lembur->started_at)
                                        <p class="text-xs text-blue-600">
                                            Durasi aktual: {{ $lembur->started_at->diffInMinutes($lembur->completed_at) }}
                                            menit
                                            ({{ number_format($lembur->started_at->diffInMinutes($lembur->completed_at) / 60, 2) }}
                                            jam)
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="col-span-2">
                        <p class="text-sm text-gray-500 mb-2">Estimasi Tunjangan</p>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                            @php
                                $quantity = $lembur->calculateQuantity();
                                $amountPerUnit = $lembur->calculateAmountPerUnit();
                                $totalAmount = $lembur->calculateTunjanganAmount();
                            @endphp
                            <div class="flex items-center justify-between">
                                <div>
                                    <p>{{ $quantity }}x Insentif Kehadiran</p>
                                    <p>Rp {{ number_format($amountPerUnit, 0, ',', '.') }} × {{ $quantity }}</p>
                                    <p class="font-bold">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                                </div>
                                <p class="text-xl font-bold text-blue-700">Rp
                                    {{ number_format($totalAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <p class="text-xs text-blue-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $lembur->total_jam >= 4 ? 'Lembur ≥ 4 jam mendapat 2x insentif kehadiran' : 'Lembur < 4 jam mendapat 1x insentif kehadiran' }}
                                </p>
                                @if (!$lembur->tunjanganKaryawan)
                                    <p class="text-xs text-blue-500 mt-1">
                                        💰 Tunjangan akan dibuat setelah Admin approve
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($lembur->deskripsi_pekerjaan)
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm text-gray-500 mb-2">Deskripsi Pekerjaan</p>
                        <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-lg whitespace-pre-wrap">
                            {{ $lembur->deskripsi_pekerjaan }}</p>
                    </div>
                @endif

                @if ($lembur->bukti_foto)
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm text-gray-500 mb-2">Bukti Foto</p>
                        <img src="{{ $lembur->bukti_foto_url }}" alt="Bukti Lembur"
                            class="w-full max-w-md rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity shadow-md"
                            onclick="openImageModal('{{ $lembur->bukti_foto_url }}')">
                    </div>
                @endif
            </div>

            @php
                $breakdown = $lembur->getTunjanganBreakdown();
            @endphp

            <div>
                <p>Staff Status: {{ $breakdown['staff_status'] }}</p>
                <p>{{ $breakdown['quantity'] }}x Insentif Kehadiran</p>
                <p>Rp {{ number_format($breakdown['amount_per_unit'], 0, ',', '.') }}</p>
                <p class="font-bold">Total: Rp {{ number_format($breakdown['total_amount'], 0, ',', '.') }}</p>

                @if ($breakdown['source'] === 'database')
                    <p class="text-xs text-green-600">
                        ✅ Dari database: {{ $breakdown['tunjangan_detail']['staff_status'] }}
                        (Rp {{ number_format($breakdown['tunjangan_detail']['amount'], 0, ',', '.') }})
                    </p>
                @else
                    <p class="text-xs text-orange-500">
                        ⚠️ Menggunakan nilai default
                    </p>
                @endif
            </div>

            <!-- Approval History -->
            @if (
                $lembur->koordinator_approved_at ||
                    $lembur->koordinator_rejected_at ||
                    $lembur->approved_at ||
                    $lembur->rejected_at)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Riwayat Approval
                    </h3>

                    <div class="space-y-4">

                        {{-- APPROVAL KOORDINATOR (Level 1) --}}
                        @if ($lembur->koordinator_approved_at || $lembur->koordinator_rejected_at)
                            <div
                                class="border-l-4 {{ $lembur->koordinator_status == 'approved' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }} p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 {{ $lembur->koordinator_status == 'approved' ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                @if ($lembur->koordinator_status == 'approved')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                @endif
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4
                                                class="text-sm font-bold {{ $lembur->koordinator_status == 'approved' ? 'text-green-900' : 'text-red-900' }}">
                                                {{ $lembur->koordinator_status == 'approved' ? '✅ Disetujui' : '❌ Ditolak' }}
                                                oleh Koordinator
                                            </h4>
                                            <span
                                                class="text-xs px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full font-medium">Level
                                                1</span>
                                        </div>
                                        <div
                                            class="mt-2 text-sm {{ $lembur->koordinator_status == 'approved' ? 'text-green-700' : 'text-red-700' }} space-y-1">
                                            <p><strong>Koordinator:</strong>
                                                @if ($lembur->coordinator)
                                                    {{ $lembur->coordinator->full_name ?? ($lembur->coordinator->user->name ?? '-') }}
                                                @elseif($lembur->koordinator_notes == 'Auto-approved by admin')
                                                    Admin (Bypass)
                                                @else
                                                    -
                                                @endif
                                            </p>
                                            <p><strong>Tanggal:</strong>
                                                {{ $lembur->koordinator_approved_at ? $lembur->koordinator_approved_at->format('d/m/Y H:i') : ($lembur->koordinator_rejected_at ? $lembur->koordinator_rejected_at->format('d/m/Y H:i') : '-') }}
                                            </p>
                                            @if ($lembur->koordinator_notes)
                                                <div class="mt-2">
                                                    <p class="font-medium">Catatan:</p>
                                                    <p class="mt-1 bg-white/70 p-2 rounded">
                                                        {{ $lembur->koordinator_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- APPROVAL ADMIN (Level 2) --}}
                        @if ($lembur->approved_at || ($lembur->rejected_at && $lembur->koordinator_status == 'approved'))
                            <div
                                class="border-l-4 {{ $lembur->status == 'approved' ? 'border-blue-500 bg-blue-50' : 'border-red-500 bg-red-50' }} p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 {{ $lembur->status == 'approved' ? 'bg-blue-500' : 'bg-red-500' }} rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                @if ($lembur->status == 'approved')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                @endif
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4
                                                class="text-sm font-bold {{ $lembur->status == 'approved' ? 'text-blue-900' : 'text-red-900' }}">
                                                {{ $lembur->status == 'approved' ? '✅ Disetujui' : '❌ Ditolak' }} oleh
                                                Admin
                                            </h4>
                                            <span
                                                class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full font-medium">Level
                                                2 - Final</span>
                                        </div>
                                        <div
                                            class="mt-2 text-sm {{ $lembur->status == 'approved' ? 'text-blue-700' : 'text-red-700' }} space-y-1">
                                            <p><strong>Admin:</strong>
                                                {{ $lembur->approvedBy->name ?? ($lembur->rejectedBy->name ?? '-') }}</p>
                                            <p><strong>Tanggal:</strong>
                                                {{ $lembur->approved_at ? $lembur->approved_at->format('d/m/Y H:i') : ($lembur->rejected_at ? $lembur->rejected_at->format('d/m/Y H:i') : '-') }}
                                            </p>
                                            @if ($lembur->approval_notes)
                                                <div class="mt-2">
                                                    <p class="font-medium">Catatan:</p>
                                                    <p class="mt-1 bg-white/70 p-2 rounded">{{ $lembur->approval_notes }}
                                                    </p>
                                                </div>
                                            @endif
                                            @if ($lembur->rejection_reason)
                                                <div class="mt-2">
                                                    <p class="font-medium">Alasan Penolakan:</p>
                                                    <p class="mt-1 bg-white/70 p-2 rounded">
                                                        {{ $lembur->rejection_reason }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            <!-- Tunjangan Info -->
            @if ($lembur->tunjanganKaryawan)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tunjangan Lembur
                    </h3>

                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <div>
                                <p class="text-sm text-gray-600">Total Tunjangan</p>
                                <p class="text-2xl font-bold text-green-600">Rp
                                    {{ number_format($lembur->tunjanganKaryawan->total_amount, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('admin.tunjangan-karyawan.show', $lembur->tunjanganKaryawan->tunjangan_karyawan_id) }}"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat Detail
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">
                            <p>Status: <span
                                    class="font-medium text-gray-900">{{ ucfirst($lembur->tunjanganKaryawan->status) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Karyawan Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Karyawan</h3>

                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div
                            class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            {{ substr($lembur->karyawan->full_name, 0, 1) }}
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-base font-semibold text-gray-900">{{ $lembur->karyawan->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $lembur->karyawan->nip }}</p>
                    </div>
                </div>

                <div class="space-y-3 border-t pt-4">
                    <div>
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->karyawan->department->name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Posisi</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->karyawan->position }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Staff</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $lembur->karyawan->staff_status)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->karyawan->user->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Absen Info -->
            @if ($lembur->absen)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Absensi</h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="text-base font-medium text-gray-900">{{ $lembur->absen->date->format('d/m/Y') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-gray-500">Clock In</p>
                                <p class="text-base font-medium text-gray-900">{{ $lembur->absen->clock_in ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Clock Out</p>
                                <p class="text-base font-medium text-gray-900">{{ $lembur->absen->clock_out ?? '-' }}</p>
                            </div>
                        </div>
                        @if ($lembur->absen->jadwal)
                            <div>
                                <p class="text-sm text-gray-500">Shift</p>
                                <p class="text-base font-medium text-gray-900">
                                    {{ $lembur->absen->jadwal->shift->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $lembur->absen->jadwal->shift->start_time ?? '-' }} -
                                    {{ $lembur->absen->jadwal->shift->end_time ?? '-' }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Jam Kerja</p>
                            <p class="text-base font-medium text-gray-900">{{ $lembur->absen->work_hours ?? 0 }} jam</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline Compact -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Dibuat</p>
                            <p class="text-xs text-gray-500">{{ $lembur->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if ($lembur->started_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Started</p>
                                <p class="text-xs text-gray-500">{{ $lembur->started_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->completed_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Completed</p>
                                <p class="text-xs text-gray-500">{{ $lembur->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->submitted_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Diajukan</p>
                                <p class="text-xs text-gray-500">{{ $lembur->submitted_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">via {{ $lembur->submitted_via ?? 'web' }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->koordinator_approved_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Approved Koordinator</p>
                                <p class="text-xs text-gray-500">
                                    {{ $lembur->koordinator_approved_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">oleh
                                    {{ $lembur->coordinator->full_name ?? 'Admin (Bypass)' }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->koordinator_rejected_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Rejected Koordinator</p>
                                <p class="text-xs text-gray-500">
                                    {{ $lembur->koordinator_rejected_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->approved_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Final Approved</p>
                                <p class="text-xs text-gray-500">{{ $lembur->approved_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">oleh {{ $lembur->approvedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->rejected_at && $lembur->koordinator_status == 'approved')
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Rejected Admin</p>
                                <p class="text-xs text-gray-500">{{ $lembur->rejected_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">oleh {{ $lembur->rejectedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @if ($lembur->status == 'submitted')
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">⚡ Quick Actions</h3>
                    <div class="space-y-2">
                        <button onclick="approveLembur()"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $lembur->koordinator_status == 'pending' ? 'Approve (Bypass)' : 'Approve Final' }}
                        </button>
                        <button onclick="rejectLembur()"
                            class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Reject
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function approveLembur() {
            const koorStatus = '{{ $lembur->koordinator_status }}';
            const notes = prompt('💬 Catatan persetujuan (opsional):');
            if (notes === null) return;

            let confirmMessage = '';
            if (koorStatus === 'pending') {
                confirmMessage = '⚡ BYPASS APPROVAL\n\n' +
                    'Anda akan approve lembur ini LANGSUNG (melewati Koordinator).\n\n' +
                    '✅ Koordinator status akan di-set sebagai approved otomatis\n' +
                    '✅ Tunjangan akan langsung dibuat\n\n' +
                    'Lanjutkan?';
            } else {
                confirmMessage = '✅ FINAL APPROVAL\n\n' +
                    'Koordinator sudah approve lembur ini.\n' +
                    'Anda akan melakukan approval final dan generate tunjangan.\n\n' +
                    'Lanjutkan?';
            }

            if (!confirm(confirmMessage)) return;

            showLoading();

            fetch(`/admin/lembur/{{ $lembur->lembur_id }}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        alert('✅ ' + data.message);
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('❌ Terjadi kesalahan');
                    console.error('Error:', error);
                });
        }

        function rejectLembur() {
            const reason = prompt('❌ Alasan penolakan (wajib):');
            if (!reason || reason.trim() === '') {
                alert('⚠️ Alasan penolakan harus diisi!');
                return;
            }

            if (!confirm(
                    '❌ Reject lembur ini?\n\n⚠️ Admin bisa reject walau koordinator sudah approve!\n\nYakin ingin reject?'))
                return;

            showLoading();

            fetch(`/admin/lembur/{{ $lembur->lembur_id }}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        rejection_reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        alert('✅ ' + data.message);
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('❌ Terjadi kesalahan');
                    console.error('Error:', error);
                });
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function showLoading() {
            const loadingHTML = `
        <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-blue-600 mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-700 font-medium">Memproses...</p>
            </div>
        </div>
    `;
            document.body.insertAdjacentHTML('beforeend', loadingHTML);
        }

        function hideLoading() {
            const loading = document.getElementById('loadingOverlay');
            if (loading) loading.remove();
        }
    </script>
@endpush
