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
        'ijin_id',      // ✅ CORE: Jadwal bisa punya ijin
        'date',
        'is_active',
         'swap_id',
          'type',
        'status',       // ✅ Status jadwal
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'is_active' => 'boolean',
    ];

    // ✅ STATUS CONSTANTS
    const STATUS_NORMAL = 'normal';
    const STATUS_HAS_IJIN = 'has_ijin';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ONCALL = 'oncall'; // ← TAMBAH INI

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

    // ✅ RELASI KE ABSEN (1:1)
    public function absen()
    {
        return $this->hasOne(Absen::class, 'jadwal_id', 'jadwal_id');
    }

    // ✅ RELASI KE IJIN (0:1)
    public function ijin()
    {
        return $this->belongsTo(Ijin::class, 'ijin_id', 'ijin_id');
    }

    // ✅ HELPER METHODS
    public function hasIjin()
    {
        return !is_null($this->ijin_id);
    }

     // ✅ RELASI KE SHIFT SWAP (0:1) - TAMBAHAN
    public function shiftSwap()
    {
        return $this->belongsTo(ShiftSwapRequest::class, 'swap_id', 'swap_id');
    }

    public function isNormal()
    {
        return $this->status === self::STATUS_NORMAL;
    }

        public function isOnCall()
    {
        return $this->type === 'oncall';
    }

    // 🆕 SCOPE ONCALL
    public function scopeOnCall($query)
    {
        return $query->where('type', 'oncall');
    }


    // ✅ UPDATE JADWAL DAN ABSEN DARI IJIN
    public function applyIjin(Ijin $ijin)
    {
        // Update jadwal
        $this->update([
            'ijin_id' => $ijin->ijin_id,
            'status' => self::STATUS_HAS_IJIN,
            'notes' => "Ijin: {$ijin->ijinType->name} - {$ijin->reason}"
        ]);

        // Update absen (cascade)
        if ($this->absen) {
            $this->absen->update([
                'ijin_id' => $ijin->ijin_id,
                'status' => $ijin->ijinType->code,
                'notes' => "Ijin: {$ijin->ijinType->name} - {$ijin->reason}"
            ]);
        }
    }

    // ✅ HAPUS IJIN DARI JADWAL
    public function removeIjin()
    {
        $this->update([
            'ijin_id' => null,
            'status' => self::STATUS_NORMAL,
            'notes' => null
        ]);

        // Reset absen
        if ($this->absen && !$this->absen->clock_in) {
            $this->absen->update([
                'ijin_id' => null,
                'status' => Absen::STATUS_SCHEDULED,
                'notes' => null
            ]);
        }
    }

    public static function generateJadwalId()
    {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $jadwalId = 'JDW' . $randomString;
            $exists = self::where('jadwal_id', $jadwalId)->exists();
        } while ($exists);

        return $jadwalId;
    }

    // ✅ BOOT: AUTO CREATE ABSEN
    protected static function boot()
    {
        parent::boot();

        static::created(function ($jadwal) {
            Absen::create([
                'absen_id' => Absen::generateAbsenId(),
                'karyawan_id' => $jadwal->karyawan_id,
                'jadwal_id' => $jadwal->jadwal_id,
                'date' => $jadwal->date,
                'status' => Absen::STATUS_SCHEDULED
            ]);
        });
    }
}
