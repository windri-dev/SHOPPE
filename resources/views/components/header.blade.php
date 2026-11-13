@php
  $initialWishlistCount = auth()->check() ? auth()->user()->wishlist()->count() : count((array) session('wishlist', []));
@endphp
<header class="site-header" id="siteHeader">
  <div class="container header-container">
    <!-- Left: Hamburger Menu (Mobile) & Brand Logo -->
    <div class="header-left">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle navigation menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <a href="{{ route('home') }}" class="header-logo" aria-label="PUTRIJALVA Home">
        <img src="{{ asset('images/logo.png') }}" alt="PUTRIJALVA" class="header-logo-image" width="160" height="46">
      </a>
    </div>

    <!-- Center: Main Navigation Links -->
    <nav class="desktop-nav" aria-label="Main Navigation">
      <ul class="nav-links">
        <li class="nav-item"><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*') ? 'active' : '' }}">{{ __('Shop') }}</a></li>
        <li class="nav-item"><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('Journal & Blog') }}</a></li>
        <li class="nav-item"><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('Our Story') }}</a></li>
        <li class="nav-item"><a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}">{{ __('FAQ') }}</a></li>
        <li class="nav-item"><a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.*') ? 'active' : '' }}">{{ __('Contact') }}</a></li>
        @if(auth()->check() && auth()->user()->isAdmin())
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link" title="{{ __('Dashboard Admin') }}">
              <span class="admin-nav-dot"></span>
              {{ __('Dashboard Admin') }}
            </a>
          </li>
        @endif
      </ul>
    </nav>

    <!-- Right: Action Icons (Language, Search, Cart, User) -->
    <div class="header-actions">
      <!-- Language Switcher -->
      <div class="lang-switcher" aria-label="{{ __('Pilih Bahasa') }}">
        <a href="{{ route('locale.switch', 'id') }}" class="lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}" title="Bahasa Indonesia">ID</a>
        <span class="lang-divider">/</span>
        <a href="{{ route('locale.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" title="English">EN</a>
      </div>

      <span class="nav-divider" aria-hidden="true"></span>

      <!-- Search Icon Toggle -->
      <button class="icon-btn" id="searchToggle" aria-label="{{ __('Search store') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>

      <!-- Wishlist / Favorites Icon -->
      <a href="{{ route('wishlist.index') }}" class="icon-btn wishlist-btn {{ request()->routeIs('wishlist.*') ? 'active' : '' }}" aria-label="{{ __('Wishlist') }}" title="{{ __('Wishlist') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        <span class="wishlist-badge-count" id="wishlistBadgeCount" style="{{ $initialWishlistCount > 0 ? '' : 'display: none;' }}">{{ $initialWishlistCount }}</span>
      </a>

      <!-- Shopping Cart Icon -->
      <button class="icon-btn cart-btn" data-action="open-cart" aria-label="{{ __('Shopping Cart') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span class="cart-badge-count" id="cartBadgeCount" style="display: none;">0</span>
      </button>

      <!-- User / Account Icon -->
      <a href="{{ route('account.index') }}" class="icon-btn account-btn {{ request()->routeIs('account.*') ? 'active' : '' }}" aria-label="{{ __('My Account') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </a>
    </div>
  </div>

  <!-- Include Search Overlay Component -->
  <x-search-overlay />
</header>

<!-- Mobile Navigation Drawer -->
<div class="drawer-backdrop" id="mobileNavBackdrop"></div>
<aside class="mobile-nav-drawer" id="mobileNavDrawer" aria-label="Mobile Navigation Menu">
  <div class="mobile-nav-header">
    <div style="display: flex; flex-direction: column; gap: 4px;">
      <a href="{{ route('home') }}" class="header-logo" aria-label="PUTRIJALVA Home" style="display: inline-block;">
        <img src="{{ asset('images/logo.png') }}" alt="PUTRIJALVA" class="header-logo-image" style="height: 38px; width: auto;">
      </a>
      <span style="font-size: 10px; color: #8C8273; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">{{ __('Contemporary Fashion & Apparel') }}</span>
    </div>
    <button class="drawer-close-btn" id="mobileNavClose" aria-label="Close Navigation">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>
  <ul class="mobile-nav-links">
    <li><a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('shop.index') }}" class="mobile-nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">{{ __('Shop Collection') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('wishlist.index') }}" class="mobile-nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">{{ __('Wishlist') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('blog.index') }}" class="mobile-nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('Blog') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('about') }}" class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">{{ __('Our Story') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('faq') }}" class="mobile-nav-link {{ request()->routeIs('faq') ? 'active' : '' }}">{{ __('FAQ & Size Guide') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('account.index') }}" class="mobile-nav-link {{ request()->routeIs('account.*') ? 'active' : '' }}">{{ __('My Account') }} <span>&rarr;</span></a></li>
    <li><a href="{{ route('contact.index') }}" class="mobile-nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}">{{ __('Contact & Concierge') }} <span>&rarr;</span></a></li>
    @if(auth()->check() && auth()->user()->isAdmin())
      <li>
        <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link" style="color: #A18A68; font-weight: 700;">
          {{ __('Dashboard Admin') }} <span>&rarr;</span>
        </a>
      </li>
    @endif
  </ul>
  <!-- Language Switcher in Mobile Drawer -->
  <div class="mobile-lang-box">
    <span class="mobile-lang-title">{{ __('Language') }}</span>
    <div class="mobile-lang-options">
      <a href="{{ route('locale.switch', 'id') }}" class="mobile-lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}">🇮🇩 ID (Indonesia)</a>
      <a href="{{ route('locale.switch', 'en') }}" class="mobile-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">🇬🇧 EN (English)</a>
    </div>
  </div>
  <div class="mobile-nav-footer">
    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-block" style="height: 44px; font-size: 12.5px; border-radius: 8px;">{{ __('Shop Collection') }}</a>
    <p style="font-size: 11px; color: #8C8C8C; text-align: center; margin-top: 12px;">Premium Fabrics &bull; Complimentary Size Exchange 7 Days</p>
  </div>
</aside>
