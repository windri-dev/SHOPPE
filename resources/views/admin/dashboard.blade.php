@extends('layouts.admin')

@section('title', 'Master Dashboard - SHOPPE Admin')
@section('page_title', 'Master Store Overview')

@section('content')
<!-- KPI Cards -->
<div class="kpi-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
  <div class="kpi-card" style="background: #fff; border: 1px solid #E0E0E0; padding: 24px; border-radius: 6px;">
    <div style="font-size: 12px; color: #777; text-transform: uppercase; font-weight: 600;">Total Omzet</div>
    <div style="font-size: 26px; font-weight: bold; color: #111; margin-top: 8px;">
      $ {{ number_format($metrics['total_revenue'], 2) }}
    </div>
  </div>
  <div class="kpi-card" style="background: #fff; border: 1px solid #E0E0E0; padding: 24px; border-radius: 6px;">
    <div style="font-size: 12px; color: #777; text-transform: uppercase; font-weight: 600;">Total Pesanan</div>
    <div style="font-size: 26px; font-weight: bold; color: #111; margin-top: 8px;">
      {{ $metrics['total_orders'] }}
    </div>
  </div>
  <div class="kpi-card" style="background: #fff; border: 1px solid #E0E0E0; padding: 24px; border-radius: 6px;">
    <div style="font-size: 12px; color: #777; text-transform: uppercase; font-weight: 600;">Produk Aktif</div>
    <div style="font-size: 26px; font-weight: bold; color: #111; margin-top: 8px;">
      {{ $metrics['active_products'] }}
    </div>
  </div>
  <div class="kpi-card" style="background: #fff; border: 1px solid #E0E0E0; padding: 24px; border-radius: 6px;">
    <div style="font-size: 12px; color: #777; text-transform: uppercase; font-weight: 600;">Pesan Belum Dibaca</div>
    <div style="font-size: 26px; font-weight: bold; color: #A18A68; margin-top: 8px;">
      {{ $metrics['pending_inquiries'] }}
    </div>
  </div>
</div>

<!-- Catalog Inventory Section -->
<div class="admin-section" style="background: #fff; border: 1px solid #E0E0E0; border-radius: 6px; padding: 24px; margin-bottom: 30px;">
  <h2 style="font-size: 18px; margin-bottom: 20px;">Katalog & Manajemen Stok Perhiasan</h2>
  <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <thead>
      <tr style="border-bottom: 2px solid #eee; text-align: left;">
        <th style="padding: 10px 0;">Produk</th>
        <th style="padding: 10px 0;">SKU</th>
        <th style="padding: 10px 0;">Kategori</th>
        <th style="padding: 10px 0;">Harga</th>
        <th style="padding: 10px 0;">Stok</th>
        <th style="padding: 10px 0; text-align: right;">Aksi Stok</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $prod)
        <tr style="border-bottom: 1px solid #f0f0f0;">
          <td style="padding: 12px 0;">
            <strong>{{ $prod->name }}</strong>
            @if($prod->badge)
              <span style="font-size: 11px; padding: 2px 6px; background: #eee; border-radius: 3px; margin-left: 6px;">{{ $prod->badge }}</span>
            @endif
          </td>
          <td style="padding: 12px 0; color: #666;">{{ $prod->sku }}</td>
          <td style="padding: 12px 0;">{{ $prod->category->name ?? '-' }}</td>
          <td style="padding: 12px 0;">{{ $prod->formatted_price }}</td>
          <td style="padding: 12px 0;">
            @if($prod->stock > 0)
              <span style="color: #2E7D32; font-weight: 600;">{{ $prod->stock }} pcs</span>
            @else
              <span style="color: #C62828; font-weight: 600;">Habis</span>
            @endif
          </td>
          <td style="padding: 12px 0; text-align: right;">
            <form action="{{ route('admin.products.toggle_stock', $prod) }}" method="POST" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-outline" style="font-size: 12px; padding: 5px 10px;">
                {{ $prod->stock > 0 ? 'Tandai Habis' : 'Isi Stok (15)' }}
              </button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
