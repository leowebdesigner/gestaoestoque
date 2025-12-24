<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait HandlesExceptionsTrait
{
    use ApiResponseTrait;

    public function handleException(Request $request, Throwable $exception): JsonResponse
    {
        Log::error('API exception', [
            'message' => $exception->getMessage(),
            'type' => get_class($exception),
        ]);

        return match (true) {
            $exception instanceof ValidationException =>
                $this->error('Validation error.', 422, $exception->errors()),
            $exception instanceof AuthenticationException =>
                $this->error('Unauthorized.', 401),
            $exception instanceof AuthorizationException =>
                $this->error('Forbidden.', 403),
            $exception instanceof ModelNotFoundException =>
                $this->error('Resource not found.', 404),
            $exception instanceof HttpExceptionInterface => $this->error(
                $exception->getStatusCode() === 404
                    ? 'Resource not found.'
                    : ($exception->getMessage() ?: 'HTTP error.'),
                $exception->getStatusCode()
            ),
            default =>
                $this->error(
                    app()->environment('production') ? 'Unexpected error.' : $exception->getMessage(),
                    500
                ),
        };
    }
}
