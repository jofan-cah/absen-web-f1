<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TunjanganKaryawan extends Model
{
    use HasFactory, LogsActivity;

    protected static $logName = 'TunjanganKaryawan';

    protected $table = 'tunjangan_karyawan';
    protected $primaryKey = 'tunjangan_karyawan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tunjangan_karyawan_id',
        'karyawan_id',
        'tunjangan_type_id',
        'absen_id',
        'period_start',
        'lembur_id',
        'period_end',
        'amount',
        'quantity',
        'total_amount',
        'status',
        'notes',
        'requested_at',
        'requested_via',
        'approved_by_user_id',
        'approved_at',
        'received_at',
        'received_confirmation_photo',
        'penalti_id',
        'hari_kerja_asli',
        'hari_potong_penalti',
        'hari_kerja_final',
        'history',
        'delay_days',              // ✅ TAMBAH
        'available_request_date',  // ✅ TAMBAH
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:2',
        'quantity' => 'integer',
        'total_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'hari_kerja_asli' => 'integer',
        'hari_potong_penalti' => 'integer',
        'hari_kerja_final' => 'integer',
        'history' => 'array',
        'delay_days' => 'integer',           // ✅ TAMBAH
        'available_request_date' => 'date',  // ✅ TAMBAH
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function tunjanganType()
    {
        return $this->belongsTo(TunjanganType::class, 'tunjangan_type_id', 'tunjangan_type_id');
    }

    public function absen()
    {
        return $this->belongsTo(Absen::class, 'absen_id', 'absen_id');
    }

    public function lembur()
    {
        return $this->belongsTo(Lembur::class, 'lembur_id', 'lembur_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'user_id');
    }

    public function penalti()
    {
        return $this->belongsTo(Penalti::class, 'penalti_id', 'penalti_id');
    }

    // ============================================
    // BOOT & ID GENERATION
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tunjangan_karyawan_id)) {
                $model->tunjangan_karyawan_id = self::generateTunjanganKaryawanId();
            }
        });

        static::saving(function ($tunjangan) {
            // Hitung hari kerja final (setelah dipotong penalti)
            $hariKerjaFinal = max(0, ($tunjangan->hari_kerja_asli ?? $tunjangan->quantity ?? 1) - ($tunjangan->hari_potong_penalti ?? 0));
            $tunjangan->hari_kerja_final = $hariKerjaFinal;

            // Total amount = amount * hari kerja final
            $tunjangan->total_amount = $tunjangan->amount * $hariKerjaFinal;
        });

        static::created(function ($tunjangan) {
            $tunjangan->addHistory('pending', 'Tunjangan dibuat otomatis oleh sistem');
        });
    }

    /**
     * Generate ID: TJK + 7 digit random (total 10 karakter)
     * Format: TJK1234567
     */
    public static function generateTunjanganKaryawanId()
    {
        $prefix = 'TJK';
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            // Generate 7 digit random number
            $randomNumber = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
            $id = $prefix . $randomNumber;

            // Check if exists
            if (!self::where('tunjangan_karyawan_id', $id)->exists()) {
                return $id;
            }
        }

        // Fallback: timestamp + random (jika 10 attempts gagal - sangat jarang)
        $timestamp = now()->format('His'); // 6 digit
        $random = str_pad(random_int(0, 9), 1, '0', STR_PAD_LEFT); // 1 digit
        return $prefix . $timestamp . $random;
    }

    // ============================================
    // WORKFLOW METHODS
    // ============================================

    public function requestTunjangan($via = 'mobile', $userId = null)
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // ✅ CEK DELAY - Apakah sudah bisa request?
        if (!$this->canRequest()) {
            throw new \Exception('Tunjangan belum bisa di-request. Masih dalam periode delay.');
        }

        $this->addHistory('requested', "Request tunjangan via {$via}", $userId);

        $this->update([
            'status' => 'requested',
            'requested_at' => now(),
            'requested_via' => $via,
        ]);

        return true;
    }

    public function approveTunjangan($userId, $notes = null)
    {
        if ($this->status !== 'requested') {
            return false;
        }

        $this->addHistory('approved', $notes ?? 'Tunjangan disetujui', $userId);

        $this->update([
            'status' => 'approved',
            'approved_by_user_id' => $userId,
            'approved_at' => now(),
            'notes' => $notes ? $this->notes . ' | ' . $notes : $this->notes,
        ]);

        return true;
    }

    public function confirmReceived($confirmationPhoto = null, $userId = null)
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $this->addHistory('received', 'Tunjangan telah diterima karyawan', $userId);

        $this->update([
            'status' => 'received',
            'received_at' => now(),
            'received_confirmation_photo' => $confirmationPhoto,
        ]);

        return true;
    }

    // ============================================
    // DELAY & REQUEST CHECK METHODS
    // ============================================

    /**
     * Check apakah sudah bisa request (dengan delay check)
     */
    public function canRequest()
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if ($this->quantity <= 0) {
            return false;
        }

        // ✅ CEK DELAY - Apakah sudah melewati available_request_date?
        if ($this->available_request_date) {
            return now()->greaterThanOrEqualTo($this->available_request_date);
        }

        return true;
    }

    /**
     * Get sisa hari delay
     */
    public function getRemainingDelayDays()
    {
        if (!$this->available_request_date) {
            return 0;
        }

        if (now()->greaterThanOrEqualTo($this->available_request_date)) {
            return 0;
        }

        $remaining = now()->diffInDays($this->available_request_date, false);

        return max(0, ceil($remaining));
    }

    /**
     * Check apakah masih dalam delay period
     */
    public function isDelayed()
    {
        return $this->getRemainingDelayDays() > 0;
    }

    public function canApprove()
    {
        return $this->status === 'requested';
    }

    // ============================================
    // HISTORY METHODS
    // ============================================

    private function addHistory($status, $notes, $userId = null)
    {
        $histories = $this->history ?? [];

        $histories[] = [
            'status' => $status,
            'notes' => $notes,
            'user_id' => $userId,
            'timestamp' => now()->toISOString(),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        $this->history = $histories;
        $this->saveQuietly();
    }

    public function getFormattedHistory()
    {
        if (!$this->history) {
            return [];
        }

        return collect($this->history)->map(function ($item) {
            $user = isset($item['user_id']) ? User::find($item['user_id']) : null;

            return [
                'status' => $item['status'],
                'notes' => $item['notes'],
                'user_name' => $user ? $user->name : 'System',
                'timestamp' => $item['created_at'] ?? $item['timestamp'],
                'formatted_date' => date('d-m-Y H:i', strtotime($item['created_at'] ?? $item['timestamp'])),
            ];
        })->toArray();
    }

    // ============================================
    // PENALTI METHODS
    // ============================================

    public function applyPenalti($penaltiId, $hariPotong)
    {
        $this->update([
            'penalti_id' => $penaltiId,
            'hari_potong_penalti' => ($this->hari_potong_penalti ?? 0) + $hariPotong,
        ]);

        $this->addHistory('penalty_applied', "Penalti diterapkan: {$hariPotong} hari potong uang makan");

        return $this;
    }

    // ============================================
    // GENERATE TUNJANGAN METHODS
    // ============================================

    public static function generateTunjanganMakan($karyawanId, $startDate, $endDate)
    {
        $karyawan = Karyawan::find($karyawanId);
        $tunjanganType = TunjanganType::where('code', 'UANG_MAKAN')->where('is_active', true)->first();

        if (!$karyawan || !$tunjanganType) {
            return false;
        }

        $amount = TunjanganDetail::getAmountByStaffStatus(
            $tunjanganType->tunjangan_type_id,
            $karyawan->staff_status
        );

        // Hitung hari kerja asli
        $hariKerjaAsli = self::countWorkDays($startDate, $endDate, $karyawanId);

        // Hitung total hari potong penalti dalam periode ini
        $hariPotongPenalti = Penalti::getTotalHariPotongan($karyawanId, $startDate, $endDate);

        return self::create([
            'tunjangan_karyawan_id' => self::generateTunjanganKaryawanId(),
            'karyawan_id' => $karyawanId,
            'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'amount' => $amount,
            'quantity' => $hariKerjaAsli,
            'hari_kerja_asli' => $hariKerjaAsli,
            'hari_potong_penalti' => $hariPotongPenalti,
            'status' => 'pending',
        ]);
    }

    public static function generateTunjanganKuota($karyawanId, $month, $year)
    {
        $karyawan = Karyawan::find($karyawanId);
        $tunjanganType = TunjanganType::where('code', 'UANG_KUOTA')->where('is_active', true)->first();

        if (!$karyawan || !$tunjanganType) {
            return false;
        }

        $amount = TunjanganDetail::getAmountByStaffStatus(
            $tunjanganType->tunjangan_type_id,
            $karyawan->staff_status
        );

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return self::create([
            'tunjangan_karyawan_id' => self::generateTunjanganKaryawanId(),
            'karyawan_id' => $karyawanId,
            'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'amount' => $amount,
            'quantity' => 1,
            'status' => 'pending',
        ]);
    }

    public static function generateTunjanganLembur($absenId, $lemburHours)
    {
        $absen = Absen::with('karyawan')->find($absenId);
        $tunjanganType = TunjanganType::where('code', 'UANG_LEMBUR')->where('is_active', true)->first();

        if (!$absen || !$tunjanganType || $lemburHours <= 0) {
            return false;
        }

        $amount = TunjanganDetail::getAmountByStaffStatus(
            $tunjanganType->tunjangan_type_id,
            $absen->karyawan->staff_status
        );

        return self::create([
            'tunjangan_karyawan_id' => self::generateTunjanganKaryawanId(),
            'karyawan_id' => $absen->karyawan_id,
            'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
            'absen_id' => $absenId,
            'period_start' => $absen->date,
            'period_end' => $absen->date,
            'amount' => $amount,
            'quantity' => $lemburHours,
            'status' => 'pending',
            'notes' => "Lembur {$lemburHours} jam pada " . $absen->date->format('d-m-Y'),
        ]);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    private static function countWorkDays($startDate, $endDate, $karyawanId)
    {
        return Absen::where('karyawan_id', $karyawanId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('clock_in')
            ->count();
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRequested($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate])
            ->orWhereBetween('period_end', [$startDate, $endDate]);
    }

    /**
     * Scope: Tunjangan yang bisa di-request (sudah lewat delay)
     */
    public function scopeCanRequest($query)
    {
        return $query->where('status', 'pending')
            ->where('quantity', '>', 0)
            ->where(function($q) {
                $q->whereNull('available_request_date')
                  ->orWhere('available_request_date', '<=', now());
            });
    }

    /**
     * Scope: Tunjangan yang masih delay
     */
    public function scopeDelayed($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('available_request_date')
            ->where('available_request_date', '>', now());
    }
}
