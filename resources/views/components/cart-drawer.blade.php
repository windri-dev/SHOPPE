<div class="drawer-backdrop" id="drawerBackdrop"></div>
<aside class="cart-drawer" id="cartDrawer" aria-label="Shopping Bag Drawer">
  <div class="drawer-header">
    <div class="drawer-title-wrap">
      <h3 class="drawer-title">Shopping bag</h3>
      <span class="drawer-item-count" id="drawerItemCount">0 items</span>
    </div>
    <button class="drawer-close-btn" data-action="close-cart" aria-label="Close Shopping Bag">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <div class="drawer-body" id="cartDrawerBody">
    <!-- Dynamic item injection -->
  </div>

  <div class="drawer-footer">
    <div class="subtotal-row">
      <span class="subtotal-label" id="subtotalLabel">Subtotal (0 items)</span>
      <span class="subtotal-price" id="cartSubtotal">$ 0.00</span>
    </div>
    <div class="drawer-footer-actions">
      <a href="{{ route('cart.index') }}" class="btn btn-outline btn-block">View Cart</a>
      @guest
        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">Login untuk Checkout</a>
      @else
        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">Proceed To Checkout</a>
      @endguest
    </div>
  </div>
</aside>
