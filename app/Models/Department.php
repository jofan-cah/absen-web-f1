<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'department_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'description',
        'manager_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id', 'user_id');
    }

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'department_id', 'department_id');
    }

    // Helper method
    public static function generateDepartmentId()
    {
        $lastDept = self::orderByDesc('department_id')->first();
        if (!$lastDept) {
            return 'DEPT001';
        }

        $lastNumber = (int) substr($lastDept->department_id, 4);
        $newNumber = $lastNumber + 1;

        return 'DEPT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
