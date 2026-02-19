@extends('admin.layouts.app')

@section('title', 'Buat Event')

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.event.index') }}" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Buat Event Baru</h1>
            <p class="text-xs text-gray-500">Isi form di bawah untuk membuat event</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.event.store') }}" class="space-y-6">
        @csrf

        {{-- Info Dasar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-2">Informasi Dasar</h2>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Event <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tipe <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="internal" {{ old('type') === 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="partnership" {{ old('type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Department (Opsional)</label>
                    <select name="department_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                        <option value="">Semua Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ old('department_id') == $dept->department_id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Lokasi</label>
                <input type="text" name="location" value="{{ old('location') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Tanggal & Waktu --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-2">Tanggal & Waktu</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
            </div>
        </div>

        {{-- Pengaturan QR --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-2">Pengaturan QR & Peserta</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Interval Refresh QR (detik)</label>
                    <input type="number" name="qr_refresh_seconds" value="{{ old('qr_refresh_seconds', 30) }}" min="10" max="300"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Maks Peserta</label>
                    <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="1"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="Kosongkan = tidak terbatas">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="allow_multi_scan" value="1" id="allow_multi_scan"
                    {{ old('allow_multi_scan') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-300">
                <label for="allow_multi_scan" class="text-sm text-gray-700">Izinkan scan berulang (1 orang bisa scan lebih dari 1x)</label>
            </div>
        </div>

        {{-- GPS (Opsional) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h2 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-2">Validasi GPS <span class="text-gray-400 font-normal text-xs">(Opsional)</span></h2>
            <p class="text-xs text-gray-500">Kosongkan jika tidak ingin validasi lokasi saat scan.</p>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Latitude</label>
                    <input type="number" step="any" name="latitude" value="{{ old('latitude') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="-6.123456">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Longitude</label>
                    <input type="number" step="any" name="longitude" value="{{ old('longitude') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="106.123456">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Radius (meter)</label>
                    <input type="number" name="radius" value="{{ old('radius', 100) }}" min="10"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('admin.event.index') }}"
                class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">Batal</a>
            <button type="submit"
                class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-xl shadow hover:from-red-600 hover:to-rose-700 transition-all">
                Simpan Event
            </button>
        </div>
    </form>
</div>
@endsection
