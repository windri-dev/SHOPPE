@extends('layouts.storefront')

@section('title', 'PUTRI.J - Modern Jewelry & Accessories')

@section('content')
<!-- Hero Section Carousel -->
<section class="hero-section" aria-label="Hero Carousel">
  <div class="container">
    <div class="hero-slider-container" id="heroSlider">
      <div class="hero-slides">
        <!-- Slide 1 -->
        <div class="hero-slide active">
          <img src="{{ asset('images/hero-1.png') }}" alt="Gold big hoops" class="hero-bg" fetchpriority="high" loading="eager" width="1200" height="600">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <h1 class="hero-title">Gold big hoops</h1>
            <div class="hero-price">$ 68.00</div>
            <a href="{{ route('shop.index') }}" class="btn">View Product</a>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="hero-slide">
          <img src="{{ asset('images/blog-1.jpg') }}" alt="Artisan Gold Vermeil" class="hero-bg" loading="lazy" width="1200" height="600">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <h2 class="hero-title">Artisan Gold Vermeil</h2>
            <div class="hero-price">$ 45.00</div>
            <a href="{{ route('shop.index') }}" class="btn">Shop Collection</a>
          </div>
        </div>

        <!-- Slide 3 -->
        <div class="hero-slide">
          <img src="{{ asset('images/blog-2.jpg') }}" alt="Sculptural Silver & Pearls" class="hero-bg" loading="lazy" width="1200" height="600">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <h2 class="hero-title">Sculptural Silver &amp; Pearls</h2>
            <div class="hero-price">$ 55.00</div>
            <a href="{{ route('shop.index') }}" class="btn">Discover Pieces</a>
          </div>
        </div>
      </div>

      <!-- Prev & Next Arrows -->
      <button type="button" class="slider-arrow prev" aria-label="Previous slide">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <button type="button" class="slider-arrow next" aria-label="Next slide">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>

      <!-- Pagination Dots -->
      <div class="hero-pagination">
        <button type="button" class="slider-dot active" aria-label="Slide 1"></button>
        <button type="button" class="slider-dot" aria-label="Slide 2"></button>
        <button type="button" class="slider-dot" aria-label="Slide 3"></button>
      </div>
    </div>
  </div>
</section>

<!-- Value Props Trust Highlights -->
<section class="trust-features-section" style="padding: 36px 0; background: #FAF9F6; border-bottom: 1px solid #EFEFEF; margin-bottom: 40px;">
  <div class="container">
    <div class="trust-features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; text-align: center;">
      <div class="trust-feature-card" style="padding: 10px;">
        <div class="trust-feature-icon" style="margin-bottom: 10px; display: flex; justify-content: center;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 12L2 9l4-6z"></path><path d="M2 9h20"></path><path d="M10 3l2 6 4-6"></path></svg>
        </div>
        <h3 style="font-size: 14px; font-weight: 700; margin: 0 0 4px; color: #111827;">18K Gold-Dipped Vermeil</h3>
        <p style="font-size: 12.5px; color: #6B7280; margin: 0;">Cast in hypoallergenic brass, 100% nickel-free &amp; lead-free.</p>
      </div>
      <div class="trust-feature-card" style="padding: 10px;">
        <div class="trust-feature-icon" style="margin-bottom: 10px; display: flex; justify-content: center;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
        </div>
        <h3 style="font-size: 14px; font-weight: 700; margin: 0 0 4px; color: #111827;">Complimentary Shipping</h3>
        <p style="font-size: 12.5px; color: #6B7280; margin: 0;">Gratis ongkir untuk pesanan di atas $50 ke seluruh Indonesia.</p>
      </div>
      <div class="trust-feature-card" style="padding: 10px;">
        <div class="trust-feature-icon" style="margin-bottom: 10px; display: flex; justify-content: center;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
        </div>
        <h3 style="font-size: 14px; font-weight: 700; margin: 0 0 4px; color: #111827;">Signature Keepsake Box</h3>
        <p style="font-size: 12.5px; color: #6B7280; margin: 0;">Dikemas elegan dalam velvet jewelry pouch khas PUTRI.J.</p>
      </div>
      <div class="trust-feature-card" style="padding: 10px;">
        <div class="trust-feature-icon" style="margin-bottom: 10px; display: flex; justify-content: center;">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
        </div>
        <h3 style="font-size: 14px; font-weight: 700; margin: 0 0 4px; color: #111827;">14-Day Free Exchange</h3>
        <p style="font-size: 12.5px; color: #6B7280; margin: 0;">Garansi tukar size dan jaminan mutu pengerjaan artisan.</p>
      </div>
    </div>
  </div>
