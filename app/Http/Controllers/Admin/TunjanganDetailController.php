<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TunjanganDetail;
use App\Models\TunjanganType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TunjanganDetailController extends Controller
{
    public function index(Request $request)
    {
        $query = TunjanganDetail::with(['tunjanganType']);

        // Filter by tunjangan type
        if ($request->filled('tunjangan_type_id')) {
            $query->where('tunjangan_type_id', $request->tunjangan_type_id);
        }

        // Filter by staff status
        if ($request->filled('staff_status')) {
            $query->where('staff_status', $request->staff_status);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $tunjanganDetails = $query->orderBy('tunjangan_type_id')
            ->orderBy('staff_status')
            ->orderBy('effective_date', 'desc')
            ->paginate(15);

        $tunjanganTypes = TunjanganType::active()->get();

        return view('admin.tunjangan-detail.indexTunDet', compact('tunjanganDetails', 'tunjanganTypes'));
    }

    public function create()
    {
        $tunjanganTypes = TunjanganType::active()->get();
        $staffStatuses = ['pkwtt', 'karyawan', 'koordinator', 'wakil_koordinator'];

        return view('admin.tunjangan-detail.createTunDet', compact('tunjanganTypes', 'staffStatuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tunjangan_type_id' => 'required|exists:tunjangan_types,tunjangan_type_id',
            'staff_status' => 'required|in:pkwtt,karyawan,koordinator,staff',
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Check jika sudah ada detail aktif untuk kombinasi yang sama pada periode yang overlap
            $existingDetail = TunjanganDetail::where('tunjangan_type_id', $request->tunjangan_type_id)
                ->where('staff_status', $request->staff_status)
                ->where('is_active', true)
                ->where(function($query) use ($request) {
                    $query->where(function($q) use ($request) {
                        // Case 1: effective_date baru berada dalam range existing
                        $q->where('effective_date', '<=', $request->effective_date)
                          ->where(function($subQ) use ($request) {
                              $subQ->whereNull('end_date')
                                   ->orWhere('end_date', '>=', $request->effective_date);
                          });
                    })->orWhere(function($q) use ($request) {
                        // Case 2: end_date baru (jika ada) berada dalam range existing
                        if ($request->end_date) {
                            $q->where('effective_date', '<=', $request->end_date)
                              ->where(function($subQ) use ($request) {
                                  $subQ->whereNull('end_date')
                                       ->orWhere('end_date', '>=', $request->end_date);
                              });
                        }
                    });
                })
                ->exists();

            if ($existingDetail) {
                return back()
                    ->withInput()
                    ->with('error', 'Sudah ada detail nominal aktif untuk kombinasi jenis tunjangan dan staff status ini pada periode yang sama!');
            }

            TunjanganDetail::create([
                'tunjangan_detail_id' => TunjanganDetail::generateTunjanganDetailId(),
                'tunjangan_type_id' => $request->tunjangan_type_id,
                'staff_status' => $request->staff_status,
                'amount' => $request->amount,
                'effective_date' => $request->effective_date,
                'end_date' => $request->end_date,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.tunjangan-detail.index')
                ->with('success', 'Detail nominal tunjangan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat detail nominal: ' . $e->getMessage());
        }
    }

    public function show(TunjanganDetail $tunjanganDetail)
    {
        $tunjanganDetail->load('tunjanganType');
        

        return view('admin.tunjangan-detail.showTunDet', compact('tunjanganDetail'));
    }

    public function edit(TunjanganDetail $tunjanganDetail)
    {
        $tunjanganTypes = TunjanganType::active()->get();
        $staffStatuses = ['pkwtt', 'karyawan', 'koordinator', 'wakil_koordinator'];

        return view('admin.tunjangan-detail.editTunDet', compact('tunjanganDetail', 'tunjanganTypes', 'staffStatuses'));
    }

    public function update(Request $request, TunjanganDetail $tunjanganDetail)
    {
        $request->validate([
            'tunjangan_type_id' => 'required|exists:tunjangan_types,tunjangan_type_id',
            'staff_status' => 'required|in:pkwtt,karyawan,koordinator,wakil_koordinator',
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Check overlap kecuali untuk record yang sedang diedit
            $existingDetail = TunjanganDetail::where('tunjangan_type_id', $request->tunjangan_type_id)
                ->where('staff_status', $request->staff_status)
                ->where('is_active', true)
                ->where('tunjangan_detail_id', '!=', $tunjanganDetail->tunjangan_detail_id)
                ->where(function($query) use ($request) {
                    $query->where(function($q) use ($request) {
                        $q->where('effective_date', '<=', $request->effective_date)
                          ->where(function($subQ) use ($request) {
                              $subQ->whereNull('end_date')
                                   ->orWhere('end_date', '>=', $request->effective_date);
                          });
                    })->orWhere(function($q) use ($request) {
                        if ($request->end_date) {
                            $q->where('effective_date', '<=', $request->end_date)
                              ->where(function($subQ) use ($request) {
                                  $subQ->whereNull('end_date')
                                       ->orWhere('end_date', '>=', $request->end_date);
                              });
                        }
                    });
                })
                ->exists();

            if ($existingDetail) {
                return back()
                    ->withInput()
                    ->with('error', 'Sudah ada detail nominal aktif untuk kombinasi jenis tunjangan dan staff status ini pada periode yang sama!');
            }

            $tunjanganDetail->update([
                'tunjangan_type_id' => $request->tunjangan_type_id,
                'staff_status' => $request->staff_status,
                'amount' => $request->amount,
                'effective_date' => $request->effective_date,
                'end_date' => $request->end_date,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.tunjangan-detail.index')
                ->with('success', 'Detail nominal tunjangan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui detail nominal: ' . $e->getMessage());
        }
    }

    public function destroy(TunjanganDetail $tunjanganDetail)
    {
        try {
            DB::beginTransaction();

            $tunjanganDetail->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail nominal tunjangan berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail nominal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(TunjanganDetail $tunjanganDetail)
    {
        try {
            DB::beginTransaction();

            $tunjanganDetail->update([
                'is_active' => !$tunjanganDetail->is_active
            ]);

            DB::commit();

            $status = $tunjanganDetail->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Detail nominal berhasil {$status}!",
                'is_active' => $tunjanganDetail->is_active
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tunjangan_details,tunjangan_detail_id'
        ]);

        try {
            DB::beginTransaction();

            TunjanganDetail::whereIn('tunjangan_detail_id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' detail nominal berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tunjangan_details,tunjangan_detail_id',
            'status' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            TunjanganDetail::whereIn('tunjangan_detail_id', $request->ids)
                ->update(['is_active' => $request->status]);

            DB::commit();

            $action = $request->status ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . " detail nominal berhasil {$action}!"
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan nominal berdasarkan tunjangan type dan staff status
    public function getAmountByStaffStatus(Request $request)
    {
        $request->validate([
            'tunjangan_type_id' => 'required|exists:tunjangan_types,tunjangan_type_id',
            'staff_status' => 'required|in:pkwtt,karyawan,koordinator,staff'
        ]);

        $amount = TunjanganDetail::getAmountByStaffStatus(
            $request->tunjangan_type_id,
            $request->staff_status
        );

        return response()->json([
            'success' => true,
            'amount' => $amount
        ]);
    }

    // Export functionality
    public function export()
    {
        try {
            $tunjanganDetails = TunjanganDetail::with('tunjanganType')->get();

            $filename = 'tunjangan_details_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($tunjanganDetails) {
                $file = fopen('php://output', 'w');

                // Header CSV
                fputcsv($file, [
                    'ID', 'Jenis Tunjangan', 'Staff Status', 'Nominal',
                    'Tanggal Berlaku', 'Tanggal Berakhir', 'Status', 'Tanggal Dibuat'
                ]);

                // Data rows
                foreach ($tunjanganDetails as $detail) {
                    fputcsv($file, [
                        $detail->tunjangan_detail_id,
                        $detail->tunjanganType->name,
                        ucfirst(str_replace('_', ' ', $detail->staff_status)),
                        number_format($detail->amount, 0, ',', '.'),
                        $detail->effective_date->format('d-m-Y'),
                        $detail->end_date ? $detail->end_date->format('d-m-Y') : '-',
                        $detail->is_active ? 'Aktif' : 'Nonaktif',
                        $detail->created_at->format('d-m-Y H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}
