@extends('admin.layouts.app')

@section('title', 'Data Karyawan')
@section('breadcrumb', 'Data Karyawan')
@section('page_title', 'Data Karyawan')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <input type="text" id="search" placeholder="Cari karyawan..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Filter Department -->
        <select id="filter-department" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Department</option>
            <!-- Will be populated via AJAX or server-side -->
        </select>

        <!-- Filter Status -->
        <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="tidak_aktif">Tidak Aktif</option>
            <option value="cuti">Cuti</option>
        </select>
    </div>

    <!-- Actions -->
    <div class="flex gap-2">
        <button onclick="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export
        </button>
        <a href="{{ route('admin.karyawan.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Karyawan
        </a>
    </div>
</div>
@endsection

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Karyawan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalKaryawan ?? 0 }}</p>
                <p class="text-sm text-green-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Terdaftar
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0Z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Karyawan Aktif</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $activeKaryawan ?? 0 }}</p>
                <p class="text-sm text-green-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    {{ number_format(($activeKaryawan/max($totalKaryawan, 1))*100, 1) }}% dari total
                </p>
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
                <p class="text-sm font-medium text-gray-600">Department</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalDepartments ?? 0 }}</p>
                <p class="text-sm text-blue-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                    Unit kerja
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Baru Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $newThisMonth ?? 0 }}</p>
                <p class="text-sm text-orange-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Karyawan baru
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
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
            <h3 class="text-lg font-semibold text-gray-900">Daftar Karyawan</h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Total: <span id="total-records">{{ $karyawans->total() ?? 0 }}</span> data</span>

                <!-- View Toggle -->
                <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                    <button onclick="setView('table')" id="table-view-btn" class="px-3 py-1 text-sm bg-primary-600 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </button>
                    <button onclick="setView('card')" id="card-view-btn" class="px-3 py-1 text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div id="table-view" class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="karyawan-table-body">
                @forelse($karyawans ?? [] as $karyawan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="selected_karyawan[]" value="{{ $karyawan->karyawan_id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($karyawan->photo)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $karyawan->photo) }}" alt="{{ $karyawan->full_name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-primary-600 font-medium text-sm">{{ strtoupper(substr($karyawan->full_name, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $karyawan->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $karyawan->user->email ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-mono">{{ $karyawan->nip }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $karyawan->department->name ?? '-' }}</div>
                        <div class="text-sm text-gray-500">{{ $karyawan->department->code ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $karyawan->position }}</div>
                        <div class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $karyawan->staff_status) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'aktif' => 'bg-green-100 text-green-800',
                                'tidak_aktif' => 'bg-red-100 text-red-800',
                                'cuti' => 'bg-yellow-100 text-yellow-800',
                            ];
                            $statusColor = $statusColors[$karyawan->employment_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                            {{ ucfirst($karyawan->employment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $karyawan->hire_date ? $karyawan->hire_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.karyawan.show', $karyawan->karyawan_id) }}"
                               class="text-primary-600 hover:text-primary-700 p-1 rounded hover:bg-primary-50 transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.karyawan.edit', $karyawan->karyawan_id) }}"
                               class="text-yellow-600 hover:text-yellow-700 p-1 rounded hover:bg-yellow-50 transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button onclick="deleteKaryawan('{{ $karyawan->karyawan_id }}', '{{ $karyawan->full_name }}')"
                                    class="text-red-600 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors"
                                    title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0Z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data karyawan</h3>
                            <p class="text-gray-500 mb-4">Mulai dengan menambahkan karyawan pertama</p>
                            <a href="{{ route('admin.karyawan.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                Tambah Karyawan
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Card View (Hidden by default) -->
    <div id="card-view" class="hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="karyawan-card-container">
            @forelse($karyawans ?? [] as $karyawan)
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center space-x-4 mb-4">
                    @if($karyawan->photo)
                        <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $karyawan->photo) }}" alt="{{ $karyawan->full_name }}">
                    @else
                        <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center">
                            <span class="text-primary-600 font-bold text-lg">{{ strtoupper(substr($karyawan->full_name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $karyawan->full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $karyawan->position }}</p>
                        <p class="text-sm text-gray-500 font-mono">{{ $karyawan->nip }}</p>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Department:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $karyawan->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Status:</span>
                        @php
                            $statusColors = [
                                'aktif' => 'bg-green-100 text-green-800',
                                'tidak_aktif' => 'bg-red-100 text-red-800',
                                'cuti' => 'bg-yellow-100 text-yellow-800',
                            ];
                            $statusColor = $statusColors[$karyawan->employment_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                            {{ ucfirst($karyawan->employment_status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Bergabung:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $karyawan->hire_date ? $karyawan->hire_date->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.karyawan.show', $karyawan->karyawan_id) }}"
                       class="px-3 py-2 text-sm text-primary-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-colors">
                        Detail
                    </a>
                    <a href="{{ route('admin.karyawan.edit', $karyawan->karyawan_id) }}"
                       class="px-3 py-2 text-sm text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg transition-colors">
                        Edit
                    </a>
                    <button onclick="deleteKaryawan('{{ $karyawan->karyawan_id }}', '{{ $karyawan->full_name }}')"
                            class="px-3 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0Z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data karyawan</h3>
                <p class="text-gray-500 mb-4">Mulai dengan menambahkan karyawan pertama</p>
                <a href="{{ route('admin.karyawan.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Tambah Karyawan
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($karyawans) && $karyawans->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $karyawans->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions (when items selected) -->
<div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-3 hidden">
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600"><span id="selected-count">0</span> item dipilih</span>
        <div class="flex gap-2">
            <button onclick="bulkExport()" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Export Terpilih
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
    // Search functionality
    const searchInput = document.getElementById('search');
    const departmentFilter = document.getElementById('filter-department');
    const statusFilter = document.getElementById('filter-status');

    let searchTimeout;

    function performSearch() {
        const searchTerm = searchInput.value;
        const department = departmentFilter.value;
        const status = statusFilter.value;

        // Build URL with parameters
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (department) params.append('department', department);
        if (status) params.append('status', status);

        // Redirect with filters
        window.location.href = `{{ route('admin.karyawan.index') }}?${params.toString()}`;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    departmentFilter.addEventListener('change', performSearch);
    statusFilter.addEventListener('change', performSearch);

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_karyawan[]"]');
    const bulkActionsDiv = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            bulkActionsDiv.classList.remove('hidden');
            selectedCountSpan.textContent = count;
        } else {
            bulkActionsDiv.classList.add('hidden');
        }

        // Update select all checkbox state
        if (count === individualCheckboxes.length && count > 0) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (count > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            individualCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox functionality
    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Initialize
    updateBulkActions();
});

// View toggle functionality
let currentView = 'table';

function setView(view) {
    currentView = view;
    const tableView = document.getElementById('table-view');
    const cardView = document.getElementById('card-view');
    const tableBtn = document.getElementById('table-view-btn');
    const cardBtn = document.getElementById('card-view-btn');

    if (view === 'table') {
        tableView.classList.remove('hidden');
        cardView.classList.add('hidden');
        tableBtn.classList.remove('bg-gray-100', 'text-gray-600');
        tableBtn.classList.add('bg-primary-600', 'text-white');
        cardBtn.classList.remove('bg-primary-600', 'text-white');
        cardBtn.classList.add('bg-gray-100', 'text-gray-600');
    } else {
        tableView.classList.add('hidden');
        cardView.classList.remove('hidden');
        cardBtn.classList.remove('bg-gray-100', 'text-gray-600');
        cardBtn.classList.add('bg-primary-600', 'text-white');
        tableBtn.classList.remove('bg-primary-600', 'text-white');
        tableBtn.classList.add('bg-gray-100', 'text-gray-600');
    }

    // Save preference to localStorage
    localStorage.setItem('karyawanViewPreference', view);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('karyawanViewPreference');
    if (savedView && savedView !== 'table') {
        setView(savedView);
    }
});

// Delete functionality
function deleteKaryawan(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus karyawan "${name}"?\n\nPerhatian: Data absensi dan jadwal yang terkait juga akan terpengaruh.`)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.karyawan.index') }}/${id}`;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk actions
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih karyawan yang ingin dihapus');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus ${count} karyawan terpilih?\n\nPerhatian: Data absensi dan jadwal yang terkait juga akan terpengaruh.`)) {
        showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.karyawan.bulk-delete') }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(tokenInput);

        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'karyawan_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function bulkExport() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_karyawan[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih karyawan yang ingin diexport');
        return;
    }

    showLoading();

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.karyawan.export') }}';

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(tokenInput);

    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'karyawan_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();

    // Hide loading after a delay since this is a download
    setTimeout(hideLoading, 2000);
}

function exportData() {
    showLoading();

    // Get current filters
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route('admin.karyawan.export') }}?' + searchParams.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'data-karyawan.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Hide loading after a delay since this is a download
    setTimeout(hideLoading, 2000);
}

function clearSelection() {
    document.querySelectorAll('input[name="selected_karyawan[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    document.getElementById('bulk-actions').classList.add('hidden');
}

// Auto-refresh every 5 minutes for live data
setInterval(function() {
    // Only refresh if no modal is open and no checkboxes are selected
    const hasSelection = document.querySelectorAll('input[name="selected_karyawan[]"]:checked').length > 0;
    const hasModal = document.querySelector('.modal:not(.hidden)');

    if (!hasSelection && !hasModal) {
        // Refresh page with current filters
        window.location.reload();
    }
}, 300000); // 5 minutes

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + A to select all (when focused on table)
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.target.closest('#table-view')) {
        e.preventDefault();
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.checked = true;
            selectAll.dispatchEvent(new Event('change'));
        }
    }

    // Escape to clear selection
    if (e.key === 'Escape') {
        clearSelection();
    }

    // Ctrl/Cmd + E to export
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportData();
    }
});

