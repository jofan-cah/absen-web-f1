<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalti extends Model
{
    use HasFactory;

    protected $primaryKey = 'penalti_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'penalti_id',
        'karyawan_id',
        'absen_id', // nullable, jika penalti dari absen tertentu
        'jenis_penalti', // 'telat', 'tidak_masuk', 'pelanggaran', 'custom'
        'deskripsi',
        'hari_potong_uang_makan', // berapa hari uang makan dipotong
        'tanggal_penalti',
        'periode_berlaku_mulai', // periode potongan mulai
        'periode_berlaku_akhir', // periode potongan berakhir
        'status', // 'active', 'completed', 'cancelled'
        'created_by_user_id',
        'approved_by_user_id',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'hari_potong_uang_makan' => 'integer',
        'tanggal_penalti' => 'date',
        'periode_berlaku_mulai' => 'date',
        'periode_berlaku_akhir' => 'date',
        'approved_at' => 'datetime',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'user_id');
    }

    public function tunjanganKaryawan()
    {
        return $this->hasMany(TunjanganKaryawan::class, 'penalti_id', 'penalti_id');
    }

    // Helper method
    public static function generatePenaltiId()
    {
        $lastPenalti = self::orderByDesc('penalti_id')->first();
        if (!$lastPenalti) {
            return 'PNL001';
        }

        $lastNumber = (int) substr($lastPenalti->penalti_id, 3);
        $newNumber = $lastNumber + 1;

        return 'PNL' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Method untuk approve penalti
    public function approvePenalti($userId, $notes = null)
    {
        if ($this->status !== 'active') {
            return false;
        }

        $this->update([
            'approved_by_user_id' => $userId,
            'approved_at' => now(),
            'notes' => $notes ? $this->notes . ' | ' . $notes : $this->notes,
        ]);

        return true;
    }

    // Check apakah penalti masih berlaku pada tanggal tertentu
    public function isActiveOnDate($date)
    {
        $checkDate = is_string($date) ? date('Y-m-d', strtotime($date)) : $date->format('Y-m-d');

        return $this->status === 'active' &&
               $checkDate >= $this->periode_berlaku_mulai->format('Y-m-d') &&
               $checkDate <= $this->periode_berlaku_akhir->format('Y-m-d');
    }

    // Get total hari potongan untuk periode tertentu
    public static function getTotalHariPotongan($karyawanId, $periodeAwal, $periodeAkhir)
    {
        return self::where('karyawan_id', $karyawanId)
                  ->where('status', 'active')
                  ->where(function($query) use ($periodeAwal, $periodeAkhir) {
                      $query->whereBetween('periode_berlaku_mulai', [$periodeAwal, $periodeAkhir])
                            ->orWhereBetween('periode_berlaku_akhir', [$periodeAwal, $periodeAkhir])
                            ->orWhere(function($q) use ($periodeAwal, $periodeAkhir) {
                                $q->where('periode_berlaku_mulai', '<=', $periodeAwal)
                                  ->where('periode_berlaku_akhir', '>=', $periodeAkhir);
                            });
                  })
                  ->sum('hari_potong_uang_makan');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('periode_berlaku_mulai', [$startDate, $endDate])
              ->orWhereBetween('periode_berlaku_akhir', [$startDate, $endDate])
              ->orWhere(function($subQ) use ($startDate, $endDate) {
                  $subQ->where('periode_berlaku_mulai', '<=', $startDate)
                       ->where('periode_berlaku_akhir', '>=', $endDate);
              });
        });
    }
}
