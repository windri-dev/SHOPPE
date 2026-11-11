<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->string('category')->default('Fashion');
            $table->text('excerpt')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content');
            $table->longText('content_en')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->default('PUTRI.J Editorial');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
