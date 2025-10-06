<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Ijin;
use App\Models\IjinType;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IjinController extends Controller
{
    /**
     * Display list of all ijin requests
     */
    public function index(Request $request)
    {
        $query = Ijin::with(['karyawan.department', 'ijinType', 'coordinator', 'admin']);

        // Search by karyawan name or NIP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by ijin type
        if ($request->filled('ijin_type_id')) {
            $query->where('ijin_type_id', $request->ijin_type_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date_from', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_to', '<=', $request->date_to);
        }

        $ijins = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk filter dropdown
        $ijinTypes = IjinType::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->get();
        $statusOptions = ['pending', 'approved', 'rejected'];

        return view('admin.ijin.indexIjin', compact(
            'ijins',
            'ijinTypes',
            'departments',
            'statusOptions'
        ));
    }

    /**
     * Display pending ijins for coordinator review
     */
    public function coordinatorPending(Request $request)
    {
        $user = auth()->user();

        $query = Ijin::with(['karyawan.department', 'ijinType'])
            ->where('coordinator_id', $user->user_id)
            ->where('coordinator_status', 'pending')
            ->where('status', 'pending');

        $ijins = $query->orderBy('created_at', 'asc')->paginate(15);

        return view('admin.ijin.coordinator-pending', compact('ijins'));
    }

    /**
     * Display pending ijins for admin review
     * Admin bisa lihat SEMUA pending (termasuk yang belum direview coordinator)
     */
    public function adminPending(Request $request)
    {
        $query = Ijin::with(['karyawan.department', 'ijinType', 'coordinator'])
            ->where('status', 'pending');

        // Optional: Filter berdasarkan coordinator status
        if ($request->filled('coordinator_status')) {
            $query->where('coordinator_status', $request->coordinator_status);
        }

        $ijins = $query->orderBy('created_at', 'asc')->paginate(15);

        // Statistik untuk card
        $stats = [
            'total_pending' => Ijin::where('status', 'pending')->count(),
            'waiting_coordinator' => Ijin::where('status', 'pending')
                ->where('coordinator_status', 'pending')
                ->count(),
            'coordinator_approved' => Ijin::where('status', 'pending')
                ->where('coordinator_status', 'approved')
                ->where('admin_status', 'pending')
                ->count(),
        ];

        return view('admin.ijin.admin-pending', compact('ijins', 'stats'));
    }

    /**
     * Show detail ijin
     */
    public function show(Ijin $ijin)
    {
        $ijin->load([
            'karyawan.department',
            'ijinType',
            'coordinator',
            'admin',
            'jadwals.shift',
            'absens',
        ]);

        return view('admin.ijin.showIjin', compact('ijin'));
    }

    /**
     * Coordinator review form
     */
    public function coordinatorReviewForm(Ijin $ijin)
    {
        $user = auth()->user();

        // Validasi akses
        if ($ijin->coordinator_id !== $user->user_id && $user->role !== 'admin') {
            return redirect()->back()->withErrors([
                'access' => 'Anda tidak berwenang mereview ijin ini'
            ]);
        }

        if ($ijin->coordinator_status !== 'pending' || $ijin->status !== 'pending') {
            return redirect()->back()->withErrors([
                'status' => 'Ijin ini sudah direview'
            ]);
        }

        $ijin->load(['karyawan.department', 'ijinType']);

        return view('admin.ijin.coordinator-review', compact('ijin'));
    }

    /**
     * Process coordinator review
     */
    public function coordinatorReview(Request $request, Ijin $ijin)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Validasi akses
            if ($ijin->coordinator_id !== $user->user_id && $user->role !== 'admin') {
                return back()->withErrors(['access' => 'Akses ditolak']);
            }

            if ($ijin->coordinator_status !== 'pending' || $ijin->status !== 'pending') {
                return back()->withErrors(['status' => 'Ijin ini sudah direview']);
            }

            if ($request->action === 'approve') {
                $ijin->update([
                    'coordinator_status' => 'approved',
                    'coordinator_note' => $request->note,
                    'coordinator_reviewed_at' => now(),
                ]);

                $message = 'Ijin berhasil di-approve, menunggu persetujuan admin';

            } else { // reject
                $ijin->update([
                    'coordinator_status' => 'rejected',
                    'coordinator_note' => $request->note,
                    'coordinator_reviewed_at' => now(),
                    'status' => 'rejected',
                ]);

                $message = 'Ijin berhasil ditolak';
            }

            DB::commit();

            return redirect()->route('admin.ijin.coordinator-pending')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Coordinator Review Failed', [
                'ijin_id' => $ijin->ijin_id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Gagal mereview ijin: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Admin review form
     */
    public function adminReviewForm(Ijin $ijin)
    {
        if ($ijin->status !== 'pending') {
            return redirect()->back()->withErrors([
                'status' => 'Ijin ini sudah direview'
            ]);
        }

        $ijin->load(['karyawan.department', 'ijinType', 'coordinator']);

        // Flag untuk menampilkan bypass option
        $needsBypass = $ijin->coordinator_status === 'pending';

        return view('admin.ijin.admin-review', compact('ijin', 'needsBypass'));
    }

    /**
     * Process admin review
     * BISA BYPASS coordinator jika belum approve
     */
    public function adminReview(Request $request, Ijin $ijin)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500',
            'bypass_coordinator' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();

            if ($user->role !== 'admin') {
                return back()->withErrors(['access' => 'Akses ditolak']);
            }

            if ($ijin->status !== 'pending') {
                return back()->withErrors(['status' => 'Ijin ini sudah direview']);
            }

            if ($request->action === 'approve') {
                $bypassUsed = false;

                // BYPASS LOGIC dengan validasi ketat
                if ($ijin->coordinator_status === 'pending') {
                    // Cek apakah admin EXPLICITLY request bypass
                    if (!$request->boolean('bypass_coordinator')) {
                        return back()->withErrors([
                            'coordinator' => 'Ijin belum di-approve koordinator. Centang "Bypass Coordinator" jika ingin melewati approval koordinator.'
                        ])->withInput();
                    }

                    // LOG BYPASS untuk audit
                    Log::warning('IJIN BYPASS COORDINATOR', [
                        'ijin_id' => $ijin->ijin_id,
                        'karyawan' => $ijin->karyawan->full_name,
                        'karyawan_id' => $ijin->karyawan_id,
                        'ijin_type' => $ijin->ijinType->name,
                        'bypassed_by_user_id' => $user->user_id,
                        'bypassed_by_name' => $user->name,
                        'bypassed_by_email' => $user->email,
                        'timestamp' => now()->toDateTimeString(),
                    ]);

                    $ijin->update([
                        'coordinator_status' => 'approved',
                        'coordinator_note' => "⚠️ BYPASS oleh Admin: {$user->name} pada " . now()->format('d/m/Y H:i'),
                        'coordinator_reviewed_at' => now(),
                    ]);

                    $bypassUsed = true;
                }

                // Update admin approval
                $ijin->update([
                    'admin_id' => $user->user_id,
                    'admin_status' => 'approved',
                    'admin_note' => $request->note,
                    'admin_reviewed_at' => now(),
                    'status' => 'approved',
                ]);

                // TRIGGER: Apply ke jadwal & absen (via Model boot event)
                // Sudah otomatis via Ijin::boot() updated event

                $message = $bypassUsed
                    ? '⚠️ Ijin berhasil di-approve dengan BYPASS coordinator'
                    : 'Ijin berhasil di-approve';

            } else { // reject
                $ijin->update([
                    'admin_id' => $user->user_id,
                    'admin_status' => 'rejected',
                    'admin_note' => $request->note,
                    'admin_reviewed_at' => now(),
                    'status' => 'rejected',
                ]);

                // TRIGGER: Remove dari jadwal & absen (via Model boot event)
                // Sudah otomatis via Ijin::boot() updated event

                $message = 'Ijin berhasil ditolak';
            }

            DB::commit();

            return redirect()->route('admin.ijin.admin-pending')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin Review Failed', [
                'ijin_id' => $ijin->ijin_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Gagal mereview ijin: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Statistics dashboard
     */
    public function statistics(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $stats = [
            'total_pending' => Ijin::where('status', 'pending')->count(),
            'pending_coordinator' => Ijin::where('coordinator_status', 'pending')
                ->where('status', 'pending')->count(),
            'pending_admin' => Ijin::where('coordinator_status', 'approved')
                ->where('admin_status', 'pending')
                ->where('status', 'pending')->count(),
            'total_approved_this_month' => Ijin::where('status', 'approved')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count(),
            'total_rejected_this_month' => Ijin::where('status', 'rejected')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count(),
        ];

        // By type
        $byType = Ijin::with('ijinType')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy('ijin_type_id')
            ->map(function ($items) {
                return [
                    'type' => $items->first()->ijinType->name,
                    'total' => $items->count(),
                    'approved' => $items->where('status', 'approved')->count(),
                    'rejected' => $items->where('status', 'rejected')->count(),
                    'pending' => $items->where('status', 'pending')->count(),
                ];
            })->values();

        return view('admin.ijin.statisticsIjin', compact('stats', 'byType', 'month', 'year'));
    }
}
