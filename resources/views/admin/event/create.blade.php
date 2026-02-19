@extends('admin.layouts.app')

@section('title', 'Buat Event Baru')

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.event.index') }}"
            class="p-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Buat Event Baru</h1>
            <p class="text-xs text-gray-500 mt-0.5">Isi semua informasi yang dibutuhkan</p>
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

    <form method="POST" action="{{ route('admin.event.store') }}" class="space-y-5">
        @csrf

        {{-- Informasi Dasar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-red-500 text-white flex items-center justify-center text-xs font-bold">1</span>
                    Informasi Dasar
                </h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Judul Event <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Rapat Koordinasi Q1 2026"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-transparent transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-white transition">
                            <option value="internal" {{ old('type', 'internal') === 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="partnership" {{ old('type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Department <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <select name="department_id" class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 bg-white transition">
                            <option value="">— Semua Department —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->department_id }}" {{ old('department_id') == $dept->department_id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Gedung A, Lantai 3"
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Keterangan tambahan tentang event ini..."
                        class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition resize-none">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Tanggal & Waktu --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-500 text-white flex items-center justify-center text-xs font-bold">2</span>
                    Tanggal & Waktu
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengaturan QR --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-green-500 text-white flex items-center justify-center text-xs font-bold">3</span>
                    Pengaturan QR & Peserta
                </h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Refresh QR (detik)</label>
                        <input type="number" name="qr_refresh_seconds" value="{{ old('qr_refresh_seconds', 30) }}" min="10" max="300"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                        <p class="text-xs text-gray-400 mt-1">Antara 10 – 300 detik</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Maks Peserta</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="1" placeholder="Tidak terbatas"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                </div>
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <input type="checkbox" name="allow_multi_scan" value="1" id="allow_multi_scan"
                        {{ old('allow_multi_scan') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-300">
                    <div>
                        <div class="text-sm font-semibold text-gray-700">Izinkan scan berulang</div>
                        <div class="text-xs text-gray-400">Satu karyawan bisa scan lebih dari satu kali</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- GPS Validasi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-orange-500 text-white flex items-center justify-center text-xs font-bold">4</span>
                        Validasi GPS
                    </h2>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Opsional</span>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-xs text-gray-500">Kosongkan jika tidak ingin membatasi lokasi saat scan.</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Latitude</label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" placeholder="-6.200000"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Longitude</label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" placeholder="106.816666"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Radius (meter)</label>
                        <input type="number" name="radius" value="{{ old('radius', 100) }}" min="10"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3 justify-end pb-2">
            <a href="{{ route('admin.event.index') }}"
                class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:from-red-600 hover:to-rose-700 transition-all">
                Buat Event
            </button>
        </div>
    </form>
</div>
@endsection
