@extends('layouts.storefront')

@section('title', 'My Account - PUTRI.J')

@section('content')
<div class="container account-page">
  <div class="account-header" style="margin-bottom: 28px;">
    <h1 class="account-title">My Account</h1>
  </div>

  {{-- Flash Notifications --}}
  <div style="max-width: 960px; margin: 0 auto 24px;">
    @if(session('success'))
      <div style="background: #E8F5E9; border: 1px solid #C8E6C9; color: #2E7D32; padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if(session('info'))
      <div style="background: #FAF8F5; border: 1px solid #EAE4DA; color: #785A28; padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <span style="font-weight: 500;">{{ session('info') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px;">
        {{ $errors->first() }}
      </div>
    @endif
  </div>

  @auth
    @php
      $activeTab = session('active_tab', request('tab', 'orders'));
      $addressBook = auth()->user()->address_book;
      $bankAccount = auth()->user()->bank_account ?? [];
      $notifPrefs = auth()->user()->notification_preferences ?? ['email_order' => true, 'whatsapp_shipping' => true, 'promo_newsletter' => true];
    @endphp

    <!-- Shopee-Style Customer Account Center -->
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 28px; align-items: start; margin-bottom: 60px;" class="account-shopee-layout">
      <!-- Left Sidebar -->
      <aside style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 18px; border-bottom: 1px solid #F3F4F6; margin-bottom: 16px;">
          <div style="width: 48px; height: 48px; border-radius: 9999px; background: #111827; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0;">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
          </div>
          <div style="overflow: hidden;">
            <div style="font-weight: 700; font-size: 15px; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Halo, {{ auth()->user()->name }}</div>
            <div style="font-size: 12px; color: #6B7280;">&#64;{{ auth()->user()->username ?: Str::slug(auth()->user()->name, '.') }}</div>
            <span style="display: inline-block; margin-top: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 700; background: #FEF3C7; color: #92400E;">MEMBER GOLD</span>
          </div>
        </div>

        <nav style="display: flex; flex-direction: column; gap: 4px;" role="tablist" aria-label="Menu Pengaturan Akun">
          <button type="button" class="member-nav-btn {{ $activeTab === 'orders' ? 'active' : '' }}" data-member-tab="orders" style="display: flex; align-items: center; justify-content: space-between; width: 100%; text-align: left; padding: 10px 12px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-size: 13px; font-weight: 600; color: #374151; transition: all 0.15s;">
            <span style="display: flex; align-items: center; gap: 10px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
              {{ __('Pesanan Saya') }}
            </span>
            @if($orders->count() > 0)
              <span style="background: #F3F4F6; color: #111827; font-size: 11px; padding: 1px 7px; border-radius: 9999px; font-weight: 700;">{{ $orders->count() }}</span>
            @endif
          </button>

          <button type="button" class="member-nav-btn {{ $activeTab === 'profile' ? 'active' : '' }}" data-member-tab="profile" style="display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 10px 12px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-size: 13px; font-weight: 600; color: #374151; transition: all 0.15s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            {{ __('Profil & Kontak Saya') }}
          </button>

          <button type="button" class="member-nav-btn {{ $activeTab === 'addresses' ? 'active' : '' }}" data-member-tab="addresses" style="display: flex; align-items: center; justify-content: space-between; width: 100%; text-align: left; padding: 10px 12px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-size: 13px; font-weight: 600; color: #374151; transition: all 0.15s;">
            <span style="display: flex; align-items: center; gap: 10px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              {{ __('Alamat Pengiriman') }}
            </span>
            <span style="background: #F3F4F6; color: #111827; font-size: 11px; padding: 1px 7px; border-radius: 9999px; font-weight: 700;">{{ count($addressBook) }}</span>
          </button>

          <button type="button" class="member-nav-btn {{ $activeTab === 'bank' ? 'active' : '' }}" data-member-tab="bank" style="display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 10px 12px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-size: 13px; font-weight: 600; color: #374151; transition: all 0.15s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="10" y1="10" x2="23" y2="10"></line></svg>
            {{ __('Rekening & Pembayaran') }}
          </button>

          <button type="button" class="member-nav-btn {{ $activeTab === 'security' ? 'active' : '' }}" data-member-tab="security" style="display: flex; align-items: center; gap: 10px; width: 100%; text-align: left; padding: 10px 12px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-size: 13px; font-weight: 600; color: #374151; transition: all 0.15s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            {{ __('Ubah Password') }}
          </button>
        </nav>

        <div style="border-top: 1px solid #F3F4F6; margin-top: 16px; padding-top: 16px; display: flex; flex-direction: column; gap: 8px;">
          <button type="button" class="btn btn-outline open-livechat-btn" style="width: 100%; font-size: 12px; padding: 9px 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            {{ __('Chat Concierge') }}
          </button>
          <form action="{{ route('account.logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn btn-outline" style="width: 100%; font-size: 12px; padding: 9px 12px; color: #DC2626; border-color: #FCA5A5;">
              {{ __('Keluar Akun') }}
            </button>
          </form>
        </div>
      </aside>

      <!-- Right Main Content Panels -->
      <div>
        <!-- PANEL 1: PESANAN SAYA -->
        <section id="member-panel-orders" class="member-panel" style="display: {{ $activeTab === 'orders' ? 'block' : 'none' }};">
          <!-- Quick Metrics Strip -->
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px;">
            <div style="background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px 18px;">
              <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #6B7280;">Total Transaksi</div>
              <div style="font-size: 22px; font-weight: 700; color: #111827; margin-top: 4px;">{{ $orders->count() }} Pesanan</div>
            </div>
            <div style="background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px 18px;">
              <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #6B7280;">Dikemas / Dikirim</div>
              <div style="font-size: 22px; font-weight: 700; color: #10B981; margin-top: 4px;">{{ $orders->whereIn('status.value', ['processing', 'shipped'])->count() }} Pesanan</div>
            </div>
            <div style="background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px 18px;">
              <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #6B7280;">Akumulasi Belanja</div>
              <div style="font-size: 22px; font-weight: 700; color: #A18A68; margin-top: 4px;">$ {{ number_format((float) $orders->sum('grand_total'), 2) }}</div>
            </div>
          </div>

          <!-- Shopee Order Status Filter Tabs -->
          <div style="display: flex; gap: 6px; overflow-x: auto; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; padding: 8px; margin-bottom: 18px;">
            <button type="button" class="order-filter-tab active" data-order-status="all" style="padding: 8px 14px; border-radius: 6px; border: none; background: #111827; color: #FFFFFF; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Semua ({{ $orders->count() }})</button>
            <button type="button" class="order-filter-tab" data-order-status="pending" style="padding: 8px 14px; border-radius: 6px; border: none; background: transparent; color: #4B5563; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Belum Bayar ({{ $orders->where('status.value', 'pending')->count() }})</button>
            <button type="button" class="order-filter-tab" data-order-status="processing" style="padding: 8px 14px; border-radius: 6px; border: none; background: transparent; color: #4B5563; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Sedang Dikemas ({{ $orders->where('status.value', 'processing')->count() }})</button>
            <button type="button" class="order-filter-tab" data-order-status="shipped" style="padding: 8px 14px; border-radius: 6px; border: none; background: transparent; color: #4B5563; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Dikirim ({{ $orders->where('status.value', 'shipped')->count() }})</button>
            <button type="button" class="order-filter-tab" data-order-status="delivered" style="padding: 8px 14px; border-radius: 6px; border: none; background: transparent; color: #4B5563; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap;">Selesai ({{ $orders->where('status.value', 'delivered')->count() }})</button>
          </div>

          <div class="dashboard-table-wrap" style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px;">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 16px; color: #111827;">Riwayat &amp; Status Pesanan Anda</h3>
            @if($orders->count() > 0)
              <table class="dashboard-table">
                <thead>
                  <tr>
                    <th>No. Order</th>
                    <th>Tanggal</th>
                    <th>Status Pesanan</th>
                    <th>Status Pembayaran</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: right;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($orders as $ord)
                    <tr class="customer-order-row" data-status="{{ $ord->status->value }}">
                      <td>
                        <strong style="color: #111827;">{{ $ord->order_number }}</strong>
                        <div style="font-size: 11px; color: #6B7280; margin-top: 2px;">{{ $ord->items->count() }} item busana</div>
                      </td>
                      <td>{{ $ord->created_at->format('d M Y') }}</td>
                      <td>
                        <span class="badge {{ $ord->status->badgeClass() }}">
                          {{ $ord->status->label() }}
                        </span>
                        @if($ord->tracking_number)
                          <div style="font-size: 11px; font-family: monospace; color: #047857; margin-top: 3px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <span>{{ $ord->tracking_number }}</span>
                          </div>
                        @endif
                      </td>
                      <td>
                        @if($ord->payment_status === 'paid')
                          <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #D1FAE5; color: #065F46;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Lunas
                          </span>
                        @elseif($ord->payment_status === 'waiting_verification')
                          <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #DBEAFE; color: #1E40AF;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                            Verifikasi
                          </span>
                        @else
                          <span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #FEF3C7; color: #92400E;">
                            Belum Bayar
                          </span>
                        @endif
                      </td>
                      <td style="text-align: right; font-weight: 700; color: #111827;">{{ $ord->formatted_grand_total }}</td>
                      <td style="text-align: right;">
                        <a href="{{ route('order.show', $ord->order_number) }}" class="table-gold-link" style="font-weight: 600; font-size: 13px;">
                          @if($ord->payment_status !== 'paid')
                            Bayar / Detail &rarr;
                          @else
                            Detail &amp; Faktur &rarr;
                          @endif
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div style="text-align: center; padding: 48px; background: #FAF9F6; border: 1px dashed #D8D8D8; border-radius: 8px;">
                <p style="color: #707070; font-size: 15px; margin-bottom: 16px;">Anda belum memiliki riwayat pesanan.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary" style="font-size: 13px;">Mulai Belanja</a>
              </div>
            @endif
          </div>
        </section>

        <!-- PANEL 2: PROFIL & KONTAK SAYA -->
        <section id="member-panel-profile" class="member-panel" style="display: {{ $activeTab === 'profile' ? 'block' : 'none' }}; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
          <div style="border-bottom: 1px solid #E5E7EB; padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #111827;">Profil &amp; Informasi Kontak Saya</h3>
            <p style="margin: 4px 0 0; font-size: 13px; color: #6B7280;">Kelola informasi profil, nomor handphone/WhatsApp, dan preferensi notifikasi pesanan Anda.</p>
          </div>

          <form action="{{ route('account.profile.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Username Akun</label>
                <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" placeholder="clara.aurelia" class="checkout-input" style="margin: 0;">
              </div>
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nama Lengkap (Sesuai KTP / Penerima) *</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="checkout-input" style="margin: 0;">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Alamat Email Aktif *</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="checkout-input" style="margin: 0;">
              </div>
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nomor Telepon / WhatsApp Aktif *</label>
                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="081234567890" required class="checkout-input" style="margin: 0;">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Jenis Kelamin</label>
                <select name="gender" class="checkout-input" style="margin: 0;">
                  <option value="">-- Pilih --</option>
                  <option value="Perempuan" {{ old('gender', auth()->user()->gender) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                  <option value="Laki-laki" {{ old('gender', auth()->user()->gender) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                  <option value="Lainnya" {{ old('gender', auth()->user()->gender) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
              </div>
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Tanggal Lahir</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', auth()->user()->birth_date ? \Illuminate\Support\Carbon::parse(auth()->user()->birth_date)->format('Y-m-d') : '') }}" class="checkout-input" style="margin: 0;">
              </div>
            </div>

            <div style="background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 16px; margin-top: 4px;">
              <div style="font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 10px;">Pengaturan Notifikasi</div>
              <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: #374151;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                  <input type="checkbox" name="email_order" value="1" {{ !empty($notifPrefs['email_order']) ? 'checked' : '' }}>
                  <span>Kirim faktur &amp; update status pesanan melalui Email</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                  <input type="checkbox" name="whatsapp_shipping" value="1" {{ !empty($notifPrefs['whatsapp_shipping']) ? 'checked' : '' }}>
                  <span>Kirim nomor resi &amp; pelacakan kurir ke WhatsApp saya</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                  <input type="checkbox" name="promo_newsletter" value="1" {{ !empty($notifPrefs['promo_newsletter']) ? 'checked' : '' }}>
                  <span>Info eksklusif peluncuran koleksi busana baru &amp; voucher member</span>
                </label>
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
              <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 13px;">Simpan Profil &amp; Kontak</button>
            </div>
          </form>
        </section>

        <!-- PANEL 3: ALAMAT PENGIRIMAN (SHOPEE ADDRESS BOOK) -->
        <section id="member-panel-addresses" class="member-panel" style="display: {{ $activeTab === 'addresses' ? 'block' : 'none' }};">
          <!-- Daftar Buku Alamat Tersimpan -->
          <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; padding-bottom: 14px; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
              <div>
                <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #111827;">Alamat Saya (Buku Alamat Pengiriman)</h3>
                <p style="margin: 4px 0 0; font-size: 13px; color: #6B7280;">Alamat bertanda <strong>Utama</strong> akan otomatis mengisi formulir saat Anda melakukan Checkout.</p>
              </div>
              <button type="button" onclick="document.getElementById('addNewAddressBox').style.display = document.getElementById('addNewAddressBox').style.display === 'none' ? 'block' : 'none'" class="btn btn-primary" style="font-size: 12px; padding: 10px 18px; display: inline-flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Alamat Baru
              </button>
            </div>

            <!-- Collapsible Form Tambah Alamat Baru -->
            <div id="addNewAddressBox" style="display: none; background: #FAF9F6; border: 1px solid #D1D5DB; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
              <h4 style="margin: 0 0 14px; font-size: 15px; font-weight: 700; color: #111827;">Tambah Lokasi Pengiriman Baru</h4>
              <form action="{{ route('account.address.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 12px;">
                @csrf
                @method('PUT')
                <input type="hidden" name="action_type" value="add_address">

                <div style="display: grid; grid-template-columns: 1fr 1fr 140px; gap: 12px;">
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Penerima *</label>
                    <input type="text" name="recipient_name" value="{{ auth()->user()->name }}" required class="checkout-input" style="margin: 0;">
                  </div>
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">No. Telepon Penerima *</label>
                    <input type="tel" name="phone" value="{{ auth()->user()->phone }}" required class="checkout-input" style="margin: 0;">
                  </div>
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Label Alamat *</label>
                    <select name="address_label" class="checkout-input" style="margin: 0;">
                      <option value="Rumah">Rumah</option>
                      <option value="Kantor">Kantor</option>
                      <option value="Apartemen">Apartemen</option>
                      <option value="Kos">Kos</option>
                    </select>
                  </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 120px; gap: 12px;">
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Provinsi *</label>
                    <input type="text" name="province" placeholder="DKI Jakarta" required class="checkout-input" style="margin: 0;">
                  </div>
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kota / Kabupaten *</label>
                    <input type="text" name="city" placeholder="Jakarta Selatan" required class="checkout-input" style="margin: 0;">
                  </div>
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kecamatan *</label>
                    <input type="text" name="district" placeholder="Kebayoran Baru" required class="checkout-input" style="margin: 0;">
                  </div>
                  <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kode Pos *</label>
                    <input type="text" name="postcode" placeholder="12190" required class="checkout-input" style="margin: 0;">
                  </div>
                </div>

                <input type="hidden" name="country" value="Indonesia">

                <div>
                  <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Jalan, Gedung, No. Rumah, RT/RW *</label>
                  <input type="text" name="address" placeholder="Jl. Senopati No. 42, RT 04/RW 02" required class="checkout-input" style="margin: 0;">
                </div>

                <div>
                  <label style="display: block; font-size: 11px; font-weight: 600; color: #374151; margin-bottom: 4px;">Detail Patokan Kurir (Opsional)</label>
                  <input type="text" name="address_notes" placeholder="Pagar hitam sebelah butik" class="checkout-input" style="margin: 0;">
                </div>

                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #374151; cursor: pointer;">
                  <input type="checkbox" name="set_as_primary" value="1" checked>
                  <span>Atur sebagai Alamat Pengiriman Utama</span>
                </label>

                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                  <button type="button" onclick="document.getElementById('addNewAddressBox').style.display='none'" class="btn btn-outline" style="font-size: 12px; padding: 8px 16px;">Batal</button>
                  <button type="submit" class="btn btn-primary" style="font-size: 12px; padding: 8px 20px;">Simpan Alamat Baru</button>
                </div>
              </form>
            </div>

            <!-- Kartu Buku Alamat -->
            <div style="display: flex; flex-direction: column; gap: 12px;">
              @forelse($addressBook as $addr)
                <div style="border: 1px solid {{ !empty($addr['is_primary']) ? '#A18A68' : '#E5E7EB' }}; background: {{ !empty($addr['is_primary']) ? '#FCFBF9' : '#FFFFFF' }}; border-radius: 10px; padding: 16px 18px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
                  <div style="flex: 1; min-width: 240px;">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px;">
                      <strong style="font-size: 14px; color: #111827;">{{ $addr['recipient_name'] ?? auth()->user()->name }}</strong>
                      <span style="color: #9CA3AF;">|</span>
                      <span style="font-size: 13px; color: #4B5563;">{{ $addr['phone'] ?? auth()->user()->phone }}</span>
                      <span style="padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; background: #F3F4F6; color: #374151;">{{ $addr['label'] ?? 'Rumah' }}</span>
                      @if(!empty($addr['is_primary']))
                        <span style="padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; border: 1px solid #A18A68; color: #A18A68; background: #FFFBEB;">Utama</span>
                      @endif
                    </div>
                    <div style="font-size: 13px; color: #374151; line-height: 1.5;">
                      {{ $addr['street_address'] ?? '' }}
                      <br>
                      {{ implode(', ', array_filter([$addr['district'] ?? null, $addr['city'] ?? null, $addr['province'] ?? null, $addr['postcode'] ?? null, $addr['country'] ?? 'Indonesia'])) }}
                    </div>
                    @if(!empty($addr['notes']))
                      <div style="font-size: 12px; color: #6B7280; margin-top: 4px;">Patokan: {{ $addr['notes'] }}</div>
                    @endif
                  </div>

                  <div style="display: flex; align-items: center; gap: 8px;">
                    @if(empty($addr['is_primary']))
                      <form action="{{ route('account.address.update') }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action_type" value="set_primary">
                        <input type="hidden" name="address_id" value="{{ $addr['id'] }}">
                        <button type="submit" class="btn btn-outline" style="font-size: 11px; padding: 6px 12px;">Atur sebagai Utama</button>
                      </form>
                      <form action="{{ route('account.address.update') }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action_type" value="delete_address">
                        <input type="hidden" name="address_id" value="{{ $addr['id'] }}">
                        <button type="submit" class="btn btn-outline" style="font-size: 11px; padding: 6px 10px; color: #DC2626; border-color: #FCA5A5;">Hapus</button>
                      </form>
                    @else
                      <a href="{{ route('checkout.index') }}" style="font-size: 12px; font-weight: 600; color: #A18A68; text-decoration: none;">Siap Dipakai Checkout &rarr;</a>
                    @endif
                  </div>
                </div>
              @empty
                <div style="padding: 20px; text-align: center; background: #FAF9F6; border: 1px dashed #D1D5DB; border-radius: 8px; color: #6B7280; font-size: 13px;">
                  Belum ada alamat pengiriman tersimpan. Silakan lengkapi formulir Alamat Utama di bawah ini.
                </div>
              @endforelse
            </div>
          </div>

          <!-- Form Edit Alamat Pengiriman Utama -->
          <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
            <h4 style="margin: 0 0 6px; font-size: 16px; font-weight: 700; color: #111827;">Pengaturan Detail Alamat Pengiriman Utama</h4>
            <p style="margin: 0 0 18px; font-size: 12px; color: #6B7280;">Perbarui lokasi pengiriman utama Anda agar terisi otomatis saat melakukan pemesanan produk.</p>

            <form action="{{ route('account.address.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
              @csrf
              @method('PUT')
              <input type="hidden" name="action_type" value="update_primary">

              <div style="display: grid; grid-template-columns: 1fr 1fr 150px; gap: 14px;">
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nama Penerima</label>
                  <input type="text" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" class="checkout-input" style="margin: 0;">
                </div>
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nomor Telepon Penerima *</label>
                  <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required placeholder="081234567890" class="checkout-input" style="margin: 0;">
                </div>
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Label Lokasi *</label>
                  <select name="address_label" class="checkout-input" style="margin: 0;">
                    @foreach(['Rumah', 'Kantor', 'Apartemen', 'Kos'] as $lbl)
                      <option value="{{ $lbl }}" {{ old('address_label', auth()->user()->address_label ?: 'Rumah') === $lbl ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 130px; gap: 14px;">
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Provinsi *</label>
                  <input type="text" name="province" value="{{ old('province', auth()->user()->province) }}" required placeholder="DKI Jakarta" class="checkout-input" style="margin: 0;">
                </div>
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Kota / Kabupaten *</label>
                  <input type="text" name="city" value="{{ old('city', auth()->user()->city) }}" required placeholder="Jakarta Selatan" class="checkout-input" style="margin: 0;">
                </div>
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Kecamatan *</label>
                  <input type="text" name="district" value="{{ old('district', auth()->user()->district) }}" required placeholder="Kebayoran Baru" class="checkout-input" style="margin: 0;">
                </div>
                <div>
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Kode Pos *</label>
                  <input type="text" name="postcode" value="{{ old('postcode', auth()->user()->postcode) }}" required placeholder="12190" class="checkout-input" style="margin: 0;">
                </div>
              </div>

              <input type="hidden" name="country" value="{{ old('country', auth()->user()->country ?: 'Indonesia') }}">

              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Alamat Lengkap (Nama Jalan, Gedung, No. Rumah, RT/RW) *</label>
                <input type="text" name="address" value="{{ old('address', auth()->user()->address) }}" required placeholder="Jl. Senopati No. 42, RT 04/RW 02" class="checkout-input" style="margin: 0;">
              </div>

              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Catatan Untuk Kurir / Patokan Lokasi (Opsional)</label>
                <input type="text" name="address_notes" value="{{ old('address_notes', auth()->user()->address_notes) }}" placeholder="Rumah pagar hitam sebelah butik kopi" class="checkout-input" style="margin: 0;">
              </div>

              <div style="display: flex; justify-content: flex-end; margin-top: 6px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 13px;">Simpan Alamat Utama</button>
              </div>
            </form>
          </div>
        </section>

        <!-- PANEL 4: REKENING BANK & PEMBAYARAN -->
        <section id="member-panel-bank" class="member-panel" style="display: {{ $activeTab === 'bank' ? 'block' : 'none' }}; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
          <div style="border-bottom: 1px solid #E5E7EB; padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #111827;">Rekening Bank &amp; Metode Pembayaran</h3>
            <p style="margin: 4px 0 0; font-size: 13px; color: #6B7280;">Simpan informasi rekening bank Anda untuk mempercepat verifikasi pembayaran transfer manual dan pengembalian dana (refund).</p>
          </div>

          @if(!empty($bankAccount['account_number']))
            <div style="background: linear-gradient(135deg, #111827 0%, #1F2937 100%); color: #FFFFFF; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
              <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #9CA3AF;">Rekening Bank Utama Tersimpan</div>
                <div style="font-size: 20px; font-weight: 700; margin-top: 4px; color: #F9FAFB;">Bank {{ $bankAccount['bank_name'] ?? '-' }} &bull; {{ $bankAccount['account_number'] }}</div>
                <div style="font-size: 13px; color: #D1D5DB; margin-top: 2px;">a.n. {{ $bankAccount['account_holder'] ?? auth()->user()->name }}</div>
              </div>
              <span style="padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: rgba(16, 185, 129, 0.2); color: #34D399;">Terverifikasi</span>
            </div>
          @endif

          <form action="{{ route('account.bank.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nama Bank *</label>
                <select name="bank_name" required class="checkout-input" style="margin: 0;">
                  @foreach(['BCA', 'Mandiri', 'BNI', 'BRI', 'BSI', 'CIMB Niaga', 'Permata', 'SeaBank', 'Bank Jago'] as $bank)
                    <option value="{{ $bank }}" {{ ($bankAccount['bank_name'] ?? '') === $bank ? 'selected' : '' }}>Bank {{ $bank }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nomor Rekening *</label>
                <input type="text" name="account_number" value="{{ old('account_number', $bankAccount['account_number'] ?? '') }}" required placeholder="Contoh: 5240981234" class="checkout-input" style="margin: 0;">
              </div>
            </div>

            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Nama Pemilik Rekening (Sesuai Buku Tabungan) *</label>
              <input type="text" name="account_holder" value="{{ old('account_holder', $bankAccount['account_holder'] ?? auth()->user()->name) }}" required class="checkout-input" style="margin: 0;">
            </div>

            <div style="display: flex; justify-content: flex-end;">
              <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 13px;">Simpan Rekening Bank</button>
            </div>
          </form>
        </section>

        <!-- PANEL 5: UBAH PASSWORD & KEAMANAN -->
        <section id="member-panel-security" class="member-panel" style="display: {{ $activeTab === 'security' ? 'block' : 'none' }}; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 12px; padding: 24px;">
          <div style="border-bottom: 1px solid #E5E7EB; padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #111827;">Keamanan Akun &amp; Ubah Password</h3>
            <p style="margin: 4px 0 0; font-size: 13px; color: #6B7280;">Untuk keamanan akun Anda, mohon jangan sebarkan password Anda kepada siapapun.</p>
          </div>

          <form action="{{ route('account.password.update') }}" method="POST" style="max-width: 520px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Password Saat Ini *</label>
              <input type="password" name="current_password" required placeholder="Masukkan password lama Anda" class="checkout-input" style="margin: 0;">
            </div>

            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Password Baru (Minimal 6 Karakter) *</label>
              <input type="password" name="password" required placeholder="Masukkan password baru" class="checkout-input" style="margin: 0;">
            </div>

            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px;">Konfirmasi Password Baru *</label>
              <input type="password" name="password_confirmation" required placeholder="Ulangi password baru" class="checkout-input" style="margin: 0;">
            </div>

            <div>
              <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 13px;">Perbarui Password</button>
            </div>
          </form>
        </section>
      </div>
    </div>
  @else
    <!-- Guest Auth: Segmented Tabs & Clean Complete Forms -->
    <div class="account-wrapper" style="max-width: 560px;">
      <div class="account-tabs" role="tablist">
        <button type="button" class="account-tab-btn {{ $errors->has('name') || $errors->has('password_confirmation') ? '' : 'active' }}" data-tab="loginPanel" role="tab" aria-selected="{{ $errors->has('name') || $errors->has('password_confirmation') ? 'false' : 'true' }}">
          Sign In
        </button>
        <button type="button" class="account-tab-btn {{ $errors->has('name') || $errors->has('password_confirmation') ? 'active' : '' }}" data-tab="registerPanel" role="tab" aria-selected="{{ $errors->has('name') || $errors->has('password_confirmation') ? 'true' : 'false' }}">
          Register
        </button>
      </div>

      <!-- Sign In Panel -->
      <div class="account-form-panel {{ $errors->has('name') || $errors->has('password_confirmation') ? '' : 'active' }}" id="loginPanel" role="tabpanel">
        <form action="{{ route('account.login') }}" method="POST" class="account-form">
          @csrf
          <div class="account-input-wrap">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email *" class="account-input" required autocomplete="email">
          </div>
          <div class="account-input-wrap">
            <input type="password" name="password" placeholder="Password *" class="account-input" required autocomplete="current-password">
          </div>
          <div class="account-checkbox-wrap">
            <label class="account-checkbox-label">
              <input type="checkbox" name="remember" class="account-checkbox-input">
              <span>Remember me</span>
            </label>
          </div>
          <button type="submit" class="account-submit-btn">SIGN IN</button>
        </form>
      </div>

      <!-- Register Panel (Complete with Contact & Default Shipping Location for Checkout) -->
      <div class="account-form-panel {{ $errors->has('name') || $errors->has('password_confirmation') ? 'active' : '' }}" id="registerPanel" role="tabpanel">
        <form action="{{ route('account.register') }}" method="POST" class="account-form">
          @csrf

          <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #A18A68; margin-bottom: 12px;">
            1. Data Profil &amp; Kontak Penerima
          </div>

          <div class="account-input-wrap">
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap (Sesuai Penerima Paket) *" class="account-input" required autocomplete="name">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="account-input-wrap">
              <input type="email" name="email" value="{{ old('email') }}" placeholder="Alamat Email Aktif *" class="account-input" required autocomplete="email">
            </div>
            <div class="account-input-wrap">
              <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="No. Handphone / WhatsApp *" class="account-input" autocomplete="tel">
            </div>
          </div>

          <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #A18A68; margin: 12px 0;">
            2. Lokasi Pengiriman Utama (Otomatis Terisi Saat Checkout)
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="account-input-wrap">
              <input type="text" name="province" value="{{ old('province') }}" placeholder="Provinsi (Contoh: DKI Jakarta)" class="account-input">
            </div>
            <div class="account-input-wrap">
              <input type="text" name="city" value="{{ old('city') }}" placeholder="Kota / Kabupaten (Contoh: Jakarta Selatan)" class="account-input">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 120px 120px; gap: 12px;">
            <div class="account-input-wrap">
              <input type="text" name="district" value="{{ old('district') }}" placeholder="Kecamatan (Contoh: Kebayoran Baru)" class="account-input">
            </div>
            <div class="account-input-wrap">
              <input type="text" name="postcode" value="{{ old('postcode') }}" placeholder="Kode Pos" class="account-input">
            </div>
            <div class="account-input-wrap">
              <select name="address_label" class="account-input" style="background: transparent; cursor: pointer;">
                <option value="Rumah" {{ old('address_label') === 'Rumah' ? 'selected' : '' }}>Rumah</option>
                <option value="Kantor" {{ old('address_label') === 'Kantor' ? 'selected' : '' }}>Kantor</option>
                <option value="Apartemen" {{ old('address_label') === 'Apartemen' ? 'selected' : '' }}>Apartemen</option>
                <option value="Kos" {{ old('address_label') === 'Kos' ? 'selected' : '' }}>Kos</option>
              </select>
            </div>
          </div>

          <input type="hidden" name="country" value="Indonesia">

          <div class="account-input-wrap">
            <input type="text" name="address" value="{{ old('address') }}" placeholder="Alamat Lengkap (Nama Jalan, Gedung, No. Rumah, RT/RW)" class="account-input" autocomplete="street-address">
          </div>

          <div class="account-input-wrap">
            <input type="text" name="address_notes" value="{{ old('address_notes') }}" placeholder="Patokan Kurir (Opsional, misal: Pagar hitam depan taman)" class="account-input">
          </div>

          <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #A18A68; margin: 12px 0;">
            3. Keamanan Kata Sandi
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="account-input-wrap">
              <input type="password" name="password" placeholder="Password (Min. 6 Karakter) *" class="account-input" required autocomplete="new-password">
            </div>
            <div class="account-input-wrap">
              <input type="password" name="password_confirmation" placeholder="Konfirmasi Password *" class="account-input" required autocomplete="new-password">
            </div>
          </div>

          <button type="submit" class="account-submit-btn" style="margin-top: 8px;">REGISTER &amp; SIMPAN PROFIL</button>
        </form>
      </div>
    </div>
  @endauth
</div>

@push('styles')
<style>
  .member-nav-btn.active {
    background: #111827 !important;
    color: #FFFFFF !important;
  }
  .member-nav-btn:hover:not(.active) {
    background: #F3F4F6 !important;
  }
  @media (max-width: 860px) {
    .account-shopee-layout {
      grid-template-columns: 1fr !important;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Guest Sign In / Register Tab Switcher
    const tabBtns = document.querySelectorAll('.account-tab-btn');
    const panels = document.querySelectorAll('.account-form-panel');

    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-tab');

        tabBtns.forEach(function (b) {
          b.classList.remove('active');
          b.setAttribute('aria-selected', 'false');
        });
        panels.forEach(function (p) {
          p.classList.remove('active');
        });

        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');

        const targetPanel = document.getElementById(targetId);
        if (targetPanel) {
          targetPanel.classList.add('active');
        }
      });
    });

    // Member Center Sidebar Tab Switcher
    const memberBtns = document.querySelectorAll('.member-nav-btn');
    const memberPanels = document.querySelectorAll('.member-panel');

    memberBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const tabKey = this.getAttribute('data-member-tab');

        memberBtns.forEach(function (b) { b.classList.remove('active'); });
        memberPanels.forEach(function (p) { p.style.display = 'none'; });

        this.classList.add('active');
        const target = document.getElementById('member-panel-' + tabKey);
        if (target) {
          target.style.display = 'block';
        }
      });
    });

    // Order Status Filter Tabs (Shopee style)
    const orderFilterTabs = document.querySelectorAll('.order-filter-tab');
    const orderRows = document.querySelectorAll('.customer-order-row');

    orderFilterTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        const status = this.getAttribute('data-order-status');

        orderFilterTabs.forEach(function (t) {
          t.style.background = 'transparent';
          t.style.color = '#4B5563';
        });
        this.style.background = '#111827';
        this.style.color = '#FFFFFF';

        orderRows.forEach(function (row) {
          if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    });
  });
</script>
@endpush
@endsection
