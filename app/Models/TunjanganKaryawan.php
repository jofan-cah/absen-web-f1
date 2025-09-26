<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TunjanganKaryawan extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_karyawan';
    protected $primaryKey = 'tunjangan_karyawan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tunjangan_karyawan_id',
        'karyawan_id',
        'tunjangan_type_id',
        'absen_id', // untuk tunjangan lembur (nullable)
        'period_start', // periode mulai
           'lembur_id', // untuk tunjangan lembur (nullable)
        'period_end', // periode akhir
        'amount', // nominal yang diberikan
        'quantity', // jumlah (untuk lembur bisa > 1)
        'total_amount', // amount * quantity
        'status', // 'pending', 'requested', 'approved', 'received'
        'notes',
        'requested_at',
        'requested_via', // 'mobile', 'web'
        'approved_by_user_id',
        'approved_at',
        'received_at',
        'received_confirmation_photo',
        'penalti_id', // jika ada penalti yang mempengaruhi
        'hari_kerja_asli', // jumlah hari kerja sebenarnya
        'hari_potong_penalti', // hari yang dipotong karena penalti
        'hari_kerja_final', // hari kerja - hari potong penalti
        'history', // JSON untuk tracking history
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
    ];

    // Relationships
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'user_id');
    }

    public function penalti()
    {
        return $this->belongsTo(Penalti::class, 'penalti_id', 'penalti_id');
    }

    // Helper method
    public static function generateTunjanganKaryawanId()
    {
        $lastTunjangan = self::orderByDesc('tunjangan_karyawan_id')->first();
        if (!$lastTunjangan) {
            return 'TJK001';
        }

        $lastNumber = (int) substr($lastTunjangan->tunjangan_karyawan_id, 3);
        $newNumber = $lastNumber + 1;

        return 'TJK' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Method untuk workflow pengambilan uang dengan history tracking
    public function requestTunjangan($via = 'mobile', $userId = null)
    {
        if ($this->status !== 'pending') {
            return false;
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

    // Method untuk menambah history
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
        $this->saveQuietly(); // Save tanpa trigger event
    }

    // Get history dengan format readable
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

    // Check if can request
    public function canRequest()
    {
        return $this->status === 'pending';
    }

    // Check if can approve
    public function canApprove()
    {
        return $this->status === 'requested';
    }

    // Auto calculate total_amount dengan penalti
    protected static function boot()
    {
        parent::boot();

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

    // Method untuk generate tunjangan otomatis dengan penalti
    public static function generateTunjanganMakan($karyawanId, $startDate, $endDate)
    {
        $karyawan = Karyawan::find($karyawanId);
        $tunjanganType = TunjanganType::where('code', 'UANG_MAKAN')->active()->first();

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
            'quantity' => $hariKerjaAsli, // untuk backward compatibility
            'hari_kerja_asli' => $hariKerjaAsli,
            'hari_potong_penalti' => $hariPotongPenalti,
            'status' => 'pending',
        ]);
    }

    // Method untuk apply penalti ke tunjangan yang sudah ada
    public function applyPenalti($penaltiId, $hariPotong)
    {
        $this->update([
            'penalti_id' => $penaltiId,
            'hari_potong_penalti' => ($this->hari_potong_penalti ?? 0) + $hariPotong,
        ]);

        $this->addHistory('penalty_applied', "Penalti diterapkan: {$hariPotong} hari potong uang makan");

        return $this;
    }

    public static function generateTunjanganKuota($karyawanId, $month, $year)
    {
        $karyawan = Karyawan::find($karyawanId);
        $tunjanganType = TunjanganType::where('code', 'UANG_KUOTA')->active()->first();

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
        $tunjanganType = TunjanganType::where('code', 'UANG_LEMBUR')->active()->first();

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

    // Helper method untuk menghitung hari kerja
    private static function countWorkDays($startDate, $endDate, $karyawanId)
    {
        return Absen::where('karyawan_id', $karyawanId)
                   ->whereBetween('date', [$startDate, $endDate])
                   ->whereNotNull('clock_in')
                   ->count();
    }

    // Scopes untuk workflow
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
}
