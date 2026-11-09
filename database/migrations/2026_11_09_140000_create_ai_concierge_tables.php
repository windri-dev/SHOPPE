<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table): void {
            $table->string('handling_mode', 20)->default('ai')->after('channel');
        });

        Schema::create('ai_knowledge_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 30)->default('knowledge'); // knowledge, boundary, response_rule
            $table->string('title', 150);
            $table->string('keywords', 255)->nullable();
            $table->text('content');
            $table->boolean('requires_human_handoff')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_entries');

        Schema::table('inquiries', function (Blueprint $table): void {
            $table->dropColumn('handling_mode');
        });
    }
};

