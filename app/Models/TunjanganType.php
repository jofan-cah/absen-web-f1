<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TunjanganType extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_types';
    protected $primaryKey = 'tunjangan_type_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tunjangan_type_id',
        'name',
        'code',
        'category', // 'harian', 'mingguan', 'bulanan'
        'base_amount', // nominal dasar
        'description',
        'is_active',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tunjanganDetails()
    {
        return $this->hasMany(TunjanganDetail::class, 'tunjangan_type_id', 'tunjangan_type_id');
    }

    public function tunjanganKaryawan()
    {
        return $this->hasMany(TunjanganKaryawan::class, 'tunjangan_type_id', 'tunjangan_type_id');
    }

    // Helper method
    public static function generateTunjanganTypeId()
    {
        $lastType = self::orderByDesc('tunjangan_type_id')->first();
        if (!$lastType) {
            return 'TJT001';
        }

        $lastNumber = (int) substr($lastType->tunjangan_type_id, 3);
        $newNumber = $lastNumber + 1;

        return 'TJT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Scope untuk filter berdasarkan kategori
    public function scopeHarian($query)
    {
        return $query->where('category', 'harian');
    }

    public function scopeMingguan($query)
    {
        return $query->where('category', 'mingguan');
    }

    public function scopeBulanan($query)
    {
        return $query->where('category', 'bulanan');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
