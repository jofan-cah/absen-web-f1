<?php

namespace App\Http\Controllers\Api;

use App\Models\Ijin;
use App\Models\IjinType;
use App\Models\Karyawan;
use App\Models\Absen;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class IjinController extends BaseApiController
{
    /**
     * Get all ijin types (untuk dropdown)
     * GET /api/ijin/types
     */
    public function getIjinTypes()
    {
        try {
            $types = IjinType::where('is_active', true)
                ->select('ijin_type_id', 'name', 'code', 'description')
                ->orderBy('name')
                ->get();

            return $this->successResponse($types, 'Berhasil mengambil data tipe ijin');
        } catch (\Exception $e) {
            Log::error('Failed to get ijin types', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil data tipe ijin');
        }
    }

    /**
     * Get ijin history untuk user
     * GET /api/ijin/my-history
     */
    public function myHistory(Request $request)
    {
        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $status = $request->query('status');
            $type = $request->query('type');
            $perPage = $this->getPerPage($request);

            $query = Ijin::with(['ijinType'])
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->orderBy('created_at', 'desc');

            if ($status) {
                $query->where('status', $status);
            }

            if ($type) {
                $query->where('ijin_type_id', $type);
            }

            $ijins = $query->paginate($perPage);

            $ijins->getCollection()->transform(function ($ijin) {
                return [
                    'ijin_id' => $ijin->ijin_id,
                    'ijin_type' => [
                        'id' => $ijin->ijinType->ijin_type_id,
                        'name' => $ijin->ijinType->name,
                        'code' => $ijin->ijinType->code,
                    ],
                    'date_from' => $ijin->date_from->format('Y-m-d'),
                    'date_to' => $ijin->date_to->format('Y-m-d'),
                    'total_days' => $ijin->total_days,
                    'reason' => $ijin->reason,
                    'status' => $ijin->status,
                    'status_label' => $this->getStatusLabel($ijin),
                    'can_cancel' => $ijin->status === 'pending',
                    'created_at' => $ijin->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return $this->paginatedResponse($ijins, 'Berhasil mengambil data riwayat ijin');

        } catch (\Exception $e) {
            Log::error('Failed to get ijin history', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil data riwayat ijin');
        }
    }

    /**
     * Get detail ijin
     * GET /api/ijin/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $ijin = Ijin::with(['ijinType'])
                ->where('ijin_id', $id)
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->first();

            if (!$ijin) {
                return $this->notFoundResponse('Data ijin tidak ditemukan');
            }

            $data = [
                'ijin_id' => $ijin->ijin_id,
                'ijin_type' => [
                    'id' => $ijin->ijinType->ijin_type_id,
                    'name' => $ijin->ijinType->name,
                    'code' => $ijin->ijinType->code,
                ],
                'date_from' => $ijin->date_from->format('Y-m-d'),
                'date_to' => $ijin->date_to->format('Y-m-d'),
                'total_days' => $ijin->total_days,
                'reason' => $ijin->reason,
                'original_shift_date' => $ijin->original_shift_date?->format('Y-m-d'),
                'replacement_shift_date' => $ijin->replacement_shift_date?->format('Y-m-d'),
                'status' => $ijin->status,
                'status_label' => $this->getStatusLabel($ijin),
                'coordinator_status' => $ijin->coordinator_status,
                'admin_status' => $ijin->admin_status,
                'coordinator_note' => $ijin->coordinator_note,
                'admin_note' => $ijin->admin_note,
                'can_cancel' => $ijin->status === 'pending',
                'created_at' => $ijin->created_at->format('Y-m-d H:i:s'),
            ];

            return $this->successResponse($data, 'Berhasil mengambil detail ijin');

        } catch (\Exception $e) {
            Log::error('Failed to get ijin detail', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil detail ijin');
        }
    }

    /**
     * Submit ijin request (Sakit, Cuti, Pribadi)
     * POST /api/ijin/submit
     */
    public function submitIjin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ijin_type_id' => 'required|exists:ijin_types,ijin_type_id',
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after_or_equal:date_from',
            'reason' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
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

            $ijinType = IjinType::find($request->ijin_type_id);

            if (in_array($ijinType->code, ['shift_swap', 'compensation_leave'])) {
                return $this->errorResponse(
                    'Gunakan endpoint khusus untuk tukar shift atau cuti pengganti',
                    400
                );
            }

            // Cek overlap
            $overlap = $this->checkOverlap($karyawan->karyawan_id, $request->date_from, $request->date_to);
            if ($overlap) {
                return $this->errorResponse(
                    'Terdapat ijin lain yang bentrok dengan tanggal yang dipilih',
                    400
                );
            }

            $coordinator = $this->getCoordinator($karyawan);
                    $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = 'ijin/' . $karyawan->karyawan_id . '/' . time() . '_' . $file->getClientOriginalName();
                $photoPath = Storage::disk('s3')->putFileAs('', $file, $fileName, 'private');
            }

            $ijin = Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $request->ijin_type_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'reason' => $request->reason,
                'coordinator_id' => $coordinator?->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
                'status' => 'pending',
                'photo_path' => $photoPath,
            ]);

            DB::commit();

            Log::info('Ijin submitted via API', [
                'ijin_id' => $ijin->ijin_id,
                'karyawan_id' => $karyawan->karyawan_id,
                'type' => $ijinType->code,
            ]);

            return $this->createdResponse([
                'ijin_id' => $ijin->ijin_id,
                'status' => $ijin->status,
                'message' => 'Pengajuan ijin Anda sedang diproses',
            ], 'Pengajuan ijin berhasil dikirim');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit ijin via API', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id ?? null,
            ]);

            return $this->serverErrorResponse('Gagal mengajukan ijin');
        }
    }

    /**
     * Submit tukar shift request
     * POST /api/ijin/shift-swap
     */
    public function submitShiftSwap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_shift_date' => 'required|date|after:today',
            'replacement_shift_date' => 'required|date|after:original_shift_date',
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

            $originalDate = Carbon::parse($request->original_shift_date);
            // if ($originalDate->dayOfWeek !== Carbon::SUNDAY) {
            //     return $this->errorResponse('Tanggal asli harus hari Minggu', 400);
            // }

            $replacementDate = Carbon::parse($request->replacement_shift_date);
            if ($replacementDate->dayOfWeek === Carbon::SUNDAY) {
                return $this->errorResponse('Tanggal pengganti tidak boleh hari Minggu', 400);
            }

            $originalSchedule = Jadwal::where('karyawan_id', $karyawan->karyawan_id)
                ->where('date', $request->original_shift_date)
                ->where('is_active', true)
                ->first();

            if (!$originalSchedule) {
                return $this->errorResponse('Tidak ada jadwal piket di tanggal yang dipilih', 400);
            }

            $replacementOverlap = $this->checkOverlap(
                $karyawan->karyawan_id,
                $request->replacement_shift_date,
                $request->replacement_shift_date
            );

            if ($replacementOverlap) {
                return $this->errorResponse('Terdapat ijin lain di tanggal pengganti', 400);
            }

            $ijinType = IjinType::where('code', 'shift_swap')->first();
            $coordinator = $this->getCoordinator($karyawan);

            $ijin = Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $ijinType->ijin_type_id,
                'date_from' => $request->original_shift_date,
                'date_to' => $request->original_shift_date,
                'original_shift_date' => $request->original_shift_date,
                'replacement_shift_date' => $request->replacement_shift_date,
                'reason' => $request->reason,
                'coordinator_id' => $coordinator?->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
                'status' => 'pending',
            ]);

            DB::commit();

            Log::info('Shift swap submitted via API', [
                'ijin_id' => $ijin->ijin_id,
                'original_date' => $request->original_shift_date,
                'replacement_date' => $request->replacement_shift_date,
            ]);

            return $this->createdResponse([
                'ijin_id' => $ijin->ijin_id,
                'status' => $ijin->status,
            ], 'Pengajuan tukar shift berhasil dikirim');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit shift swap via API', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengajukan tukar shift');
        }
    }

    /**
     * Submit cuti pengganti request
     * POST /api/ijin/compensation-leave
     */
    public function submitCompensationLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_shift_date' => 'required|date|before:today',
            'date_from' => 'required|date|after:today',
            'date_to' => 'required|date|after_or_equal:date_from',
            'reason' => 'nullable|string|max:500',
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

            // $originalDate = Carbon::parse($request->original_shift_date);
            // if ($originalDate->dayOfWeek !== Carbon::SUNDAY) {
            //     return $this->errorResponse('Tanggal piket harus hari Minggu', 400);
            // }

            $attendance = Absen::where('karyawan_id', $karyawan->karyawan_id)
                ->where('date', $request->original_shift_date)
                ->where('status', 'present')
                ->first();

            if (!$attendance) {
                return $this->errorResponse('Anda belum tercatat piket di tanggal tersebut', 400);
            }

            $alreadyClaimed = Ijin::where('karyawan_id', $karyawan->karyawan_id)
                ->where('original_shift_date', $request->original_shift_date)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($alreadyClaimed) {
                return $this->errorResponse(
                    'Tanggal piket ini sudah pernah diajukan untuk cuti pengganti',
                    400
                );
            }

            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);

            if ($dateFrom->dayOfWeek === Carbon::SUNDAY || $dateTo->dayOfWeek === Carbon::SUNDAY) {
                return $this->errorResponse('Tanggal cuti pengganti tidak boleh hari Minggu', 400);
            }

            $overlap = $this->checkOverlap($karyawan->karyawan_id, $request->date_from, $request->date_to);
            if ($overlap) {
                return $this->errorResponse(
                    'Terdapat ijin lain yang bentrok dengan tanggal yang dipilih',
                    400
                );
            }

            $ijinType = IjinType::where('code', 'compensation_leave')->first();
            $coordinator = $this->getCoordinator($karyawan);

            $ijin = Ijin::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'ijin_type_id' => $ijinType->ijin_type_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'original_shift_date' => $request->original_shift_date,
                'reason' => $request->reason ?? 'Kompensasi piket tanggal ' . $originalDate->format('d/m/Y'),
                'coordinator_id' => $coordinator?->user_id,
                'coordinator_status' => 'pending',
                'admin_status' => 'pending',
                'status' => 'pending',
            ]);

            DB::commit();

            Log::info('Compensation leave submitted via API', [
                'ijin_id' => $ijin->ijin_id,
                'original_shift_date' => $request->original_shift_date,
            ]);

            return $this->createdResponse([
                'ijin_id' => $ijin->ijin_id,
                'status' => $ijin->status,
            ], 'Pengajuan cuti pengganti berhasil dikirim');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit compensation leave via API', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengajukan cuti pengganti');
        }
    }

    /**
     * Cancel ijin (hanya untuk status pending)
     * DELETE /api/ijin/{id}
     */
    public function cancel(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            $ijin = Ijin::where('ijin_id', $id)
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->first();

            if (!$ijin) {
                return $this->notFoundResponse('Data ijin tidak ditemukan');
            }

            if ($ijin->status !== 'pending') {
                return $this->errorResponse(
                    'Hanya ijin dengan status pending yang bisa dibatalkan',
                    400
                );
            }

            $ijin->delete();

            DB::commit();

            Log::info('Ijin cancelled via API', [
                'ijin_id' => $id,
                'karyawan_id' => $karyawan->karyawan_id,
            ]);

            return $this->noContentResponse('Pengajuan ijin berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel ijin', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal membatalkan ijin');
        }
    }

    /**
     * Get available piket dates untuk compensation leave
     * GET /api/ijin/available-piket-dates
     */
    public function getAvailablePiketDates(Request $request)
    {
        try {
            $user = $request->user();
            $karyawan = Karyawan::where('user_id', $user->user_id)->first();

            if (!$karyawan) {
                return $this->notFoundResponse('Data karyawan tidak ditemukan');
            }

            $piketDates = Absen::where('karyawan_id', $karyawan->karyawan_id)
                ->where('status', 'present')
                ->whereRaw('DAYOFWEEK(date) = 1')
                ->where('date', '<', now())
                ->whereNotIn('date', function($query) use ($karyawan) {
                    $query->select('original_shift_date')
                        ->from('ijins')
                        ->where('karyawan_id', $karyawan->karyawan_id)
                        ->whereIn('status', ['pending', 'approved'])
                        ->whereNotNull('original_shift_date');
                })
                ->orderBy('date', 'desc')
                ->get(['date'])
                ->map(function($item) {
                    return [
                        'date' => $item->date->format('Y-m-d'),
                        'formatted_date' => $item->date->format('d/m/Y'),
                        'day_name' => 'Minggu'
                    ];
                });

            if ($piketDates->isEmpty()) {
                return $this->emptyResponse('Tidak ada piket yang tersedia untuk diklaim');
            }

            return $this->successResponse($piketDates, 'Berhasil mengambil data piket yang tersedia');

        } catch (\Exception $e) {
            Log::error('Failed to get available piket dates', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal mengambil data piket');
        }
    }

    // PRIVATE HELPERS

    private function getCoordinator($karyawan)
    {
        $coordinator = Karyawan::where('department_id', $karyawan->department_id)
            ->whereIn('staff_status', ['koordinator', 'wakil_koordinator'])
            ->where('employment_status', 'active')
            ->first();

        return $coordinator?->user;
    }

    private function checkOverlap($karyawanId, $dateFrom, $dateTo)
    {
        return Ijin::where('karyawan_id', $karyawanId)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('date_from', [$dateFrom, $dateTo])
                    ->orWhereBetween('date_to', [$dateFrom, $dateTo])
                    ->orWhere(function($q) use ($dateFrom, $dateTo) {
                        $q->where('date_from', '<=', $dateFrom)
                          ->where('date_to', '>=', $dateTo);
                    });
            })
            ->exists();
    }

    private function getStatusLabel($ijin)
    {
        if ($ijin->status === 'approved') {
            return 'Disetujui';
        } elseif ($ijin->status === 'rejected') {
            return 'Ditolak';
        } else {
            // Pending - show detail status
            if ($ijin->coordinator_status === 'rejected') {
                return 'Ditolak oleh Koordinator';
            } elseif ($ijin->admin_status === 'rejected') {
                return 'Ditolak oleh Admin';
            } elseif ($ijin->coordinator_status === 'pending') {
                return 'Menunggu Koordinator';
            } elseif ($ijin->admin_status === 'pending') {
                return 'Menunggu Admin';
            }
            return 'Sedang Diproses';
        }
    }
}
