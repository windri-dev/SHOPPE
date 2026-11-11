<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('products')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(12)->withQueryString();

        $stats = [
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'active_categories' => Category::has('products')->count(),
            'empty_categories' => Category::doesntHave('products')->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $baseSlug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $category = Category::create([
            'name' => trim($validated['name']),
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Kategori '{$category->name}' (Slug: {$category->slug}) berhasil ditambahkan ke etalase.");
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $category->update([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Perubahan data kategori '{$category->name}' berhasil disimpan.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $productsCount = $category->products()->count();

        if ($productsCount > 0) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih menampung {$productsCount} produk busana. Silakan ubah kategori produk-produk tersebut terlebih dahulu.");
        }

        $name = $category->name;
        $category->delete();

        return back()->with('success', "Kategori '{$name}' berhasil dihapus dari sistem.");
    }
}
