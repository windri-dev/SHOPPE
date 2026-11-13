@extends('layouts.storefront')

@section('title', __('Daftar Keinginan') . ' - PUTRI.J')

@section('content')
<main class="container wishlist-page" style="padding-top: 40px; padding-bottom: 80px;">
  <!-- Header Title -->
  <div class="wishlist-header" style="text-align: center; margin-bottom: 48px;">
    <h1 style="font-family: var(--font-primary); font-size: clamp(1.75rem, 3vw, 2.25rem); font-weight: 700; color: #111827; margin-bottom: 10px; letter-spacing: -0.01em;">
      {{ __('Daftar Keinginan') }}
    </h1>
    <p style="font-size: 14px; color: #6B7280; max-width: 540px; margin: 0 auto;">
      {{ __('Koleksi busana pilihan dan potongan favorit yang Anda simpan di butik PUTRI.J.') }}
    </p>
  </div>

  @if($products->count() > 0)
    <!-- Wishlist Grid -->
    <div class="wishlist-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 32px;">
      @foreach($products as $product)
        <div class="wishlist-item-card" style="background: #FFFFFF; border-radius: 8px; border: 1px solid #EFEFEF; overflow: hidden; display: flex; flex-direction: column; transition: all 0.25s ease;">
          <div style="position: relative; aspect-ratio: 1 / 1; overflow: hidden; background: #FAF9F6;">
            <a href="{{ route('shop.show', $product->slug) }}">
              <img
                src="{{ asset($product->primary_image) }}"
                alt="{{ $product->name }}"
                style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;"
                loading="lazy"
              >
            </a>

            @if($product->badge)
              <span class="product-badge badge {{ $product->badge === 'Sold out' ? 'badge-new' : 'badge-sale' }}" style="position: absolute; top: 12px; left: 12px;">
                {{ __($product->badge) }}
              </span>
            @endif

            <!-- Remove Button -->
            <form action="{{ route('wishlist.destroy', $product->id) }}" method="POST" style="position: absolute; top: 12px; right: 12px; z-index: 5;">
              @csrf
              @method('DELETE')
              <button
                type="submit"
                class="wishlist-remove-btn"
                title="{{ __('Hapus dari Wishlist') }}"
                aria-label="{{ __('Hapus dari Wishlist') }}"
                style="width: 34px; height: 34px; border-radius: 50%; background: rgba(255, 255, 255, 0.92); border: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6B7280; transition: all 0.2s ease;"
                onmouseover="this.style.color='#D82700'; this.style.borderColor='#D82700'; this.style.transform='scale(1.08)';"
                onmouseout="this.style.color='#6B7280'; this.style.borderColor='#E5E7EB'; this.style.transform='scale(1)';"
              >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </form>
          </div>

          <div style="padding: 18px; display: flex; flex-direction: column; flex: 1;">
            <div style="font-size: 11.5px; text-transform: uppercase; color: #8C8273; font-weight: 600; letter-spacing: 0.05em; margin-bottom: 4px;">
              {{ $product->category->name ?? 'Apparel' }}
            </div>
            <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 8px; line-height: 1.35;">
              <a href="{{ route('shop.show', $product->slug) }}" style="color: #111827; text-decoration: none;">
                {{ $product->name }}
              </a>
            </h3>

            <div style="font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 16px;">
              {{ $product->formatted_price }}
              @if($product->formatted_original_price)
                <span style="font-size: 13px; color: #9CA3AF; text-decoration: line-through; font-weight: 400; margin-left: 6px;">
                  {{ $product->formatted_original_price }}
                </span>
              @endif
            </div>

            <div style="margin-top: auto; display: flex; gap: 8px;">
              @if($product->isInStock())
                <button
                  type="button"
                  class="btn btn-primary btn-block"
                  data-action="quick-add"
                  data-product-id="{{ $product->id }}"
                  data-product-title="{{ $product->name }}"
                  data-product-price="{{ $product->price }}"
                  data-product-img="{{ asset($product->primary_image) }}"
                  style="flex: 1; padding: 10px; font-size: 12.5px; border-radius: 6px; letter-spacing: 0.03em;"
                >
                  {{ __('Tambah ke Keranjang') }}
                </button>
              @else
                <button
                  type="button"
                  class="btn btn-outline btn-block"
                  disabled
                  style="flex: 1; padding: 10px; font-size: 12.5px; border-radius: 6px; opacity: 0.6; cursor: not-allowed;"
                >
                  {{ __('Sold Out') }}
                </button>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    @if($products->hasPages())
      <div class="pagination-wrapper" style="margin-top: 48px;">
        {{ $products->links('partials.pagination') }}
      </div>
    @endif
  @else
    <!-- Empty State -->
    <div class="wishlist-empty-state" style="text-align: center; padding: 72px 20px; background: #FAF9F6; border-radius: 12px; border: 1px dashed #E5E7EB; max-width: 580px; margin: 0 auto;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="1.8">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
      </div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 10px;">
        {{ __('Daftar Keinginan Anda Masih Kosong') }}
      </h2>
      <p style="font-size: 14px; color: #6B7280; max-width: 420px; margin: 0 auto 28px; line-height: 1.6;">
        {{ __('Simpan busana, gaun, dan siluet favorit Anda dengan menekan tombol hati untuk mempermudah belanja kapan saja.') }}
      </p>
      <a href="{{ route('shop.index') }}" class="btn btn-primary" style="padding: 12px 28px; font-size: 13px; border-radius: 6px;">
        {{ __('Mulai Jelajahi Koleksi') }}
      </a>
    </div>
  @endif
</main>
@endsection

