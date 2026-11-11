@extends('layouts.admin')

@section('title', 'Master Dashboard - PUTRI.J Admin')
@section('page_title', 'Master Store Overview')

@section('content')
<!-- 1. KPI Stat Cards -->
<section id="overview" class="admin-stats-grid">
  <div class="admin-stat-card">
    <div class="admin-stat-top">
      <span class="admin-stat-label">Total Omzet</span>
      <div class="admin-stat-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
      </div>
    </div>
    <div class="admin-stat-value">$ {{ number_format((float) $metrics['total_revenue'], 2) }}</div>
    <div class="admin-stat-trend">
      <span class="trend-up">Aktif</span>
      <span class="trend-neutral">Pesanan terverifikasi</span>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="admin-stat-top">
      <span class="admin-stat-label">Total Pesanan</span>
      <div class="admin-stat-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line></svg>
      </div>
    </div>
    <div class="admin-stat-value">{{ $metrics['total_orders'] }}</div>
    <div class="admin-stat-trend">
      <span class="trend-neutral">Transaksi masuk</span>
    </div>
  </div>

  <div class="admin-stat-card">
    <div class="admin-stat-top">
      <span class="admin-stat-label">Produk Aktif</span>
      <div class="admin-stat-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline></svg>
      </div>
    </div>
    <div class="admin-stat-value">{{ $metrics['active_products'] }}</div>
    <div class="admin-stat-trend">
      <span class="trend-neutral">Koleksi katalog</span>
    </div>
  </div>

  <a href="{{ route('admin.livechat') }}" class="admin-stat-card" style="text-decoration: none; color: inherit; display: block; transition: transform 0.2s, box-shadow 0.2s;">
    <div class="admin-stat-top">
      <span class="admin-stat-label">Pesan Belum Dibaca</span>
      <div class="admin-stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
      </div>
    </div>
    <div class="admin-stat-value" style="color: var(--color-accent);">{{ $metrics['pending_inquiries'] }}</div>
    <div class="admin-stat-trend">
      <span class="trend-up" style="color: #A18A68; font-weight: 600;">Buka Menu Live Chat &rarr;</span>
      <span class="trend-neutral">Tiket Concierge</span>
    </div>
  </a>
</section>

<!-- 2. Recent Orders Management Section -->
<section id="orders-section" class="admin-card" style="margin-bottom: 28px;">
  <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
      <h2 class="admin-card-title">Pesanan Pelanggan Terbaru</h2>
      <p style="font-size: 13px; color: #6B7280; margin: 4px 0 0 0;">Ringkasan transaksi terbaru yang masuk ke sistem.</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="table-gold-link" style="font-size: 13px; font-weight: 600; text-decoration: none;">
      Kelola Semua Pesanan &rarr;
    </a>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>No. Order</th>
          <th>Pelanggan</th>
          <th>Tanggal</th>
          <th>Total</th>
          <th>Status</th>
          <th>Ubah Status</th>
          <th style="text-align: right;">Resi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td><strong>{{ $order->order_number }}</strong></td>
            <td>
              <div style="font-weight: 500;">{{ $order->customer_name }}</div>
              <div style="font-size: 12px; color: #777;">{{ $order->customer_email }}</div>
            </td>
            <td>{{ $order->created_at?->format('d M Y H:i') }}</td>
            <td><strong>{{ $order->formatted_grand_total }}</strong></td>
            <td>
              <span class="badge-status {{ $order->status->value }}">
                {{ $order->status->label() }}
              </span>
            </td>
            <td>
              <form action="{{ route('admin.orders.status', $order) }}" method="POST" style="display: inline-flex; gap: 6px; align-items: center;">
                @csrf
                <select name="status" class="status-select" onchange="this.form.submit()" aria-label="Ubah status {{ $order->order_number }}">
                  @foreach(\App\Enums\OrderStatus::cases() as $statusCase)
                    <option value="{{ $statusCase->value }}" {{ $order->status === $statusCase ? 'selected' : '' }}>
                      {{ $statusCase->label() }}
                    </option>
                  @endforeach
                </select>
              </form>
            </td>
            <td style="text-align: right;">
              <a href="{{ route('order.show', $order->order_number) }}" target="_blank" class="table-action-btn">
                Detail &nearr;
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 32px; color: #777;">Belum ada pesanan masuk.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<!-- 3. Catalog Inventory Section -->
<section id="catalog-section" class="admin-card" style="margin-bottom: 28px;">
  <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
      <h2 class="admin-card-title">Katalog &amp; Stok Busana (Preview)</h2>
      <p style="font-size: 13px; color: #6B7280; margin: 4px 0 0 0;">Monitoring cepat status stok dan ketersediaan etalase.</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="table-gold-link" style="font-size: 13px; font-weight: 600; text-decoration: none;">
      Kelola Semua Katalog &amp; Stok ({{ $metrics['active_products'] }}) &rarr;
    </a>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Produk</th>
          <th>SKU</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Stok</th>
          <th style="text-align: right;">Aksi Stok</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $prod)
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <img src="{{ asset($prod->primary_image) }}" alt="{{ $prod->name }}" class="top-product-thumb" width="44" height="44">
                <div>
                  <strong>{{ $prod->name }}</strong>
                  @if($prod->badge)
                    <span style="font-size: 11px; padding: 2px 6px; background: #F0F0F0; border-radius: 3px; margin-left: 6px;">{{ $prod->badge }}</span>
                  @endif
                </div>
              </div>
            </td>
            <td style="color: #666;">{{ $prod->sku }}</td>
            <td>{{ $prod->category->name ?? '-' }}</td>
            <td><strong>{{ $prod->formatted_price }}</strong></td>
            <td>
              @if($prod->stock > 0)
                <span class="badge-status instock">{{ $prod->stock }} pcs</span>
              @else
                <span class="badge-status outofstock">Habis</span>
              @endif
            </td>
            <td style="text-align: right;">
              <form action="{{ route('admin.products.toggle_stock', $prod) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="table-action-btn">
                  {{ $prod->stock > 0 ? 'Tandai Habis' : 'Isi Stok (15)' }}
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

