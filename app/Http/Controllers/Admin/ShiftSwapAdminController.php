<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftSwapRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftSwapAdminController extends Controller
{
    /**
     * Display pending swap requests
     * GET /admin/shift-swap/pending
     */
    public function index(Request $request)
    {
        $query = ShiftSwapRequest::pendingAdminApproval()
            ->with([
                'requesterKaryawan.department',
                'partnerKaryawan.department',
                'requesterJadwal.shift',
                'partnerJadwal.shift'
            ]);

        // Filter untuk koordinator - hanya lihat department mereka
        $user = auth()->user();
        if ($user->role !== 'admin') {
            $userDepartmentId = $user->karyawan->department_id ?? null;

            if ($userDepartmentId) {
                $query->where(function ($q) use ($userDepartmentId) {
                    // Tampilkan jika salah satu karyawan dari department koordinator
                    $q->whereHas('requesterKaryawan', function ($subQuery) use ($userDepartmentId) {
                        $subQuery->where('department_id', $userDepartmentId);
                    })->orWhereHas('partnerKaryawan', function ($subQuery) use ($userDepartmentId) {
                        $subQuery->where('department_id', $userDepartmentId);
                    });
                });
            }
        }

        $swapRequests = $query->recent()->paginate(15);

        return view('admin.shift-swap.indexSw', compact('swapRequests'));
    }

    /**
     * Show detail swap request
     * GET /admin/shift-swap/{swap_id}
     */
    public function show($swap_id)
    {
        $swapRequest = ShiftSwapRequest::with([
            'requesterKaryawan',
            'partnerKaryawan',
            'requesterJadwal.shift',
            'partnerJadwal.shift'
        ])->findOrFail($swap_id);

        return view('admin.shift-swap.showSw', compact('swapRequest'));
    }

    /**
     * Approve swap request
     * POST /admin/shift-swap/{swap_id}/approve
     */
    public function approve(Request $request, $swap_id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            $swapRequest = ShiftSwapRequest::findOrFail($swap_id);

            if ($swapRequest->status !== ShiftSwapRequest::STATUS_PENDING_ADMIN_APPROVAL) {
                return back()->withErrors(['error' => 'Request tidak dalam status pending approval']);
            }

            DB::beginTransaction();

            $swapRequest->approveByAdminAndSwap(
                auth()->user()->user_id,
                $request->admin_notes
            );

            DB::commit();

            // TODO: Send notification to requester & partner

            return redirect()->route('admin.shift-swap.indexSw')
                ->with('success', 'Request tukar shift berhasil disetujui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve swap request', [
                'swap_id' => $swap_id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reject swap request
     * POST /admin/shift-swap/{swap_id}/reject
     */
    public function reject(Request $request, $swap_id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        try {
            $swapRequest = ShiftSwapRequest::findOrFail($swap_id);

            if ($swapRequest->status !== ShiftSwapRequest::STATUS_PENDING_ADMIN_APPROVAL) {
                return back()->withErrors(['error' => 'Request tidak dalam status pending approval']);
            }

            DB::beginTransaction();

            $swapRequest->rejectByAdmin(
                auth()->user()->user_id,
                $request->admin_notes
            );

            DB::commit();

            // TODO: Send notification to requester & partner

            return redirect()->route('admin.shift-swap.indexSw')
                ->with('success', 'Request tukar shift ditolak');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject swap request', [
                'swap_id' => $swap_id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show all swap history
     * GET /admin/shift-swap/history
     */
    public function history(Request $request)
    {
        $status = $request->query('status');

        $query = ShiftSwapRequest::with([
            'requesterKaryawan',
            'partnerKaryawan',
            'requesterJadwal.shift',
            'partnerJadwal.shift',
            'approvedByAdmin'
        ])->recent();

        if ($status) {
            $query->where('status', $status);
        } else {
            // Default: show completed and rejected
            $query->whereIn('status', [
                ShiftSwapRequest::STATUS_COMPLETED,
                ShiftSwapRequest::STATUS_REJECTED_BY_ADMIN,
                ShiftSwapRequest::STATUS_REJECTED_BY_PARTNER
            ]);
        }

        $swapRequests = $query->paginate(20);

        return view('admin.shift-swap.historySw', compact('swapRequests'));
    }
}
