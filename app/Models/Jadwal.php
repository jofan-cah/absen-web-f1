<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $primaryKey = 'jadwal_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'jadwal_id',
        'karyawan_id',
        'shift_id',
        'date',
        'is_active',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'shift_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'user_id');
    }

    public function absen()
    {
        return $this->hasOne(Absen::class, 'jadwal_id', 'jadwal_id');
    }

    // Helper method
    public static function generateJadwalId()
    {
        $lastJadwal = self::orderByDesc('jadwal_id')->first();
        if (!$lastJadwal) {
            return 'JDW001';
        }

        $lastNumber = (int) substr($lastJadwal->jadwal_id, 3);
        $newNumber = $lastNumber + 1;

        return 'JDW' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Auto create absen saat jadwal dibuat
    protected static function boot()
    {
        parent::boot();

        static::created(function ($jadwal) {
            Absen::create([
                'absen_id' => Absen::generateAbsenId(),
                'karyawan_id' => $jadwal->karyawan_id,
                'jadwal_id' => $jadwal->jadwal_id,
                'date' => $jadwal->date,
                'status' => 'scheduled'
            ]);
        });
    }
}
