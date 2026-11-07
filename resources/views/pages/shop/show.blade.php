@extends('layouts.storefront')

@section('title', $product->name . ' - SHOPPE')

@section('content')
<div class="container page-container">
  <div class="product-detail-layout">
    <!-- Gallery -->
    <div class="product-gallery">
      <img src="{{ asset($product->primary_image) }}" alt="{{ $product->name }}" class="main-pdp-img">
    </div>

    <!-- Info -->
    <div class="product-detail-info">
      <h1 class="pdp-title">{{ $product->name }}</h1>
      <div class="pdp-price">{{ $product->formatted_price }}</div>

      <div class="pdp-desc">
        <p>{{ $product->description }}</p>
      </div>

      <div class="pdp-meta">
        <div><strong>SKU:</strong> {{ $product->sku }}</div>
        <div><strong>Kategori:</strong> {{ $product->category->name ?? 'Jewelry' }}</div>
        <div><strong>Ketersediaan:</strong> {!! $product->isInStock() ? '<span style="color:#2E7D32;">Tersedia (' . $product->stock . ' pcs)</span>' : '<span style="color:#C62828;">Stok Habis</span>' !!}</div>
      </div>

      @if($product->isInStock())
        <div class="pdp-actions" style="margin-top: 30px;">
          <button class="btn btn-primary btn-block" data-action="quick-add" data-product-id="{{ $product->id }}" data-product-title="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-img="{{ asset($product->primary_image) }}">
            Add To Cart
          </button>
        </div>
      @else
        <div style="margin-top: 30px;">
          <button class="btn btn-block" disabled style="background: #E0E0E0; cursor: not-allowed; color: #888;">
            Sold Out
          </button>
        </div>
      @endif
    </div>
  </div>

  @if($relatedProducts->count() > 0)
    <section class="similar-products-section" style="margin-top: 60px;">
      <h2 class="section-title">Similar Items</h2>
      <div class="product-grid" style="margin-top: 25px;">
        @foreach($relatedProducts as $related)
          <x-product-card :product="$related" />
        @endforeach
      </div>
    </section>
  @endif
</div>
@endsection
