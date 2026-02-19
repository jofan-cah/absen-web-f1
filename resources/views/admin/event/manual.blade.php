@extends('admin.layouts.app')

@section('title', 'Manual Attendance – ' . $event->title)

@section('content')
<div class="p-4 sm:p-6 max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.event.show', $event) }}"
            class="p-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Catat Manual</h1>
            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $event->title }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if($karyawans->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm text-amber-800 font-medium">Semua karyawan sudah tercatat hadir di event ini.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700">Pilih Karyawan</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $karyawans->count() }} karyawan tersedia</p>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('admin.event.process-manual', $event) }}" class="space-y-4">
                    @csrf

                    <div>
                        <div class="relative mb-2">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="search-karyawan" placeholder="Cari nama atau NIP karyawan..."
                                class="w-full pl-10 pr-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                                oninput="filterKaryawan(this.value)">
                        </div>

                        <select name="karyawan_id" id="karyawan-select" required size="8"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
                            style="height: 200px">
                            @foreach($karyawans as $kar)
                                <option value="{{ $kar->karyawan_id }}"
                                    data-name="{{ strtolower($kar->full_name) }}"
                                    data-nip="{{ $kar->nip }}">
                                    {{ $kar->full_name }} — {{ $kar->nip }}
                                    @if($kar->department) ({{ $kar->department->name }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($event->type === 'partnership')
                        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jumlah Orang</label>
                                <input type="number" name="jumlah_orang" min="1" value="{{ old('jumlah_orang', 1) }}"
                                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan</label>
                                <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional"
                                    class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-1">
                        <a href="{{ route('admin.event.show', $event) }}"
                            class="flex-1 py-3 text-center bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                            Batal
                        </a>
                        <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold rounded-xl shadow-sm hover:from-blue-600 hover:to-indigo-700 transition-all">
                            Catat Kehadiran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
function filterKaryawan(q) {
    q = q.toLowerCase().trim();
    const opts = document.querySelectorAll('#karyawan-select option');
    opts.forEach(opt => {
        const match = !q
            || opt.dataset.name.includes(q)
            || opt.dataset.nip.includes(q);
        opt.style.display = match ? '' : 'none';
    });
}
</script>
@endsection
