<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustProductStockRequest;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->latest();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'instock') {
                $query->where('stock', '>', 3);
            } elseif ($request->stock_status === 'lowstock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 3);
            } elseif ($request->stock_status === 'outofstock') {
                $query->where('stock', '<=', 0);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        $stats = [
            'total' => Product::count(),
            'instock' => Product::where('stock', '>', 3)->count(),
            'lowstock' => Product::where('stock', '>', 0)->where('stock', '<=', 3)->count(),
            'outofstock' => Product::where('stock', '<=', 0)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Menyimpan produk perhiasan baru ke database.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $sku = ! empty($validated['sku'])
            ? strtoupper(trim($validated['sku']))
            : 'SKU-'.strtoupper(Str::random(7));

        $badge = $validated['badge'] ?? null;
        if ((int) $validated['stock'] <= 0 && empty($badge)) {
            $badge = 'Sold out';
        }

        $images = ! empty($validated['image_url'])
            ? [$validated['image_url']]
            : ['images/product-1.png'];

        $product = Product::create([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'sku' => $sku,
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'stock' => (int) $validated['stock'],
            'badge' => $badge,
            'description' => $validated['description'] ?? 'Koleksi perhiasan eksklusif PUTRI.J dengan bahan premium.',
            'images' => $images,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => true,
            'sales_count' => 0,
        ]);

        return back()->with('success', "Produk '{$product->name}' (SKU: {$product->sku}) berhasil ditambahkan dengan stok {$product->stock} pcs.");
    }

    /**
     * Memperbarui detail produk & harga.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $stock = (int) $validated['stock'];
        $badge = $validated['badge'] ?? null;

        if ($stock <= 0 && empty($badge)) {
            $badge = 'Sold out';
        } elseif ($stock > 0 && $badge === 'Sold out') {
            $badge = null;
        }

        $images = $product->images;
        if (! empty($validated['image_url'])) {
            $images = [$validated['image_url']];
        }

        $product->update([
            'name' => $validated['name'],
            'category_id' => (int) $validated['category_id'],
            'sku' => strtoupper(trim($validated['sku'])),
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'stock' => $stock,
            'badge' => $badge,
            'description' => $validated['description'] ?? $product->description,
            'images' => $images,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $product->is_active,
        ]);

        return back()->with('success', "Perubahan data produk '{$product->name}' berhasil disimpan.");
    }

    /**
     * Menambah, mengurangi, atau mengatur stok produk secara spesifik.
     */
    public function adjustStock(AdjustProductStockRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $qty = (int) $validated['quantity'];
        $oldStock = $product->stock;
        $type = $validated['adjustment_type'];

        $newStock = match ($type) {
            'add' => $oldStock + $qty,
            'subtract' => max(0, $oldStock - $qty),
            'set' => max(0, $qty),
        };

        $badge = $product->badge;
        if ($newStock <= 0) {
            $badge = 'Sold out';
        } elseif ($oldStock <= 0 && $newStock > 0 && $badge === 'Sold out') {
            $badge = null;
        }

        $product->update([
            'stock' => $newStock,
            'badge' => $badge,
        ]);

        $deltaText = match ($type) {
            'add' => "+{$qty} pcs",
            'subtract' => "-{$qty} pcs",
            'set' => "diatur ke {$qty} pcs",
        };

        $reasonText = ! empty($validated['reason']) ? " (Alasan: {$validated['reason']})" : '';

        return back()->with('success', "Stok '{$product->name}' berhasil diubah {$deltaText}{$reasonText}. Stok terkini: {$newStock} pcs.");
    }

    /**
     * Menghapus atau menonaktifkan produk perhiasan.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;

        if ($product->orderItems()->exists()) {
            $product->update([
                'is_active' => false,
                'stock' => 0,
                'badge' => 'Archived',
            ]);

            return back()->with('info', "Produk '{$name}' telah dinonaktifkan (diarsipkan) dan stok di-set 0 karena memiliki riwayat transaksi pelanggan.");
        }

        $product->delete();

        return back()->with('success', "Produk '{$name}' berhasil dihapus permanen dari sistem.");
    }

    /**
     * Tombol cepat toggle ketersediaan barang (0 <-> 15).
     */
    public function toggleStock(Product $product): RedirectResponse
    {
        $newStock = $product->stock > 0 ? 0 : 15;
        $badge = $newStock === 0 ? 'Sold out' : null;

        $product->update([
            'stock' => $newStock,
            'badge' => $badge,
        ]);

        return back()->with('success', "Status stok {$product->name} berhasil diperbarui ({$newStock} pcs).");
    }
}
