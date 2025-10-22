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
          'uang_kuota'=> 'boolean'
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
        // Company code fixed
        $companyCode = '01';

        // Gunakan hire_date atau tanggal sekarang
        $date = $hireDate ? Carbon::parse($hireDate) : Carbon::now();

        // Ambil 2 digit terakhir tahun: 2025 -> 25
        $year = $date->format('y');

        // Ambil 2 digit bulan: Desember -> 12
        $month = $date->format('m');

        // Cari nomor urut terakhir dari bulan dan tahun yang sama
        $lastKaryawan = self::whereYear('hire_date', $date->year)
                            ->whereMonth('hire_date', $date->month)
                            ->orderByDesc('nip')
                            ->first();

        if (!$lastKaryawan) {
            $sequence = 1;
        } else {
            // Ambil 3 digit terakhir dari NIP
            $lastSequence = (int) substr($lastKaryawan->nip, -3);
            $sequence = $lastSequence + 1;
        }

        // Format sequence jadi 3 digit: 001, 002, 003
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        // Gabungkan: 01 + 25 + 12 + 001 = 012512001
        return $companyCode . $year . $month . $sequenceFormatted;
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

        return 'KAR'.str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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
}