</section>

<!-- Latest Products Section -->
<section class="catalog-section" aria-label="Latest Products" style="margin-bottom: 60px;">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Shop The Latest</h2>
      <a href="{{ route('shop.index') }}" class="section-link">View All</a>
    </div>

    <div class="product-grid" id="productGrid">
      @foreach($latestProducts as $product)
        <x-product-card :product="$product" />
      @endforeach
    </div>
  </div>
</section>

@if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
  <!-- Featured Curated Collection Section -->
  <section class="catalog-section featured-catalog-section" aria-label="Featured Collection" style="margin-bottom: 60px; padding: 48px 0; background: #FAF9F6; border-radius: 8px;">
    <div class="container">
      <div class="section-header">
        <div>
          <h2 class="section-title">Curated Best Sellers</h2>
          <p style="color: #6B7280; font-size: 13.5px; margin: 4px 0 0;">Koleksi mahakarya paling diminati dan direkomendasikan oleh personal stylist kami.</p>
        </div>
        <a href="{{ route('shop.index', ['sort' => 'popular']) }}" class="section-link">Explore Popular</a>
      </div>

      <div class="product-grid">
        @foreach($featuredProducts as $product)
          <x-product-card :product="$product" />
        @endforeach
      </div>
    </div>
  </section>
@endif

@if(isset($latestPosts) && $latestPosts->isNotEmpty())
  <!-- From Our Journal / Blog Section -->
  <section class="catalog-section home-blog-section" aria-label="From Our Journal" style="margin-bottom: 70px;">
    <div class="container">
      <div class="section-header">
        <div>
          <h2 class="section-title">From Our Journal</h2>
          <p style="color: #6B7280; font-size: 13.5px; margin: 4px 0 0;">Inspirasi gaya perhiasan, teknik layering, dan rahasia perawatan perhiasan artisan.</p>
        </div>
        <a href="{{ route('blog.index') }}" class="section-link">View All Stories</a>
      </div>

      <div class="home-blog-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 28px;">
        @foreach($latestPosts as $post)
          <article class="blog-card" style="margin-bottom: 0;">
            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-media" style="display: block; overflow: hidden; border-radius: 6px;">
              <img
                src="{{ asset($post->image ?? 'images/blog-1.jpg') }}"
                alt="{{ $post->title }}"
                class="blog-card-img"
                loading="lazy"
                width="400"
                height="260"
                style="width: 100%; height: 220px; object-fit: cover; transition: transform 0.3s;"
              >
            </a>
            <div class="blog-card-content" style="padding-top: 14px;">
              <span class="blog-card-meta" style="font-size: 12px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.05em;">
                {{ $post->category }} &bull; {{ $post->published_at?->format('M d, Y') ?? 'Recently' }}
              </span>
              <h3 class="blog-card-title" style="font-size: 17px; font-weight: 600; margin: 8px 0 6px; line-height: 1.4;">
                <a href="{{ route('blog.show', $post->slug) }}" style="color: #111827; text-decoration: none;">
                  {{ $post->title }}
                </a>
              </h3>
              <p class="blog-card-excerpt" style="font-size: 13px; color: #6B7280; line-height: 1.5; margin-bottom: 12px;">
                {{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}
              </p>
              <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-readmore" style="font-size: 12.5px; font-weight: 700; color: #A18A68; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                Read Story &rarr;
              </a>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
@endsection
