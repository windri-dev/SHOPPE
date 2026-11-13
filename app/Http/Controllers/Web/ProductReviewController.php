<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, Product $product): RedirectResponse
    {
        $user = auth()->user();
        $isVerified = false;

        if ($user) {
            $isVerified = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('status', '!=', OrderStatus::CANCELLED->value);
                })
                ->exists();
        }

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'customer_name' => (string) $request->string('customer_name')->trim(),
            'customer_email' => $request->filled('customer_email')
                ? (string) $request->string('customer_email')->trim()
                : $user?->email,
            'rating' => (int) $request->input('rating'),
            'size_purchased' => $request->filled('size_purchased')
                ? (string) $request->string('size_purchased')->trim()
                : null,
            'headline' => $request->filled('headline')
                ? (string) $request->string('headline')->trim()
                : null,
            'quality_tag' => $request->filled('quality_tag')
                ? (string) $request->string('quality_tag')->trim()
                : null,
            'fit_tag' => $request->filled('fit_tag')
                ? (string) $request->string('fit_tag')->trim()
                : null,
            'comment' => (string) $request->string('comment')->trim(),
            'is_verified_purchase' => $isVerified,
            'is_approved' => true,
        ]);

        return redirect()
            ->to(route('shop.show', $product->slug).'#customer-reviews')
            ->with('review_success', 'Terima kasih atas ulasan dan penilaian Anda! Kontribusi Anda sangat berharga bagi komunitas pelanggan PUTRIJALVA.');
    }

    public function helpful(Request $request, ProductReview $review): JsonResponse|RedirectResponse
    {
        $review->increment('helpful_count');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'helpful_count' => $review->helpful_count,
                'message' => 'Terima kasih atas tanggapan Anda!',
            ]);
        }

        return back()->with('review_success', 'Terima kasih atas tanggapan Anda!');
    }
}
