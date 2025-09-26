<?php

namespace App\Http\Controllers\Api;

use App\Models\TunjanganKaryawan;
use App\Models\TunjanganType;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TunjanganController extends BaseApiController
{
    /**
     * Report Uang Makan
     * GET /api/tunjangan/uang-makan/report
     */
    public function uangMakanReport(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        // Get tunjangan type for Uang Makan
        $tunjanganType = TunjanganType::where('code', 'UANG_MAKAN')->first();

        if (!$tunjanganType) {
            return $this->notFoundResponse('Tipe tunjangan Uang Makan tidak ditemukan');
        }

        // Query tunjangan karyawan
        $query = TunjanganKaryawan::with(['tunjanganType', 'penalti', 'approvedBy'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month);

        $tunjangans = $query->orderBy('period_start', 'desc')->paginate($perPage);

        // Summary
        $allTunjangans = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
            ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->get();

        $summary = [
            'total_tunjangan' => $allTunjangans->count(),
            'total_hari_kerja_asli' => $allTunjangans->sum('hari_kerja_asli'),
            'total_hari_potong_penalti' => $allTunjangans->sum('hari_potong_penalti'),
            'total_hari_kerja_final' => $allTunjangans->sum('hari_kerja_final'),
            'total_nominal' => $allTunjangans->sum('total_amount'),
            'status' => [
                'pending' => $allTunjangans->where('status', 'pending')->count(),
                'requested' => $allTunjangans->where('status', 'requested')->count(),
                'approved' => $allTunjangans->where('status', 'approved')->count(),
                'received' => $allTunjangans->where('status', 'received')->count(),
            ],
            'nominal_by_status' => [
                'pending' => $allTunjangans->where('status', 'pending')->sum('total_amount'),
                'requested' => $allTunjangans->where('status', 'requested')->sum('total_amount'),
                'approved' => $allTunjangans->where('status', 'approved')->sum('total_amount'),
                'received' => $allTunjangans->where('status', 'received')->sum('total_amount'),
            ]
        ];

        return $this->paginatedResponse($tunjangans, 'Report uang makan berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ]);
    }

    /**
     * Report Uang Kuota
     * GET /api/tunjangan/uang-kuota/report
     */
    public function uangKuotaReport(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        // Get tunjangan type for Uang Kuota
        $tunjanganType = TunjanganType::where('code', 'UANG_KUOTA')->first();

        if (!$tunjanganType) {
            return $this->notFoundResponse('Tipe tunjangan Uang Kuota tidak ditemukan');
        }

        // Query tunjangan karyawan
        $query = TunjanganKaryawan::with(['tunjanganType', 'approvedBy'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month);

        $tunjangans = $query->orderBy('period_start', 'desc')->paginate($perPage);

        // Summary
        $allTunjangans = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
            ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->get();

        $summary = [
            'total_tunjangan' => $allTunjangans->count(),
            'total_nominal' => $allTunjangans->sum('total_amount'),
            'status' => [
                'pending' => $allTunjangans->where('status', 'pending')->count(),
                'requested' => $allTunjangans->where('status', 'requested')->count(),
                'approved' => $allTunjangans->where('status', 'approved')->count(),
                'received' => $allTunjangans->where('status', 'received')->count(),
            ],
            'nominal_by_status' => [
                'pending' => $allTunjangans->where('status', 'pending')->sum('total_amount'),
                'requested' => $allTunjangans->where('status', 'requested')->sum('total_amount'),
                'approved' => $allTunjangans->where('status', 'approved')->sum('total_amount'),
                'received' => $allTunjangans->where('status', 'received')->sum('total_amount'),
            ]
        ];

        return $this->paginatedResponse($tunjangans, 'Report uang kuota berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ]);
    }

    /**
     * Report Uang Lembur
     * GET /api/tunjangan/uang-lembur/report
     */
    public function uangLemburReport(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        // Get tunjangan type for Uang Lembur
        $tunjanganType = TunjanganType::where('code', 'UANG_LEMBUR')->first();

        if (!$tunjanganType) {
            return $this->notFoundResponse('Tipe tunjangan Uang Lembur tidak ditemukan');
        }

        // Query tunjangan karyawan
        $query = TunjanganKaryawan::with(['tunjanganType', 'absen', 'approvedBy'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month);

        $tunjangans = $query->orderBy('period_start', 'desc')->paginate($perPage);

        // Summary
        $allTunjangans = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
            ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->get();

        $summary = [
            'total_tunjangan' => $allTunjangans->count(),
            'total_jam_lembur' => $allTunjangans->sum('quantity'),
            'total_nominal' => $allTunjangans->sum('total_amount'),
            'status' => [
                'pending' => $allTunjangans->where('status', 'pending')->count(),
                'requested' => $allTunjangans->where('status', 'requested')->count(),
                'approved' => $allTunjangans->where('status', 'approved')->count(),
                'received' => $allTunjangans->where('status', 'received')->count(),
            ],
            'nominal_by_status' => [
                'pending' => $allTunjangans->where('status', 'pending')->sum('total_amount'),
                'requested' => $allTunjangans->where('status', 'requested')->sum('total_amount'),
                'approved' => $allTunjangans->where('status', 'approved')->sum('total_amount'),
                'received' => $allTunjangans->where('status', 'received')->sum('total_amount'),
            ]
        ];

        return $this->paginatedResponse($tunjangans, 'Report uang lembur berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ]);
    }

    /**
     * List semua tunjangan karyawan (all types)
     * GET /api/tunjangan/my-list
     */
    public function myList(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $status = $request->get('status'); // pending, requested, approved, received
        $type = $request->get('type'); // UANG_MAKAN, UANG_KUOTA, UANG_LEMBUR
        $perPage = $this->getPerPage($request);

        $query = TunjanganKaryawan::with(['tunjanganType', 'penalti', 'approvedBy'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month);

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by type
        if ($type) {
            $tunjanganType = TunjanganType::where('code', $type)->first();
            if ($tunjanganType) {
                $query->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id);
            }
        }

        $tunjangans = $query->orderBy('period_start', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);

        // Summary
        $allTunjangans = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->get();

        $summary = [
            'total' => $allTunjangans->count(),
            'total_nominal' => $allTunjangans->sum('total_amount'),
            'by_status' => [
                'pending' => $allTunjangans->where('status', 'pending')->count(),
                'requested' => $allTunjangans->where('status', 'requested')->count(),
                'approved' => $allTunjangans->where('status', 'approved')->count(),
                'received' => $allTunjangans->where('status', 'received')->count(),
            ],
            'nominal_by_status' => [
                'pending' => $allTunjangans->where('status', 'pending')->sum('total_amount'),
                'requested' => $allTunjangans->where('status', 'requested')->sum('total_amount'),
                'approved' => $allTunjangans->where('status', 'approved')->sum('total_amount'),
                'received' => $allTunjangans->where('status', 'received')->sum('total_amount'),
            ]
        ];

        return $this->paginatedResponse($tunjangans, 'Data tunjangan berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ]);
    }

    /**
     * Detail tunjangan
     * GET /api/tunjangan/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $tunjangan = TunjanganKaryawan::with([
            'tunjanganType',
            'penalti',
            'absen.jadwal.shift',
            'approvedBy'
        ])
            ->where('tunjangan_karyawan_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$tunjangan) {
            return $this->notFoundResponse('Data tunjangan tidak ditemukan');
        }

        // Get formatted history
        $history = $tunjangan->getFormattedHistory();

        return $this->successResponse([
            'tunjangan' => $tunjangan,
            'history' => $history,
            'can_request' => $tunjangan->canRequest(),
            'can_confirm' => $tunjangan->status === 'approved',
        ], 'Detail tunjangan berhasil diambil');
    }

    /**
     * Request pencairan tunjangan (pending -> requested)
     * POST /api/tunjangan/{id}/request
     */
    public function requestTunjangan(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $tunjangan = TunjanganKaryawan::where('tunjangan_karyawan_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$tunjangan) {
            return $this->notFoundResponse('Data tunjangan tidak ditemukan');
        }

        if (!$tunjangan->canRequest()) {
            return $this->forbiddenResponse('Tunjangan dengan status ' . $tunjangan->status . ' tidak dapat direquest');
        }

        try {
            $tunjangan->requestTunjangan('mobile', $user->user_id);

            return $this->successResponse(
                $tunjangan->fresh(['tunjanganType', 'approvedBy']),
                'Request tunjangan berhasil. Menunggu persetujuan admin.'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal request tunjangan: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi tunjangan sudah diterima (approved -> received)
     * POST /api/tunjangan/{id}/confirm-received
     */
    public function confirmReceived(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $tunjangan = TunjanganKaryawan::where('tunjangan_karyawan_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$tunjangan) {
            return $this->notFoundResponse('Data tunjangan tidak ditemukan');
        }

        if ($tunjangan->status !== 'approved') {
            return $this->forbiddenResponse('Hanya tunjangan dengan status approved yang dapat dikonfirmasi');
        }

        try {
            $tunjangan->confirmReceived(null, $user->user_id);

            return $this->successResponse(
                $tunjangan->fresh(['tunjanganType', 'approvedBy']),
                'Konfirmasi penerimaan tunjangan berhasil'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal konfirmasi penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Summary all tunjangan
     * GET /api/tunjangan/summary
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get all tunjangan types
        $uangMakan = TunjanganType::where('code', 'UANG_MAKAN')->first();
        $uangKuota = TunjanganType::where('code', 'UANG_KUOTA')->first();
        $uangLembur = TunjanganType::where('code', 'UANG_LEMBUR')->first();

        $allTunjangans = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month)
            ->get();

        $summary = [
            'total_semua_tunjangan' => $allTunjangans->count(),
            'total_nominal_semua' => $allTunjangans->sum('total_amount'),
            'uang_makan' => [
                'count' => $uangMakan ? $allTunjangans->where('tunjangan_type_id', $uangMakan->tunjangan_type_id)->count() : 0,
                'total_nominal' => $uangMakan ? $allTunjangans->where('tunjangan_type_id', $uangMakan->tunjangan_type_id)->sum('total_amount') : 0,
                'hari_kerja' => $uangMakan ? $allTunjangans->where('tunjangan_type_id', $uangMakan->tunjangan_type_id)->sum('hari_kerja_final') : 0,
            ],
            'uang_kuota' => [
                'count' => $uangKuota ? $allTunjangans->where('tunjangan_type_id', $uangKuota->tunjangan_type_id)->count() : 0,
                'total_nominal' => $uangKuota ? $allTunjangans->where('tunjangan_type_id', $uangKuota->tunjangan_type_id)->sum('total_amount') : 0,
            ],
            'uang_lembur' => [
                'count' => $uangLembur ? $allTunjangans->where('tunjangan_type_id', $uangLembur->tunjangan_type_id)->count() : 0,
                'total_nominal' => $uangLembur ? $allTunjangans->where('tunjangan_type_id', $uangLembur->tunjangan_type_id)->sum('total_amount') : 0,
                'total_jam' => $uangLembur ? $allTunjangans->where('tunjangan_type_id', $uangLembur->tunjangan_type_id)->sum('quantity') : 0,
            ],
            'by_status' => [
                'pending' => [
                    'count' => $allTunjangans->where('status', 'pending')->count(),
                    'nominal' => $allTunjangans->where('status', 'pending')->sum('total_amount'),
                ],
                'requested' => [
                    'count' => $allTunjangans->where('status', 'requested')->count(),
                    'nominal' => $allTunjangans->where('status', 'requested')->sum('total_amount'),
                ],
                'approved' => [
                    'count' => $allTunjangans->where('status', 'approved')->count(),
                    'nominal' => $allTunjangans->where('status', 'approved')->sum('total_amount'),
                ],
                'received' => [
                    'count' => $allTunjangans->where('status', 'received')->count(),
                    'nominal' => $allTunjangans->where('status', 'received')->sum('total_amount'),
                ],
            ]
        ];

        return $this->successResponse([
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ], 'Summary tunjangan berhasil diambil');
    }
}
