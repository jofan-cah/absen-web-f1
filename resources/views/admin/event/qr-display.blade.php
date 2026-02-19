<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR - {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-red-600 via-rose-600 to-red-800 min-h-screen flex flex-col items-center justify-center select-none"
    ondblclick="toggleFullscreen()">

    <div class="text-center text-white px-6 max-w-lg w-full">

        {{-- Logo / Brand --}}
        <div class="mb-4">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-white/20">
                <div class="w-5 h-5 bg-white/20 rounded flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase">Absensi F1</span>
            </div>
        </div>

        {{-- Event Title --}}
        <h1 class="text-2xl sm:text-3xl font-bold mb-1">{{ $event->title }}</h1>
        <p class="text-white/70 text-sm mb-1">
            {{ $event->location ? $event->location . ' • ' : '' }}{{ $event->start_date->format('d M Y') }}
        </p>

        {{-- QR Code Canvas --}}
        <div class="relative inline-block my-6">
            <div class="bg-white rounded-2xl p-4 shadow-2xl inline-block" id="qr-wrapper">
                <canvas id="qr-canvas" width="280" height="280"></canvas>
                {{-- Loading overlay --}}
                <div id="qr-loading" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-2xl" style="display:none!important">
                    <div class="animate-spin w-8 h-8 border-4 border-red-500 border-t-transparent rounded-full"></div>
                </div>
            </div>
            {{-- Expired overlay --}}
            <div id="qr-expired" class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-2xl hidden">
                <div class="text-white text-center">
                    <div class="text-4xl mb-1">⏱</div>
                    <div class="text-sm font-bold">QR Expired</div>
                    <div class="text-xs opacity-80">Memperbarui...</div>
                </div>
            </div>
        </div>

        {{-- Countdown --}}
        <div class="mb-4">
            <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm px-5 py-2 rounded-full">
                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse" id="dot-indicator"></div>
                <span class="text-sm font-semibold" id="countdown-text">Refresh dalam <span id="countdown">{{ $event->qr_refresh_seconds }}</span>s</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="flex justify-center gap-6 text-white/90">
            <div class="text-center">
                <div class="text-3xl font-bold" id="stat-peserta">0</div>
                <div class="text-xs text-white/60 mt-0.5">Peserta Hadir</div>
            </div>
            @if($event->type === 'partnership')
            <div class="w-px bg-white/20"></div>
            <div class="text-center">
                <div class="text-3xl font-bold" id="stat-orang">0</div>
                <div class="text-xs text-white/60 mt-0.5">Total Orang</div>
            </div>
            @endif
        </div>

        {{-- Instruction --}}
        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 text-xs text-white/80">
            Scan QR ini menggunakan Aplikasi Mobile F1 untuk mencatat kehadiran
        </div>

        {{-- Fullscreen hint --}}
        <p class="mt-3 text-xs text-white/40">Double-click untuk fullscreen</p>
    </div>

    <script>
        const EVENT_ID   = @json($event->event_id);
        const REFRESH    = {{ $event->qr_refresh_seconds }};
        const OTP_URL    = @json(route('admin.event.qr-otp', $event->event_id));
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let countdown      = REFRESH;
        let countdownTimer = null;
        let statsTimer     = null;

        // ─── Render QR ──────────────────────────────────────────────────────────
        async function fetchAndRenderQr() {
            try {
                document.getElementById('qr-expired').classList.add('hidden');

                const res  = await fetch(OTP_URL, {
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok) { showExpired(); return; }

                const qrData = JSON.stringify({
                    event_id: data.event_id,
                    otp:      data.otp,
                    ts:       data.ts,
                });

                const canvas = document.getElementById('qr-canvas');
                QRCode.toCanvas(canvas, qrData, {
                    width:            280,
                    margin:           1,
                    color: { dark: '#1a1a1a', light: '#ffffff' },
                    errorCorrectionLevel: 'M',
                }, (err) => { if (err) console.error(err); });

                // Update stats
                document.getElementById('stat-peserta').textContent = data.total_attendees ?? 0;
                const statOrang = document.getElementById('stat-orang');
                if (statOrang) statOrang.textContent = data.total_orang ?? 0;

                // Restart countdown
                countdown = REFRESH;

            } catch (e) {
                console.error('QR fetch error:', e);
            }
        }

        function showExpired() {
            document.getElementById('qr-expired').classList.remove('hidden');
        }

        // ─── Countdown ──────────────────────────────────────────────────────────
        function startCountdown() {
            clearInterval(countdownTimer);
            countdownTimer = setInterval(() => {
                countdown--;
                document.getElementById('countdown').textContent = countdown;

                if (countdown <= 3) {
                    document.getElementById('dot-indicator').classList.remove('bg-green-400');
                    document.getElementById('dot-indicator').classList.add('bg-yellow-400');
                }

                if (countdown <= 0) {
                    document.getElementById('dot-indicator').classList.remove('bg-yellow-400');
                    document.getElementById('dot-indicator').classList.add('bg-green-400');
                    fetchAndRenderQr();
                    countdown = REFRESH;
                }
            }, 1000);
        }

        // ─── Stats Polling ────────────────────────────────────────────────────
        function startStatsPolling() {
            statsTimer = setInterval(async () => {
                try {
                    const res  = await fetch(OTP_URL, {
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    document.getElementById('stat-peserta').textContent = data.total_attendees ?? 0;
                    const statOrang = document.getElementById('stat-orang');
                    if (statOrang) statOrang.textContent = data.total_orang ?? 0;
                } catch (e) {}
            }, 5000);
        }

        // ─── Fullscreen ──────────────────────────────────────────────────────
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        }

        // ─── Init ─────────────────────────────────────────────────────────────
        fetchAndRenderQr();
        startCountdown();
        startStatsPolling();
    </script>
</body>
</html>
