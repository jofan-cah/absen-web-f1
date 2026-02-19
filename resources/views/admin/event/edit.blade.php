@extends('admin.layouts.app')

@section('title', 'Edit Event – ' . $event->title)

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.event.show', $event) }}"
            class="p-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Edit Event</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $event->event_id }} • {{ $event->title }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.event.update', $event) }}" class="space-y-5">
        @csrf @method('PUT')

        {{-- 1. Informasi Event --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-red-500 text-white flex items-center justify-center text-xs font-bold">1</span>
                    Informasi Event
                </h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Event <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe Event <span class="text-red-500">*</span></label>
                        <select name="type" id="eventType" required onchange="togglePartnershipFields()"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-white transition">
                            <option value="internal" {{ old('type', $event->type) === 'internal' ? 'selected' : '' }}>Internal (Rapat, Bukber, Gathering)</option>
                            <option value="partnership" {{ old('type', $event->type) === 'partnership' ? 'selected' : '' }}>Partnership / Mitra</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location', $event->location) }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Maks. Peserta</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" min="1"
                            placeholder="Tidak terbatas"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition resize-none">{{ old('description', $event->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. Waktu --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-500 text-white flex items-center justify-center text-xs font-bold">2</span>
                    Waktu Event
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}" required
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Pengaturan QR --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-green-500 text-white flex items-center justify-center text-xs font-bold">3</span>
                    Pengaturan QR
                </h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Interval Refresh QR (detik)</label>
                    <input type="number" name="qr_refresh_seconds" value="{{ old('qr_refresh_seconds', $event->qr_refresh_seconds) }}" min="10" max="300"
                        class="w-full sm:w-48 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    <p class="text-xs text-gray-400 mt-1">Antara 10 – 300 detik</p>
                </div>

                <div id="internalInfo" class="flex items-start gap-2.5 p-3.5 bg-blue-50 border border-blue-100 rounded-xl">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-blue-700">Event <strong>Internal</strong> tidak mengizinkan scan berulang — setiap karyawan hanya bisa hadir 1x.</p>
                </div>

                <div id="partnershipScanOpt" class="hidden">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" name="allow_multi_scan" value="1" id="allowMultiScan"
                            {{ old('allow_multi_scan', $event->allow_multi_scan) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-300">
                        <div>
                            <div class="text-sm font-semibold text-gray-700">Boleh scan berkali-kali</div>
                            <div class="text-xs text-gray-400">Setiap kunjungan pelanggan akan direkam terpisah</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- 4. Pengaturan Mitra (Partnership only) --}}
        <div id="partnershipFields" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-purple-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-purple-500 text-white flex items-center justify-center text-xs font-bold">4</span>
                    Pengaturan Mitra
                    <span class="text-xs font-normal text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Partnership only</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5 ml-7">Validasi lokasi GPS saat karyawan scan</p>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Koordinat GPS</label>
                        <input type="text" id="gpsInput"
                            value="{{ $event->latitude && $event->longitude ? $event->latitude . ', ' . $event->longitude : '' }}"
                            placeholder="Paste dari Google Maps: -7.707, 110.604"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300 transition font-mono"
                            onpaste="setTimeout(parseGps, 50)" oninput="parseGps()">
                        <input type="hidden" name="latitude" id="latInput" value="{{ old('latitude', $event->latitude) }}">
                        <input type="hidden" name="longitude" id="lngInput" value="{{ old('longitude', $event->longitude) }}">
                        <div id="gpsPreview" class="hidden mt-1.5 text-xs font-mono px-2 py-1 rounded-lg bg-green-50 border border-green-100 text-green-700"></div>
                        <div id="gpsError" class="hidden mt-1.5 text-xs text-red-600"></div>
                        <p class="text-xs text-gray-400 mt-1">Copy koordinat dari Google Maps, paste di sini</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Radius (meter)</label>
                        <input type="number" name="radius" value="{{ old('radius', $event->radius ?? 200) }}" min="50"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300 transition">
                        <p class="text-xs text-gray-400 mt-1">Jarak maks dari lokasi (min 50m)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Scope Peserta --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-orange-500 text-white flex items-center justify-center text-xs font-bold" id="scopeStep">4</span>
                        Scope Peserta
                    </h2>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Opsional</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5 ml-7">Kosongkan untuk semua karyawan</p>
            </div>
            <div class="p-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Department</label>
                    <select name="department_id" class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-white transition">
                        <option value="">— Semua Department —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ old('department_id', $event->department_id) == $dept->department_id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end pb-2">
            <a href="{{ route('admin.event.show', $event) }}"
                class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:from-red-600 hover:to-rose-700 transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePartnershipFields() {
    const type     = document.getElementById('eventType').value;
    const gpsBlock = document.getElementById('partnershipFields');
    const scanOpt  = document.getElementById('partnershipScanOpt');
    const infoBox  = document.getElementById('internalInfo');
    const stepNum  = document.getElementById('scopeStep');

    if (type === 'partnership') {
        gpsBlock.classList.remove('hidden');
        scanOpt.classList.remove('hidden');
        infoBox.classList.add('hidden');
        stepNum.textContent = '5';
    } else {
        gpsBlock.classList.add('hidden');
        scanOpt.classList.add('hidden');
        infoBox.classList.remove('hidden');
        document.getElementById('allowMultiScan').checked = false;
        stepNum.textContent = '4';
    }
}

function parseGps() {
    const raw     = document.getElementById('gpsInput').value.trim();
    const preview = document.getElementById('gpsPreview');
    const errEl   = document.getElementById('gpsError');
    const latIn   = document.getElementById('latInput');
    const lngIn   = document.getElementById('lngInput');

    latIn.value = '';
    lngIn.value = '';
    preview.classList.add('hidden');
    errEl.classList.add('hidden');

    if (!raw) return;

    const parts = raw.replace(/[()]/g, '').split(/[,\s]+/).filter(p => p !== '');
    if (parts.length >= 2) {
        const lat = parseFloat(parts[0]);
        const lng = parseFloat(parts[1]);

        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            latIn.value = lat;
            lngIn.value = lng;
            preview.textContent = '✓ Lat: ' + lat + ' | Long: ' + lng;
            preview.classList.remove('hidden');
            errEl.classList.add('hidden');
        } else {
            errEl.textContent = 'Format koordinat tidak valid';
            errEl.classList.remove('hidden');
        }
    }
}

togglePartnershipFields();
if (document.getElementById('gpsInput').value) parseGps();
</script>
@endpush
@endsection
