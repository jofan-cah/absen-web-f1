<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Tiket - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-sm w-full">
        @if($attendance)
            {{-- Valid Ticket --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 px-6 py-8 text-white text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold">Tiket Valid</h1>
                    <p class="text-white/80 text-sm mt-1">Kehadiran terverifikasi</p>
                </div>

                <div class="p-6 space-y-4">
                    <div class="bg-gray-50 rounded-2xl p-4 space-y-3">
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Event</div>
                            <div class="font-bold text-gray-900">{{ $attendance->event?->title }}</div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Peserta</div>
                            <div class="font-semibold text-gray-900">{{ $attendance->karyawan?->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $attendance->karyawan?->nip }} • {{ $attendance->karyawan?->department?->name }}</div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Waktu Hadir</div>
                                <div class="font-semibold text-gray-900 text-sm">{{ $attendance->check_in_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $attendance->check_in_at->format('H:i:s') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Metode</div>
                                <div class="font-semibold text-gray-900 text-sm">
                                    {{ $attendance->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="text-xs text-gray-300 font-mono break-all">{{ $attendance->ticket_token }}</div>
                    </div>
                </div>
            </div>
        @else
            {{-- Invalid Ticket --}}
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-br from-red-500 to-rose-600 px-6 py-8 text-white text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold">Tiket Tidak Valid</h1>
                    <p class="text-white/80 text-sm mt-1">Token tidak ditemukan atau tidak valid</p>
                </div>
                <div class="p-6 text-center text-sm text-gray-500">
                    Tiket ini tidak terdaftar dalam sistem atau sudah tidak berlaku.
                </div>
            </div>
        @endif

        <div class="text-center mt-4 text-xs text-gray-400">{{ config('app.name') }} — Sistem Absensi F1</div>
    </div>
</body>
</html>
