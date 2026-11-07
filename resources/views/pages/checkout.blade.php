@extends('layouts.storefront')

@section('title', 'Checkout - SHOPPE')

@section('content')
<div class="container checkout-page">
  <div class="checkout-header">
    <h1 class="checkout-title">Checkout</h1>
  </div>

  @if($errors->any())
    <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 4px; margin-bottom: 30px; font-size: 14px;">
      <strong>Mohon lengkapi formulir dengan benar:</strong>
      <ul style="margin-top: 8px; padding-left: 20px;">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" class="checkout-grid">
    @csrf

    <!-- Billing Details -->
    <div class="checkout-billing-form">
      <h2 class="checkout-section-title">Billing Details</h2>
      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->name ?? '') }}" placeholder="First Name *" class="checkout-input" required>
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name *" class="checkout-input" required>
        </div>
      </div>
      <div class="checkout-field-wrap">
        <div class="checkout-select-wrap">
          <select name="country" class="checkout-select" required>
            <option value="Indonesia">Indonesia</option>
            <option value="Singapore">Singapore</option>
            <option value="Malaysia">Malaysia</option>
          </select>
          <span class="checkout-select-arrow">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </div>
      </div>
      <div class="checkout-field-wrap">
        <input type="text" name="street_address" value="{{ old('street_address', auth()->user()->address ?? '') }}" placeholder="Street Address *" class="checkout-input" required>
      </div>
      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="city" value="{{ old('city') }}" placeholder="Town / City *" class="checkout-input" required>
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="postcode" value="{{ old('postcode') }}" placeholder="Postcode / ZIP *" class="checkout-input" required>
        </div>
      </div>
      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="Phone *" class="checkout-input" required>
        </div>
        <div class="checkout-field-wrap">
          <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="Email Address *" class="checkout-input" required>
        </div>
      </div>
      <div class="checkout-field-wrap">
        <textarea name="order_notes" rows="3" placeholder="Order notes (optional), e.g. special notes for delivery." class="checkout-input" style="min-height: 80px; resize: vertical;">{{ old('order_notes') }}</textarea>
      </div>
    </div>

    <!-- Order Review -->
    <div class="checkout-order-col">
      <div class="checkout-order-card">
        <h2 class="checkout-section-title" style="margin-bottom: 24px;">Your Order</h2>
        <div id="checkoutItemsList">
          <!-- Populated from cart JS -->
        </div>

        <div class="checkout-payment-methods">
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="bank_transfer" class="payment-method-radio" checked>
              <span class="payment-method-name">Direct bank transfer</span>
            </label>
          </div>
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="qris" class="payment-method-radio">
              <span class="payment-method-name">QRIS Instant Payment</span>
            </label>
          </div>
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="credit_card" class="payment-method-radio">
              <span class="payment-method-name">Credit Card / Debit Online</span>
            </label>
          </div>
        </div>

        <!-- Hidden input for JSON cart items submitted by checkout form handler -->
        <div id="hiddenCartInputs"></div>

        <button type="submit" class="btn-place-order">
          PLACE ORDER
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
