<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\BannerService;
use App\Services\CatalogService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(
        private BannerService $banners,
        private CatalogService $catalog,
        private SettingService $settings,
    ) {
    }

    public function home(): View
    {
        return view('home.index', [
            'banners' => $this->banners->list(true),
            'settings' => $this->settings->get(),
            'catalog' => $this->catalog->frontendCatalog(),
        ]);
    }

    public function products(): View
    {
        return view('shop.products', $this->pageData());
    }

    public function cart(): View
    {
        return view('shop.cart', $this->pageData());
    }

    public function contact(): View
    {
        return view('shop.contact', $this->pageData());
    }

    public function combos(): View
    {
        return view('shop.combos', $this->pageData());
    }

    public function season(): View
    {
        return view('shop.season', $this->pageData());
    }

    public function bulk(): View
    {
        return view('shop.bulk', $this->pageData());
    }

    public function show(string $slug): View|RedirectResponse
    {
        $product = Product::query()
            ->with(['category', 'images'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (! $product) {
            return redirect()->route('shop.products');
        }

        return view('shop.show', array_merge($this->pageData(), [
            'product' => $product,
        ]));
    }

    private function pageData(): array
    {
        return [
            'settings' => $this->settings->get(),
            'catalog' => $this->catalog->frontendCatalog(),
        ];
    }
}
