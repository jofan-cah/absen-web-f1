<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BaseApiController extends Controller
{
    /**
     * Default success message
     */
    protected $defaultSuccessMessage = 'Operation successful';

    /**
     * Default error message
     */
    protected $defaultErrorMessage = 'Operation failed';

    /**
     * Default items per page for pagination
     */
    protected $defaultPerPage = 15;

    /**
     * Maximum items per page
     */
    protected $maxPerPage = 100;

    /**
     * Return a success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message ?? $this->defaultSuccessMessage,
        ];

        // Only add data key if data is provided
        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @param mixed $data
     * @return JsonResponse
     */
    protected function errorResponse(string $message = null, int $statusCode = 400, $errors = null, $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message ?? $this->defaultErrorMessage,
        ];

        // Add errors if provided
        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        // Add data if provided (useful for some error cases)
        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated JSON response
     *
     * @param LengthAwarePaginator $paginator
     * @param string $message
     * @param array $meta
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = null, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message ?? $this->defaultSuccessMessage,
            'data' => $paginator->items(),
            'meta' => array_merge([
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ], $meta)
        ];

        return response()->json($response);
    }

    /**
     * Return a collection response with manual pagination info
     *
     * @param Collection $collection
     * @param int $currentPage
     * @param int $perPage
     * @param int $total
     * @param string $message
     * @param array $meta
     * @return JsonResponse
     */
    protected function collectionResponse(Collection $collection, int $currentPage = 1, int $perPage = null, int $total = null, string $message = null, array $meta = []): JsonResponse
    {
        $perPage = $perPage ?? $this->defaultPerPage;
        $total = $total ?? $collection->count();
        $lastPage = (int) ceil($total / $perPage);

        $response = [
            'success' => true,
            'message' => $message ?? $this->defaultSuccessMessage,
            'data' => $collection->values(),
            'meta' => array_merge([
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'has_more_pages' => $currentPage < $lastPage,
            ], $meta)
        ];

        return response()->json($response);
    }

    /**
     * Return a not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Return an unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized access'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Return a forbidden response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Access forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Return a validation error response
     *
     * @param mixed $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Return a server error response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * Return a created response (for successful POST requests)
     *
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    protected function createdResponse($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Return a no content response (for successful DELETE requests)
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function noContentResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    /**
     * Get validated per page value from request
     *
     * @param Request $request
     * @return int
     */
    protected function getPerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', $this->defaultPerPage);

        // Ensure per_page is within acceptable range
        return min(max($perPage, 1), $this->maxPerPage);
    }

    /**
     * Get current page from request
     *
     * @param Request $request
     * @return int
     */
    protected function getCurrentPage(Request $request): int
    {
        return max((int) $request->get('page', 1), 1);
    }

    /**
     * Format API response for exceptions
     *
     * @param \Throwable $exception
     * @param bool $debug
     * @return JsonResponse
     */
    protected function exceptionResponse(\Throwable $exception, bool $debug = false): JsonResponse
    {
        $message = 'An error occurred';
        $statusCode = 500;
        $errors = null;

        // Handle specific exception types
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $message = 'Validation failed';
            $statusCode = 422;
            $errors = $exception->errors();
        } elseif ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $message = 'Resource not found';
            $statusCode = 404;
        } elseif ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $message = 'Unauthenticated';
            $statusCode = 401;
        } elseif ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            $message = 'Unauthorized';
            $statusCode = 403;
        }

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        // Add debug information in development environment
        if ($debug && app()->environment(['local', 'testing'])) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Transform data using a transformer/resource class
     *
     * @param mixed $data
     * @param string $transformer
     * @return mixed
     */
    protected function transformData($data, string $transformer = null)
    {
        if (!$transformer) {
            return $data;
        }

        if (class_exists($transformer)) {
            return new $transformer($data);
        }

        return $data;
    }

    /**
     * Add timing information to response (for debugging)
     *
     * @param array $response
     * @param float $startTime
     * @return array
     */
    protected function addTimingInfo(array $response, float $startTime = null): array
    {
        if ($startTime && app()->environment(['local', 'testing'])) {
            $response['debug']['execution_time'] = round((microtime(true) - $startTime) * 1000, 2) . 'ms';
        }

        return $response;
    }

    /**
     * Create a response with custom structure
     *
     * @param array $structure
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function customResponse(array $structure, int $statusCode = 200): JsonResponse
    {
        return response()->json($structure, $statusCode);
    }

    /**
     * Response for empty results
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function emptyResponse(string $message = 'No data found'): JsonResponse
    {
        return $this->successResponse([], $message);
    }

    /**
     * Response with additional metadata
     *
     * @param mixed $data
     * @param array $meta
     * @param string $message
     * @return JsonResponse
     */
    protected function responseWithMeta($data, array $meta, string $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message ?? $this->defaultSuccessMessage,
            'data' => $data,
            'meta' => $meta
        ];

        return response()->json($response);
    }
}
