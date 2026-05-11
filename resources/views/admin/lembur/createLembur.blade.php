@extends('admin.layouts.app')

@section('title', 'Input Lembur Manual')
@section('breadcrumb', 'Input Lembur Manual')
@section('page_title', 'Input Lembur Manual - Admin')

@section('page_actions')
<a href="{{ route('admin.lembur.index') }}"
   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>
@endsection

@section('content')

{{-- Error bag --}}
@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- Info Banner --}}
<div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4 mb-6">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-amber-900 mb-1">Input Lembur Manual - Hanya Admin</h3>
            <p class="text-xs text-amber-800">
                Gunakan form ini untuk karyawan yang <strong>lupa mengajukan lembur</strong> via aplikasi.
                Data akan langsung masuk sebagai <strong>Submitted</strong> dan menunggu approval.
                <br>Hitungan tunjangan mengikuti aturan: <strong>lembur &lt; 4 jam = 1x uang makan</strong>, <strong>&ge; 4 jam = 2x uang makan</strong>.
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form Utama --}}
    <div class="lg:col-span-2">
        <form action="{{ route('admin.lembur.store') }}" method="POST" enctype="multipart/form-data" id="form-lembur">
            @csrf
            {{-- absen_id dikirim sebagai hidden field, diisi via AJAX --}}
            <input type="hidden" name="absen_id" id="absen_id" value="{{ old('absen_id') }}">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900">Data Lembur</h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Karyawan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Karyawan <span class="text-red-500">*</span>
                        </label>
                        <select name="karyawan_id" id="karyawan_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('karyawan_id') border-red-400 @enderror">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->karyawan_id }}"
                                        data-dept="{{ $k->department->name ?? '-' }}"
                                        data-staff="{{ $k->staff_status }}"
                                        {{ old('karyawan_id') == $k->karyawan_id ? 'selected' : '' }}>
                                    {{ $k->full_name }} — {{ $k->nip }} ({{ $k->department->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <p id="karyawan-info" class="text-xs text-gray-500 mt-1 hidden"></p>
                    </div>

                    {{-- Tanggal Lembur --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Lembur <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lembur" id="tanggal_lembur" required
                               max="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_lembur') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('tanggal_lembur') border-red-400 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Maksimal hari ini. Jadwal & absen akan dicek otomatis.</p>
                    </div>

                    {{-- Info Jadwal (muncul setelah karyawan + tanggal dipilih) --}}
                    <div id="jadwal-loading" class="hidden text-center py-3">
                        <svg class="animate-spin h-5 w-5 text-primary-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <p class="text-xs text-gray-500 mt-1">Memeriksa jadwal...</p>
                    </div>

                    <div id="jadwal-info-box" class="hidden rounded-lg border p-4">
                        <div class="flex items-start gap-3">
                            <div id="jadwal-icon" class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p id="jadwal-title" class="text-sm font-semibold"></p>
                                <p id="jadwal-desc" class="text-xs mt-0.5"></p>
                                <div id="jadwal-detail" class="mt-2 grid grid-cols-3 gap-2 hidden">
                                    <div class="bg-white rounded border border-gray-200 p-2 text-center">
                                        <p class="text-xs text-gray-500">Shift</p>
                                        <p id="info-shift" class="text-xs font-bold text-gray-800 mt-0.5">-</p>
                                    </div>
                                    <div class="bg-white rounded border border-gray-200 p-2 text-center">
                                        <p class="text-xs text-gray-500">Clock In</p>
                                        <p id="info-clockin" class="text-xs font-bold text-gray-800 mt-0.5">-</p>
                                    </div>
                                    <div class="bg-white rounded border border-gray-200 p-2 text-center">
                                        <p class="text-xs text-gray-500">Clock Out</p>
                                        <p id="info-clockout" class="text-xs font-bold text-gray-800 mt-0.5">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Jenis Lembur --}}
                    <div id="section-jenis" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Lembur <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_lembur" id="jenis_lembur" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('jenis_lembur') border-red-400 @enderror">
                            <option value="regular" {{ old('jenis_lembur', 'regular') == 'regular' ? 'selected' : '' }}>
                                Regular (Lembur Biasa)
                            </option>
                            <option value="oncall" {{ old('jenis_lembur') == 'oncall' ? 'selected' : '' }}>
                                On Call
                            </option>
                        </select>
                    </div>

                    {{-- Jam Mulai & Jam Selesai --}}
                    <div id="section-jam" class="hidden">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jam Mulai <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-400 font-normal">(dari shift end)</span>
                            </label>
                            <input type="time" name="jam_mulai" id="jam_mulai" required
                                   value="{{ old('jam_mulai') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm bg-gray-50 @error('jam_mulai') border-red-400 @enderror">
                            <p class="text-xs text-gray-400 mt-1">Otomatis dari shift end. Bisa diubah jika perlu.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_selesai" id="jam_selesai" required
                                   value="{{ old('jam_selesai') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('jam_selesai') border-red-400 @enderror">
                        </div>
                    </div>
                    </div>

                    {{-- Preview Total Jam --}}
                    <div id="preview-jam" class="hidden rounded-lg p-4 border">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Total Lembur</p>
                                <p class="text-2xl font-bold" id="preview-total-jam">0 jam</p>
                            </div>
                            <div id="preview-tunjangan" class="text-right">
                                <p class="text-xs text-gray-500 mb-0.5">Tunjangan</p>
                                <span id="preview-badge" class="px-3 py-1 rounded-full text-sm font-semibold"></span>
                            </div>
                        </div>
                        <p id="preview-note" class="text-xs text-gray-500 mt-2"></p>
                    </div>

                    {{-- Deskripsi & Foto (muncul setelah jadwal valid) --}}
                    <div id="section-deskripsi" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Deskripsi Pekerjaan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="deskripsi_pekerjaan" id="deskripsi_pekerjaan" rows="4"
                                  placeholder="Jelaskan pekerjaan yang dilakukan saat lembur..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('deskripsi_pekerjaan') border-red-400 @enderror">{{ old('deskripsi_pekerjaan') }}</textarea>
                    </div>

                    <div id="section-foto" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bukti Foto
                            <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="file" name="bukti_foto" id="bukti_foto" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('bukti_foto') border-red-400 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, maks 5MB.</p>
                        <div id="foto-preview" class="mt-2 hidden">
                            <img id="foto-preview-img" src="" alt="Preview" class="h-32 rounded-lg border border-gray-200 object-cover">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Opsi Approval --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-4">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-base font-semibold text-gray-900">Opsi Approval</h2>
                </div>
                <div class="p-6 space-y-4">

                    {{-- Bypass Koordinator --}}
                    <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors" id="bypass-label">
                        <input type="checkbox" name="bypass_koordinator" id="bypass_koordinator" value="1"
                               {{ old('bypass_koordinator') ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Bypass Koordinator</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Jika dicentang, lembur langsung menunggu approval Admin (skip review koordinator).
                                Cocok untuk karyawan yang koordinatornya tidak tersedia.
                            </p>
                        </div>
                    </label>

                    {{-- Catatan Admin --}}
                    <div id="catatan-wrapper" class="{{ old('bypass_koordinator') ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Bypass</label>
                        <textarea name="catatan_admin" rows="2"
                                  placeholder="Alasan bypass koordinator..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('catatan_admin') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('admin.lembur.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" id="btn-submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Lembur
                </button>
            </div>

        </form>
    </div>

    {{-- Sidebar: Panduan Hitungan --}}
    <div class="space-y-4">

        {{-- Aturan Hitungan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Aturan Hitungan Tunjangan</h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-700 font-bold text-xs">1x</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Lembur &lt; 4 Jam</p>
                        <p class="text-xs text-blue-700 mt-0.5">1x uang makan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-green-700 font-bold text-xs">2x</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-900">Lembur &ge; 4 Jam</p>
                        <p class="text-xs text-green-700 mt-0.5">2x uang makan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-orange-900">Hari Lebaran</p>
                        <p class="text-xs text-orange-700 mt-0.5">Insentif Lebaran (tarif khusus)</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 pt-1">
                    Nominal tunjangan per karyawan berbeda berdasarkan <strong>staff_status</strong>
                    dan dikonfigurasikan di menu Tunjangan Detail.
                    Tunjangan otomatis digenerate saat Admin approval.
                </p>
            </div>
        </div>

        {{-- Alur Setelah Simpan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Alur Setelah Disimpan</h3>
            </div>
            <div class="p-5">
                <ol class="space-y-3">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">1</span>
                        <p class="text-xs text-gray-600">Data tersimpan dengan status <span class="font-semibold text-yellow-700 bg-yellow-100 px-1.5 py-0.5 rounded">Submitted</span></p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">2</span>
                        <p class="text-xs text-gray-600">
                            Tanpa bypass: Koordinator review terlebih dahulu<br>
                            Dengan bypass: Langsung ke tahap Admin
                        </p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">3</span>
                        <p class="text-xs text-gray-600">Admin approve → tunjangan otomatis digenerate</p>
                    </li>
                </ol>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const karyawanSelect  = document.getElementById('karyawan_id');
    const tanggalInput    = document.getElementById('tanggal_lembur');
    const absenIdInput    = document.getElementById('absen_id');
    const karyawanInfo    = document.getElementById('karyawan-info');
    const jadwalLoading   = document.getElementById('jadwal-loading');
    const jadwalInfoBox   = document.getElementById('jadwal-info-box');
    const jamMulai        = document.getElementById('jam_mulai');
    const jamSelesai      = document.getElementById('jam_selesai');
    const previewBox      = document.getElementById('preview-jam');
    const totalJamEl      = document.getElementById('preview-total-jam');
    const badgeEl         = document.getElementById('preview-badge');
    const noteEl          = document.getElementById('preview-note');
    const btnSubmit       = document.getElementById('btn-submit');

    const sections = ['section-jenis', 'section-jam', 'section-deskripsi', 'section-foto'];

    function showSections(show) {
        sections.forEach(id => {
            document.getElementById(id).classList.toggle('hidden', !show);
        });
        previewBox.classList.add('hidden');
    }

    // ── Info karyawan ──────────────────────────────────────────────
    karyawanSelect.addEventListener('change', function () {
        const opt   = this.options[this.selectedIndex];
        const dept  = opt.getAttribute('data-dept');
        const staff = opt.getAttribute('data-staff');
        if (this.value && dept) {
            karyawanInfo.textContent = `Department: ${dept} | Status: ${staff ?? '-'}`;
            karyawanInfo.classList.remove('hidden');
        } else {
            karyawanInfo.classList.add('hidden');
        }
        fetchJadwal();
    });

    tanggalInput.addEventListener('change', fetchJadwal);

    // ── AJAX cek jadwal ────────────────────────────────────────────
    let fetchTimeout = null;
    function fetchJadwal() {
        const karyawanId = karyawanSelect.value;
        const tanggal    = tanggalInput.value;

        jadwalInfoBox.classList.add('hidden');
        showSections(false);
        absenIdInput.value = '';
        btnSubmit.disabled = true;

        if (!karyawanId || !tanggal) return;

        clearTimeout(fetchTimeout);
        fetchTimeout = setTimeout(async () => {
            jadwalLoading.classList.remove('hidden');

            try {
                const url = `{{ route('admin.lembur.jadwal-info') }}?karyawan_id=${karyawanId}&tanggal=${tanggal}`;
                const res  = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                jadwalLoading.classList.add('hidden');
                tampilkanJadwalInfo(data);

            } catch (e) {
                jadwalLoading.classList.add('hidden');
                tampilkanError('Gagal memuat data jadwal. Coba lagi.');
            }
        }, 400);
    }

    function tampilkanJadwalInfo(data) {
        jadwalInfoBox.classList.remove('hidden');
        const icon    = document.getElementById('jadwal-icon');
        const title   = document.getElementById('jadwal-title');
        const desc    = document.getElementById('jadwal-desc');
        const detail  = document.getElementById('jadwal-detail');

        // Reset warna box
        jadwalInfoBox.className = 'rounded-lg border p-4';

        if (!data.found) {
            setInfoBox('red', '✕', 'Tidak Ada Jadwal', data.message);
            showSections(false);
            btnSubmit.disabled = true;
            return;
        }

        const absen  = data.absen;
        const jadwal = data.jadwal;
        const shift  = jadwal.shift;

        // Cek: sudah ada lembur
        if (data.lembur_existing) {
            setInfoBox('orange', '!', 'Sudah Ada Lembur',
                `Lembur untuk hari ini sudah tercatat (ID: ${data.lembur_existing.lembur_id}, status: ${data.lembur_existing.status}).`);
            showSections(false);
            btnSubmit.disabled = true;
            return;
        }

        // Cek: tidak ada absen
        if (!absen) {
            setInfoBox('orange', '!', 'Belum Ada Absen',
                `Karyawan memiliki jadwal (shift: ${shift?.name ?? '-'}) tapi belum absen pada hari ini.`);
            showSections(false);
            btnSubmit.disabled = true;
            return;
        }

        // Cek: belum clock out
        if (!absen.clock_out) {
            setInfoBox('orange', '!', 'Belum Clock Out',
                `Karyawan sudah clock in (${absen.clock_in ?? '-'}) tapi belum clock out. Harus clock out dulu.`);
            showSections(false);
            btnSubmit.disabled = true;
            return;
        }

        // ✅ Semua valid
        setInfoBox('green', '✓', `Jadwal Ditemukan — ${shift?.name ?? '-'}`,
            `Karyawan hadir dan sudah clock out. Lembur mulai dari jam selesai shift.`);

        // Tampilkan detail absen
        detail.classList.remove('hidden');
        document.getElementById('info-shift').textContent    = shift?.name ?? '-';
        document.getElementById('info-clockin').textContent  = absen.clock_in  ?? '-';
        document.getElementById('info-clockout').textContent = absen.clock_out ?? '-';

        // Isi hidden absen_id
        absenIdInput.value = absen.absen_id;

        // Auto-fill jam_mulai dari shift end
        if (shift?.end_time && !jamMulai.dataset.userEdited) {
            jamMulai.value = shift.end_time;
        }

        showSections(true);
        btnSubmit.disabled = false;
        hitungTotalJam();
    }

    function setInfoBox(color, iconChar, titleText, descText) {
        const box    = document.getElementById('jadwal-info-box');
        const icon   = document.getElementById('jadwal-icon');
        const title  = document.getElementById('jadwal-title');
        const desc   = document.getElementById('jadwal-desc');
        const detail = document.getElementById('jadwal-detail');

        const colors = {
            green:  { box: 'border-green-300 bg-green-50',   icon: 'bg-green-100 text-green-700',  title: 'text-green-900',  desc: 'text-green-700' },
            orange: { box: 'border-orange-300 bg-orange-50', icon: 'bg-orange-100 text-orange-700',title: 'text-orange-900', desc: 'text-orange-700' },
            red:    { box: 'border-red-300 bg-red-50',       icon: 'bg-red-100 text-red-700',       title: 'text-red-900',    desc: 'text-red-700' },
        };
        const c = colors[color] || colors.red;

        box.className  = `rounded-lg border p-4 ${c.box}`;
        icon.className = `w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-base ${c.icon}`;
        icon.textContent = iconChar;
        title.className  = `text-sm font-semibold ${c.title}`;
        title.textContent = titleText;
        desc.className   = `text-xs mt-0.5 ${c.desc}`;
        desc.textContent  = descText;
        detail.classList.add('hidden');
    }

    function tampilkanError(msg) {
        jadwalInfoBox.classList.remove('hidden');
        setInfoBox('red', '!', 'Error', msg);
        showSections(false);
        btnSubmit.disabled = true;
    }

    // Tandai jam_mulai jika user edit manual
    jamMulai.addEventListener('input', function () {
        this.dataset.userEdited = '1';
    });

    // ── Hitung preview total jam ────────────────────────────────────
    function hitungTotalJam() {
        const mulai   = jamMulai.value;
        const selesai = jamSelesai.value;

        if (!mulai || !selesai) { previewBox.classList.add('hidden'); return; }

        let [mH, mM] = mulai.split(':').map(Number);
        let [sH, sM] = selesai.split(':').map(Number);

        let totalMenit = (sH * 60 + sM) - (mH * 60 + mM);
        if (totalMenit <= 0) totalMenit += 24 * 60;

        const jam    = Math.floor(totalMenit / 60);
        const menit  = totalMenit % 60;
        const jamDes = totalMenit / 60;

        previewBox.classList.remove('hidden');
        totalJamEl.textContent = menit > 0 ? `${jam} jam ${menit} menit` : `${jam} jam`;

        if (jamDes >= 4) {
            previewBox.className = 'rounded-lg p-4 border border-green-300 bg-green-50';
            totalJamEl.className = 'text-2xl font-bold text-green-700';
            badgeEl.className    = 'px-3 py-1 rounded-full text-sm font-semibold bg-green-200 text-green-800';
            badgeEl.textContent  = '2x Uang Makan';
            noteEl.textContent   = 'Lembur >= 4 jam mendapat 2x uang makan.';
        } else {
            previewBox.className = 'rounded-lg p-4 border border-blue-300 bg-blue-50';
            totalJamEl.className = 'text-2xl font-bold text-blue-700';
            badgeEl.className    = 'px-3 py-1 rounded-full text-sm font-semibold bg-blue-200 text-blue-800';
            badgeEl.textContent  = '1x Uang Makan';
            noteEl.textContent   = 'Lembur < 4 jam mendapat 1x uang makan.';
        }
    }

    jamMulai.addEventListener('change', hitungTotalJam);
    jamSelesai.addEventListener('change', hitungTotalJam);

    // ── Bypass koordinator toggle ───────────────────────────────────
    const bypassCheck    = document.getElementById('bypass_koordinator');
    const catatanWrapper = document.getElementById('catatan-wrapper');
    bypassCheck.addEventListener('change', function () {
        catatanWrapper.classList.toggle('hidden', !this.checked);
    });

    // ── Preview foto ────────────────────────────────────────────────
    const fotoInput   = document.getElementById('bukti_foto');
    const fotoPreview = document.getElementById('foto-preview');
    const fotoImg     = document.getElementById('foto-preview-img');
    fotoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => { fotoImg.src = e.target.result; fotoPreview.classList.remove('hidden'); };
            reader.readAsDataURL(file);
        } else {
            fotoPreview.classList.add('hidden');
        }
    });

    // ── Trigger jika ada nilai dari old() ──────────────────────────
    if (karyawanSelect.value && tanggalInput.value) {
        fetchJadwal();
    }
});
</script>
@endpush
