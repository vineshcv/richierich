<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->nullable()->after('price');
            $table->unsignedInteger('stock_threshold')->nullable()->after('stock');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('stock_held')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_held');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'stock_threshold']);
        });
    }
};
