<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustProductStockRequest;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\CurrencyService;
use App\Services\ProductTranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    public function __construct(
        protected ProductTranslationService $translationService
    ) {}

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

        $exchangeRate = app(CurrencyService::class)->getUsdToIdrRate();

        return view('admin.products.index', compact('products', 'categories', 'stats', 'exchangeRate'));
    }

    /**
     * Menyimpan produk busana/pakaian baru ke database.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $rate = app(CurrencyService::class)->getUsdToIdrRate();

        $price = (float) $validated['price'];
        if ($price >= 1000) {
            $price = round($price / $rate, 2);
        }

        $originalPrice = isset($validated['original_price']) && $validated['original_price'] !== null && $validated['original_price'] !== ''
            ? (float) $validated['original_price']
            : null;
        if ($originalPrice !== null && $originalPrice >= 1000) {
            $originalPrice = round($originalPrice / $rate, 2);
        }

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

        $images = [];

        // 1. Upload file gambar utama ke storage server
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $images[] = $request->file('image')->store('products', 'public');
        }

        // 2. Upload multiple gambar galeri ke storage server jika ada
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $images[] = $file->store('products', 'public');
                }
            }
        }

        // 3. Fallback jika tidak mengupload file (menggunakan URL gambar atau default butik)
        if (empty($images)) {
            if (! empty($validated['image_url'])) {
                $images = [$validated['image_url']];
            } else {
                $images = ['images/product-1.png'];
            }
        }

        $nameEn = ! empty($validated['name_en']) ? trim($validated['name_en']) : null;
        $descriptionEn = ! empty($validated['description_en']) ? trim($validated['description_en']) : null;

        // Auto-Translate via AI jika nama atau deskripsi bahasa Inggris belum diisi
        if (empty($nameEn) || empty($descriptionEn)) {
            $translated = $this->translationService->translateToEnglish(
                $validated['name'],
                $validated['description'] ?? 'Koleksi busana eksklusif PUTRIJALVA dengan material premium pilihan.'
            );
            $nameEn = $nameEn ?: $translated['name_en'];
            $descriptionEn = $descriptionEn ?: $translated['description_en'];
        }

        $stock = (int) $validated['stock'];
        $sizeStocks = $validated['size_stocks'] ?? null;
        if (! is_array($sizeStocks)) {
            $q = (int) floor($stock / 4);
            $rem = $stock % 4;
            $sizeStocks = [
                'S' => $q + ($rem > 0 ? 1 : 0),
                'M' => $q + ($rem > 1 ? 1 : 0),
                'L' => $q + ($rem > 2 ? 1 : 0),
                'XL' => $q,
            ];
        }

        // Upload video ke storage server atau simpan URL eksternal
        $videoUrl = null;
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoUrl = $request->file('video')->store('products/videos', 'public');
        } elseif (! empty($validated['video_url'])) {
            $videoUrl = trim($validated['video_url']);
        }

        $product = Product::create([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'name_en' => $nameEn,
            'slug' => $slug,
            'sku' => $sku,
            'price' => $price,
            'original_price' => $originalPrice,
            'stock' => $stock,
            'size_stocks' => $sizeStocks,
            'badge' => $badge,
            'description' => $validated['description'] ?? 'Koleksi busana eksklusif PUTRIJALVA dengan material premium pilihan.',
            'description_en' => $descriptionEn,
            'images' => $images,
            'video_url' => $videoUrl,
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

        $images = is_array($product->images) && ! empty($product->images) ? $product->images : ['images/product-1.png'];

        // 1. Upload file gambar baru jika ada
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $newPath = $request->file('image')->store('products', 'public');
            // Bersihkan file lama jika tersimpan di disk public storage (bukan asset default)
            if (! empty($images[0]) && ! str_starts_with($images[0], 'images/') && Storage::disk('public')->exists($images[0])) {
                Storage::disk('public')->delete($images[0]);
            }
            $images[0] = $newPath;
        }

        // 2. Upload multiple gambar galeri jika ada
        if ($request->hasFile('images')) {
            $uploadedList = [];
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $uploadedList[] = $file->store('products', 'public');
                }
            }
            if (! empty($uploadedList)) {
                $images = $uploadedList;
            }
        } elseif (! empty($validated['image_url'])) {
            $images[0] = $validated['image_url'];
        }

        $sizeStocks = $validated['size_stocks'] ?? null;
        if (is_array($sizeStocks)) {
            $stock = array_sum(array_map('intval', $sizeStocks));
        } elseif ($stock !== $product->stock) {
            $q = (int) floor($stock / 4);
            $rem = $stock % 4;
            $sizeStocks = [
                'S' => $q + ($rem > 0 ? 1 : 0),
                'M' => $q + ($rem > 1 ? 1 : 0),
                'L' => $q + ($rem > 2 ? 1 : 0),
                'XL' => $q,
            ];
        } else {
            $sizeStocks = $product->size_stocks;
        }

        $nameEn = ! empty($validated['name_en']) ? trim($validated['name_en']) : null;
        $descriptionEn = ! empty($validated['description_en']) ? trim($validated['description_en']) : null;

        // Auto-Translate via AI jika kolom bahasa Inggris dikosongkan
        if (empty($nameEn) || empty($descriptionEn)) {
            $translated = $this->translationService->translateToEnglish(
                $validated['name'],
                $validated['description'] ?? $product->getRawDescription() ?? ''
            );
            $nameEn = $nameEn ?: $translated['name_en'];
            $descriptionEn = $descriptionEn ?: $translated['description_en'];
        }

        $rate = app(CurrencyService::class)->getUsdToIdrRate();
        $price = (float) $validated['price'];
        if ($price >= 1000) {
            $price = round($price / $rate, 2);
        }

        $originalPrice = isset($validated['original_price']) && $validated['original_price'] !== null && $validated['original_price'] !== ''
            ? (float) $validated['original_price']
            : null;
        if ($originalPrice !== null && $originalPrice >= 1000) {
            $originalPrice = round($originalPrice / $rate, 2);
        }

        // Upload video baru ke storage server, tautkan URL baru, atau hapus video jika diminta
        $videoUrl = $product->video_url;
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            if ($product->video_url && ! str_starts_with($product->video_url, 'http://') && ! str_starts_with($product->video_url, 'https://')) {
                $oldVideoPath = preg_replace('#^storage/#', '', $product->video_url);
                if (Storage::disk('public')->exists($oldVideoPath)) {
                    Storage::disk('public')->delete($oldVideoPath);
                }
            }
            $videoUrl = $request->file('video')->store('products/videos', 'public');
        } elseif ($request->filled('video_url')) {
            $videoUrl = trim($request->input('video_url'));
        } elseif ($request->boolean('remove_video') || ($request->has('video_url') && empty($validated['video_url']) && ! $request->hasFile('video'))) {
            if ($product->video_url && ! str_starts_with($product->video_url, 'http://') && ! str_starts_with($product->video_url, 'https://')) {
                $oldVideoPath = preg_replace('#^storage/#', '', $product->video_url);
                if (Storage::disk('public')->exists($oldVideoPath)) {
                    Storage::disk('public')->delete($oldVideoPath);
                }
            }
            $videoUrl = null;
        }

        $product->update([
            'name' => $validated['name'],
            'name_en' => $nameEn,
            'category_id' => (int) $validated['category_id'],
            'sku' => strtoupper(trim($validated['sku'])),
            'price' => $price,
            'original_price' => $originalPrice,
            'stock' => $stock,
            'size_stocks' => $sizeStocks,
            'badge' => $badge,
            'description' => $validated['description'] ?? $product->getRawDescription(),
            'description_en' => $descriptionEn,
            'images' => $images,
            'video_url' => $videoUrl,
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

    /**
     * AJAX endpoint: Auto-Translate nama & deskripsi produk dari ID ke EN menggunakan AI.
     */
    public function autoTranslate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65000'],
        ]);

        $translated = $this->translationService->translateToEnglish(
            $validated['name'],
            $validated['description'] ?? ''
        );

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Terjemahan Bahasa Inggris berhasil di-generate oleh AI.',
            'name_en' => $translated['name_en'] ?? '',
            'description_en' => $translated['description_en'] ?? '',
            'data' => $translated,
        ]);
    }
}
