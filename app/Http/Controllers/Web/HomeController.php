<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(6)
            ->get();

        $latestProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(6)
            ->get();

        $latestPosts = BlogPost::latest()->take(3)->get();

        return view('pages.home', compact('featuredProducts', 'latestProducts', 'latestPosts'));
    }
}
