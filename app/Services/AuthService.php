<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function login(string $email, string $password): array
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais invalidas.'],
            ]);
        }

        $cacheKey = 'auth:token:user:' . $user->id;
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken) {
            $existingToken = PersonalAccessToken::findToken($cachedToken);

            if ($existingToken && (int) $existingToken->tokenable_id === (int) $user->id) {
                return [
                    'token' => $cachedToken,
                    'token_type' => 'Bearer',
                ];
            }
        }

        $token = $user->createToken('postman')->plainTextToken;
        Cache::forever($cacheKey, $token);

        return [
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(?User $user): void
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }
}
