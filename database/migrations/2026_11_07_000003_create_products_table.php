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
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->integer('stock')->default(10);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->integer('sales_count')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['price', 'sales_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
