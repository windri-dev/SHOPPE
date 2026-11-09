@extends('layouts.storefront')

@section('title', $product->name . ' - PUTRI.J')
@section('meta_description', \Illuminate\Support\Str::limit($product->description, 155))

@section('content')
<div class="container product-detail-page">
  <div class="product-detail-layout">
    <!-- Left: Gallery -->
    <div class="product-gallery">
      @php
        $galleryImages = is_array($product->images) && count($product->images) > 0
          ? $product->images
          : [$product->primary_image];
      @endphp
      <div class="gallery-thumbs">
        @foreach($galleryImages as $idx => $img)
          <button type="button" class="thumb-item {{ $idx === 0 ? 'active' : '' }}" data-img="{{ asset($img) }}" aria-label="Thumbnail {{ $idx + 1 }}">
            <img src="{{ asset($img) }}" alt="{{ $product->name }} thumbnail {{ $idx + 1 }}" width="96" height="96">
          </button>
        @endforeach
      </div>

      <div class="gallery-main-wrap">
        <div class="gallery-main-img-box">
          <img src="{{ asset($product->primary_image) }}" alt="{{ $product->name }}" id="mainProductImage" width="540" height="540">
        </div>
        <div class="gallery-progress-bar">
          <div class="gallery-progress-fill"></div>
        </div>
      </div>
    </div>

    <!-- Right: Product Info -->
    <div class="detail-info">
      <h1 class="detail-title">{{ $product->name }}</h1>
      <div class="detail-price">
        {{ $product->formatted_price }}
        @if($product->formatted_original_price)
          <span class="old-price" style="font-size: 14px; color: #707070; text-decoration: line-through; margin-left: 8px;">
            {{ $product->formatted_original_price }}
          </span>
        @endif
      </div>

      <div class="detail-rating-row">
        <div class="star-rating" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <span class="rating-count">1 customer review</span>
      </div>

      <p class="detail-short-desc">{{ $product->description }}</p>

      @if($product->isInStock())
        <div class="detail-actions-row">
          <div class="qty-control" aria-label="Product quantity">
            <button type="button" class="qty-btn" id="pdpQtyMinus" aria-label="Decrease quantity">&minus;</button>
            <input type="number" id="pdpQtyInput" class="qty-input" value="1" min="1" max="{{ $product->stock }}" aria-label="Quantity">
            <button type="button" class="qty-btn" id="pdpQtyPlus" aria-label="Increase quantity">&#43;</button>
          </div>

          <button
            type="button"
            class="btn-add-cart-outline"
            id="pdpAddToCartBtn"
            data-action="quick-add"
            data-product-id="{{ $product->id }}"
            data-product-title="{{ $product->name }}"
            data-product-price="{{ $product->price }}"
            data-product-img="{{ asset($product->primary_image) }}"
          >
            ADD TO CART
          </button>
        </div>
      @else
        <div class="detail-actions-row">
          <button type="button" class="btn-add-cart-outline" disabled style="opacity: 0.6; cursor: not-allowed;">
            SOLD OUT
          </button>
        </div>
      @endif

      <div class="detail-meta-social">
        <button
          type="button"
          class="wishlist-icon-btn"
          data-action="wishlist"
          data-product-id="{{ $product->id }}"
          aria-label="Add to wishlist"
        >
          <svg viewBox="0 0 24 24">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
        </button>
        <span class="meta-divider"></span>
        <div class="share-socials">
          <a href="mailto:?subject={{ urlencode($product->name) }}&body={{ urlencode(url()->current()) }}" class="share-link" aria-label="Share via Email">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
          </a>
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="share-link" aria-label="Share on Instagram">
            <svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path></svg>
          </a>
        </div>
      </div>

      <div class="detail-meta-list">
        <div class="meta-item"><strong>SKU:</strong> {{ $product->sku }}</div>
        <div class="meta-item"><strong>Categories:</strong> {{ $product->category->name ?? 'Jewelry' }}</div>
        <div class="meta-item">
          <strong>Availability:</strong>
          @if($product->isInStock())
            <span style="color: #2E7D32;">In Stock ({{ $product->stock }} pcs)</span>
          @else
            <span style="color: #C62828;">Sold Out</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Product Tabs -->
  <section class="product-tabs-section">
    <div class="tab-nav-list" role="tablist">
      <button type="button" class="tab-btn active" data-pdp-tab="tabDesc" role="tab" aria-selected="true">Description</button>
      <button type="button" class="tab-btn" data-pdp-tab="tabInfo" role="tab" aria-selected="false">Additional information</button>
    </div>

    <div class="tab-panel active" id="tabDesc" role="tabpanel">
      <p>{{ $product->description }}</p>
    </div>

    <div class="tab-panel" id="tabInfo" role="tabpanel">
      <div class="additional-info-list">
        <div class="info-row">
          <span class="info-label">Weight:</span>
          <span class="info-value">0.03 kg</span>
        </div>
        <div class="info-row">
          <span class="info-label">Dimensions:</span>
          <span class="info-value">15 x 10 x 1 cm</span>
        </div>
        <div class="info-row">
          <span class="info-label">Colours:</span>
          <span class="info-value">18k Gold-Dipped, Sterling Silver</span>
        </div>
        <div class="info-row">
          <span class="info-label">Material:</span>
          <span class="info-value">Hypoallergenic Recycled Brass</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Similar Items -->
  @if($relatedProducts->count() > 0)
    <section class="similar-products-section">
      <h2 class="similar-section-title">Similar Items</h2>
      <div class="product-grid">
        @foreach($relatedProducts as $related)
          <x-product-card :product="$related" />
        @endforeach
      </div>
    </section>
  @endif
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('pdpQtyInput');
    const minusBtn = document.getElementById('pdpQtyMinus');
    const plusBtn = document.getElementById('pdpQtyPlus');
    const addBtn = document.getElementById('pdpAddToCartBtn');

    if (minusBtn && qtyInput) {
      minusBtn.addEventListener('click', function () {
        const val = Math.max(1, (parseInt(qtyInput.value, 10) || 1) - 1);
        qtyInput.value = val;
        if (addBtn) addBtn.setAttribute('data-product-qty', String(val));
      });
    }

    if (plusBtn && qtyInput) {
      plusBtn.addEventListener('click', function () {
        const max = parseInt(qtyInput.getAttribute('max') || '99', 10);
        const val = Math.min(max, (parseInt(qtyInput.value, 10) || 1) + 1);
        qtyInput.value = val;
        if (addBtn) addBtn.setAttribute('data-product-qty', String(val));
      });
    }

    // Tabs switching
    const tabBtns = document.querySelectorAll('[data-pdp-tab]');
    const tabPanels = document.querySelectorAll('.tab-panel');
    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const target = this.getAttribute('data-pdp-tab');
        tabBtns.forEach(function (b) {
          b.classList.remove('active');
          b.setAttribute('aria-selected', 'false');
        });
        tabPanels.forEach(function (p) {
          p.classList.remove('active');
        });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');
        const panel = document.getElementById(target);
        if (panel) panel.classList.add('active');
      });
    });

    // Thumbnail switching
    const thumbs = document.querySelectorAll('.thumb-item');
    const mainImg = document.getElementById('mainProductImage');
    thumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        thumbs.forEach(function (t) { t.classList.remove('active'); });
        this.classList.add('active');
        const src = this.getAttribute('data-img');
        if (mainImg && src) mainImg.src = src;
      });
    });
  });
</script>
@endpush
@endsection
