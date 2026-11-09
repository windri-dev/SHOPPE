<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->json('shipping_address');
            $table->string('payment_method')->default('bank_transfer');
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_gateway')->nullable()->comment('Provider gateway: midtrans, xendit, tripay, simulated, manual');
            $table->string('payment_reference')->nullable()->comment('ID referensi dari gateway (e.g. Snap Token, Invoice ID)');
            $table->string('payment_channel')->nullable()->comment('Kanal pembayaran (e.g. qris, bca_va, credit_card)');
            $table->text('payment_url')->nullable()->comment('URL pembayaran langsung ke gateway jika ada');
            $table->string('payment_proof')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('payment_gateway');
            $table->index('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
