@extends('layouts.storefront')

@section('title', 'Shopping Cart - PUTRI.J')

@section('content')
<div class="container cart-page">
  <h1 class="cart-page-title">Shopping Cart</h1>

  <div class="cart-page-layout">
    <!-- Left Column: Cart Items -->
    <div class="cart-items-column">
      <div class="cart-items-list" id="cartTableContainer">
        <!-- Rendered dynamically by cart.js -->
      </div>
    </div>

    <!-- Right Column: Cart Totals -->
    <div class="cart-totals-section">
      <h2 class="cart-totals-heading">Cart totals</h2>

      <div class="cart-totals-table">
        <div class="cart-totals-row">
          <span class="cart-totals-label">SUBTOTAL</span>
          <span class="cart-totals-val" id="cartPageSubtotal">$ 0.00</span>
        </div>
        <div class="cart-totals-row">
          <span class="cart-totals-label">SHIPPING</span>
          <span class="cart-totals-val">Free standard shipping on orders over $50</span>
        </div>
      </div>

      <div class="cart-totals-divider"></div>

      <div class="cart-grand-total-row">
        <span class="cart-grand-total-label">TOTAL</span>
        <span class="cart-grand-total-val" id="cartPageTotal">$ 0.00</span>
      </div>

      <a href="{{ route('checkout.index') }}" class="btn-proceed-checkout">
        PROCEED TO CHECKOUT
      </a>
    </div>
  </div>
</div>
@endsection
