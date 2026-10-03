<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Order;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\CurrentStore;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(CategoryService $categories, CurrentStore $current): View
    {
        $storeId = $current->adminId();

        return view('admin.dashboard', [
            'productCount' => Product::query()->when($storeId, fn ($q) => $q->where('store_id', $storeId))->count(),
            'bannerCount' => Banner::query()->when($storeId, fn ($q) => $q->where('store_id', $storeId))->count(),
            'categoryCount' => $categories->search()->count(),
            'orderCount' => Order::query()->when($storeId, fn ($q) => $q->where('store_id', $storeId))->count(),
        ]);
    }
}
