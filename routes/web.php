<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\RazorpayController;
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
Route::post('/checkout/razorpay', [RazorpayController::class, 'create'])->name('checkout.razorpay');
Route::post('/checkout/razorpay/verify', [RazorpayController::class, 'verify'])->name('checkout.razorpay.verify');
Route::post('/checkout/login', [RazorpayController::class, 'login'])->middleware('throttle:8,1')->name('checkout.login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::post('stores/switch', [AdminStoreController::class, 'switch'])->name('stores.switch');
        Route::middleware('superadmin')->group(function () {
            Route::resource('stores', AdminStoreController::class)->except(['show']);
            Route::resource('users', AdminUserController::class)->except(['show']);
            Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
        });
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/whatsapp', [AdminOrderController::class, 'whatsapp'])->name('orders.whatsapp');

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::get('banners', [AdminBannerController::class, 'index'])->name('banners.index');
        Route::post('banners', [AdminBannerController::class, 'store'])->name('banners.store');
        Route::put('banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
        Route::delete('banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');
    });
});
