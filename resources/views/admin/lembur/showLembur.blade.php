@extends('admin.layouts.app')

@section('title', 'Detail Lembur')
@section('breadcrumb', 'Detail Lembur')
@section('page_title', 'Detail Lembur #' . $lembur->lembur_id)

@section('page_actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.lembur.index') }}"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        @if ($lembur->status == 'submitted')
            <button onclick="approveLembur()"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Approve
            </button>
            <button onclick="rejectLembur()"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Reject
            </button>
        @endif
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Status Lembur</h3>
                    @if ($lembur->status == 'submitted')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                            Menunggu Persetujuan
                        </span>
                    @elseif($lembur->status == 'approved')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Disetujui
                        </span>
                    @elseif($lembur->status == 'rejected')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ditolak
                        </span>
                    @elseif($lembur->status == 'processed')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Sudah Diproses
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">ID Lembur</p>
                        <p class="text-base font-semibold text-gray-900 font-mono">{{ $lembur->lembur_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Submit</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $lembur->submitted_at ? $lembur->submitted_at->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detail Lembur -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Lembur</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Lembur</p>
                            <p class="text-base font-semibold text-gray-900">{{ $lembur->tanggal_lembur->format('d F Y') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $lembur->tanggal_lembur->format('l') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Kategori</p>
                            @if ($lembur->kategori_lembur == 'reguler')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Reguler
                                </span>
                            @elseif($lembur->kategori_lembur == 'hari_libur')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                    Hari Libur
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    Hari Besar
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Jam Mulai</p>
                                <p class="text-lg font-bold text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Jam Selesai</p>
                                <p class="text-lg font-bold text-gray-900">{{ substr($lembur->jam_selesai, 0, 5) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Total Jam</p>
                                <p class="text-lg font-bold text-primary-600">{{ number_format($lembur->total_jam, 1) }} jam
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-500 mb-1">Multiplier</p>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-indigo-600">{{ $lembur->multiplier }}x</span>
                            <span class="text-sm text-gray-500">pengali upah lembur</span>
                        </div>
                    </div>

                    @if ($lembur->deskripsi_pekerjaan)
                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-500 mb-2">Deskripsi Pekerjaan</p>
                            <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $lembur->deskripsi_pekerjaan }}
                            </p>
                        </div>
                    @endif

                    @if ($lembur->bukti_foto_url)
                    <h1>{{$lembur->bukti_foto_url}}</h1>
                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-500 mb-2">Bukti Foto</p>
                            <img src="{{ $lembur->bukti_foto_url }}" alt="Bukti Lembur"
                                class="w-full max-w-md rounded-lg border border-gray-200">
                        </div>
                    @endif

                </div>
            </div>

            <!-- Approval Info -->
            @if ($lembur->approved_at || $lembur->rejected_at)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        @if ($lembur->approved_at)
                            Informasi Persetujuan
                        @else
                            Informasi Penolakan
                        @endif
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">
                                @if ($lembur->approved_at)
                                    Disetujui Oleh
                                @else
                                    Ditolak Oleh
                                @endif
                            </p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $lembur->approvedBy->name ?? ($lembur->rejectedBy->name ?? '-') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $lembur->approved_at ? $lembur->approved_at->format('d/m/Y H:i') : ($lembur->rejected_at ? $lembur->rejected_at->format('d/m/Y H:i') : '-') }}
                            </p>
                        </div>
                        @if ($lembur->approval_notes)
                            <div>
                                <p class="text-sm text-gray-500">Catatan</p>
                                <p class="text-base text-gray-900 bg-green-50 p-3 rounded-lg">
                                    {{ $lembur->approval_notes }}</p>
                            </div>
                        @endif
                        @if ($lembur->rejection_reason)
                            <div>
                                <p class="text-sm text-gray-500">Alasan Penolakan</p>
                                <p class="text-base text-gray-900 bg-red-50 p-3 rounded-lg">
                                    {{ $lembur->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Tunjangan Info -->
            @if ($lembur->tunjanganKaryawan)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tunjangan Lembur</h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Total Tunjangan</p>
                                <p class="text-2xl font-bold text-indigo-600">Rp
                                    {{ number_format($lembur->tunjanganKaryawan->total_amount, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('admin.tunjangan-karyawan.show', $lembur->tunjanganKaryawan->tunjangan_karyawan_id) }}"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Lihat Detail
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">
                            <p>Status: <span
                                    class="font-medium text-gray-900">{{ ucfirst($lembur->tunjanganKaryawan->status) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Karyawan Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Karyawan</h3>

                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div
                            class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($lembur->karyawan->full_name, 0, 1) }}
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-base font-semibold text-gray-900">{{ $lembur->karyawan->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $lembur->karyawan->nip }}</p>
                    </div>
                </div>

                <div class="space-y-3 border-t pt-4">
                    <div>
                        <p class="text-sm text-gray-500">Department</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->karyawan->department->name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Posisi</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->karyawan->position }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Staff</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $lembur->karyawan->staff_status)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Absen Info (if exists) -->
            @if ($lembur->absen)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Absensi</h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="text-base font-medium text-gray-900">{{ $lembur->absen->date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Clock In</p>
                            <p class="text-base font-medium text-gray-900">{{ $lembur->absen->clock_in ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Clock Out</p>
                            <p class="text-base font-medium text-gray-900">{{ $lembur->absen->clock_out ?? '-' }}</p>
                        </div>
                        @if ($lembur->absen->jadwal)
                            <div>
                                <p class="text-sm text-gray-500">Shift</p>
                                <p class="text-base font-medium text-gray-900">
                                    {{ $lembur->absen->jadwal->shift->name ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Dibuat</p>
                            <p class="text-xs text-gray-500">{{ $lembur->created_at->format('d/m/Y H:i') }}</p>
                            @if ($lembur->createdBy)
                                <p class="text-xs text-gray-400">oleh {{ $lembur->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    @if ($lembur->submitted_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Disubmit</p>
                                <p class="text-xs text-gray-500">{{ $lembur->submitted_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">via {{ ucfirst($lembur->submitted_via ?? 'web') }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->approved_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Disetujui</p>
                                <p class="text-xs text-gray-500">{{ $lembur->approved_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">oleh {{ $lembur->approvedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($lembur->rejected_at)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Ditolak</p>
                                <p class="text-xs text-gray-500">{{ $lembur->rejected_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">oleh {{ $lembur->rejectedBy->name ?? '-' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function approveLembur() {
            const notes = prompt('Catatan persetujuan (opsional):');
            if (notes === null) return;

            if (!confirm('Approve lembur ini?')) return;

            showLoading();

            fetch('{{ route('admin.lembur.approve', $lembur->lembur_id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }

        function rejectLembur() {
            const reason = prompt('Alasan penolakan (wajib):');
            if (!reason) {
                alert('Alasan penolakan harus diisi!');
                return;
            }

            if (!confirm('Reject lembur ini?')) return;

            showLoading();

            fetch('{{ route('admin.lembur.reject', $lembur->lembur_id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        rejection_reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast('Terjadi kesalahan', 'error');
                });
        }
    </script>
@endpush
