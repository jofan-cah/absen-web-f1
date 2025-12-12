<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Karyawan extends Model
{
    use HasFactory;

    protected $primaryKey = 'karyawan_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'karyawan_id',
        'user_id',
        'department_id',
        'nip',
        'full_name',
        'position',
        'phone',
        'address',
        'hire_date',
        'birth_date',
        'gender',
        'photo',
        'employment_status',
        'staff_status',
        'uang_kuota',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'uang_kuota' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'karyawan_id', 'karyawan_id');
    }

    public function absens()
    {
        return $this->hasMany(Absen::class, 'karyawan_id', 'karyawan_id');
    }

    public function ijins()
    {
        return $this->hasMany(Ijin::class, 'karyawan_id', 'karyawan_id');
    }



    public static function generateNIP($hireDate = null)
    {
        $companyCode = '01';
        $date = $hireDate ? Carbon::parse($hireDate) : Carbon::now();
        $year = $date->format('y');
        $month = $date->format('m');

        // Format tetap 01yymm
        $prefix = $companyCode . $year . $month;

        // Ambil sequence terbesar global (tidak peduli ganti bulan)
        $maxSequence = self::selectRaw('MAX(CAST(RIGHT(nip, 3) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $sequence = $maxSequence ? $maxSequence + 1 : 1;
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        return $prefix . $sequenceFormatted;
    }


    // Helper method untuk generate Karyawan ID
    public static function generateKaryawanId()
    {
        $lastKaryawan = self::orderByDesc('karyawan_id')->first();
        if (!$lastKaryawan) {
            return 'KAR001';
        }

        $lastNumber = (int) substr($lastKaryawan->karyawan_id, 3);
        $newNumber = $lastNumber + 1;

        return 'KAR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Boot method - Auto generate saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto generate karyawan_id jika kosong
            if (empty($model->karyawan_id)) {
                $model->karyawan_id = self::generateKaryawanId();
            }

            // Auto generate NIP jika kosong
            if (empty($model->nip)) {
                $model->nip = self::generateNIP($model->hire_date);
            }
        });
    }

    // Permission check
    public function canEditSchedule()
    {
        return in_array($this->staff_status, ['koordinator', 'wakil_koordinator']);
    }

    // Helper untuk cek ijin aktif hari ini
    public function hasActiveIjinToday()
    {
        $today = now()->format('Y-m-d');

        return $this->ijins()
            ->where('status', 'approved')
            ->where('date_from', '<=', $today)
            ->where('date_to', '>=', $today)
            ->exists();
    }

    // Helper untuk get ijin hari ini
    public function getTodayIjin()
    {
        $today = now()->format('Y-m-d');

        return $this->ijins()
            ->where('status', 'approved')
            ->where('date_from', '<=', $today)
            ->where('date_to', '>=', $today)
            ->with('ijinType')
            ->first();
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'karyawan_id', 'karyawan_id')
            ->where('is_active', true);
    }

    /**
     * Get array of active device tokens (untuk kirim FCM)
     */
    public function getActiveDeviceTokens()
    {
        return $this->deviceTokens()->pluck('device_token')->toArray();
    }
}
