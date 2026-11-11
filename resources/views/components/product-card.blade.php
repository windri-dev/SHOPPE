@props(['product'])

<article
  class="product-card"
  data-id="{{ $product->id }}"
  data-product-id="{{ $product->id }}"
  data-slug="{{ $product->slug }}"
  data-title="{{ $product->name }}"
  data-price="{{ $product->price }}"
  data-image="{{ asset($product->primary_image) }}"
  data-url="{{ route('shop.show', $product->slug) }}"
  data-description="{{ $product->description }}"
  data-category="{{ $product->category->slug ?? 'apparel' }}"
  data-stock="{{ $product->isInStock() ? 'true' : 'false' }}"
  data-video="{{ $product->video_url ?? '' }}"
  data-video-type="{{ $product->video_type }}"
  data-video-embed="{{ $product->video_embed_url ?? '' }}"
>
  <div class="product-media">
    <a href="{{ route('shop.show', $product->slug) }}" class="product-media-link">
      <img
        src="{{ asset($product->primary_image) }}"
        alt="{{ $product->name }}"
        class="product-image"
        loading="lazy"
        width="380"
        height="380"
      >
    </a>

    @if($product->badge)
      <span class="product-badge badge {{ $product->badge === 'Sold out' ? 'badge-new' : 'badge-sale' }}">
        {{ __($product->badge) }}
      </span>
    @endif

    <div class="product-actions">
      <button
        type="button"
        class="action-icon-btn"
        data-action="wishlist"
        data-product-id="{{ $product->id }}"
        title="Add to wishlist"
        aria-label="Add {{ $product->name }} to wishlist"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
      </button>
      <button
        type="button"
        class="action-icon-btn"
        data-action="quick-view"
        data-product-id="{{ $product->id }}"
        title="{{ __('Quick View') }}"
        aria-label="{{ __('Quick View') }} {{ $product->name }}"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
      </button>
    </div>

    @if($product->isInStock())
      <button
        type="button"
        class="product-quick-add"
        data-action="quick-add"
        data-product-id="{{ $product->id }}"
        data-product-title="{{ $product->name }}"
        data-product-price="{{ $product->price }}"
        data-product-img="{{ asset($product->primary_image) }}"
      >
        {{ __('Add to Cart') }}
      </button>
    @else
      <button
        type="button"
        class="product-quick-add"
        disabled
        style="opacity: 0.75; cursor: not-allowed;"
      >
        {{ __('Sold Out') }}
      </button>
    @endif
  </div>

  <div class="product-info">
    <h3 class="product-title">
      <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
    </h3>
    <div class="product-price">
      {{ $product->formatted_price }}
      @if($product->formatted_original_price)
        <span class="old-price">{{ $product->formatted_original_price }}</span>
      @endif
    </div>
  </div>
</article>
