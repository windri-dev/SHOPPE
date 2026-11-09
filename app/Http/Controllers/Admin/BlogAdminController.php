<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::latest('published_at');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10)->withQueryString();
        $blogCategories = BlogPost::select('category')->distinct()->pluck('category');

        $stats = [
            'total_posts' => BlogPost::count(),
            'categories_count' => $blogCategories->count(),
        ];

        return view('admin.blogs.index', compact('posts', 'blogCategories', 'stats'));
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $post = BlogPost::create([
            'title' => trim($validated['title']),
            'slug' => $slug,
            'category' => trim($validated['category']),
            'author' => strtoupper(trim($validated['author'])),
            'excerpt' => trim($validated['excerpt']),
            'content' => trim($validated['content']),
            'image' => ! empty($validated['image']) ? $validated['image'] : 'images/blog-1.jpg',
            'published_at' => Carbon::now(),
        ]);

        return back()->with('success', "Artikel jurnal '{$post->title}' berhasil diterbitkan.");
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blog): RedirectResponse
    {
        $validated = $request->validated();

        $blog->update([
            'title' => trim($validated['title']),
            'category' => trim($validated['category']),
            'author' => strtoupper(trim($validated['author'])),
            'excerpt' => trim($validated['excerpt']),
            'content' => trim($validated['content']),
            'image' => ! empty($validated['image']) ? $validated['image'] : $blog->image,
        ]);

        return back()->with('success', "Perubahan artikel '{$blog->title}' berhasil disimpan.");
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $title = $blog->title;
        $blog->delete();

        return back()->with('success', "Artikel '{$title}' berhasil dihapus dari jurnal toko.");
    }
}
