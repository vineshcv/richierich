<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 40)->nullable()->after('status');
            $table->json('payment_details')->nullable()->after('payment_method');
            $table->string('fulfillment_status', 40)->nullable()->after('payment_details');
            $table->string('tracking_number', 80)->nullable()->after('fulfillment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_details', 'fulfillment_status', 'tracking_number']);
        });
    }
};
