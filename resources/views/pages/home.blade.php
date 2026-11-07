@extends('layouts.storefront')

@section('title', 'SHOPPE - Modern Jewelry & Accessories')

@section('content')
<!-- Hero Section Carousel -->
<section class="hero-section" aria-label="Hero Carousel">
  <div class="container">
    <div class="hero-slider-container" id="heroSlider">
      <div class="hero-slides">
        <div class="hero-slide active">
          <img src="{{ asset('images/hero-1.png') }}" alt="Gold big hoops" class="hero-bg" fetchpriority="high" loading="eager" width="1200" height="600">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <h1 class="hero-title">Gold big hoops</h1>
            <div class="hero-price">$ 68.00</div>
            <a href="{{ route('shop.index') }}" class="btn">View Product</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Latest Products Section -->
<section class="catalog-section" aria-label="Latest Products">
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
@endsection
