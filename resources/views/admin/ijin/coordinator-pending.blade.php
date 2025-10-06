@extends('admin.layouts.app')

@section('title', 'Pending Review - Koordinator')
@section('page_title', 'Pending Review')
@section('breadcrumb', 'Ijin / Pending Review')

@section('content')
    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Review Ijin - Koordinator</h1>
                        <p class="text-sm text-gray-600 mt-1">Daftar pengajuan ijin yang menunggu review Anda</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.ijin.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Semua Ijin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Notice -->
    @if($ijins->count() > 0)
        <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-blue-800">
                        <strong>Catatan:</strong> Anda memiliki <strong>{{ $ijins->total() }}</strong> pengajuan ijin yang menunggu review.
                        Approve akan meneruskan ke admin untuk approval final, sedangkan reject akan langsung menolak ijin.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Review</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $ijins->total() }}</p>
                    <p class="text-sm text-yellow-600 mt-1">Menunggu review Anda</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Ijin Anda</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ \App\Models\Ijin::where('coordinator_id', auth()->user()->user_id)->count() }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">Semua ijin yang ditugaskan</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Ijin Pending</h3>
                <span class="text-sm text-gray-500">Total: {{ $ijins->total() }} data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Karyawan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe Ijin</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pengajuan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ijins as $ijin)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($ijin->karyawan->full_name, 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ijin->karyawan->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $ijin->karyawan->nip }} • {{ $ijin->karyawan->department->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-rose-500 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $ijin->ijinType->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $ijin->total_days }} hari</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($ijin->date_from)->format('d M Y') }}</div>
                                @if ($ijin->date_from != $ijin->date_to)
                                    <div class="text-xs text-gray-500">s/d {{ \Carbon\Carbon::parse($ijin->date_to)->format('d M Y') }}</div>
                                @endif
                                @if ($ijin->ijinType->code === 'shift_swap' && $ijin->original_shift_date)
                                    <div class="text-xs text-blue-600 mt-1">
                                        <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($ijin->original_shift_date)->format('d M') }}
                                        @if ($ijin->replacement_shift_date)
                                            → {{ \Carbon\Carbon::parse($ijin->replacement_shift_date)->format('d M') }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="text-sm text-gray-900">{{ $ijin->created_at->diffForHumans() }}</div>
                                <div class="text-xs text-gray-500">{{ $ijin->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.ijin.show', $ijin->ijin_id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.ijin.coordinator-review-form', $ijin->ijin_id) }}" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 text-xs font-medium transition-colors" title="Review Ijin">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        Review
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 text-lg mb-4">Tidak ada ijin yang perlu direview</p>
                                <p class="text-gray-400 text-sm">Semua pengajuan ijin sudah Anda review</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($ijins->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ $ijins->firstItem() }}</span> -
                        <span class="font-medium">{{ $ijins->lastItem() }}</span> dari
                        <span class="font-medium">{{ $ijins->total() }}</span> data
                    </div>
                    <div>
                        {{ $ijins->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Action Panel -->
    @if($ijins->count() > 0)
        <div class="mt-6 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">Tips Review Ijin</h3>
                    <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                        <li>Periksa alasan pengajuan ijin dengan teliti</li>
                        <li>Pastikan tanggal ijin tidak bentrok dengan jadwal penting</li>
                        <li>Untuk shift swap, cek apakah tanggal pengganti masuk akal</li>
                        <li>Jika reject, berikan catatan yang jelas agar karyawan mengerti</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endsection
