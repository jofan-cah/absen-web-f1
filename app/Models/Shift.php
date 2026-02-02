<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory, LogsActivity;

    protected static $logName = 'Shift';

    protected $primaryKey = 'shift_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'shift_id',
        'name',
        'code',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'break_duration',
        'late_tolerance',
        'early_checkout_tolerance',
        'is_overnight',
        'is_active',
    ];

    protected $casts = [
        'is_overnight' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'shift_id', 'shift_id');
    }

    // Helper method
    public static function generateShiftId()
    {
        // Get the highest numeric part from existing shift_ids
        $lastShift = self::selectRaw("MAX(CAST(SUBSTRING(shift_id, 4) AS UNSIGNED)) as max_num")
            ->first();

        $lastNumber = $lastShift->max_num ?? 0;
        $newNumber = $lastNumber + 1;

        return 'SHF' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
