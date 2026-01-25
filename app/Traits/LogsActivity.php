<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Trait LogsActivity
 *
 * Auto-log create, update, delete untuk Model
 * Semua error di-catch agar tidak break operasi utama
 */
trait LogsActivity
{
    /**
     * Property untuk simpan data lama sebelum update
     */
    protected $oldAttributesForLog = [];

    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        // ==========================================
        // LOG SAAT CREATE
        // ==========================================
        static::created(function ($model) {
            static::safeLogActivity('create', $model);
        });

        // ==========================================
        // SIMPAN DATA LAMA SEBELUM UPDATE
        // ==========================================
        static::updating(function ($model) {
            try {
                $model->oldAttributesForLog = $model->getOriginal();
            } catch (\Exception $e) {
                // Ignore
            }
        });

        // ==========================================
        // LOG SAAT UPDATE
        // ==========================================
        static::updated(function ($model) {
            if ($model->wasChanged()) {
                static::safeLogActivity('update', $model, $model->oldAttributesForLog ?? []);
            }
        });

        // ==========================================
        // LOG SAAT DELETE
        // ==========================================
        static::deleted(function ($model) {
            static::safeLogActivity('delete', $model);
        });
    }

    /**
     * Safe wrapper untuk log activity
     * Tidak akan throw exception, hanya log warning jika gagal
     */
    protected static function safeLogActivity(string $action, $model, array $oldData = []): void
    {
        try {
            // Skip jika table activity_logs tidak ada
            if (!Schema::hasTable('activity_logs')) {
                return;
            }

            // Skip jika logging disabled
            if (!static::shouldLogActivity()) {
                return;
            }

            $moduleName = static::getActivityLogName();
            $moduleId = static::safeGetKey($model);
            $identifier = static::safeGetIdentifier($model);

            // Prepare data dengan safe encoding
            $newData = static::safeToArray($model);
            $oldDataSafe = static::safeEncodeData($oldData);
            $changedFields = [];

            // Hitung changed fields untuk update
            if ($action === 'update' && !empty($oldData)) {
                $changedFields = static::calculateChangedFields($oldDataSafe, $newData);
            }

            // Description
            $description = match ($action) {
                'create' => "Membuat {$moduleName}: {$identifier}",
                'update' => "Mengupdate {$moduleName}: {$identifier}",
                'delete' => "Menghapus {$moduleName}: {$identifier}",
                default => "{$action} {$moduleName}: {$identifier}",
            };

            // Log ke ActivityLog
            ActivityLog::log($action, $description, [
                'module' => $moduleName,
                'module_id' => $moduleId,
                'old_data' => $action === 'update' || $action === 'delete' ? $oldDataSafe : null,
                'new_data' => $action === 'create' || $action === 'update' ? $newData : null,
                'changed_fields' => !empty($changedFields) ? $changedFields : null,
            ]);

        } catch (\Exception $e) {
            // Log warning tapi jangan break operasi utama
            Log::warning("LogsActivity failed for {$action}: " . $e->getMessage(), [
                'model' => get_class($model),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check apakah harus log
     */
    protected static function shouldLogActivity(): bool
    {
        // Skip jika ada property $disableLogging = true
        if (property_exists(static::class, 'disableLogging') && static::$disableLogging === true) {
            return false;
        }
        return true;
    }

    /**
     * Get nama model untuk log
     */
    protected static function getActivityLogName(): string
    {
        if (property_exists(static::class, 'logName') && !empty(static::$logName)) {
            return static::$logName;
        }
        return class_basename(static::class);
    }

    /**
     * Safe get primary key
     */
    protected static function safeGetKey($model): ?string
    {
        try {
            $key = $model->getKey();
            return $key ? (string) $key : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Safe get identifier untuk description
     */
    protected static function safeGetIdentifier($model): string
    {
        try {
            // Coba beberapa common fields
            $fields = ['name', 'full_name', 'title', 'nip', 'code'];
            foreach ($fields as $field) {
                if (isset($model->{$field}) && !empty($model->{$field})) {
                    return (string) $model->{$field};
                }
            }
            // Fallback ke primary key
            return (string) ($model->getKey() ?? 'unknown');
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Safe convert model to array
     * Handle potential encoding issues
     */
    protected static function safeToArray($model): array
    {
        try {
            $data = $model->toArray();
            return static::safeEncodeData($data);
        } catch (\Exception $e) {
            // Fallback: hanya ambil fillable attributes
            try {
                $fillable = $model->getFillable();
                $data = [];
                foreach ($fillable as $field) {
                    if (isset($model->{$field})) {
                        $data[$field] = $model->{$field};
                    }
                }
                return static::safeEncodeData($data);
            } catch (\Exception $e2) {
                return ['error' => 'Failed to serialize model data'];
            }
        }
    }

    /**
     * Safe encode data untuk JSON storage
     * Remove/convert problematic values
     */
    protected static function safeEncodeData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            try {
                // Skip binary/blob data
                if (is_resource($value)) {
                    $result[$key] = '[RESOURCE]';
                    continue;
                }

                // Skip sangat besar data
                if (is_string($value) && strlen($value) > 10000) {
                    $result[$key] = '[TRUNCATED: ' . strlen($value) . ' chars]';
                    continue;
                }

                // Convert objects to string
                if (is_object($value)) {
                    if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                        $result[$key] = $value->format('Y-m-d H:i:s');
                    } elseif (method_exists($value, '__toString')) {
                        $result[$key] = (string) $value;
                    } else {
                        $result[$key] = '[OBJECT: ' . get_class($value) . ']';
                    }
                    continue;
                }

                // Nested arrays
                if (is_array($value)) {
                    $result[$key] = static::safeEncodeData($value);
                    continue;
                }

                $result[$key] = $value;
            } catch (\Exception $e) {
                $result[$key] = '[ERROR: ' . $e->getMessage() . ']';
            }
        }
        return $result;
    }

    /**
     * Calculate changed fields between old and new data
     */
    protected static function calculateChangedFields(array $oldData, array $newData): array
    {
        $changes = [];
        $skipFields = ['created_at', 'updated_at', 'deleted_at'];

        foreach ($newData as $key => $newValue) {
            if (in_array($key, $skipFields)) {
                continue;
            }

            $oldValue = $oldData[$key] ?? null;

            // Compare dengan type juggling
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Disable logging sementara untuk operasi tertentu
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
