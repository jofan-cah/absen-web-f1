@extends('admin.layouts.app')

@section('title', 'Detail Activity Log')
@section('page-title', 'Detail Activity Log')

@section('content')
<div class="space-y-6">
    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Basic Info --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">ID</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $log->id }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Waktu</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $log->created_at->format('d M Y H:i:s') }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Action</dt>
                    <dd>
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
                            ];
                            $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $color }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Module</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $log->module ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Module ID</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $log->module_id ?? '-' }}</dd>
                </div>
                <div class="py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500 mb-1">Deskripsi</dt>
                    <dd class="text-sm text-gray-900">{{ $log->description }}</dd>
                </div>
            </dl>
        </div>

        {{-- User & Request Info --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">User & Request Info</h3>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">User</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $log->user_name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">User ID</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $log->user_id ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Karyawan ID</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $log->karyawan_id ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Platform</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ ucfirst($log->platform) }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Device Type</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ ucfirst($log->device_type ?? '-') }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">IP Address</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $log->ip_address ?? '-' }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">Request Method</dt>
                    <dd class="text-sm font-mono text-gray-900">{{ $log->request_method ?? '-' }}</dd>
                </div>
                <div class="py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500 mb-1">Request URL</dt>
                    <dd class="text-xs font-mono text-gray-700 break-all">{{ $log->request_url ?? '-' }}</dd>
                </div>
                <div class="py-2">
                    <dt class="text-sm text-gray-500 mb-1">User Agent</dt>
                    <dd class="text-xs text-gray-700 break-all">{{ $log->user_agent ?? '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Data Changes --}}
    @if($log->old_data || $log->new_data || $log->changed_fields)
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Perubahan</h3>

        @if($log->changed_fields)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Fields yang Berubah</h4>
            <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="pb-2 pr-4">Field</th>
                            <th class="pb-2 pr-4">Nilai Lama</th>
                            <th class="pb-2">Nilai Baru</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach($log->changed_fields as $field => $changes)
                        <tr class="border-t border-gray-200">
                            <td class="py-2 pr-4 font-medium">{{ $field }}</td>
                            <td class="py-2 pr-4 text-red-600">
                                <code class="bg-red-50 px-1 rounded">{{ is_array($changes['old']) ? json_encode($changes['old']) : $changes['old'] }}</code>
                            </td>
                            <td class="py-2 text-green-600">
                                <code class="bg-green-50 px-1 rounded">{{ is_array($changes['new']) ? json_encode($changes['new']) : $changes['new'] }}</code>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @if($log->old_data)
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Data Sebelum</h4>
                <div class="bg-red-50 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            @if($log->new_data)
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Data Sesudah</h4>
                <div class="bg-green-50 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Error Info --}}
    @if($log->error_message || $log->error_trace)
    <div class="bg-white rounded-xl shadow-sm p-6 border border-red-200">
        <h3 class="text-lg font-semibold text-red-800 mb-4">Error Information</h3>

        @if($log->error_message)
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Error Message</h4>
            <div class="bg-red-50 rounded-lg p-4">
                <p class="text-sm text-red-700">{{ $log->error_message }}</p>
            </div>
        </div>
        @endif

        @if($log->error_trace)
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Stack Trace</h4>
            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                <pre class="text-xs text-green-400 whitespace-pre-wrap">{{ $log->error_trace }}</pre>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
