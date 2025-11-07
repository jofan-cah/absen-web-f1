<?php

namespace App\Http\Controllers\Api;

use App\Models\ShiftSwapRequest;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ShiftSwapController extends BaseApiController
{
    /**
     * Create swap request
     * POST /api/shift-swap/request
     */
    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requester_jadwal_id' => 'required|exists:jadwals,jadwal_id',
            'partner_karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'partner_jadwal_id' => 'required|exists:jadwals,jadwal_id',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            // Get jadwals
            $requesterJadwal = Jadwal::with('shift')->find($request->requester_jadwal_id);
            $partnerJadwal = Jadwal::with('shift')->find($request->partner_jadwal_id);

            // Validasi ownership
            if ($requesterJadwal->karyawan_id != $karyawan->karyawan_id) {
                return $this->forbiddenResponse('Jadwal bukan milik Anda');
            }

            if ($partnerJadwal->karyawan_id != $request->partner_karyawan_id) {
                return $this->errorResponse('Jadwal partner tidak valid', 400);
            }

            // Validasi jadwal aktif
            if (!$requesterJadwal->is_active || !$partnerJadwal->is_active) {
                return $this->errorResponse('Jadwal tidak aktif', 400);
            }

            // Validasi sudah ada absensi atau belum
            $requesterAbsen = Absen::where('jadwal_id', $requesterJadwal->jadwal_id)->first();
            $partnerAbsen = Absen::where('jadwal_id', $partnerJadwal->jadwal_id)->first();

            if ($requesterAbsen && ($requesterAbsen->clock_in || $requesterAbsen->clock_out)) {
                return $this->errorResponse('Tidak bisa tukar shift, Anda sudah melakukan absensi', 400);
            }

            if ($partnerAbsen && ($partnerAbsen->clock_in || $partnerAbsen->clock_out)) {
                return $this->errorResponse('Tidak bisa tukar shift, partner sudah melakukan absensi', 400);
            }

            // Validasi tanggal sama (optional - sesuai business rule)
            if ($requesterJadwal->date->format('Y-m-d') != $partnerJadwal->date->format('Y-m-d')) {
                return $this->errorResponse('Hanya bisa tukar shift di tanggal yang sama', 400);
            }

            // Cek existing pending request
            $existingRequest = ShiftSwapRequest::where('requester_jadwal_id', $requesterJadwal->jadwal_id)
                ->whereIn('status', [
                    ShiftSwapRequest::STATUS_PENDING_PARTNER,
                    ShiftSwapRequest::STATUS_APPROVED_BY_PARTNER
                ])
                ->first();

            if ($existingRequest) {
                return $this->errorResponse('Anda sudah punya request pending untuk jadwal ini', 400);
            }

            // Create swap request
            $swapRequest = ShiftSwapRequest::create([
                'swap_id' => ShiftSwapRequest::generateSwapId(),
                'requester_karyawan_id' => $karyawan->karyawan_id,
                'requester_jadwal_id' => $requesterJadwal->jadwal_id,
                'partner_karyawan_id' => $request->partner_karyawan_id,
                'partner_jadwal_id' => $partnerJadwal->jadwal_id,
                'reason' => $request->reason,
                'status' => ShiftSwapRequest::STATUS_PENDING_PARTNER,
            ]);

            DB::commit();

            // TODO: Send notification to partner

            return $this->createdResponse(
                $swapRequest->load(['requesterKaryawan', 'partnerKaryawan', 'requesterJadwal.shift', 'partnerJadwal.shift']),
                'Request tukar shift berhasil dikirim'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create swap request', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal membuat request tukar shift');
        }
    }

    /**
     * Respond to swap request (approve/reject)
     * POST /api/shift-swap/respond/{swap_id}
     */
      public function respondToRequest(Request $request, $swap_id)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $swapRequest = ShiftSwapRequest::with([
                'requesterJadwal.shift',
                'partnerJadwal.shift',
                'requesterKaryawan',
                'partnerKaryawan'
            ])->find($swap_id);

            if (!$swapRequest) {
                return $this->notFoundResponse('Request tidak ditemukan');
            }

            // Validasi ownership
            if ($swapRequest->partner_karyawan_id != $karyawan->karyawan_id) {
                return $this->forbiddenResponse('Request ini bukan untuk Anda');
            }

            // Validasi status
            if (!$swapRequest->canBeRespondedTo()) {
                return $this->errorResponse('Request sudah diproses sebelumnya', 400);
            }

            DB::beginTransaction();

            if ($request->action === 'reject') {
                $swapRequest->reject($request->notes);
                DB::commit();

                // TODO: Send notification to requester

                return $this->successResponse(
                    $swapRequest,
                    'Request tukar shift ditolak'
                );
            }

            // ✅ FIX: Approve by partner (menunggu admin approval)
            if ($request->action === 'approve') {
                $swapRequest->approveByPartner($request->notes); // ✅ Method yang benar
                DB::commit();

                // TODO: Send notification to requester & admin

                return $this->successResponse(
                    $swapRequest->fresh()->load([
                        'requesterJadwal.shift',
                        'partnerJadwal.shift',
                        'requesterKaryawan',
                        'partnerKaryawan'
                    ]),
                    'Request disetujui! Menunggu persetujuan admin/koordinator'
                );
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to respond to swap request', [
                'swap_id' => $swap_id,
                'error' => $e->getMessage()
            ]);
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    /**
     * Cancel swap request
     * POST /api/shift-swap/cancel/{swap_id}
     */
    public function cancelRequest($swap_id)
    {
        try {
            $user = request()->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $swapRequest = ShiftSwapRequest::find($swap_id);

            if (!$swapRequest) {
                return $this->notFoundResponse('Request tidak ditemukan');
            }

            // Validasi ownership
            if ($swapRequest->requester_karyawan_id != $karyawan->karyawan_id) {
                return $this->forbiddenResponse('Hanya requester yang bisa membatalkan');
            }

            // Validasi status
            if (!$swapRequest->canBeCancelled()) {
                return $this->errorResponse('Request tidak dapat dibatalkan', 400);
            }

            DB::beginTransaction();
            $swapRequest->cancel();
            DB::commit();

            // TODO: Send notification to partner

            return $this->successResponse(
                $swapRequest,
                'Request tukar shift berhasil dibatalkan'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel swap request', [
                'swap_id' => $swap_id,
                'error' => $e->getMessage()
            ]);
            return $this->serverErrorResponse('Gagal membatalkan request');
        }
    }

    /**
     * Get swap history
     * GET /api/shift-swap/history
     */
    public function getHistory(Request $request)
    {
        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $status = $request->query('status');
            $perPage = $this->getPerPage($request);

            $query = ShiftSwapRequest::forKaryawan($karyawan->karyawan_id)
                ->with([
                    'requesterKaryawan',
                    'partnerKaryawan',
                    'requesterJadwal.shift',
                    'partnerJadwal.shift'
                ])
                ->recent();

            if ($status) {
                $query->where('status', $status);
            }

            $history = $query->paginate($perPage);

            return $this->paginatedResponse($history, 'History tukar shift berhasil diambil');

        } catch (\Exception $e) {
            Log::error('Failed to get swap history', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil history');
        }
    }

    /**
     * Get pending requests for current user
     * GET /api/shift-swap/pending
     */
    public function getPendingRequests(Request $request)
    {
        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            // Get requests where user is partner (menunggu approval user)
            $pendingRequests = ShiftSwapRequest::where('partner_karyawan_id', $karyawan->karyawan_id)
                ->pending()
                ->with([
                    'requesterKaryawan',
                    'requesterJadwal.shift',
                    'partnerJadwal.shift'
                ])
                ->recent()
                ->get();

            return $this->successResponse(
                $pendingRequests,
                'Pending requests berhasil diambil'
            );

        } catch (\Exception $e) {
            Log::error('Failed to get pending requests', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil pending requests');
        }
    }

    /**
     * Get available jadwals for swap
     * GET /api/shift-swap/available-jadwals
     */
    public function getAvailableJadwals(Request $request)
    {
        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $date = $request->query('date');

            if (!$date) {
                return $this->errorResponse('Parameter date wajib diisi', 400);
            }

            // Get jadwals for specified date, exclude current user, only active & no swap
            $availableJadwals = Jadwal::where('date', $date)
                ->where('karyawan_id', '!=', $karyawan->karyawan_id)
                ->where('is_active', true)
                ->whereNull('swap_id')
                ->whereDoesntHave('absen', function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('clock_in')
                          ->orWhereNotNull('clock_out');
                    });
                })
                ->with(['karyawan', 'shift'])
                ->get();

            return $this->successResponse(
                $availableJadwals,
                'Jadwal tersedia berhasil diambil'
            );

        } catch (\Exception $e) {
            Log::error('Failed to get available jadwals', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil jadwal tersedia');
        }
    }
}
