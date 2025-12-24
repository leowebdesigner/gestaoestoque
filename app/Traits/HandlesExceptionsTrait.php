<?php

namespace App\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Illuminate\Support\Facades\Log;

trait HandlesExceptionsTrait
{
    use ApiResponseTrait;

    protected function handleException(Request $request, Throwable $exception): JsonResponse
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
            $exception instanceof HttpExceptionInterface =>
                $this->error($exception->getMessage() ?: 'HTTP error.', $exception->getStatusCode()),
            default =>
                $this->error(
                    app()->environment('production') ? 'Unexpected error.' : $exception->getMessage(),
                    500
                ),
        };
    }
}
