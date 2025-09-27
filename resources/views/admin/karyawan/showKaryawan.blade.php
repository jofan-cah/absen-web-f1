@extends('admin.layouts.app')

@section('title', 'Detail Karyawan')
@section('breadcrumb', 'Detail Karyawan')
@section('page_title', 'Detail Karyawan')

@section('page_actions')
<div class="flex items-center space-x-3">
    <a href="{{ route('admin.karyawan.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('admin.karyawan.edit', $karyawan->karyawan_id) }}"
       class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm font-medium transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Profile Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="relative">
            <!-- Background -->
            <div class="h-32 bg-gradient-to-r from-primary-600 via-purple-600 to-indigo-700"></div>

            <!-- Profile Content -->
            <div class="relative px-6 pb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-end space-y-4 sm:space-y-0 sm:space-x-6">
                    <!-- Avatar -->
                    <div class="relative -mt-16">
                        <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-white shadow-xl bg-white">
                            @if($karyawan->photo)

                                 <img src="{{ Storage::disk('s3')->url($karyawan->photo) }}"
                                     alt="{{ $karyawan->full_name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-3xl font-bold text-white">
                                        {{ strtoupper(substr($karyawan->full_name, 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <div class="absolute -bottom-2 -right-2">
                            @if($karyawan->employment_status === 'aktif')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border-2 border-white">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                    Aktif
                                </span>
                            @elseif($karyawan->employment_status === 'tidak_aktif')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border-2 border-white">
                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                    Tidak Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border-2 border-white">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
                                    {{ ucfirst($karyawan->employment_status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $karyawan->full_name }}</h1>
                                <p class="text-lg text-gray-600">{{ $karyawan->position }}</p>
                                <div class="flex items-center mt-1 text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $karyawan->department->name ?? 'Belum ada department' }}
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                                <!-- Reset Password -->
                                @if($karyawan->user)
                                <button onclick="openResetPasswordModal()"
                                        class="inline-flex items-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Reset Password
                                </button>
                                @endif

                                <!-- Toggle Status -->
                                <form method="POST" action="{{ route('admin.karyawan.toggle-status', $karyawan->karyawan_id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 {{ $karyawan->employment_status === 'aktif' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg text-sm font-medium transition-colors duration-200"
                                            onclick="return confirm('Apakah Anda yakin ingin {{ $karyawan->employment_status === 'aktif' ? 'menonaktifkan' : 'mengaktifkan' }} karyawan ini?')">
                                        @if($karyawan->employment_status === 'aktif')
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                            </svg>
                                            Nonaktifkan
                                        @else
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Aktifkan
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Hadir -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Hadir</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_hadir'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 font-medium mt-1">Bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Alpha -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Alpha</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_alpha'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 font-medium mt-1">Bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Terlambat -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Datang Terlambat</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_terlambat'] ?? 0 }}</p>
                    <p class="text-xs text-yellow-600 font-medium mt-1">Bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Jam Kerja -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Jam Kerja</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_jam_kerja'] ?? 0, 1) }}</p>
                    <p class="text-xs text-purple-600 font-medium mt-1">Jam bulan ini</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Information & Recent Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Detail Information -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Informasi Detail</h3>

                <div class="space-y-4">
                    <!-- NIP & ID -->
                    <div>
                        <label class="text-sm font-medium text-gray-500">NIP</label>
                        <p class="text-sm font-mono text-gray-900">{{ $karyawan->nip }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Karyawan ID</label>
                        <p class="text-sm font-mono text-gray-900">{{ $karyawan->karyawan_id }}</p>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Contact Info -->
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->user->email ?? 'Belum ada email' }}</p>
                    </div>

                    @if($karyawan->phone)
                    <div>
                        <label class="text-sm font-medium text-gray-500">No. Telepon</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->phone }}</p>
                    </div>
                    @endif

                    @if($karyawan->address)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Alamat</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->address }}</p>
                    </div>
                    @endif

                    <hr class="border-gray-200">

                    <!-- Personal Info -->
                    @if($karyawan->birth_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tanggal Lahir</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->birth_date->format('d F Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $karyawan->birth_date->age }} tahun</p>
                    </div>
                    @endif

                    @if($karyawan->gender)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Jenis Kelamin</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    @endif

                    <hr class="border-gray-200">

                    <!-- Work Info -->
                    <div>
                        <label class="text-sm font-medium text-gray-500">Department</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->department->name ?? 'Belum ada department' }}</p>
                        @if($karyawan->department)
                        <p class="text-xs text-gray-500">{{ $karyawan->department->code }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Posisi</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->position }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Status Staff</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $karyawan->staff_status == 'koordinator' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $karyawan->staff_status == 'wakil_koordinator' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $karyawan->staff_status == 'staff' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $karyawan->staff_status)) }}
                        </span>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Status Karyawan</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $karyawan->employment_status == 'aktif' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $karyawan->employment_status == 'tidak_aktif' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $karyawan->employment_status == 'cuti' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $karyawan->employment_status)) }}
                        </span>
                    </div>

                    @if($karyawan->hire_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Tanggal Bergabung</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->hire_date->format('d F Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $karyawan->hire_date->diffForHumans() }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-500">Terdaftar</label>
                        <p class="text-sm text-gray-900">{{ $karyawan->created_at->format('d F Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $karyawan->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Absensi Bulan Ini</h3>
                    <span class="text-sm text-gray-500">{{ now()->format('F Y') }}</span>
                </div>

                @if($recentAbsens && $recentAbsens->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="text-left py-3 px-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                                    <th class="text-left py-3 px-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                                    <th class="text-left py-3 px-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-left py-3 px-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($recentAbsens as $absen)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-3 text-sm text-gray-900">
                                            {{ $absen->date->format('d M Y') }}
                                            <div class="text-xs text-gray-500">{{ $absen->date->format('l') }}</div>
                                        </td>
                                        <td class="py-3 px-3 text-sm text-gray-900">
                                            @if($absen->clock_in)
                                                {{ \Carbon\Carbon::parse($absen->clock_in)->format('H:i') }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-sm text-gray-900">
                                            @if($absen->clock_out)
                                                {{ \Carbon\Carbon::parse($absen->clock_out)->format('H:i') }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $absen->status == 'present' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $absen->status == 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $absen->status == 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $absen->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ !in_array($absen->status, ['present', 'late', 'absent', 'scheduled']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                                @if($absen->status == 'present')
                                                    Hadir
                                                @elseif($absen->status == 'late')
                                                    Terlambat
                                                @elseif($absen->status == 'absent')
                                                    Alpha
                                                @elseif($absen->status == 'scheduled')
                                                    Terjadwal
                                                @else
                                                    {{ ucfirst($absen->status) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-sm text-gray-900">
                                            @if($absen->late_minutes && $absen->late_minutes > 0)
                                                <span class="text-red-600 font-medium">{{ $absen->late_minutes }} menit</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- View All Link -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.absen.index', ['karyawan' => $karyawan->karyawan_id]) }}"
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Lihat Semua Riwayat Absensi →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 text-sm">Belum ada data absensi bulan ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
@if($karyawan->user)
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reset Password</h3>
            <form action="{{ route('admin.karyawan.reset-password', $karyawan->karyawan_id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Karyawan</label>
                    <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded">{{ $karyawan->full_name }}</p>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeResetPasswordModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
    document.querySelector('#resetPasswordModal form').reset();
}

// Close modal when clicking outside
document.getElementById('resetPasswordModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetPasswordModal();
    }
});
</script>
@endpush
