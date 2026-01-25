<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiErrorLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // Log jika response adalah error (status >= 500)
            if ($response->getStatusCode() >= 500) {
                try {
                    $this->logApiError($request, $response);
                } catch (\Throwable $logError) {
                    // Jangan block response jika logging gagal
                    Log::warning('Failed to log API error: ' . $logError->getMessage());
                }
            }

            return $response;
        } catch (\Throwable $e) {
            // Log exception yang tidak ter-handle
            try {
                $this->logException($request, $e);
            } catch (\Throwable $logError) {
                Log::warning('Failed to log API exception: ' . $logError->getMessage());
            }

            // Re-throw untuk ditangani oleh exception handler
            throw $e;
        }
    }

    /**
     * Log API error response
     */
    protected function logApiError(Request $request, Response $response): void
    {
        try {
            $content = json_decode($response->getContent(), true);
            $message = $content['message'] ?? 'API Error';

            ActivityLog::log('error', $message, [
                'module' => $this->detectModule($request),
                'error_message' => $message,
                'new_data' => [
                    'status_code' => $response->getStatusCode(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'input' => $this->sanitizeInput($request->all()),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API error: ' . $e->getMessage());
        }
    }

    /**
     * Log unhandled exception
     */
    protected function logException(Request $request, \Throwable $exception): void
    {
        try {
            ActivityLog::logError($exception, 'Unhandled API Exception', [
                'module' => $this->detectModule($request),
                'new_data' => [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'input' => $this->sanitizeInput($request->all()),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API exception: ' . $e->getMessage());
        }
    }

    /**
     * Detect module from request path
     */
    protected function detectModule(Request $request): string
    {
        $path = $request->path();

        // Extract module name from API path
        // api/lembur/xxx -> Lembur
        // api/ijin/xxx -> Ijin
        // api/absen/xxx -> Absen

        $segments = explode('/', $path);

        if (count($segments) >= 2 && $segments[0] === 'api') {
            return ucfirst($segments[1]); // lembur -> Lembur
        }

        return 'API';
    }

    /**
     * Remove sensitive data from input before logging
     */
    protected function sanitizeInput(array $input): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'current_password', 'new_password', 'token', 'device_token'];

        foreach ($sensitiveKeys as $key) {
            if (isset($input[$key])) {
                $input[$key] = '***REDACTED***';
            }
        }

        // Remove file uploads from log (too large)
        foreach ($input as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $input[$key] = '[FILE: ' . $value->getClientOriginalName() . ']';
            }
        }

        return $input;
    }
}
