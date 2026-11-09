<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'PUTRI.J - Master Store Dashboard')</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Google Fonts: DM Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">

  <!-- Stylesheets via Vite -->
  @vite(['resources/css/shoppe.css'])
  @stack('styles')
</head>
<body class="admin-body">
  <div class="dashboard-app" id="dashboardApp">
    <!-- Admin Sidebar Navigation -->
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="admin-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
          <span class="admin-brand-logo">PUTRI.J</span>
          <span class="admin-badge-role">STORE MANAGER</span>
        </a>
      </div>

      <nav class="admin-nav" aria-label="Admin Navigation">
        <div class="admin-nav-group-title">Main Management</div>
        <ul class="admin-nav-list">
          <li class="admin-nav-item">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Overview &amp; KPI
              </span>
            </a>
          </li>
          <li class="admin-nav-item">
            <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                Manajemen Pesanan
              </span>
              @if(($sidebarMetrics['pending_orders'] ?? 0) > 0)
                <span class="admin-badge-count" style="background: #F59E0B; color: #fff; font-size: 11px; padding: 2px 7px; border-radius: 9999px; font-weight: 700;" title="{{ $sidebarMetrics['pending_orders'] }} Menunggu Pembayaran">{{ $sidebarMetrics['pending_orders'] }}</span>
              @endif
            </a>
          </li>
          <li class="admin-nav-item">
            <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                Katalog &amp; Stok
              </span>
              @if(($sidebarMetrics['low_stock_products'] ?? 0) > 0)
                <span class="admin-badge-count" style="background: #EF4444; color: #fff; font-size: 11px; padding: 2px 7px; border-radius: 9999px; font-weight: 700;" title="{{ $sidebarMetrics['low_stock_products'] }} Stok Menipis / Habis">{{ $sidebarMetrics['low_stock_products'] }}</span>
              @endif
            </a>
          </li>
          <li class="admin-nav-item">
            <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"></path></svg>
                Kategori Produk
              </span>
              <span class="admin-badge-count" style="background: #FAF9F6; color: #4B5563; font-size: 11px; padding: 2px 7px; border-radius: 9999px; font-weight: 700; border: 1px solid #D1D5DB;">{{ $sidebarMetrics['categories_count'] ?? 0 }}</span>
            </a>
          </li>
          <li class="admin-nav-item">
            <a href="{{ route('admin.blogs.index') }}" class="admin-nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                Jurnal &amp; Artikel
              </span>
              <span class="admin-badge-count" style="background: #FAF9F6; color: #4B5563; font-size: 11px; padding: 2px 7px; border-radius: 9999px; font-weight: 700; border: 1px solid #D1D5DB;">{{ $sidebarMetrics['blogs_count'] ?? 0 }}</span>
            </a>
          </li>
          <li class="admin-nav-item">
            <a href="{{ route('admin.customers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Daftar Pelanggan
              </span>
            </a>
          </li>
          <li class="admin-nav-item">
            <a href="{{ route('admin.livechat') }}" class="admin-nav-link {{ request()->routeIs('admin.livechat*') ? 'active' : '' }}">
              <span class="admin-nav-link-left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                Live Chat Concierge
              </span>
              @if(($sidebarMetrics['unread_inquiries'] ?? 0) > 0)
                <span class="admin-badge-count" style="background: #EF4444; color: #fff; font-size: 11px; padding: 2px 7px; border-radius: 9999px; font-weight: 700;">{{ $sidebarMetrics['unread_inquiries'] }}</span>
              @endif
            </a>
          </li>
        </ul>
      </nav>

      <div class="admin-sidebar-footer">
        <div class="admin-user-info">
          <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
          <div class="admin-user-meta">
            <span class="admin-user-name">{{ auth()->user()->name ?? 'Store Manager' }}</span>
            <span class="admin-user-role">{{ auth()->user()->email ?? 'admin@shoppe.com' }}</span>
          </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button
            type="submit"
            class="table-action-btn btn-danger"
            title="Keluar dari sesi admin"
            style="background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.16); padding: 6px 10px;"
          >
            Keluar
          </button>
        </form>
      </div>
    </aside>

    <!-- Admin Main Content -->
    <main class="admin-main">
      <header class="admin-topbar">
        <div class="admin-topbar-left">
          <button type="button" class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle Menu">&#9776;</button>
          <div class="admin-page-title">
            <h1 style="font-size: 20px;">@yield('page_title', 'Master Store Management')</h1>
          </div>
        </div>
        <div class="admin-topbar-right">
          <a href="{{ route('home') }}" target="_blank" class="btn-view-store">
            Buka Etalase Toko &nearr;
          </a>
        </div>
      </header>

      <div class="admin-content">
        @if(session('success'))
          <div style="background: #E8F5E9; border: 1px solid #C8E6C9; color: #2E7D32; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-size: 14px;">
            &#10003; {{ session('success') }}
          </div>
        @endif

        @if(session('error'))
          <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-size: 14px;">
            &#9888; {{ session('error') }}
          </div>
        @endif

        @if($errors->any())
          <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-size: 14px;">
            {{ $errors->first() }}
          </div>
        @endif

        @yield('content')
      </div>
    </main>
  </div>

  @stack('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const mobileToggle = document.getElementById('adminMobileToggle');
      const sidebar = document.getElementById('adminSidebar');
      if (mobileToggle && sidebar) {
        mobileToggle.addEventListener('click', function () {
          sidebar.classList.toggle('open');
        });
      }
    });
  </script>
</body>
</html>
