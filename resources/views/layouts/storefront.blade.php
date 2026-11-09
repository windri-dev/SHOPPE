<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="user-auth" content="{{ auth()->check() ? '1' : '0' }}">
  <meta name="login-url" content="{{ route('login') }}">
  <title>@yield('title', 'PUTRI.J - Modern Luxury Jewelry & Accessories')</title>
  <meta name="description" content="@yield('meta_description', 'Discover handcrafted minimalist jewelry, earrings, necklaces, and artisan accessories.')">
  <meta name="theme-color" content="#A18A68">

  <!-- User Auth State for Client Scripts -->
  <script>
    window.ShoppeUser = {
      isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
      loginUrl: "{{ route('login') }}"
    };
  </script>

  <!-- Technical SEO & Indexability -->
  <link rel="canonical" href="{{ url()->current() }}">
  <meta name="robots" content="@yield('robots', 'index, follow')">

  <!-- Google Fonts: DM Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

  <!-- Stylesheets via Vite -->
  @vite(['resources/css/shoppe.css'])
  @stack('styles')
</head>
<body>
  <!-- Header Component -->
  <x-header />

  <!-- Main Content -->
  <main class="main-content" id="mainContent">
    @yield('content')
  </main>

  <!-- Cart Drawer Component -->
  <x-cart-drawer />

  <!-- Quick View Modal Component -->
  <div class="modal-backdrop" id="quickViewModal" role="dialog" aria-modal="true" aria-labelledby="modalProductTitle">
    <div class="quickview-modal">
      <button type="button" class="modal-close-btn" id="closeQuickView" aria-label="Close quick view">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <div class="modal-image-col">
        <img src="{{ asset('images/product-1.png') }}" alt="Product preview" id="modalProductImg" width="400" height="400">
      </div>
      <div class="modal-info-col">
        <h2 class="modal-product-title" id="modalProductTitle">Jewelry Piece</h2>
        <div class="modal-product-price" id="modalProductPrice">$ 20.00</div>
        <p class="modal-product-desc" id="modalProductDesc"></p>
        <div class="modal-actions">
          <button type="button" class="btn btn-primary" id="modalAddToCartBtn">Add To Cart</button>
          <a href="{{ route('shop.index') }}" class="btn btn-outline" id="modalViewDetailLink">View Details</a>
        </div>
      </div>
    </div>
  </div>

  <!-- LiveChat Concierge Component (Hanya tampil di Halaman Depan / Home) -->
  @if(request()->routeIs('home'))
    <x-livechat />
  @endif

  <!-- Toast Notification Container -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- Footer Component -->
  <x-footer />

  <!-- Global Scripts via Vite -->
  @vite(['resources/js/slider.js', 'resources/js/cart.js', 'resources/js/livechat.js', 'resources/js/app.js'])
  @stack('scripts')
</body>
</html>
