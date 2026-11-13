@extends('layouts.storefront')

@section('title', 'Order ' . $order->order_number . ' - PUTRIJALVA')

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
      We've received your order <strong>{{ $order->order_number }}</strong> — thank you for shopping with PUTRIJALVA.
    </span>
  </div>

  <!-- Interactive Payment Status & Instruction Box -->
  <div style="margin-bottom: 32px; border-radius: 12px; overflow: hidden; border: 1px solid {{ $order->payment_status === 'paid' ? '#A7F3D0' : ($order->payment_status === 'waiting_verification' ? '#BFDBFE' : '#FDE68A') }}; background: {{ $order->payment_status === 'paid' ? '#F0FDF4' : ($order->payment_status === 'waiting_verification' ? '#EFF6FF' : '#FFFDF7') }};">
    <div style="padding: 20px 24px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
          <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: #111827;">{{ __('Informasi Pembayaran Pesanan') }}</h3>
          @if($order->payment_status === 'paid')
            <span style="background: #10B981; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
              {{ __('LUNAS') }}
            </span>
          @elseif($order->payment_status === 'waiting_verification')
            <span style="background: #2563EB; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
              {{ __('MENUNGGU VERIFIKASI BUKTI') }}
            </span>
          @else
            <span style="background: #F59E0B; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px;">
              {{ __('MENUNGGU PEMBAYARAN') }}
            </span>
          @endif
        </div>
        <div style="font-size: 15px; font-weight: 700; color: #111827;">
          {{ __('Total Tagihan:') }} <span style="color: #A18A68;">{{ $order->formatted_grand_total }}</span>
        </div>
      </div>

      @if($order->payment_status === 'paid')
        <div style="display: flex; align-items: flex-start; gap: 12px;">
          <div style="background: #10B981; color: #fff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          </div>
          <div>
            <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: #065F46;">Pembayaran Terkonfirmasi</h4>
            <p style="margin: 0; font-size: 13.5px; color: #065F46; line-height: 1.5;">
              Terima kasih! Pembayaran Anda telah terverifikasi secara resmi pada <strong>{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : 'hari ini' }}</strong> via {{ strtoupper($order->payment_gateway ?? $order->payment_method) }}{{ $order->payment_channel ? ' (' . strtoupper($order->payment_channel) . ')' : '' }}. Busana Anda saat ini dalam antrean pengemasan rapi dan segera diserahkan ke pihak ekspedisi.
            </p>
          </div>
        </div>
      @elseif($order->payment_status === 'waiting_verification')
        <div style="display: flex; align-items: flex-start; gap: 12px;">
          <div style="background: #2563EB; color: #fff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
          </div>
          <div>
            <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: #1E40AF;">Verifikasi Sedang Berlangsung</h4>
            <p style="margin: 0; font-size: 13.5px; color: #1E40AF; line-height: 1.5;">
              Bukti konfirmasi pembayaran Anda telah masuk ke sistem kami. Tim finance PUTRIJALVA sedang mencocokkan mutasi rekening Anda. Status pesanan akan otomatis terbarui menjadi <strong>Diproses</strong> setelah verifikasi selesai.
            </p>
          </div>
        </div>
      @else
        <!-- Unpaid State: Primary PGA Payment Gateway & Secondary Manual Transfer -->
        <p style="font-size: 13.5px; color: #4B5563; margin: 0 0 16px 0;">
          Pesanan ini siap dibayar. Pilih metode pembayaran instan otomatis melalui Payment Gateway (QRIS, Virtual Account, E-Wallet) atau transfer bank manual:
        </p>

        <!-- Primary Option: Instant PGA (Midtrans / QRIS / VA) -->
        <div style="background: #FFFFFF; border: 2px solid #A18A68; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(161, 138, 104, 0.08);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 14px;">
            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <span style="background: #A18A68; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">{{ __('Rekomendasi') }}</span>
                <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">{{ __('Pembayaran Otomatis & Instan') }}</h4>
              </div>
              <p style="margin: 0; font-size: 13px; color: #6B7280;">
                Mendukung <strong>QRIS (BCA, GoPay, OVO, ShopeePay)</strong>, <strong>Virtual Account Multi-Bank</strong>, serta <strong>Kartu Kredit / Debit</strong>. Verifikasi otomatis dalam hitungan detik tanpa upload bukti transfer.
              </p>
            </div>
            <div style="text-align: right;">
              <span style="font-size: 11px; color: #6B7280; display: block;">Estimasi Konversi IDR (1 USD ≈ Rp 16.000)</span>
              <span style="font-size: 16px; font-weight: 800; color: #A18A68;">Rp {{ number_format(round((float)$order->grand_total * 16000), 0, ',', '.') }}</span>
            </div>
          </div>

          <!-- PGA Action Buttons -->
          <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <button
              type="button"
              id="payWithPgaBtn"
              class="btn btn-primary"
              style="padding: 12px 26px; font-size: 14px; font-weight: 700; background: #A18A68; border-color: #A18A68; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(161, 138, 104, 0.3);"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
              <span>{{ __('Bayar Sekarang (PGA / Snap)') }}</span>
            </button>

            <!-- Instant Sandbox Simulation for rapid testing -->
            <form action="{{ route('order.confirm_payment', $order->order_number) }}" method="POST" style="margin: 0;">
              @csrf
              <input type="hidden" name="action_type" value="instant_simulate">
              <button
                type="submit"
                class="btn btn-outline"
                style="padding: 11px 18px; font-size: 13px; font-weight: 600; border-color: #10B981; color: #047857; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
                title="Simulasi instan khusus testing sandbox"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                {{ __('Simulasi Lunas Sandbox') }}
              </button>
            </form>

            <!-- WhatsApp Concierge Support Button -->
            <a
              href="https://wa.me/6281234567890?text={{ urlencode('Halo Concierge Butik PUTRIJALVA, saya ingin konfirmasi pesanan #' . $order->order_number . ' atas nama ' . $order->customer_name . ' dengan total ' . $order->formatted_grand_total . '. Mohon bantuannya.') }}"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-outline"
              style="padding: 11px 18px; font-size: 13px; font-weight: 700; border-color: #25D366; color: #128C7E; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;"
              title="Hubungi Concierge Butik via WhatsApp"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="#25D366"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
              <span>{{ __('WhatsApp Concierge') }}</span>
            </a>
          </div>

          <!-- Channel Badges -->
          <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 14px; padding-top: 12px; border-top: 1px solid #F3F4F6; font-size: 11px; color: #6B7280;">
            <span style="font-weight: 600;">Metode Didukung:</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">QRIS</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">BCA Virtual Account</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">Mandiri VA</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">BNI VA</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">BRI VA</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">GoPay</span>
            <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-weight: 600; color: #374151;">Visa / Mastercard</span>
          </div>
        </div>

        <!-- Secondary Option: Manual Bank Transfer -->
        <div style="border: 1px solid #E5E7EB; border-radius: 8px; background: #FAFAFA; padding: 14px 18px;">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
              <div style="font-size: 13.5px; font-weight: 700; color: #374151;">Atau Transfer Bank Manual ke Rekening PUTRIJALVA</div>
              <div style="font-size: 12px; color: #6B7280;">Memerlukan upload konfirmasi transfer yang diverifikasi tim finance kami.</div>
            </div>
            <button
              type="button"
              onclick="document.getElementById('manualPaymentFormSection').style.display = document.getElementById('manualPaymentFormSection').style.display === 'none' ? 'block' : 'none'"
              class="btn btn-outline"
              style="padding: 7px 14px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; background: #fff;"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
              Tampilkan Rekening & Form &darr;
            </button>
          </div>

          <!-- Collapsible Manual Transfer Section -->
          <div id="manualPaymentFormSection" style="display: none; margin-top: 14px; padding-top: 14px; border-top: 1px dashed #D1D5DB;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 16px;">
              <!-- BCA -->
              <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 6px; padding: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                  <span style="font-weight: 700; font-size: 13px; color: #003399;">Bank Central Asia (BCA)</span>
                  <span style="font-size: 10px; background: #EFF6FF; color: #1D4ED8; padding: 1px 5px; border-radius: 4px; font-weight: 600;">Manual</span>
                </div>
                <div style="font-family: monospace; font-size: 15px; font-weight: 700; color: #111827;">123-456-7890</div>
                <div style="font-size: 11px; color: #6B7280;">a.n. PT PUTRIJALVA INDONESIA</div>
              </div>

              <!-- Mandiri -->
              <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 6px; padding: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                  <span style="font-weight: 700; font-size: 13px; color: #003066;">Bank Mandiri</span>
                  <span style="font-size: 10px; background: #EFF6FF; color: #1D4ED8; padding: 1px 5px; border-radius: 4px; font-weight: 600;">Manual</span>
                </div>
                <div style="font-family: monospace; font-size: 15px; font-weight: 700; color: #111827;">102-00-1234567-8</div>
                <div style="font-size: 11px; color: #6B7280;">a.n. PT PUTRIJALVA INDONESIA</div>
              </div>
            </div>

            <!-- Manual Form -->
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
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nominal yang Ditransfer ($)</label>
                <input type="number" step="0.01" name="transfer_amount" required value="{{ $order->grand_total }}" style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #D1D5DB; border-radius: 6px;">
              </div>

              <div style="grid-column: 1 / -1;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Catatan Transfer (Opsional)</label>
                <input type="text" name="transfer_notes" placeholder="Nomor referensi mutasi atau jam transfer..." style="width: 100%; padding: 8px 12px; font-size: 13px; border: 1px solid #D1D5DB; border-radius: 6px;">
              </div>

              <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; margin-top: 4px;">
                <button type="submit" class="btn btn-primary" style="padding: 9px 20px; font-size: 13px; font-weight: 600;">
                  {{ __('Kirim Bukti Konfirmasi Transfer') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div class="order-details-grid">
    <!-- Left: Order Information -->
    <div>
      <h1 class="order-section-title">{{ __('Detail Pesanan') }}</h1>

      <div class="order-info-grid">
        <div class="order-info-group">
          <div class="order-info-label">{{ __('Nomor Pesanan:') }}</div>
          <p class="order-info-value">{{ $order->order_number }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">{{ __('Metode Pengiriman') }}</div>
          <p class="order-info-value">Standard Express Delivery</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">{{ __('Email') }}</div>
          <p class="order-info-value">{{ $order->customer_email }}</p>
        </div>

        <div class="order-info-group">
          <div class="order-info-label">{{ __('Alamat Pengiriman') }}</div>
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

      <!-- Store Concierge Contact Box -->
      <div style="margin-top: 24px; background: #FAF9F6; border: 1px dashed #D5CBBB; border-radius: 10px; padding: 18px 20px;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
          <div>
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 14px; color: #111827; margin-bottom: 4px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
              <span>{{ __('Customer Concierge & Layanan Bantuan') }}</span>
            </div>
            <p style="margin: 0; font-size: 13px; color: #4B5563; line-height: 1.5;">
              {{ __('Ada pertanyaan mengenai pesanan Anda atau butuh perubahan alamat pengiriman? Hubungi kami via WhatsApp resmi di:') }}
              <strong style="color: #111827;">+62 812-3456-7890</strong>
            </p>
          </div>
          <a href="{{ app(\App\Services\Notification\WhatsAppNotificationService::class)->generateOrderConfirmationUrl($order) }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; background: #059669; color: #FFFFFF; font-size: 12.5px; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
            <span>{{ __('Konfirmasi via WhatsApp') }}</span> &nearr;
          </a>
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
              <span class="order-summary-item-price">{{ app(\App\Services\CurrencyService::class)->format((float) $item->line_total) }}</span>
            </li>
          @endforeach
        </ul>

        <div class="order-summary-calc-row">
          <span>SUBTOTAL</span>
          <span>{{ app(\App\Services\CurrencyService::class)->format((float) $order->subtotal) }}</span>
        </div>

        <div class="order-summary-calc-row">
          <span>SHIPPING</span>
          <span>{{ (float) $order->shipping_cost > 0 ? app(\App\Services\CurrencyService::class)->format((float) $order->shipping_cost) : __('Bebas Biaya Pengiriman') }}</span>
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

  @if($order->transactions->isNotEmpty())
    <div style="margin-top: 36px; background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; padding: 20px;">
      <h3 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 14px 0;">Riwayat Transaksi Payment Gateway</h3>
      <div style="overflow-x: auto;">
        <table style="width: 100%; font-size: 13px; text-align: left; border-collapse: collapse;">
          <thead>
            <tr style="border-bottom: 2px solid #F3F4F6; color: #6B7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
              <th style="padding: 8px 12px;">Gateway</th>
              <th style="padding: 8px 12px;">ID Transaksi</th>
              <th style="padding: 8px 12px;">Metode / Channel</th>
              <th style="padding: 8px 12px;">Nominal (IDR)</th>
              <th style="padding: 8px 12px;">Status</th>
              <th style="padding: 8px 12px;">Waktu</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->transactions as $trx)
              <tr style="border-bottom: 1px solid #F3F4F6;">
                <td style="padding: 10px 12px; font-weight: 600; text-transform: uppercase;">{{ $trx->provider }}</td>
                <td style="padding: 10px 12px; font-family: monospace; font-size: 12px; color: #374151;">{{ $trx->transaction_id }}</td>
                <td style="padding: 10px 12px; text-transform: uppercase;">{{ $trx->payment_channel ?? '-' }}</td>
                <td style="padding: 10px 12px; font-weight: 600;">Rp {{ number_format((float) $trx->amount, 0, ',', '.') }}</td>
                <td style="padding: 10px 12px;">
                  @if($trx->status === 'settlement' || $trx->status === 'paid' || $trx->status === 'capture')
                    <span style="background: #ECFDF5; color: #065F46; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">Lunas</span>
                  @elseif($trx->status === 'pending')
                    <span style="background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">Menunggu</span>
                  @else
                    <span style="background: #FEE2E2; color: #991B1B; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">{{ ucfirst($trx->status) }}</span>
                  @endif
                </td>
                <td style="padding: 10px 12px; color: #6B7280; font-size: 12px;">{{ $trx->created_at?->format('d M Y, H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>

@push('scripts')
@if(!empty($paymentSession) && ($paymentSession['driver'] ?? '') === 'midtrans')
  <script src="{{ config('payment.providers.midtrans.snap_url') }}" data-client-key="{{ $paymentSession['client_key'] ?? config('payment.providers.midtrans.client_key') }}"></script>
@endif
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Clear client-side cart after order completion
    if (window.cartManager && typeof window.cartManager.clearCart === 'function') {
      window.cartManager.clearCart();
    }

    const payBtn = document.getElementById('payWithPgaBtn');
    const snapToken = @json($paymentSession['token'] ?? null);
    const redirectUrl = @json($paymentSession['redirect_url'] ?? null);
    const isPaid = @json($order->isPaid());
    const checkStatusUrl = @json(route('order.check_status', $order->order_number));

    if (payBtn) {
      payBtn.addEventListener('click', function () {
        if (window.snap && snapToken) {
          window.snap.pay(snapToken, {
            onSuccess: function (result) {
              window.location.reload();
            },
            onPending: function (result) {
              window.location.reload();
            },
            onError: function (result) {
              alert('Pembayaran gagal diproses: ' + (result?.status_message || 'Silakan coba kembali'));
            },
            onClose: function () {
              console.log('Customer closed popup Snap tanpa menyelesaikan pembayaran');
            }
          });
        } else if (redirectUrl) {
          window.location.href = redirectUrl;
        } else {
          alert('Sesi pembayaran belum siap. Silakan refresh halaman.');
        }
      });
    }

    // Auto-polling status pembayaran jika pesanan belum lunas
    if (!isPaid) {
      let pollCount = 0;
      const maxPoll = 150; // maks 10 menit polling (tiap 4 detik)
      const pollTimer = setInterval(function () {
        pollCount++;
        if (pollCount > maxPoll) {
          clearInterval(pollTimer);
          return;
        }

        fetch(checkStatusUrl, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.is_paid === true) {
            clearInterval(pollTimer);
            window.location.reload();
          }
        })
        .catch(function (err) {
          console.warn('Status poll warning:', err);
        });
      }, 4000);
    }
  });
</script>
@endpush
@endsection
