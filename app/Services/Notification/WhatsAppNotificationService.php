<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Order;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Format nomor HP ke format internasional WhatsApp (e.g. 0812 -> 62812).
     */
    public static function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62'.$cleaned;
        }

        return $cleaned;
    }

    /**
     * Kirim notifikasi konfirmasi pesanan baru ke WhatsApp pembeli.
     */
    public function sendOrderNotification(Order $order): bool
    {
        $targetPhone = self::formatPhone($order->customer_phone);
        if (empty($targetPhone)) {
            Log::info("WhatsAppNotification: Pembeli pesanan {$order->order_number} tidak memiliki nomor telepon.");

            return false;
        }

        $formattedTotal = CurrencyService::formatPrice($order->grand_total);
        $orderUrl = route('order.show', $order->order_number);
        $conciergePhone = config('services.whatsapp.concierge_phone', '+62 812-3456-7890');
        $street = $order->shipping_address['street'] ?? $order->shipping_address['street_address'] ?? '-';
        $city = $order->shipping_address['city'] ?? '';
        $postcode = $order->shipping_address['postcode'] ?? '';
        $addressSummary = trim("{$street}, {$city} {$postcode}");

        $message = "✨ *PUTRIJALVA BOUTIQUE - PESANAN DITERIMA* ✨\n\n".
            "Halo *{$order->customer_name}*,\n".
            "Terima kasih telah berbelanja di PUTRIJALVA Boutique.\n\n".
            "📋 *Nomor Pesanan:* `{$order->order_number}`\n".
            "💰 *Total Pembayaran:* {$formattedTotal}\n".
            '📦 *Metode Pembayaran:* '.strtoupper(str_replace('_', ' ', $order->payment_method))."\n".
            "📍 *Alamat Pengiriman:* {$addressSummary}\n\n".
            "Silakan periksa detail pesanan dan instruksi pembayaran di tautan resmi kami:\n".
            "👉 {$orderUrl}\n\n".
            "📞 *Layanan Bantuan / Customer Concierge:* {$conciergePhone}\n".
            'Tim kami siap melayani Anda jika membutuhkan bantuan atau informasi tambahan.';

        return $this->dispatchMessage($targetPhone, $message);
    }

    /**
     * Kirim notifikasi pesanan telah dikirim (lengkap dengan nomor resi) ke WhatsApp pembeli.
     */
    public function sendShippingNotification(Order $order): bool
    {
        $targetPhone = self::formatPhone($order->customer_phone);
        if (empty($targetPhone)) {
            return false;
        }

        $tracking = $order->tracking_number ?? 'Sedang diproses kurir';
        $orderUrl = route('order.show', $order->order_number);

        $message = "🚚 *PESANAN ANDA TELAH DIKIRIM* 🚚\n\n".
            "Halo *{$order->customer_name}*,\n".
            "Busana istimewa Anda untuk pesanan *{$order->order_number}* telah diserahkan ke jasa kurir pengiriman.\n\n".
            "🏷️ *Nomor Resi / Tracking:* `{$tracking}`\n".
            "📍 *Status Pengiriman:* Dalam Perjalanan (Shipped)\n\n".
            "Pantau posisi paket Anda secara langsung melalui halaman pesanan:\n".
            "👉 {$orderUrl}\n\n".
            'Terima kasih atas kepercayaan Anda memilih PUTRIJALVA Boutique.';

        return $this->dispatchMessage($targetPhone, $message);
    }

    /**
     * Buat URL langsung WhatsApp Concierge untuk konfirmasi pesanan oleh pembeli.
     */
    public function generateOrderConfirmationUrl(Order $order): string
    {
        $conciergePhone = config('services.whatsapp.concierge_phone', '6281234567890');
        $formattedTotal = CurrencyService::formatPrice($order->grand_total);

        $text = "Halo Concierge PUTRIJALVA, saya {$order->customer_name} ingin konfirmasi pesanan #{$order->order_number} (Total: {$formattedTotal}).";

        return 'https://wa.me/'.self::formatPhone($conciergePhone).'?text='.rawurlencode($text);
    }

    /**
     * Buat URL langsung WhatsApp untuk admin menyapa pelanggan terkait pesanan.
     */
    public function generateCustomerChatUrl(Order $order): ?string
    {
        $targetPhone = self::formatPhone($order->customer_phone);
        if (empty($targetPhone)) {
            return null;
        }

        $text = "Halo {$order->customer_name}, ini Tim Customer Concierge PUTRIJALVA terkait pesanan Anda #{$order->order_number}. Ada yang bisa kami bantu?";

        return 'https://wa.me/'.$targetPhone.'?text='.rawurlencode($text);
    }

    /**
     * Dispatch pesan WhatsApp via Driver (log, fonnte, api).
     */
    protected function dispatchMessage(string $targetPhone, string $message): bool
    {
        $driver = config('services.whatsapp.driver', 'log');
        $token = config('services.whatsapp.api_token');
        $endpoint = config('services.whatsapp.api_url', 'https://api.fonnte.com/send');

        if ($driver === 'fonnte' && ! empty($token)) {
            try {
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => $token,
                ])->post($endpoint, [
                    'target' => $targetPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

                if ($response->successful()) {
                    Log::info("WhatsApp notification sent to {$targetPhone} via Fonnte.");

                    return true;
                }

                Log::warning("Fonnte WhatsApp API returned status {$response->status()}: ".$response->body());
            } catch (\Throwable $e) {
                Log::warning("Error calling WhatsApp Gateway API for {$targetPhone}: ".$e->getMessage());
            }
        }

        // Default: Log Driver (safe for local, testing, sandbox, and fallback)
        Log::info("WhatsApp Notification Dispatch [Driver: {$driver}]: Target={$targetPhone} | Message: ".str_replace("\n", ' ', $message));

        return true;
    }
}
