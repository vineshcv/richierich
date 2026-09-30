<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BannerService
{
    public const MAX_BANNERS = 5;

    public function list(bool $activeOnly = false)
    {
        return Banner::query()
            ->when($activeOnly, fn ($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data, UploadedFile $image): Banner
    {
        if (Banner::count() >= self::MAX_BANNERS) {
            throw ValidationException::withMessages([
                'image' => ['Maximum of '.self::MAX_BANNERS.' banners allowed.'],
            ]);
        }

        $data['image_path'] = $image->store('banners', 'public');
        $data['sort_order'] = $data['sort_order'] ?? ((int) Banner::max('sort_order') + 1);
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
