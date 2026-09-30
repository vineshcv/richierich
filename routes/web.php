<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Web\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/shop', [ShopController::class, 'products'])->name('shop.products');
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/contact', [ShopController::class, 'contact'])->name('shop.contact');
Route::get('/combos', [ShopController::class, 'combos'])->name('shop.combos');
Route::get('/season', [ShopController::class, 'season'])->name('shop.season');
Route::get('/bulk', [ShopController::class, 'bulk'])->name('shop.bulk');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::get('banners', [AdminBannerController::class, 'index'])->name('banners.index');
        Route::post('banners', [AdminBannerController::class, 'store'])->name('banners.store');
        Route::put('banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
        Route::delete('banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');
        Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
