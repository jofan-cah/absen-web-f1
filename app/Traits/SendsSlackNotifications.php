<?php

namespace App\Traits;

use App\Services\SlackNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait SendsSlackNotifications
{
    protected $slackService;
    protected $shouldNotifySuccess = false; // Default: hanya error yang dikirim
    protected $shouldNotifyError = true;

    /**
     * Initialize Slack service
     */
    protected function initSlackService()
    {
        if (!$this->slackService) {
            $this->slackService = app(SlackNotificationService::class);
        }
    }

    /**
     * Success response dengan Slack notification
     */
    protected function successResponseWithSlack($data = null, $message = 'Success', $code = 200, $notifySlack = null)
    {
        $this->initSlackService();

        // Kirim ke Slack kalau enabled
        $shouldNotify = $notifySlack ?? $this->shouldNotifySuccess;

        if ($shouldNotify) {
            $this->slackService->notifySuccess($message, [
                'endpoint' => request()->path(),
                'method' => request()->method(),
                'user' => auth()->user()->name ?? 'Guest',
                'ip' => request()->ip(),
                'data' => $this->sanitizeData($data),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response dengan Slack notification
     */
    protected function errorResponseWithSlack($message = 'Error', $code = 500, $error = null, $notifySlack = null)
    {
        $this->initSlackService();

        // Kirim ke Slack kalau enabled
        $shouldNotify = $notifySlack ?? $this->shouldNotifyError;

        if ($shouldNotify) {
            $this->slackService->notifyError("API Error: {$message}", $error, [
                'endpoint' => request()->path(),
                'method' => request()->method(),
                'user' => auth()->user()->name ?? 'Guest',
                'ip' => request()->ip(),
                'status_code' => $code,
                'request_data' => $this->sanitizeData(request()->all()),
            ]);
        }

        // Log error
        if ($error) {
            Log::error("API Error: {$message}", [
                'error' => $error instanceof \Exception ? $error->getMessage() : $error,
                'endpoint' => request()->path(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? ($error instanceof \Exception ? $error->getMessage() : $error) : null,
        ], $code);
    }

    /**
     * Sanitize data (remove sensitive info)
     */
    protected function sanitizeData($data)
    {
        if (!$data) {
            return null;
        }

        if (is_array($data)) {
            // Remove sensitive keys
            $sensitive = ['password', 'token', 'secret', 'api_key', 'fcm_token'];
            foreach ($sensitive as $key) {
                if (isset($data[$key])) {
                    $data[$key] = '***HIDDEN***';
                }
            }
        }

        // Limit data size (max 500 chars)
        $json = json_encode($data);
        if (strlen($json) > 500) {
            return substr($json, 0, 500) . '... (truncated)';
        }

        return $data;
    }

    /**
     * Enable success notification untuk action tertentu
     */
    protected function enableSuccessNotification()
    {
        $this->shouldNotifySuccess = true;
        return $this;
    }

    /**
     * Disable error notification untuk action tertentu
     */
    protected function disableErrorNotification()
    {
        $this->shouldNotifyError = false;
        return $this;
    }
}
