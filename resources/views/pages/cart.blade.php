@extends('layouts.storefront')

@section('title', __('Keranjang Belanja') . ' - PUTRI.J')

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
        <div style="background: #FAF8F5; border: 1px solid #EAE4DA; border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; font-size: 13px; color: #5C5243; line-height: 1.5;">
          <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #111827; margin-bottom: 4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>{{ __('Perhatian: Perlu Masuk / Mendaftar') }}</span>
          </div>
          {{ __('Anda harus masuk ke akun Anda atau mendaftar terlebih dahulu sebelum dapat melanjutkan ke proses checkout pesanan.') }}
        </div>

        <a href="{{ route('checkout.index') }}" class="btn-proceed-checkout" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          {{ __('LOGIN UNTUK CHECKOUT') }}
        </a>
      @else
        <a href="{{ route('checkout.index') }}" class="btn-proceed-checkout">
          {{ __('PROCEED TO CHECKOUT') }}
        </a>
      @endguest
    </div>
  </div>
</div>
@endsection
