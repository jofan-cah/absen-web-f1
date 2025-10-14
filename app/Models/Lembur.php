<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lemburs';
    protected $primaryKey = 'lembur_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'lembur_id',
        'karyawan_id',
        'absen_id',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'deskripsi_pekerjaan',
        'bukti_foto',
        'status',
        'submitted_at',
        'submitted_via',
        'approved_by_user_id',
        'approved_at',
        'approval_notes',
        'rejected_by_user_id',
        'rejected_at',
        'rejection_reason',
        'tunjangan_karyawan_id',
        'created_by_user_id',
        'coordinator_id',
        'koordinator_status',
        'koordinator_approved_at',
        'koordinator_notes',
        'koordinator_rejected_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'tanggal_lembur' => 'datetime:Y-m-d',
        'total_jam' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'koordinator_approved_at' => 'datetime',
        'koordinator_rejected_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',

    ];



    public function getBuktiFotoUrlAttribute()
    {
        if ($this->bukti_foto) {
            // Jika file-nya di S3 PRIVATE, buat URL sementara 1 jam
            return Storage::disk('s3')->temporaryUrl(
                $this->bukti_foto,
                now()->addMinutes(60)
            );

            // ATAU jika bucket kamu PUBLIC, cukup:
            // return Storage::disk('s3')->url($this->bukti_foto);
        }

        return null;
    }

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function absen()
    {
        return $this->belongsTo(Absen::class, 'absen_id', 'absen_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'user_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id', 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'user_id');
    }

    public function tunjanganKaryawan()
    {
        return $this->belongsTo(TunjanganKaryawan::class, 'tunjangan_karyawan_id', 'tunjangan_karyawan_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(Karyawan::class, 'coordinator_id', 'karyawan_id');
    }

    // Helper method
    public static function generateLemburId()
    {
        $lastLembur = self::orderByDesc('lembur_id')->first();
        if (!$lastLembur) {
            return 'LBR001';
        }

        $lastNumber = (int) substr($lastLembur->lembur_id, 3);
        $newNumber = $lastNumber + 1;

        return 'LBR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Auto calculate total jam
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($lembur) {
            if ($lembur->jam_mulai && $lembur->jam_selesai) {
                $mulai = Carbon::parse($lembur->jam_mulai);
                $selesai = Carbon::parse($lembur->jam_selesai);

                // Handle overnight overtime
                if ($selesai->lt($mulai)) {
                    $selesai->addDay();
                }

                $lembur->total_jam = $selesai->diffInHours($mulai, true);
            }
        });
    }

    // Workflow methods
    public function submit($via = 'mobile')
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_via' => $via,
        ]);

        return true;
    }

    /**
     * Approve oleh KOORDINATOR (Level 1)
     * TIDAK generate tunjangan, hanya update koordinator_status
     */
    public function approveByKoordinator($userId, $notes = null)
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        if ($this->koordinator_status !== 'pending') {
            throw new \Exception('Koordinator sudah melakukan review sebelumnya');
        }

        // VALIDASI: Karyawan harus sudah clock out
        if (!$this->hasClockOut()) {
            throw new \Exception('Karyawan belum melakukan clock out. Lembur tidak dapat diapprove.');
        }

        $this->update([
            'koordinator_status' => 'approved',
            'koordinator_approved_at' => now(),
            'koordinator_notes' => $notes,
            'coordinator_id' => $userId, // Simpan user_id koordinator (bukan karyawan_id)
        ]);

        return true;
    }

    /**
     * Approve oleh ADMIN (Level 2 - FINAL)
     * Generate tunjangan setelah admin approve
     */
    public function approveByAdmin($userId, $notes = null)
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        // VALIDASI: Koordinator harus sudah approve dulu
        if ($this->koordinator_status !== 'approved') {
            throw new \Exception('Lembur belum diapprove oleh Koordinator');
        }

        // VALIDASI: Karyawan harus sudah clock out
        if (!$this->hasClockOut()) {
            throw new \Exception('Karyawan belum melakukan clock out. Lembur tidak dapat diapprove.');
        }

        $this->update([
            'status' => 'approved',
            'approved_by_user_id' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        // 🎯 Generate tunjangan lembur otomatis (HANYA di admin approve)
        $this->generateTunjangan();

        return true;
    }

    /**
     * Cek apakah koordinator sudah approve
     */
    public function isApprovedByKoordinator()
    {
        return $this->koordinator_status === 'approved';
    }

    /**
     * Cek apakah koordinator sudah reject
     */
    public function isRejectedByKoordinator()
    {
        return $this->koordinator_status === 'rejected';
    }

    /**
     * Cek apakah menunggu approval koordinator
     */
    public function isPendingKoordinator()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'pending';
    }

    /**
     * Cek apakah menunggu approval admin
     */
    public function isPendingAdmin()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'approved';
    }

    /**
     * Cek apakah bisa di-approve oleh koordinator
     */
    public function canApproveByKoordinator()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'pending';
    }

    /**
     * Cek apakah bisa di-approve oleh admin
     */
    public function canApproveByAdmin()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'approved';
    }

    public function reject($userId, $reason)
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejected_by_user_id' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    // Cek apakah sudah clock out
    public function hasClockOut()
    {
        if (!$this->absen) {
            return false;
        }

        return !is_null($this->absen->clock_out);
    }

    // Validasi waktu pengajuan lembur
    public static function canSubmitLembur($absenId)
    {
        $absen = Absen::with('jadwal.shift')->find($absenId);

        if (!$absen || !$absen->jadwal) {
            return ['can_submit' => false, 'message' => 'Data absen tidak ditemukan'];
        }

        $shift = $absen->jadwal->shift;
        $shiftEndTime = Carbon::parse($absen->jadwal->date->format('Y-m-d') . ' ' . $shift->end_time);

        // Handle overnight shift
        if ($shift->is_overnight) {
            $shiftEndTime->addDay();
        }

        $maxSubmitTime = $shiftEndTime->copy()->addHour(); // Max 1 jam setelah shift
        $now = Carbon::now();

        if ($now->greaterThan($maxSubmitTime)) {
            return [
                'can_submit' => false,
                'message' => 'Pengajuan lembur hanya dapat dilakukan maksimal 1 jam setelah shift berakhir'
            ];
        }

        return ['can_submit' => true];
    }

    // Generate tunjangan berdasarkan jam lembur
    // 0-3.99 jam = 1x uang makan
    // 4-7.99 jam = 2x uang makan
    // ≥ 8 jam = 2x uang makan (maksimal)
    public function generateTunjangan()
    {
        if ($this->status !== 'approved' || $this->tunjangan_karyawan_id) {
            return false;
        }

        $karyawan = $this->karyawan;
        $tunjanganType = TunjanganType::where('code', 'UANG_LEMBUR')->active()->first();

        if (!$tunjanganType) {
            return false;
        }

        // Ambil amount per satuan dari TunjanganDetail berdasarkan staff_status
        // 20k untuk karyawan/koordinator/wakil_koordinator
        // 15k untuk pkwtt
        $amountPerUnit = TunjanganDetail::getAmountByStaffStatus(
            $tunjanganType->tunjangan_type_id,
            $karyawan->staff_status
        );

        // HITUNG QUANTITY BERDASARKAN TOTAL JAM
        $totalJam = $this->total_jam;

        if ($totalJam >= 4) {
            $quantity = 2; // 2x uang makan (4 jam ke atas)
        } else {
            $quantity = 1; // 1x uang makan (0-3.99 jam)
        }

        $totalAmount = $amountPerUnit * $quantity;

        $tunjangan = TunjanganKaryawan::create([
            'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
            'karyawan_id' => $this->karyawan_id,
            'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
            'absen_id' => $this->absen_id,
            'lembur_id' => $this->lembur_id,
            'period_start' => $this->tanggal_lembur,
            'period_end' => $this->tanggal_lembur,
            'amount' => $amountPerUnit, // 20k atau 15k per unit
            'quantity' => $quantity, // 1x atau 2x
            'total_amount' => $totalAmount, // 20k/15k atau 40k/30k
            'status' => 'pending',
            'notes' => "Tunjangan lembur - {$this->total_jam} jam pada " .
                $this->tanggal_lembur->format('d-m-Y') .
                " ({$quantity}x uang makan = Rp " . number_format($totalAmount, 0, ',', '.') . ")",
        ]);

        $this->update([
            'tunjangan_karyawan_id' => $tunjangan->tunjangan_karyawan_id,
            'status' => 'processed',
        ]);

        return $tunjangan;
    }

    // Check permissions
    public function canSubmit()
    {
        return $this->status === 'draft';
    }

    public function canApprove()
    {
        return $this->status === 'submitted';
    }

    public function canEdit()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeForKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_lembur', $date);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_lembur', [$startDate, $endDate]);
    }

    // Get total jam lembur dalam periode
    public static function getTotalJamLembur($karyawanId, $startDate, $endDate)
    {
        return self::where('karyawan_id', $karyawanId)
            ->approved()
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->sum('total_jam');
    }
}
