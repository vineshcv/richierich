<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable()->after('razorpay_payment_id');
            $table->string('email')->nullable()->after('customer_name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->json('shipping_address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['customer_name', 'email', 'phone', 'shipping_address']);
        });
    }
};
