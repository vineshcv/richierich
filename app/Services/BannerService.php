<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    public function list(bool $activeOnly = false)
    {
        $storeId = request()->is('admin', 'admin/*')
            ? app(CurrentStore::class)->adminId()
            : null;

        return Banner::query()
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->when($activeOnly, fn ($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data, UploadedFile $image): Banner
    {
        $data['image_path'] = $image->store('banners', 'public');
        $data['store_id'] = app(CurrentStore::class)->adminId();
        $data['sort_order'] = $data['sort_order'] ?? ((int) Banner::query()->where('store_id', $data['store_id'])->max('sort_order') + 1);
        $data['is_active'] = $data['is_active'] ?? true;

        return Banner::create($data);
    }

    public function update(Banner $banner, array $data, ?UploadedFile $image = null): Banner
    {
        if ($image) {
            Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = $image->store('banners', 'public');
        }
        $banner->update($data);

        return $banner->fresh();
    }

    public function delete(Banner $banner): void
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
    }
}
