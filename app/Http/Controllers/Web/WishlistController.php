<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Tampilkan halaman daftar keinginan (Wishlist) pengguna atau tamu.
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user) {
            Wishlist::syncGuestSessionToDatabase($user->id);

            $products = $user->wishlistProducts()
                ->with('category')
                ->latest('wishlists.created_at')
                ->paginate(12);
        } else {
            $productIds = session('wishlist', []);
            $products = Product::whereIn('id', $productIds)
                ->with('category')
                ->latest('id')
                ->paginate(12);
        }

        return view('pages.wishlist', compact('products'));
    }

    /**
     * Toggle produk masuk atau keluar dari daftar keinginan (AJAX).
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $productId = (int) $validated['product_id'];
        $product = Product::findOrFail($productId);
        $user = auth()->user();

        if ($user) {
            $existing = Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                $existing->delete();
                $inWishlist = false;
                $message = __('Produk telah dihapus dari daftar keinginan.');
            } else {
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
                $inWishlist = true;
                $message = __('Produk telah ditambahkan ke daftar keinginan.');
            }

            $count = Wishlist::where('user_id', $user->id)->count();
        } else {
            $wishlist = session('wishlist', []);

            if (in_array($productId, $wishlist, true)) {
                $wishlist = array_values(array_filter($wishlist, fn ($id) => $id !== $productId));
                $inWishlist = false;
                $message = __('Produk telah dihapus dari daftar keinginan.');
            } else {
                $wishlist[] = $productId;
                $inWishlist = true;
                $message = __('Produk telah ditambahkan ke daftar keinginan.');
            }

            session(['wishlist' => $wishlist]);
            $count = count($wishlist);
        }

        return response()->json([
            'success' => true,
            'product_id' => $productId,
            'product_name' => $product->name,
            'in_wishlist' => $inWishlist,
            'count' => $count,
            'message' => $message,
        ]);
    }

    /**
     * Hapus satu produk dari halaman wishlist.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {
            Wishlist::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->delete();
        } else {
            $wishlist = session('wishlist', []);
            $wishlist = array_values(array_filter($wishlist, fn ($id) => $id !== $product->id));
            session(['wishlist' => $wishlist]);
        }

        return back()->with('success', __('Produk berhasil dihapus dari daftar keinginan Anda.'));
    }

    /**
     * Kembalikan jumlah item di wishlist saat ini (JSON).
     */
    public function count(): JsonResponse
    {
        $user = auth()->user();
        $count = $user
            ? Wishlist::where('user_id', $user->id)->count()
            : count(session('wishlist', []));

        return response()->json([
            'count' => $count,
        ]);
    }
}
