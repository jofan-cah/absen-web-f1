@extends('admin.layouts.app')

@section('title', 'Data Lembur')
@section('breadcrumb', 'Data Lembur')
@section('page_title', 'Management Lembur - Admin')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Filter Karyawan -->
        <select id="filter-karyawan" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <option value="">Semua Karyawan</option>
            @foreach($karyawans as $karyawan)
                <option value="{{ $karyawan->karyawan_id }}" {{ request('karyawan_id') == $karyawan->karyawan_id ? 'selected' : '' }}>
                    {{ $karyawan->full_name }} - {{ $karyawan->nip }}
                </option>
            @endforeach
        </select>

        <!-- Filter Status Final -->
        <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <option value="">Semua Status</option>
            @foreach($statusOptions as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <!-- Filter Status Koordinator -->
        <select id="filter-koordinator-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <option value="">Semua Status Koordinator</option>
            @foreach($koordinatorStatusOptions as $koorStatus)
                <option value="{{ $koorStatus }}" {{ request('koordinator_status') == $koorStatus ? 'selected' : '' }}>
                    {{ $koorStatus == 'pending' ? 'Pending Koordinator' : ($koorStatus == 'approved' ? 'Approved Koordinator' : 'Rejected Koordinator') }}
                </option>
            @endforeach
        </select>

        <!-- Filter Tanggal -->
        <input type="date" id="filter-tanggal-dari" value="{{ request('tanggal_dari') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
               placeholder="Dari Tanggal">

        <input type="date" id="filter-tanggal-sampai" value="{{ request('tanggal_sampai') }}"
               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
               placeholder="Sampai Tanggal">
    </div>

    <button onclick="resetFilter()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
        Reset Filter
    </button>
</div>
@endsection

@section('content')

<!-- Info Banner -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="flex-1">
            <h3 class="text-sm font-semibold text-blue-900 mb-1">Informasi Admin - Final Approval</h3>
            <p class="text-xs text-blue-700">
                Sebagai <strong>Admin</strong>, Anda dapat:
                <br>• <strong>Approve Langsung (Bypass Koordinator)</strong> - Untuk lembur yang belum di-review koordinator
                <br>• <strong>Final Approval</strong> - Untuk lembur yang sudah di-approve koordinator
                <br>• Setelah approve, sistem otomatis generate tunjangan
            </p>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <!-- Total -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Total Lembur</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Koordinator -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Pending Koordinator</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['pending_koordinator'] }}</p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Admin (Priority) -->
    <div class="bg-white rounded-lg shadow-sm border-2 border-yellow-400 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1 flex items-center gap-1">
                    Pending Admin
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                        Anda
                    </span>
                </p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['pending_admin'] }}</p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Approved -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Disetujui</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['approved'] }}</p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-1">Ditolak</p>
                <p class="text-2xl font-bold text-gray-900">{{ $summary['rejected'] }}</p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Bulk Actions -->
    <div id="bulk-actions" class="hidden px-6 py-3 bg-blue-50 border-b border-blue-200">
        <div class="flex items-center justify-between">
            <span class="text-sm text-blue-700"><span id="selected-count">0</span> item dipilih</span>
            <div class="flex gap-2">
                <button onclick="bulkApprove()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Approve Terpilih
                </button>
                <button onclick="clearSelection()" class="px-3 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Koor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Final</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lemburs as $lembur)
                <tr class="hover:bg-blue-50 transition-colors duration-150" data-koordinator-status="{{ $lembur->koordinator_status }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($lembur->status == 'submitted')
                            <input type="checkbox" name="selected_lemburs[]" value="{{ $lembur->lembur_id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 row-checkbox">
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold shadow-md">
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
                        <div class="text-sm text-gray-900 font-medium">{{ $lembur->tanggal_lembur->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $lembur->tanggal_lembur->format('l') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ substr($lembur->jam_mulai, 0, 5) }} - {{ substr($lembur->jam_selesai ?? '00:00', 0, 5) }}</div>
                        @if($lembur->started_at)
                            <div class="text-xs text-gray-400">Started: {{ $lembur->started_at->format('H:i') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-indigo-600">{{ number_format($lembur->total_jam, 1) }} jam</span>
                    </td>

                    <!-- Status Koordinator -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $koorStatusConfig = [
                                'pending' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'Pending Koor', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Approved Koor', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Rejected Koor', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ];
                            $koorStatus = $koorStatusConfig[$lembur->koordinator_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($lembur->koordinator_status), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
                        @endphp
                        <span class="px-2 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full {{ $koorStatus['bg'] }} {{ $koorStatus['text'] }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $koorStatus['icon'] }}"/>
                            </svg>
                            {{ $koorStatus['label'] }}
                        </span>

                        @if($lembur->coordinator)
                            <p class="text-xs text-gray-500 mt-1">{{ $lembur->coordinator->full_name ?? '-' }}</p>
                        @endif
                    </td>

                    <!-- Status Final -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusConfig = [
                                'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => 'Draft'],
                                'submitted' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Submitted'],
                                'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Approved'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Rejected'],
                                'processed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Processed'],
                            ];
                            $status = $statusConfig[$lembur->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($lembur->status)];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['bg'] }} {{ $status['text'] }}">
                            {{ $status['label'] }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.lembur.show', $lembur->lembur_id) }}"
                               class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Admin bisa approve (bypass atau final) --}}
                            @if($lembur->status == 'submitted')
                                <button onclick="approveLembur('{{ $lembur->lembur_id }}', '{{ $lembur->koordinator_status }}')"
                                        class="text-green-600 hover:text-green-700 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                        title="{{ $lembur->koordinator_status == 'pending' ? 'Approve (Bypass Koordinator)' : 'Approve (Admin Final)' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button onclick="rejectLembur('{{ $lembur->lembur_id }}')"
                                        class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                        title="Reject (Admin)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                            <p class="text-gray-500">Data lembur akan muncul di sini setelah karyawan submit</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($lemburs->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        {{ $lemburs->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterKaryawan = document.getElementById('filter-karyawan');
    const filterStatus = document.getElementById('filter-status');
    const filterKoordinatorStatus = document.getElementById('filter-koordinator-status');
    const filterTanggalDari = document.getElementById('filter-tanggal-dari');
    const filterTanggalSampai = document.getElementById('filter-tanggal-sampai');

    function performFilter() {
        const params = new URLSearchParams();

        if (filterKaryawan.value) params.append('karyawan_id', filterKaryawan.value);
        if (filterStatus.value) params.append('status', filterStatus.value);
        if (filterKoordinatorStatus.value) params.append('koordinator_status', filterKoordinatorStatus.value);
        if (filterTanggalDari.value) params.append('tanggal_dari', filterTanggalDari.value);
        if (filterTanggalSampai.value) params.append('tanggal_sampai', filterTanggalSampai.value);

        window.location.href = `{{ route('admin.lembur.index') }}?${params.toString()}`;
    }

    filterKaryawan.addEventListener('change', performFilter);
    filterStatus.addEventListener('change', performFilter);
    filterKoordinatorStatus.addEventListener('change', performFilter);
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

function resetFilter() {
    window.location.href = '{{ route("admin.lembur.index") }}';
}

// Approve lembur (bypass atau final)
function approveLembur(id, koorStatus) {
    const notes = prompt('💬 Catatan persetujuan (opsional):');
    if (notes === null) return;

    let confirmMessage = '';
    if (koorStatus === 'pending') {
        confirmMessage = '⚡ BYPASS APPROVAL\n\n' +
                        'Anda akan approve lembur ini LANGSUNG (melewati Koordinator).\n\n' +
                        '✅ Koordinator status akan di-set sebagai approved otomatis\n' +
                        '✅ Tunjangan akan langsung dibuat\n\n' +
                        'Lanjutkan?';
    } else {
        confirmMessage = '✅ FINAL APPROVAL\n\n' +
                        'Koordinator sudah approve lembur ini.\n' +
                        'Anda akan melakukan approval final dan generate tunjangan.\n\n' +
                        'Lanjutkan?';
    }

    if (!confirm(confirmMessage)) return;

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
            alert('✅ ' + data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        hideLoading();
        alert('❌ Terjadi kesalahan');
        console.error('Error:', error);
    });
}

// Reject lembur
function rejectLembur(id) {
    const reason = prompt('❌ Alasan penolakan (wajib):');
    if (!reason || reason.trim() === '') {
        alert('⚠️ Alasan penolakan harus diisi!');
        return;
    }

    if (!confirm('❌ Reject lembur ini?\n\n⚠️ Admin bisa reject walau koordinator sudah approve!\n\nSetelah reject:\n- Status menjadi REJECTED (final)\n- Tunjangan TIDAK dibuat\n- Karyawan tidak mendapat tunjangan')) return;

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
            alert('✅ ' + data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        hideLoading();
        alert('❌ Terjadi kesalahan');
        console.error('Error:', error);
    });
}

