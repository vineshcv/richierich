<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@richierich.test'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'status' => 'active',
            ]
        );

        Setting::query()->firstOrCreate([], [
            'show_share' => true,
            'show_whatsapp' => true,
            'show_cart' => true,
            'show_view' => true,
            'whatsapp_number' => '919567779354',
            'store_name' => 'Richie Rich',
        ]);

        $this->call(DressCatalogSeeder::class);
    }
}
