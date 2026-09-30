<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('show_share')->default(true);
            $table->boolean('show_whatsapp')->default(true);
            $table->boolean('show_cart')->default(true);
            $table->boolean('show_view')->default(true);
            $table->string('whatsapp_number')->nullable();
            $table->string('store_name')->default('Richie Rich');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
