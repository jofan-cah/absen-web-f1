<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventAttendance extends Model
{
    use HasFactory;

    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'attendance_id',
        'event_id',
        'karyawan_id',
        'check_in_at',
        'method',
        'jumlah_orang',
        'keterangan',
        'latitude',
        'longitude',
        'ticket_token',
        'verified_by',
    ];

    protected $casts = [
        'check_in_at'  => 'datetime',
        'jumlah_orang' => 'integer',
        'latitude'     => 'float',
        'longitude'    => 'float',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    // ─── ID & Token Generator ────────────────────────────────────────────────────

    public static function generateAttendanceId(): string
    {
        return 'EAT' . strtoupper(Str::random(12));
    }

    public static function generateTicketToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    // ─── Boot ───────────────────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->attendance_id)) {
                $model->attendance_id = self::generateAttendanceId();
            }
            if (empty($model->ticket_token)) {
                $model->ticket_token = self::generateTicketToken();
            }
            if (empty($model->check_in_at)) {
                $model->check_in_at = now();
            }
        });
    }
}
