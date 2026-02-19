@extends('admin.layouts.app')

@section('title', 'Scanner - ' . $event->title)

@section('content')
<div class="p-4 sm:p-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.event.show', $event->event_id) }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">QR Scanner</h1>
            <p class="text-xs text-gray-500">{{ $event->title }} • Scan ID Card karyawan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Scanner Area --}}
        <div class="space-y-4">
            {{-- Flash Message --}}
            <div id="flash-success" class="hidden px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="flash-success-text"></span>
            </div>
            <div id="flash-error" class="hidden px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span id="flash-error-text"></span>
            </div>

            {{-- Webcam Scanner --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-700">Webcam Scanner</h2>
                    <button id="btn-start-cam" onclick="startCamera()"
                        class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-200 transition-colors">
                        Aktifkan Kamera
                    </button>
                </div>
                <div id="reader" class="w-full rounded-xl overflow-hidden bg-gray-100 aspect-video flex items-center justify-center">
                    <div class="text-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        </svg>
                        Klik "Aktifkan Kamera" untuk mulai scan
                    </div>
                </div>
            </div>

            {{-- Manual NIP Input --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-3">Input NIP Manual</h2>
                <form id="scan-form" class="space-y-3">
                    @csrf
                    <input type="text" id="nip-input" placeholder="Masukkan NIP karyawan..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">

                    @if($event->type === 'partnership')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1 block">Jumlah Orang</label>
                                <input type="number" id="jumlah-orang" min="1" value="1"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1 block">Keterangan</label>
                                <input type="text" id="keterangan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                            </div>
                        </div>
                    @endif

                    <button type="button" onclick="submitScan(document.getElementById('nip-input').value)"
                        class="w-full py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow">
                        Catat Kehadiran
                    </button>
                </form>
            </div>
        </div>

        {{-- Recent Scans --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="max-height: 600px">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-sm font-bold text-gray-700">Kehadiran Terbaru</h2>
                <span class="text-xs font-bold text-gray-400" id="total-scan">{{ $recentAttendances->count() }}</span>
            </div>
            <div class="overflow-y-auto flex-1" id="attendance-list">
                @forelse($recentAttendances as $att)
                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-gray-900 truncate">{{ $att->karyawan?->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $att->karyawan?->department?->name }} • {{ $att->check_in_at->format('H:i:s') }}</div>
                    </div>
                    @if($event->type === 'partnership')
                        <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">{{ $att->jumlah_orang }}x</span>
                    @endif
                </div>
                @empty
                <div class="flex items-center justify-center h-32 text-gray-400 text-sm">Belum ada scan</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const SCAN_URL   = @json(route('admin.event.process-scan', $event->event_id));
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const IS_PARTNERSHIP = @json($event->type === 'partnership');

    let html5QrCode = null;
    let lastScan    = '';
    let scanning    = false;

    function showFlash(type, message) {
        const el  = document.getElementById('flash-' + type);
        const txt = document.getElementById('flash-' + type + '-text');
        txt.textContent = message;
        el.classList.remove('hidden');

        // Play beep
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = type === 'success' ? 880 : 220;
        osc.start();
        setTimeout(() => { osc.stop(); ctx.close(); }, 200);

        setTimeout(() => el.classList.add('hidden'), 4000);
    }

    function prependToList(karyawan, time, jumlah) {
        const list = document.getElementById('attendance-list');
        const div  = document.createElement('div');
        div.className = 'flex items-center gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 bg-green-50 transition-colors';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm text-gray-900 truncate">${karyawan.full_name}</div>
                <div class="text-xs text-gray-400">${karyawan.department ?? '-'} • ${time}</div>
            </div>
            ${IS_PARTNERSHIP && jumlah ? `<span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">${jumlah}x</span>` : ''}
        `;
        list.insertBefore(div, list.firstChild);
        setTimeout(() => div.classList.remove('bg-green-50'), 2000);

        const total = document.getElementById('total-scan');
        total.textContent = parseInt(total.textContent || 0) + 1;
    }

    async function submitScan(nip) {
        nip = nip.trim();
        if (!nip || nip === lastScan) return;
        lastScan = nip;

        const body = { nip };
        if (IS_PARTNERSHIP) {
            body.jumlah_orang = document.getElementById('jumlah-orang')?.value ?? 1;
            body.keterangan   = document.getElementById('keterangan')?.value ?? '';
        }

        try {
            const res  = await fetch(SCAN_URL, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept':       'application/json',
                },
                body: JSON.stringify(body),
            });
            const data = await res.json();

            if (data.success) {
                showFlash('success', data.message);
                prependToList(data.karyawan, data.check_in_at, data.jumlah_orang);
                document.getElementById('nip-input').value = '';
            } else {
                showFlash('error', data.message);
            }
        } catch (e) {
            showFlash('error', 'Terjadi kesalahan jaringan');
        }

        setTimeout(() => { lastScan = ''; }, 3000);
    }

    function startCamera() {
        html5QrCode = new Html5Qrcode('reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                let nip = decodedText.trim();
                // Try JSON format: {"nip": "..."}
                try {
                    const obj = JSON.parse(decodedText);
                    if (obj.nip) nip = obj.nip;
                } catch (e) {}
                submitScan(nip);
            },
            () => {}
        ).catch(err => {
            showFlash('error', 'Gagal mengaktifkan kamera: ' + err);
        });

        document.getElementById('btn-start-cam').textContent = 'Kamera Aktif';
        document.getElementById('btn-start-cam').className = 'px-3 py-1.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded-lg cursor-default';
    }

    // Focus NIP input on load
    document.getElementById('nip-input').focus();
    // Enter key submit
    document.getElementById('nip-input').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') submitScan(e.target.value);
    });
</script>
@endsection
