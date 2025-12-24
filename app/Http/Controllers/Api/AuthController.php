<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais invalidas.'],
            ]);
        }

        $cacheKey = 'auth:token:user:' . $user->id;
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken) {
            $existingToken = PersonalAccessToken::findToken($cachedToken);

            if ($existingToken && (int) $existingToken->tokenable_id === (int) $user->id) {
                return response()->json([
                    'token' => $cachedToken,
                    'token_type' => 'Bearer',
                ]);
            }
        }

        $token = $user->createToken('postman')->plainTextToken;
        Cache::forever($cacheKey, $token);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}
