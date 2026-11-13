<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Notification\WhatsAppNotificationService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutService
{
    /**
     * Memproses pesanan secara atomik dengan DB transaction dan row locking.
     */
    public function processCheckout(array $data, array $cartItems, ?int $userId = null): Order
    {
        $order = DB::transaction(function () use ($data, $cartItems, $userId) {
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($cartItems as $item) {
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                if (! $product) {
                    throw new Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                }

                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $size = strtoupper(trim((string) ($item['size'] ?? $item['variant'] ?? 'M')));
                if (! in_array($size, ['XS', 'S', 'M', 'L', 'XL'], true)) {
                    $size = 'M';
                }

                if (! $product->hasStockForSize($size, $qty)) {
                    $avail = $product->getStockForSize($size);
                    Log::channel('transactions')->warning("Checkout stock insufficient for product [{$product->sku}] size [{$size}]", [
                        'product_id' => $product->id,
                        'sku' => $product->sku,
                        'size' => $size,
                        'requested_quantity' => $qty,
                        'available_stock' => $avail,
                        'user_id' => $userId,
                    ]);

                    throw new Exception("Maaf, stok busana '{$product->name}' untuk ukuran {$size} tidak mencukupi (tersisa {$avail} pcs).");
                }

                $product->decrementSizeStock($size, $qty);
                $product->increment('sales_count', $qty);

                $lineTotal = $product->price * $qty;
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'size' => $size,
                    'product_image' => $product->primary_image,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ];
            }

            $shippingCost = (float) ($data['shipping_cost'] ?? 0);
            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $grandTotal = max(0, $subtotal + $shippingCost - $discountAmount);

            $orderNumber = 'SHP-'.strtoupper(uniqid());
            $fullName = trim(($data['first_name'] ?? '').' '.(($data['last_name'] ?? '') !== '-' ? ($data['last_name'] ?? '') : ''));

            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'status' => OrderStatus::PENDING,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'customer_name' => $fullName,
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'] ?? null,
                'shipping_address' => [
                    'label' => $data['address_label'] ?? 'Rumah',
                    'country' => $data['country'] ?? 'Indonesia',
                    'province' => $data['province'] ?? '',
                    'city' => $data['city'] ?? '',
                    'district' => $data['district'] ?? '',
                    'postcode' => $data['postcode'] ?? '',
                    'street' => $data['street_address'] ?? '',
                    'street_address' => $data['street_address'] ?? '',
                ],
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'notes' => $data['order_notes'] ?? null,
            ]);

            $order->items()->createMany($itemsToCreate);

            // Sinkronisasi otomatis ke profil pengiriman pengguna jika dicentang atau alamat profil masih kosong
            if ($userId) {
                $user = User::find($userId);
                if ($user && (! empty($data['save_to_profile']) || empty($user->address) || empty($user->city))) {
                    $user->update([
                        'phone' => $data['phone'] ?: $user->phone,
                        'country' => $data['country'] ?? ($user->country ?: 'Indonesia'),
                        'province' => $data['province'] ?? $user->province,
                        'city' => $data['city'] ?? $user->city,
                        'district' => $data['district'] ?? $user->district,
                        'postcode' => $data['postcode'] ?? $user->postcode,
                        'address' => $data['street_address'] ?? $user->address,
                        'address_label' => $data['address_label'] ?? ($user->address_label ?: 'Rumah'),
                    ]);
                }
            }

            Log::channel('transactions')->info("Order [{$order->order_number}] placed successfully", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $userId,
                'customer_email' => $order->customer_email,
                'customer_name' => $order->customer_name,
                'grand_total' => $order->grand_total,
                'payment_method' => $order->payment_method,
                'items_count' => count($itemsToCreate),
            ]);

            return $order;
        });

        // Kirim email notifikasi invoice pesanan resmi ke pembeli
        try {
            if (! empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new OrderInvoiceMail($order->load('items')));
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim email konfirmasi pesanan ke {$order->customer_email}: ".$e->getMessage());
        }

        // Kirim notifikasi WhatsApp otomatis ke pembeli
        try {
            app(WhatsAppNotificationService::class)->sendOrderNotification($order);
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim notifikasi WhatsApp ke {$order->customer_phone}: ".$e->getMessage());
        }

        return $order;
    }
}
