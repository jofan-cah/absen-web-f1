<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
        'koordinator_status',
        'koordinator_approved_at',
        'koordinator_notes',
        'koordinator_rejected_at',
        'submitted_at',
        'submitted_via',
        'started_at',
        'completed_at',
        'approved_by_user_id',
        'approved_at',
        'approval_notes',
        'rejected_by_user_id',
        'rejected_at',
        'rejection_reason',
        'tunjangan_karyawan_id',
        'coordinator_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'tanggal_lembur' => 'datetime:Y-m-d',
        'total_jam' => 'decimal:2',
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'koordinator_approved_at' => 'datetime',
        'koordinator_rejected_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function absen()
    {
        return $this->belongsTo(Absen::class, 'absen_id', 'absen_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(Karyawan::class, 'coordinator_id', 'karyawan_id');
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

    // ============================================
    // BOOT & ID GENERATION
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->lembur_id)) {
                $model->lembur_id = self::generateLemburId();
            }
        });

        static::saving(function ($model) {
            // Auto calculate total_jam jika jam_mulai dan jam_selesai ada
            if ($model->jam_mulai && $model->jam_selesai) {
                $mulai = Carbon::createFromFormat('H:i:s', $model->jam_mulai);
                $selesai = Carbon::createFromFormat('H:i:s', $model->jam_selesai);

                // Handle jika jam selesai melewati tengah malam
                if ($selesai->lessThan($mulai)) {
                    $selesai->addDay();
                }

                $model->total_jam = $mulai->diffInMinutes($selesai) / 60;
            }
        });
    }

    public static function generateLemburId()
    {
        $prefix = 'LMB';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $date . $random;
    }

    // ============================================
    // SCOPES
    // ============================================

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

    public function scopePendingKoordinator($query)
    {
        return $query->where('status', 'submitted')
            ->where('koordinator_status', 'pending');
    }

    public function scopePendingAdmin($query)
    {
        return $query->where('status', 'submitted')
            ->where('koordinator_status', 'approved');
    }

    // ============================================
    // STATUS CHECKERS
    // ============================================

    public function canEdit()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canSubmit()
    {
        return $this->status === 'draft'
            && $this->jam_selesai
            && $this->deskripsi_pekerjaan
            && $this->bukti_foto;
    }

    public function hasClockOut()
    {
        return $this->absen && $this->absen->clock_out;
    }

    public function isApprovedByKoordinator()
    {
        return $this->koordinator_status === 'approved';
    }

    public function isRejectedByKoordinator()
    {
        return $this->koordinator_status === 'rejected';
    }

    public function isPendingKoordinator()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'pending';
    }

    public function isPendingAdmin()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'approved';
    }

    public function canApproveByKoordinator()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'pending';
    }

    public function canApproveByAdmin()
    {
        return $this->status === 'submitted' && $this->koordinator_status === 'approved';
    }

    // ============================================
    // SUBMIT
    // ============================================

    public function submit($via = 'web')
    {
        if (!$this->canSubmit()) {
            throw new \Exception('Lembur tidak dapat disubmit. Pastikan semua data sudah lengkap.');
        }

        $this->update([
            'status' => 'submitted',
            'koordinator_status' => 'pending',
            'submitted_at' => now(),
            'submitted_via' => $via,
        ]);

        return true;
    }

    // ============================================
    // APPROVAL - KOORDINATOR (LEVEL 1)
    // ============================================

    /**
     * Approve by Koordinator (Level 1)
     *
     * @param string $userId - User ID dari Auth::user()->user_id
     * @param string|null $notes
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

        // ✅ FIX: Ambil karyawan_id dari user_id
        $koordinator = \App\Models\Karyawan::where('user_id', $userId)->first();

        if (!$koordinator) {
            throw new \Exception('Data koordinator tidak ditemukan');
        }

        $this->update([
            'koordinator_status' => 'approved',
            'koordinator_approved_at' => now(),
            'koordinator_notes' => $notes,
            'coordinator_id' => $koordinator->karyawan_id, // ✅ PAKAI karyawan_id
        ]);

        return true;
    }

    /**
     * Reject by Koordinator (Level 1)
     *
     * @param string $userId - User ID dari Auth::user()->user_id
     * @param string $reason
     */
    public function rejectByKoordinator($userId, $reason)
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        if ($this->koordinator_status !== 'pending') {
            throw new \Exception('Koordinator sudah melakukan review sebelumnya');
        }

        // ✅ FIX: Ambil karyawan_id dari user_id
        $koordinator = \App\Models\Karyawan::where('user_id', $userId)->first();

        $this->update([
            'status' => 'rejected',
            'koordinator_status' => 'rejected',
            'koordinator_rejected_at' => now(),
            'koordinator_notes' => $reason,
            'rejected_by_user_id' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'coordinator_id' => $koordinator ? $koordinator->karyawan_id : null, // ✅ PAKAI karyawan_id
        ]);

        return true;
    }

    // ============================================
    // APPROVAL - ADMIN (LEVEL 2)
    // ============================================

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

        // Generate tunjangan lembur otomatis
        $this->generateTunjangan();

        return true;
    }

    public function approveByAdminDirect($userId, $notes = null)
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        // VALIDASI: Karyawan harus sudah clock out
        if (!$this->hasClockOut()) {
            throw new \Exception('Karyawan belum melakukan clock out. Lembur tidak dapat diapprove.');
        }

        $this->update([
            // Update koordinator status sekalian (auto-approved by admin)
            'koordinator_status' => 'approved',
            'koordinator_approved_at' => now(),
            'koordinator_notes' => 'Auto-approved by admin (bypass)',

            // Update status final
            'status' => 'approved',
            'approved_by_user_id' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);

        // Generate tunjangan lembur otomatis
        $this->generateTunjangan();

        return true;
    }

    public function rejectByAdmin($userId, $reason)
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        // Admin bisa reject walau koordinator sudah approve
        $this->update([
            'status' => 'rejected',
            'rejected_by_user_id' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    // ============================================
    // GENERATE TUNJANGAN
    // ============================================

    public function generateTunjangan()
    {
        if ($this->tunjangan_karyawan_id) {
            Log::info("Tunjangan sudah pernah dibuat untuk Lembur ID: {$this->lembur_id}");
            return false;
        }

        // Get tunjangan type UANG_LEMBUR
        $tunjanganType = $this->getLemburTunjanganType();

        if (!$tunjanganType) {
            throw new \Exception('Tunjangan Type UANG_LEMBUR tidak ditemukan');
        }

        // Calculate tunjangan
        $quantity = $this->calculateQuantity();
        $amountPerUnit = $this->calculateAmountPerUnit();
        $totalAmount = $this->calculateTunjanganAmount();

        // Log untuk debug
        Log::info("Generate Tunjangan Lembur", [
            'lembur_id' => $this->lembur_id,
            'karyawan_id' => $this->karyawan_id,
            'staff_status' => $this->karyawan->staff_status ?? 'unknown',
            'total_jam' => $this->total_jam,
            'quantity' => $quantity,
            'amount_per_unit' => $amountPerUnit,
            'total_amount' => $totalAmount,
        ]);

        // Create tunjangan karyawan
        $tunjangan = TunjanganKaryawan::create([
            'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
            'karyawan_id' => $this->karyawan_id,
            'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
            'absen_id' => $this->absen_id,
            'lembur_id' => $this->lembur_id,
            'period_start' => $this->tanggal_lembur,
            'period_end' => $this->tanggal_lembur,
            'amount' => $amountPerUnit,
            'quantity' => $quantity,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'notes' => "Tunjangan lembur {$this->tanggal_lembur->format('d/m/Y')} - {$this->total_jam} jam ({$quantity}x uang makan)",
            'hari_kerja_final' => 1, // Sesuaikan dengan logic
        ]);

        // Update lembur dengan tunjangan_karyawan_id
        $this->update([
            'tunjangan_karyawan_id' => $tunjangan->tunjangan_karyawan_id,
        ]);

        Log::info("Tunjangan berhasil dibuat", [
            'lembur_id' => $this->lembur_id,
            'tunjangan_id' => $tunjangan->tunjangan_karyawan_id,
            'amount' => $amountPerUnit,
            'quantity' => $quantity,
            'total_amount' => $totalAmount,
        ]);

        return $tunjangan;
    }

    // ============================================
    // TUNJANGAN CALCULATION
    // ============================================

    /**
     * Get tunjangan type untuk UANG_LEMBUR
     */
    public function getLemburTunjanganType()
    {
        return TunjanganType::where('code', 'UANG_LEMBUR')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get tunjangan detail berdasarkan staff_status karyawan
     */
    public function getLemburTunjanganDetail()
    {
        $tunjanganType = $this->getLemburTunjanganType();

        if (!$tunjanganType) {
            Log::warning("Tunjangan Type UANG_LEMBUR tidak ditemukan");
            return null;
        }

        // Ambil staff_status dari karyawan
        $staffStatus = $this->karyawan->staff_status ?? 'karyawan';

        // Cari tunjangan detail berdasarkan staff_status
        $tunjanganDetail = TunjanganDetail::where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
            ->where('staff_status', $staffStatus)
            ->where('is_active', true)
            ->where(function ($query) {
                // Cek effective_date dan end_date
                $query->where('effective_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    });
            })
            ->first();

        return $tunjanganDetail;
    }

    /**
     * Calculate quantity tunjangan berdasarkan total jam
     * < 4 jam = 1x uang makan
     * >= 4 jam = 2x uang makan
     */
    public function calculateQuantity()
    {
        return $this->total_jam >= 4 ? 2 : 1;
    }

    /**
     * Calculate amount per unit dari database tunjangan_detail
     */
    public function calculateAmountPerUnit()
    {
        $tunjanganDetail = $this->getLemburTunjanganDetail();

        if ($tunjanganDetail && $tunjanganDetail->amount) {
            return (int) $tunjanganDetail->amount;
        }

        // Fallback ke default jika tidak ada di database
        $staffStatus = $this->karyawan->staff_status ?? 'karyawan';

        Log::warning("Tunjangan Detail UANG_LEMBUR untuk staff_status {$staffStatus} tidak ditemukan, menggunakan default");

        // Default fallback
        return in_array($staffStatus, ['karyawan', 'koordinator', 'wakil_koordinator'])
            ? 20000
            : 15000;
    }

    /**
     * Calculate total tunjangan amount
     * Total = Quantity × Amount Per Unit
     */
    public function calculateTunjanganAmount()
    {
        return $this->calculateQuantity() * $this->calculateAmountPerUnit();
    }

    /**
     * Get description tunjangan
     */
    public function getTunjanganDescription()
    {
        $quantity = $this->calculateQuantity();

        if ($this->total_jam >= 4) {
            return "Lembur ≥ 4 jam mendapat {$quantity}x uang makan";
        } else {
            return "Lembur < 4 jam mendapat {$quantity}x uang makan";
        }
    }

    /**
     * Get tunjangan breakdown lengkap dengan info dari database
     */
    public function getTunjanganBreakdown()
    {
        $tunjanganType = $this->getLemburTunjanganType();
        $tunjanganDetail = $this->getLemburTunjanganDetail();

        $staffStatus = $this->karyawan->staff_status ?? 'karyawan';
        $quantity = $this->calculateQuantity();
        $amountPerUnit = $this->calculateAmountPerUnit();
        $totalAmount = $this->calculateTunjanganAmount();

        return [
            // Info Lembur
            'total_jam' => $this->total_jam,
            'quantity' => $quantity,
            'amount_per_unit' => $amountPerUnit,
            'total_amount' => $totalAmount,
            'description' => $this->getTunjanganDescription(),
            'calculation' => "{$quantity} × Rp " . number_format($amountPerUnit, 0, ',', '.') . " = Rp " . number_format($totalAmount, 0, ',', '.'),

            // Info Karyawan
            'staff_status' => $staffStatus,
            'karyawan_name' => $this->karyawan->full_name ?? '-',

            // Info dari Database
            'tunjangan_type' => $tunjanganType ? [
                'id' => $tunjanganType->tunjangan_type_id,
                'name' => $tunjanganType->name,
                'code' => $tunjanganType->code,
            ] : null,

            'tunjangan_detail' => $tunjanganDetail ? [
                'id' => $tunjanganDetail->tunjangan_detail_id,
                'staff_status' => $tunjanganDetail->staff_status,
                'amount' => $tunjanganDetail->amount,
                'effective_date' => $tunjanganDetail->effective_date,
                'end_date' => $tunjanganDetail->end_date,
                'is_active' => $tunjanganDetail->is_active,
            ] : null,

            // Source info
            'source' => $tunjanganDetail ? 'database' : 'fallback_default',
        ];
    }

    /**
     * Check apakah tunjangan sudah dibuat
     */
    public function hasTunjangan()
    {
        return !is_null($this->tunjangan_karyawan_id) && !is_null($this->tunjanganKaryawan);
    }

    /**
     * Get tunjangan status
     */
    public function getTunjanganStatus()
    {
        if ($this->hasTunjangan()) {
            return $this->tunjanganKaryawan->status ?? 'unknown';
        }

        return null;
    }

    public function getBuktiFotoUrlAttribute()
    {
        if ($this->bukti_foto) {
            return Storage::disk('s3')->temporaryUrl(
                $this->bukti_foto,
                now()->addMinutes(60)
            );
        }
        return null;
    }
}
