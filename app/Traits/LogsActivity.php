<?php

namespace App\Traits;

use App\Models\ActivityLog;

/**
 * Trait LogsActivity
 *
 * Tambahkan trait ini ke Model untuk auto-log create, update, delete
 *
 * Contoh penggunaan:
 * ```php
 * class Karyawan extends Model
 * {
 *     use LogsActivity;
 *
 *     // Optional: customize fields yang di-log
 *     protected static $logAttributes = ['name', 'email', 'status'];
 *
 *     // Optional: fields yang tidak di-log
 *     protected static $logExcept = ['password', 'remember_token'];
 *
 *     // Optional: custom log name
 *     protected static $logName = 'Karyawan';
 * }
 * ```
 */
trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        // Log saat create
        static::created(function ($model) {
            if (static::shouldLog()) {
                ActivityLog::logCreate($model, static::getLogDescription('create', $model));
            }
        });

        // Simpan data lama sebelum update
        static::updating(function ($model) {
            $model->oldAttributes = $model->getOriginal();
        });

        // Log saat update
        static::updated(function ($model) {
            if (static::shouldLog() && $model->wasChanged()) {
                $oldData = $model->oldAttributes ?? $model->getOriginal();
                ActivityLog::logUpdate($model, $oldData, static::getLogDescription('update', $model));
            }
        });

        // Log saat delete
        static::deleted(function ($model) {
            if (static::shouldLog()) {
                ActivityLog::logDelete($model, static::getLogDescription('delete', $model));
            }
        });
    }

    /**
     * Cek apakah harus log (bisa di-override di model)
     */
    protected static function shouldLog(): bool
    {
        // Skip jika ada property $disableLogging = true
        if (property_exists(static::class, 'disableLogging') && static::$disableLogging) {
            return false;
        }

        return true;
    }

    /**
     * Get deskripsi log (bisa di-override di model)
     */
    protected static function getLogDescription(string $action, $model): ?string
    {
        $modelName = static::getLogName();
        $identifier = static::getModelIdentifier($model);

        return match ($action) {
            'create' => "Membuat {$modelName} baru: {$identifier}",
            'update' => "Mengupdate {$modelName}: {$identifier}",
            'delete' => "Menghapus {$modelName}: {$identifier}",
            default => null,
        };
    }

    /**
     * Get nama model untuk log
     */
    protected static function getLogName(): string
    {
        if (property_exists(static::class, 'logName')) {
            return static::$logName;
        }

        return class_basename(static::class);
    }

    /**
     * Get identifier model (bisa di-override)
     */
    protected static function getModelIdentifier($model): string
    {
        // Coba beberapa common fields
        if (isset($model->name)) {
            return $model->name;
        }

        if (isset($model->full_name)) {
            return $model->full_name;
        }

        if (isset($model->title)) {
            return $model->title;
        }

        return (string) $model->getKey();
    }

    /**
     * Disable logging sementara
     */
    public static function withoutLogging(callable $callback)
    {
        $originalValue = property_exists(static::class, 'disableLogging')
            ? static::$disableLogging
            : false;

        static::$disableLogging = true;

        try {
            return $callback();
        } finally {
            static::$disableLogging = $originalValue;
        }
    }
}
