<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $clara = User::where('email', 'buyer@shoppe.com')->first();
        $regina = User::where('email', 'user@example.com')->first();
        $dimas = User::where('email', 'dimas.satria@example.com')->first();
        $amanda = User::where('email', 'amanda.kusuma@example.com')->first();
        $sofia = User::where('email', 'sofia.maharani@example.com')->first();
        $kevin = User::where('email', 'kevin.wijaya@example.com')->first();

        $products = Product::all()->keyBy('sku');

        if ($products->isEmpty()) {
            return;
        }

        $ordersData = [
            // 1. Clara Aurelia - Delivered Order
            [
                'user' => $clara,
                'order_number' => 'SHP-2026-1001',
                'status' => OrderStatus::DELIVERED,
                'payment_method' => 'bank_transfer',
                'tracking_number' => 'JNE-8829103948',
                'notes' => 'Tolong bubble wrap ekstra dan kemasan garment wrap aman.',
                'created_at' => Carbon::now()->subDays(24),
                'shipping_cost' => 5.00,
                'discount_amount' => 5.00,
                'address' => [
                    'first_name' => 'Clara',
                    'last_name' => 'Aurelia',
                    'street' => 'Jl. Senopati No. 42',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12190',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-EAR-001', 'qty' => 1],
                    ['sku' => 'SKU-NEC-002', 'qty' => 1],
                ],
            ],

            // 2. Clara Aurelia - Shipped Order
            [
                'user' => $clara,
                'order_number' => 'SHP-2026-1002',
                'status' => OrderStatus::SHIPPED,
                'payment_method' => 'credit_card',
                'tracking_number' => 'SICEPAT-772910344',
                'notes' => 'Pengiriman harap di jam kantor.',
                'created_at' => Carbon::now()->subDays(6),
                'shipping_cost' => 0.00, // Free shipping
                'discount_amount' => 10.00,
                'address' => [
                    'first_name' => 'Clara',
                    'last_name' => 'Aurelia',
                    'street' => 'Jl. Senopati No. 42',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12190',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-BRC-002', 'qty' => 1],
                    ['sku' => 'SKU-RNG-001', 'qty' => 1],
                ],
            ],

            // 3. Clara Aurelia - Processing Order
            [
                'user' => $clara,
                'order_number' => 'SHP-2026-1003',
                'status' => OrderStatus::PROCESSING,
                'payment_method' => 'qris',
                'tracking_number' => null,
                'notes' => 'Mohon sertakan kartu ucapan selamat ulang tahun.',
                'created_at' => Carbon::now()->subDays(2),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Clara',
                    'last_name' => 'Aurelia',
                    'street' => 'Jl. Senopati No. 42',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12190',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-SET-001', 'qty' => 1],
                ],
            ],

            // 4. Clara Aurelia - Pending Payment Order
            [
                'user' => $clara,
                'order_number' => 'SHP-2026-1004',
                'status' => OrderStatus::PENDING,
                'payment_method' => 'bank_transfer',
                'tracking_number' => null,
                'notes' => null,
                'created_at' => Carbon::now()->subHours(8),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Clara',
                    'last_name' => 'Aurelia',
                    'street' => 'Jl. Senopati No. 42',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12190',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-EAR-002', 'qty' => 2],
                    ['sku' => 'SKU-HAR-001', 'qty' => 1],
                ],
            ],

            // 5. Clara Aurelia - Cancelled Order
            [
                'user' => $clara,
                'order_number' => 'SHP-2026-1005',
                'status' => OrderStatus::CANCELLED,
                'payment_method' => 'bank_transfer',
                'tracking_number' => null,
                'notes' => 'Pembeli membatalkan pesanan karena ingin mengganti varian cincin.',
                'created_at' => Carbon::now()->subDays(18),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Clara',
                    'last_name' => 'Aurelia',
                    'street' => 'Jl. Senopati No. 42',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12190',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-RNG-002', 'qty' => 1],
                ],
            ],

            // 6. Regina Pratama - Delivered Order
            [
                'user' => $regina,
                'order_number' => 'SHP-2026-1006',
                'status' => OrderStatus::DELIVERED,
                'payment_method' => 'bank_transfer',
                'tracking_number' => 'POS-992381204',
                'notes' => null,
                'created_at' => Carbon::now()->subDays(15),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Regina',
                    'last_name' => 'Pratama',
                    'street' => 'Jl. Kemang Raya No. 10',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12730',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-HAR-001', 'qty' => 1],
                    ['sku' => 'SKU-EAR-002', 'qty' => 1],
                ],
            ],

            // 7. Regina Pratama - Shipped Order
            [
                'user' => $regina,
                'order_number' => 'SHP-2026-1007',
                'status' => OrderStatus::SHIPPED,
                'payment_method' => 'credit_card',
                'tracking_number' => 'JNE-4491028371',
                'notes' => 'Tinggalkan di pos satpam bila rumah kosong.',
                'created_at' => Carbon::now()->subDays(3),
                'shipping_cost' => 0.00,
                'discount_amount' => 5.00,
                'address' => [
                    'first_name' => 'Regina',
                    'last_name' => 'Pratama',
                    'street' => 'Jl. Kemang Raya No. 10',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12730',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-WAT-001', 'qty' => 1],
                ],
            ],

            // 8. Dimas Satria - Delivered Order (Bandung)
            [
                'user' => $dimas,
                'order_number' => 'SHP-2026-1008',
                'status' => OrderStatus::DELIVERED,
                'payment_method' => 'qris',
                'tracking_number' => 'JNE-9901827411',
                'notes' => 'Hadiah anniversary untuk istri tercinta.',
                'created_at' => Carbon::now()->subDays(20),
                'shipping_cost' => 8.00,
                'discount_amount' => 10.00,
                'address' => [
                    'first_name' => 'Dimas',
                    'last_name' => 'Satria',
                    'street' => 'Jl. Dago Asri No. 15',
                    'city' => 'Kota Bandung',
                    'province' => 'Jawa Barat',
                    'postal_code' => '40135',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-SET-002', 'qty' => 1],
                ],
            ],

            // 9. Dimas Satria - Processing Order
            [
                'user' => $dimas,
                'order_number' => 'SHP-2026-1009',
                'status' => OrderStatus::PROCESSING,
                'payment_method' => 'bank_transfer',
                'tracking_number' => null,
                'notes' => null,
                'created_at' => Carbon::now()->subDays(1),
                'shipping_cost' => 8.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Dimas',
                    'last_name' => 'Satria',
                    'street' => 'Jl. Dago Asri No. 15',
                    'city' => 'Kota Bandung',
                    'province' => 'Jawa Barat',
                    'postal_code' => '40135',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-BRC-001', 'qty' => 1],
                    ['sku' => 'SKU-PEN-001', 'qty' => 1],
                ],
            ],

            // 10. Amanda Kusuma - Shipped Order (Surabaya)
            [
                'user' => $amanda,
                'order_number' => 'SHP-2026-1010',
                'status' => OrderStatus::SHIPPED,
                'payment_method' => 'credit_card',
                'tracking_number' => 'SICEPAT-991204855',
                'notes' => null,
                'created_at' => Carbon::now()->subDays(4),
                'shipping_cost' => 10.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Amanda',
                    'last_name' => 'Kusuma',
                    'street' => 'Jl. Raya Darmo Permai III No. 8',
                    'city' => 'Surabaya',
                    'province' => 'Jawa Timur',
                    'postal_code' => '60189',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-EAR-003', 'qty' => 1],
                    ['sku' => 'SKU-RNG-001', 'qty' => 1],
                ],
            ],

            // 11. Amanda Kusuma - Delivered Order
            [
                'user' => $amanda,
                'order_number' => 'SHP-2026-1011',
                'status' => OrderStatus::DELIVERED,
                'payment_method' => 'bank_transfer',
                'tracking_number' => 'JNE-6610294812',
                'notes' => null,
                'created_at' => Carbon::now()->subDays(28),
                'shipping_cost' => 10.00,
                'discount_amount' => 5.00,
                'address' => [
                    'first_name' => 'Amanda',
                    'last_name' => 'Kusuma',
                    'street' => 'Jl. Raya Darmo Permai III No. 8',
                    'city' => 'Surabaya',
                    'province' => 'Jawa Timur',
                    'postal_code' => '60189',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-BRO-001', 'qty' => 1],
                ],
            ],

            // 12. Sofia Maharani - Pending Payment (Bali)
            [
                'user' => $sofia,
                'order_number' => 'SHP-2026-1012',
                'status' => OrderStatus::PENDING,
                'payment_method' => 'bank_transfer',
                'tracking_number' => null,
                'notes' => 'Akan transfer via BCA Virtual Account sore ini.',
                'created_at' => Carbon::now()->subHours(5),
                'shipping_cost' => 12.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Sofia',
                    'last_name' => 'Maharani',
                    'street' => 'Jl. Sunset Road No. 108',
                    'city' => 'Seminyak, Kuta',
                    'province' => 'Bali',
                    'postal_code' => '80361',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-ANK-001', 'qty' => 2],
                    ['sku' => 'SKU-ANK-002', 'qty' => 1],
                ],
            ],

            // 13. Sofia Maharani - Delivered Order
            [
                'user' => $sofia,
                'order_number' => 'SHP-2026-1013',
                'status' => OrderStatus::DELIVERED,
                'payment_method' => 'credit_card',
                'tracking_number' => 'POS-339102847',
                'notes' => null,
                'created_at' => Carbon::now()->subDays(12),
                'shipping_cost' => 12.00,
                'discount_amount' => 10.00,
                'address' => [
                    'first_name' => 'Sofia',
                    'last_name' => 'Maharani',
                    'street' => 'Jl. Sunset Road No. 108',
                    'city' => 'Seminyak, Kuta',
                    'province' => 'Bali',
                    'postal_code' => '80361',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-WAT-002', 'qty' => 1],
                ],
            ],

            // 14. Kevin Wijaya - Processing Order (Medan)
            [
                'user' => $kevin,
                'order_number' => 'SHP-2026-1014',
                'status' => OrderStatus::PROCESSING,
                'payment_method' => 'qris',
                'tracking_number' => null,
                'notes' => 'Mohon dipastikan ukuran kalung sesuai 42cm.',
                'created_at' => Carbon::now()->subDays(1),
                'shipping_cost' => 15.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Kevin',
                    'last_name' => 'Wijaya',
                    'street' => 'Jl. Diponegoro No. 24',
                    'city' => 'Kota Medan',
                    'province' => 'Sumatera Utara',
                    'postal_code' => '20152',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-NEC-003', 'qty' => 1],
                    ['sku' => 'SKU-RNG-003', 'qty' => 1],
                ],
            ],

            // 15. Guest Customer 1 - Delivered Order
            [
                'user' => null,
                'order_number' => 'SHP-2026-1015',
                'status' => OrderStatus::DELIVERED,
                'payment_method' => 'credit_card',
                'tracking_number' => 'JNE-1122334455',
                'notes' => null,
                'created_at' => Carbon::now()->subDays(16),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Nadia',
                    'last_name' => 'Lestari',
                    'street' => 'Jl. Cempaka Putih Tengah No. 5',
                    'city' => 'Jakarta Pusat',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '10510',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-EAR-005', 'qty' => 1],
                    ['sku' => 'SKU-HAR-002', 'qty' => 1],
                ],
            ],

            // 16. Guest Customer 2 - Shipped Order
            [
                'user' => null,
                'order_number' => 'SHP-2026-1016',
                'status' => OrderStatus::SHIPPED,
                'payment_method' => 'bank_transfer',
                'tracking_number' => 'SICEPAT-556677889',
                'notes' => null,
                'created_at' => Carbon::now()->subDays(2),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Bambang',
                    'last_name' => 'Irawan',
                    'street' => 'Jl. Bintaro Utama Sektor 3A No. 12',
                    'city' => 'Tangerang Selatan',
                    'province' => 'Banten',
                    'postal_code' => '15224',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-SET-003', 'qty' => 1],
                ],
            ],

            // 17. Guest Customer 3 - Pending Order
            [
                'user' => null,
                'order_number' => 'SHP-2026-1017',
                'status' => OrderStatus::PENDING,
                'payment_method' => 'bank_transfer',
                'tracking_number' => null,
                'notes' => null,
                'created_at' => Carbon::now()->subHours(2),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Tania',
                    'last_name' => 'Hapsari',
                    'street' => 'Jl. Margonda Raya No. 200',
                    'city' => 'Depok',
                    'province' => 'Jawa Barat',
                    'postal_code' => '16424',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-HAR-003', 'qty' => 1],
                ],
            ],

            // 18. Guest Customer 4 - Cancelled Order
            [
                'user' => null,
                'order_number' => 'SHP-2026-1018',
                'status' => OrderStatus::CANCELLED,
                'payment_method' => 'bank_transfer',
                'tracking_number' => null,
                'notes' => 'Batas waktu pembayaran telah kedaluwarsa (Expired).',
                'created_at' => Carbon::now()->subDays(10),
                'shipping_cost' => 5.00,
                'discount_amount' => 0.00,
                'address' => [
                    'first_name' => 'Rian',
                    'last_name' => 'Saputra',
                    'street' => 'Jl. Tebet Barat Dalam No. 18',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12810',
                    'country' => 'Indonesia',
                ],
                'items' => [
                    ['sku' => 'SKU-NEC-004', 'qty' => 1],
                ],
            ],
        ];

        foreach ($ordersData as $data) {
            $user = $data['user'];
            $addr = $data['address'];

            $customerName = $user ? $user->name : "{$addr['first_name']} {$addr['last_name']}";
            $customerEmail = $user ? $user->email : strtolower("{$addr['first_name']}.{$addr['last_name']}@example.com");
            $customerPhone = $user?->phone ?? '+62 812 0000 9999';

            $subtotal = 0.0;
            $itemsToCreate = [];

            foreach ($data['items'] as $itemData) {
                $sku = $itemData['sku'];
                $qty = $itemData['qty'];
                $product = $products->get($sku);

                if (! $product) {
                    continue;
                }

                $unitPrice = (float) $product->price;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->primary_image,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ];
            }

            $shippingCost = (float) $data['shipping_cost'];
            $discountAmount = (float) $data['discount_amount'];
            $grandTotal = max(0.0, ($subtotal + $shippingCost) - $discountAmount);

            $order = Order::updateOrCreate(
                ['order_number' => $data['order_number']],
                [
                    'user_id' => $user?->id,
                    'status' => $data['status'],
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount_amount' => $discountAmount,
                    'grand_total' => $grandTotal,
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'shipping_address' => $data['address'],
                    'payment_method' => $data['payment_method'],
                    'payment_status' => match ($data['status']) {
                        OrderStatus::DELIVERED, OrderStatus::SHIPPED, OrderStatus::PROCESSING => 'paid',
                        OrderStatus::PENDING => ($data['order_number'] === 'SHP-2026-1014' ? 'waiting_verification' : 'unpaid'),
                        OrderStatus::CANCELLED => 'refunded',
                    },
                    'payment_proof' => $data['order_number'] === 'SHP-2026-1014' ? 'uploads/proofs/sample-receipt.jpg' : null,
                    'paid_at' => in_array($data['status'], [OrderStatus::DELIVERED, OrderStatus::SHIPPED, OrderStatus::PROCESSING]) ? $data['created_at'] : null,
                    'tracking_number' => $data['tracking_number'],
                    'notes' => $data['notes'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['created_at'],
                ]
            );

            // Re-sync items
            $order->items()->delete();
            foreach ($itemsToCreate as $item) {
                $order->items()->create($item);
            }
        }
    }
}
