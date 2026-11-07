<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SHOPPE - Master Store Dashboard')</title>
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
    <!-- Admin Sidebar -->
    <aside class="dashboard-sidebar">
      <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">SHOPPE</a>
        <span class="sidebar-role-badge">STORE MANAGER</span>
      </div>

      <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item active">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
          Dashboard
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}</div>
          <div class="user-meta">
            <span class="user-name">{{ auth()->user()->name ?? 'Store Manager' }}</span>
            <span class="user-email">{{ auth()->user()->email ?? 'admin@shoppe.com' }}</span>
          </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn-sidebar-logout" title="Keluar dari sesi admin">
            Keluar (Logout) &rarr;
          </button>
        </form>
      </div>
    </aside>

    <!-- Admin Main Body -->
    <main class="dashboard-main">
      <header class="dashboard-topbar">
        <div class="topbar-title">
          <h1>@yield('page_title', 'Master Store Management')</h1>
          <p>Pantau omzet, inventaris, dan status pesanan toko perhiasan Anda.</p>
        </div>
        <div class="topbar-actions">
          <a href="{{ route('home') }}" target="_blank" class="btn btn-outline" style="font-size: 13px;">
            Buka Etalase Toko &nearr;
          </a>
        </div>
      </header>

      @if(session('success'))
        <div style="background: #E8F5E9; color: #2E7D32; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
          ✓ {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div style="background: #FFEBEE; color: #C62828; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
          ⚠ {{ session('error') }}
        </div>
      @endif

      <div class="dashboard-content">
        @yield('content')
      </div>
    </main>
  </div>

  @vite(['resources/js/dashboard.js'])
  @stack('scripts')
</body>
</html>
