<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TunjanganDetail extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_details';
    protected $primaryKey = 'tunjangan_detail_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tunjangan_detail_id',
        'tunjangan_type_id',
        'staff_status', // 'pkwtt', 'karyawan', 'koordinator', 'wakil_koordinator'
        'amount', // nominal sesuai status
        'effective_date', // tanggal mulai berlaku
        'end_date', // tanggal berakhir (nullable)
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tunjanganType()
    {
        return $this->belongsTo(TunjanganType::class, 'tunjangan_type_id', 'tunjangan_type_id');
    }

    // Helper method
    public static function generateTunjanganDetailId()
    {
        $lastDetail = self::orderByDesc('tunjangan_detail_id')->first();
        if (!$lastDetail) {
            return 'TJD001';
        }

        $lastNumber = (int) substr($lastDetail->tunjangan_detail_id, 3);
        $newNumber = $lastNumber + 1;

        return 'TJD' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Scope untuk mendapatkan nominal yang aktif berdasarkan staff_status
    public function scopeForStaffStatus($query, $staffStatus)
    {
        return $query->where('staff_status', $staffStatus)
                    ->where('is_active', true)
                    ->where('effective_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }
     // Scope untuk mendapatkan yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Method untuk mendapatkan nominal tunjangan berdasarkan staff status
    public static function getAmountByStaffStatus($tunjanganTypeId, $staffStatus)
    {
        return self::where('tunjangan_type_id', $tunjanganTypeId)
                  ->forStaffStatus($staffStatus)
                  ->orderByDesc('effective_date')
                  ->value('amount') ?? 0;
    }
}
