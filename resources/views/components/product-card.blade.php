@props(['product'])

<article class="product-card" data-product-id="{{ $product->id }}">
  <div class="product-media">
    <a href="{{ route('shop.show', $product->slug) }}">
      <img src="{{ asset($product->primary_image) }}" alt="{{ $product->name }}" loading="lazy" width="380" height="380">
    </a>
    
    @if($product->badge)
      <span class="badge {{ $product->badge === 'Sold out' ? 'badge-soldout' : 'badge-discount' }}">
        {{ $product->badge }}
      </span>
    @endif

    <div class="product-actions-overlay">
      <button class="action-btn" data-action="wishlist" title="Add to wishlist" aria-label="Add to wishlist">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
      </button>
      <button class="action-btn" data-action="quick-view" data-product-id="{{ $product->id }}" title="Quick view" aria-label="Quick view">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      </button>
    </div>

    @if($product->isInStock())
      <button class="product-quick-add" data-action="quick-add" data-product-id="{{ $product->id }}" data-product-title="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-img="{{ asset($product->primary_image) }}">
        ADD TO CART
      </button>
    @else
      <div class="product-sold-out-overlay">
        <span>SOLD OUT</span>
      </div>
    @endif
  </div>

  <div class="product-info">
    <h3 class="product-title">
      <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
    </h3>
    <div class="product-price">
      {{ $product->formatted_price }}
      @if($product->formatted_original_price)
        <span class="product-old-price">{{ $product->formatted_original_price }}</span>
      @endif
    </div>
  </div>
</article>
