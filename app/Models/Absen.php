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
        'date' => 'datetime:Y-m-d',
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

    public static function generateAbsenId()
    {
        do {
            // Format: ABS + 12 karakter random alphanumeric = 15 karakter total
            // Contoh: ABSF9E2D1C8B7A6

            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $absenId = 'ABS' . $randomString;

            // Pastikan unique dengan cek database
            $exists = self::where('absen_id', $absenId)->exists();
        } while ($exists);

        return $absenId;
    }

    // Check if can be edited
    public function isEditable()
    {
        return $this->status === 'scheduled';
    }
}
