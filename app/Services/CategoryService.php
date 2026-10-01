<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function search(?string $q = null, bool $activeOnly = false)
    {
        return Category::query()
            ->when($activeOnly, fn ($q) => $q->where('is_active', true))
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', '%'.$q.'%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findOrCreateByName(string $name, array $extra = []): Category
    {
        $name = trim($name);
        $existing = Category::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first();
        if ($existing) {
            return $existing;
        }

        return $this->create(array_merge(['name' => $name], $extra));
    }

    public function create(array $data, ?UploadedFile $image = null): Category
    {
        if ($image) {
            $data['image_path'] = $image->store('categories', 'public');
        }
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['store_id'] = $data['store_id'] ?? app(CurrentStore::class)->adminId();

        return Category::create($data);
    }

    public function update(Category $category, array $data, ?UploadedFile $image = null): Category
    {
        if ($image) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $image->store('categories', 'public');
        }
        // Keep existing slug unless explicitly provided (storefront uses dress ids like antitarnish).
        if (! array_key_exists('slug', $data)) {
            unset($data['slug']);
        }
        $category->update($data);

        return $category->fresh();
    }

    public function delete(Category $category): void
    {
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }
        $category->delete();
    }
}
