<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->latest('published_at')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(4)->withQueryString();
        $categories = BlogPost::query()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return view('pages.blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $relatedPosts = BlogPost::where('id', '!=', $post->id)->latest('published_at')->take(3)->get();

        return view('pages.blog.show', compact('post', 'relatedPosts'));
    }
}
