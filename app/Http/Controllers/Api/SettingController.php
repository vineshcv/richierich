<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(private SettingService $settings)
    {
    }

    public function show(): JsonResponse
    {
        return response()->json(['data' => $this->settings->get()]);
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        return response()->json([
            'data' => $this->settings->update($request->validated()),
        ]);
    }
}
