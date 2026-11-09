<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Dapatkan nama unik identifier driver.
     */
    public function getDriverName(): string;

    /**
     * Buat sesi transaksi pembayaran / token dari pesanan.
     *
     * @return array{
     *     success: bool,
     *     reference: string,
     *     token: string|null,
     *     redirect_url: string|null,
     *     client_key: string|null,
     *     instructions?: array<string, mixed>|null,
     *     raw_response?: array<string, mixed>
     * }
     */
    public function createPaymentSession(Order $order): array;

    /**
     * Ekstrak dan parse payload webhook menjadi format terstandarisasi.
     *
     * @return array{
     *     order_number: string,
     *     transaction_id: string,
     *     status: string,
     *     payment_channel: string|null,
     *     amount: float,
     *     signature: string|null,
     *     raw_payload: array<string, mixed>
     * }
     */
    public function parseWebhook(Request $request): array;

    /**
     * Verifikasi keaslian hash / signature webhook untuk mencegah fraud.
     */
    public function verifyWebhookSignature(Request $request): bool;
}
