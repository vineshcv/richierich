<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json($this->auth->attempt($request->validated()));
    }

    public function me(): JsonResponse
    {
        return response()->json(['user' => $this->auth->me()]);
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return response()->json(['message' => 'Logged out']);
    }

    public function refresh(): JsonResponse
    {
        return response()->json($this->auth->refresh());
    }
}
