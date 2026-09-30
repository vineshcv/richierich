<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DressCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/dress_catalog.json');
        if (! File::exists($path)) {
            $this->command?->error('dress_catalog.json missing');

            return;
        }

        $data = json_decode(File::get($path), true);
        $business = '/Applications/MAMP/htdocs/business';

        // Categories — keep dress demo ids (antitarnish, etc.)
        $categoryMap = [];
        foreach ($data['categories'] as $i => $cat) {
            $imageRel = null;
            $srcImage = $this->resolveAsset($business, $cat['image'] ?? null);
            if ($srcImage) {
                $imageRel = 'categories/'.basename(explode('?', $srcImage)[0]);
                Storage::disk('public')->put($imageRel, File::get($srcImage));
            }

            $slug = $cat['id'] ?? Str::slug($cat['name']);
            $model = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $cat['name'],
                    'description' => $cat['desc'] ?? null,
                    'image_path' => $imageRel,
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
            $categoryMap[$cat['id']] = $model->id;
        }

        // Extra category for any unknown
        $categoryMap['season'] = Category::query()->firstOrCreate(
            ['slug' => 'specials'],
            ['name' => 'Specials', 'description' => 'Season edits', 'sort_order' => 99, 'is_active' => true]
        )->id;

        // Clear existing dress products/images (keep admin). Safer: upsert by sku from dress id
        foreach ($data['products'] as $item) {
            $this->importProduct($item, $categoryMap, $business);
        }

        // Banners (replace existing seeded banners for dress look)
        Banner::query()->delete();
        foreach (($data['banners'] ?? []) as $i => $banner) {
            $src = $business.'/'.$banner['image'];
            if (! File::exists($src)) {
                continue;
            }
            $rel = 'banners/'.$banner['image'];
            Storage::disk('public')->put($rel, File::get($src));
            Banner::create([
                'title' => $banner['title'] ?? null,
                'image_path' => $rel,
                'link_url' => url($banner['link'] ?? '/shop'),
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        $this->command?->info('Imported '.count($data['products']).' products, '.count($data['categories']).' categories, banners.');
    }

    private function importProduct(array $item, array $categoryMap, string $business): void
    {
        $catKey = $item['category'] ?? 'western';
        $categoryId = $categoryMap[$catKey] ?? ($categoryMap['western'] ?? Category::query()->value('id'));

        $fabric = null;
        $fit = null;
        foreach ($item['specs'] ?? [] as $spec) {
            if (! is_array($spec) || count($spec) < 2) {
                continue;
            }
            if (Str::lower($spec[0]) === 'fabric') {
                $fabric = $spec[1];
            }
            if (in_array(Str::lower($spec[0]), ['length', 'fit', 'neck'], true) && ! $fit) {
                $fit = $spec[0].': '.$spec[1];
            }
        }

        $product = Product::query()->updateOrCreate(
            ['sku' => 'DRESS-'.Str::upper($item['id'])],
            [
                'category_id' => $categoryId,
                'name' => $item['name'],
                'slug' => Str::slug($item['id']),
                'description' => $item['description'] ?? ($item['short'] ?? null),
                'designer' => $item['brand'] ?? 'Richie Rich Boutique',
                'fabric' => $fabric,
                'fit' => $fit,
                'colors' => null,
                'available_sizes' => ['S', 'M', 'L', 'XL'],
                'show_price' => true,
                'price' => $this->parsePrice($item['price'] ?? '0'),
                'care_instructions' => collect($item['specs'] ?? [])->first(fn ($s) => is_array($s) && Str::lower($s[0] ?? '') === 'care')[1] ?? 'Gentle wash. Dry in shade.',
                'tags' => array_values(array_filter([$item['tag'] ?? null])),
                'status' => 'active',
            ]
        );

        // Replace images
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
            $img->delete();
        }

        $gallery = $item['gallery'] ?? [$item['image'] ?? null];
        foreach ($gallery as $i => $imgName) {
            $src = $this->resolveAsset($business, $imgName);
            if (! $src) {
                continue;
            }
            $base = basename(explode('?', $src)[0]);
            $rel = 'products/'.$base;
            Storage::disk('public')->put($rel, File::get($src));
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $rel,
                'sort_order' => $i,
                'is_primary' => $i === 0,
            ]);
        }
    }

    private function parsePrice(string $price): float
    {
        $n = preg_replace('/[^\d.]/', '', $price);

        return $n !== '' ? (float) $n : 0;
    }

    private function resolveAsset(string $business, ?string $name): ?string
    {
        if (! $name) {
            return null;
        }
        $file = basename(explode('?', $name)[0]);
        $candidates = [
            $business.'/'.$file,
            $business.'/'.explode('?', $name)[0],
            public_path('assets/'.$file),
            // FileZilla layout: public_html/assets + app in public_html/richierich
            base_path('../assets/'.$file),
            base_path('public/assets/'.$file),
        ];
        foreach ($candidates as $full) {
            if (File::exists($full)) {
                return $full;
            }
        }

        return null;
    }
}
