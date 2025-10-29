@extends('admin.layouts.app')

@section('title', 'Edit OnCall')

@section('content')
<div class="compact-content">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.oncall.show', $lembur->lembur_id) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit OnCall</h1>
                <p class="text-sm text-gray-600 mt-1">Update informasi OnCall untuk {{ $lembur->karyawan->full_name }}</p>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg animate-fade-in">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800 mb-2">Terdapat beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.oncall.update', $lembur->lembur_id) }}" method="POST" id="oncallForm">
                    @csrf
                    @method('PUT')

                    <!-- Info: Karyawan tidak bisa diganti -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Informasi:</p>
                                <p>Karyawan tidak dapat diganti saat edit. Hanya tanggal, jam mulai, dan deskripsi yang bisa diubah.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Karyawan (Read Only) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Karyawan
                        </label>
                        <div class="flex items-center p-3 bg-gray-50 border border-gray-300 rounded-lg">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold mr-3">
                                {{ substr($lembur->karyawan->full_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $lembur->karyawan->full_name }}</p>
                                <p class="text-xs text-gray-600">{{ $lembur->karyawan->nip }} - {{ $lembur->karyawan->department->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal OnCall -->
                    <div class="mb-6">
                        <label for="tanggal_oncall" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal OnCall <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_oncall" id="tanggal_oncall" required
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_oncall', $lembur->tanggal_lembur->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tanggal_oncall') border-red-500 @enderror">
                        @error('tanggal_oncall')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Tanggal tidak boleh lampau</p>
                    </div>

                    <!-- Jam Mulai (Estimasi) -->
                    <div class="mb-6">
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Mulai (Estimasi) <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="jam_mulai" id="jam_mulai" required
                               value="{{ old('jam_mulai', substr($lembur->jam_mulai, 0, 5)) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('jam_mulai') border-red-500 @enderror">
                        @error('jam_mulai')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Estimasi waktu karyawan mulai bekerja OnCall</p>
                    </div>

                    <!-- Deskripsi Pekerjaan -->
                    <div class="mb-6">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi Pekerjaan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" required maxlength="500"
                                  placeholder="Contoh: Maintenance server urgent, handle customer issue, dll..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $lembur->deskripsi_pekerjaan) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500" id="deskripsi-counter">{{ strlen($lembur->deskripsi_pekerjaan) }}/500 karakter</p>
                    </div>

                    <!-- Alert Warning -->
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Perhatian!</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Karyawan akan menerima <strong>notifikasi update</strong> di aplikasi mobile</li>
                                    <li>Jadwal dan Absen akan <strong>terupdate otomatis</strong></li>
                                    <li>Pastikan data yang diubah sudah <strong>benar</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update OnCall
                        </button>
                        <a href="{{ route('admin.oncall.show', $lembur->lembur_id) }}"
                           class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-sm border border-yellow-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview Update
                </h3>

                <!-- Preview Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 space-y-3">
                    <!-- Karyawan Preview -->
                    <div class="flex items-center pb-3 border-b border-gray-200">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold text-lg shadow-md">
                            <span>{{ substr($lembur->karyawan->full_name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $lembur->karyawan->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $lembur->karyawan->nip }}</p>
                            <p class="text-xs text-gray-500">{{ $lembur->karyawan->department->name ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Tanggal & Jam Preview -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="ml-2 font-medium text-gray-900" id="preview-date">{{ $lembur->tanggal_lembur->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-600">Jam Mulai:</span>
                            <span class="ml-2 font-medium text-gray-900" id="preview-jam">{{ substr($lembur->jam_mulai, 0, 5) }}</span>
                        </div>
                    </div>

                    <!-- Deskripsi Preview -->
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-xs font-medium text-gray-500 mb-1">Deskripsi:</p>
                        <p class="text-sm text-gray-700" id="preview-deskripsi">{{ $lembur->deskripsi_pekerjaan }}</p>
                    </div>

                    <!-- Status Badge -->
                    <div class="pt-3 border-t border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Waiting Check-in
                        </span>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-4 p-3 bg-white rounded-lg border border-yellow-200">
                    <p class="text-xs text-gray-600">
                        <span class="font-semibold text-yellow-600">⚠️ Perhatian:</span> Perubahan akan langsung ternotifikasi ke karyawan. Pastikan data sudah benar sebelum update.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const tanggalInput = document.getElementById('tanggal_oncall');
    const jamInput = document.getElementById('jam_mulai');
    const deskripsiTextarea = document.getElementById('deskripsi');
    const form = document.getElementById('oncallForm');

    // Preview elements
    const previewDate = document.getElementById('preview-date');
    const previewJam = document.getElementById('preview-jam');
    const previewDeskripsi = document.getElementById('preview-deskripsi');

    // Update tanggal preview
    tanggalInput.addEventListener('change', function() {
        if (this.value) {
            const date = new Date(this.value);
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
            previewDate.textContent = date.toLocaleDateString('id-ID', options);
        }
    });

    // Update jam preview
    jamInput.addEventListener('change', function() {
        previewJam.textContent = this.value || '{{ substr($lembur->jam_mulai, 0, 5) }}';
    });

    // Update deskripsi preview with counter
    deskripsiTextarea.addEventListener('input', function() {
        const maxLength = 500;
        const currentLength = this.value.length;
        const counter = document.getElementById('deskripsi-counter');

        counter.textContent = `${currentLength}/${maxLength} karakter`;

        if (currentLength > maxLength) {
            counter.className = 'mt-2 text-xs text-red-500';
            this.value = this.value.substring(0, maxLength);
        } else if (currentLength > 450) {
            counter.className = 'mt-2 text-xs text-orange-500';
        } else {
            counter.className = 'mt-2 text-xs text-gray-500';
        }

        // Update preview
        previewDeskripsi.textContent = this.value || '{{ $lembur->deskripsi_pekerjaan }}';
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        let hasError = false;
        const requiredFields = [tanggalInput, jamInput, deskripsiTextarea];

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                hasError = true;
                field.classList.add('border-red-500');

                // Show error message if not exists
                if (!field.parentNode.querySelector('.error-message')) {
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'mt-2 text-sm text-red-600 error-message';
                    errorMsg.textContent = 'Field ini wajib diisi';
                    field.parentNode.appendChild(errorMsg);
                }
            } else {
                field.classList.remove('border-red-500');
                const errorMsg = field.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });

        if (hasError) {
            e.preventDefault();

            // Scroll to first error
            const firstError = document.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    // Real-time validation on blur
    [tanggalInput, jamInput, deskripsiTextarea].forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });

        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
/* Smooth transitions */
input, select, textarea {
    transition: all 0.2s ease-in-out;
}

/* Border red animation */
.border-red-500 {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Fade in animation for alerts */
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button hover effect */
button, .btn {
    transition: all 0.2s ease-in-out;
}

button:hover, .btn:hover {
    transform: translateY(-1px);
}

button:active, .btn:active {
    transform: translateY(0);
}

/* Sticky preview on desktop */
@media (min-width: 1024px) {
    .sticky {
        position: sticky;
        top: 1.5rem;
    }
}
</style>
@endpush
