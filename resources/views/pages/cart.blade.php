@extends('layouts.storefront')

@section('title', 'Shopping Cart - SHOPPE')

@section('content')
<div class="container page-container">
  <h1 class="page-title text-center" style="margin-bottom: 40px;">Shopping Cart</h1>
  <div class="cart-page-layout">
    <div class="cart-items-column" id="cartTableContainer">
      <!-- Injected by cart.js -->
    </div>
    <div class="cart-totals-column">
      <div class="cart-totals-card">
        <h3>Cart totals</h3>
        <div class="totals-row">
          <span>SUBTOTAL</span>
          <span id="cartPageSubtotal">$ 0.00</span>
        </div>
        <div class="totals-row grand-total">
          <span>TOTAL</span>
          <span id="cartPageTotal">$ 0.00</span>
        </div>
        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">PROCEED TO CHECKOUT</a>
      </div>
    </div>
  </div>
</div>
@endsection
