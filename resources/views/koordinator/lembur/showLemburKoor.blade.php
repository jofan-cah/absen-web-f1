@extends('admin.layouts.app')

@section('title', 'Detail Lembur')
@section('breadcrumb', 'Detail Lembur')
@section('page_title', 'Detail Lembur #' . $lembur->lembur_id)

@section('page_actions')
    <div class="flex gap-2">
        <a href="{{ route('koordinator.lembur.index') }}"
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
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Status Badge -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @php
                    $statusConfig = [
                        'draft' => [
                            'bg' => 'bg-gray-100',
                            'text' => 'text-gray-700',
                            'icon' =>
                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                        'submitted' => [
                            'bg' => 'bg-yellow-100',
                            'text' => 'text-yellow-700',
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'approved' => [
                            'bg' => 'bg-green-100',
                            'text' => 'text-green-700',
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'rejected' => [
                            'bg' => 'bg-red-100',
                            'text' => 'text-red-700',
                            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        'processed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'M5 13l4 4L19 7'],
                    ];
                    $status = $statusConfig[$lembur->status] ?? [
                        'bg' => 'bg-gray-100',
                        'text' => 'text-gray-700',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ];
                @endphp

                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status Lembur</h3>
                        <div class="flex items-center gap-3">
                            <span
                                class="px-4 py-2 inline-flex items-center gap-2 text-sm font-semibold rounded-lg {{ $status['bg'] }} {{ $status['text'] }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $status['icon'] }}" />
                                </svg>
                                {{ ucfirst($lembur->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ optional($lembur->created_at)->format('d/m/Y H:i') ?? '' }}
                        </p>

                    </div>
                </div>
            </div>

            <!-- Detail Lembur -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Lembur</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Tanggal Lembur</p>
                        <p class="text-base font-medium text-gray-900">{{ $lembur->tanggal_lembur->format('d F Y') }}</p>
                        <p class="text-sm text-gray-400">{{ $lembur->tanggal_lembur->format('l') }}</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Jam Mulai</p>
                        <p class="text-base font-medium text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }}</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Jam Selesai</p>
                        <p class="text-base font-medium text-gray-900">{{ substr($lembur->jam_selesai, 0, 5) }}</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-sm text-gray-500">Total Jam</p>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-2xl font-bold text-indigo-600">{{ number_format($lembur->total_jam, 1) }}</span>
                            <span class="text-sm text-gray-500">jam</span>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <p class="text-sm text-gray-500 mb-2">Tunjangan yang Didapat</p>
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-4">
                            @php
                                $quantity = $lembur->total_jam >= 4 ? 2 : 1;
                                $staffStatus = $lembur->karyawan->staff_status;
                                $amountPerUnit = in_array($staffStatus, [
                                    'karyawan',
                                    'koordinator',
                                    'wakil_koordinator',
                                ])
                                    ? 20000
                                    : 15000;
                                $totalAmount = $amountPerUnit * $quantity;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-indigo-600 font-medium">{{ $quantity }}x Uang Makan</p>
                                    <p class="text-xs text-indigo-500">Rp {{ number_format($amountPerUnit, 0, ',', '.') }}
                                        × {{ $quantity }}</p>
                                </div>
                                <p class="text-xl font-bold text-indigo-700">Rp
                                    {{ number_format($totalAmount, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                💡
                                {{ $lembur->total_jam >= 4 ? 'Lembur ≥ 4 jam mendapat 2x uang makan' : 'Lembur < 4 jam mendapat 1x uang makan' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if ($lembur->deskripsi_pekerjaan)
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm text-gray-500 mb-2">Deskripsi Pekerjaan</p>
                        <p class="text-base text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $lembur->deskripsi_pekerjaan }}</p>
                    </div>
                @endif

                @if ($lembur->bukti_foto)
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm text-gray-500 mb-2">Bukti Foto</p>
                        <img src="{{ Storage::disk('public')->url($lembur->bukti_foto) }}" alt="Bukti Lembur"
                            class="w-full max-w-md rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition-opacity"
                            onclick="openImageModal(this.src)">
                    </div>
                @endif
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

                                @if ($lembur->coordinator)
                                    <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                        <svg class="inline w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                        </svg>
                                        Koordinator
                                    </span>
                                @endif
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
                                <p class="text-base text-gray-900 bg-green-50 p-3 rounded-lg">{{ $lembur->approval_notes }}
                                </p>
                            </div>
                        @endif
                        @if ($lembur->rejection_reason)
                            <div>
                                <p class="text-sm text-gray-500">Alasan Penolakan</p>
                                <p class="text-base text-gray-900 bg-red-50 p-3 rounded-lg">{{ $lembur->rejection_reason }}
                                </p>
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
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
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
                            <p class="text-xs text-gray-500">{{ optional($lembur->created_at)->format('d/m/Y H:i') ??'' }}</p>
                            <p class="text-xs text-gray-400">via {{ $lembur->submitted_via ?? 'web' }}</p>
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
                                <p class="text-sm font-medium text-gray-900">Diajukan</p>
                                <p class="text-xs text-gray-500">{{ $lembur->submitted_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-400">via {{ $lembur->submitted_via ?? 'web' }}</p>
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

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-[90vh] rounded-lg">
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

            fetch(`/koordinator/lembur/{{ $lembur->lembur_id }}/approve`, {
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
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('Terjadi kesalahan');
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

            fetch(`/koordinator/lembur/{{ $lembur->lembur_id }}/reject`, {
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
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    alert('Terjadi kesalahan');
                });
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function showLoading() {
            // Implement loading
        }

        function hideLoading() {
            // Hide loading
        }
    </script>
@endpush
