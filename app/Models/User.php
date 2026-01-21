<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id', 'user_id');
    }

    // Helper method
    public static function generateUserId()
    {
        $lastUser = self::orderByDesc('user_id')->first();
        if (!$lastUser) {
            return 'USR001';
        }

        $lastNumber = (int) substr($lastUser->user_id, 3);
        $newNumber = $lastNumber + 1;

        return 'USR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getDepartmentId()
    {
        return $this->karyawan ? $this->karyawan->department_id : null;
    }

    public function canManageSchedule()
    {
        return in_array($this->role, ['admin', 'coordinator', 'wakil_coordinator']);
    }

        public function departments()
    {
        return $this->belongsToMany(Department::class, 'koordinator_departments');
    }
}
