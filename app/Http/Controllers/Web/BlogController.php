<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::latest()->paginate(6);
        return view('pages.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $relatedPosts = BlogPost::where('id', '!=', $post->id)->take(3)->get();

        return view('pages.blog.show', compact('post', 'relatedPosts'));
    }
}