// Bulk approve
function bulkApprove() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_lemburs[]"]:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('⚠️ Pilih lembur yang ingin diapprove');
        return;
    }

    // Cek apakah ada yang pending koordinator
    const rows = Array.from(checkedBoxes).map(cb => cb.closest('tr'));
    const hasPendingKoor = rows.some(row => row.dataset.koordinatorStatus === 'pending');

    let message = `💬 Catatan persetujuan untuk ${ids.length} lembur (opsional):`;
    const notes = prompt(message);
    if (notes === null) return;

    let confirmMsg = `✅ Bulk Approve ${ids.length} lembur?\n\n`;
    if (hasPendingKoor) {
        confirmMsg += '⚠️ PERHATIAN:\n- Ada lembur yang akan di-BYPASS (koordinator belum approve)\n- Koordinator status akan di-set approved otomatis\n\n';
    }
    confirmMsg += 'Sistem akan generate tunjangan untuk semua lembur yang di-approve.\n\nLanjutkan?';

    if (!confirm(confirmMsg)) return;

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
            alert('✅ ' + data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        hideLoading();
        alert('❌ Terjadi kesalahan');
        console.error('Error:', error);
    });
}

function clearSelection() {
    document.querySelectorAll('input[name="selected_lemburs[]"]').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    document.getElementById('bulk-actions').classList.add('hidden');
}

function showLoading() {
    const loadingHTML = `
        <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-blue-600 mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-700 font-medium">Memproses...</p>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
}

function hideLoading() {
    const loading = document.getElementById('loadingOverlay');
    if (loading) loading.remove();
}
</script>
@endpush
