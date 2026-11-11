<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider')->comment('Provider: midtrans, xendit, tripay, simulated');
            $table->string('transaction_id')->comment('Transaction ID resmi dari payment gateway');
            $table->string('payment_channel')->nullable()->comment('Kanal: qris, bca_va, mandiri_va, gopay, credit_card');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending')->comment('pending, settlement, capture, deny, expire, cancel, refund');
            $table->string('signature')->nullable()->comment('Signature hash dari webhook untuk audit');
            $table->json('payload_response')->nullable()->comment('Raw JSON response / webhook payload');
            $table->timestamps();

            $table->index(['provider', 'transaction_id']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