// Table sorting (if needed)
function sortTable(columnIndex, direction = 'asc') {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('sort', columnIndex);
    currentUrl.searchParams.set('direction', direction);
    window.location.href = currentUrl.toString();
}

// Print functionality
function printKaryawan() {
    const printWindow = window.open('', '_blank');
    const printContent = document.getElementById(currentView === 'table' ? 'table-view' : 'card-view').outerHTML;

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Data Karyawan</title>
            <link href="https://cdn.tailwindcss.com" rel="stylesheet">
            <style>
                @media print {
                    .no-print { display: none !important; }
                    body { margin: 0; }
                    table { page-break-inside: auto; }
                    tr { page-break-inside: avoid; page-break-after: auto; }
                }
            </style>
        </head>
        <body class="bg-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold mb-4">Data Karyawan</h1>
                <p class="text-gray-600 mb-6">Dicetak pada: ${new Date().toLocaleDateString('id-ID')}</p>
                ${printContent}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

// Add print button to page actions (you can add this to the template)
document.addEventListener('DOMContentLoaded', function() {
    // Add print button after export button
    const exportBtn = document.querySelector('button[onclick="exportData()"]');
    if (exportBtn) {
        const printBtn = document.createElement('button');
        printBtn.onclick = printKaryawan;
        printBtn.className = 'px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2';
        printBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-1a2 2 0 00-2-2H9a2 2 0 00-2 2v1a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        `;
        exportBtn.parentNode.insertBefore(printBtn, exportBtn.nextSibling);
    }
});
</script>
@endpush

@push('styles')
<style>
/* Additional responsive improvements */
@media (max-width: 768px) {
    .mobile-container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Stack stats cards on mobile */
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    /* Responsive table */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive table {
        min-width: 800px;
    }

    /* Mobile card improvements */
    #card-view .grid {
        grid-template-columns: 1fr;
    }
}

/* Improved loading states */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Better hover states */
tr:hover .action-buttons {
    opacity: 1;
}

.action-buttons {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

/* Bulk actions animation */
#bulk-actions {
    animation: slideUpFadeIn 0.3s ease-out;
}

@keyframes slideUpFadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, 20px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

/* Status indicators */
.status-active::before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    margin-right: 8px;
    animation: pulse 2s infinite;
}

.status-inactive::before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #ef4444;
    border-radius: 50%;
    margin-right: 8px;
}

/* Print styles */
@media print {
    .no-print,
    .action-buttons,
    #bulk-actions,
    .page-actions {
        display: none !important;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }

    table {
        font-size: 12px;
    }

    .stats-grid {
        display: none;
    }
}
</style>
@endpush
