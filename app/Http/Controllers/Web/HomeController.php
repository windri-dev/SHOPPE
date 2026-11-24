<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroBanners = Banner::active()->ordered()->get();
        $sliderDesktopHeight = (int) StoreSetting::get('slider_desktop_height', '580');
        $sliderMobileHeight = (int) StoreSetting::get('slider_mobile_height', '400');

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

        return view('pages.home', compact('heroBanners', 'sliderDesktopHeight', 'sliderMobileHeight', 'featuredProducts', 'latestProducts', 'latestPosts'));
    }
}
