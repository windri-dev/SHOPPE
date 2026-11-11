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

        @if($product->hasVideo())
          <button type="button" class="thumb-item thumb-video" id="pdpThumbVideoBtn" aria-label="Lihat Video Produk">
            <div class="thumb-video-icon-wrap">
              <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22">
                <path d="M8 5v14l11-7z"/>
              </svg>
              <span>VIDEO</span>
            </div>
          </button>
        @endif
      </div>

      <div class="gallery-main-wrap">
        <!-- Main Image Container -->
        <div class="gallery-main-img-box" id="pdpMainImgBox" style="position: relative;">
          <img src="{{ asset($product->primary_image) }}" alt="{{ $product->name }}" id="mainProductImage" width="540" height="540">

          @if($product->hasVideo())
            <button type="button" class="pdp-video-badge-btn" id="pdpFloatingPlayBtn" aria-label="Putar Video Busana">
              <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                <path d="M8 5v14l11-7z"/>
              </svg>
              <span>Lihat Video Busana</span>
            </button>
          @endif
        </div>

        <!-- Main Video Container (Shown when Video thumbnail is active) -->
        @if($product->hasVideo())
          <div class="gallery-main-video-box" id="pdpMainVideoBox" style="display: none;">
            @if($product->video_type === 'youtube' || $product->video_type === 'vimeo')
              <iframe
                id="pdpVideoIframe"
                src="{{ $product->video_embed_url }}"
                title="{{ $product->name }} Video"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            @else
              <video
                id="pdpDirectVideo"
                controls
                playsinline
                preload="metadata"
                poster="{{ asset($product->primary_image) }}"
              >
                <source src="{{ $product->video_url }}" type="video/mp4">
                Browser Anda tidak mendukung pemutaran video langsung.
              </video>
            @endif
          </div>
        @endif

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

      <a href="#customer-reviews" class="detail-rating-row" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
        <div class="star-rating" style="color: #A18A68; font-size: 15px;" aria-label="{{ $product->average_rating }} out of 5 stars">
          @for($i = 1; $i <= 5; $i++)
            @if($i <= (int) round($product->average_rating))
              &#9733;
            @else
              <span style="color: #D1D5DB;">&#9733;</span>
            @endif
          @endfor
        </div>
        <span class="rating-count" style="font-size: 13px; color: #4B5563; font-weight: 500;">
          <strong style="color: #111827;">{{ number_format($product->average_rating, 1) }}</strong> / 5.0 ({{ $product->reviews_count }} ulasan)
        </span>
      </a>

      <p class="detail-short-desc">{{ $product->description }}</p>

      <!-- Apparel Size Selector Pill -->
      <div style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
          <span style="font-size: 13px; font-weight: 600; color: #111827;">Pilihan Ukuran (Size):</span>
          <button type="button" onclick="document.querySelector('[data-pdp-tab=\'tabInfo\']').click()" style="background: none; border: none; font-size: 12px; color: #A18A68; text-decoration: underline; cursor: pointer;">Panduan Ukuran (Size Chart)</button>
        </div>
        <div style="display: flex; gap: 8px;" id="pdpSizeSelector">
          @foreach(['S', 'M', 'L', 'XL'] as $idx => $size)
            <button
              type="button"
              class="pdp-size-btn {{ $idx === 1 ? 'active' : '' }}"
              data-size="{{ $size }}"
              style="min-width: 44px; height: 38px; border: 1px solid {{ $idx === 1 ? '#111827' : '#D1D5DB' }}; background: {{ $idx === 1 ? '#111827' : '#FFFFFF' }}; color: {{ $idx === 1 ? '#FFFFFF' : '#374151' }}; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;"
            >
              {{ $size }}
            </button>
          @endforeach
        </div>
      </div>

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
        <div class="meta-item"><strong>Kategori:</strong> {{ $product->category->name ?? 'Apparel & Ready-to-Wear' }}</div>
        <div class="meta-item">
          <strong>Ketersediaan:</strong>
          @if($product->isInStock())
            <span style="color: #2E7D32;">In Stock ({{ $product->stock }} pcs)</span>
          @else
            <span style="color: #C62828;">Sold Out</span>
          @endif
        </div>
        <div class="meta-item">
          <strong>Garansi:</strong>
          <span style="color: #4B5563;">Complimentary Size Exchange 7 Hari</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Tabs -->
  <section class="product-tabs-section">
    <div class="tab-nav-list" role="tablist">
      <button type="button" class="tab-btn active" data-pdp-tab="tabDesc" role="tab" aria-selected="true">Description</button>
      <button type="button" class="tab-btn" data-pdp-tab="tabInfo" role="tab" aria-selected="false">Panduan Ukuran &amp; Material</button>
    </div>

    <div class="tab-panel active" id="tabDesc" role="tabpanel">
      <p style="line-height: 1.8; color: #4B5563;">{{ $product->description }}</p>
      <div style="margin-top: 18px; padding: 16px; background: #FAF9F6; border-radius: 8px; border: 1px solid #E5E7EB;">
        <h4 style="font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">✨ Catatan Styling PUTRI.J</h4>
        <p style="font-size: 13.5px; color: #6B7280; margin: 0; line-height: 1.6;">
          Padukan busana ini dengan sepatu hak minimalis atau mules santai untuk kesan refined luxury. Material berkarakter jatuh rapi dan bernapas sejuk, nyaman dikenakan sepanjang hari dalam berbagai suasana.
        </p>
      </div>
    </div>

    <div class="tab-panel" id="tabInfo" role="tabpanel">
      <div class="additional-info-list">
        <div class="info-row">
          <span class="info-label">Material Kain:</span>
          <span class="info-value">French Washed Linen, Mulberry Silk Blend, Organic Poplin Cotton</span>
        </div>
        <div class="info-row">
          <span class="info-label">Pilihan Ukuran:</span>
          <span class="info-value">S (LD 88-92 cm), M (LD 94-98 cm), L (LD 100-104 cm), XL (LD 106-112 cm)</span>
        </div>
        <div class="info-row">
          <span class="info-label">Siluet / Potongan:</span>
          <span class="info-value">Tailored Relaxed Fit, Draped Silhouette</span>
        </div>
        <div class="info-row">
          <span class="info-label">Petunjuk Perawatan:</span>
          <span class="info-value">Cuci lembut dengan air dingin (hand wash/delicate cycle), jemur terbalik di tempat teduh, setrika suhu sedang.</span>
        </div>
        <div class="info-row">
          <span class="info-label">Garansi Tukar Ukuran:</span>
          <span class="info-value">Tukar ukuran gratis 1x dalam 7 hari jika baju kebesaran atau kekecilan.</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Customer Reviews & Rating Section (Shopee Marketplace Style) -->
  <section class="product-reviews-section" id="customer-reviews" style="margin-top: 50px; margin-bottom: 60px; scroll-margin-top: 90px;">
    <!-- Section Title -->
    <div style="margin-bottom: 16px;">
      <h2 style="font-size: 19px; font-weight: 700; color: rgba(0,0,0,0.87); margin: 0; letter-spacing: -0.01em;">Penilaian Produk</h2>
    </div>

    @if(session('review_success'))
      <div style="margin-bottom: 20px; padding: 12px 16px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 4px; color: #065F46; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>{{ session('review_success') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div style="margin-bottom: 20px; padding: 12px 16px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 4px; color: #991B1B; font-size: 13px;">
        <strong>Mohon lengkapi isian penilaian:</strong>
        <ul style="margin: 4px 0 0 18px; padding: 0;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Shopee-Style Rating Overview Box (Themed to PUTRI.J Luxury Palette) -->
    <div class="shoppe-rating-overview" style="background: #FAF9F6; border: 1px solid #EAEAEA; border-radius: 6px; padding: 26px 30px; margin-bottom: 24px; display: flex; flex-wrap: wrap; align-items: center; gap: 28px;">
      <!-- Left: Big Score & Luxury Gold Stars -->
      <div style="min-width: 150px; text-align: center;">
        <div style="line-height: 1;">
          <span style="font-size: 32px; font-weight: 700; color: #111827;">{{ number_format($product->average_rating, 1) }}</span>
          <span style="font-size: 16px; font-weight: 500; color: #A18A68; margin-left: 2px;">dari 5</span>
        </div>
        <div style="color: #A18A68; font-size: 20px; margin-top: 8px; letter-spacing: 2px;">
          @for($i = 1; $i <= 5; $i++)
            @if($i <= (int) round($product->average_rating))
              &#9733;
            @else
              <span style="color: #D1D5DB;">&#9733;</span>
            @endif
          @endfor
        </div>
      </div>

      <!-- Right: Filter Pills -->
      <div style="flex: 1; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
        @php
          $breakdown = $product->rating_breakdown;
        @endphp
        <button type="button" class="shoppe-filter-btn active" data-filter="all">Semua</button>
        <button type="button" class="shoppe-filter-btn" data-filter="5">5 Bintang ({{ $breakdown[5]['count'] }})</button>
        <button type="button" class="shoppe-filter-btn" data-filter="4">4 Bintang ({{ $breakdown[4]['count'] }})</button>
        <button type="button" class="shoppe-filter-btn" data-filter="3">3 Bintang ({{ $breakdown[3]['count'] }})</button>
        <button type="button" class="shoppe-filter-btn" data-filter="2">2 Bintang ({{ $breakdown[2]['count'] }})</button>
        <button type="button" class="shoppe-filter-btn" data-filter="1">1 Bintang ({{ $breakdown[1]['count'] }})</button>
        <button type="button" class="shoppe-filter-btn" data-filter="comment">Dengan Komentar ({{ $product->reviews_with_comment_count }})</button>
        <button type="button" class="shoppe-filter-btn" data-filter="media">Dengan Media ({{ $product->reviews_with_media_count }})</button>

        <button
          type="button"
          id="btnToggleReviewForm"
          style="margin-left: auto; height: 32px; padding: 0 16px; background: #111827; color: #FFFFFF; border: 1px solid #111827; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;"
          onmouseover="this.style.background='#A18A68'; this.style.borderColor='#A18A68';"
          onmouseout="this.style.background='#111827'; this.style.borderColor='#111827';"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          Tulis Penilaian
        </button>
      </div>
    </div>

    <!-- Review Submission Form (Expandable) -->
    <div id="reviewFormContainer" style="display: {{ $errors->any() ? 'block' : 'none' }}; margin-bottom: 28px; padding: 24px; border: 1px solid #EAEAEA; border-radius: 6px; background: #FAF9F6;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #EAEAEA;">
        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0;">Beri Penilaian &amp; Ulasan Produk</h3>
        <button type="button" id="btnCloseReviewForm" style="background: none; border: none; font-size: 20px; line-height: 1; color: #9CA3AF; cursor: pointer;">&times;</button>
      </div>

      <form action="{{ route('shop.reviews.store', $product->slug) }}" method="POST">
        @csrf
        <!-- Rating Star Picker -->
        <div style="margin-bottom: 18px;">
          <label style="display: block; font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 6px;">Kualitas Produk <span style="color: #A18A68;">*</span></label>
          <div style="display: flex; align-items: center; gap: 12px;">
            <div id="starPickerGroup" style="display: inline-flex; gap: 4px; font-size: 26px; cursor: pointer; color: #D1D5DB;">
              @for($s = 1; $s <= 5; $s++)
                <span class="star-pick-item" data-value="{{ $s }}" style="transition: color 0.15s ease;">&#9733;</span>
              @endfor
            </div>
            <input type="hidden" name="rating" id="reviewRatingInput" value="{{ old('rating', '5') }}" required>
            <span id="starRatingLabel" style="font-size: 13px; font-weight: 600; color: #A18A68;">5 Bintang - Sangat Baik</span>
          </div>
        </div>

        <!-- Name, Email, Variant & Tags Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 14px;">
          <div>
            <label for="reviewCustomerName" style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 5px;">Username / Nama <span style="color: #A18A68;">*</span></label>
            <input
              type="text"
              name="customer_name"
              id="reviewCustomerName"
              value="{{ old('customer_name', auth()->user()?->username ?? auth()->user()?->name) }}"
              required
              placeholder="Contoh: zainulll12"
              style="width: 100%; padding: 8px 12px; font-size: 13.5px; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; box-sizing: border-box; background: #FFFFFF;"
            >
          </div>
          <div>
            <label for="reviewSizePurchased" style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 5px;">Variasi Produk yang Dibeli</label>
            <input
              type="text"
              name="size_purchased"
              id="reviewSizePurchased"
              value="{{ old('size_purchased', 'Size M, Standar') }}"
              placeholder="Contoh: Size M, Champagne Silk"
              style="width: 100%; padding: 8px 12px; font-size: 13.5px; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; box-sizing: border-box; background: #FFFFFF;"
            >
          </div>
          <div>
            <label for="reviewQualityTag" style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 5px;">Kualitas Bahan</label>
            <input
              type="text"
              name="quality_tag"
              id="reviewQualityTag"
              value="{{ old('quality_tag') }}"
              placeholder="Contoh: Sangat bagus & adem"
              style="width: 100%; padding: 8px 12px; font-size: 13.5px; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; box-sizing: border-box; background: #FFFFFF;"
            >
          </div>
          <div>
            <label for="reviewFitTag" style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 5px;">Kesesuaian Ukuran</label>
            <input
              type="text"
              name="fit_tag"
              id="reviewFitTag"
              value="{{ old('fit_tag') }}"
              placeholder="Contoh: Pas di badan"
              style="width: 100%; padding: 8px 12px; font-size: 13.5px; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; box-sizing: border-box; background: #FFFFFF;"
            >
          </div>
        </div>

        <!-- Comment -->
        <div style="margin-bottom: 16px;">
          <label for="reviewComment" style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 5px;">Deskripsi Ulasan <span style="color: #A18A68;">*</span></label>
          <textarea
            name="comment"
            id="reviewComment"
            rows="3"
            required
            placeholder="Tulis ulasan Anda mengenai kualitas barang, kecepatan pengiriman, dan pelayanan penjual..."
            style="width: 100%; padding: 10px 12px; font-size: 13.5px; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; box-sizing: border-box; resize: vertical; background: #FFFFFF;"
          >{{ old('comment') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <button
            type="button"
            id="btnCancelReviewForm"
            style="padding: 7px 16px; background: #FFFFFF; border: 1px solid #D1D5DB; color: #4B5563; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer;"
          >
            Batal
          </button>
          <button
            type="submit"
            style="padding: 7px 22px; background: #111827; border: 1px solid #111827; color: #FFFFFF; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;"
            onmouseover="this.style.background='#A18A68'; this.style.borderColor='#A18A68';"
            onmouseout="this.style.background='#111827'; this.style.borderColor='#111827';"
          >
            Kirim Penilaian
          </button>
        </div>
      </form>
    </div>

    <!-- Review Items Feed (Exact Shopee Format with Theme-Compliant Palette) -->
    <div class="shoppe-reviews-list" id="shoppeReviewsContainer">
      @forelse($product->approvedReviews as $review)
        <div
          class="shoppe-review-item"
          data-rating="{{ $review->rating }}"
          data-has-comment="{{ $review->has_comment ? '1' : '0' }}"
          data-has-media="{{ $review->has_media ? '1' : '0' }}"
          style="display: flex; gap: 16px; padding: 22px 0; border-bottom: 1px solid #EAEAEA;"
        >
          <!-- Left: User Avatar -->
          <div style="flex-shrink: 0;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $loop->index % 2 === 0 ? '#111827' : '#F3F4F6' }}; color: {{ $loop->index % 2 === 0 ? '#FAF9F6' : '#6B7280' }}; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; overflow: hidden; border: 1px solid rgba(0,0,0,0.06);">
              @if($loop->index % 2 === 0)
                {{ substr($review->customer_name, 0, 1) }}
              @else
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              @endif
            </div>
          </div>

          <!-- Right: Review Content -->
          <div style="flex: 1; min-width: 0;">
            <!-- Username -->
            <div style="font-size: 13px; color: #111827; font-weight: 600;">
              {{ $review->display_name }}
            </div>

            <!-- Gold Stars (PUTRI.J Palette) -->
            <div style="color: #A18A68; font-size: 13px; margin: 3px 0 4px 0; letter-spacing: 1px;">
              @for($s = 1; $s <= 5; $s++)
                @if($s <= $review->rating)
                  &#9733;
                @else
                  <span style="color: #D1D5DB;">&#9733;</span>
                @endif
              @endfor
            </div>

            <!-- Timestamp & Variant -->
            <div style="font-size: 12px; color: #6B7280; margin-bottom: 10px;">
              {{ $review->created_at->format('Y-m-d H:i') }} | Variasi: {{ $review->size_purchased ?? 'Size M, Champagne Silk' }}
            </div>

            <!-- Structured Attributes -->
            @if($review->quality_tag)
              <div style="font-size: 13px; color: #6B7280; margin-bottom: 4px;">
                Kualitas Bahan: <span style="color: #111827; font-weight: 500;">{{ $review->quality_tag }}</span>
              </div>
            @endif
            @if($review->fit_tag)
              <div style="font-size: 13px; color: #6B7280; margin-bottom: 8px;">
                Kesesuaian Ukuran: <span style="color: #111827; font-weight: 500;">{{ $review->fit_tag }}</span>
              </div>
            @endif

            <!-- Comment Text -->
            <div style="font-size: 14px; line-height: 1.6; color: #374151; word-break: break-word; margin-bottom: 12px;">
              {{ $review->comment }}
            </div>

            <!-- Media Photos Thumbnails -->
            @if($review->has_media)
              <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px;">
                @foreach($review->images as $imgIdx => $img)
                  <div
                    class="review-media-thumb"
                    data-preview-src="{{ asset($img) }}"
                    style="width: 72px; height: 72px; border-radius: 4px; overflow: hidden; border: 1px solid #EAEAEA; cursor: pointer; position: relative;"
                  >
                    <img src="{{ asset($img) }}" alt="Foto Ulasan" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
                    @if($imgIdx === 0 && count($review->images) > 2)
                      <span style="position: absolute; bottom: 2px; left: 2px; background: rgba(0,0,0,0.65); color: #FFFFFF; font-size: 9px; padding: 1px 4px; border-radius: 2px; display: inline-flex; align-items: center; gap: 2px;">
                        ▶ 0:04
                      </span>
                    @endif
                  </div>
                @endforeach
              </div>
            @endif

            <!-- Respon Penjual (Seller Response Box with Gold Accent Line) -->
            @if($review->seller_reply)
              <div style="background: #F7F5F0; border-left: 3px solid #A18A68; border-radius: 4px; padding: 12px 16px; margin-bottom: 14px; border-top: 1px solid #EFECE6; border-right: 1px solid #EFECE6; border-bottom: 1px solid #EFECE6;">
                <div style="font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 4px;">Respon Penjual:</div>
                <div style="font-size: 13px; line-height: 1.5; color: #4B5563;">{{ $review->seller_reply }}</div>
              </div>
            @endif

            <!-- Bottom Row: Helpful Vote & Three Dots Menu -->
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: #9CA3AF;">
              <button
                type="button"
                class="btn-helpful"
                data-review-id="{{ $review->id }}"
                data-helpful-url="{{ route('reviews.helpful', $review->id) }}"
                style="background: none; border: none; font-size: 12.5px; color: #6B7280; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; padding: 0; transition: color 0.15s ease;"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                <span class="helpful-count-badge">{{ $review->helpful_count }}</span>
              </button>
              <span style="color: #9CA3AF; font-size: 16px; cursor: pointer; user-select: none;">⋮</span>
            </div>
          </div>
        </div>
      @empty
        <div style="text-align: center; padding: 40px 20px; color: #6B7280; font-size: 14px;">
          Belum ada penilaian untuk produk ini.
        </div>
      @endforelse

      <div id="noMatchingReviewsMsg" style="display: none; text-align: center; padding: 40px 20px; color: #6B7280; font-size: 14px;">
        Tidak ada penilaian yang sesuai dengan filter ini.
      </div>
    </div>
  </section>

  <!-- Image Lightbox Modal for Review Media -->
  <div id="reviewMediaModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="position: relative; max-width: 90vw; max-height: 90vh;">
      <button type="button" id="btnCloseReviewMediaModal" style="position: absolute; top: -36px; right: 0; background: none; border: none; color: #FFFFFF; font-size: 28px; cursor: pointer; line-height: 1;">&times;</button>
      <img id="reviewModalImg" src="" alt="Preview Ulasan" style="max-width: 80vw; max-height: 80vh; object-fit: contain; border-radius: 4px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
    </div>
  </div>

  <style>
    .shoppe-filter-btn {
      height: 32px;
      padding: 0 14px;
      background: #FFFFFF;
      border: 1px solid #E5E7EB;
      border-radius: 4px;
      font-size: 13px;
      color: #374151;
      cursor: pointer;
      transition: all 0.15s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      user-select: none;
    }
    .shoppe-filter-btn:hover {
      border-color: #A18A68;
      color: #A18A68;
    }
    .shoppe-filter-btn.active {
      border-color: #A18A68;
      color: #A18A68;
      background: #F7F5F0;
      font-weight: 600;
    }
  </style>

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

    // Size Selector Buttons
    const sizeBtns = document.querySelectorAll('#pdpSizeSelector .pdp-size-btn');
    sizeBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        sizeBtns.forEach(function (b) {
          b.style.background = '#FFFFFF';
          b.style.color = '#374151';
          b.style.borderColor = '#D1D5DB';
          b.classList.remove('active');
        });
        this.style.background = '#111827';
        this.style.color = '#FFFFFF';
        this.style.borderColor = '#111827';
        this.classList.add('active');
      });
    });

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

    // Gallery switching (Images & Video)
    const thumbs = document.querySelectorAll('.gallery-thumbs .thumb-item:not(#pdpThumbVideoBtn)');
    const videoThumbBtn = document.getElementById('pdpThumbVideoBtn');
    const floatingPlayBtn = document.getElementById('pdpFloatingPlayBtn');
    const mainImgBox = document.getElementById('pdpMainImgBox');
    const mainVideoBox = document.getElementById('pdpMainVideoBox');
    const mainImg = document.getElementById('mainProductImage');
    const directVideo = document.getElementById('pdpDirectVideo');

    function switchToImage(thumb) {
      document.querySelectorAll('.gallery-thumbs .thumb-item').forEach(function (t) {
        t.classList.remove('active');
      });
      if (thumb) thumb.classList.add('active');
      if (mainVideoBox) {
        mainVideoBox.style.display = 'none';
        if (directVideo && !directVideo.paused) directVideo.pause();
      }
      if (mainImgBox) mainImgBox.style.display = 'block';
    }

    function switchToVideo() {
      document.querySelectorAll('.gallery-thumbs .thumb-item').forEach(function (t) {
        t.classList.remove('active');
      });
      if (videoThumbBtn) videoThumbBtn.classList.add('active');
      if (mainImgBox) mainImgBox.style.display = 'none';
      if (mainVideoBox) {
        mainVideoBox.style.display = 'flex';
        if (directVideo) {
          directVideo.play().catch(function () {});
        }
      }
    }

    thumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        switchToImage(this);
        const src = this.getAttribute('data-img');
        if (mainImg && src) mainImg.src = src;
      });
    });

    if (videoThumbBtn) {
      videoThumbBtn.addEventListener('click', function () {
        switchToVideo();
      });
    }

    if (floatingPlayBtn) {
      floatingPlayBtn.addEventListener('click', function () {
        switchToVideo();
      });
    }

    // Customer Reviews: Toggle Form
    const btnToggleReviewForm = document.getElementById('btnToggleReviewForm');
    const btnCloseReviewForm = document.getElementById('btnCloseReviewForm');
    const btnCancelReviewForm = document.getElementById('btnCancelReviewForm');
    const reviewFormContainer = document.getElementById('reviewFormContainer');

    function openReviewForm() {
      if (reviewFormContainer) {
        reviewFormContainer.style.display = 'block';
        reviewFormContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    function closeReviewForm() {
      if (reviewFormContainer) {
        reviewFormContainer.style.display = 'none';
      }
    }

    if (btnToggleReviewForm) {
      btnToggleReviewForm.addEventListener('click', function () {
        if (reviewFormContainer && reviewFormContainer.style.display === 'none') {
          openReviewForm();
        } else {
          closeReviewForm();
        }
      });
    }

    if (btnCloseReviewForm) btnCloseReviewForm.addEventListener('click', closeReviewForm);
    if (btnCancelReviewForm) btnCancelReviewForm.addEventListener('click', closeReviewForm);

    // Customer Reviews: Star Picker
    const starPickerGroup = document.getElementById('starPickerGroup');
    const reviewRatingInput = document.getElementById('reviewRatingInput');
    const starRatingLabel = document.getElementById('starRatingLabel');
    const starItems = document.querySelectorAll('.star-pick-item');

    const starLabels = {
      1: '1 / 5 - Kurang Puas',
      2: '2 / 5 - Cukup',
      3: '3 / 5 - Baik',
      4: '4 / 5 - Sangat Bagus',
      5: '5 / 5 - Luar Biasa (Rekomendasi)'
    };

    function updateStarDisplay(val) {
      starItems.forEach(function (star) {
        const starVal = parseInt(star.getAttribute('data-value'), 10);
        if (starVal <= val) {
          star.style.color = '#A18A68';
        } else {
          star.style.color = '#D1D5DB';
        }
      });
      if (starRatingLabel && starLabels[val]) {
        starRatingLabel.textContent = starLabels[val];
      }
    }

    if (reviewRatingInput) {
      const initialVal = parseInt(reviewRatingInput.value, 10) || 5;
      updateStarDisplay(initialVal);

      starItems.forEach(function (star) {
        star.addEventListener('mouseenter', function () {
          const hoverVal = parseInt(this.getAttribute('data-value'), 10);
          updateStarDisplay(hoverVal);
        });

        star.addEventListener('click', function () {
          const clickedVal = parseInt(this.getAttribute('data-value'), 10);
          reviewRatingInput.value = clickedVal;
          updateStarDisplay(clickedVal);
        });
      });

      if (starPickerGroup) {
        starPickerGroup.addEventListener('mouseleave', function () {
          const currentVal = parseInt(reviewRatingInput.value, 10) || 5;
          updateStarDisplay(currentVal);
        });
      }
    }

    // Shopee-Style Filter Buttons
    const filterBtns = document.querySelectorAll('.shoppe-filter-btn');
    const reviewItems = document.querySelectorAll('.shoppe-review-item');
    const noMatchingMsg = document.getElementById('noMatchingReviewsMsg');

    filterBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        filterBtns.forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');
        let visibleCount = 0;

        reviewItems.forEach(function (item) {
          let matches = false;
          const rating = item.getAttribute('data-rating');
          const hasComment = item.getAttribute('data-has-comment');
          const hasMedia = item.getAttribute('data-has-media');

          if (filter === 'all') {
            matches = true;
          } else if (['1', '2', '3', '4', '5'].includes(filter)) {
            matches = rating === filter;
          } else if (filter === 'comment') {
            matches = hasComment === '1';
          } else if (filter === 'media') {
            matches = hasMedia === '1';
          }

          if (matches) {
            item.style.display = 'flex';
            visibleCount++;
          } else {
            item.style.display = 'none';
          }
        });

        if (noMatchingMsg) {
          noMatchingMsg.style.display = (visibleCount === 0 && reviewItems.length > 0) ? 'block' : 'none';
        }
      });
    });

    // Review Media Modal Preview
    const mediaThumbs = document.querySelectorAll('.review-media-thumb');
    const mediaModal = document.getElementById('reviewMediaModal');
    const modalImg = document.getElementById('reviewModalImg');
    const btnCloseModal = document.getElementById('btnCloseReviewMediaModal');

    mediaThumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        const src = this.getAttribute('data-preview-src');
        if (src && modalImg && mediaModal) {
          modalImg.src = src;
          mediaModal.style.display = 'flex';
        }
      });
    });

    if (btnCloseModal && mediaModal) {
      btnCloseModal.addEventListener('click', function () {
        mediaModal.style.display = 'none';
      });
    }

    if (mediaModal) {
      mediaModal.addEventListener('click', function (e) {
        if (e.target === mediaModal) {
          mediaModal.style.display = 'none';
        }
      });
    }

    // Customer Reviews: Helpful Button AJAX
    const helpfulBtns = document.querySelectorAll('.btn-helpful');
    helpfulBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (this.disabled) return;
        const btnElem = this;
        const helpfulUrl = btnElem.getAttribute('data-helpful-url');
        const badgeElem = btnElem.querySelector('.helpful-count-badge');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        btnElem.disabled = true;
        btnElem.style.opacity = '0.7';

        fetch(helpfulUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
          }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.success) {
            if (badgeElem) badgeElem.textContent = data.helpful_count;
            btnElem.style.color = '#A18A68';
            btnElem.style.opacity = '1';
          }
        })
        .catch(function () {
          btnElem.disabled = false;
          btnElem.style.opacity = '1';
        });
      });
    });
  });
</script>
@endpush
@endsection