<!-- 4. Ringkasan Live Chat & Concierge -->
<section id="inquiries-section" class="admin-card" style="margin-bottom: 28px;">
  <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
      <h2 class="admin-card-title">Live Chat &amp; Pertanyaan Pelanggan Terbaru</h2>
      <p style="font-size: 13px; color: #6B7280; margin: 4px 0 0 0;">
        Pantau percakapan aktif dari pelanggan. Untuk ruang chat lengkap bergaya messenger, buka menu khusus Live Chat.
      </p>
    </div>
    <a href="{{ route('admin.livechat') }}" class="table-action-btn" style="background: #1B1B18; color: #FFFFFF; padding: 9px 18px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
      Buka Menu Live Chat Penuh &rarr;
    </a>
  </div>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Pelanggan</th>
          <th>Topik / Subjek</th>
          <th>Pesan Terakhir</th>
          <th>Channel</th>
          <th>Status</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inquiries->take(5) as $inq)
          @php
            $lastMsg = $inq->messages->last();
          @endphp
          <tr>
            <td>
              <div style="font-weight: 600; color: #111827;">{{ $inq->name }}</div>
              <div style="font-size: 12px; color: #6B7280;">{{ $inq->email }}</div>
            </td>
            <td>
              <span style="font-weight: 500;">{{ $inq->subject ?: 'Konsultasi Busana' }}</span>
              <div style="font-size: 11px; color: #9CA3AF;">#{{ $inq->ticket_number }}</div>
            </td>
            <td style="max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #4B5563; font-size: 12.5px;">
              {{ $lastMsg ? $lastMsg->message : '-' }}
            </td>
            <td>
              <span class="chat-header-channel-tag {{ $inq->channel === 'contact_form' ? 'form' : '' }}">
                {{ $inq->channel === 'contact_form' ? 'Formulir' : 'Livechat' }}
              </span>
            </td>
            <td>
              <span class="badge-status {{ $inq->status->value === 'unread' ? 'waiting' : 'answered' }}">
                {{ $inq->status->label() }}
              </span>
            </td>
            <td style="text-align: right;">
              <a href="{{ route('admin.livechat') }}" class="table-gold-link" style="font-weight: 600; font-size: 12.5px;">
                Buka Chat &rarr;
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; padding: 32px; color: #6B7280;">Belum ada tiket pertanyaan pelanggan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
