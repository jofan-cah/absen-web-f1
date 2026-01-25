<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\ApiErrorLogger::class,
        ]);

        $middleware->alias([
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            // 'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // =============================================
        // HANDLE ALL API EXCEPTIONS WITH JSON RESPONSE
        // =============================================
        $exceptions->render(function (Throwable $e, Request $request) {
            // Only handle API requests
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null; // Let Laravel handle web errors normally
            }

            $statusCode = 500;
            $message = 'Terjadi kesalahan pada server';
            $errors = null;

            // Handle specific exception types with user-friendly messages
            if ($e instanceof ValidationException) {
                $statusCode = 422;
                $message = 'Data tidak valid';
                $errors = $e->errors();
            } elseif ($e instanceof ModelNotFoundException) {
                $statusCode = 404;
                $message = 'Data tidak ditemukan';
            } elseif ($e instanceof NotFoundHttpException) {
                $statusCode = 404;
                $message = 'Endpoint tidak ditemukan';
            } elseif ($e instanceof MethodNotAllowedHttpException) {
                $statusCode = 405;
                $message = 'Method tidak diizinkan';
            } elseif ($e instanceof AuthenticationException) {
                $statusCode = 401;
                $message = 'Sesi login telah berakhir. Silakan login kembali';
            } elseif ($e instanceof AuthorizationException) {
                $statusCode = 403;
                $message = 'Anda tidak memiliki akses untuk melakukan aksi ini';
            } elseif ($e instanceof QueryException) {
                $statusCode = 500;
                $message = 'Terjadi kesalahan database';
                // Log the actual error for debugging
                \Illuminate\Support\Facades\Log::error('Database Error: ' . $e->getMessage(), [
                    'sql' => $e->getSql() ?? null,
                    'bindings' => $e->getBindings() ?? null,
                ]);
            } elseif ($e instanceof \Exception) {
                // Generic exception - use the message if it's user-friendly
                $exceptionMessage = $e->getMessage();

                // Check if message is user-friendly (not technical)
                if (!empty($exceptionMessage) &&
                    !str_contains($exceptionMessage, 'SQLSTATE') &&
                    !str_contains($exceptionMessage, 'Call to') &&
                    !str_contains($exceptionMessage, 'Undefined') &&
                    strlen($exceptionMessage) < 200) {
                    $message = $exceptionMessage;
                }
            }

            // Log error for debugging (only for 500 errors)
            if ($statusCode >= 500) {
                \Illuminate\Support\Facades\Log::error('API Error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'user_id' => $request->user()?->user_id ?? null,
                ]);

                // Log to ActivityLog jika tersedia
                try {
                    if (class_exists(\App\Models\ActivityLog::class)) {
                        \App\Models\ActivityLog::logError($e, $message, [
                            'url' => $request->fullUrl(),
                            'method' => $request->method(),
                        ]);
                    }
                } catch (\Exception $logException) {
                    // Ignore logging errors
                }
            }

            // Build response
            $response = [
                'success' => false,
                'message' => $message,
            ];

            if ($errors !== null) {
                $response['errors'] = $errors;
            }

            // Add debug info only in local/testing environment
            if (app()->environment(['local', 'testing'])) {
                $response['debug'] = [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }

            return response()->json($response, $statusCode);
        });
    })->create();
