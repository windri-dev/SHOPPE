@extends('layouts.storefront')

@section('title', __('Keranjang Belanja') . ' - PUTRIJALVA')

@section('content')
<div class="container cart-page">
  <h1 class="cart-page-title">{{ __('Keranjang Belanja') }}</h1>

  <div class="cart-page-layout">
    <!-- Left Column: Cart Items -->
    <div class="cart-items-column">
      <div class="cart-items-list" id="cartTableContainer">
        <!-- Rendered dynamically by cart.js -->
      </div>
    </div>

    <!-- Right Column: Cart Totals -->
    <div class="cart-totals-section">
      <h2 class="cart-totals-heading">{{ __('Cart totals') }}</h2>

      <div class="cart-totals-table">
        <div class="cart-totals-row">
          <span class="cart-totals-label">{{ __('SUBTOTAL') }}</span>
          <span class="cart-totals-val" id="cartPageSubtotal">$ 0.00</span>
        </div>
        <div class="cart-totals-row">
          <span class="cart-totals-label">{{ __('SHIPPING') }}</span>
          <span class="cart-totals-val">{{ __('Free standard shipping on orders over $50') }}</span>
        </div>
      </div>

      <div class="cart-totals-divider"></div>

      <div class="cart-grand-total-row">
        <span class="cart-grand-total-label">{{ __('TOTAL') }}</span>
        <span class="cart-grand-total-val" id="cartPageTotal">$ 0.00</span>
      </div>

      @guest
        <div style="background: #FDFBF7; border: 1px solid #EFE8DC; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 12.5px; color: #6B7280; line-height: 1.5;">
          <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; color: #111827; margin-bottom: 2px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span>{{ __('Checkout Cepat Tanpa Login') }}</span>
          </div>
          {{ __('Anda dapat langsung memesan sebagai Tamu (Guest) tanpa harus login. Cukup lengkapi data penerima dan nomor kontak aktif saat checkout.') }}
        </div>
      @endguest

      <a href="{{ route('checkout.index') }}" class="btn-proceed-checkout">
        {{ __('PROCEED TO CHECKOUT') }}
      </a>
    </div>
  </div>
</div>
@endsection
