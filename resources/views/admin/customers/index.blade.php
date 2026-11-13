@extends('layouts.admin')

@section('title', 'Direktori Pelanggan - PUTRIJALVA Admin')
@section('page_title', 'Daftar & Direktori Pelanggan')

@section('content')
<div class="admin-customers-page" style="display: flex; flex-direction: column; gap: 20px;">
  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        Direktori Pelanggan &amp; Member
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Kelola profil pelanggan terdaftar, riwayat akumulasi belanja (LTV), frekuensi pemesanan, dan kontak langsung.
      </p>
    </div>
    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.orders.index') }}" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151;">
        Lihat Pesanan &rarr;
      </a>
      <a href="{{ route('admin.dashboard') }}" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151;">
        &larr; Dashboard
      </a>
    </div>
  </div>

  <!-- Stats Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #A18A68;">
      <span style="font-size: 12px; font-weight: 600; color: #8F7655; text-transform: uppercase; letter-spacing: 0.05em;">Pelanggan Terdaftar</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ number_format($stats['total_customers']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Akun pembeli aktif di sistem</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #10B981;">
      <span style="font-size: 12px; font-weight: 600; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">Pembeli Berulang (Active)</span>
      <div style="font-size: 26px; font-weight: 700; color: #10B981; margin-top: 4px;">{{ number_format($stats['active_buyers']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Memiliki minimal 1 riwayat transaksi</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #111827;">
      <span style="font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em;">Total Akun Pengguna</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ number_format($stats['total_members']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Termasuk staf &amp; tim internal</div>
    </div>
  </div>

  <!-- Search & Toolbar -->
  <div class="admin-card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <div style="font-size: 14px; font-weight: 600; color: #374151;">
        Menampilkan {{ $customers->total() }} Pelanggan
      </div>

      <!-- Search Box -->
      <form action="{{ route('admin.customers.index') }}" method="GET" style="display: flex; gap: 8px; min-width: 280px;">
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari Nama, Email, No. HP, Alamat..."
          class="chat-search-input"
          style="padding-left: 12px; width: 280px;"
        >
        <button type="submit" class="table-action-btn" style="background: #1B1B18; color: #fff; padding: 6px 14px;">
          Cari
        </button>
        @if(request('search'))
          <a href="{{ route('admin.customers.index') }}" class="table-action-btn" style="background: #E5E7EB; color: #374151; padding: 6px 10px; text-decoration: none;">
            Reset
          </a>
        @endif
      </form>
    </div>
  </div>

  <!-- Customers Data Table -->
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Profil Pelanggan</th>
            <th>Kontak &amp; Alamat</th>
            <th>Jumlah Pesanan</th>
            <th>Total Akumulasi Belanja</th>
            <th>Bergabung Sejak</th>
            <th style="text-align: right;">Aksi Pelanggan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customers as $customer)
            @php
              $initials = collect(explode(' ', $customer->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
              $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone ?? '');
              if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
              }
            @endphp
            <tr>
              <td>
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 40px; height: 40px; border-radius: 50%; background: #FAF9F6; border: 1.5px solid #E5E7EB; color: #A18A68; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    {{ $initials ?: 'CU' }}
                  </div>
                  <div>
                    <div style="font-weight: 600; color: #111827; font-size: 14px;">
                      {{ $customer->name }}
                      @if($customer->username)
                        <span style="font-size: 11px; color: #9CA3AF; font-weight: 500;">(&#64;{{ $customer->username }})</span>
                      @endif
                    </div>
                    <div style="font-size: 12px; color: #6B7280;">{{ $customer->email }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div style="font-size: 13px; color: #374151; font-weight: 500;">
                  {{ $customer->phone ?? 'Belum ada nomor telepon' }}
                  @if($customer->address_label)
                    <span style="font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 4px; background: #FEF3C7; color: #92400E; margin-left: 4px;">{{ $customer->address_label }}</span>
                  @endif
                </div>
                <div style="font-size: 12px; color: #6B7280; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $customer->address }}">
                  {{ implode(', ', array_filter([$customer->address, $customer->city, $customer->province])) ?: 'Alamat belum diatur' }}
                </div>
              </td>
              <td>
                <div style="display: inline-flex; align-items: center; gap: 6px;">
                  <span style="font-size: 14px; font-weight: 700; color: #111827;">{{ $customer->orders_count }}</span>
                  <span style="font-size: 12px; color: #6B7280;">order</span>
                </div>
              </td>
              <td>
                <div style="font-weight: 700; color: #111827; font-size: 14px;">
                  $ {{ number_format((float) ($customer->orders_sum_grand_total ?? 0), 2) }}
                </div>
                @if($customer->orders_count > 0)
                  <span style="font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 4px; background: #D1FAE5; color: #047857;">
                    Verified Buyer
                  </span>
                @else
                  <span style="font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 4px; background: #F3F4F6; color: #6B7280;">
                    Belum Transaksi
                  </span>
                @endif
              </td>
              <td>
                <div style="font-size: 13px; color: #374151;">
                  {{ $customer->created_at ? $customer->created_at->format('d M Y') : '-' }}
                </div>
                <div style="font-size: 11px; color: #9CA3AF;">
                  {{ $customer->created_at ? $customer->created_at->diffForHumans() : '' }}
                </div>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px; align-items: center;">
                  <!-- View Customer's Orders in Admin Orders -->
                  <a
                    href="{{ route('admin.orders.index', ['search' => $customer->email]) }}"
                    class="table-action-btn"
                    style="background: #FAF9F6; color: #111827; border: 1px solid #D1D5DB; padding: 5px 10px; font-size: 11px; text-decoration: none;"
                    title="Lihat riwayat pesanan milik {{ $customer->name }}"
                  >
                    Pesanan ({{ $customer->orders_count }})
                  </a>

                  <!-- WhatsApp Direct -->
                  @if($cleanPhone)
                    <a
                      href="https://wa.me/{{ $cleanPhone }}"
                      target="_blank"
                      rel="noopener"
                      class="table-action-btn"
                      style="background: #25D366; color: #fff; padding: 5px 8px; text-decoration: none; border-radius: 6px;"
                      title="Kirim pesan WhatsApp ke {{ $customer->phone }}"
                    >
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </a>
                  @endif

                  <!-- Email Direct -->
                  <a
                    href="mailto:{{ $customer->email }}?subject=Halo {{ urlencode($customer->name) }} - Shoppe Concierge"
                    class="table-action-btn"
                    style="background: #F3F4F6; color: #374151; padding: 5px 8px; text-decoration: none; border-radius: 6px;"
                    title="Kirim email ke {{ $customer->email }}"
                  >
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 48px 24px; color: #9CA3AF;">
                <div style="font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 4px;">Tidak ada pelanggan yang ditemukan</div>
                <div style="font-size: 13px;">Silakan periksa kata kunci pencarian Anda.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid #E5E7EB; background: #FAF9F6; display: flex; justify-content: center;">
        {{ $customers->links('partials.pagination') }}
      </div>
    @endif
  </div>
</div>
@endsection

