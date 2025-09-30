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
        do {
            // Format: JDW + 12 karakter random alphanumeric = 15 karakter total
            // Contoh: JDWF9E2D1C8B7A6

            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $jadwalId = 'JDW' . $randomString;

            // Pastikan unique dengan cek database
            $exists = self::where('jadwal_id', $jadwalId)->exists();
        } while ($exists);

        return $jadwalId;
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
