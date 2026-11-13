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
    <h1 class="overview-title">{{ $isId ? 'Ringkasan Penjualan' : 'Sales Overview' }}</h1>
    <div style="display: flex; align-items: center; gap: 10px;">
      <button type="button" class="date-range-picker-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <span>{{ $isId ? '10 April 2026 - 11 Mei 2026' : 'April 10, 2026 - May 11, 2026' }}</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
      </button>
    </div>
  </div>

  <!-- 2. BARIS 4 KARTU STATISTIK METRIK (Total Sales, New Customer, Komisi / Return, Total Revenue) -->
  <section class="stats-four-grid" id="overview">
    
    <!-- Card 1: Total Sales -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Total Penjualan' : 'Total Sales' }}</span>
        <div class="stat-widget-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value">2500</div>
        <span class="trend-pill-green">
          &uarr; 4.9%
        </span>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Bulan lalu: 2.345' : 'Last month: 2345' }}</span>
        <span style="font-size: 11px; font-weight: 600; color: #10B981;">
          <span style="display: none;">Total Omzet</span>{{ $isId ? 'Total Omzet' : 'Total Revenue' }}
        </span>
      </div>
    </div>

    <!-- Card 2: New Customer -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Pelanggan Baru' : 'New Customers' }}</span>
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
        <div class="stat-widget-value">{{ $metrics['customers_count'] ?? 110 }}</div>
        <span class="trend-pill-coral">
          &uarr; 7.5%
        </span>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Bulan lalu: 89' : 'Last month: 89' }}</span>
        <span style="font-size: 11px; color: #98A2B3;">{{ $isId ? 'Member aktif' : 'Active members' }}</span>
      </div>
    </div>

    <!-- Card 3: Return Products / Komisi Bersih Toko (Input vs Output) -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Retur Produk' : 'Return Products' }}</span>
        <div class="stat-widget-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="21 8 21 21 3 21 3 8"></polyline>
            <rect x="1" y="3" width="22" height="5"></rect>
            <line x1="10" y1="12" x2="14" y2="12"></line>
          </svg>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value">72</div>
        <span class="trend-pill-coral">
          &darr; 6.0%
        </span>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Bulan lalu: 60' : 'Last month: 60' }}</span>
        <span style="font-size: 11px; font-weight: 600; color: #A18A68;" title="{{ $isId ? 'Laba Komisi: $' : 'Commission Profit: $' }}{{ number_format($metrics['financial']['net_commission'] ?? 0, 2) }}">
          {{ $isId ? 'Komisi' : 'Commission' }}: ${{ number_format($metrics['financial']['net_commission'] ?? 0, 0) }}
        </span>
      </div>
    </div>

    <!-- Card 4: Total Revenue -->
    <div class="stat-widget-card">
      <div class="stat-widget-header">
        <span class="stat-widget-title">{{ $isId ? 'Total Pendapatan' : 'Total Revenue' }}</span>
        <div class="stat-widget-icon">
          <span style="font-weight: 700; font-size: 16px; color: #344054;">$</span>
        </div>
      </div>
      <div class="stat-widget-main">
        <div class="stat-widget-value">$8,220.64</div>
      </div>
      <div class="stat-widget-footer">
        <span>{{ $isId ? 'Bulan lalu: $620,00' : 'Last month: $620.00' }}</span>
        <span style="font-size: 11px; color: #10B981; font-weight: 600;">{{ $isId ? 'Terverifikasi' : 'Verified' }}</span>
      </div>
    </div>
  </section>

  <!-- 3. BARIS DUA DIAGRAM (Revenue analytics & Total Income Stacked Chart) -->
  <section class="charts-split-grid">
    
    <!-- Chart 1: Revenue analytics (Bar Chart dengan Tooltip $22,430) -->
    <div class="chart-box-card">
      <div class="chart-box-header">
        <div>
          <h2 class="chart-box-title">{{ $isId ? 'Analisis Pendapatan' : 'Revenue Analytics' }}</h2>
        </div>
        <div>
          <button type="button" class="chart-filter-select">
            {{ $isId ? 'Minggu Ini' : 'This Week' }} &or;
          </button>
        </div>
      </div>

      <div style="position: relative; width: 100%; height: 260px;">
        <!-- Floating Highlight Pill Badge in Luxury Gold #A18A68 -->
        <div style="position: absolute; top: 12px; left: 35%; transform: translateX(-50%); background: #A18A68; color: #FFFFFF; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; box-shadow: 0 4px 10px rgba(161, 138, 104, 0.35); pointer-events: none; z-index: 10; display: flex; align-items: center; justify-content: center;">
          $22,430
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
          <input type="text" placeholder="{{ $isId ? 'Cari pesanan...' : 'Search orders...' }}" aria-label="{{ $isId ? 'Cari pesanan' : 'Search orders' }}">
        </div>

        <!-- Sort Pill -->
        <button type="button" class="orders-sort-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="7 15 12 20 17 15"></polyline>
            <polyline points="17 9 12 4 7 9"></polyline>
          </svg>
          <span>{{ $isId ? 'Urutkan' : 'Sort by' }}</span>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>
      </div>
    </div>

    <div class="admin-table-wrap" style="border: none; overflow-x: auto;">
      <table class="finexy-table">
        <thead>
          <tr>
            <th style="width: 32px;"><input type="checkbox" style="cursor: pointer; border-radius: 4px;"></th>
            <th>{{ $isId ? 'No. Pesanan' : 'Order ID' }}</th>
            <th>{{ $isId ? 'Tanggal' : 'Date' }}</th>
            <th>{{ $isId ? 'Pelanggan' : 'Customer' }}</th>
            <th>{{ $isId ? 'Kategori' : 'Category' }}</th>
            <th>{{ $isId ? 'Status' : 'Status' }}</th>
            <th>{{ $isId ? 'Item' : 'Items' }}</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody>
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
            <tr>
              <td><input type="checkbox" style="cursor: pointer; border-radius: 4px;"></td>
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
              <td style="text-align: right; font-weight: 700; color: #101828;">
                $ {{ number_format((float) $order->grand_total, 2) }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 32px; color: #667085;">
                {{ $isId ? 'Belum ada pesanan terbaru.' : 'No recent orders found.' }}
              </td>
            </tr>
          @endforelse
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
        labels: {!! json_encode($isId ? ['Jum', 'Sab', 'Min', 'Sen', 'Sel', 'Rab', 'Kam'] : ['Fri', 'Sat', 'Sun', 'Mon', 'Thu', 'Wen', 'Thus']) !!},
        datasets: [{
          label: '{{ $isId ? 'Pendapatan' : 'Revenue' }}',
          data: [18, 12, 22.43, 14, 18, 24, 17],
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
              label: function(ctx) { return '$' + (ctx.raw * 1000).toLocaleString(); }
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
            max: 30,
            grid: { color: '#F2F4F7' },
            ticks: {
              stepSize: 5,
              font: { size: 12, family: 'DM Sans, sans-serif' },
              color: '#707070',
              callback: function(val) { return val + 'k'; }
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
        labels: {!! json_encode($isId ? ['00', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu'] : ['00', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']) !!},
        datasets: [
          {
            label: '{{ $isId ? 'Beban Operasional' : 'Loss' }}',
            data: [0, 18, 22, 25, 20, 28, 32, 24, 26],
            backgroundColor: '#111111',
            borderRadius: 6,
            barPercentage: 0.55,
            stack: 'incomeStack'
          },
          {
            label: '{{ $isId ? 'Laba Bersih' : 'Profit' }}',
            data: [0, 15, 18, 17, 16, 21, 23, 16, 17],
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
              label: function(ctx) { return ctx.dataset.label + ': $' + (ctx.raw * 1000).toLocaleString(); }
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
            max: 50,
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
});
</script>
@endpush
