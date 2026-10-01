<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class CurrentStore
{
    private bool $resolved = false;

    private ?Store $adminStore = null;

    public function admin(): ?Store
    {
        if ($this->resolved) {
            return $this->adminStore;
        }

        $this->resolved = true;
        $user = Auth::user();

        if ($user && $user->role === 'store_admin') {
            return $this->adminStore = $user->store_id
                ? Store::query()->find($user->store_id)
                : null;
        }

        if ($user && $user->store_id) {
            return $this->adminStore = Store::query()->find($user->store_id);
        }

        $selected = session('admin_store_id');
        $store = $selected ? Store::query()->find($selected) : null;
        if (! $store) {
            $store = $this->website();
            if ($store) {
                session(['admin_store_id' => $store->id]);
            }
        }

        return $this->adminStore = $store;
    }

    public function adminId(): ?int
    {
        return $this->admin()?->id;
    }

    public function canSwitch(): bool
    {
        $user = Auth::user();

        return $user && $user->role === 'superadmin';
    }

    public function website(): ?Store
    {
        return Store::query()->where('is_default', true)->orderBy('id')->first()
            ?? Store::query()->orderBy('id')->first();
    }

    public function websiteId(): ?int
    {
        return $this->website()?->id;
    }

    public function owns(object $model): bool
    {
        $storeId = $this->adminId();

        return $storeId !== null && (int) ($model->store_id ?? 0) === $storeId;
    }
}
