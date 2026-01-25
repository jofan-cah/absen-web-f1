<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShiftSwapRequest extends Model
{
    use HasFactory, LogsActivity;

    protected static $logName = 'ShiftSwapRequest';

    protected $primaryKey = 'swap_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'swap_id',
        'requester_karyawan_id',
        'requester_jadwal_id',
        'partner_karyawan_id',
        'partner_jadwal_id',
        'reason',
        'status',
        'partner_response_at',
        'partner_notes',
        'approved_by_admin_id',
        'admin_approved_at',
        'admin_notes',
        'completed_at',
    ];

    protected $casts = [
        'partner_response_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING_PARTNER = 'pending_partner';
    const STATUS_APPROVED_BY_PARTNER = 'approved_by_partner';
    const STATUS_PENDING_ADMIN_APPROVAL = 'pending_admin_approval';
    const STATUS_REJECTED_BY_PARTNER = 'rejected_by_partner';
    const STATUS_REJECTED_BY_ADMIN = 'rejected_by_admin';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function requesterKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'requester_karyawan_id', 'karyawan_id');
    }

    public function partnerKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'partner_karyawan_id', 'karyawan_id');
    }

    public function requesterJadwal()
    {
        return $this->belongsTo(Jadwal::class, 'requester_jadwal_id', 'jadwal_id');
    }

    public function partnerJadwal()
    {
        return $this->belongsTo(Jadwal::class, 'partner_jadwal_id', 'jadwal_id');
    }

    public function approvedByAdmin()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by_admin_id', 'user_id');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Generate unique swap_id
     */
    public static function generateSwapId()
    {
        $lastSwap = self::orderByDesc('swap_id')->first();

        if (!$lastSwap) {
            return 'SWP001';
        }

        $lastNumber = (int) substr($lastSwap->swap_id, 3);
        $newNumber = $lastNumber + 1;

        return 'SWP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING_PARTNER;
    }

    /**
     * Check if request is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED_BY_PARTNER;
    }

    /**
     * Check if request is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED_BY_PARTNER;
    }

    /**
     * Check if request is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if swap is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->isPending();
    }

    /**
     * Check if request can be responded to
     */
    public function canBeRespondedTo()
    {
        return $this->isPending();
    }

    /**
     * Approve by partner (perlu admin approval selanjutnya)
     */
    public function approveByPartner($partnerNotes = null)
    {
        if (!$this->canBeRespondedTo()) {
            throw new \Exception('Request tidak dapat diproses');
        }

        $this->update([
            'status' => self::STATUS_PENDING_ADMIN_APPROVAL,
            'partner_response_at' => now(),
            'partner_notes' => $partnerNotes ?? 'Disetujui oleh partner'
        ]);

        return true;
    }

    /**
     * Approve by admin and process swap
     */
    public function approveByAdminAndSwap($adminId, $adminNotes = null)
    {
        if ($this->status !== self::STATUS_PENDING_ADMIN_APPROVAL) {
            throw new \Exception('Request tidak dalam status pending admin approval');
        }

        DB::beginTransaction();

        try {
            // Get jadwals
            $jadwalA = $this->requesterJadwal;
            $jadwalB = $this->partnerJadwal;

            // Validate absensi
            $absenA = $jadwalA->absen;
            $absenB = $jadwalB->absen;

            if (($absenA && ($absenA->clock_in || $absenA->clock_out)) ||
                ($absenB && ($absenB->clock_in || $absenB->clock_out))) {
                throw new \Exception('Tidak bisa tukar shift, ada yang sudah melakukan absensi');
            }

            // Get shift names for notes
            $shiftAName = $jadwalA->shift->name;
            $shiftBName = $jadwalB->shift->name;

            // 🔥 SWAP SHIFT_ID (bukan karyawan_id!)
            $tempShiftId = $jadwalA->shift_id;

            // Update jadwal A: dapat shift dari B
            $jadwalA->update([
                'shift_id' => $jadwalB->shift_id,
                'swap_id' => $this->swap_id,
                'notes' => ($jadwalA->notes ? $jadwalA->notes . ' | ' : '') . "Shift ditukar: {$shiftAName} → {$shiftBName}"
            ]);

            // Update jadwal B: dapat shift dari A
            $jadwalB->update([
                'shift_id' => $tempShiftId,
                'swap_id' => $this->swap_id,
                'notes' => ($jadwalB->notes ? $jadwalB->notes . ' | ' : '') . "Shift ditukar: {$shiftBName} → {$shiftAName}"
            ]);

            // Update notes di absens (karyawan tetap sama, cuma shift berubah)
            if ($absenA) {
                $absenA->update([
                    'notes' => ($absenA->notes ? $absenA->notes . ' | ' : '') . "Shift ditukar: {$shiftAName} → {$shiftBName}"
                ]);
            }

            if ($absenB) {
                $absenB->update([
                    'notes' => ($absenB->notes ? $absenB->notes . ' | ' : '') . "Shift ditukar: {$shiftBName} → {$shiftAName}"
                ]);
            }

            // Update swap request status
            $this->update([
                'status' => self::STATUS_COMPLETED,
                'approved_by_admin_id' => $adminId,
                'admin_approved_at' => now(),
                'admin_notes' => $adminNotes ?? 'Disetujui oleh admin',
                'completed_at' => now()
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject by admin
     */
    public function rejectByAdmin($adminId, $adminNotes = null)
    {
        if ($this->status !== self::STATUS_PENDING_ADMIN_APPROVAL) {
            throw new \Exception('Request tidak dalam status pending admin approval');
        }

        $this->update([
            'status' => self::STATUS_REJECTED_BY_ADMIN,
            'approved_by_admin_id' => $adminId,
            'admin_approved_at' => now(),
            'admin_notes' => $adminNotes ?? 'Ditolak oleh admin'
        ]);

        return true;
    }

    /**
     * Reject swap request
     */
    public function reject($partnerNotes = null)
    {
        if (!$this->canBeRespondedTo()) {
            throw new \Exception('Request tidak dapat diproses');
        }

        $this->update([
            'status' => self::STATUS_REJECTED_BY_PARTNER,
            'partner_response_at' => now(),
            'partner_notes' => $partnerNotes ?? 'Ditolak oleh partner'
        ]);

        return true;
    }

    /**
     * Cancel swap request
     */
    public function cancel()
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('Request tidak dapat dibatalkan');
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'partner_notes' => 'Dibatalkan oleh requester'
        ]);

        return true;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_PARTNER);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED_BY_PARTNER);
    }

    public function scopeForKaryawan($query, $karyawanId)
    {
        return $query->where(function($q) use ($karyawanId) {
            $q->where('requester_karyawan_id', $karyawanId)
              ->orWhere('partner_karyawan_id', $karyawanId);
        });
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopePendingAdminApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_ADMIN_APPROVAL);
    }
}
