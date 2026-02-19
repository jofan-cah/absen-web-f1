@extends('admin.layouts.app')

@section('title', 'Scanner – ' . $event->title)

@section('content')
<div class="p-4 sm:p-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.event.show', $event) }}"
            class="p-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-gray-900">QR Scanner</h1>
            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-green-50 border border-green-200 rounded-full">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-xs font-semibold text-green-700">Live</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Left: Scanner --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Flash Messages --}}
            <div id="flash-success" class="hidden p-3.5 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-green-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span id="flash-success-text" class="text-sm font-semibold text-green-800"></span>
            </div>
            <div id="flash-error" class="hidden p-3.5 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <span id="flash-error-text" class="text-sm font-semibold text-red-800"></span>
            </div>

            {{-- Webcam Scanner --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-sm font-bold text-gray-700">Kamera QR</h2>
                    <button id="btn-start-cam" onclick="startCamera()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded-lg hover:bg-green-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        </svg>
                        Aktifkan Kamera
                    </button>
                </div>
                <div id="reader" class="w-full bg-gray-100 aspect-video flex items-center justify-center">
                    <div class="text-center p-6">
                        <div class="w-16 h-16 rounded-2xl bg-gray-200 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Klik tombol "Aktifkan Kamera" untuk mulai scan</p>
                    </div>
                </div>
            </div>

            {{-- Manual NIP Input --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="text-sm font-bold text-gray-700 mb-3.5">Input NIP / Barcode Manual</h2>
                <div class="space-y-3">
                    <input type="text" id="nip-input" placeholder="Masukkan NIP atau scan ID card..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition font-mono">

                    @if($event->type === 'partnership')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Jumlah Orang</label>
                                <input type="number" id="jumlah-orang" min="1" value="1"
                                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Keterangan</label>
                                <input type="text" id="keterangan" placeholder="Opsional..."
                                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 transition">
                            </div>
                        </div>
                    @endif

                    <button type="button" onclick="submitScan(document.getElementById('nip-input').value)"
                        class="w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-bold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-sm">
                        Catat Kehadiran
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Recent Scans --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col" style="max-height:600px">
                <div class="px-4 py-3.5 border-b border-gray-100 bg-gray-50/50 flex-shrink-0 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-700">Kehadiran Terbaru</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Update otomatis setiap scan</p>
                    </div>
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-100" id="total-scan">{{ $recentAttendances->count() }}</span>
                </div>
                <div class="overflow-y-auto flex-1" id="attendance-list">
                    @forelse($recentAttendances as $att)
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-sm text-gray-900 truncate">{{ $att->karyawan?->full_name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $att->karyawan?->department?->name }} • {{ $att->check_in_at->format('H:i:s') }}</div>
                        </div>
                        @if($event->type === 'partnership')
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-100">{{ $att->jumlah_orang }}x</span>
                        @endif
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center h-40 gap-2">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Belum ada scan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const SCAN_URL      = @json(route('admin.event.process-scan', $event->event_id));
    const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').content;
    const IS_PARTNERSHIP = @json($event->type === 'partnership');

    let html5QrCode = null;
    let lastScan    = '';

    function showFlash(type, message) {
        const el  = document.getElementById('flash-' + type);
        const txt = document.getElementById('flash-' + type + '-text');
        txt.textContent = message;
        el.classList.remove('hidden');

        // Beep sound
        try {
            const ctx  = new (window.AudioContext || window.webkitAudioContext)();
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = type === 'success' ? 880 : 220;
            osc.start();
            setTimeout(() => { osc.stop(); ctx.close(); }, 150);
        } catch (e) {}

        setTimeout(() => el.classList.add('hidden'), 4000);
    }

    function prependToList(karyawan, time, jumlah) {
        const list = document.getElementById('attendance-list');

        // Remove empty state if present
        const emptyDiv = list.querySelector('.flex.flex-col.items-center');
        if (emptyDiv) emptyDiv.parentElement.remove();

        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 px-4 py-3 border-b border-gray-50 bg-green-50 transition-colors';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm text-gray-900 truncate">${karyawan.full_name}</div>
                <div class="text-xs text-gray-400 mt-0.5">${karyawan.department ?? '-'} • ${time}</div>
            </div>
            ${IS_PARTNERSHIP && jumlah ? `<span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-100">${jumlah}x</span>` : ''}
        `;
        list.insertBefore(div, list.firstChild);
        setTimeout(() => div.classList.remove('bg-green-50'), 2500);

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
        const btn = document.getElementById('btn-start-cam');
        html5QrCode = new Html5Qrcode('reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                let nip = decodedText.trim();
                try {
                    const obj = JSON.parse(decodedText);
                    if (obj.nip) nip = obj.nip;
                } catch (e) {}
                submitScan(nip);
            },
            () => {}
        ).then(() => {
            btn.innerHTML = `<span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Kamera Aktif`;
            btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-500 text-white text-xs font-semibold rounded-lg cursor-default';
        }).catch(err => {
            showFlash('error', 'Gagal mengaktifkan kamera: ' + err);
        });
    }

    // Focus and enter key
    const nipInput = document.getElementById('nip-input');
    nipInput.focus();
    nipInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitScan(e.target.value);
        }
    });
</script>
@endsection
