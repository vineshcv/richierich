<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(private CategoryService $categories)
    {
    }

    public function list(array $filters = [])
    {
        return Product::query()
            ->with(['category', 'images'])
            ->when(! empty($filters['category_id']), fn ($q) => $q->where('category_id', $filters['category_id']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['q']), function ($q) use ($filters) {
                $term = $filters['q'];
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', '%'.$term.'%')
                        ->orWhere('sku', 'like', '%'.$term.'%')
                        ->orWhere('designer', 'like', '%'.$term.'%')
                        ->orWhere('admin_note', 'like', '%'.$term.'%');
                });
            })
            ->latest()
            ->paginate($filters['per_page'] ?? 20);
    }

    public function create(array $data, array $images = []): Product
    {
        $images = $images ?: [];

        return DB::transaction(function () use ($data, $images) {
            $data = $this->resolveCategory($data);
            $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));
            $data['status'] = $data['status'] ?? 'active';
            $data['show_price'] = $data['show_price'] ?? true;

            $product = Product::create(collect($data)->except([
                'images', 'category_name', 'remove_image_ids', 'primary_image_id', 'replace_images',
            ])->all());
            $this->storeImages($product, $images);

            return $product->load(['category', 'images']);
        });
    }

    public function update(Product $product, array $data, array $images = []): Product
    {
        $images = array_values(array_filter($images ?: [], fn ($f) => $f instanceof UploadedFile));
        $removeIds = array_map('intval', $data['remove_image_ids'] ?? []);
        $primaryId = isset($data['primary_image_id']) ? (int) $data['primary_image_id'] : null;
        $replaceAll = filter_var($data['replace_images'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return DB::transaction(function () use ($product, $data, $images, $removeIds, $primaryId, $replaceAll) {
            $data = $this->resolveCategory($data);
            if (isset($data['name'])) {
                $data['slug'] = Str::slug($data['name']).'-'.$product->id;
            }

            $product->update(collect($data)->except([
                'images', 'category_name', 'remove_image_ids', 'primary_image_id', 'replace_images',
            ])->all());

            if ($replaceAll) {
                foreach ($product->images()->get() as $image) {
                    $this->deleteImageFile($image);
                }
            } elseif ($removeIds) {
                $toDelete = $product->images()->whereIn('id', $removeIds)->get();
                foreach ($toDelete as $image) {
                    $this->deleteImageFile($image);
                }
            }

            if ($images) {
                $remaining = $product->images()->count();
                if ($remaining + count($images) > 10) {
                    throw ValidationException::withMessages([
                        'images' => ['A product can have at most 10 images.'],
                    ]);
                }
                $this->storeImages($product, $images);
            }

            $product->refresh()->load('images');

            if ($primaryId && $product->images->contains('id', $primaryId)) {
                $this->setPrimary($product, $primaryId);
            } elseif ($product->images->isNotEmpty() && ! $product->images->contains('is_primary', true)) {
                $this->setPrimary($product, (int) $product->images->first()->id);
            }

            return $product->fresh()->load(['category', 'images']);
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $image) {
                $this->deleteImageFile($image);
            }
            $product->delete();
        });
    }

    public function setPrimary(Product $product, int $imageId): void
    {
        $product->images()->update(['is_primary' => false]);
        $product->images()->where('id', $imageId)->update(['is_primary' => true]);
    }

    private function deleteImageFile(ProductImage $image): void
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }

    private function resolveCategory(array $data): array
    {
        if (! empty($data['category_name']) && empty($data['category_id'])) {
            $category = $this->categories->findOrCreateByName($data['category_name']);
            $data['category_id'] = $category->id;
        }
        unset($data['category_name']);

        return $data;
    }

    /** @param array<int, UploadedFile> $images */
    private function storeImages(Product $product, array $images): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $sort = (int) $product->images()->max('sort_order');

        foreach ($images as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'sort_order' => ++$sort,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
            if (! $hasPrimary && $index === 0) {
                $hasPrimary = true;
            }
        }
    }
}
