<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'karyawan_id',
        'user_name',
        'action',
        'module',
        'module_id',
        'description',
        'old_data',
        'new_data',
        'changed_fields',
        'error_message',
        'error_trace',
        'ip_address',
        'user_agent',
        'request_url',
        'request_method',
        'platform',
        'device_type',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'changed_fields' => 'array',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    // ========================================
    // HELPER METHODS - STATIC LOGGING
    // ========================================

    /**
     * Log aktivitas umum
     */
    public static function log(string $action, string $description, array $options = []): self
    {
        $user = Auth::user();
        $request = Request::instance();

        return self::create([
            'user_id' => $options['user_id'] ?? $user?->user_id,
            'karyawan_id' => $options['karyawan_id'] ?? $user?->karyawan?->karyawan_id,
            'user_name' => $options['user_name'] ?? $user?->name,
            'action' => $action,
            'module' => $options['module'] ?? null,
            'module_id' => $options['module_id'] ?? null,
            'description' => $description,
            'old_data' => $options['old_data'] ?? null,
            'new_data' => $options['new_data'] ?? null,
            'changed_fields' => $options['changed_fields'] ?? null,
            'error_message' => $options['error_message'] ?? null,
            'error_trace' => $options['error_trace'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method(),
            'platform' => self::detectPlatform($request),
            'device_type' => $options['device_type'] ?? self::detectDeviceType($request),
        ]);
    }

    /**
     * Log Login
     */
    public static function logLogin($user, string $platform = 'web'): self
    {
        return self::log('login', "User {$user->name} berhasil login", [
            'user_id' => $user->user_id,
            'karyawan_id' => $user->karyawan?->karyawan_id,
            'user_name' => $user->name,
            'module' => 'User',
            'module_id' => $user->user_id,
            'platform' => $platform,
        ]);
    }

    /**
     * Log Logout
     */
    public static function logLogout($user, string $platform = 'web'): self
    {
        return self::log('logout', "User {$user->name} logout", [
            'user_id' => $user->user_id,
            'karyawan_id' => $user->karyawan?->karyawan_id,
            'user_name' => $user->name,
            'module' => 'User',
            'module_id' => $user->user_id,
            'platform' => $platform,
        ]);
    }

    /**
     * Log Login Gagal
     */
    public static function logLoginFailed(string $email, string $reason = 'Invalid credentials'): self
    {
        return self::log('login_failed', "Login gagal untuk email: {$email}", [
            'module' => 'User',
            'error_message' => $reason,
            'new_data' => ['email' => $email],
        ]);
    }

    /**
     * Log Create
     */
    public static function logCreate(Model $model, string $description = null): self
    {
        $moduleName = class_basename($model);
        $moduleId = $model->getKey();

        return self::log('create', $description ?? "Membuat {$moduleName} baru", [
            'module' => $moduleName,
            'module_id' => $moduleId,
            'new_data' => $model->toArray(),
        ]);
    }

    /**
     * Log Update
     */
    public static function logUpdate(Model $model, array $oldData, string $description = null): self
    {
        $moduleName = class_basename($model);
        $moduleId = $model->getKey();
        $newData = $model->toArray();

        // Hitung field yang berubah
        $changedFields = self::getChangedFields($oldData, $newData);

        return self::log('update', $description ?? "Mengupdate {$moduleName}", [
            'module' => $moduleName,
            'module_id' => $moduleId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'changed_fields' => $changedFields,
        ]);
    }

    /**
     * Log Delete
     */
    public static function logDelete(Model $model, string $description = null): self
    {
        $moduleName = class_basename($model);
        $moduleId = $model->getKey();

        return self::log('delete', $description ?? "Menghapus {$moduleName}", [
            'module' => $moduleName,
            'module_id' => $moduleId,
            'old_data' => $model->toArray(),
        ]);
    }

    /**
     * Log Approve
     */
    public static function logApprove(Model $model, string $description = null): self
    {
        $moduleName = class_basename($model);
        $moduleId = $model->getKey();

        return self::log('approve', $description ?? "Menyetujui {$moduleName}", [
            'module' => $moduleName,
            'module_id' => $moduleId,
            'new_data' => $model->toArray(),
        ]);
    }

    /**
     * Log Reject
     */
    public static function logReject(Model $model, string $reason = null, string $description = null): self
    {
        $moduleName = class_basename($model);
        $moduleId = $model->getKey();

        return self::log('reject', $description ?? "Menolak {$moduleName}", [
            'module' => $moduleName,
            'module_id' => $moduleId,
            'new_data' => $model->toArray(),
            'error_message' => $reason,
        ]);
    }

    /**
     * Log Submit
     */
    public static function logSubmit(Model $model, string $description = null): self
    {
        $moduleName = class_basename($model);
        $moduleId = $model->getKey();

        return self::log('submit', $description ?? "Submit {$moduleName}", [
            'module' => $moduleName,
            'module_id' => $moduleId,
            'new_data' => $model->toArray(),
        ]);
    }

    /**
     * Log Error
     */
    public static function logError(\Throwable $exception, string $description = null, array $context = []): self
    {
        return self::log('error', $description ?? 'Error: ' . $exception->getMessage(), [
            'module' => $context['module'] ?? null,
            'module_id' => $context['module_id'] ?? null,
            'error_message' => $exception->getMessage(),
            'error_trace' => $exception->getTraceAsString(),
            'new_data' => $context,
        ]);
    }

    /**
     * Log Export
     */
    public static function logExport(string $module, string $description = null, array $filters = []): self
    {
        return self::log('export', $description ?? "Export data {$module}", [
            'module' => $module,
            'new_data' => ['filters' => $filters],
        ]);
    }

    /**
     * Log Import
     */
    public static function logImport(string $module, int $totalRows, int $successRows, string $description = null): self
    {
        return self::log('import', $description ?? "Import data {$module}", [
            'module' => $module,
            'new_data' => [
                'total_rows' => $totalRows,
                'success_rows' => $successRows,
                'failed_rows' => $totalRows - $successRows,
            ],
        ]);
    }

    // ========================================
    // PRIVATE HELPERS
    // ========================================

    /**
     * Get changed fields between old and new data
     */
    private static function getChangedFields(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;

            // Skip timestamp fields
            if (in_array($key, ['created_at', 'updated_at'])) {
                continue;
            }

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Detect platform from request
     */
    private static function detectPlatform($request): string
    {
        // Cek apakah dari API route
        if ($request->is('api/*')) {
            // Cek header custom dari mobile app
            $platform = $request->header('X-Platform');
            if ($platform === 'mobile') {
                return 'mobile';
            }
            return 'api';
        }

        return 'web';
    }

    /**
     * Detect device type from user agent
     */
    private static function detectDeviceType($request): ?string
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        if (str_contains($userAgent, 'android')) {
            return 'android';
        }

        if (str_contains($userAgent, 'iphone') || str_contains($userAgent, 'ipad')) {
            return 'ios';
        }

        if (str_contains($userAgent, 'mobile')) {
            return 'mobile';
        }

        return 'desktop';
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeErrors($query)
    {
        return $query->where('action', 'error');
    }

    public function scopeLogins($query)
    {
        return $query->whereIn('action', ['login', 'logout', 'login_failed']);
    }

    public function scopeDataChanges($query)
    {
        return $query->whereIn('action', ['create', 'update', 'delete']);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
