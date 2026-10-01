<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('pin', 12)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        $setting = Schema::hasTable('settings') ? DB::table('settings')->first() : null;
        $storeId = DB::table('stores')->insertGetId([
            'name' => $setting->store_name ?? 'Richie Rich',
            'location' => null,
            'address' => null,
            'phone' => $setting->whatsapp_number ?? null,
            'pin' => null,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        foreach (['categories', 'products', 'banners', 'orders'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('store_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
            DB::table($table)->whereNull('store_id')->update(['store_id' => $storeId]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
            $table->unique(['store_id', 'name']);
            $table->unique(['store_id', 'slug']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropUnique(['sku']);
            $table->unique(['store_id', 'slug']);
            $table->unique(['store_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'slug']);
            $table->dropUnique(['store_id', 'sku']);
            $table->unique('slug');
            $table->unique('sku');
            $table->dropConstrainedForeignId('store_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'name']);
            $table->dropUnique(['store_id', 'slug']);
            $table->unique('name');
            $table->unique('slug');
            $table->dropConstrainedForeignId('store_id');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store_id');
        });

        Schema::dropIfExists('stores');
    }
};
