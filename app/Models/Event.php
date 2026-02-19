<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'type',
        'location',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'qr_token',
        'qr_secret',
        'qr_refresh_seconds',
        'max_participants',
        'allow_multi_scan',
        'latitude',
        'longitude',
        'radius',
        'department_id',
        'created_by',
        'status',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'allow_multi_scan' => 'boolean',
        'qr_refresh_seconds' => 'integer',
        'max_participants' => 'integer',
        'radius'           => 'integer',
        'latitude'         => 'float',
        'longitude'        => 'float',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class, 'event_id', 'event_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'ongoing']);
    }

    // ─── ID Generator ───────────────────────────────────────────────────────────

    public static function generateEventId(): string
    {
        $last = self::orderByDesc('event_id')->first();
        if (!$last) {
            return 'EVT001';
        }
        $num = (int) substr($last->event_id, 3);
        return 'EVT' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }

    // ─── OTP ────────────────────────────────────────────────────────────────────

    /**
     * Generate a time-based OTP for QR display.
     * Returns [otp, ts].
     */
    public function generateOtp(): array
    {
        $ts  = now()->timestamp;
        $otp = substr(hash_hmac('sha256', $this->qr_token . $ts, $this->qr_secret), 0, 12);
        return ['otp' => $otp, 'ts' => $ts];
    }

    /**
     * Validate OTP received from mobile scan.
     */
    public function validateOtp(string $otp, int $ts): bool
    {
        $ttl      = $this->qr_refresh_seconds * 2;
        $expected = substr(hash_hmac('sha256', $this->qr_token . $ts, $this->qr_secret), 0, 12);
        return hash_equals($expected, $otp) && abs(time() - $ts) <= $ttl;
    }

    /**
     * Check if GPS validation is enabled.
     */
    public function hasGpsValidation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Calculate distance (meters) from event location to given coordinates.
     * Uses Haversine formula.
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $R    = 6371000; // Earth radius in meters
        $phi1 = deg2rad($this->latitude);
        $phi2 = deg2rad($lat);
        $dphi = deg2rad($lat - $this->latitude);
        $dlam = deg2rad($lng - $this->longitude);

        $a = sin($dphi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dlam / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    // ─── Boot ───────────────────────────────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->event_id)) {
                $model->event_id = self::generateEventId();
            }
            if (empty($model->qr_token)) {
                $model->qr_token = bin2hex(random_bytes(32));
            }
            if (empty($model->qr_secret)) {
                $model->qr_secret = bin2hex(random_bytes(32));
            }
        });
    }
}
