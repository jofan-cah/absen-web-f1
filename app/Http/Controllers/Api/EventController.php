<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class EventController extends BaseApiController
{
    // ─── List Event Aktif ────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user     = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $events = Event::with('department')
            ->active()
            ->where(function ($q) use ($karyawan) {
                $q->whereNull('department_id')
                  ->orWhere('department_id', $karyawan->department_id);
            })
            ->where('start_date', '<=', today())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', today());
            })
            ->orderBy('start_date')
            ->get()
            ->map(fn($e) => $this->formatEvent($e));

        return $this->successResponse($events, 'Daftar event aktif');
    }

    // ─── Detail Event ────────────────────────────────────────────────────────────

    public function show(Request $request, string $id)
    {
        $user     = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $event = Event::with('department')->findOrFail($id);

        $myAttendances = EventAttendance::where('event_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->orderByDesc('check_in_at')
            ->get();

        return $this->successResponse([
            'event'         => $this->formatEvent($event),
            'my_attendances' => $myAttendances,
            'already_attended' => $myAttendances->isNotEmpty(),
        ], 'Detail event');
    }

    // ─── Scan QR dari Layar (Mobile) ────────────────────────────────────────────

    public function scanEventQr(Request $request)
    {
        $user     = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $request->validate([
            'event_id'    => 'required|string',
            'otp'         => 'required|string',
            'ts'          => 'required|integer',
            'jumlah_orang' => 'nullable|integer|min:1',
            'keterangan'  => 'nullable|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ]);

        // 1. Cari event
        $event = Event::find($request->event_id);
        if (!$event) {
            return $this->notFoundResponse('Event tidak ditemukan');
        }

        // 2. Cek status
        if (!in_array($event->status, ['active', 'ongoing'])) {
            return $this->errorResponse('Event tidak aktif', 422);
        }

        // 3. Cek tanggal
        $today = today();
        if ($today->lt($event->start_date)) {
            return $this->errorResponse('Event belum dimulai', 422);
        }
        if ($event->end_date && $today->gt($event->end_date)) {
            return $this->errorResponse('Event sudah selesai', 422);
        }

        // 4. Validasi OTP (expired check termasuk di dalamnya)
        if (!$event->validateOtp($request->otp, (int) $request->ts)) {
            $age = abs(time() - (int) $request->ts);
            if ($age > $event->qr_refresh_seconds * 2) {
                return $this->errorResponse('QR sudah expired, silakan scan ulang', 422);
            }
            return $this->errorResponse('QR tidak valid', 422);
        }

        // 5. Cek double scan
        if (!$event->allow_multi_scan) {
            $exists = EventAttendance::where('event_id', $event->event_id)
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->exists();

            if ($exists) {
                return $this->errorResponse('Anda sudah tercatat hadir di event ini', 422);
            }
        }

        // 6. Validasi GPS (opsional)
        if ($event->hasGpsValidation() && $request->filled('latitude') && $request->filled('longitude')) {
            $distance = $event->distanceTo((float) $request->latitude, (float) $request->longitude);
            if ($distance > $event->radius) {
                return $this->errorResponse(
                    'Anda tidak berada di lokasi event (jarak: ' . round($distance) . 'm, max: ' . $event->radius . 'm)',
                    422
                );
            }
        }

        // 7. Simpan kehadiran
        $attendance = EventAttendance::create([
            'event_id'    => $event->event_id,
            'karyawan_id' => $karyawan->karyawan_id,
            'method'      => 'qr_scan',
            'jumlah_orang' => $event->type === 'partnership' ? ($request->jumlah_orang ?? 1) : 1,
            'keterangan'  => $request->keterangan,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return $this->createdResponse([
            'attendance_id' => $attendance->attendance_id,
            'event_title'   => $event->title,
            'check_in_at'   => $attendance->check_in_at->format('Y-m-d H:i:s'),
            'jumlah_orang'  => $attendance->jumlah_orang,
            'ticket_token'  => $attendance->ticket_token,
        ], 'Berhasil absen di event ' . $event->title);
    }

    // ─── Riwayat Event Saya ──────────────────────────────────────────────────────

    public function history(Request $request)
    {
        $user     = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $attendances = EventAttendance::with('event')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->orderByDesc('check_in_at')
            ->paginate(20);

        return $this->paginatedResponse($attendances, 'Riwayat event saya');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────────

    private function formatEvent(Event $event): array
    {
        return [
            'event_id'           => $event->event_id,
            'title'              => $event->title,
            'description'        => $event->description,
            'type'               => $event->type,
            'location'           => $event->location,
            'start_date'         => $event->start_date?->format('Y-m-d'),
            'end_date'           => $event->end_date?->format('Y-m-d'),
            'start_time'         => $event->start_time,
            'end_time'           => $event->end_time,
            'status'             => $event->status,
            'qr_refresh_seconds' => $event->qr_refresh_seconds,
            'department'         => $event->department?->name,
            'has_gps_validation' => $event->hasGpsValidation(),
        ];
    }
}
