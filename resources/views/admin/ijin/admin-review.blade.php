@extends('admin.layouts.app')

@section('title', 'Review Ijin - Admin')
@section('page_title', 'Review Ijin')
@section('breadcrumb', 'Review Ijin')

@section('content')
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-red-800 font-medium mb-2">Terdapat kesalahan:</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Review Pengajuan Ijin</h1>
                        <p class="text-sm text-gray-600 mt-1">Approve atau tolak pengajuan ijin sebagai Admin</p>
                    </div>
                </div>
                <a href="{{ route('admin.ijin.admin-pending') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Bypass Warning (jika coordinator belum approve) -->
    @if ($needsBypass)
        <div class="mb-6 rounded-lg bg-yellow-50 border-2 border-yellow-200 p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">⚠️ Peringatan: Koordinator Belum Review</h3>
                    <p class="text-yellow-800 mb-3">Ijin ini belum di-review oleh koordinator <strong>{{ $ijin->coordinator->name ?? '-' }}</strong>. Anda dapat:</p>
                    <ul class="list-disc list-inside text-yellow-800 space-y-1 mb-4">
                        <li>Menunggu koordinator untuk review terlebih dahulu, atau</li>
                        <li>Melakukan <strong>bypass</strong> dan langsung approve/reject sebagai admin</li>
                    </ul>
                    <div class="bg-yellow-100 rounded-lg p-3 border border-yellow-300">
                        <p class="text-sm text-yellow-900">
                            <strong>Catatan:</strong> Jika Anda bypass, sistem akan otomatis mencatat bahwa approval koordinator di-skip oleh admin dan akan tersimpan di log audit.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Ijin Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Karyawan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    Informasi Karyawan
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">{{ strtoupper(substr($ijin->karyawan->full_name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $ijin->karyawan->full_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $ijin->karyawan->nip }}</p>
                            <p class="text-sm text-gray-600">{{ $ijin->karyawan->department->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Ijin -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                    Detail Pengajuan Ijin
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Ijin</label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-pink-100 text-pink-800 font-medium">
                                {{ $ijin->ijinType->name }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengajuan</label>
                        <p class="text-gray-900">{{ $ijin->created_at->format('d M Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->date_from)->format('d F Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->date_to)->format('d F Y') }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                        <p class="text-gray-900 font-semibold">{{ $ijin->total_days }} hari</p>
                    </div>

                    @if ($ijin->ijinType->code === 'shift_swap')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shift Asli</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->original_shift_date)->format('d F Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shift Pengganti</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->replacement_shift_date)->format('d F Y') }}</p>
                        </div>
                    @endif

                    @if ($ijin->ijinType->code === 'compensation_leave' && $ijin->original_shift_date)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kompensasi Dari Piket</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($ijin->original_shift_date)->format('d F Y') }}</p>
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <p class="text-gray-900">{{ $ijin->reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Review Coordinator -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Status Review Koordinator
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Koordinator</span>
                        <span class="text-sm text-gray-900">{{ $ijin->coordinator->name ?? '-' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        @if ($ijin->coordinator_status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <div class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                                Pending
                            </span>
                        @elseif ($ijin->coordinator_status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></div>
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></div>
                                Rejected
                            </span>
                        @endif
                    </div>

                    @if ($ijin->coordinator_reviewed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Waktu Review</span>
                            <span class="text-sm text-gray-900">{{ $ijin->coordinator_reviewed_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif

                    @if ($ijin->coordinator_note)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Koordinator</label>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-sm text-gray-900">{{ $ijin->coordinator_note }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Review Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Form Review Admin
                </h2>

                <form id="reviewForm" method="POST" action="{{ route('admin.ijin.admin-review', $ijin->ijin_id) }}">
                    @csrf

                    <!-- Hidden Input for Action -->
                    <input type="hidden" name="action" id="action" value="">

                    <!-- Bypass Checkbox (hanya muncul jika coordinator belum approve) -->
                    @if ($needsBypass)
                        <div class="mb-6 p-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                            <div class="flex items-start">
                                <input type="checkbox" name="bypass_coordinator" id="bypass_coordinator" value="1" class="mt-1 h-5 w-5 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                <label for="bypass_coordinator" class="ml-3 text-sm">
                                    <span class="font-semibold text-yellow-900">Bypass Koordinator</span>
                                    <p class="text-yellow-800 mt-1">Saya memahami dan ingin melewati approval koordinator. Tindakan ini akan dicatat dalam sistem.</p>
                                </label>
                            </div>
                        </div>
                    @endif

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <textarea name="note" id="note" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" placeholder="Tambahkan catatan review Anda...">{{ old('note') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Catatan akan dilihat oleh karyawan dan tersimpan dalam histori</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button type="button" onclick="submitReview('approve')" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Approve Ijin
                        </button>

                        <button type="button" onclick="submitReview('reject')" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reject Ijin
                        </button>

                        <a href="{{ route('admin.ijin.admin-pending') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function submitReview(action) {
        const form = document.getElementById('reviewForm');
        const actionInput = document.getElementById('action');
        const bypassCheckbox = document.getElementById('bypass_coordinator');

        // Set action value
        actionInput.value = action;

        // Validation untuk bypass
        @if ($needsBypass)
        if (action === 'approve' && !bypassCheckbox.checked) {
            if (!confirm('Koordinator belum mereview ijin ini. Apakah Anda yakin ingin approve tanpa centang "Bypass Koordinator"?\n\nJika ya, Anda harus mencentang checkbox bypass terlebih dahulu.')) {
                return;
            }
            alert('Silakan centang checkbox "Bypass Koordinator" untuk melanjutkan.');
            bypassCheckbox.focus();
            return;
        }
        @endif

        // Confirmation
        let message = '';
        if (action === 'approve') {
            @if ($needsBypass)
            message = 'Anda akan APPROVE ijin ini dengan BYPASS koordinator.\n\nTindakan ini akan dicatat dalam log audit.\n\nLanjutkan?';
            @else
            message = 'Apakah Anda yakin ingin APPROVE ijin ini?';
            @endif
        } else {
            message = 'Apakah Anda yakin ingin REJECT ijin ini?';
        }

        if (confirm(message)) {
            showLoading();
            form.submit();
        }
    }
</script>
@endpush
