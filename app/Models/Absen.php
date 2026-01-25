<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;
    // LogsActivity disabled - causing issues with old data
    // use LogsActivity;

    // protected static $logName = 'Absen';

    protected $primaryKey = 'absen_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'absen_id',
        'karyawan_id',
        'jadwal_id',
        'ijin_id',      // ✅ Referensi ke ijin (sync dari jadwal)
        'date',
        'clock_in',
        'clock_in_photo',
        'clock_in_latitude',
        'clock_in_longitude',
        'clock_in_address',
        'clock_out',
        'clock_out_photo',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_out_address',
        'status',       // ✅ Follow dari ijin_type->code
        'late_minutes',
        'type',
        'early_checkout_minutes',
        'work_hours',
        'notes',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'clock_in_latitude' => 'decimal:8',
        'clock_in_longitude' => 'decimal:8',
        'clock_out_latitude' => 'decimal:8',
        'clock_out_longitude' => 'decimal:8',
        'work_hours' => 'decimal:2',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_PRESENT = 'present';
    const STATUS_LATE = 'late';
    const STATUS_EARLY_CHECKOUT = 'early_checkout';
    const STATUS_ABSENT = 'absent';
    const STATUS_SICK = 'sick';
    const STATUS_ANNUAL = 'annual';
    const STATUS_PERSONAL = 'personal';
    const STATUS_SHIFT_SWAP = 'shift_swap';
    const STATUS_COMPENSATION_LEAVE = 'compensation_leave';

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'jadwal_id');
    }

    public function ijin()
    {
        return $this->belongsTo(Ijin::class, 'ijin_id', 'ijin_id');
    }

    // Helper methods
    public function hasIjin()
    {
        return !is_null($this->ijin_id);
    }

    public function isOnCall()
    {
        return $this->type === 'oncall';
    }
        public function scopeOnCall($query)
    {
        return $query->where('type', 'oncall');
    }



    public function getUangMakanAttribute()
    {
        // Kalau OnCall → gak dapat uang makan normal
        if ($this->isOnCall()) {
            return 0;
        }

        // Kalau kerja >= 8 jam → dapat 1 uang makan
        if ($this->work_hours >= 8) {
            return 1;
        }

        return 0;
    }
    public function isLeaveStatus()
    {
        return in_array($this->status, [
            self::STATUS_SICK,
            self::STATUS_ANNUAL,
            self::STATUS_PERSONAL,
            self::STATUS_COMPENSATION_LEAVE
        ]);
    }

    public function canClockIn()
    {
        return !$this->isLeaveStatus();
    }

    public function isEditable()
    {
        return $this->status === self::STATUS_SCHEDULED
            && !$this->clock_in
            && !$this->ijin_id;
    }

    // Accessor
    public function getClockInPhotoUrlAttribute()
    {
        if ($this->clock_in_photo) {
            return Storage::disk('s3')->temporaryUrl(
                $this->clock_in_photo,
                now()->addMinutes(60)
            );
        }
        return null;
    }

    public function getClockOutPhotoUrlAttribute()
    {
        if ($this->clock_out_photo) {
            return Storage::disk('s3')->temporaryUrl(
                $this->clock_out_photo,
                now()->addMinutes(60)
            );
        }
        return null;
    }

    public static function generateAbsenId()
    {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $absenId = 'ABS' . $randomString;
            $exists = self::where('absen_id', $absenId)->exists();
        } while ($exists);

        return $absenId;
    }
}
