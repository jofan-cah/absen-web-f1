@extends('admin.layouts.app')

@section('title', 'Detail Absensi')
@section('breadcrumb', 'Detail Absensi')
@section('page_title', 'Detail Absensi - ' . $absen->karyawan->full_name)

@section('page_actions')
<div class="flex gap-3">
    <a href="{{ route('admin.absen.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>

    <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Print
    </button>

    @if($absen->clock_in_photo || $absen->clock_out_photo)
        <button onclick="showPhotosModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Lihat Foto
        </button>
    @endif
</div>
@endsection

@section('content')

<!-- Header Information -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center space-x-4">
            <!-- Avatar -->
            <div class="h-16 w-16 bg-gray-300 rounded-full flex items-center justify-center">
                @if($absen->karyawan->photo)
                    <img src="{{ asset('storage/' . $absen->karyawan->photo) }}" alt="{{ $absen->karyawan->full_name }}" class="h-16 w-16 rounded-full object-cover">
                @else
                    <span class="text-xl font-bold text-gray-700">
                        {{ substr($absen->karyawan->full_name, 0, 2) }}
                    </span>
                @endif
            </div>

            <!-- Basic Info -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $absen->karyawan->full_name }}</h2>
                <p class="text-gray-600">{{ $absen->karyawan->nip }} • {{ $absen->karyawan->department->name ?? 'No Department' }}</p>
                <p class="text-sm text-gray-500">{{ $absen->karyawan->position ?? 'No Position' }}</p>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mt-4 lg:mt-0">
            @php
                $statusClasses = [
                    'scheduled' => 'bg-gray-100 text-gray-800 border-gray-200',
                    'present' => 'bg-green-100 text-green-800 border-green-200',
                    'late' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'absent' => 'bg-red-100 text-red-800 border-red-200',
                    'early_checkout' => 'bg-orange-100 text-orange-800 border-orange-200'
                ];

                $statusNames = [
                    'scheduled' => 'Terjadwal',
                    'present' => 'Hadir',
                    'late' => 'Terlambat',
                    'absent' => 'Tidak Hadir',
                    'early_checkout' => 'Pulang Cepat'
                ];
            @endphp
            <span class="inline-flex items-center px-4 py-2 text-lg font-semibold rounded-full border {{ $statusClasses[$absen->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                {{ $statusNames[$absen->status] ?? ucfirst($absen->status) }}
            </span>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left Column: Attendance Details -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Schedule Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Informasi Jadwal
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <p class="text-lg text-gray-900">{{ $absen->date->format('d F Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $absen->date->format('l') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                        <p class="text-lg text-gray-900">{{ $absen->jadwal->shift->name }}</p>
                        <p class="text-sm text-gray-500 font-mono">
                            {{ \Carbon\Carbon::parse($absen->jadwal->shift->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($absen->jadwal->shift->end_time)->format('H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clock In/Out Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Detail Kehadiran
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Clock In -->
                    <div class="border-l-4 border-green-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Clock In
                        </h4>

                        @if($absen->clock_in)
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Waktu</label>
                                    <p class="text-xl font-mono text-gray-900">{{ \Carbon\Carbon::parse($absen->clock_in)->format('H:i:s') }}</p>
                                </div>

                                @if($absen->clock_in_address)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                        <p class="text-sm text-gray-600">{{ $absen->clock_in_address }}</p>
                                        @if($absen->clock_in_latitude && $absen->clock_in_longitude)
                                            <p class="text-xs text-gray-500 font-mono">
                                                {{ $absen->clock_in_latitude }}, {{ $absen->clock_in_longitude }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if($absen->clock_in_photo)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                                        <img src="{{ asset('storage/' . $absen->clock_in_photo) }}"
                                             alt="Clock In Photo"
                                             class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                             onclick="showImageModal('{{ asset('storage/' . $absen->clock_in_photo) }}', 'Foto Clock In')">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500">Belum clock in</p>
                            </div>
                        @endif
                    </div>

                    <!-- Clock Out -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Clock Out
                        </h4>

                        @if($absen->clock_out)
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Waktu</label>
                                    <p class="text-xl font-mono text-gray-900">{{ \Carbon\Carbon::parse($absen->clock_out)->format('H:i:s') }}</p>
                                </div>

                                @if($absen->clock_out_address)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                        <p class="text-sm text-gray-600">{{ $absen->clock_out_address }}</p>
                                        @if($absen->clock_out_latitude && $absen->clock_out_longitude)
                                            <p class="text-xs text-gray-500 font-mono">
                                                {{ $absen->clock_out_latitude }}, {{ $absen->clock_out_longitude }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if($absen->clock_out_photo)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                                        <img src="{{ asset('storage/' . $absen->clock_out_photo) }}"
                                             alt="Clock Out Photo"
                                             class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                             onclick="showImageModal('{{ asset('storage/' . $absen->clock_out_photo) }}', 'Foto Clock Out')">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-500">Belum clock out</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        @if($absen->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Catatan
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $absen->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Statistics & Summary -->
    <div class="space-y-6">

        <!-- Work Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ringkasan Kerja
                </h3>
            </div>
            <div class="p-6 space-y-4">

                <!-- Work Hours -->
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-blue-900">Total Jam Kerja</p>
                        <p class="text-2xl font-bold text-blue-700">
                            {{ $absen->work_hours ? number_format($absen->work_hours, 1) : '0' }}
                        </p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Late Minutes -->
                @if($absen->late_minutes > 0)
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-red-900">Terlambat</p>
                            <p class="text-2xl font-bold text-red-700">{{ $absen->late_minutes }}</p>
                            <p class="text-xs text-red-600">menit</p>
                        </div>
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                    </div>
                @endif

                <!-- Early Checkout -->
                @if($absen->early_checkout_minutes > 0)
                    <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-orange-900">Pulang Cepat</p>
                            <p class="text-2xl font-bold text-orange-700">{{ $absen->early_checkout_minutes }}</p>
                            <p class="text-xs text-orange-600">menit</p>
                        </div>
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                @if($absen->clock_in_latitude && $absen->clock_in_longitude)
                    <button onclick="showLocationModal('{{ $absen->clock_in_latitude }}', '{{ $absen->clock_in_longitude }}', 'Clock In Location')"
                            class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Lihat Lokasi Clock In
                    </button>
                @endif

                @if($absen->clock_out_latitude && $absen->clock_out_longitude)
                    <button onclick="showLocationModal('{{ $absen->clock_out_latitude }}', '{{ $absen->clock_out_longitude }}', 'Clock Out Location')"
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Lihat Lokasi Clock Out
                    </button>
                @endif

                <button onclick="exportPDF()"
                        class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Info Karyawan</h3>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIP</label>
                    <p class="text-sm text-gray-900">{{ $absen->karyawan->nip }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Department</label>
                    <p class="text-sm text-gray-900">{{ $absen->karyawan->department->name ?? 'No Department' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Position</label>
                    <p class="text-sm text-gray-900">{{ $absen->karyawan->position ?? 'No Position' }}</p>
                </div>

                @if($absen->karyawan->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <p class="text-sm text-gray-900">{{ $absen->karyawan->phone }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="imageModalTitle" class="text-lg font-medium text-gray-900">Foto</h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <img id="imageModalImg" src="" alt="" class="w-full h-auto rounded-lg">
        </div>
    </div>
</div>

<!-- Location Modal -->
<div id="locationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="locationModalTitle" class="text-lg font-medium text-gray-900">Lokasi</h3>
                <button onclick="closeLocationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="mapContainer" class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                <p class="text-gray-500">Loading map...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showImageModal(imageSrc, title) {
    document.getElementById('imageModalImg').src = imageSrc;
    document.getElementById('imageModalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function showLocationModal(lat, lng, title) {
    document.getElementById('locationModalTitle').textContent = title;
    document.getElementById('locationModal').classList.remove('hidden');

    // Initialize map
    initMap(lat, lng);
}

function closeLocationModal() {
    document.getElementById('locationModal').classList.add('hidden');
}

function initMap(lat, lng) {
    const mapContainer = document.getElementById('mapContainer');

    // Simple map implementation using Google Maps or OpenStreetMap
    // For this example, we'll use a simple iframe with Google Maps
    const mapUrl = `https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=${lat},${lng}&zoom=15`;

    // Alternative: OpenStreetMap iframe
    const osmUrl = `https://www.openstreetmap.org/export/embed.html?bbox=${lng-0.01},${lat-0.01},${lng+0.01},${lat+0.01}&layer=mapnik&marker=${lat},${lng}`;

    mapContainer.innerHTML = `
        <iframe
            width="100%"
            height="100%"
            frameborder="0"
            scrolling="no"
            marginheight="0"
            marginwidth="0"
            src="${osmUrl}"
            style="border-radius: 8px;">
        </iframe>
    `;
}

function showPhotosModal() {
    // Create a modal to show both photos side by side
    const modal = document.createElement('div');
    modal.id = 'photosModal';
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';

    const clockInPhoto = '{{ $absen->clock_in_photo ? asset("storage/" . $absen->clock_in_photo) : "" }}';
    const clockOutPhoto = '{{ $absen->clock_out_photo ? asset("storage/" . $absen->clock_out_photo) : "" }}';

    modal.innerHTML = `
        <div class="relative top-10 mx-auto p-5 border max-w-6xl shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Foto Absensi</h3>
                    <button onclick="closePhotosModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    ${clockInPhoto ? `
                        <div>
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Clock In</h4>
                            <img src="${clockInPhoto}" alt="Clock In Photo" class="w-full h-auto rounded-lg shadow-md">
                        </div>
                    ` : ''}
                    ${clockOutPhoto ? `
                        <div>
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Clock Out</h4>
                            <img src="${clockOutPhoto}" alt="Clock Out Photo" class="w-full h-auto rounded-lg shadow-md">
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

function closePhotosModal() {
    const modal = document.getElementById('photosModal');
    if (modal) {
        modal.remove();
    }
}

function exportPDF() {
    // Hide non-printable elements
    const actionButtons = document.querySelector('.page-actions');
    const quickActions = document.querySelector('.quick-actions');

    if (actionButtons) actionButtons.style.display = 'none';
    if (quickActions) quickActions.style.display = 'none';

    // Print the page
    window.print();

    // Restore elements
    setTimeout(() => {
        if (actionButtons) actionButtons.style.display = '';
        if (quickActions) quickActions.style.display = '';
    }, 1000);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const imageModal = document.getElementById('imageModal');
    const locationModal = document.getElementById('locationModal');
    const photosModal = document.getElementById('photosModal');

    if (event.target === imageModal) {
        closeImageModal();
    }
    if (event.target === locationModal) {
        closeLocationModal();
    }
    if (event.target === photosModal) {
        closePhotosModal();
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
        closeLocationModal();
        closePhotosModal();
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        exportPDF();
    }
});
</script>
@endpush

@push('styles')
<style>
/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .print-break {
        page-break-before: always;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .bg-blue-50,
    .bg-red-50,
    .bg-orange-50,
    .bg-green-50 {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
    }
}

/* Modal animations */
.modal-enter {
    animation: modalEnter 0.3s ease-out;
}

@keyframes modalEnter {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Image hover effects */
.image-hover {
    transition: all 0.3s ease;
}

.image-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Status badge animations */
.status-badge {
    animation: statusPulse 2s infinite;
}

@keyframes statusPulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* Loading animation for map */
.map-loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Custom scrollbar */
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}

.custom-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-responsive {
        grid-template-columns: 1fr;
    }

    .text-responsive {
        font-size: 0.875rem;
    }

    .modal-responsive {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Button loading state */
.btn-loading {
    opacity: 0.6;
    pointer-events: none;
}

.btn-loading::after {
    content: '';
    width: 16px;
    height: 16px;
    margin-left: 8px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Success/Error states */
.success-state {
    border-left: 4px solid #10b981;
    background-color: #f0fdf4;
}

.error-state {
    border-left: 4px solid #ef4444;
    background-color: #fef2f2;
}

.warning-state {
    border-left: 4px solid #f59e0b;
    background-color: #fffbeb;
}

/* Timeline styles for attendance flow */
.timeline-item {
    position: relative;
    padding-left: 2rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e5e7eb;
}

.timeline-item::after {
    content: '';
    position: absolute;
    left: 0.25rem;
    top: 0.5rem;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
    background-color: #3b82f6;
}

.timeline-item:last-child::before {
    display: none;
}
</style>
@endpush
