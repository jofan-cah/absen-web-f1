@extends('admin.layouts.app')

@section('title', 'Detail Tukar Shift')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.shift-swap.indexSw') }}"
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Tukar Shift</h1>
                <p class="text-sm text-gray-600 mt-1">ID: <span class="font-mono">{{ $swapRequest->swap_id }}</span></p>
            </div>
        </div>

        <!-- Status Badge -->
        <div>
            @if($swapRequest->status == 'pending_admin_approval')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-orange-100 text-orange-800 border border-orange-200">
                    <div class="w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></div>
                    Pending Approval
                </span>
            @elseif($swapRequest->status == 'completed')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Completed
                </span>
            @elseif($swapRequest->status == 'rejected_by_admin')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Rejected by Admin
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Main Info -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Swap Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-red-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Informasi Tukar Shift
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Requester Side -->
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center mb-3">
                                <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">
                                        {{ substr($swapRequest->requesterKaryawan->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 font-medium">REQUESTER</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $swapRequest->requesterKaryawan->name }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NIP:</span>
                                    <span class="font-medium text-gray-900">{{ $swapRequest->requesterKaryawan->employee_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Department:</span>
                                    <span class="font-medium text-gray-900">{{ $swapRequest->requesterKaryawan->department->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tanggal:</span>
                                    <span class="font-medium text-gray-900">{{ $swapRequest->requesterJadwal->date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Shift Awal:</span>
                                    <div class="text-right">
                                        <div class="font-bold text-blue-700">{{ $swapRequest->requesterJadwal->shift->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($swapRequest->requesterJadwal->shift->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($swapRequest->requesterJadwal->shift->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Swap Arrow -->
                        <div class="hidden md:flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 z-10">
                            <div class="bg-white rounded-full p-3 shadow-lg border-2 border-orange-300">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                        </div>

                        <!-- Partner Side -->
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center mb-3">
                                <div class="h-12 w-12 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-lg">
                                        {{ substr($swapRequest->partnerKaryawan->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-green-600 font-medium">PARTNER</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $swapRequest->partnerKaryawan->name }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">NIP:</span>
                                    <span class="font-medium text-gray-900">{{ $swapRequest->partnerKaryawan->employee_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Department:</span>
                                    <span class="font-medium text-gray-900">{{ $swapRequest->partnerKaryawan->department->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tanggal:</span>
                                    <span class="font-medium text-gray-900">{{ $swapRequest->partnerJadwal->date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Shift Awal:</span>
                                    <div class="text-right">
                                        <div class="font-bold text-green-700">{{ $swapRequest->partnerJadwal->shift->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($swapRequest->partnerJadwal->shift->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($swapRequest->partnerJadwal->shift->end_time)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alasan -->
                    @if($swapRequest->reason)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-medium text-gray-600 uppercase mb-2">Alasan Tukar Shift</p>
                        <p class="text-sm text-gray-900">{{ $swapRequest->reason }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Timeline Review
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">

                        <!-- Step 1: Request Created -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Request Dibuat</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $swapRequest->created_at->format('d F Y, H:i') }} WIB</p>
                                <p class="text-sm text-gray-600 mt-1">Oleh {{ $swapRequest->requesterKaryawan->name }}</p>
                            </div>
                        </div>

                        <!-- Step 2: Partner Approval -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full {{ $swapRequest->status == 'pending_partner' ? 'bg-yellow-100' : 'bg-green-100' }} flex items-center justify-center">
                                    @if($swapRequest->status == 'pending_partner')
                                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Partner Review</h4>
                                @if($swapRequest->partner_response_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $swapRequest->partner_response_at->format('d F Y, H:i') }} WIB</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $swapRequest->partnerKaryawan->name }} menyetujui</p>
                                    @if($swapRequest->partner_notes)
                                        <p class="text-xs text-gray-500 mt-2 italic">"{{ $swapRequest->partner_notes }}"</p>
                                    @endif
                                @else
                                    <p class="text-xs text-yellow-600 mt-1">Menunggu approval dari partner...</p>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Admin Approval -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full
                                    {{ $swapRequest->status == 'pending_admin_approval' ? 'bg-orange-100' :
                                       ($swapRequest->status == 'completed' ? 'bg-green-100' : 'bg-red-100') }}
                                    flex items-center justify-center">
                                    @if($swapRequest->status == 'pending_admin_approval')
                                        <svg class="w-5 h-5 text-orange-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($swapRequest->status == 'completed')
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Admin Approval</h4>
                                @if($swapRequest->admin_approved_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $swapRequest->admin_approved_at->format('d F Y, H:i') }} WIB</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Oleh {{ $swapRequest->approvedByAdmin->name ?? 'Admin' }}
                                    </p>
                                    @if($swapRequest->admin_notes)
                                        <p class="text-xs text-gray-500 mt-2 italic">"{{ $swapRequest->admin_notes }}"</p>
                                    @endif
                                @else
                                    <p class="text-xs text-orange-600 mt-1">Menunggu approval admin...</p>
                                @endif
                            </div>
                        </div>

                        <!-- Step 4: Completed -->
                        @if($swapRequest->completed_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">Swap Completed</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $swapRequest->completed_at->format('d F Y, H:i') }} WIB</p>
                                <p class="text-sm text-gray-600 mt-1">Jadwal berhasil ditukar</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Actions -->
        <div class="space-y-6">

            <!-- Action Card -->
            @if($swapRequest->status == 'pending_admin_approval')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Aksi</h2>
                </div>
                <div class="p-6 space-y-3">
                    <button onclick="approveSwap()"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Approve Swap
                    </button>
                    <button onclick="rejectSwap()"
                            class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Reject Swap
                    </button>
                </div>
            </div>
            @endif

            <!-- Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi</h2>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dibuat:</span>
                        <span class="font-medium text-gray-900">{{ $swapRequest->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diupdate:</span>
                        <span class="font-medium text-gray-900">{{ $swapRequest->updated_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($swapRequest->completed_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Selesai:</span>
                        <span class="font-medium text-gray-900">{{ $swapRequest->completed_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Approve Tukar Shift</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.shift-swap.approveSw', $swapRequest->swap_id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="admin_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeApproveModal()"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Tukar Shift</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.shift-swap.reject', $swapRequest->swap_id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="admin_notes" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function approveSwap() {
    document.getElementById('approve-modal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
}

function rejectSwap() {
    document.getElementById('reject-modal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endpush

@endsection
