<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IjinType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IjinTypeController extends Controller
{
    /**
     * Display list of ijin types
     */
    public function index(Request $request)
    {
        $query = IjinType::withCount('ijins');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $ijinTypes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.ijin-type.indexIjinType', compact('ijinTypes'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.ijin-type.createIjinType');
    }

    /**
     * Store new ijin type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:ijin_types,code',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            IjinType::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.ijin-type.index')
                ->with('success', 'Tipe ijin berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambahkan tipe ijin: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show detail ijin type
     */
    public function show(IjinType $ijinType)
    {
        $ijinType->loadCount('ijins');

        return view('admin.ijin-type.showIjinType', compact('ijinType'));
    }

    /**
     * Show edit form
     */
    public function edit(IjinType $ijinType)
    {
        return view('admin.ijin-type.editIjinType', compact('ijinType'));
    }

    /**
     * Update ijin type
     */
    public function update(Request $request, IjinType $ijinType)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:ijin_types,code,' . $ijinType->ijin_type_id . ',ijin_type_id',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Validasi: tidak boleh edit code untuk tipe default
            $defaultCodes = ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave'];
            if (in_array($ijinType->code, $defaultCodes) && $ijinType->code !== $request->code) {
                return back()->withErrors(['code' => 'Tidak dapat mengubah code untuk tipe ijin default'])
                    ->withInput();
            }

            $ijinType->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
            ]);

            DB::commit();

            return redirect()->route('admin.ijin-type.index')
                ->with('success', 'Tipe ijin berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengupdate tipe ijin: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete ijin type
     */
    public function destroy(IjinType $ijinType)
    {
        try {
            DB::beginTransaction();

            // Validasi: tidak boleh hapus tipe default
            $defaultCodes = ['sick', 'annual', 'personal', 'shift_swap', 'compensation_leave'];
            if (in_array($ijinType->code, $defaultCodes)) {
                return back()->withErrors(['delete' => 'Tidak dapat menghapus tipe ijin default']);
            }

            // Cek apakah sudah ada ijin yang menggunakan
            if ($ijinType->ijins()->count() > 0) {
                return back()->withErrors(['delete' => 'Tidak dapat menghapus tipe ijin yang sudah digunakan']);
            }

            $ijinType->delete();

            DB::commit();

            return redirect()->route('admin.ijin-type.index')
                ->with('success', 'Tipe ijin berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus tipe ijin: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(IjinType $ijinType)
    {
        try {
            DB::beginTransaction();

            $ijinType->update([
                'is_active' => !$ijinType->is_active
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Status tipe ijin berhasil diubah');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }
}
