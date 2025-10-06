@extends('admin.layouts.app')

@section('title', 'Review Ijin - Koordinator')
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
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Review Pengajuan Ijin</h1>
                        <p class="text-sm text-gray-600 mt-1">Approve atau tolak pengajuan ijin sebagai Koordinator</p>
                    </div>
                </div>
                <a href="{{ route('admin.ijin.coordinator-pending') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Info Notice -->
    <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
                <p class="text-sm text-blue-800">
                    <strong>Catatan:</strong> Jika Anda approve, ijin akan diteruskan ke admin untuk approval final. Jika Anda reject, ijin akan langsung ditolak.
                </p>
            </div>
        </div>
    </div>

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
        </div>

        <!-- Right Column - Review Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Form Review Koordinator
                </h2>

                <form id="reviewForm" method="POST" action="{{ route('admin.ijin.coordinator-review', $ijin->ijin_id) }}">
                    @csrf

                    <!-- Hidden Input for Action -->
                    <input type="hidden" name="action" id="action" value="">

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <textarea name="note" id="note" rows="4" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm" placeholder="Tambahkan catatan review Anda...">{{ old('note') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Catatan akan dilihat oleh admin dan karyawan</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button type="button" onclick="submitReview('approve')" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Approve & Teruskan ke Admin
                        </button>

                        <button type="button" onclick="submitReview('reject')" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reject Ijin
                        </button>

                        <a href="{{ route('admin.ijin.coordinator-pending') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Batal
                        </a>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Informasi Review</h3>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• Approve: Ijin akan diteruskan ke admin untuk approval final</li>
                        <li>• Reject: Ijin akan langsung ditolak tanpa perlu review admin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function submitReview(action) {
        const form = document.getElementById('reviewForm');
        const actionInput = document.getElementById('action');

        // Set action value
        actionInput.value = action;

        // Confirmation
        let message = '';
        if (action === 'approve') {
            message = 'Apakah Anda yakin ingin APPROVE ijin ini?\n\nIjin akan diteruskan ke admin untuk approval final.';
        } else {
            message = 'Apakah Anda yakin ingin REJECT ijin ini?\n\nIjin akan langsung ditolak tanpa perlu review admin.';
        }

        if (confirm(message)) {
            showLoading();
            form.submit();
        }
    }
</script>
@endpush
