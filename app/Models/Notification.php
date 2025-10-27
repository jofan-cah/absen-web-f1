<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'notification_id',
        'karyawan_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'fcm_sent',
        'fcm_sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'fcm_sent' => 'boolean',
        'fcm_sent_at' => 'datetime'
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    // ============================================
    // BOOT & ID GENERATION
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->notification_id)) {
                $model->notification_id = self::generateNotificationId();
            }
        });
    }

    public static function generateNotificationId()
    {
        // Format: NOTIF + YmdHis + 4 digit random
        // Contoh: NOTIF202510220830001234
        return 'NOTIF' . now()->format('YmdHis') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Mark FCM as sent
     */
    public function markFCMSent()
    {
        $this->update([
            'fcm_sent' => true,
            'fcm_sent_at' => now()
        ]);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope: Get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope: Get by karyawan
     */
    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    /**
     * Scope: Get by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Recent notifications (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    // ============================================
    // STATIC FACTORY METHODS
    // ============================================

    /**
     * Create reminder clock in notification
     */
    public static function createReminderClockIn($karyawanId, $jadwalId, $shiftName)
    {
        return self::create([
            'karyawan_id' => $karyawanId,
            'type' => 'reminder_clock_in',
            'title' => 'Reminder Absen Masuk',
            // 'message' => "Jangan lupa absen masuk ya! Shift {$shiftName} sudah dimulai.",
            //  'message' => "Wayahe Kerjo njir Absent e yo di pikir ! shift {$shiftName} sudah dimulai.",
             'message' => "HIDUP JOK.......",
            'data' => [
                'jadwal_id' => $jadwalId,
                'date' => now()->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Create reminder clock out notification
     */
    public static function createReminderClockOut($karyawanId, $absenId, $shiftName)
    {
        return self::create([
            'karyawan_id' => $karyawanId,
            'type' => 'reminder_clock_out',
            'title' => 'Reminder Absen Pulang',
            'message' => "Jangan lupa absen pulang ya! Shift {$shiftName} sudah selesai.",
            'data' => [
                'absen_id' => $absenId,
                'date' => now()->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Create absent alert notification
     */
    public static function createAbsentAlert($karyawanId, $date)
    {
        return self::create([
            'karyawan_id' => $karyawanId,
            'type' => 'absent_alert',
            'title' => 'Kamu Belum Absen!',
            'message' => "Kamu belum absen hari ini. Segera hubungi koordinator untuk konfirmasi.",
            'data' => [
                'date' => $date
            ]
        ]);
    }
}
