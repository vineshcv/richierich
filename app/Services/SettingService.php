<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function get(): Setting
    {
        return Setting::query()->first() ?? Setting::create([
            'show_share' => true,
            'show_whatsapp' => true,
            'show_cart' => true,
            'show_view' => true,
            'whatsapp_number' => null,
            'store_name' => 'Richierich',
        ]);
    }

    public function update(array $data): Setting
    {
        $setting = $this->get();
        $setting->update($data);

        return $setting->fresh();
    }
}
