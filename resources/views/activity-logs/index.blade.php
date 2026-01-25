@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@push('styles')
<style>
    .log-card {
        transition: all 0.2s ease;
    }
    .log-card:hover {
        background-color: #f8fafc;
    }
    .badge-action {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_today']) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Login Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['logins_today']) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Perubahan Data</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['changes_today']) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Error Hari Ini</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['errors_today']) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                <select name="action" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Action</option>
                    @foreach($filterOptions['actions'] as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                <select name="module" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Module</option>
                    @foreach($filterOptions['modules'] as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                <select name="platform" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Platform</option>
                    @foreach($filterOptions['platforms'] as $platform)
                        <option value="{{ $platform }}" {{ request('platform') == $platform ? 'selected' : '' }}>
                            {{ ucfirst($platform) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Activity Logs Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="log-card">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->user_name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $log->user_id }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $actionColors = [
                                    'login' => 'bg-green-100 text-green-800',
                                    'logout' => 'bg-gray-100 text-gray-800',
                                    'login_failed' => 'bg-red-100 text-red-800',
                                    'create' => 'bg-blue-100 text-blue-800',
                                    'update' => 'bg-yellow-100 text-yellow-800',
                                    'delete' => 'bg-red-100 text-red-800',
                                    'approve' => 'bg-green-100 text-green-800',
                                    'reject' => 'bg-red-100 text-red-800',
                                    'submit' => 'bg-purple-100 text-purple-800',
                                    'error' => 'bg-red-100 text-red-800',
                                    'export' => 'bg-indigo-100 text-indigo-800',
                                    'import' => 'bg-indigo-100 text-indigo-800',
                                ];
                                $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="badge-action inline-flex rounded-full {{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->module ?? '-' }}
                            @if($log->module_id)
                                <span class="text-xs text-gray-500">({{ $log->module_id }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate" title="{{ $log->description }}">
                            {{ Str::limit($log->description, 50) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($log->platform === 'mobile')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z"/></svg>
                                    Mobile
                                </span>
                            @elseif($log->platform === 'web')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"/></svg>
                                    Web
                                </span>
                            @else
                                <span class="text-gray-500">{{ ucfirst($log->platform) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 font-mono">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada activity log
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
