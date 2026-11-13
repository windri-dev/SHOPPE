@php
  $isId = app()->getLocale() === 'id';
@endphp

@extends('layouts.admin')

@section('title', ($isId ? 'Ringkasan Penjualan & Dashboard' : 'Sales Overview & Dashboard') . ' - PUTRIJALVA Admin')
@section('page_title', $isId ? 'Ringkasan & Dashboard Toko' : 'Master Store Overview & Dashboard')

@push('styles')
<style>
  .sales-overview-wrap {
    display: flex;
    flex-direction: column;
    gap: 22px;
    max-width: 1440px;
    margin: 0 auto;
  }

  /* Page Header */
  .overview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
  }

  .overview-title {
    font-size: 26px;
    font-weight: 800;
    color: #101828;
    margin: 0;
    letter-spacing: -0.03em;
  }

  .date-range-picker-btn {
    background: #FFFFFF;
    border: 1px solid #D0D5DD;
    border-radius: 12px;
    padding: 9px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #344054;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
    transition: all 0.2s ease;
  }

  .date-range-picker-btn:hover {
    border-color: #98A2B3;
    background: #F9FAFB;
  }

  /* Custom Dropdowns */
  .custom-dropdown-wrap {
    position: relative;
    display: inline-block;
  }

  .custom-dropdown-menu {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    min-width: 220px;
    background: #FFFFFF;
    border: 1px solid #EAECF0;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(16, 24, 40, 0.1);
    padding: 6px;
    z-index: 100;
    display: none;
    flex-direction: column;
    gap: 2px;
    animation: dropdownFadeIn 0.15s ease-out forwards;
  }

  .custom-dropdown-menu.show {
    display: flex;
  }

  @keyframes dropdownFadeIn {
    from {
      opacity: 0;
      transform: translateY(-4px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .custom-dropdown-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #344054;
    border-radius: 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    text-align: left;
    width: 100%;
    transition: all 0.15s ease;
  }

  .custom-dropdown-item:hover {
    background: #F9FAFB;
    color: #101828;
  }

  .custom-dropdown-item.active {
    background: #FAF7F2;
    color: #8C7554;
    font-weight: 700;
  }

  .custom-dropdown-item svg {
    color: #8C7554;
  }

  /* 4 Top Stat Cards Grid */
  .stats-four-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }

  .stat-widget-card {
    background: #FFFFFF;
    border: 1px solid #EAECF0;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .stat-widget-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 24, 40, 0.06);
  }

  .stat-widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }

  .stat-widget-title {
    font-size: 13.5px;
    font-weight: 600;
    color: #475467;
  }

  .stat-widget-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #F9FAFB;
    border: 1px solid #EAECF0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #344054;
  }

  .stat-widget-main {
    display: flex;
    align-items: baseline;
    gap: 10px;
    flex-wrap: wrap;
  }

  .stat-widget-value {
    font-size: 30px;
    font-weight: 800;
    color: #101828;
    letter-spacing: -0.03em;
    line-height: 1.1;
  }

  .trend-pill-green {
    background: #ECFDF5;
    color: #027A48;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .trend-pill-coral {
    background: #FAF7F2;
    color: #8C7554;
    border: 1px solid #EFE8DC;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .stat-widget-footer {
    margin-top: 14px;
    font-size: 12px;
    color: #667085;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  /* Two Charts Row */
  .charts-split-grid {
    display: grid;
    grid-template-columns: 1.55fr 1fr;
    gap: 18px;
    align-items: stretch;
  }

  .chart-box-card {
    background: #FFFFFF;
    border: 1px solid #EAECF0;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .chart-box-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
  }

  .chart-box-title {
    font-size: 16px;
    font-weight: 700;
    color: #101828;
    margin: 0;
  }

  .chart-box-sub {
    font-size: 12px;
    color: #667085;
    margin: 3px 0 0 0;
  }

  .chart-filter-select {
    background: #FFFFFF;
    border: 1px solid #D0D5DD;
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #344054;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
  }

  /* Table Card */
  .orders-table-card {
    background: #FFFFFF;
    border: 1px solid #EAECF0;
    border-radius: 18px;
    box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
    overflow: hidden;
  }

  .orders-table-header {
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    border-bottom: 1px solid #EAECF0;
  }

  .orders-search-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #FFFFFF;
    border: 1px solid #D0D5DD;
    border-radius: 10px;
    padding: 7px 12px;
    font-size: 12.5px;
    color: #667085;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
  }

  .orders-search-pill input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 12.5px;
    color: #101828;
    width: 140px;
  }

  .orders-sort-pill {
    background: #FFFFFF;
    border: 1px solid #D0D5DD;
    border-radius: 10px;
    padding: 7px 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: #344054;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
  }

  /* Modern Flat Table */
  .finexy-table {
    width: 100%;
    border-collapse: collapse;
  }

  .finexy-table th {
    padding: 14px 24px;
    font-size: 12px;
    font-weight: 600;
    color: #667085;
    border-bottom: 1px solid #EAECF0;
    text-align: left;
    background: #FFFFFF;
  }

  .finexy-table td {
    padding: 16px 24px;
    font-size: 13.5px;
    color: #344054;
    border-bottom: 1px solid #F2F4F7;
    vertical-align: middle;
  }

  .finexy-table tbody tr:hover td {
    background-color: #FAFAFA;
  }

  .finexy-table tbody tr:last-child td {
    border-bottom: none;
  }

  .order-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
  }

  .status-pending {
    background: #FAF7F2;
    color: #8C7554;
    border: 1px solid #EFE8DC;
  }

  .status-completed {
    background: #ECFDF5;
    color: #027A48;
  }

  .status-processing {
    background: #EFF8FF;
    color: #175CD3;
  }

  @media (max-width: 1100px) {
    .stats-four-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    .charts-split-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 640px) {
    .stats-four-grid {
      grid-template-columns: 1fr;
    }
    .orders-table-header {
      flex-direction: column;
      align-items: stretch;
    }
  }
