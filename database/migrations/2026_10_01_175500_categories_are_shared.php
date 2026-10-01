<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $indexNames = fn () => collect(DB::select('SHOW INDEX FROM categories'))->pluck('Key_name');

        if (! $indexNames()->contains('categories_store_id_index')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index('store_id');
            });
        }

        $names = $indexNames();
        Schema::table('categories', function (Blueprint $table) use ($names) {
            if ($names->contains('categories_store_id_name_unique')) {
                $table->dropUnique(['store_id', 'name']);
            }
            if ($names->contains('categories_store_id_slug_unique')) {
                $table->dropUnique(['store_id', 'slug']);
            }
        });

        $groups = DB::table('categories')->orderBy('id')->get()->groupBy(function ($row) {
            return mb_strtolower(trim((string) $row->name));
        });

        foreach ($groups as $rows) {
            if ($rows->count() < 2) {
                continue;
            }
            $keep = $rows->first();
            $dropIds = $rows->skip(1)->pluck('id')->all();
            DB::table('products')->whereIn('category_id', $dropIds)->update(['category_id' => $keep->id]);
            DB::table('categories')->whereIn('id', $dropIds)->delete();
        }

        $used = [];
        foreach (DB::table('categories')->orderBy('id')->get() as $row) {
            $slug = $row->slug ?: Str::slug($row->name);
            $base = $slug !== '' ? $slug : 'category';
            $candidate = $base;
            $n = 2;
            while (isset($used[$candidate])) {
                $candidate = $base.'-'.$n;
                $n++;
            }
            $used[$candidate] = true;
            if ($candidate !== $row->slug) {
                DB::table('categories')->where('id', $row->id)->update(['slug' => $candidate]);
            }
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->unique('name');
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
            $table->unique(['store_id', 'name']);
            $table->unique(['store_id', 'slug']);
        });
    }
};
