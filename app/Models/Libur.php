<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libur extends Model
{
    use HasFactory;

    protected $primaryKey = 'libur_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'libur_id',
        'name',
        'date',
        'type',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Helper method for ID generation
    public static function generateLiburId()
    {
        $lastLibur = self::orderByDesc('libur_id')->first();
        if (!$lastLibur) {
            return 'LIB001';
        }

        $lastNumber = (int) substr($lastLibur->libur_id, 3);
        $newNumber = $lastNumber + 1;

        return 'LIB' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Boot method for automatic ID assignment
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->libur_id)) {
                $model->libur_id = self::generateLiburId();
            }
        });
    }
}
