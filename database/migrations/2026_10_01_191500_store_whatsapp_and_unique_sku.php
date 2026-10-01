<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('stores', 'whatsapp_number')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->string('whatsapp_number', 20)->nullable()->after('phone');
            });
        }

        DB::table('stores')->orderBy('id')->get()->each(function ($store) {
            $current = preg_replace('/\D+/', '', (string) ($store->whatsapp_number ?? '')) ?? '';
            if ($current !== '') {
                return;
            }
            $fromPhone = preg_replace('/\D+/', '', (string) ($store->phone ?? '')) ?? '';
            if ($fromPhone === '') {
                return;
            }
            DB::table('stores')->where('id', $store->id)->update([
                'whatsapp_number' => $fromPhone,
            ]);
        });

        DB::table('stores')->whereIn('name', ['Richie Rich', 'Richie Riche'])->update([
            'name' => 'Richierich',
        ]);

        if (Schema::hasTable('settings')) {
            DB::table('settings')->update(['store_name' => 'Richierich']);
        }

        DB::table('products')->where('sku', '')->update(['sku' => null]);

        $indexes = collect(DB::select('SHOW INDEX FROM products WHERE Column_name = ?', ['sku']))
            ->pluck('Key_name')
            ->unique();

        Schema::table('products', function (Blueprint $table) use ($indexes) {
            if ($indexes->contains('products_store_id_sku_unique')) {
                $table->dropUnique('products_store_id_sku_unique');
            }
        });

        $still = collect(DB::select('SHOW INDEX FROM products WHERE Key_name = ?', ['products_sku_unique']));
        if ($still->isEmpty()) {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('sku');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->unique(['store_id', 'sku']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }
};
