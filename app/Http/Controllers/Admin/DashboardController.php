<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Services\BannerService;
use App\Services\CategoryService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(CategoryService $categories): View
    {
        return view('admin.dashboard', [
            'productCount' => Product::count(),
            'bannerCount' => Banner::count(),
            'categoryCount' => $categories->search()->count(),
            'bannerMax' => BannerService::MAX_BANNERS,
        ]);
    }
}
