<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Event::with(['department', 'creator'])
            ->withCount('attendances');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.event.indexEvent', compact('events'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────────

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.event.createEvent', compact('departments'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'type'                => 'required|in:internal,partnership',
            'location'            => 'nullable|string|max:255',
            'start_date'          => 'required|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'start_time'          => 'nullable|date_format:H:i',
            'end_time'            => 'nullable|date_format:H:i',
            'qr_refresh_seconds'  => 'integer|min:10|max:300',
            'max_participants'    => 'nullable|integer|min:1',
            'allow_multi_scan'    => 'boolean',
            'latitude'            => 'nullable|numeric',
            'longitude'           => 'nullable|numeric',
            'radius'              => 'nullable|integer|min:10',
            'department_id'       => 'nullable|exists:departments,department_id',
        ]);

        $validated['created_by']         = auth()->id();
        $validated['allow_multi_scan']    = $request->boolean('allow_multi_scan');
        $validated['qr_refresh_seconds']  = $request->input('qr_refresh_seconds', 30);
        $validated['radius']              = $request->input('radius', 100);

        Event::create($validated);

        return redirect()->route('admin.event.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    // ─── Show ────────────────────────────────────────────────────────────────────

    public function show(string $id)
    {
        $event = Event::with(['department', 'creator'])->findOrFail($id);

        $attendances = EventAttendance::with(['karyawan.department', 'verifiedBy'])
            ->where('event_id', $id)
            ->orderByDesc('check_in_at')
            ->get();

        $totalOrang = $attendances->sum('jumlah_orang');

        return view('admin.event.showEvent', compact('event', 'attendances', 'totalOrang'));
    }

    // ─── Edit ────────────────────────────────────────────────────────────────────

    public function edit(string $id)
    {
        $event       = Event::findOrFail($id);
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.event.editEvent', compact('event', 'departments'));
    }

    // ─── Update ──────────────────────────────────────────────────────────────────

    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'type'                => 'required|in:internal,partnership',
            'location'            => 'nullable|string|max:255',
            'start_date'          => 'required|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'start_time'          => 'nullable|date_format:H:i',
            'end_time'            => 'nullable|date_format:H:i',
            'qr_refresh_seconds'  => 'integer|min:10|max:300',
            'max_participants'    => 'nullable|integer|min:1',
            'allow_multi_scan'    => 'boolean',
            'latitude'            => 'nullable|numeric',
            'longitude'           => 'nullable|numeric',
            'radius'              => 'nullable|integer|min:10',
            'department_id'       => 'nullable|exists:departments,department_id',
        ]);

        $validated['allow_multi_scan']   = $request->boolean('allow_multi_scan');
        $validated['qr_refresh_seconds'] = $request->input('qr_refresh_seconds', 30);
        $validated['radius']             = $request->input('radius', 100);

        $event->update($validated);

        return redirect()->route('admin.event.show', $id)
            ->with('success', 'Event berhasil diperbarui.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────────

    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.event.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    // ─── Update Status ───────────────────────────────────────────────────────────

    public function updateStatus(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'status' => 'required|in:draft,active,ongoing,completed,cancelled',
        ]);

        $event->update(['status' => $request->status]);

        return back()->with('success', 'Status event berhasil diubah menjadi ' . $request->status . '.');
    }

    // ─── QR Display ──────────────────────────────────────────────────────────────

    public function showQr(string $id)
    {
        $event = Event::findOrFail($id);

        if (!in_array($event->status, ['active', 'ongoing'])) {
            return redirect()->route('admin.event.show', $id)
                ->with('error', 'QR hanya tersedia untuk event berstatus active atau ongoing.');
        }

        return view('admin.event.qr-display', compact('event'));
    }

    // ─── Generate QR OTP (AJAX) ──────────────────────────────────────────────────

    public function generateQrOtp(string $id)
    {
        $event = Event::findOrFail($id);

        if (!in_array($event->status, ['active', 'ongoing'])) {
            return response()->json(['error' => 'Event tidak aktif'], 403);
        }

        ['otp' => $otp, 'ts' => $ts] = $event->generateOtp();

        $totalAttendees = EventAttendance::where('event_id', $id)->count();
        $totalOrang     = EventAttendance::where('event_id', $id)->sum('jumlah_orang');

        return response()->json([
            'event_id'        => $event->event_id,
            'otp'             => $otp,
            'ts'              => $ts,
            'expires_in'      => $event->qr_refresh_seconds,
            'total_attendees' => $totalAttendees,
            'total_orang'     => $totalOrang,
        ]);
    }

    // ─── Scan Page (Webcam) ──────────────────────────────────────────────────────

    public function scanPage(string $id)
    {
        $event = Event::findOrFail($id);

        if (!in_array($event->status, ['active', 'ongoing'])) {
            return redirect()->route('admin.event.show', $id)
                ->with('error', 'Scanner hanya tersedia untuk event berstatus active atau ongoing.');
        }

        $recentAttendances = EventAttendance::with(['karyawan.department'])
            ->where('event_id', $id)
            ->orderByDesc('check_in_at')
            ->limit(20)
            ->get();

        return view('admin.event.scan', compact('event', 'recentAttendances'));
    }

    // ─── Process Scan (NIP from webcam) ─────────────────────────────────────────

    public function processScan(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        if (!in_array($event->status, ['active', 'ongoing'])) {
            return response()->json(['success' => false, 'message' => 'Event tidak aktif'], 422);
        }

        $request->validate([
            'nip'         => 'required|string',
            'jumlah_orang' => 'nullable|integer|min:1',
            'keterangan'  => 'nullable|string',
        ]);

        // Cari karyawan by NIP
        $karyawan = Karyawan::where('nip', $request->nip)
            ->where('employment_status', 'active')
            ->first();

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Karyawan dengan NIP ' . $request->nip . ' tidak ditemukan'], 404);
        }

        // Cek double scan
        if (!$event->allow_multi_scan) {
            $exists = EventAttendance::where('event_id', $id)
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => $karyawan->full_name . ' sudah tercatat hadir di event ini',
                ], 422);
            }
        }

        $attendance = EventAttendance::create([
            'event_id'    => $id,
            'karyawan_id' => $karyawan->karyawan_id,
            'method'      => 'qr_scan',
            'jumlah_orang' => $event->type === 'partnership' ? ($request->jumlah_orang ?? 1) : 1,
            'keterangan'  => $request->keterangan,
            'verified_by' => auth()->id(),
        ]);

        return response()->json([
            'success'     => true,
            'message'     => $karyawan->full_name . ' berhasil dicatat hadir',
            'karyawan'    => [
                'full_name'  => $karyawan->full_name,
                'nip'        => $karyawan->nip,
                'department' => $karyawan->department?->name,
            ],
            'check_in_at'  => $attendance->check_in_at->format('H:i:s'),
            'jumlah_orang' => $attendance->jumlah_orang,
        ]);
    }

    // ─── Manual Attendance Page ───────────────────────────────────────────────────

    public function manualPage(string $id)
    {
        $event = Event::findOrFail($id);

        if (!in_array($event->status, ['active', 'ongoing'])) {
            return redirect()->route('admin.event.show', $id)
                ->with('error', 'Manual attendance hanya tersedia untuk event berstatus active atau ongoing.');
        }

        $karyawans = Karyawan::with('department')
            ->where('employment_status', 'active')
            ->when($event->department_id, fn($q) => $q->where('department_id', $event->department_id))
            ->orderBy('full_name')
            ->get();

        // Exclude yang sudah hadir (jika tidak allow multi scan)
        if (!$event->allow_multi_scan) {
            $alreadyIn = EventAttendance::where('event_id', $id)->pluck('karyawan_id')->toArray();
            $karyawans = $karyawans->whereNotIn('karyawan_id', $alreadyIn)->values();
        }

        return view('admin.event.manual', compact('event', 'karyawans'));
    }

    // ─── Process Manual ──────────────────────────────────────────────────────────

    public function processManual(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'karyawan_id'  => 'required|exists:karyawans,karyawan_id',
            'jumlah_orang' => 'nullable|integer|min:1',
            'keterangan'   => 'nullable|string',
        ]);

        if (!$event->allow_multi_scan) {
            $exists = EventAttendance::where('event_id', $id)
                ->where('karyawan_id', $request->karyawan_id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Karyawan sudah tercatat hadir di event ini.');
            }
        }

        EventAttendance::create([
            'event_id'    => $id,
            'karyawan_id' => $request->karyawan_id,
            'method'      => 'manual',
            'jumlah_orang' => $event->type === 'partnership' ? ($request->jumlah_orang ?? 1) : 1,
            'keterangan'  => $request->keterangan,
            'verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Kehadiran berhasil dicatat.');
    }

    // ─── Remove Attendance ────────────────────────────────────────────────────────

    public function removeAttendance(string $id, string $att)
    {
        $attendance = EventAttendance::where('event_id', $id)
            ->where('attendance_id', $att)
            ->firstOrFail();

        $attendance->delete();

        return back()->with('success', 'Data kehadiran berhasil dihapus.');
    }

    // ─── PDF ─────────────────────────────────────────────────────────────────────

    public function previewPdf(string $id)
    {
        $event       = Event::with(['department', 'creator'])->findOrFail($id);
        $attendances = EventAttendance::with(['karyawan.department', 'verifiedBy'])
            ->where('event_id', $id)
            ->orderByDesc('check_in_at')
            ->get();
        $totalOrang = $attendances->sum('jumlah_orang');

        $pdf = Pdf::loadView('admin.event.pdf', compact('event', 'attendances', 'totalOrang'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('event-' . $id . '.pdf');
    }

    public function downloadPdf(string $id)
    {
        $event       = Event::with(['department', 'creator'])->findOrFail($id);
        $attendances = EventAttendance::with(['karyawan.department', 'verifiedBy'])
            ->where('event_id', $id)
            ->orderByDesc('check_in_at')
            ->get();
        $totalOrang = $attendances->sum('jumlah_orang');

        $pdf = Pdf::loadView('admin.event.pdf', compact('event', 'attendances', 'totalOrang'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('event-' . $id . '.pdf');
    }
}
