<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IjinType extends Model
{
    use HasFactory;

    protected $primaryKey = 'ijin_type_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ijin_type_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function ijins()
    {
        return $this->hasMany(Ijin::class, 'ijin_type_id', 'ijin_type_id');
    }

    // Helper method untuk generate ID
    public static function generateIjinTypeId()
    {
        do {
            // Format: IJT + 12 karakter random = 15 karakter total
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';

            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $ijinTypeId = 'IJT' . $randomString;
            $exists = self::where('ijin_type_id', $ijinTypeId)->exists();
        } while ($exists);

        return $ijinTypeId;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ijin_type_id)) {
                $model->ijin_type_id = self::generateIjinTypeId();
            }
        });
    }
}
