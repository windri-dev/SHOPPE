<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BannerAdminController extends Controller
{
    public function index(Request $request): View
    {
        $banners = Banner::ordered()->get();

        $stats = [
            'total' => $banners->count(),
            'active' => $banners->where('is_active', true)->count(),
            'inactive' => $banners->where('is_active', false)->count(),
        ];

        return view('admin.banners.index', compact('banners', 'stats'));
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = 'images/hero-1.png';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'banner_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('banners', $filename, 'public');
            $imagePath = 'storage/' . $path;
        } elseif (! empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        Banner::create([
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'subtitle_en' => $validated['subtitle_en'] ?? null,
            'button_text' => ! empty($validated['button_text']) ? $validated['button_text'] : 'Lihat Koleksi',
            'button_text_en' => ! empty($validated['button_text_en']) ? $validated['button_text_en'] : 'Shop Collection',
            'button_link' => ! empty($validated['button_link']) ? $validated['button_link'] : '/shop',
            'image' => $imagePath,
            'badge_text' => $validated['badge_text'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Banner '{$validated['title']}' berhasil ditambahkan ke slider utama.");
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validated();

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'banner_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('banners', $filename, 'public');
            $imagePath = 'storage/' . $path;
        } elseif (! empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        $banner->update([
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'subtitle_en' => $validated['subtitle_en'] ?? null,
            'button_text' => ! empty($validated['button_text']) ? $validated['button_text'] : 'Lihat Koleksi',
            'button_text_en' => ! empty($validated['button_text_en']) ? $validated['button_text_en'] : 'Shop Collection',
            'button_link' => ! empty($validated['button_link']) ? $validated['button_link'] : '/shop',
            'image' => $imagePath,
            'badge_text' => $validated['badge_text'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $banner->is_active,
        ]);

        return back()->with('success', "Banner '{$banner->title}' berhasil diperbarui.");
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $title = $banner->title;
        $banner->delete();

        return back()->with('success', "Banner '{$title}' berhasil dihapus dari slider.");
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update([
            'is_active' => ! $banner->is_active,
        ]);

        $statusText = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status banner '{$banner->title}' berhasil {$statusText}.");
    }
}
