@extends('admin.layouts.app')

@section('title', 'Data Lembur')
@section('breadcrumb', 'Data Lembur')
@section('page_title', 'Data Lembur')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Filter Karyawan -->
        <select id="filter-karyawan" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Karyawan</option>
            @foreach($karyawans as $karyawan)
                <option value="{{ $karyawan->karyawan_id }}" {{ request('karyawan_id') == $karyawan->karyawan_id ? 'selected' : '' }}>
                    {{ $karyawan->full_name }}
                </option>
            @endforeach
        </select>

        <!-- Filter Status -->
        <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Status</option>
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <!-- Filter Kategori -->
        <select id="filter-kategori" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Kategori</option>
            @foreach($kategoriOptions as $kategori)
                <option value="{{ $kategori }}" {{ request('kategori_lembur') == $kategori ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $kategori)) }}
                </option>
            @endforeach
        </select>

        <!-- Filter Tanggal -->
        <input type="date" id="filter-tanggal-dari" value="{{ request('tanggal_dari') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        <input type="date" id="filter-tanggal-sampai" value="{{ request('tanggal_sampai') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
    </div>

    <!-- Actions -->
    <div class="flex gap-2">
        <a href="{{ route('admin.lembur.report') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Report
        </a>
        <button onclick="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export
        </button>
        <a href="{{ route('admin.lembur.generate-tunjangan.form') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Generate Tunjangan
        </a>
    </div>
</div>
@endsection

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Lembur</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $summary['total'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Perlu Approve</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $summary['submitted'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $summary['approved'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Ditolak</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $summary['rejected'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Jam (Bulan Ini)</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($summary['total_jam_bulan_ini'], 1) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Lembur</h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Total: <span id="total-records">{{ $lemburs->total() }}</span> data</span>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lemburs as $lembur)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="selected_lemburs[]" value="{{ $lembur->lembur_id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr($lembur->karyawan->full_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $lembur->karyawan->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $lembur->karyawan->department->name ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $lembur->tanggal_lembur->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $lembur->tanggal_lembur->format('l') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }} - {{ substr($lembur->jam_selesai, 0, 5) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ number_format($lembur->total_jam, 1) }} jam</div>
                        <div class="text-xs text-gray-500">{{ $lembur->multiplier }}x</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($lembur->kategori_lembur == 'reguler')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Reguler
                            </span>
                        @elseif($lembur->kategori_lembur == 'hari_libur')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Hari Libur
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Hari Besar
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($lembur->status == 'submitted')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1 animate-pulse"></div>
                                Submitted
                            </span>
                        @elseif($lembur->status == 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                Approved
                            </span>
                        @elseif($lembur->status == 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                Rejected
                            </span>
                        @elseif($lembur->status == 'processed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <div class="w-2 h-2 bg-indigo-500 rounded-full mr-1"></div>
                                Processed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ ucfirst($lembur->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.lembur.show', $lembur->lembur_id) }}"
                               class="text-primary-600 hover:text-primary-700 p-1 rounded hover:bg-primary-50 transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            @if($lembur->status == 'submitted')
                                <button onclick="approveLembur('{{ $lembur->lembur_id }}')"
                                        class="text-green-600 hover:text-green-700 p-1 rounded hover:bg-green-50 transition-colors"
                                        title="Approve">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button onclick="rejectLembur('{{ $lembur->lembur_id }}')"
                                        class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors"
                                        title="Reject">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            @endif

                            @if($lembur->canEdit())
                                <button onclick="deleteLembur('{{ $lembur->lembur_id }}', '{{ $lembur->karyawan->full_name }}')"
                                        class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data lembur</h3>
                            <p class="text-gray-500">Data lembur akan muncul di sini setelah karyawan submit dari aplikasi mobile</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($lemburs->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $lemburs->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions -->
<div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-3 hidden">
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600"><span id="selected-count">0</span> item dipilih</span>
        <div class="flex gap-2">
            <button onclick="bulkApprove()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Approve Terpilih
            </button>
            <button onclick="bulkDelete()" class="px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Hapus Terpilih
            </button>
            <button onclick="clearSelection()" class="px-3 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterKaryawan = document.getElementById('filter-karyawan');
    const filterStatus = document.getElementById('filter-status');
    const filterKategori = document.getElementById('filter-kategori');
    const filterTanggalDari = document.getElementById('filter-tanggal-dari');
    const filterTanggalSampai = document.getElementById('filter-tanggal-sampai');

    function performFilter() {
        const params = new URLSearchParams();

        if (filterKaryawan.value) params.append('karyawan_id', filterKaryawan.value);
        if (filterStatus.value) params.append('status', filterStatus.value);
        if (filterKategori.value) params.append('kategori_lembur', filterKategori.value);
        if (filterTanggalDari.value) params.append('tanggal_dari', filterTanggalDari.value);
        if (filterTanggalSampai.value) params.append('tanggal_sampai', filterTanggalSampai.value);

        window.location.href = `{{ route('admin.lembur.index') }}?${params.toString()}`;
    }

    filterKaryawan.addEventListener('change', performFilter);
    filterStatus.addEventListener('change', performFilter);
    filterKategori.addEventListener('change', performFilter);
    filterTanggalDari.addEventListener('change', performFilter);
    filterTanggalSampai.addEventListener('change', performFilter);

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_lemburs[]"]');
    const bulkActionsDiv = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('input[name="selected_lemburs[]"]:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActionsDiv.classList.remove('hidden');
            selectedCountSpan.textContent = count;
        } else {
            bulkActionsDiv.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            individualCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Approve lembur
function approveLembur(id) {
    const notes = prompt('Catatan persetujuan (opsional):');
    if (notes === null) return; // User cancelled

    showLoading();

    fetch(`/admin/lembur/${id}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ notes: notes })
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

    showLoading();

    fetch(`/admin/lembur/${id}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ rejection_reason: reason })
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

// Bulk approve
function bulkApprove() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_lemburs[]"]:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Pilih lembur yang ingin diapprove');
        return;
    }

    const notes = prompt('Catatan persetujuan untuk semua item (opsional):');
    if (notes === null) return;

    if (!confirm(`Approve ${ids.length} lembur terpilih?`)) return;

    showLoading();

    fetch('{{ route("admin.lembur.bulk-approve") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ ids: ids, notes: notes })
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

// Bulk delete
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_lemburs[]"]:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Pilih lembur yang ingin dihapus');
        return;
    }

    if (!confirm(`Hapus ${ids.length} lembur terpilih? Aksi ini tidak dapat dibatalkan.`)) return;

    showLoading();

    fetch('{{ route("admin.lembur.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ ids: ids })
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

// Delete single lembur
function deleteLembur(id, karyawanName) {
    if (!confirm(`Hapus data lembur untuk ${karyawanName}?`)) return;

    showLoading();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/lembur/bulk-delete`;

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'ids[]';
    idInput.value = id;

    form.appendChild(tokenInput);
    form.appendChild(idInput);
    document.body.appendChild(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': tokenInput.value
        },
        body: JSON.stringify({ ids: [id] })
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

// Export data
function exportData() {
    showLoading();

    const params = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("admin.lembur.export") }}?' + params.toString();

    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'data-lembur.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(hideLoading, 2000);
}

// Clear selection
function clearSelection() {
    document.querySelectorAll('input[name="selected_lemburs[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('bulk-actions').classList.add('hidden');
}
</script>
@endpush
