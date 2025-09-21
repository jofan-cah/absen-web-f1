<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
public function index(Request $request) {
    $query = Shift::withCount('jadwals');
    // Add search and filter logic
    $shifts = $query->orderBy('created_at', 'desc')->paginate(15);
    return view('admin.shift.indexShift', compact('shifts'));
}

    public function create()
    {
        return view('admin.shift.createShift');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shifts',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'break_duration' => 'required|integer|min:0',
            'late_tolerance' => 'required|integer|min:0',
            'early_checkout_tolerance' => 'required|integer|min:0',
            'is_overnight' => 'boolean',
        ]);

        // Validasi break time
        if ($request->break_start && $request->break_end) {
            if ($request->break_start >= $request->break_end) {
                return back()->withErrors([
                    'break_end' => 'Jam akhir istirahat harus lebih besar dari jam mulai istirahat'
                ])->withInput();
            }
        }

        Shift::create([
            'shift_id' => Shift::generateShiftId(),
            'name' => $request->name,
            'code' => $request->code,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'break_duration' => $request->break_duration,
            'late_tolerance' => $request->late_tolerance,
            'early_checkout_tolerance' => $request->early_checkout_tolerance,
            'is_overnight' => $request->boolean('is_overnight'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.shift.index')
            ->with('success', 'Shift berhasil ditambahkan');
    }

    public function show(Shift $shift)
    {
        $shift->loadCount(['jadwals as total_jadwal']);
        return view('admin.shift.showShift', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        return view('admin.shift.editShift', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shifts,code,' . $shift->shift_id . ',shift_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'break_duration' => 'required|integer|min:0',
            'late_tolerance' => 'required|integer|min:0',
            'early_checkout_tolerance' => 'required|integer|min:0',
            'is_overnight' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Validasi break time
        if ($request->break_start && $request->break_end) {
            if ($request->break_start >= $request->break_end) {
                return back()->withErrors([
                    'break_end' => 'Jam akhir istirahat harus lebih besar dari jam mulai istirahat'
                ])->withInput();
            }
        }

        $shift->update([
            'name' => $request->name,
            'code' => $request->code,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'break_duration' => $request->break_duration,
            'late_tolerance' => $request->late_tolerance,
            'early_checkout_tolerance' => $request->early_checkout_tolerance,
            'is_overnight' => $request->boolean('is_overnight'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.shift.index')
            ->with('success', 'Shift berhasil diupdate');
    }

    public function destroy(Shift $shift)
    {
        // Check if shift is being used
        $jadwalCount = $shift->jadwals()->count();

        if ($jadwalCount > 0) {
            return back()->withErrors([
                'delete' => 'Shift tidak bisa dihapus karena masih digunakan dalam ' . $jadwalCount . ' jadwal'
            ]);
        }

        $shift->delete();

        return redirect()->route('admin.shift.index')
            ->with('success', 'Shift berhasil dihapus');
    }

    // AJAX method untuk get active shifts (untuk jadwal form)
    public function getActiveShifts()
    {
        $shifts = Shift::where('is_active', true)
            ->select('shift_id', 'name', 'code', 'start_time', 'end_time')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shifts
        ]);
    }

    // Toggle active status
    public function toggleStatus(Shift $shift)
    {
        $shift->update([
            'is_active' => !$shift->is_active
        ]);

        $status = $shift->is_active ? 'aktif' : 'non-aktif';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Shift berhasil diubah menjadi {$status}",
                'is_active' => $shift->is_active
            ]);
        }

        return back()->with('success', "Shift berhasil diubah menjadi {$status}");
    }
}
