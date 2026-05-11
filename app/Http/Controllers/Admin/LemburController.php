<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\TunjanganKaryawan;
use App\Models\TunjanganType;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LemburController extends Controller
{
    /**
     * Display listing - untuk admin approval
     */
    // public function index(Request $request)
    // {
    //     $query = Lembur::with([
    //         'karyawan.user',
    //         'karyawan.department',
    //         'absen',
    //         'approvedBy',
    //         'rejectedBy',
    //         'tunjanganKaryawan',
    //         'coordinator'
    //     ]);

    //     // Filter by karyawan
    //     if ($request->filled('karyawan_id')) {
    //         $query->where('karyawan_id', $request->karyawan_id);
    //     }

    //     // Filter by status
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     // 🆕 Filter by koordinator_status
    //     if ($request->filled('koordinator_status')) {
    //         $query->where('koordinator_status', $request->koordinator_status);
    //     }

    //     // Filter by tanggal
    //     if ($request->filled('tanggal_dari')) {
    //         $query->where('tanggal_lembur', '>=', $request->tanggal_dari);
    //     }

    //     if ($request->filled('tanggal_sampai')) {
    //         $query->where('tanggal_lembur', '<=', $request->tanggal_sampai);
    //     }

    //     $lemburs = $query->orderBy('tanggal_lembur', 'desc')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(20);

    //     // Data untuk filter
    //     $karyawans = Karyawan::with('user')
    //         ->where('employment_status', 'active')
    //         ->orderBy('full_name')
    //         ->get();

    //     $statusOptions = ['draft', 'submitted', 'approved', 'rejected', 'processed'];
    //     $koordinatorStatusOptions = ['pending', 'approved', 'rejected']; // 🆕

    //     // Summary dengan breakdown koordinator
    //     $summary = [
    //         'total' => Lembur::count(),
    //         'submitted' => Lembur::where('status', 'submitted')->count(),

    //         // 🆕 Breakdown submitted
    //         'pending_koordinator' => Lembur::where('status', 'submitted')
    //             ->where('koordinator_status', 'pending')
    //             ->count(),
    //         'pending_admin' => Lembur::where('status', 'submitted')
    //             ->where('koordinator_status', 'approved')
    //             ->count(),

    //         'approved' => Lembur::where('status', 'approved')->count(),
    //         'rejected' => Lembur::where('status', 'rejected')->count(),
    //         'total_jam_bulan_ini' => Lembur::whereYear('tanggal_lembur', now()->year)
    //             ->whereMonth('tanggal_lembur', now()->month)
    //             ->sum('total_jam'),
    //     ];

    //     return view('admin.lembur.indexLembur', compact(
    //         'lemburs',
    //         'karyawans',
    //         'statusOptions',
    //         'koordinatorStatusOptions', // 🆕
    //         'summary'
    //     ));
    // }

    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        // ===============================
        // BASE QUERY
        // ===============================
        $query = Lembur::with([
            'karyawan.user',
            'karyawan.department',
            'absen',
            'approvedBy',
            'rejectedBy',
            'tunjanganKaryawan',
            'coordinator',
        ]);

        // ===============================
        // 🔒 FILTER KOORDINATOR MULTI-DEPARTMENT
        // ===============================
        $departmentIds = collect();

        if (! $isAdmin) {
            // Ambil semua department yang dikoordinatori
            $departmentIds = $user->departments()->pluck('department_id');

            if ($departmentIds->isNotEmpty()) {
                $query->whereHas('karyawan', function ($q) use ($departmentIds) {
                    $q->whereIn('department_id', $departmentIds);
                });
            } else {
                // Bukan admin & bukan koordinator → tidak boleh lihat apa-apa
                $query->whereRaw('1 = 0');
            }
        }

        // ===============================
        // FILTER REQUEST
        // ===============================
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('koordinator_status')) {
            $query->where('koordinator_status', $request->koordinator_status);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_lembur', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_lembur', '<=', $request->tanggal_sampai);
        }

        // ===============================
        // DATA LIST
        // ===============================
        $lemburs = $query
            ->orderBy('tanggal_lembur', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // ===============================
        // DROPDOWN KARYAWAN (KONSISTEN)
        // ===============================
        $karyawansQuery = Karyawan::with('user')
            ->where('employment_status', 'active');

        if (! $isAdmin && $departmentIds->isNotEmpty()) {
            $karyawansQuery->whereIn('department_id', $departmentIds);
        }

        $karyawans = $karyawansQuery
            ->orderBy('full_name')
            ->get();

        // ===============================
        // OPTIONS
        // ===============================
        $statusOptions = ['draft', 'submitted', 'approved', 'rejected', 'processed'];
        $koordinatorStatusOptions = ['pending', 'approved', 'rejected'];

        // ===============================
        // SUMMARY (SESUI ROLE)
        // ===============================
        $summaryQuery = clone $query;

        $summary = [
            'total' => (clone $summaryQuery)->count(),

            'submitted' => (clone $summaryQuery)
                ->where('status', 'submitted')
                ->count(),

            'pending_koordinator' => (clone $summaryQuery)
                ->where('status', 'submitted')
                ->where('koordinator_status', 'pending')
                ->count(),

            'pending_admin' => (clone $summaryQuery)
                ->where('status', 'submitted')
                ->where('koordinator_status', 'approved')
                ->count(),

            'approved' => (clone $summaryQuery)
                ->where('status', 'approved')
                ->count(),

            'rejected' => (clone $summaryQuery)
                ->where('status', 'rejected')
                ->count(),

            'total_jam_bulan_ini' => (clone $summaryQuery)
                ->whereYear('tanggal_lembur', now()->year)
                ->whereMonth('tanggal_lembur', now()->month)
                ->sum('total_jam'),
        ];

        // ===============================
        // RETURN VIEW
        // ===============================
        return view('admin.lembur.indexLembur', compact(
            'lemburs',
            'karyawans',
            'statusOptions',
            'koordinatorStatusOptions',
            'summary'
        ));
    }

    /**
     * Form input lembur manual (admin only)
     */
    public function create()
    {
        $karyawans = Karyawan::with(['user', 'department'])
            ->where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('admin.lembur.createLembur', compact('karyawans'));
    }

    /**
     * AJAX: Ambil info jadwal + absen karyawan pada tanggal tertentu
     */
    public function getJadwalInfo(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'tanggal'     => 'required|date',
        ]);

        $jadwal = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $request->karyawan_id)
            ->whereDate('date', $request->tanggal)
            ->where('is_active', true)
            ->first();

        if (! $jadwal) {
            return response()->json([
                'found'   => false,
                'message' => 'Tidak ada jadwal kerja pada tanggal tersebut.',
            ]);
        }

        $absen = $jadwal->absen;

        // Cek apakah sudah ada lembur untuk absen ini
        $lemburExisting = null;
        if ($absen) {
            $lemburExisting = Lembur::where('absen_id', $absen->absen_id)
                ->whereIn('status', ['draft', 'submitted', 'approved', 'processed'])
                ->first();
        }

        return response()->json([
            'found'   => true,
            'jadwal'  => [
                'jadwal_id' => $jadwal->jadwal_id,
                'type'      => $jadwal->type,
                'status'    => $jadwal->status,
                'shift'     => $jadwal->shift ? [
                    'name'     => $jadwal->shift->name,
                    'end_time' => substr($jadwal->shift->end_time, 0, 5), // H:i
                ] : null,
            ],
            'absen' => $absen ? [
                'absen_id'  => $absen->absen_id,
                'status'    => $absen->status,
                'type'      => $absen->type,
                'clock_in'  => $absen->clock_in  ? substr($absen->clock_in, 0, 5) : null,
                'clock_out' => $absen->clock_out ? substr($absen->clock_out, 0, 5) : null,
            ] : null,
            'lembur_existing' => $lemburExisting ? [
                'lembur_id' => $lemburExisting->lembur_id,
                'status'    => $lemburExisting->status,
            ] : null,
        ]);
    }

    /**
     * Simpan lembur manual yang diinput admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id'          => 'required|exists:karyawans,karyawan_id',
            'tanggal_lembur'       => 'required|date|before_or_equal:today',
            'absen_id'             => 'required|exists:absens,absen_id',
            'jam_mulai'            => 'required|date_format:H:i',
            'jam_selesai'          => 'required|date_format:H:i',
            'jenis_lembur'         => 'required|in:regular,oncall',
            'deskripsi_pekerjaan'  => 'required|string|max:1000',
            'bukti_foto'           => 'nullable|file|image|max:5120',
            'bypass_koordinator'   => 'nullable|boolean',
            'catatan_admin'        => 'nullable|string|max:500',
        ]);

        // Ambil dan validasi absen
        $absen = Absen::with('jadwal.shift')->find($request->absen_id);

        if (! $absen || $absen->karyawan_id !== $request->karyawan_id) {
            return back()
                ->withErrors(['absen_id' => 'Data absen tidak valid untuk karyawan ini.'])
                ->withInput();
        }

        if (! $absen->clock_out) {
            return back()
                ->withErrors(['absen_id' => 'Karyawan belum clock out pada hari tersebut.'])
                ->withInput();
        }

        // Cegah duplikat lembur untuk absen yang sama
        $duplikat = Lembur::where('absen_id', $absen->absen_id)
            ->whereIn('status', ['draft', 'submitted', 'approved', 'processed'])
            ->first();

        if ($duplikat) {
            return back()
                ->withErrors(['absen_id' => 'Sudah ada pengajuan lembur untuk absen ini (ID: ' . $duplikat->lembur_id . ', status: ' . $duplikat->status . ').'])
                ->withInput();
        }

        // Hitung total jam (handle lintas tengah malam)
        $mulai   = Carbon::createFromFormat('H:i', $request->jam_mulai);
        $selesai = Carbon::createFromFormat('H:i', $request->jam_selesai);
        if ($selesai->lessThanOrEqualTo($mulai)) {
            $selesai->addDay();
        }
        $totalJam = $mulai->diffInMinutes($selesai) / 60;

        if ($totalJam <= 0) {
            return back()
                ->withErrors(['jam_selesai' => 'Jam selesai harus lebih besar dari jam mulai.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Upload bukti foto ke S3 (opsional)
            $buktiFotoPath = null;
            if ($request->hasFile('bukti_foto')) {
                $buktiFotoPath = Storage::disk('s3')->putFile('lembur', $request->file('bukti_foto'));
            }

            $bypassKoordinator = $request->boolean('bypass_koordinator');

            $lembur = Lembur::create([
                'lembur_id'               => Lembur::generateLemburId(),
                'karyawan_id'             => $request->karyawan_id,
                'absen_id'                => $absen->absen_id,
                'tanggal_lembur'          => $request->tanggal_lembur,
                'jenis_lembur'            => $request->jenis_lembur,
                'jam_mulai'               => $request->jam_mulai . ':00',
                'jam_selesai'             => $request->jam_selesai . ':00',
                'deskripsi_pekerjaan'     => $request->deskripsi_pekerjaan,
                'bukti_foto'              => $buktiFotoPath,
                'status'                  => 'submitted',
                'koordinator_status'      => $bypassKoordinator ? 'approved' : 'pending',
                'koordinator_approved_at' => $bypassKoordinator ? now() : null,
                'koordinator_notes'       => $bypassKoordinator
                    ? 'Bypass oleh Admin: ' . ($request->catatan_admin ?? '-')
                    : null,
                'submitted_at'            => now(),
                'submitted_via'           => 'web',
                'created_by_user_id'      => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.lembur.show', $lembur->lembur_id)
                ->with('success', 'Lembur manual berhasil dibuat! Total: ' . number_format($totalJam, 1) . ' jam.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal buat lembur manual: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Gagal membuat lembur: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show detail lembur
     */
    public function show(Lembur $lembur)
    {
        $lembur->load([
            'karyawan.user',
            'karyawan.department',
            'absen.jadwal.shift',
            'approvedBy',
            'rejectedBy',
            'createdBy',
            'tunjanganKaryawan',
            'coordinator',
        ]);

        return view('admin.lembur.showLembur', compact('lembur'));
    }

    public function approve(Lembur $lembur, Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        if ($lembur->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Lembur tidak dapat disetujui! Status saat ini: '.$lembur->status,
            ], 422);
        }

        $user = Auth::user();

        // VALIDASI: User harus admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Admin yang dapat melakukan approval',
            ], 403);
        }

        try {
            DB::beginTransaction();

            // ✅ CEK JENIS LEMBUR: OnCall atau Request biasa?
            if ($lembur->jenis_lembur === 'oncall') {
                // dd('JOFAN');
                // 🔥 ONCALL: Auto-generate tunjangan LANGSUNG
                $result = $lembur->approveOnCall($user->user_id, $request->notes);
                $message = 'OnCall berhasil disetujui dan tunjangan telah dibuat secara langsung!';
            } else {
                // 📋 LEMBUR REQUEST: Flow normal
                if ($lembur->koordinator_status === 'approved') {
                    $result = $lembur->approveByAdmin($user->user_id, $request->notes);
                    $message = 'Lembur berhasil disetujui oleh Admin dan tunjangan telah dibuat!';
                } else {
                    $result = $lembur->approveByAdminDirect($user->user_id, $request->notes);
                    $message = 'Lembur berhasil disetujui langsung oleh Admin (bypass koordinator)!';
                }
            }

            if (! $result) {
                throw new \Exception('Gagal menyetujui lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'tunjangan_id' => $lembur->tunjangan_karyawan_id,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal approve lembur: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject lembur
     */
    /**
     * Reject lembur - LEVEL 2 (Admin)
     * Admin bisa reject walau koordinator sudah approve
     */
    public function reject(Lembur $lembur, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($lembur->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Lembur tidak dapat ditolak! Status saat ini: '.$lembur->status,
            ], 422);
        }

        $user = Auth::user();

        // VALIDASI: User harus admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Admin yang dapat reject lembur',
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Reject oleh admin (LEVEL 2)
            $result = $lembur->rejectByAdmin($user->user_id, $request->rejection_reason);

            if (! $result) {
                throw new \Exception('Gagal menolak lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil ditolak oleh Admin',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal reject lembur: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk approve - DENGAN LOGIC VALIDASI PERMISSION BARU
     */
    /**
     * Bulk approve - LEVEL 2 (Admin)
     * Hanya approve yang sudah di-approve koordinator
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:lemburs,lembur_id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Validasi: User harus admin
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin yang dapat melakukan bulk approve',
                ], 403);
            }

            $approved = 0;
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $lembur = Lembur::with('karyawan.department')->find($id);

                    if ($lembur->status !== 'submitted') {
                        $errors[] = "{$lembur->karyawan->full_name} - Status tidak valid";

                        continue;
                    }

                    // VALIDASI: Koordinator harus sudah approve
                    if ($lembur->koordinator_status !== 'approved') {
                        $errors[] = "{$lembur->karyawan->full_name} - Belum diapprove koordinator";

                        continue;
                    }

                    // Approve oleh admin (LEVEL 2)
                    $result = $lembur->approveByAdmin($user->user_id, $request->notes);

                    if ($result) {
                        $approved++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "{$lembur->karyawan->full_name} - {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "{$approved} lembur berhasil disetujui oleh Admin";
            if (! empty($errors)) {
                $message .= '. Error: '.implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'approved_count' => $approved,
                'error_count' => count($errors),
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal bulk approve: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lemburs,lembur_id',
        ]);

        try {
            DB::beginTransaction();

            $lemburs = Lembur::whereIn('lembur_id', $request->ids)->get();

            foreach ($lemburs as $lembur) {
                if (! $lembur->canEdit()) {
                    throw new \Exception("Lembur untuk '{$lembur->karyawan->full_name}' tidak dapat dihapus!");
                }
            }

            // Delete photos
            foreach ($lemburs as $lembur) {
                if ($lembur->bukti_foto) {
                    Storage::disk('public')->delete($lembur->bukti_foto);
                }
            }

            Lembur::whereIn('lembur_id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids).' data lembur berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate tunjangan lembur mingguan
     * Akan generate tunjangan untuk semua lembur yang approved dalam 1 minggu
     */
    public function generateTunjanganMingguan(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

            // Get tunjangan type lembur
            $tunjanganType = TunjanganType::where('code', 'UANG_LEMBUR')->active()->first();

            if (! $tunjanganType) {
                throw new \Exception('Tunjangan type UANG_LEMBUR tidak ditemukan');
            }

            // Get semua lembur approved dalam minggu ini yang belum di-generate
            $lemburs = Lembur::with('karyawan')
                ->where('status', 'approved')
                ->whereBetween('tanggal_lembur', [$weekStart, $weekEnd])
                ->whereNull('tunjangan_karyawan_id')
                ->get();

            if ($lemburs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada lembur yang perlu di-generate tunjangan untuk periode ini',
                ], 404);
            }

            $generated = 0;
            foreach ($lemburs as $lembur) {
                try {
                    $lembur->generateTunjangan();
                    $generated++;
                } catch (\Exception $e) {
                    Log::error("Gagal generate tunjangan untuk lembur {$lembur->lembur_id}: ".$e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$generated} tunjangan berhasil di-generate untuk periode {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}",
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate tunjangan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Form untuk generate tunjangan mingguan
     */
    public function generateTunjanganForm()
    {
        return view('admin.lembur.generateTunjangan');
    }

    /**
     * Report analytics lembur
     */
    public function report(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Analytics data
        $analytics = [
            'total_lembur' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])->count(),
            'total_approved' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])->count(),
            'total_jam' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])->sum('total_jam'),
            'by_kategori' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])
                ->selectRaw('kategori_lembur, COUNT(*) as count, SUM(total_jam) as total_jam')
                ->groupBy('kategori_lembur')
                ->get(),
            'by_department' => Lembur::with('karyawan.department')
                ->whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])
                ->get()
                ->groupBy('karyawan.department.name')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'total_jam' => $items->sum('total_jam'),
                    ];
                }),
        ];

        return view('admin.lembur.report', compact('analytics', 'month', 'year'));
    }

    /**
     * Export data lembur
     */
    public function export(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $format = $request->get('format', 'pdf'); // pdf or csv

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $lemburs = Lembur::with(['karyawan.user', 'karyawan.department', 'absen.jadwal.shift', 'approvedBy'])
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->orderBy('tanggal_lembur')
            ->get();

        if ($format === 'csv') {
            return $this->exportCsv($lemburs, $month, $year);
        }

        return $this->exportPdf($lemburs, $month, $year);
    }

    private function exportCsv($lemburs, $month, $year)
    {
        $filename = "Laporan_Lembur_{$month}_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($lemburs) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Tanggal',
                'NIP',
                'Nama',
                'Department',
                'Jam Mulai',
                'Jam Selesai',
                'Total Jam',
                'Kategori',
                'Status',
                'Disetujui Oleh',
            ]);

            foreach ($lemburs as $lembur) {
                fputcsv($file, [
                    $lembur->tanggal_lembur->format('d/m/Y'),
                    $lembur->karyawan->nip,
                    $lembur->karyawan->full_name,
                    $lembur->karyawan->department->name ?? '-',
                    $lembur->jam_mulai,
                    $lembur->jam_selesai,
                    $lembur->total_jam,
                    $lembur->kategori_lembur,
                    $lembur->status,
                    $lembur->approvedBy->name ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($lemburs, $month, $year)
    {
        $data = [
            'lemburs' => $lemburs,
            'month' => $month,
            'year' => $year,
            'period' => Carbon::create($year, $month)->format('F Y'),
        ];

        $pdf = Pdf::loadView('admin.lembur.exportPdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("Laporan_Lembur_{$month}_{$year}.pdf");
    }
}
