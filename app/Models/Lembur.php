<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'absen_id', // optional, jika lembur dari absen tertentu
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'total_jam', // dihitung otomatis
        'kategori_lembur', // 'reguler', 'hari_libur', 'hari_besar'
        'multiplier', // 1.5x untuk reguler, 2x untuk libur, dst
        'deskripsi_pekerjaan',
        'bukti_foto', // foto bukti lembur
        'status', // 'draft', 'submitted', 'approved', 'rejected', 'processed'
        'submitted_at',
        'submitted_via', // 'mobile', 'web'
        'approved_by_user_id',
        'approved_at',
        'approval_notes',
        'rejected_by_user_id',
        'rejected_at',
        'rejection_reason',
        'tunjangan_karyawan_id', // reference ke tunjangan yang sudah dibuat
        'created_by_user_id',
    ];

    protected $casts = [
        'tanggal_lembur' => 'datetime:Y-m-d',
        'total_jam' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

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

            // Set multiplier berdasarkan kategori jika belum ada
            if (!$lembur->multiplier) {
                $lembur->multiplier = match($lembur->kategori_lembur) {
                    'hari_libur' => 2.0,
                    'hari_besar' => 2.5,
                    default => 1.5,
                };
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

    public function approve($userId, $notes = null)
    {
        if ($this->status !== 'submitted') {
            return false;
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

    // Generate tunjangan dari lembur yang diapprove
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

    $amountPerJam = TunjanganDetail::getAmountByStaffStatus(
        $tunjanganType->tunjangan_type_id,
        $karyawan->staff_status
    );

    // Hitung dengan multiplier
    $finalAmount = $amountPerJam * $this->multiplier;
    $totalHours = $this->total_jam;

    $tunjangan = TunjanganKaryawan::create([
        'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
        'karyawan_id' => $this->karyawan_id,
        'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
        'absen_id' => $this->absen_id,   // ← TETAP ISI (tracking absen)
        'lembur_id' => $this->lembur_id, // ← TAMBAH INI (relasi lembur)
        'period_start' => $this->tanggal_lembur,
        'period_end' => $this->tanggal_lembur,
        'amount' => $finalAmount,
        'quantity' => $totalHours,
        'status' => 'pending',
        'notes' => "Lembur {$this->kategori_lembur} - {$totalHours} jam (multiplier {$this->multiplier}x) pada " .
                  $this->tanggal_lembur->format('d-m-Y'),
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

