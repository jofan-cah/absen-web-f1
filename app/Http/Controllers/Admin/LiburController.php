<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Libur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiburController extends Controller
{
    public function index(Request $request)
    {
        $query = Libur::query();

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        // Sorting
        $sortField = $request->get('sort', 'date');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSortFields = ['date', 'name', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $liburs = $query->paginate(15)->withQueryString();

        return view('admin.libur.indexLibur', compact('liburs'));
    }

    public function create()
    {
        return view('admin.libur.createLibur');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:liburs,date',
            'type' => 'nullable|in:nasional,cuti_bersama,perusahaan',
            'description' => 'nullable|string',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            DB::beginTransaction();

            Libur::create([
                'libur_id' => Libur::generateLiburId(),
                'name' => $request->name,
                'date' => $request->date,
                'type' => $request->type,
                'description' => $request->description,
                'color' => $request->color ?? '#FFD700',
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.libur.index')
                ->with('success', 'Hari libur berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambahkan hari libur: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Libur $libur)
    {
        return view('admin.libur.editLibur', compact('libur'));
    }

    public function update(Request $request, Libur $libur)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:liburs,date,' . $libur->libur_id . ',libur_id',
            'type' => 'nullable|in:nasional,cuti_bersama,perusahaan',
            'description' => 'nullable|string',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $libur->update([
                'name' => $request->name,
                'date' => $request->date,
                'type' => $request->type,
                'description' => $request->description,
                'color' => $request->color ?? '#FFD700',
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();

            return redirect()->route('admin.libur.index')
                ->with('success', 'Hari libur berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengupdate hari libur: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Libur $libur)
    {
        try {
            DB::beginTransaction();

            $libur->delete();

            DB::commit();

            return redirect()->route('admin.libur.index')
                ->with('success', 'Hari libur berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus hari libur: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(Libur $libur)
    {
        try {
            $libur->update([
                'is_active' => !$libur->is_active
            ]);

            $status = $libur->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->route('admin.libur.index')
                ->with('success', 'Hari libur berhasil ' . $status);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_liburs' => 'required|array',
            'selected_liburs.*' => 'exists:liburs,libur_id'
        ]);

        try {
            DB::beginTransaction();

            Libur::whereIn('libur_id', $request->selected_liburs)->delete();

            DB::commit();

            return redirect()->route('admin.libur.index')
                ->with('success', count($request->selected_liburs) . ' hari libur berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus hari libur: ' . $e->getMessage()]);
        }
    }

    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'selected_liburs' => 'required|array',
            'selected_liburs.*' => 'exists:liburs,libur_id',
            'status' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            Libur::whereIn('libur_id', $request->selected_liburs)
                ->update(['is_active' => $request->status]);

            DB::commit();

            $statusText = $request->status ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->route('admin.libur.index')
                ->with('success', count($request->selected_liburs) . ' hari libur berhasil ' . $statusText);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }
}
