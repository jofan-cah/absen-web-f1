@extends('admin.layouts.app')

@section('title', 'Laporan Tunjangan 1 Minggu')
@section('breadcrumb', 'Laporan / 1 Minggu')
@section('page_title', 'Generate Laporan Tunjangan 1 Minggu')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Generate Laporan 1 Minggu</h2>
        <p class="text-sm text-gray-600 mt-1">Buat laporan tunjangan untuk 1 minggu spesifik (Senin-Minggu)</p>
    </div>

    <form action="{{ route('admin.tunjangan-karyawan.single-week-report') }}" method="POST" target="_blank" class="p-6">
        @csrf

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tunjangan_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Tunjangan Mingguan <span class="text-red-500">*</span>
                    </label>
                    <select name="tunjangan_type_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Pilih Tunjangan</option>
                        @foreach($tunjanganTypes as $type)
                            <option value="{{ $type->tunjangan_type_id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="week_start" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai Minggu (Senin) <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="week_start" required class="w-full border border-gray-300 rounded-lg px-3 py-2"
                        value="{{ date('Y-m-d', strtotime('monday this week')) }}">
                    <p class="text-xs text-gray-500 mt-1">Sistem akan otomatis adjust ke Senin terdekat</p>
                </div>
            </div>

            <!-- Preview -->
            <div id="week-preview" class="bg-gray-50 border border-gray-200 rounded-lg p-4 hidden">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Preview Minggu:</h4>
                <div id="preview-content" class="text-sm text-gray-600"></div>
            </div>

            <!-- Quick Select Buttons -->
            <div class="border-t pt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Quick Select:</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <button type="button" onclick="setWeek('this')" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-sm">
                        Minggu Ini
                    </button>
                    <button type="button" onclick="setWeek('last')" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                        Minggu Lalu
                    </button>
                    <button type="button" onclick="setWeek('next')" class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm">
                        Minggu Depan
                    </button>
                    <button type="button" onclick="setWeek('two-weeks-ago')" class="px-3 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 text-sm">
                        2 Minggu Lalu
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
            <a href="{{ route('admin.tunjangan-karyawan.report-form') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Kembali
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                Generate PDF Laporan
            </button>
        </div>
    </form>
</div>

<script>
function setWeek(period) {
    const weekStartInput = document.querySelector('input[name="week_start"]');
    const today = new Date();
    let mondayDate;

    switch(period) {
        case 'this':
            // Monday of this week
            const dayOfWeek = today.getDay();
            const daysToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek; // Sunday = 0, Monday = 1
            mondayDate = new Date(today);
            mondayDate.setDate(today.getDate() + daysToMonday);
            break;

        case 'last':
            // Monday of last week
            const lastWeek = new Date(today);
            lastWeek.setDate(today.getDate() - 7);
            const lastDayOfWeek = lastWeek.getDay();
            const lastDaysToMonday = lastDayOfWeek === 0 ? -6 : 1 - lastDayOfWeek;
            mondayDate = new Date(lastWeek);
            mondayDate.setDate(lastWeek.getDate() + lastDaysToMonday);
            break;

        case 'next':
            // Monday of next week
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            const nextDayOfWeek = nextWeek.getDay();
            const nextDaysToMonday = nextDayOfWeek === 0 ? -6 : 1 - nextDayOfWeek;
            mondayDate = new Date(nextWeek);
            mondayDate.setDate(nextWeek.getDate() + nextDaysToMonday);
            break;

        case 'two-weeks-ago':
            // Monday of 2 weeks ago
            const twoWeeksAgo = new Date(today);
            twoWeeksAgo.setDate(today.getDate() - 14);
            const twoDayOfWeek = twoWeeksAgo.getDay();
            const twoDaysToMonday = twoDayOfWeek === 0 ? -6 : 1 - twoDayOfWeek;
            mondayDate = new Date(twoWeeksAgo);
            mondayDate.setDate(twoWeeksAgo.getDate() + twoDaysToMonday);
            break;
    }

    if (mondayDate) {
        const formattedDate = mondayDate.toISOString().split('T')[0];
        weekStartInput.value = formattedDate;
        updatePreview();
    }
}

function updatePreview() {
    const weekStartInput = document.querySelector('input[name="week_start"]');
    const preview = document.getElementById('week-preview');
    const previewContent = document.getElementById('preview-content');

    if (weekStartInput.value) {
        const startDate = new Date(weekStartInput.value);

        // Adjust to Monday if not already
        const dayOfWeek = startDate.getDay();
        if (dayOfWeek !== 1) { // If not Monday
            const daysToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
            startDate.setDate(startDate.getDate() + daysToMonday);
        }

        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + 6); // Sunday

        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const startStr = startDate.toLocaleDateString('id-ID', options);
        const endStr = endDate.toLocaleDateString('id-ID', options);

        previewContent.innerHTML = `
            <strong>Periode:</strong> ${startStr} - ${endStr}<br>
            <strong>Hari:</strong> ${startDate.toLocaleDateString('id-ID', {weekday: 'long'})} - ${endDate.toLocaleDateString('id-ID', {weekday: 'long'})}<br>
            <small class="text-gray-500">Laporan akan mencakup 7 hari kerja dalam minggu ini</small>
        `;

        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const weekStartInput = document.querySelector('input[name="week_start"]');
    weekStartInput.addEventListener('change', updatePreview);

    // Initial preview
    updatePreview();
});
</script>

@endsection
