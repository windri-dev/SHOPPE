<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Memproses pesanan secara atomik dengan DB transaction dan row locking.
     */
    public function processCheckout(array $data, array $cartItems, ?int $userId = null): Order
    {
        return DB::transaction(function () use ($data, $cartItems, $userId) {
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($cartItems as $item) {
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                if (! $product) {
                    throw new Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                }

                $qty = max(1, (int) ($item['quantity'] ?? 1));

                if ($product->stock < $qty) {
                    throw new Exception("Maaf, stok busana '{$product->name}' tidak mencukupi (tersisa {$product->stock}).");
                }

                $product->decrement('stock', $qty);
                $product->increment('sales_count', $qty);

                $lineTotal = $product->price * $qty;
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
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

            return $order;
        });
    }
}
