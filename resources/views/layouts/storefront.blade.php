<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="user-auth" content="{{ auth()->check() ? '1' : '0' }}">
  <meta name="login-url" content="{{ route('login') }}">
  <title>@yield('title', 'PUTRI.J - Contemporary Ready-to-Wear Fashion & Apparel')</title>
  <meta name="description" content="@yield('meta_description', 'Koleksi busana kontemporer, midi dress elegan, atasan sutra, blazer tailored, dan pakaian wanita modern dari butik PUTRI.J.')">
  <meta name="theme-color" content="#A18A68">

  <!-- Favicons & Touch Icons -->
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
  <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">

  <!-- User Auth State for Client Scripts -->
  <script>
    window.ShoppeUser = {
      isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
      loginUrl: "{{ route('login') }}"
    };
    window.ShoppeLocale = "{{ app()->getLocale() }}";
    window.__trans = {
      emptyCartDrawer: "{{ __('Your shopping bag is currently empty.') }}",
      emptyCartPage: "{{ __('Keranjang belanja Anda saat ini kosong.') }}",
      startShopping: "{{ __('Mulai Belanja') }}",
      exploreShop: "{{ __('Explore Shop') }}",
      emptyCheckout: "{{ __('Keranjang Anda kosong.') }}",
      chooseProduct: "{{ __('Pilih produk di katalog') }}",
      subtotal: "{{ __('Subtotal') }}",
      items: "{{ __('items') }}",
      item: "{{ __('item') }}",
      remove: "{{ __('Hapus') }}",
      qty: "{{ __('Qty') }}"
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
      <div class="modal-image-col" style="position: relative;">
        <img src="{{ asset('images/product-1.png') }}" alt="Product preview" id="modalProductImg" width="400" height="400">
        <div id="modalProductVideoBox" style="display: none; position: absolute; inset: 0; background: #0B0F17; border-radius: 8px; overflow: hidden; align-items: center; justify-content: center;">
          <video id="modalProductVideo" controls playsinline style="width: 100%; height: 100%; object-fit: contain;"></video>
          <iframe id="modalProductIframe" style="width: 100%; height: 100%; border: none;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <button type="button" id="modalVideoToggleBtn" style="display: none; position: absolute; bottom: 12px; right: 12px; background: rgba(17, 24, 39, 0.88); color: #FFFFFF; font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 9999px; border: 1px solid rgba(255, 255, 255, 0.25); cursor: pointer; align-items: center; gap: 6px; z-index: 10;" aria-label="Toggle video preview">
          <svg viewBox="0 0 24 24" fill="#A18A68" width="12" height="12"><path d="M8 5v14l11-7z"/></svg>
          <span id="modalVideoToggleLabel">Lihat Video</span>
        </button>
      </div>
      <div class="modal-info-col">
        <h2 class="modal-product-title" id="modalProductTitle">Apparel Piece</h2>
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
