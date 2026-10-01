<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CatalogService;
use App\Services\CurrentStore;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->configureFileZillaPublicStorage();
    }

    public function boot(): void
    {
        View::composer('layouts.shop', function ($view) {
            $data = $view->getData();

            if (! array_key_exists('settings', $data)) {
                $view->with('settings', app(SettingService::class)->get());
            }

            if (! array_key_exists('catalog', $data)) {
                $view->with('catalog', app(CatalogService::class)->frontendCatalog());
            }

            if (! array_key_exists('footerCategories', $data)) {
                $view->with(
                    'footerCategories',
                    Category::query()
                        ->where('is_active', true)
                        ->where('slug', '!=', 'specials')
                        ->whereHas('products', fn ($query) => $query->where('status', 'active'))
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get()
                );
            }
        });

        View::composer('layouts.admin', function ($view) {
            $current = app(CurrentStore::class);
            $view->with('currentStore', $current->admin());
            $view->with('canSwitchStore', $current->canSwitch());
            $view->with('adminStores', $current->canSwitch()
                ? \App\Models\Store::query()->orderBy('name')->get()
                : collect());
        });
    }

    /**
     * Hostinger FileZilla layout:
     *   public_html/index.php + assets/ + storage/
     *   public_html/richierich/  ← Laravel app
     */
    private function configureFileZillaPublicStorage(): void
    {
        $webRoot = dirname(base_path());
        $siblingIndex = $webRoot.DIRECTORY_SEPARATOR.'index.php';
        $siblingAssets = $webRoot.DIRECTORY_SEPARATOR.'assets';

        $isFileZillaLayout = is_file($siblingIndex) && is_dir($siblingAssets);
        $forced = (bool) env('FORCE_WEB_STORAGE', false);

        if (! $isFileZillaLayout && ! $forced) {
            return;
        }

        $this->app->usePublicPath($webRoot);

        $webStorage = $webRoot.DIRECTORY_SEPARATOR.'storage';
        if (! is_dir($webStorage)) {
            @mkdir($webStorage, 0755, true);
        }

        foreach (['products', 'banners', 'categories'] as $dir) {
            $path = $webStorage.DIRECTORY_SEPARATOR.$dir;
            if (! is_dir($path)) {
                @mkdir($path, 0755, true);
            }
        }

        config([
            'filesystems.disks.public.root' => $webStorage,
            'filesystems.disks.public.url' => rtrim((string) env('APP_URL', 'http://localhost'), '/').'/storage',
        ]);
    }
}
