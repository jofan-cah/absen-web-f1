<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TunjanganType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TunjanganTypeController extends Controller
{
    public function index()
    {
        $tunjanganTypes = TunjanganType::with('tunjanganDetails')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.tunjangan-type.indexTunType', compact('tunjanganTypes'));
    }

    public function create()
    {
        return view('admin.tunjangan-type.createTunType');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:tunjangan_types,code',
            'category' => 'required|in:harian,mingguan,bulanan',
            'base_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            TunjanganType::create([
                'tunjangan_type_id' => TunjanganType::generateTunjanganTypeId(),
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'category' => $request->category,
                'base_amount' => $request->base_amount,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.tunjangan-type.index')
                ->with('success', 'Jenis tunjangan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat jenis tunjangan: ' . $e->getMessage());
        }
    }

    public function show(TunjanganType $tunjanganType)
    {
        $tunjanganType->load(['tunjanganDetails' => function($query) {
            $query->orderBy('staff_status')->orderBy('effective_date', 'desc');
        }, 'tunjanganKaryawan' => function($query) {
            $query->with('karyawan')->latest()->take(5);
        }]);

        return view('admin.tunjangan-type.showTunType', compact('tunjanganType'));
    }

    public function edit(TunjanganType $tunjanganType)
    {
        return view('admin.tunjangan-type.editTunType', compact('tunjanganType'));
    }

    public function update(Request $request, TunjanganType $tunjanganType)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tunjangan_types', 'code')->ignore($tunjanganType->tunjangan_type_id, 'tunjangan_type_id')
            ],
            'category' => 'required|in:harian,mingguan,bulanan',
            'base_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $tunjanganType->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'category' => $request->category,
                'base_amount' => $request->base_amount,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.tunjangan-type.index')
                ->with('success', 'Jenis tunjangan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui jenis tunjangan: ' . $e->getMessage());
        }
    }

    public function destroy(TunjanganType $tunjanganType)
    {
        try {
            // Check jika masih ada tunjangan detail atau transaksi terkait
            if ($tunjanganType->tunjanganDetails()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus jenis tunjangan yang masih memiliki detail nominal!'
                ], 422);
            }

            if ($tunjanganType->tunjanganKaryawan()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus jenis tunjangan yang masih memiliki transaksi!'
                ], 422);
            }

            DB::beginTransaction();

            $tunjanganType->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jenis tunjangan berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jenis tunjangan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(TunjanganType $tunjanganType)
    {
        try {
            DB::beginTransaction();

            $tunjanganType->update([
                'is_active' => !$tunjanganType->is_active
            ]);

            DB::commit();

            $status = $tunjanganType->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Jenis tunjangan berhasil {$status}!",
                'is_active' => $tunjanganType->is_active
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
            'ids.*' => 'exists:tunjangan_types,tunjangan_type_id'
        ]);

        try {
            DB::beginTransaction();

            $tunjanganTypes = TunjanganType::whereIn('tunjangan_type_id', $request->ids)->get();

            foreach ($tunjanganTypes as $tunjanganType) {
                // Check dependencies
                if ($tunjanganType->tunjanganDetails()->exists() ||
                    $tunjanganType->tunjanganKaryawan()->exists()) {
                    throw new \Exception("Jenis tunjangan '{$tunjanganType->name}' masih memiliki data terkait!");
                }
            }

            TunjanganType::whereIn('tunjangan_type_id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' jenis tunjangan berhasil dihapus!'
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
            'ids.*' => 'exists:tunjangan_types,tunjangan_type_id',
            'status' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            TunjanganType::whereIn('tunjangan_type_id', $request->ids)
                ->update(['is_active' => $request->status]);

            DB::commit();

            $action = $request->status ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . " jenis tunjangan berhasil {$action}!"
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan active tunjangan types
    public function getActiveTunjanganTypes()
    {
        $tunjanganTypes = TunjanganType::active()
            ->select('tunjangan_type_id', 'name', 'code', 'category', 'base_amount')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tunjanganTypes
        ]);
    }

    // Export functionality (similar to other controllers)
    public function export()
    {
        try {
            $tunjanganTypes = TunjanganType::with('tunjanganDetails')->get();

            $filename = 'tunjangan_types_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($tunjanganTypes) {
                $file = fopen('php://output', 'w');

                // Header CSV
                fputcsv($file, [
                    'ID', 'Nama', 'Kode', 'Kategori', 'Nominal Dasar',
                    'Deskripsi', 'Status', 'Tanggal Dibuat'
                ]);

                // Data rows
                foreach ($tunjanganTypes as $type) {
                    fputcsv($file, [
                        $type->tunjangan_type_id,
                        $type->name,
                        $type->code,
                        ucfirst($type->category),
                        number_format($type->base_amount, 0, ',', '.'),
                        $type->description,
                        $type->is_active ? 'Aktif' : 'Nonaktif',
                        $type->created_at->format('d-m-Y H:i:s')
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
