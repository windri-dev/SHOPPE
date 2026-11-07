<header class="site-header" id="siteHeader">
  <div class="container header-container">
    <!-- Left: Hamburger Menu (Mobile) & Brand Logo -->
    <div class="header-left">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle navigation menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <a href="{{ route('home') }}" class="header-logo" aria-label="Shoppe Home">SHOPPE</a>
    </div>

    <!-- Right: Navigation Links & Action Icons -->
    <div class="header-right">
      <nav class="desktop-nav" aria-label="Main Navigation">
        <ul class="nav-links">
          <li class="nav-item"><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*') ? 'active' : '' }}">Shop</a></li>
          <li class="nav-item"><a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a></li>
          <li class="nav-item"><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Our Story</a></li>
          @if(auth()->check() && auth()->user()->isAdmin())
            <li class="nav-item">
              <a href="{{ route('admin.dashboard') }}" style="color: #A18A68; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #A18A68;"></span>
                Dashboard Admin
              </a>
            </li>
          @endif
        </ul>
      </nav>

      <span class="nav-divider" aria-hidden="true"></span>

      <!-- Action Icons (Search, Cart, User) -->
      <div class="header-actions">
        <!-- Search Icon Toggle -->
        <button class="icon-btn" id="searchToggle" aria-label="Search store">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </button>

        <!-- Shopping Cart Icon -->
        <button class="icon-btn cart-btn" data-action="open-cart" aria-label="Shopping Cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <span class="cart-badge-count" id="cartBadgeCount" style="display: none;">0</span>
        </button>

        <!-- User / Account Icon -->
        <a href="{{ route('account.index') }}" class="icon-btn {{ request()->routeIs('account.*') ? 'active' : '' }}" aria-label="My Account">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Include Search Overlay Component -->
  <x-search-overlay />
</header>

<!-- Mobile Navigation Drawer -->
<div class="drawer-backdrop" id="mobileNavBackdrop"></div>
<aside class="mobile-nav-drawer" id="mobileNavDrawer" aria-label="Mobile Navigation Menu">
  <div class="mobile-nav-header">
    <span class="header-logo">SHOPPE</span>
    <button class="drawer-close-btn" id="mobileNavClose" aria-label="Close Navigation">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>
  <ul class="mobile-nav-links">
    <li><a href="{{ route('shop.index') }}" class="mobile-nav-link">Shop <span>&rarr;</span></a></li>
    <li><a href="{{ route('blog.index') }}" class="mobile-nav-link">Blog <span>&rarr;</span></a></li>
    <li><a href="{{ route('about') }}" class="mobile-nav-link">Our Story <span>&rarr;</span></a></li>
    <li><a href="{{ route('account.index') }}" class="mobile-nav-link">My Account <span>&rarr;</span></a></li>
    <li><a href="{{ route('contact.index') }}" class="mobile-nav-link">Contact Us <span>&rarr;</span></a></li>
  </ul>
</aside>
