<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// Public read
Route::get('categories', [CategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::get('banners', [BannerController::class, 'index']);
Route::get('settings', [SettingController::class, 'show']);

// Admin JWT
Route::middleware(['auth:api', 'admin'])->group(function () {
    Route::post('categories', [CategoryController::class, 'store']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::post('products/{product}', [ProductController::class, 'update']); // multipart
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    Route::post('banners', [BannerController::class, 'store']);
    Route::delete('banners/{banner}', [BannerController::class, 'destroy']);

    Route::put('settings', [SettingController::class, 'update'])->middleware('superadmin');
});
