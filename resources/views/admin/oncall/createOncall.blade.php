@extends('admin.layouts.app')

@section('title', 'Assign OnCall')

@section('content')
<div class="compact-content">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.oncall.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assign OnCall</h1>
                <p class="text-sm text-gray-600 mt-1">Assign jadwal OnCall ke karyawan untuk pekerjaan urgent</p>
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
                <form action="{{ route('admin.oncall.store') }}" method="POST" id="oncallForm">
                    @csrf

                    <!-- Pilih Karyawan -->
                    <div class="mb-6">
                        <label for="karyawan_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Karyawan <span class="text-red-500">*</span>
                        </label>
                        <select name="karyawan_id" id="karyawan_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('karyawan_id') border-red-500 @enderror">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->karyawan_id }}"
                                        data-name="{{ $karyawan->full_name }}"
                                        data-nip="{{ $karyawan->nip }}"
                                        data-dept="{{ $karyawan->department->name ?? '-' }}"
                                        {{ old('karyawan_id') == $karyawan->karyawan_id ? 'selected' : '' }}>
                                    {{ $karyawan->full_name }} - {{ $karyawan->nip }} ({{ $karyawan->department->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal OnCall -->
                    <div class="mb-6">
                        <label for="tanggal_oncall" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal OnCall <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_oncall" id="tanggal_oncall" required
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_oncall', date('Y-m-d')) }}"
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
                               value="{{ old('jam_mulai', '20:00') }}"
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
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500" id="deskripsi-counter">0/500 karakter</p>
                    </div>

                    <!-- Alert Info -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Yang akan terjadi setelah assign OnCall:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Sistem akan membuat <strong>Jadwal OnCall</strong> untuk karyawan</li>
                                    <li>Sistem akan membuat <strong>Absen kosong</strong> (belum clock_in)</li>
                                    <li>Sistem akan membuat <strong>Lembur OnCall</strong> dengan status waiting_checkin</li>
                                    <li>Karyawan akan menerima <strong>notifikasi</strong> di aplikasi mobile</li>
                                    <li>Karyawan harus <strong>absen masuk & pulang</strong> seperti biasa</li>
                                    <li>Setelah selesai, karyawan wajib <strong>isi deskripsi & upload bukti foto</strong></li>
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
                            Assign OnCall
                        </button>
                        <a href="{{ route('admin.oncall.index') }}"
                           class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview OnCall
                </h3>

                <!-- Preview Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 space-y-3">
                    <!-- Karyawan Preview -->
                    <div class="flex items-center pb-3 border-b border-gray-200">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold text-lg shadow-md">
                            <span id="preview-initial">?</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-semibold text-gray-900" id="preview-name">Pilih karyawan</p>
                            <p class="text-xs text-gray-500" id="preview-nip">-</p>
                            <p class="text-xs text-gray-500" id="preview-dept">-</p>
                        </div>
                    </div>

                    <!-- Tanggal & Jam Preview -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="ml-2 font-medium text-gray-900" id="preview-date">{{ date('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-600">Jam Mulai:</span>
                            <span class="ml-2 font-medium text-gray-900" id="preview-jam">20:00</span>
                        </div>
                    </div>

                    <!-- Deskripsi Preview -->
                    <div class="pt-3 border-t border-gray-200">
                        <p class="text-xs font-medium text-gray-500 mb-1">Deskripsi:</p>
                        <p class="text-sm text-gray-700 italic" id="preview-deskripsi">Belum diisi...</p>
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
                <div class="mt-4 p-3 bg-white rounded-lg border border-blue-200">
                    <p class="text-xs text-gray-600">
                        <span class="font-semibold text-blue-600">💡 Tips:</span> Pastikan jam mulai sesuai dengan estimasi. Karyawan dapat melakukan check-in kapan saja setelah OnCall di-assign.
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
    const karyawanSelect = document.getElementById('karyawan_id');
    const tanggalInput = document.getElementById('tanggal_oncall');
    const jamInput = document.getElementById('jam_mulai');
    const deskripsiTextarea = document.getElementById('deskripsi');
    const form = document.getElementById('oncallForm');

    // Preview elements
    const previewInitial = document.getElementById('preview-initial');
    const previewName = document.getElementById('preview-name');
    const previewNip = document.getElementById('preview-nip');
    const previewDept = document.getElementById('preview-dept');
    const previewDate = document.getElementById('preview-date');
    const previewJam = document.getElementById('preview-jam');
    const previewDeskripsi = document.getElementById('preview-deskripsi');

    // Update karyawan preview
    karyawanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            const name = selectedOption.dataset.name;
            const nip = selectedOption.dataset.nip;
            const dept = selectedOption.dataset.dept;

            previewInitial.textContent = name.charAt(0).toUpperCase();
            previewName.textContent = name;
            previewNip.textContent = nip;
            previewDept.textContent = dept;
        } else {
            previewInitial.textContent = '?';
            previewName.textContent = 'Pilih karyawan';
            previewNip.textContent = '-';
            previewDept.textContent = '-';
        }
    });

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
        previewJam.textContent = this.value || '20:00';
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
        previewDeskripsi.textContent = this.value || 'Belum diisi...';
        previewDeskripsi.className = this.value ? 'text-sm text-gray-700' : 'text-sm text-gray-400 italic';
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        let hasError = false;
        const requiredFields = [karyawanSelect, tanggalInput, jamInput, deskripsiTextarea];

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
    [karyawanSelect, tanggalInput, jamInput, deskripsiTextarea].forEach(field => {
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

/* Preview card animation */
#preview-initial {
    transition: all 0.3s ease-in-out;
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