</style>
@endpush

@section('content')
<div class="sales-overview-wrap">

  <!-- 1. HEADER HALAMAN: Sales Overview & Date Range Selector -->
  <div class="overview-header">
    <div>
      <h1 class="overview-title">{{ $isId ? 'Ringkasan Penjualan & Performa Toko' : 'Sales Overview & Store Performance' }}</h1>
      <p style="font-size: 13px; color: #667085; margin: 4px 0 0 0;">{{ $isId ? 'Pantau arus pendapatan, pesanan masuk, pelanggan, dan metrik butik secara real-time.' : 'Monitor revenue flow, incoming orders, customers, and boutique metrics in real-time.' }}</p>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
      <div class="custom-dropdown-wrap">
        <button type="button" class="date-range-picker-btn" id="dateRangeToggle" aria-haspopup="true" aria-expanded="false">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span id="dateRangeLabel">{{ now()->subDays(30)->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} &ndash; {{ now()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} (30 {{ $isId ? 'Hari' : 'Days' }})</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="dateRangeArrow"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
        <div class="custom-dropdown-menu" id="dateRangeMenu" role="menu">
          <button type="button" class="custom-dropdown-item" data-range="7" data-label="{{ now()->subDays(7)->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} &ndash; {{ now()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} (7 {{ $isId ? 'Hari' : 'Days' }})">
            <span>{{ $isId ? '7 Hari Terakhir' : 'Last 7 Days' }}</span>
          </button>
          <button type="button" class="custom-dropdown-item active" data-range="30" data-label="{{ now()->subDays(30)->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} &ndash; {{ now()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} (30 {{ $isId ? 'Hari' : 'Days' }})">
            <span>{{ $isId ? '30 Hari Terakhir (Default)' : 'Last 30 Days (Default)' }}</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          </button>
          <button type="button" class="custom-dropdown-item" data-range="month" data-label="{{ now()->startOfMonth()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} &ndash; {{ now()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} ({{ $isId ? 'Bulan Ini' : 'This Month' }})">
            <span>{{ $isId ? 'Bulan Ini (' . now()->locale('id')->isoFormat('MMMM Y') . ')' : 'This Month (' . now()->isoFormat('MMMM Y') . ')' }}</span>
          </button>
          <button type="button" class="custom-dropdown-item" data-range="year" data-label="{{ now()->startOfYear()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} &ndash; {{ now()->locale($isId ? 'id' : 'en')->isoFormat('D MMM Y') }} ({{ now()->year }})">
            <span>{{ $isId ? 'Tahun Ini (' . now()->year . ')' : 'This Year (' . now()->year . ')' }}</span>
          </button>
          <button type="button" class="custom-dropdown-item" data-range="all" data-label="{{ $isId ? 'Semua Periode Transaksi' : 'All Time Transaction' }}">
            <span>{{ $isId ? 'Semua Waktu' : 'All Time' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. BARIS 4 KARTU STATISTIK METRIK (Total Sales, New Customer, Komisi / Return, Total Revenue) -->
  <section class="stats-four-grid" id="overview">
    
    <!-- Card 1: Total Sales -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Total Pesanan Masuk' : 'Total Orders' }}</span>
        <div class="stat-widget-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value">{{ number_format($metrics['total_orders'] ?? 0) }}</div>
        <span class="trend-pill-green">
          &uarr; {{ $metrics['completed_orders_count'] ?? 0 }} {{ $isId ? 'selesai' : 'done' }}
        </span>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Pesanan Selesai / Terkirim' : 'Delivered / Completed' }}: <strong>{{ number_format($metrics['completed_orders_count'] ?? 0) }}</strong></span>
        <span style="font-size: 11px; font-weight: 600; color: #10B981;">
          <span style="display: none;">Total Omzet</span>{{ $isId ? 'Total Omzet' : 'Total Revenue' }}
        </span>
      </div>
    </div>

    <!-- Card 2: New Customer -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Total Pelanggan Terdaftar' : 'Registered Customers' }}</span>
        <div class="stat-widget-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value">{{ number_format($metrics['customers_count'] ?? 0) }}</div>
        <span class="trend-pill-coral">
          &uarr; {{ $isId ? 'Aktif' : 'Active' }}
        </span>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Akun Pelanggan' : 'Customer Accounts' }}: <strong>{{ $isId ? 'Aktif' : 'Active' }}</strong></span>
        <span style="font-size: 11px; color: #A18A68; font-weight: 600;">{{ $isId ? 'Member Butik' : 'Boutique Members' }}</span>
      </div>
    </div>

    <!-- Card 3: Return Products / Komisi Bersih Toko (Input vs Output) -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Pesanan Batal & Retur' : 'Cancelled & Returns' }}</span>
        <div class="stat-widget-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="21 8 21 21 3 21 3 8"></polyline>
            <rect x="1" y="3" width="22" height="5"></rect>
            <line x1="10" y1="12" x2="14" y2="12"></line>
          </svg>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value">{{ number_format($metrics['status_breakdown']['cancelled'] ?? 0) }}</div>
        <span class="trend-pill-coral">
          {{ $metrics['status_breakdown']['cancelled'] ?? 0 }} {{ $isId ? 'dibatalkan' : 'cancelled' }}
        </span>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Menunggu Proses' : 'Pending Action' }}: <strong>{{ number_format($metrics['status_breakdown']['pending'] ?? 0) }}</strong></span>
        <span style="font-size: 11px; font-weight: 600; color: #A18A68;" title="{{ $isId ? 'Laba Komisi:' : 'Commission Profit:' }} {{ \App\Services\CurrencyService::formatPrice($metrics['financial']['net_commission'] ?? 0) }}">
          {{ $isId ? 'Komisi Bersih' : 'Net Commission' }}: {{ \App\Services\CurrencyService::formatPrice($metrics['financial']['net_commission'] ?? 0) }}
        </span>
      </div>
    </div>

    <!-- Card 4: Total Revenue -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Total Pendapatan (Omzet)' : 'Total Revenue' }}</span>
        <div class="stat-widget-icon">
          <span style="font-weight: 700; font-size: 14px; color: #344054;">{{ $isId ? 'Rp' : '$' }}</span>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value" style="font-size: 22px;">{{ \App\Services\CurrencyService::formatPrice($metrics['total_revenue'] ?? 0) }}</div>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Arus Masuk Kotor' : 'Gross Inflow' }}: <strong>{{ \App\Services\CurrencyService::formatPrice($metrics['financial']['total_input'] ?? 0) }}</strong></span>
        <span style="font-size: 11px; color: #10B981; font-weight: 600;">Margin {{ $metrics['financial']['commission_rate'] ?? 0 }}%</span>
      </div>
    </div>
  </section>

  <!-- 3. BARIS DUA DIAGRAM (Revenue analytics & Total Income Stacked Chart) -->
  <section class="charts-split-grid">
    
    <!-- Chart 1: Revenue analytics (Bar Chart dengan Tooltip) -->
    <div class="chart-box-card">
      <div class="chart-box-header">
        <div>
          <h2 class="chart-box-title">{{ $isId ? 'Analisis Pendapatan Harian' : 'Daily Revenue Analytics' }}</h2>
          <p class="chart-box-sub">{{ $isId ? 'Arus kas masuk bruto 7 hari terakhir' : 'Gross revenue flow over the last 7 days' }}</p>
        </div>
        <div>
          <div class="custom-dropdown-wrap">
            <button type="button" class="chart-filter-select" id="chartFilterToggle" aria-haspopup="true" aria-expanded="false" style="display: inline-flex; align-items: center; gap: 6px;">
              <span id="chartFilterLabel">{{ $isId ? '7 Hari Terakhir' : 'Last 7 Days' }}</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="custom-dropdown-menu" id="chartFilterMenu" role="menu" style="min-width: 170px;">
              <button type="button" class="custom-dropdown-item active" data-chart-range="7">
                <span>{{ $isId ? '7 Hari Terakhir' : 'Last 7 Days' }}</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
              <button type="button" class="custom-dropdown-item" data-chart-range="14">
                <span>{{ $isId ? '14 Hari Terakhir' : 'Last 14 Days' }}</span>
              </button>
              <button type="button" class="custom-dropdown-item" data-chart-range="30">
                <span>{{ $isId ? '30 Hari Terakhir' : 'Last 30 Days' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div style="position: relative; width: 100%; height: 260px;">
        <!-- Floating Highlight Pill Badge in Luxury Gold #A18A68 -->
        <div style="position: absolute; top: 12px; left: 35%; transform: translateX(-50%); background: #A18A68; color: #FFFFFF; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; box-shadow: 0 4px 10px rgba(161, 138, 104, 0.35); pointer-events: none; z-index: 10; display: flex; align-items: center; justify-content: center;">
          {{ \App\Services\CurrencyService::formatPrice($metrics['sales_chart']['highest_day_input'] ?? 0) }}
          <span style="position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #A18A68;"></span>
        </div>
        <canvas id="revenueAnalyticsChart"></canvas>
      </div>
    </div>

    <!-- Chart 2: Total Income (Profit vs Loss Stacked Bar Chart) -->
    <div class="chart-box-card">
      <div class="chart-box-header">
        <div>
          <h2 class="chart-box-title">{{ $isId ? 'Total Pemasukan' : 'Total Income' }}</h2>
          <p class="chart-box-sub">{{ $isId ? 'Lihat rincian laba dan beban pada periode berjalan' : 'View your income in a certain period of time' }}</p>
        </div>
        <div style="display: flex; gap: 12px; font-size: 12px; font-weight: 600; align-items: center;">
          <span style="display: flex; align-items: center; gap: 5px; color: #A18A68;">
            <span style="width: 8px; height: 8px; background: #A18A68; border-radius: 50%;"></span> {{ $isId ? 'Laba Bersih' : 'Profit' }}
          </span>
          <span style="display: flex; align-items: center; gap: 5px; color: #111111;">
            <span style="width: 8px; height: 8px; background: #111111; border-radius: 50%;"></span> {{ $isId ? 'Beban Operasional' : 'Loss' }}
          </span>
        </div>
      </div>

      <div style="position: relative; width: 100%; height: 260px;">
        <canvas id="totalIncomeChart"></canvas>
      </div>
    </div>
  </section>

  <!-- 4. TABEL RECENT ORDERS -->
  <section class="orders-table-card">
    <div class="orders-table-header">
      <h2 style="font-size: 16px; font-weight: 700; color: #101828; margin: 0;">{{ $isId ? 'Pesanan Terbaru' : 'Recent Orders' }}</h2>
      <div style="display: flex; align-items: center; gap: 10px;">
        <!-- Search Input -->
        <div class="orders-search-pill">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#667085" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" id="orderSearchInput" placeholder="{{ $isId ? 'Cari pesanan...' : 'Search orders...' }}" aria-label="{{ $isId ? 'Cari pesanan' : 'Search orders' }}">
        </div>

        <!-- Sort Pill Dropdown -->
        <div class="custom-dropdown-wrap">
          <button type="button" class="orders-sort-pill" id="ordersSortToggle" aria-haspopup="true" aria-expanded="false">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="7 15 12 20 17 15"></polyline>
              <polyline points="17 9 12 4 7 9"></polyline>
            </svg>
            <span id="ordersSortLabel">{{ $isId ? 'Urutkan' : 'Sort by' }}</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </button>
          <div class="custom-dropdown-menu" id="ordersSortMenu" role="menu" style="min-width: 200px;">
            <button type="button" class="custom-dropdown-item active" data-sort="newest">
              <span>{{ $isId ? 'Terbaru (Default)' : 'Newest First' }}</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </button>
            <button type="button" class="custom-dropdown-item" data-sort="oldest">
              <span>{{ $isId ? 'Terlama' : 'Oldest First' }}</span>
            </button>
            <button type="button" class="custom-dropdown-item" data-sort="highest">
              <span>{{ $isId ? 'Nominal Tertinggi' : 'Highest Amount' }}</span>
            </button>
            <button type="button" class="custom-dropdown-item" data-sort="lowest">
              <span>{{ $isId ? 'Nominal Terendah' : 'Lowest Amount' }}</span>
            </button>
            <button type="button" class="custom-dropdown-item" data-sort="customer">
              <span>{{ $isId ? 'Nama Pelanggan (A-Z)' : 'Customer Name (A-Z)' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="admin-table-wrap" style="border: none; overflow-x: auto;">
      <table class="finexy-table" id="ordersTable">
        <thead>
          <tr>
            <th style="width: 32px;"><input type="checkbox" id="selectAllOrders" style="cursor: pointer; border-radius: 4px;"></th>
            <th>{{ $isId ? 'No. Pesanan' : 'Order ID' }}</th>
            <th>{{ $isId ? 'Tanggal' : 'Date' }}</th>
            <th>{{ $isId ? 'Pelanggan' : 'Customer' }}</th>
            <th>{{ $isId ? 'Kategori' : 'Category' }}</th>
            <th>{{ $isId ? 'Status' : 'Status' }}</th>
            <th>{{ $isId ? 'Item' : 'Items' }}</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody id="ordersTableBody">
          @forelse($orders as $order)
            @php
              $firstItem = $order->items->first();
              $categoryName = $firstItem?->product?->category?->name ?? ($isId ? 'Crop Top & Busana Wanita' : 'Crop Top & Womenswear');
              $itemsCount = $order->items->sum('quantity') ?: 1;
              $statusClass = match($order->status->value) {
                'delivered' => 'status-completed',
                'shipped' => 'status-processing',
                'processing' => 'status-processing',
                default => 'status-pending'
              };
              $statusLabel = match($order->status->value) {
                'delivered' => $isId ? 'Selesai' : 'Completed',
                'shipped' => $isId ? 'Dikirim' : 'Shipped',
                'processing' => $isId ? 'Diproses' : 'Processing',
                default => $isId ? 'Menunggu' : 'Pending'
              };
            @endphp
            <tr class="order-row"
                data-order-number="{{ strtolower($order->order_number) }}"
                data-customer="{{ strtolower($order->customer_name) }}"
                data-category="{{ strtolower($categoryName) }}"
                data-status="{{ strtolower($order->status->value) }}"
                data-amount="{{ (float) $order->grand_total }}"
                data-timestamp="{{ $order->created_at?->timestamp ?? 0 }}">
              <td><input type="checkbox" class="order-select-item" style="cursor: pointer; border-radius: 4px;"></td>
              <td>
                <span style="font-weight: 600; color: #101828;">#{{ substr(str_replace('-', '', $order->order_number), -6) }}</span>
              </td>
              <td style="color: #475467; font-size: 13px;">
                {{ $order->created_at?->format('d M Y') }}
              </td>
              <td>
                <span style="font-weight: 500; color: #101828;">{{ $order->customer_name }}</span>
              </td>
              <td style="color: #475467;">
                {{ $categoryName }}
              </td>
              <td>
                <span class="order-status-pill {{ $statusClass }}">
                  {{ $statusLabel }}
                </span>
              </td>
              <td style="color: #475467;">
                {{ $itemsCount }} {{ $isId ? 'Item' : ($itemsCount > 1 ? 'Items' : 'Item') }}
              </td>
              <td style="text-align: right; font-weight: 700; color: #101828; white-space: nowrap;">
                {{ \App\Services\CurrencyService::formatPrice($order->grand_total) }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 32px; color: #667085;">
                {{ $isId ? 'Belum ada pesanan terbaru.' : 'No recent orders found.' }}
              </td>
            </tr>
          @endforelse
          <tr id="noOrdersMatchRow" style="display: none;">
            <td colspan="8" style="text-align: center; padding: 32px; color: #667085;">
              {{ $isId ? 'Tidak ada pesanan yang sesuai pencarian / filter.' : 'No orders matched your search / filter criteria.' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // 1. Chart Revenue analytics (Vertical Bar Chart matching reference image)
  const ctxRevenue = document.getElementById('revenueAnalyticsChart');
  if (ctxRevenue && typeof Chart !== 'undefined') {
    new Chart(ctxRevenue, {
      type: 'bar',
      data: {
        labels: {!! json_encode($metrics['sales_chart']['labels'] ?? ['Jum', 'Sab', 'Min', 'Sen', 'Sel', 'Rab', 'Kam']) !!},
        datasets: [{
          label: '{{ $isId ? 'Pendapatan' : 'Revenue' }}',
          data: {!! json_encode($metrics['sales_chart']['inputs'] ?? [18, 12, 22.43, 14, 18, 24, 17]) !!},
          backgroundColor: '#A18A68',
          hoverBackgroundColor: '#8C7554',
          borderRadius: 14,
          borderSkipped: false,
          barPercentage: 0.55,
          categoryPercentage: 0.7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#111111',
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) { return '{{ $isId ? 'Rp ' : '$' }}' + Number(ctx.raw).toLocaleString(); }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { size: 12, family: 'DM Sans, sans-serif' },
              color: '#707070'
            }
          },
          y: {
            beginAtZero: true,
            grid: { color: '#F2F4F7' },
            ticks: {
              font: { size: 12, family: 'DM Sans, sans-serif' },
              color: '#707070',
              callback: function(val) { return Number(val) >= 1000 ? (val / 1000) + 'k' : val; }
            }
          }
        }
      }
    });
  }

  // 2. Chart Total Income (Stacked Bar Chart: Profit vs Loss matching reference image)
  const ctxIncome = document.getElementById('totalIncomeChart');
  if (ctxIncome && typeof Chart !== 'undefined') {
    new Chart(ctxIncome, {
      type: 'bar',
      data: {
        labels: {!! json_encode($metrics['monthly_income']['months'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu']) !!},
        datasets: [
          {
            label: '{{ $isId ? 'Beban Operasional' : 'Loss / Expense' }}',
            data: {!! json_encode($metrics['monthly_income']['loss'] ?? [14, 18, 19, 16, 21, 26, 19, 21]) !!},
            backgroundColor: '#111111',
            borderRadius: 6,
            barPercentage: 0.55,
            stack: 'incomeStack'
          },
          {
            label: '{{ $isId ? 'Laba Bersih' : 'Net Profit' }}',
            data: {!! json_encode($metrics['monthly_income']['profit'] ?? [18, 24, 26, 22, 28, 34, 25, 27]) !!},
            backgroundColor: '#A18A68',
            hoverBackgroundColor: '#8C7554',
            borderRadius: 6,
            barPercentage: 0.55,
            stack: 'incomeStack'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#111111',
            padding: 10,
            cornerRadius: 8,
            callbacks: {
              label: function(ctx) { return ctx.dataset.label + ': ' + (Number(ctx.raw) * 1000).toLocaleString(); }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              font: { size: 11, family: 'DM Sans, sans-serif' },
              color: '#707070'
            }
          },
          y: {
            beginAtZero: true,
            grid: { color: '#F2F4F7' },
            ticks: {
              stepSize: 10,
              font: { size: 11, family: 'DM Sans, sans-serif' },
              color: '#707070',
              callback: function(val) { return val + 'k'; }
            }
          }
        }
      }
    });
  }

  // 3. DROPDOWN HANDLERS & INTERACTIONS
  const dropdownWrappers = document.querySelectorAll('.custom-dropdown-wrap');

  function closeAllDropdowns() {
    document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
      menu.classList.remove('show');
    });
    document.querySelectorAll('[aria-expanded="true"]').forEach(btn => {
      btn.setAttribute('aria-expanded', 'false');
    });
  }

  // Setup click toggles for all dropdown buttons
  dropdownWrappers.forEach(wrap => {
    const toggleBtn = wrap.querySelector('button[aria-haspopup="true"]');
    const menu = wrap.querySelector('.custom-dropdown-menu');
    if (!toggleBtn || !menu) return;

    toggleBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      const isAlreadyOpen = menu.classList.contains('show');
      closeAllDropdowns();
      if (!isAlreadyOpen) {
        menu.classList.add('show');
        toggleBtn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  // Global click outside to close dropdowns
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.custom-dropdown-wrap')) {
      closeAllDropdowns();
    }
  });

  // Escape key closes dropdowns
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeAllDropdowns();
    }
  });

  // 3A. Date Range Picker Dropdown Selection
  const dateRangeMenu = document.getElementById('dateRangeMenu');
  const dateRangeLabel = document.getElementById('dateRangeLabel');
  if (dateRangeMenu && dateRangeLabel) {
    dateRangeMenu.querySelectorAll('.custom-dropdown-item').forEach(item => {
      item.addEventListener('click', function () {
        const selectedLabel = this.getAttribute('data-label') || this.querySelector('span')?.textContent;
        dateRangeLabel.textContent = selectedLabel;

        // Update active checkmark
        dateRangeMenu.querySelectorAll('.custom-dropdown-item').forEach(el => {
          el.classList.remove('active');
          el.querySelector('svg')?.remove();
        });
        this.classList.add('active');
        this.insertAdjacentHTML('beforeend', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>');
        closeAllDropdowns();
      });
    });
  }

  // 3B. Chart Filter Dropdown Selection
  const chartFilterMenu = document.getElementById('chartFilterMenu');
  const chartFilterLabel = document.getElementById('chartFilterLabel');
  if (chartFilterMenu && chartFilterLabel) {
    chartFilterMenu.querySelectorAll('.custom-dropdown-item').forEach(item => {
      item.addEventListener('click', function () {
        const text = this.querySelector('span')?.textContent;
        if (text) chartFilterLabel.textContent = text;

        // Update active checkmark
        chartFilterMenu.querySelectorAll('.custom-dropdown-item').forEach(el => {
          el.classList.remove('active');
          el.querySelector('svg')?.remove();
        });
        this.classList.add('active');
        this.insertAdjacentHTML('beforeend', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>');
        closeAllDropdowns();
      });
    });
  }

  // 3C. Orders Table Sort & Live Search
  const ordersSortMenu = document.getElementById('ordersSortMenu');
  const ordersSortLabel = document.getElementById('ordersSortLabel');
  const ordersTableBody = document.getElementById('ordersTableBody');
  const orderSearchInput = document.getElementById('orderSearchInput');
  const noOrdersMatchRow = document.getElementById('noOrdersMatchRow');
  const selectAllOrders = document.getElementById('selectAllOrders');

  // Select all checkbox
  if (selectAllOrders && ordersTableBody) {
    selectAllOrders.addEventListener('change', function () {
      const isChecked = this.checked;
      ordersTableBody.querySelectorAll('.order-select-item').forEach(cb => {
        const row = cb.closest('.order-row');
        if (row && row.style.display !== 'none') {
          cb.checked = isChecked;
        }
      });
    });
  }

  // Live Search filter
  function filterOrders() {
    if (!ordersTableBody) return;
    const query = (orderSearchInput?.value || '').trim().toLowerCase();
    const rows = ordersTableBody.querySelectorAll('.order-row');
    let visibleCount = 0;

    rows.forEach(row => {
      const orderNum = row.getAttribute('data-order-number') || '';
      const customer = row.getAttribute('data-customer') || '';
      const category = row.getAttribute('data-category') || '';
      const status = row.getAttribute('data-status') || '';

      const match = !query || orderNum.includes(query) || customer.includes(query) || category.includes(query) || status.includes(query);
      if (match) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (noOrdersMatchRow) {
      noOrdersMatchRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }
  }

  if (orderSearchInput) {
    orderSearchInput.addEventListener('input', filterOrders);
  }

  // Sort orders function
  function sortOrders(criteria) {
    if (!ordersTableBody) return;
    const rows = Array.from(ordersTableBody.querySelectorAll('.order-row'));
    if (!rows.length) return;

    rows.sort((a, b) => {
      if (criteria === 'newest') {
        return (Number(b.getAttribute('data-timestamp')) || 0) - (Number(a.getAttribute('data-timestamp')) || 0);
      }
      if (criteria === 'oldest') {
        return (Number(a.getAttribute('data-timestamp')) || 0) - (Number(b.getAttribute('data-timestamp')) || 0);
      }
      if (criteria === 'highest') {
        return (Number(b.getAttribute('data-amount')) || 0) - (Number(a.getAttribute('data-amount')) || 0);
      }
      if (criteria === 'lowest') {
        return (Number(a.getAttribute('data-amount')) || 0) - (Number(b.getAttribute('data-amount')) || 0);
      }
      if (criteria === 'customer') {
        const nameA = a.getAttribute('data-customer') || '';
        const nameB = b.getAttribute('data-customer') || '';
        return nameA.localeCompare(nameB);
      }
      return 0;
    });

    // Re-append sorted rows before noOrdersMatchRow
    rows.forEach(row => {
      if (noOrdersMatchRow) {
        ordersTableBody.insertBefore(row, noOrdersMatchRow);
      } else {
        ordersTableBody.appendChild(row);
      }
    });
  }

  if (ordersSortMenu && ordersSortLabel) {
    ordersSortMenu.querySelectorAll('.custom-dropdown-item').forEach(item => {
      item.addEventListener('click', function () {
        const sortType = this.getAttribute('data-sort');
        const text = this.querySelector('span')?.textContent;
        if (text) ordersSortLabel.textContent = text;

        // Update active checkmark
        ordersSortMenu.querySelectorAll('.custom-dropdown-item').forEach(el => {
          el.classList.remove('active');
          el.querySelector('svg')?.remove();
        });
        this.classList.add('active');
        this.insertAdjacentHTML('beforeend', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>');

        sortOrders(sortType);
        closeAllDropdowns();
      });
    });
  }
});
</script>
@endpush
