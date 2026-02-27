@extends('admin.layouts.app')

@section('title', 'Generate Tunjangan Karyawan')
@section('breadcrumb', 'Manajemen Tunjangan / Generate')
@section('page_title', 'Generate Tunjangan Karyawan')

@section('content')

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <p class="font-medium mb-2">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-{{ count($tunjanganTypes) }} gap-6 mb-8">
        @foreach ($tunjanganTypes as $type)
            <div
                class="bg-gradient-to-br
            @if ($type->category == 'harian') from-orange-400 to-red-500
            @elseif($type->category == 'bulanan') from-blue-400 to-cyan-500
            @else from-purple-400 to-pink-500 @endif
            rounded-xl p-4 text-white">
                <h3 class="text-lg font-semibold">{{ $type->display_name }}</h3>
                <p class="text-sm opacity-90">{{ ucfirst($type->category) }}</p>
                <p class="text-xs opacity-75 mt-2">{{ $type->description }}</p>

                @if ($type->tunjanganDetails->count() > 0)
                    <div class="mt-3 space-y-1 text-xs">
                        @foreach ($type->tunjanganDetails->take(2) as $detail)
                            <div class="flex justify-between">
                                <span>{{ ucfirst(str_replace('_', ' ', $detail->staff_status)) }}:</span>
                                <span>Rp {{ number_format($detail->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        @if ($type->tunjanganDetails->count() > 2)
                            <div class="text-center opacity-60">... +{{ $type->tunjanganDetails->count() - 2 }} lainnya
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Main Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Generate Tunjangan Karyawan</h2>
            <p class="text-sm text-gray-600 mt-1">Pilih jenis tunjangan dan karyawan</p>
        </div>

        <form action="{{ route('admin.tunjangan-karyawan.generate') }}" method="POST" id="generateForm" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Step 1: Pilih Jenis Tunjangan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Jenis Tunjangan <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-{{ count($tunjanganTypes) }} gap-4">
                        @foreach ($tunjanganTypes as $type)
                            <label class="tunjangan-option cursor-pointer">
                                <input type="radio" name="tunjangan_type_id" value="{{ $type->tunjangan_type_id }}"
                                    class="sr-only tunjangan-radio" data-category="{{ $type->category }}" required>
                                <div
                                    class="border-2 border-gray-200 rounded-xl p-4 transition-all hover:border-primary-300">
                                    <div class="flex items-center justify-between mb-3">
                                        <div
                                            class="w-10 h-10
                                        @if ($type->category == 'harian') bg-orange-100
                                        @elseif($type->category == 'bulanan') bg-blue-100
                                        @else bg-purple-100 @endif
                                        rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5
                                            @if ($type->category == 'harian') text-orange-600
                                            @elseif($type->category == 'bulanan') text-blue-600
                                            @else text-purple-600 @endif"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if ($type->category == 'harian')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                @elseif($type->category == 'bulanan')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2" />
                                                @endif
                                            </svg>
                                        </div>
                                        <div class="check-icon hidden w-6 h-6 text-primary-600">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="font-medium text-gray-900">{{ $type->display_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ ucfirst($type->category) }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Step 2: Pilih Karyawan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Karyawan <span class="text-red-500">*</span>
                    </label>

                    <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto">
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all" class="rounded mr-2">
                                <span class="text-sm font-medium">Pilih Semua</span>
                            </label>
                            <span id="selected-count" class="text-xs text-gray-500">0 dipilih</span>
                        </div>

                        <div class="space-y-2">
                            @foreach ($karyawans as $karyawan)
                                <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="karyawan_ids[]" value="{{ $karyawan->karyawan_id }}"
                                        class="rounded mr-3 karyawan-checkbox" data-staff="{{ $karyawan->staff_status }}">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium">{{ $karyawan->full_name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $karyawan->nip }} • {{ $karyawan->department->name ?? '-' }} •
                                            {{ ucfirst(str_replace('_', ' ', $karyawan->staff_status)) }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Step 3: Configuration berdasarkan kategori -->
                <div id="config-section" class="hidden">
                    <!-- Config Harian -->
                    <div id="config-harian" class="config-type hidden">
                        <h3 class="text-lg font-semibold mb-4">Konfigurasi Tunjangan Harian</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Periode Mulai</label>
                                <input type="date" name="period_start"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    value="{{ date('Y-m-01') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Periode Akhir</label>
                                <input type="date" name="period_end"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ date('Y-m-t') }}">
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                            <p class="text-sm text-orange-800">
                                <strong>Info:</strong> Tunjangan dihitung berdasarkan jumlah hari kerja (absensi masuk)
                                dalam periode yang dipilih.
                            </p>
                        </div>
                    </div>

                    <!-- Config Bulanan -->
                    <div id="config-bulanan" class="config-type hidden">
                        <h3 class="text-lg font-semibold mb-4">Konfigurasi Tunjangan Bulanan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                                <select name="month" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Info:</strong> Tunjangan bulanan diberikan dengan nominal tetap per bulan sesuai
                                status karyawan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div id="summary-section" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Ringkasan Generate</h4>
                    <div class="text-sm space-y-2">
                        <div class="flex justify-between">
                            <span>Jenis Tunjangan:</span>
                            <span id="summary-type" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Jumlah Karyawan:</span>
                            <span id="summary-count" class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Periode:</span>
                            <span id="summary-period" class="font-medium">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                <a href="{{ route('admin.tunjangan-karyawan.index') }}"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Generate Tunjangan
                </button>
            </div>
        </form>
    </div>

    <script>
        // Data tunjangan dari database
        const tunjanganData = {!! json_encode($tunjanganTypes->keyBy('tunjangan_type_id')) !!};

        document.addEventListener('DOMContentLoaded', function() {
            const tunjanganRadios = document.querySelectorAll('.tunjangan-radio');
            const karyawanCheckboxes = document.querySelectorAll('.karyawan-checkbox');
            const selectAll = document.getElementById('select-all');
            const selectedCount = document.getElementById('selected-count');
            const configSection = document.getElementById('config-section');
            const summarySection = document.getElementById('summary-section');

            // Handle tunjangan selection
            tunjanganRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        updateTunjanganSelection(this.value);
                        showConfig(this.dataset.category);
                        updateSummary();
                    }
                });
            });

            // Handle karyawan selection
            selectAll.addEventListener('change', function() {
                karyawanCheckboxes.forEach(cb => cb.checked = this.checked);
                updateKaryawanCount();
                updateSummary();
            });

            karyawanCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateKaryawanCount();
                    updateSelectAllState();
                    updateSummary();
                });
            });

            function updateTunjanganSelection(selectedId) {
                document.querySelectorAll('.tunjangan-option').forEach(option => {
                    const input = option.querySelector('input');
                    const div = option.querySelector('div');
                    const checkIcon = option.querySelector('.check-icon');

                    if (input.value === selectedId) {
                        div.classList.remove('border-gray-200');
                        div.classList.add('border-primary-500', 'bg-primary-50');
                        checkIcon.classList.remove('hidden');
                    } else {
                        div.classList.remove('border-primary-500', 'bg-primary-50');
                        div.classList.add('border-gray-200');
                        option.querySelector('.check-icon').classList.add('hidden');
                    }
                });
            }

            function showConfig(category) {
                document.querySelectorAll('.config-type').forEach(config => {
                    config.classList.add('hidden');
                });

                // Mingguan dan harian pake config yang sama (periode)
                let targetConfigId = '';
                if (category === 'harian' || category === 'mingguan') {
                    targetConfigId = 'config-harian'; // Pake config harian
                } else if (category === 'bulanan') {
                    targetConfigId = 'config-bulanan';
                }

                const targetConfig = document.getElementById(targetConfigId);
                if (targetConfig) {
                    targetConfig.classList.remove('hidden');
                    configSection.classList.remove('hidden');
                }
            }

            function updateKaryawanCount() {
                const count = document.querySelectorAll('.karyawan-checkbox:checked').length;
                selectedCount.textContent = `${count} dipilih`;
            }

            function updateSelectAllState() {
                const checked = document.querySelectorAll('.karyawan-checkbox:checked').length;
                const total = karyawanCheckboxes.length;

                if (checked === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                } else if (checked === total) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            }

            function updateSummary() {
                const selectedTunjangan = document.querySelector('.tunjangan-radio:checked');
                const checkedKaryawan = document.querySelectorAll('.karyawan-checkbox:checked').length;

                // Update type
                if (selectedTunjangan && tunjanganData[selectedTunjangan.value]) {
                    document.getElementById('summary-type').textContent = tunjanganData[selectedTunjangan.value]
                        .name;
                }

                // Update count
                document.getElementById('summary-count').textContent = `${checkedKaryawan} karyawan`;

                // Update period
                let period = '-';
                if (selectedTunjangan) {
                    const category = selectedTunjangan.dataset.category;
                    if (category === 'harian') {
                        const start = document.querySelector('input[name="period_start"]')?.value;
                        const end = document.querySelector('input[name="period_end"]')?.value;
                        if (start && end) {
                            period = `${start} s/d ${end}`;
                        }
                    } else if (category === 'bulanan') {
                        const month = document.querySelector('select[name="month"]')?.value;
                        const year = document.querySelector('select[name="year"]')?.value;
                        if (month && year) {
                            const monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep',
                                'Okt', 'Nov', 'Des'
                            ];
                            period = `${monthNames[parseInt(month)]} ${year}`;
                        }
                    }
                }
                document.getElementById('summary-period').textContent = period;
            }

            // Initialize
            updateKaryawanCount();

            // Form validation
            document.getElementById('generateForm').addEventListener('submit', function(e) {
                const selectedTunjangan = document.querySelector('.tunjangan-radio:checked');
                const checkedKaryawan = document.querySelectorAll('.karyawan-checkbox:checked');

                if (!selectedTunjangan) {
                    e.preventDefault();
                    alert('Pilih jenis tunjangan terlebih dahulu!');
                    return;
                }

                if (checkedKaryawan.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal 1 karyawan!');
                    return;
                }
            });

            // Add change listeners for period updates
            document.querySelector('input[name="period_start"]')?.addEventListener('change', updateSummary);
            document.querySelector('input[name="period_end"]')?.addEventListener('change', updateSummary);
            document.querySelector('select[name="month"]')?.addEventListener('change', updateSummary);
            document.querySelector('select[name="year"]')?.addEventListener('change', updateSummary);
        });
    </script>

@endsection
