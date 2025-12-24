<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $service
    ) {}

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $payload = $request->validated();

        return response()->json(
            $this->service->login($payload['email'], $payload['password'])
        );
    }

    public function logout(): JsonResponse
    {
        $this->service->logout(request()->user());

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}
