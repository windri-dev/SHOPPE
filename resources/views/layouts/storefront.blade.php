<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SHOPPE - Modern Luxury Jewelry & Accessories')</title>
  <meta name="description" content="@yield('meta_description', 'Discover handcrafted minimalist jewelry, earrings, necklaces, and artisan accessories.')">
  <meta name="theme-color" content="#A18A68">

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
  <main class="main-content">
    @yield('content')
  </main>

  <!-- Cart Drawer Component -->
  <x-cart-drawer />

  <!-- LiveChat Concierge Component -->
  <x-livechat />

  <!-- Toast Notification Container -->
  <div class="toast-container" id="toastContainer" aria-live="polite"></div>

  <!-- Footer Component -->
  <x-footer />

  <!-- Global Scripts via Vite -->
  @vite(['resources/js/slider.js', 'resources/js/cart.js', 'resources/js/livechat.js', 'resources/js/app.js'])
  @stack('scripts')
</body>
</html>
