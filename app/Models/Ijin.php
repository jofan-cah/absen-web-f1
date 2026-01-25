<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Ijin extends Model
{
    use HasFactory;
    // LogsActivity disabled - causing issues with old data
    // use LogsActivity;

    // protected static $logName = 'Ijin';

    protected $primaryKey = 'ijin_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ijin_id',
        'karyawan_id',
        'ijin_type_id',
        'date_from',
        'date_to',
        'reason',
        'original_shift_date',
        'replacement_shift_date',
        'coordinator_id',
        'coordinator_status',
        'coordinator_note',
        'coordinator_reviewed_at',
        'admin_id',
        'admin_status',
        'admin_note',
        'admin_reviewed_at',
        'status',
        'photo_path',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'original_shift_date' => 'date',
        'replacement_shift_date' => 'date',
        'coordinator_reviewed_at' => 'datetime',
        'admin_reviewed_at' => 'datetime',
    ];

    // Tambah di $appends
protected $appends = ['total_days', 'photo_url'];

    public function getTotalDaysAttribute()
    {
        if (!$this->date_from || !$this->date_to) {
            return 0;
        }

        $from = Carbon::parse($this->date_from);
        $to = Carbon::parse($this->date_to);

        return $from->diffInDays($to) + 1;
    }

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function ijinType()
    {
        return $this->belongsTo(IjinType::class, 'ijin_type_id', 'ijin_type_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'ijin_id', 'ijin_id');
    }

    public function absens()
    {
        return $this->hasMany(Absen::class, 'ijin_id', 'ijin_id');
    }

    // Apply ijin ke jadwal
    public function applyToJadwals()
    {
        $from = Carbon::parse($this->date_from);
        $to = Carbon::parse($this->date_to);

        Log::info('Applying Ijin to Jadwals', [
            'ijin_id' => $this->ijin_id,
            'type' => $this->ijinType->code,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ]);

        switch ($this->ijinType->code) {
            case 'shift_swap':
                $this->processShiftSwap();
                break;

            case 'compensation_leave':
                $this->processCompensationLeave();
                break;

            default:
                $this->processRegularLeave();
                break;
        }
    }

    private function processRegularLeave()
    {
        $from = Carbon::parse($this->date_from);
        $to = Carbon::parse($this->date_to);

        while ($from->lte($to)) {
            $jadwal = Jadwal::where('karyawan_id', $this->karyawan_id)
                            ->where('date', $from->toDateString())
                            ->first();

            if ($jadwal && $jadwal->isNormal()) {
                $jadwal->applyIjin($this);
            } elseif (!$jadwal) {
                Absen::create([
                    'absen_id' => Absen::generateAbsenId(),
                    'karyawan_id' => $this->karyawan_id,
                    'jadwal_id' => null,
                    'ijin_id' => $this->ijin_id,
                    'date' => $from->toDateString(),
                    'status' => $this->ijinType->code,
                    'notes' => "Ijin: {$this->ijinType->name} - {$this->reason}"
                ]);
            }

            $from->addDay();
        }
    }

    private function processShiftSwap()
    {
        $originalJadwal = Jadwal::where('karyawan_id', $this->karyawan_id)
                                ->where('date', $this->original_shift_date)
                                ->first();

        if (!$originalJadwal) {
            Log::warning('Original jadwal not found for shift swap', [
                'ijin_id' => $this->ijin_id,
                'date' => $this->original_shift_date,
            ]);
            return;
        }

        // Update jadwal asli jadi inactive
        $originalJadwal->update([
            'ijin_id' => $this->ijin_id,
            'status' => Jadwal::STATUS_HAS_IJIN,
            'is_active' => false,
            'notes' => "Tukar shift ke " . Carbon::parse($this->replacement_shift_date)->format('d/m/Y')
        ]);

        // Update absen asli
        if ($originalJadwal->absen) {
            $originalJadwal->absen->update([
                'ijin_id' => $this->ijin_id,
                'status' => 'shift_swap',
                'notes' => "Tukar shift ke " . Carbon::parse($this->replacement_shift_date)->format('d/m/Y')
            ]);
        }

        // ✅ CEK APAKAH JADWAL DI TANGGAL PENGGANTI SUDAH ADA
        $existingReplacementJadwal = Jadwal::where('karyawan_id', $this->karyawan_id)
                                        ->where('date', $this->replacement_shift_date)
                                        ->first();

        if ($existingReplacementJadwal) {
            // ✅ JIKA SUDAH ADA, UPDATE AJA
            $existingReplacementJadwal->update([
                'ijin_id' => $this->ijin_id,
                'shift_id' => $originalJadwal->shift_id,
                'is_active' => true,
                'status' => Jadwal::STATUS_NORMAL,
                'notes' => "Tukar shift dari " . Carbon::parse($this->original_shift_date)->format('d/m/Y'),
            ]);

            Log::info('Updated existing replacement jadwal for shift swap', [
                'ijin_id' => $this->ijin_id,
                'jadwal_id' => $existingReplacementJadwal->jadwal_id,
                'date' => $this->replacement_shift_date,
            ]);
        } else {
            // ✅ JIKA BELUM ADA, CREATE BARU
            Jadwal::create([
                'jadwal_id' => Jadwal::generateJadwalId(),
                'karyawan_id' => $this->karyawan_id,
                'shift_id' => $originalJadwal->shift_id,
                'ijin_id' => $this->ijin_id,
                'date' => $this->replacement_shift_date,
                'is_active' => true,
                'status' => Jadwal::STATUS_NORMAL,
                'notes' => "Tukar shift dari " . Carbon::parse($this->original_shift_date)->format('d/m/Y'),
                'created_by_user_id' => auth()->user()->user_id ?? 'SYSTEM',
            ]);

            Log::info('Created new replacement jadwal for shift swap', [
                'ijin_id' => $this->ijin_id,
                'date' => $this->replacement_shift_date,
            ]);
        }
    }

    private function processCompensationLeave()
    {
        $from = Carbon::parse($this->date_from);
        $to = Carbon::parse($this->date_to);

        while ($from->lte($to)) {
            $jadwal = Jadwal::where('karyawan_id', $this->karyawan_id)
                            ->where('date', $from->toDateString())
                            ->first();

            if ($jadwal) {
                $jadwal->update([
                    'ijin_id' => $this->ijin_id,
                    'status' => Jadwal::STATUS_HAS_IJIN,
                    'is_active' => false,
                    'notes' => "Cuti pengganti - Piket " . Carbon::parse($this->original_shift_date)->format('d/m/Y')
                ]);

                if ($jadwal->absen) {
                    $jadwal->absen->update([
                        'ijin_id' => $this->ijin_id,
                        'status' => 'compensation_leave',
                        'notes' => "Cuti pengganti - Piket " . Carbon::parse($this->original_shift_date)->format('d/m/Y')
                    ]);
                }
            }

            $from->addDay();
        }
    }

    public function removeFromJadwals()
    {
        Log::info('Removing Ijin from Jadwals', [
            'ijin_id' => $this->ijin_id,
        ]);

        $this->jadwals()->each(function ($jadwal) {
            $jadwal->removeIjin();
        });

        Absen::where('ijin_id', $this->ijin_id)
             ->whereNull('jadwal_id')
             ->delete();
    }

    public static function generateIjinId()
    {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $length = rand(12, 17);
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            $ijinId = 'IJN' . $randomString;
            $exists = self::where('ijin_id', $ijinId)->exists();
        } while ($exists);

        return $ijinId;
    }

    // Scope helpers
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function needsCoordinatorReview()
    {
        return $this->coordinator_status === 'pending' && $this->status === 'pending';
    }

    public function needsAdminReview()
    {
        return $this->coordinator_status === 'approved'
               && $this->admin_status === 'pending'
               && $this->status === 'pending';
    }

    public function isShiftSwap()
    {
        return $this->ijinType->code === 'shift_swap';
    }

    public function isCompensationLeave()
    {
        return $this->ijinType->code === 'compensation_leave';
    }

        public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }

        // Generate temporary signed URL (valid for 60 minutes)
        return Storage::disk('s3')->temporaryUrl(
            $this->photo_path,
            now()->addMinutes(60)
        );
    }

    // Helper methods
    public function hasPhoto()
    {
        return !empty($this->photo_path) && Storage::disk('s3')->exists($this->photo_path);
    }




    // ✅ BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        // ✅ AUTO GENERATE ID
        static::creating(function ($model) {
            if (empty($model->ijin_id)) {
                $model->ijin_id = self::generateIjinId();
            }
        });

        static::updated(function ($ijin) {
            if ($ijin->isDirty('status') && $ijin->status === 'approved') {
                // ✅ LOAD IJIN TYPE DULU
                if (!$ijin->relationLoaded('ijinType')) {
                    $ijin->load('ijinType');
                }

                $ijin->applyToJadwals();
            }
        });

        // ✅ KETIKA REJECTED → RESET JADWAL & ABSEN
        static::updated(function ($ijin) {
            if ($ijin->isDirty('status') && $ijin->status === 'rejected') {
                $ijin->removeFromJadwals();
            }
        });

           static::deleting(function ($model) {
            // Delete photo when ijin is deleted
            $model->deletePhoto();
        });
    }
}
