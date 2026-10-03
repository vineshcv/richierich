<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function attempt(array $credentials): array
    {
        if (! $token = Auth::guard('api')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();
        if (! in_array($user->role, ['superadmin', 'store_admin'], true) || $user->status !== 'active') {
            Auth::guard('api')->logout();

            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => $user,
        ];
    }

    public function logout(): void
    {
        Auth::guard('api')->logout();
    }

    public function refresh(): array
    {
        $token = Auth::guard('api')->refresh();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user(),
        ];
    }

    public function me(): ?User
    {
        return Auth::guard('api')->user();
    }
}
