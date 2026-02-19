@extends('admin.layouts.app')

@section('title', 'Manual Attendance - ' . $event->title)

@section('content')
<div class="p-4 sm:p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.event.show', $event->event_id) }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Manual Attendance</h1>
            <p class="text-xs text-gray-500">{{ $event->title }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="POST" action="{{ route('admin.event.process-manual', $event->event_id) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Karyawan <span class="text-red-500">*</span></label>
                <input type="text" id="search-karyawan" placeholder="Ketik nama atau NIP..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 mb-2"
                    oninput="filterKaryawan(this.value)">

                <select name="karyawan_id" id="karyawan-select" required size="6"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 h-48">
                    @foreach($karyawans as $kar)
                        <option value="{{ $kar->karyawan_id }}" data-name="{{ strtolower($kar->full_name) }}" data-nip="{{ $kar->nip }}">
                            {{ $kar->full_name }} — {{ $kar->nip }} ({{ $kar->department?->name }})
                        </option>
                    @endforeach
                </select>
                @error('karyawan_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            @if($event->type === 'partnership')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Orang</label>
                        <input type="number" name="jumlah_orang" min="1" value="{{ old('jumlah_orang', 1) }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>
            @endif

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.event.show', $event->event_id) }}"
                    class="flex-1 py-2.5 text-center bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit"
                    class="flex-1 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow hover:from-blue-600 hover:to-indigo-700 transition-all">
                    Catat Kehadiran
                </button>
            </div>
        </form>
    </div>

    @if($karyawans->isEmpty())
        <div class="mt-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm">
            Semua karyawan sudah tercatat hadir di event ini.
        </div>
    @endif
</div>

<script>
function filterKaryawan(q) {
    q = q.toLowerCase().trim();
    const select = document.getElementById('karyawan-select');
    Array.from(select.options).forEach(opt => {
        const match = !q || opt.dataset.name.includes(q) || opt.dataset.nip.includes(q);
        opt.style.display = match ? '' : 'none';
    });
}
</script>
@endsection
