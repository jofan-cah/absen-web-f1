@extends('admin.layouts.app')

@section('title', 'Lembur Department Saya')
@section('breadcrumb', 'Lembur Department Saya')
@section('page_title', 'Management Lembur - ' . (auth()->user()->karyawan->department->name ?? 'Department'))

@section('page_actions')
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <!-- Filter Status -->
            <select id="filter-status"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Status</option>
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Tanggal -->
            <input type="date" id="filter-tanggal-dari" value="{{ request('tanggal_dari') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Dari Tanggal">

            <input type="date" id="filter-tanggal-sampai" value="{{ request('tanggal_sampai') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Sampai Tanggal">
        </div>

        <button onclick="resetFilter()"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Reset Filter
        </button>
    </div>
@endsection

@section('content')

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Lembur</p>
                    <p class="text-3xl font-bold mt-1">{{ $summary['total'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Submitted -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Menunggu Approval</p>
                    <p class="text-3xl font-bold mt-1">{{ $summary['submitted'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Disetujui</p>
                    <p class="text-3xl font-bold mt-1">{{ $summary['approved'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Ditolak</p>
                    <p class="text-3xl font-bold mt-1">{{ $summary['rejected'] }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lemburs as $lembur)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr($lembur->karyawan->full_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $lembur->karyawan->full_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $lembur->karyawan->nip }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $lembur->tanggal_lembur->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $lembur->tanggal_lembur->format('l') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }} -
                                    {{ substr($lembur->jam_selesai, 0, 5) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-semibold text-gray-900">{{ number_format($lembur->total_jam, 1) }}
                                    jam</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => [
                                            'bg' => 'bg-gray-100',
                                            'text' => 'text-gray-700',
                                            'label' => 'Draft',
                                        ],
                                        'submitted' => [
                                            'bg' => 'bg-yellow-100',
                                            'text' => 'text-yellow-700',
                                            'label' => 'Menunggu',
                                        ],
                                        'approved' => [
                                            'bg' => 'bg-green-100',
                                            'text' => 'text-green-700',
                                            'label' => 'Disetujui',
                                        ],
                                        'rejected' => [
                                            'bg' => 'bg-red-100',
                                            'text' => 'text-red-700',
                                            'label' => 'Ditolak',
                                        ],
                                        'processed' => [
                                            'bg' => 'bg-blue-100',
                                            'text' => 'text-blue-700',
                                            'label' => 'Diproses',
                                        ],
                                    ];
                                    $status = $statusConfig[$lembur->status] ?? [
                                        'bg' => 'bg-gray-100',
                                        'text' => 'text-gray-700',
                                        'label' => ucfirst($lembur->status),
                                    ];
                                @endphp
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['bg'] }} {{ $status['text'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Detail (Selalu ada) -->
                                    <a href="{{ route('koordinator.lembur.show', $lembur->lembur_id) }}"
                                        class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- ✅ Button Approve/Reject HANYA muncul jika koordinator_status = pending --}}
                                    @if ($lembur->status == 'submitted' && $lembur->koordinator_status == 'pending')
                                        <button onclick="approveLembur('{{ $lembur->lembur_id }}')"
                                            class="text-green-600 hover:text-green-700 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                            title="Approve (Koordinator)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                        <button onclick="rejectLembur('{{ $lembur->lembur_id }}')"
                                            class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                            title="Reject (Koordinator)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @elseif($lembur->koordinator_status == 'approved')
                                        {{-- ✅ Info: Sudah di-approve, menunggu admin --}}
                                        <span class="text-xs text-gray-500 italic flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Menunggu Admin
                                        </span>
                                    @elseif($lembur->koordinator_status == 'rejected')
                                        {{-- ✅ Info: Sudah di-reject --}}
                                        <span class="text-xs text-red-500 italic flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data lembur</h3>
                                    <p class="text-gray-500">Data lembur dari karyawan department Anda akan muncul di sini
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($lemburs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $lemburs->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const filterStatus = document.getElementById('filter-status');
            const filterTanggalDari = document.getElementById('filter-tanggal-dari');
            const filterTanggalSampai = document.getElementById('filter-tanggal-sampai');

            function performFilter() {
                const params = new URLSearchParams();

                if (filterStatus.value) params.append('status', filterStatus.value);
                if (filterTanggalDari.value) params.append('tanggal_dari', filterTanggalDari.value);
                if (filterTanggalSampai.value) params.append('tanggal_sampai', filterTanggalSampai.value);

                window.location.href = `{{ route('koordinator.lembur.index') }}?${params.toString()}`;
            }

            filterStatus.addEventListener('change', performFilter);
            filterTanggalDari.addEventListener('change', performFilter);
            filterTanggalSampai.addEventListener('change', performFilter);
        });

        function resetFilter() {
            window.location.href = '{{ route('koordinator.lembur.index') }}';
        }

        // Approve lembur
        function approveLembur(id) {
            const notes = prompt('Catatan persetujuan (opsional):');
            if (notes === null) return;

            if (!confirm('Approve lembur ini?')) return;

            showLoading();

            fetch(`/koordinator/lembur/${id}/approve`, {
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

        // Reject lembur
        function rejectLembur(id) {
            const reason = prompt('Alasan penolakan (wajib):');
            if (!reason) {
                alert('Alasan penolakan harus diisi!');
                return;
            }

            if (!confirm('Reject lembur ini?')) return;

            showLoading();

            fetch(`/koordinator/lembur/${id}/reject`, {
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

        // Loading & Toast helpers (reuse dari admin)
        function showLoading() {
            // Implement loading indicator
        }

        function hideLoading() {
            // Hide loading indicator
        }

        function showToast(message, type) {
            alert(message); // Simple alert, bisa diganti dengan toast library
        }
    </script>
@endpush
