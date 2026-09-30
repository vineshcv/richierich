<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('designer')->nullable();
            $table->string('fabric')->nullable();
            $table->string('fit')->nullable();
            $table->json('colors')->nullable();
            $table->json('available_sizes')->nullable();
            $table->boolean('show_price')->default(true);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('sku')->nullable()->unique();
            $table->text('care_instructions')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('active'); // active | draft | archived
            $table->timestamps();

            $table->index(['category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
