<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CatalogService
{
    /** @var array<string, mixed>|null */
    private ?array $catalogJson = null;

    /**
     * Build dress_data.js–compatible payload for the storefront JS.
     *
     * @return array{categories: list<array>, products: list<array>, combos: list<array>, season: list<array>}
     */
    public function frontendCatalog(): array
    {
        app(StockService::class)->sweepExpired();

        $categories = Category::query()
            ->where('is_active', true)
            ->where('slug', '!=', 'specials')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (Category $c) {
                $id = $this->normalizeCategoryId($c->slug);
                $fallback = $this->catalogImageForCategory($id) ?? $this->catalogImageForCategory($c->slug);

                return [
                    'id' => $id,
                    'name' => $c->name,
                    'desc' => $c->description ?: '',
                    'image' => $this->mediaUrl($c->image_path, $fallback),
                ];
            })
            ->values()
            ->all();

        $products = Product::query()
            ->with(['category', 'images', 'store'])
            ->where('status', 'active')
            ->latest()
            ->get()
            ->map(fn (Product $p) => $this->mapProduct($p))
            ->values()
            ->all();

        $static = $this->staticOffers();

        return [
            'categories' => $categories,
            'products' => $products,
            'combos' => $static['combos'],
            'season' => $static['season'],
        ];
    }

    private function mapProduct(Product $product): array
    {
        $fallback = $this->catalogImageForProduct($product->slug);

        $images = $product->images
            ->map(fn ($img) => $this->mediaUrl($img->path, $fallback))
            ->values()
            ->all();

        $primaryPath = $product->primaryImage()?->path;
        $primary = $this->mediaUrl($primaryPath, $fallback);
        if (! $images) {
            $images = [$primary];
        }

        $tags = is_array($product->tags) ? $product->tags : [];
        $tag = $tags[0] ?? ($product->category?->name ?? '');

        $price = '';
        if ($product->show_price && $product->price !== null) {
            $price = '₹'.number_format((float) $product->price, 0, '.', ',');
        }

        $specs = [];
        if ($product->fabric) {
            $specs[] = ['Fabric', $product->fabric];
        }
        if ($product->fit) {
            $specs[] = ['Fit', $product->fit];
        }
        if ($product->designer) {
            $specs[] = ['Designer', $product->designer];
        }
        if (is_array($product->colors) && $product->colors) {
            $specs[] = ['Colors', implode(', ', $product->colors)];
        }
        if ($product->sku) {
            $specs[] = ['SKU', $product->sku];
        }

        $bullets = array_values(array_filter([
            $product->fabric,
            $product->fit,
            $tag ?: null,
        ]));

        $short = $product->description
            ? \Illuminate\Support\Str::limit(strip_tags($product->description), 110)
            : ($product->category?->name ?? '');

        $categoryId = $this->normalizeCategoryId($product->category?->slug ?? 'western');

        return [
            'id' => $product->slug,
            'name' => $product->name,
            'brand' => 'Richierich',
            'category' => $categoryId,
            'tag' => $tag,
            'price' => $price,
            'image' => $primary,
            'gallery' => $images,
            'short' => $short,
            'description' => $product->description ?: $short,
            'bullets' => $bullets ?: [$short],
            'specs' => $specs ?: [['Type', $product->category?->name ?? 'Dress']],
            'sizes' => [],
            'colors' => is_array($product->colors) ? array_values($product->colors) : [],
            'stock' => $product->stock,
            'stock_threshold' => $product->stock_threshold,
            'whatsapp' => preg_replace('/\D+/', '', (string) ($product->store?->whatsapp_number ?? '')) ?? '',
            'url' => url('/product/'.$product->slug),
            'wa' => $product->name,
            'care' => $product->care_instructions,
        ];
    }

    /**
     * Prefer admin/storage uploads; fall back to public_html/assets seed images.
     */
    private function mediaUrl(?string $path, ?string $fallbackBasename = null): string
    {
        if ($path) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                return $path;
            }

            $relative = ltrim(explode('?', $path)[0], '/');
            if (Storage::disk('public')->exists($relative)) {
                return Storage::disk('public')->url($relative);
            }
        }

        $candidates = [];
        if ($path) {
            $candidates[] = basename(explode('?', $path)[0]);
        }
        if ($fallbackBasename) {
            $candidates[] = basename(explode('?', $fallbackBasename)[0]);
        }

        foreach ($candidates as $base) {
            if ($base && $this->webAssetExists($base)) {
                return asset('assets/'.$base);
            }
        }

        return asset('assets/dress_logo.webp');
    }

    private function webAssetExists(string $base): bool
    {
        $base = basename(explode('?', $base)[0]);

        // Normal Laravel: project/public/assets
        if (is_file(public_path('assets/'.$base))) {
            return true;
        }

        // FileZilla layout: public_html/assets + public_html/richierich (app)
        if (is_file(base_path('../assets/'.$base))) {
            return true;
        }

        return false;
    }

    private function normalizeCategoryId(?string $slug): string
    {
        $slug = strtolower(trim((string) $slug));

        return match ($slug) {
            'anti-tarnish', 'anti_tarnish', 'antitarnishjewellery' => 'antitarnish',
            default => $slug !== '' ? $slug : 'western',
        };
    }

    private function catalogImageForProduct(string $slug): ?string
    {
        foreach ($this->catalog()['products'] ?? [] as $item) {
            if (($item['id'] ?? '') === $slug) {
                $img = $item['image'] ?? ($item['gallery'][0] ?? null);

                return $img ? basename(explode('?', $img)[0]) : null;
            }
        }

        return null;
    }

    private function catalogImageForCategory(string $id): ?string
    {
        foreach ($this->catalog()['categories'] ?? [] as $item) {
            $itemId = $this->normalizeCategoryId($item['id'] ?? '');
            if ($itemId === $this->normalizeCategoryId($id) || ($item['id'] ?? '') === $id) {
                $img = $item['image'] ?? null;

                return $img ? basename(explode('?', $img)[0]) : null;
            }
        }

        return null;
    }

    /**
     * @return array{combos: list<array>, season: list<array>}
     */
    private function staticOffers(): array
    {
        $data = $this->catalog();

        return [
            'combos' => array_map(fn ($item) => $this->mapOffer($item), $data['combos'] ?? []),
            'season' => array_map(fn ($item) => $this->mapOffer($item), $data['season'] ?? []),
        ];
    }

    private function mapOffer(array $item): array
    {
        $image = isset($item['image']) ? basename(explode('?', $item['image'])[0]) : null;

        return array_merge($item, [
            'image' => $this->mediaUrl(null, $image),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function catalog(): array
    {
        if ($this->catalogJson !== null) {
            return $this->catalogJson;
        }

        $path = database_path('seeders/dress_catalog.json');
        if (! File::exists($path)) {
            return $this->catalogJson = [];
        }

        return $this->catalogJson = json_decode(File::get($path), true) ?: [];
    }
}
