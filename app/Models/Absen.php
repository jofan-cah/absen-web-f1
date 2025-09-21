<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absen extends Model
{
    use HasFactory;

    protected $primaryKey = 'absen_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'absen_id',
        'karyawan_id',
        'jadwal_id',
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
        'status',
        'late_minutes',
        'early_checkout_minutes',
        'work_hours',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_latitude' => 'decimal:8',
        'clock_in_longitude' => 'decimal:8',
        'clock_out_latitude' => 'decimal:8',
        'clock_out_longitude' => 'decimal:8',
        'work_hours' => 'decimal:2',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'jadwal_id');
    }

    // Helper method
    public static function generateAbsenId()
    {
        $lastAbsen = self::orderByDesc('absen_id')->first();
        if (!$lastAbsen) {
            return 'ABS001';
        }

        $lastNumber = (int) substr($lastAbsen->absen_id, 3);
        $newNumber = $lastNumber + 1;

        return 'ABS' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Check if can be edited
    public function isEditable()
    {
        return $this->status === 'scheduled';
    }
}
