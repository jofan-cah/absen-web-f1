<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
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

    // Helper method
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

    // Permission check
    public function canEditSchedule()
    {
        return in_array($this->staff_status, ['koordinator', 'wakil_koordinator']);
    }
}
