@extends('layouts.storefront')

@section('title', 'Order ' . $order->order_number . ' - PUTRI.J')

@section('content')
<div class="container order-details-page">
  <!-- Flash Success / Error Message -->
  @if(session('success'))
    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
      {{ session('success') }}
    </div>
  @endif

  <div class="order-confirmation-banner">
    <span class="order-confirmation-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M9 12l2 2 4-4"></path>
      </svg>
    </span>
    <span class="order-confirmation-text">
      We've received your order <strong>{{ $order->order_number }}</strong> — thank you for shopping with PUTRI.J.
    </span>
  </div>

  <!-- Interactive Payment Status & Instruction Box -->
  <div style="margin-bottom: 32px; border-radius: 12px; overflow: hidden; border: 1px solid {{ $order->payment_status === 'paid' ? '#A7F3D0' : ($order->payment_status === 'waiting_verification' ? '#BFDBFE' : '#FDE68A') }}; background: {{ $order->payment_status === 'paid' ? '#F0FDF4' : ($order->payment_status === 'waiting_verification' ? '#EFF6FF' : '#FFFDF7') }};">
    <div style="padding: 20px 24px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
          <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: #111827;">Informasi Pembayaran Pesanan</h3>
          @if($order->payment_status === 'paid')
            <span style="background: #10B981; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
              LUNAS
            </span>
          @elseif($order->payment_status === 'waiting_verification')
            <span style="background: #2563EB; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
              MENUNGGU VERIFIKASI BUKTI
            </span>
          @else
            <span style="background: #F59E0B; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px;">
              MENUNGGU PEMBAYARAN
            </span>
          @endif
        </div>
        <div style="font-size: 15px; font-weight: 700; color: #111827;">
          Total Tagihan: <span style="color: #A18A68;">{{ $order->formatted_grand_total }}</span>
        </div>
      </div>

      @if($order->payment_status === 'paid')
        <p style="margin: 0; font-size: 13.5px; color: #065F46;">
          Terima kasih! Pembayaran Anda telah terverifikasi secara resmi pada <strong>{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : 'hari ini' }}</strong>. Perhiasan Anda saat ini dalam antrean pengemasan aman dan segera diserahkan ke pihak ekspedisi.
        </p>
      @elseif($order->payment_status === 'waiting_verification')
        <p style="margin: 0; font-size: 13.5px; color: #1E40AF;">
          Bukti konfirmasi pembayaran Anda telah masuk ke sistem kami. Tim finance PUTRI.J sedang mencocokkan mutasi rekening Anda. Status pesanan akan otomatis terbarui menjadi <strong>Diproses</strong> setelah verifikasi selesai.
        </p>
      @else
        <!-- Unpaid State: Bank details and Confirmation Actions -->
        <p style="font-size: 13.5px; color: #6B7280; margin: 0 0 16px 0;">
          Silakan selesaikan pembayaran sesuai total tagihan di atas ke salah satu rekening resmi PUTRI.J Luxury berikut:
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin-bottom: 20px;">
          <!-- BCA -->
          <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <span style="font-weight: 700; font-size: 14px; color: #003399;">Bank Central Asia (BCA)</span>
              <span style="font-size: 11px; background: #EFF6FF; color: #1D4ED8; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Manual Transfer</span>
            </div>
            <div style="font-family: monospace; font-size: 16px; font-weight: 700; color: #111827; letter-spacing: 0.05em;">
              123-456-7890
            </div>
            <div style="font-size: 12px; color: #6B7280; margin-top: 2px;">a.n. PT Putri.J Luxury Indonesia</div>
          </div>

          <!-- Mandiri -->
          <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <span style="font-weight: 700; font-size: 14px; color: #003066;">Bank Mandiri</span>
              <span style="font-size: 11px; background: #EFF6FF; color: #1D4ED8; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Manual Transfer</span>
            </div>
            <div style="font-family: monospace; font-size: 16px; font-weight: 700; color: #111827; letter-spacing: 0.05em;">
              102-00-1234567-8
            </div>
            <div style="font-size: 12px; color: #6B7280; margin-top: 2px;">a.n. PT Putri.J Luxury Indonesia</div>
          </div>
        </div>

        <!-- Action Choice Buttons -->
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; border-top: 1px dashed #D1D5DB; padding-top: 16px;">
          <!-- 1. Instant Sandbox / Gateway Simulator Button -->
          <form action="{{ route('order.confirm_payment', $order->order_number) }}" method="POST" style="margin: 0;">
            @csrf
            <input type="hidden" name="action_type" value="instant_simulate">
            <button
              type="submit"
              class="btn btn-primary"
              style="padding: 10px 20px; font-size: 13px; font-weight: 600; background: #10B981; border-color: #10B981; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
              title="Simulasikan pembayaran instan sukses tanpa menunggu transfer manual"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
              Simulasi Bayar Instan (Langsung Lunas)
            </button>
          </form>

          <!-- 2. Toggle Manual Confirmation Form -->
          <button
            type="button"
            onclick="document.getElementById('manualPaymentFormSection').style.display = document.getElementById('manualPaymentFormSection').style.display === 'none' ? 'block' : 'none'"
            class="btn btn-outline"
            style="padding: 10px 18px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Konfirmasi Bukti Transfer Manual &darr;
          </button>
        </div>

        <!-- Collapsible Manual Transfer Confirmation Form -->
        <div id="manualPaymentFormSection" style="display: none; margin-top: 18px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 18px;">
          <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #111827;">Formulir Konfirmasi Transfer Pelanggan</h4>
          <form action="{{ route('order.confirm_payment', $order->order_number) }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            @csrf
            <input type="hidden" name="action_type" value="manual_transfer">

            @php
              $savedBank = auth()->user()?->bank_account ?? [];
              $defaultBank = $savedBank['bank_name'] ?? 'BCA';
              $defaultHolder = $savedBank['account_holder'] ?? $order->customer_name;
            @endphp
            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Bank Asal Pengirim</label>
              <select name="bank_name" required style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #D1D5DB; border-radius: 6px;">
                @foreach(['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'BSI', 'SeaBank', 'Bank Lainnya'] as $bankOpt)
                  <option value="{{ $bankOpt }}" {{ $defaultBank === $bankOpt ? 'selected' : '' }}>{{ $bankOpt }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Pemilik Rekening</label>
              <input type="text" name="sender_name" required value="{{ $defaultHolder }}" placeholder="Nama pemilik rekening" style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #D1D5DB; border-radius: 6px;">
            </div>

            <div>
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nominal yang Ditransfer ($ / Rp)</label>
              <input type="number" step="0.01" name="transfer_amount" required value="{{ $order->grand_total }}" style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #D1D5DB; border-radius: 6px;">
            </div>

            <div style="grid-column: 1 / -1;">
              <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Catatan Transfer (Opsional)</label>
              <input type="text" name="transfer_notes" placeholder="Nomor referensi mutasi atau jam transfer..." style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #D1D5DB; border-radius: 6px;">
            </div>

            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; margin-top: 6px;">
              <button type="submit" class="btn btn-primary" style="padding: 9px 22px; font-size: 13px; font-weight: 600;">
                Kirim Bukti Konfirmasi Transfer
              </button>
            </div>
          </form>
        </div>
      @endif
    </div>
  </div>

  <div class="order-details-grid">
    <!-- Left: Order Information -->
    <div>
      <h1 class="order-section-title">Order Details</h1>

      <div class="order-info-grid">
        <div class="order-info-group">
          <div class="order-info-label">ORDER NUMBER</div>
          <p class="order-info-value">{{ $order->order_number }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">DELIVERY OPTIONS</div>
          <p class="order-info-value">Standard Express Delivery</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">EMAIL</div>
          <p class="order-info-value">{{ $order->customer_email }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">DELIVERY ADDRESS</div>
          <p class="order-info-value">
            <strong>{{ $order->customer_name }}</strong>
            @if(!empty($order->shipping_address['label']))
              <span style="display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; background: #FEF3C7; color: #92400E; margin-left: 4px;">{{ $order->shipping_address['label'] }}</span>
            @endif
            <br>
            {{ $order->shipping_address['street'] ?? '-' }}<br>
            {{ implode(', ', array_filter([$order->shipping_address['district'] ?? null, $order->shipping_address['city'] ?? null, $order->shipping_address['province'] ?? null, $order->shipping_address['postcode'] ?? null])) }}<br>
            {{ $order->shipping_address['country'] ?? 'Indonesia' }}
          </p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">PAYMENT METHOD</div>
          <p class="order-info-value">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">CONTACT NUMBER</div>
          <p class="order-info-value">{{ $order->customer_phone ?: '-' }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">ORDER DATE</div>
          <p class="order-info-value">{{ $order->created_at?->format('F j, Y') }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">STATUS PESANAN</div>
          <p class="order-info-value">{{ $order->status->label() }}</p>
        </div>
      </div>
    </div>

    <!-- Right: Order Summary -->
    <div>
      <h2 class="order-section-title">Order Summary</h2>

      <div class="order-summary-card">
        <div class="order-summary-header">
          <span>PRODUCT</span>
          <span>TOTAL</span>
        </div>

        <ul class="order-summary-list">
          @foreach($order->items as $item)
            <li class="order-summary-item">
              <span class="order-summary-item-name">{{ $item->product_name }} &times; {{ $item->quantity }}</span>
              <span class="order-summary-item-price">$ {{ number_format((float) $item->line_total, 2) }}</span>
            </li>
          @endforeach
        </ul>

        <div class="order-summary-calc-row">
          <span>SUBTOTAL</span>
          <span>$ {{ number_format((float) $order->subtotal, 2) }}</span>
        </div>

        <div class="order-summary-calc-row">
          <span>SHIPPING</span>
          <span>{{ (float) $order->shipping_cost > 0 ? '$ ' . number_format((float) $order->shipping_cost, 2) : 'Free shipping' }}</span>
        </div>

        <div class="order-summary-total-row">
          <span>TOTAL</span>
          <span>{{ $order->formatted_grand_total }}</span>
        </div>
      </div>

      <div style="margin-top: 28px; display: flex; justify-content: flex-end;">
        <a href="{{ route('shop.index') }}" class="btn btn-outline">Continue Shopping &rarr;</a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Clear client-side cart after order completion
    if (window.cartManager && typeof window.cartManager.clearCart === 'function') {
      window.cartManager.clearCart();
    }
  });
</script>
@endpush
@endsection
