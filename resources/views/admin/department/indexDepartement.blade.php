@extends('admin.layouts.app')

@section('title', 'Data Department')
@section('breadcrumb', 'Data Department')
@section('page_title', 'Data Department')

@section('page_actions')
<div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
    <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <input type="text" id="search" placeholder="Cari department..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Filter Status -->
        <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Tidak Aktif</option>
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
        <a href="{{ route('admin.department.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Department
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
                <p class="text-sm font-medium text-gray-600">Total Department</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $departments->total() ?? 0 }}</p>
                <p class="text-sm text-blue-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                    Unit kerja
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Department Aktif</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $departments->where('is_active', true)->count() ?? 0 }}</p>
                <p class="text-sm text-green-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Beroperasi
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
                <p class="text-sm font-medium text-gray-600">Total Karyawan</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $departments->sum('karyawans_count') ?? 0 }}</p>
                <p class="text-sm text-purple-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0Z"/>
                    </svg>
                    Semua department
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0Z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Dengan Manager</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $departments->whereNotNull('manager_user_id')->count() ?? 0 }}</p>
                <p class="text-sm text-orange-600 mt-1">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Ada manager
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
            <h3 class="text-lg font-semibold text-gray-900">Daftar Department</h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Total: <span id="total-records">{{ $departments->total() ?? 0 }}</span> data</span>

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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="department-table-body">
                @forelse($departments ?? [] as $department)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" name="selected_departments[]" value="{{ $department->department_id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">{{ strtoupper(substr($department->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $department->name }}</div>
                                @if($department->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($department->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $department->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($department->manager)
                            <div class="flex items-center">
                                <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $department->manager->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $department->manager->email }}</div>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">Belum ada manager</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="text-lg font-semibold text-gray-900">{{ $department->karyawans_count }}</span>
                            <span class="text-sm text-gray-500 ml-1">orang</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($department->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $department->created_at->format('d M Y') }}
                        <div class="text-xs text-gray-400">{{ $department->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.department.show', $department->department_id) }}"
                               class="text-primary-600 hover:text-primary-700 p-1 rounded hover:bg-primary-50 transition-colors"
                               title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.department.edit', $department->department_id) }}"
                               class="text-yellow-600 hover:text-yellow-700 p-1 rounded hover:bg-yellow-50 transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button onclick="toggleStatus('{{ $department->department_id }}', {{ $department->is_active ? 'false' : 'true' }})"
                                    class="text-{{ $department->is_active ? 'red' : 'green' }}-600 hover:text-{{ $department->is_active ? 'red' : 'green' }}-700 p-1 rounded hover:bg-{{ $department->is_active ? 'red' : 'green' }}-50 transition-colors"
                                    title="{{ $department->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                @if($department->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                            <button onclick="deleteDepartment('{{ $department->department_id }}', '{{ $department->name }}')"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data department</h3>
                            <p class="text-gray-500 mb-4">Mulai dengan menambahkan department pertama</p>
                            <a href="{{ route('admin.department.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                Tambah Department
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="department-card-container">
            @forelse($departments ?? [] as $department)
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($department->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $department->name }}</h3>
                            <p class="text-sm text-gray-500 font-mono">{{ $department->code }}</p>
                        </div>
                    </div>
                    @if($department->is_active)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                @if($department->description)
                <div class="mb-4">
                    <p class="text-sm text-gray-600">{{ Str::limit($department->description, 100) }}</p>
                </div>
                @endif

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Manager:</span>
                        @if($department->manager)
                            <span class="text-sm font-medium text-gray-900">{{ $department->manager->name }}</span>
                        @else
                            <span class="text-sm text-gray-400">Belum ada</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Karyawan:</span>
                        <span class="text-sm font-bold text-gray-900">{{ $department->karyawans_count }} orang</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Dibuat:</span>
                        <span class="text-sm text-gray-900">{{ $department->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="flex justify-between gap-2">
                    <a href="{{ route('admin.department.show', $department->department_id) }}"
                       class="flex-1 px-3 py-2 text-sm text-primary-600 hover:text-primary-700 hover:bg-primary-50 rounded-lg transition-colors text-center">
                        Detail
                    </a>
                    <a href="{{ route('admin.department.edit', $department->department_id) }}"
                       class="flex-1 px-3 py-2 text-sm text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg transition-colors text-center">
                        Edit
                    </a>
                    <button onclick="deleteDepartment('{{ $department->department_id }}', '{{ $department->name }}')"
                            class="flex-1 px-3 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data department</h3>
                <p class="text-gray-500 mb-4">Mulai dengan menambahkan department pertama</p>
                <a href="{{ route('admin.department.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Tambah Department
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($departments) && $departments->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $departments->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions (when items selected) -->
<div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 px-6 py-3 hidden">
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600"><span id="selected-count">0</span> item dipilih</span>
        <div class="flex gap-2">
            <button onclick="bulkToggleStatus()" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Toggle Status
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
    const statusFilter = document.getElementById('filter-status');

    let searchTimeout;

    function performSearch() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;

        // Build URL with parameters
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (status !== '') params.append('status', status);

        // Redirect with filters
        window.location.href = `{{ route('admin.department.index') }}?${params.toString()}`;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    statusFilter.addEventListener('change', performSearch);

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const individualCheckboxes = document.querySelectorAll('input[name="selected_departments[]"]');
    const bulkActionsDiv = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('input[name="selected_departments[]"]:checked');
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
    localStorage.setItem('departmentViewPreference', view);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('departmentViewPreference');
    if (savedView && savedView !== 'table') {
        setView(savedView);
    }
});

// Delete functionality
function deleteDepartment(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus department "${name}"?\n\nPerhatian: Department yang masih memiliki karyawan aktif tidak dapat dihapus.`)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.department.index') }}/${id}`;

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

// Toggle status functionality
function toggleStatus(id, newStatus) {
    const statusText = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';

    if (confirm(`Apakah Anda yakin ingin ${statusText} department ini?`)) {
        showLoading();

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.department.index') }}/${id}/toggle-status`;

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk actions
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_departments[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih department yang ingin dihapus');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus ${count} department terpilih?\n\nPerhatian: Department yang masih memiliki karyawan aktif tidak dapat dihapus.`)) {
        showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.department.bulk-delete") }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(tokenInput);

        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'department_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function bulkToggleStatus() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_departments[]"]:checked');
    const count = checkedBoxes.length;

    if (count === 0) {
        alert('Pilih department yang ingin diubah statusnya');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin mengubah status ${count} department terpilih?`)) {
        showLoading();

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.department.bulk-toggle-status") }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(tokenInput);

        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'department_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function exportData() {
    showLoading();

    // Get current filters
    const searchParams = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("admin.department.export") }}?' + searchParams.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = 'data-department.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Hide loading after a delay since this is a download
    setTimeout(hideLoading, 2000);
}

function clearSelection() {
    document.querySelectorAll('input[name="selected_departments[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    document.getElementById('bulk-actions').classList.add('hidden');
}

// Auto-refresh every 5 minutes for live data
setInterval(function() {
    // Only refresh if no modal is open and no checkboxes are selected
    const hasSelection = document.querySelectorAll('input[name="selected_departments[]"]:checked').length > 0;
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
