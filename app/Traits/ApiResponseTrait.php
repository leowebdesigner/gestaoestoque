<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function success(array $data = [], string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function created(array $data = [], string $message = 'Created.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function accepted(array $data = [], string $message = 'Accepted.'): JsonResponse
    {
        return $this->success($data, $message, 202);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json([], 204);
    }

    protected function error(string $message, int $status, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
