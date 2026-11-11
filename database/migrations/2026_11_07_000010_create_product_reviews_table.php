<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('size_purchased')->nullable();
            $table->string('headline')->nullable();
            $table->text('comment');
            $table->string('quality_tag')->nullable();
            $table->string('fit_tag')->nullable();
            $table->json('images')->nullable();
            $table->text('seller_reply')->nullable();
            $table->timestamp('seller_replied_at')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_approved']);
            $table->index(['product_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
