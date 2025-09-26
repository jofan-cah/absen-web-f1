@extends('admin.layouts.app')

@section('title', 'Generate Tunjangan Lembur')
@section('breadcrumb', 'Generate Tunjangan Lembur')
@section('page_title', 'Generate Tunjangan Lembur Mingguan')

@section('page_actions')
<div class="flex gap-2">
    <a href="{{ route('admin.lembur.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>
@endsection

@section('content')

<!-- Info Banner -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Cara Generate Tunjangan Lembur</h3>
            <div class="text-sm text-blue-700 space-y-1">
                <p>• Pilih minggu (Senin - Minggu) untuk periode tunjangan lembur</p>
                <p>• Sistem akan mencari semua lembur yang sudah <span class="font-semibold">approved</span> tapi belum di-generate</p>
                <p>• Tunjangan akan di-group per karyawan dan dihitung total jam × multiplier × nominal</p>
                <p>• Setelah generate, status lembur berubah menjadi <span class="font-semibold">processed</span></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Pilih Periode Mingguan</h3>

            @if($availableWeeks->isEmpty())
                <div class="text-center py-12">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Lembur yang Perlu Diproses</h3>
                    <p class="text-gray-500 mb-4">Semua lembur yang sudah approved telah di-generate tunjangannya</p>
                    <a href="{{ route('admin.lembur.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Lihat Data Lembur
                    </a>
                </div>
            @else
                <form id="generate-form" method="POST" action="{{ route('admin.lembur.generate-tunjangan.mingguan') }}" class="space-y-6">
                    @csrf

                    <!-- Week Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Minggu</label>
                        <div class="space-y-3">
                            @foreach($availableWeeks as $week)
                            <div class="relative">
                                <input type="radio"
                                       id="week_{{ $loop->index }}"
                                       name="week_start"
                                       value="{{ $week['week_start'] }}"
                                       class="peer hidden"
                                       onchange="selectWeek({{ $loop->index }})">
                                <label for="week_{{ $loop->index }}"
                                       class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-primary-300 hover:bg-primary-50 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:shadow-md">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white font-bold">
                                                {{ \Carbon\Carbon::parse($week['week_start'])->format('d') }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $week['label'] }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($week['week_start'])->format('F Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $week['count'] }} lembur
                                        </span>
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 peer-checked:border-primary-500 peer-checked:bg-primary-500 flex items-center justify-center transition-all">
                                            <svg class="w-4 h-4 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview Info (Hidden by default) -->
                    <div id="preview-section" class="hidden">
                        <div class="bg-gradient-to-br from-primary-50 to-blue-50 border border-primary-200 rounded-xl p-6">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Preview Generate
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Periode</p>
                                    <p id="preview-period" class="text-base font-semibold text-gray-900">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Lembur</p>
                                    <p id="preview-count" class="text-base font-semibold text-orange-600">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-4 border-t">
                        <p class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">Catatan:</span> Tunjangan akan dibuat dengan status <span class="font-semibold">pending</span>
                        </p>
                        <button type="submit"
                                id="submit-btn"
                                disabled
                                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2 disabled:bg-gray-300 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Generate Tunjangan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="space-y-6">
        <!-- Statistics Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Total Minggu Tersedia</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $availableWeeks->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Total Lembur Pending</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $availableWeeks->sum('count') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Proses Generate</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">1</div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pilih Minggu</p>
                        <p class="text-xs text-gray-500 mt-1">Tentukan periode mingguan yang akan di-generate</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">2</div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Sistem Kalkulasi</p>
                        <p class="text-xs text-gray-500 mt-1">Hitung total jam × multiplier × nominal per karyawan</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">3</div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Buat Tunjangan</p>
                        <p class="text-xs text-gray-500 mt-1">Generate tunjangan untuk setiap karyawan</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-sm">✓</div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Selesai</p>
                        <p class="text-xs text-gray-500 mt-1">Status lembur menjadi processed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-xl border border-primary-200 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Quick Links</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.lembur.index') }}?status=approved" class="flex items-center justify-between p-3 bg-white rounded-lg hover:shadow-md transition-all group">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary-600">Lembur Approved</span>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('admin.tunjangan-karyawan.index') }}" class="flex items-center justify-between p-3 bg-white rounded-lg hover:shadow-md transition-all group">
                    <span class="text-sm font-medium text-gray-700 group-hover:text-primary-600">Data Tunjangan</span>
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const weeks = @json($availableWeeks);

function selectWeek(index) {
    const week = weeks[index];
    const submitBtn = document.getElementById('submit-btn');
    const previewSection = document.getElementById('preview-section');
    const previewPeriod = document.getElementById('preview-period');
    const previewCount = document.getElementById('preview-count');

    // Show preview
    previewSection.classList.remove('hidden');

    // Update preview
    previewPeriod.textContent = week.label;
    previewCount.textContent = week.count + ' lembur';

    // Enable submit button
    submitBtn.disabled = false;
}

// Form submission
document.getElementById('generate-form')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const selectedWeek = document.querySelector('input[name="week_start"]:checked');

    if (!selectedWeek) {
        alert('Pilih minggu terlebih dahulu!');
        return;
    }

    if (!confirm('Generate tunjangan lembur untuk periode yang dipilih?')) {
        return;
    }

    showLoading();
    this.submit();
});
</script>
@endpush
