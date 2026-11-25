@extends('layouts.storefront')

@section('title', 'Checkout - PUTRIJALVA')

@section('content')
<div class="container checkout-page">
  <div class="checkout-header">
    <h1 class="checkout-title">Checkout</h1>
  </div>

  @if($errors->any())
    <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 6px; margin-bottom: 30px; font-size: 14px;">
      <strong>Mohon lengkapi formulir dengan benar:</strong>
      <ul style="margin-top: 8px; padding-left: 20px;">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @auth
    @php
      $user = auth()->user();
      $addressBook = $user->address_book;
    @endphp
    <!-- Shopee-Style Active Shipping Address Banner -->
    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; margin-bottom: 28px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
      <div style="height: 4px; background: repeating-linear-gradient(45deg, #A18A68, #A18A68 24px, transparent 24px, transparent 32px, #111827 32px, #111827 56px, transparent 56px, transparent 64px);"></div>
      <div style="padding: 18px 24px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
          <div style="display: flex; align-items: center; gap: 8px; color: #A18A68; font-weight: 700; font-size: 14px; margin-bottom: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span>{{ __('Alamat Pengiriman Terdaftar') }}</span>
          </div>
          <div style="font-size: 14px; color: #111827;">
            <strong id="bannerRecipientName">{{ $user->name }}</strong>
            <span style="color: #6B7280; margin: 0 6px;">|</span>
            <span id="bannerRecipientPhone" style="font-weight: 600;">{{ $user->phone ?: __('No. HP belum diisi') }}</span>
            <span id="bannerAddressLabel" style="margin-left: 8px; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; background: #FEF3C7; color: #92400E;">{{ __($user->address_label ?: 'Rumah') }}</span>
          </div>
          <div id="bannerFullAddress" style="font-size: 13px; color: #4B5563; margin-top: 4px;">
            @if($user->address)
              {{ $user->address }}, {{ implode(', ', array_filter([$user->district, $user->city, $user->province, $user->postcode])) }}
            @else
              {{ __('Alamat pengiriman utama belum dilengkapi di profil Anda. Silakan isi di bawah ini (otomatis tersimpan ke akun Anda).') }}
            @endif
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          @if(count($addressBook) > 1)
            <select id="savedAddressSelector" class="checkout-input" style="margin: 0; font-size: 12px; padding: 8px 12px; height: auto; width: auto; background: #FAF9F6; font-weight: 600;">
              @foreach($addressBook as $idx => $addr)
                <option value="{{ $idx }}" data-addr="{{ json_encode($addr) }}" {{ !empty($addr['is_primary']) ? 'selected' : '' }}>
                  {{ __('Pilih:') }} {{ $addr['label'] ?? __('Rumah') }} - {{ Str::limit($addr['street_address'] ?? '', 28) }}
                </option>
              @endforeach
            </select>
          @endif
          <a href="{{ route('account.index', ['tab' => 'addresses']) }}" class="btn btn-outline" style="font-size: 12px; padding: 8px 14px;">
            {{ __('Kelola Buku Alamat') }}
          </a>
        </div>
      </div>
    </div>
  @endauth

  @guest
    <!-- Guest Checkout Notice Banner -->
    <div style="background: #FDFBF7; border: 1px solid #EFE8DC; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 38px; height: 38px; border-radius: 8px; background: #FAF7F2; border: 1px solid #EAE4DA; display: flex; align-items: center; justify-content: center; color: #A18A68; flex-shrink: 0;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <div>
          <div style="font-weight: 700; font-size: 14.5px; color: #111827;">{{ __('Checkout Sebagai Tamu (Tanpa Perlu Login)') }}</div>
          <div style="font-size: 13px; color: #6B7280; margin-top: 2px;">{{ __('Pesanan Anda langsung diproses. Mohon pastikan nomor telepon/WhatsApp aktif agar kami dapat mengirimkan konfirmasi pesanan.') }}</div>
        </div>
      </div>
      <a href="{{ route('login') }}" class="btn btn-outline" style="font-size: 12px; padding: 8px 16px; border-color: #D1D5DB; color: #374151; font-weight: 600;">
        {{ __('Sudah Punya Akun? Masuk di Sini') }} &rarr;
      </a>
    </div>
  @endguest

  <!-- Store Concierge Contact Information Banner -->
  <div style="background: #FAF9F6; border: 1px dashed #D5CBBB; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: #4B5563;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
      <span>
        <strong>{{ __('Butuh Bantuan Checkout atau Konfirmasi Ukuran?') }}</strong>
        {{ __('Layanan Butik & WhatsApp Concierge:') }} <strong style="color: #111827;">+62 812-3456-7890</strong>
      </span>
    </div>
    <a href="https://wa.me/6281234567890?text={{ rawurlencode('Halo Concierge PUTRIJALVA, saya butuh bantuan saat checkout.') }}" target="_blank" rel="noopener noreferrer" style="font-size: 12px; font-weight: 700; color: #047857; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 5px 12px; border-radius: 6px;">
      <span>Chat WhatsApp</span> &nearr;
    </a>
  </div>

  <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" class="checkout-grid">
    @csrf

    <!-- Billing & Shipping Details -->
    <div class="checkout-billing-form">
      <h2 class="checkout-section-title">{{ __('Detail Penerima & Alamat Pengiriman') }}</h2>
      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="first_name" id="chkFirstName" value="{{ old('first_name', auth()->user()->first_name ?? '') }}" placeholder="{{ __('Nama Depan *') }}" class="checkout-input" required>
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="last_name" id="chkLastName" value="{{ old('last_name', auth()->user()->last_name ?? '') }}" placeholder="{{ __('Nama Belakang *') }}" class="checkout-input" required>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="tel" name="phone" id="chkPhone" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="{{ __('Nomor Telepon / WhatsApp Aktif *') }}" class="checkout-input" required>
          <small style="display: block; font-size: 11px; color: #6B7280; margin-top: 4px;">{{ __('Wajib diisi untuk konfirmasi status pesanan dan koordinasi kurir.') }}</small>
        </div>
        <div class="checkout-field-wrap">
          <input type="email" name="email" id="chkEmail" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="{{ __('Alamat Email Aktif *') }}" class="checkout-input" required>
          <small style="display: block; font-size: 11px; color: #6B7280; margin-top: 4px;">{{ __('Invoice resmi dan pelacakan akan dikirimkan ke email ini.') }}</small>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <div class="checkout-select-wrap">
            @php $selectedCountry = old('country', auth()->user()->country ?? 'Indonesia'); @endphp
            <select name="country" id="chkCountry" class="checkout-select" required>
              <option value="Indonesia" {{ $selectedCountry === 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
              <option value="Singapore" {{ $selectedCountry === 'Singapore' ? 'selected' : '' }}>Singapore</option>
              <option value="Malaysia" {{ $selectedCountry === 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
            </select>
            <span class="checkout-select-arrow">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </span>
          </div>
        </div>
        <div class="checkout-field-wrap">
          <div class="checkout-select-wrap">
            @php $selectedLabel = old('address_label', auth()->user()->address_label ?? 'Rumah'); @endphp
            <select name="address_label" id="chkAddressLabel" class="checkout-select">
              <option value="Rumah" {{ $selectedLabel === 'Rumah' ? 'selected' : '' }}>{{ __('Label: Rumah') }}</option>
              <option value="Kantor" {{ $selectedLabel === 'Kantor' ? 'selected' : '' }}>{{ __('Label: Kantor') }}</option>
              <option value="Apartemen" {{ $selectedLabel === 'Apartemen' ? 'selected' : '' }}>{{ __('Label: Apartemen') }}</option>
              <option value="Kos" {{ $selectedLabel === 'Kos' ? 'selected' : '' }}>{{ __('Label: Kos') }}</option>
            </select>
            <span class="checkout-select-arrow">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </span>
          </div>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="province" id="chkProvince" value="{{ old('province', auth()->user()->province ?? '') }}" placeholder="{{ __('Provinsi (Contoh: DKI Jakarta)') }}" class="checkout-input">
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="city" id="chkCity" value="{{ old('city', auth()->user()->city ?? '') }}" placeholder="{{ __('Kota / Kabupaten *') }}" class="checkout-input" required>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="district" id="chkDistrict" value="{{ old('district', auth()->user()->district ?? '') }}" placeholder="{{ __('Kecamatan (Contoh: Kebayoran Baru)') }}" class="checkout-input">
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="postcode" id="chkPostcode" value="{{ old('postcode', auth()->user()->postcode ?? '') }}" placeholder="{{ __('Kode Pos *') }}" class="checkout-input" required>
        </div>
      </div>

      <div class="checkout-field-wrap">
        <input type="text" name="street_address" id="chkStreetAddress" value="{{ old('street_address', auth()->user()->address ?? '') }}" placeholder="{{ __('Alamat Lengkap (Nama Jalan, Gedung, No. Rumah, RT/RW) *') }}" class="checkout-input" required>
      </div>

      <div class="checkout-field-wrap">
        <textarea name="order_notes" id="chkOrderNotes" rows="3" placeholder="{{ __('Catatan untuk kurir / patokan lokasi (opsional)...') }}" class="checkout-input" style="min-height: 80px; resize: vertical;">{{ old('order_notes', auth()->user()->address_notes ?? '') }}</textarea>
      </div>

      @auth
        <div style="margin-top: 8px;">
          <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: #374151; cursor: pointer;">
            <input type="checkbox" name="save_to_profile" value="1" checked>
            <span>{{ __('Simpan perubahan alamat ini sebagai lokasi pengiriman utama di profil saya') }}</span>
          </label>
        </div>
      @endauth
    </div>

    <!-- Order Review -->
    <div class="checkout-order-col">
      <div class="checkout-order-card">
        <h2 class="checkout-section-title" style="margin-bottom: 24px;">{{ __('Your Order') }}</h2>
        <div id="checkoutItemsList">
          <!-- Populated from cart JS -->
        </div>

        <!-- Shipping Method Selection -->
        <div style="margin: 20px 0; padding-top: 18px; border-top: 1px solid #E5E7EB;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #111827; margin: 0;">
              {{ __('Pilihan Layanan Pengiriman:') }}
            </label>
            <span id="shippingZoneBadge" style="font-size: 11px; font-weight: 600; background: #F3F4F6; color: #4B5563; padding: 2px 8px; border-radius: 9999px;">
              Memuat tarif...
            </span>
          </div>
          <div style="display: flex; flex-direction: column; gap: 8px;" id="shippingOptionsWrapper">
            <!-- Initial SSR fallback, dynamically re-rendered by fetchShippingRates() -->
            <label style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1.5px solid #111827; border-radius: 6px; cursor: pointer; background: #FAF9F6; transition: all 0.2s ease;" class="shipping-option-label">
              <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #111827;">
                <input type="radio" name="shipping_service" value="reguler" data-cost="0" checked style="accent-color: #111827;">
                <span>{{ __('Reguler (JNE REG / SiCepat REG / J&T EZ)') }}</span>
              </span>
              <span style="font-size: 12px; font-weight: 700; color: #059669;">{{ __('GRATIS') }}</span>
            </label>
            <label style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1px solid #E5E7EB; border-radius: 6px; cursor: pointer; background: #FFFFFF; transition: all 0.2s ease;" class="shipping-option-label">
              <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #374151;">
                <input type="radio" name="shipping_service" value="express" data-cost="2" style="accent-color: #111827;">
                <span>{{ __('Express Next Day (JNE YES / SiCepat BEST)') }}</span>
              </span>
              <span style="font-size: 12.5px; font-weight: 600; color: #111827;">{{ \App\Services\CurrencyService::formatPrice(2) }}</span>
            </label>
          </div>
          <input type="hidden" name="shipping_cost" id="checkoutShippingCost" value="0">
        </div>

        <!-- Payment Method Selection -->
        <div class="checkout-payment-methods" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #E5E7EB;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #111827; margin: 0;">
              {{ __('Pilihan Metode Pembayaran:') }}
            </label>
            <span style="font-size: 11px; color: #059669; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
              <span>{{ __('SSL 256-Bit Aman') }}</span>
            </span>
          </div>

          <div style="display: flex; flex-direction: column; gap: 10px;" id="paymentOptionsWrapper">
            @php
              $activeBanksList = \App\Models\BankAccount::active()->ordered()->get();
            @endphp

            <!-- 1. Bank Transfer Manual -->
            <div class="payment-card-box" id="cardBankTransfer" style="border: 1.5px solid #111827; border-radius: 10px; background: #FAF9F6; transition: all 0.2s ease; overflow: hidden;">
              <label class="payment-method-label" style="display: flex; align-items: center; justify-content: space-between; padding: 13px 16px; cursor: pointer; margin: 0;">
                <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 700; color: #111827;">
                  <input type="radio" name="payment_method" value="bank_transfer" class="payment-method-radio" checked style="accent-color: #111827; width: 16px; height: 16px;">
                  <span>{{ __('Transfer Bank Manual (Rekening Butik)') }}</span>
                </span>
                <span style="font-size: 10.5px; background: #EFF6FF; color: #1D4ED8; font-weight: 700; padding: 2px 8px; border-radius: 99px;">Paling Populer</span>
              </label>

              <!-- Sub-section: Pilihan Rekening Bank Aktif -->
              <div id="bankTransferDetails" style="padding: 0 16px 16px 16px; display: flex; flex-direction: column; gap: 10px;">
                <p style="margin: 0 0 4px 0; font-size: 12px; color: #6B7280; line-height: 1.45;">
                  {{ __('Pilih salah satu rekening bank tujuan. Nomor rekening dan instruksi pembayaran akan dicantumkan lengkap pada invoice pesanan:') }}
                </p>

                <div style="display: flex; flex-direction: column; gap: 8px;" id="bankItemsList">
                  @forelse($activeBanksList as $idx => $bankItem)
                    <label class="bank-account-item" style="display: flex; flex-direction: column; gap: 8px; padding: 12px 14px; background: #FFFFFF; border: 1.5px solid {{ $loop->first ? '#A18A68' : '#E5E7EB' }}; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;">
                      <!-- Baris 1: Radio + Logo + Nama Bank & Nama Pemilik -->
                      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                          <input type="radio" name="bank_account_id" value="{{ $bankItem->id }}" data-bank-name="{{ $bankItem->bank_name }}" class="bank-choice-radio" {{ $loop->first ? 'checked' : '' }} style="accent-color: #A18A68; width: 16px; height: 16px;">
                          <div style="display: flex; align-items: center; gap: 8px;">
                            {!! $bankItem->bank_icon_svg !!}
                            <span style="font-size: 13.5px; font-weight: 700; color: #111827;">{{ $bankItem->bank_name }}</span>
                          </div>
                        </div>
                        <span style="font-size: 11px; color: #6B7280;">a.n. <strong>{{ $bankItem->account_holder }}</strong></span>
                      </div>

                      <!-- Baris 2: Kotak Nomor Rekening & Tombol Salin -->
                      <div style="display: flex; align-items: center; justify-content: space-between; background: #FAF9F6; border: 1px solid #EFE8DC; border-radius: 6px; padding: 6px 10px; width: 100%; box-sizing: border-box; gap: 8px;">
                        <span style="font-family: 'DM Mono', monospace, ui-monospace; font-size: 12.5px; font-weight: 700; color: #111827; white-space: nowrap;">{{ $bankItem->account_number }}</span>
                        <button type="button" onclick="copyAccNumber('{{ $bankItem->clean_account_number }}', '{{ $bankItem->bank_name }}', event)" style="background: #FFFFFF; color: #8F7655; border: 1px solid #A18A68; padding: 2px 8px; border-radius: 4px; font-size: 10.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 3px; flex-shrink: 0;" title="Salin nomor rekening">
                          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                          <span>Salin</span>
                        </button>
                      </div>
                    </label>
                  @empty
                    <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 6px; padding: 10px; font-size: 12px; color: #92400E;">
                      Rekening butik sedang dalam pemeliharaan. Silakan hubungi Concierge via Livechat.
                    </div>
                  @endforelse
                </div>
              </div>
            </div>

            <!-- 2. QRIS Instant -->
            <div class="payment-card-box" id="cardQris" style="border: 1px solid #E5E7EB; border-radius: 10px; background: #FFFFFF; transition: all 0.2s ease; overflow: hidden;">
              <label class="payment-method-label" style="display: flex; align-items: center; justify-content: space-between; padding: 13px 16px; cursor: pointer; margin: 0;">
                <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: #374151;">
                  <input type="radio" name="payment_method" value="qris" class="payment-method-radio" style="accent-color: #111827; width: 16px; height: 16px;">
                  <span>{{ __('QRIS Instant Payment (Otomatis & Cepat)') }}</span>
                </span>
                <span style="font-size: 10.5px; background: #ECFDF5; color: #059669; font-weight: 700; padding: 2px 8px; border-radius: 99px;">Instan</span>
              </label>
              <div id="qrisDetails" style="padding: 0 16px 14px 16px; display: none;">
                <div style="background: #FAF9F6; border: 1px dashed #D1D5DB; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #4B5563; line-height: 1.5;">
                  <div style="font-weight: 700; color: #111827; margin-bottom: 2px; display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span>Scan QRIS Instan</span>
                  </div>
                  {{ __('Kode QR dinamis akan langsung muncul di layar setelah Anda menekan tombol Buat Pesanan. Scan via BCA Mobile, Livin, BRImo, BNI, GoPay, OVO, ShopeePay, atau DANA. Terverifikasi detik itu juga tanpa upload bukti transfer.') }}
                </div>
              </div>
            </div>

            <!-- 3. Credit / Debit Card -->
            <div class="payment-card-box" id="cardCreditCard" style="border: 1px solid #E5E7EB; border-radius: 10px; background: #FFFFFF; transition: all 0.2s ease; overflow: hidden;">
              <label class="payment-method-label" style="display: flex; align-items: center; justify-content: space-between; padding: 13px 16px; cursor: pointer; margin: 0;">
                <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: #374151;">
                  <input type="radio" name="payment_method" value="credit_card" class="payment-method-radio" style="accent-color: #111827; width: 16px; height: 16px;">
                  <span>{{ __('Kartu Kredit / Debit Online (Visa / Mastercard)') }}</span>
                </span>
                <span style="font-size: 10.5px; background: #F3F4F6; color: #4B5563; font-weight: 700; padding: 2px 8px; border-radius: 99px;">3D Secure</span>
              </label>
              <div id="creditCardDetails" style="padding: 0 16px 14px 16px; display: none;">
                <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #4B5563; line-height: 1.5;">
                  {{ __('Pembayaran kartu aman standar internasional dengan enkripsi TLS 1.3 dan verifikasi kode OTP 3D Secure langsung dari bank penerbit kartu Anda.') }}
                </div>
              </div>
            </div>

            <!-- 4. Cash on Delivery (COD) -->
            <div class="payment-card-box" id="cardCod" style="border: 1px solid #E5E7EB; border-radius: 10px; background: #FFFFFF; transition: all 0.2s ease; overflow: hidden;">
              <label class="payment-method-label" style="display: flex; align-items: center; justify-content: space-between; padding: 13px 16px; cursor: pointer; margin: 0;">
                <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: #374151;">
                  <input type="radio" name="payment_method" value="cod" class="payment-method-radio" style="accent-color: #111827; width: 16px; height: 16px;">
                  <span>{{ __('Bayar di Tempat / COD (Cash on Delivery)') }}</span>
                </span>
                <span style="font-size: 10.5px; background: #FEF3C7; color: #92400E; font-weight: 700; padding: 2px 8px; border-radius: 99px;">Tunai</span>
              </label>
              <div id="codDetails" style="padding: 0 16px 14px 16px; display: none;">
                <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #92400E; line-height: 1.5;">
                  {{ __('Harap siapkan uang pas sebesar total pembayaran saat kurir ekspedisi mengantarkan paket busana ke alamat Anda.') }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Hidden input for JSON cart items submitted by checkout form handler -->
        <div id="hiddenCartInputs"></div>

        <button type="submit" class="btn-place-order" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <span>{{ __('KONFIRMASI & BUAT PESANAN') }}</span>
        </button>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Dynamic Shipping Courier Rate Calculation API
    const shippingHidden = document.getElementById('checkoutShippingCost');
    const shippingZoneBadge = document.getElementById('shippingZoneBadge');
    const shippingWrapper = document.getElementById('shippingOptionsWrapper');

    function fetchShippingRates() {
      const provInput = document.getElementById('chkProvince');
      const cityInput = document.getElementById('chkCity');
      const prov = provInput ? provInput.value.trim() : '';
      const city = cityInput ? cityInput.value.trim() : '';
      const subtotalUsd = (window.cartManager && typeof window.cartManager.getCartSubtotal === 'function') ? window.cartManager.getCartSubtotal() : 0;
      const itemCount = (window.cartManager && typeof window.cartManager.getCartCount === 'function') ? window.cartManager.getCartCount() : 1;
      const weightKg = Math.max(0.5, (itemCount * 0.25));

      if (shippingZoneBadge) shippingZoneBadge.textContent = 'Menghitung tarif kurir...';

      fetch('{{ route('shipping.calculate') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          service: 'reguler',
          province: prov,
          city: city,
          subtotal: subtotalUsd,
          weight_kg: weightKg
        })
      })
      .then(res => res.json())
      .then(res => {
        if (!res.success || !res.data) return;
        const data = res.data;
        const services = data.available_services || [];

        if (shippingZoneBadge) {
          const zoneLabels = {
            'jabodetabek': 'Zona: Jabodetabek (Same-Day & Instant)',
            'jawa': 'Zona: Pulau Jawa (Reguler & Next Day)',
            'sumatera': 'Zona: Sumatera (Antar Pulau)',
            'bali_nusra': 'Zona: Bali & Nusa Tenggara',
            'kalimantan': 'Zona: Kalimantan',
            'sulawesi': 'Zona: Sulawesi',
            'maluku_papua': 'Zona: Maluku & Papua'
          };
          shippingZoneBadge.textContent = zoneLabels[data.zone] || 'Zona: Domestik';
        }

        if (shippingWrapper && services.length > 0) {
          shippingWrapper.innerHTML = '';
          services.forEach((srv, idx) => {
            const isChecked = idx === 0;
            const card = document.createElement('label');
            card.className = 'shipping-option-label';
            card.style.cssText = `display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1.5px solid ${isChecked ? '#111827' : '#E5E7EB'}; border-radius: 8px; cursor: pointer; background: ${isChecked ? '#FAF9F6' : '#FFFFFF'}; transition: all 0.2s ease;`;

            card.innerHTML = `
              <div style="display: flex; align-items: center; gap: 10px;">
                <input type="radio" name="shipping_service" value="${srv.service}" data-cost="${srv.cost_usd}" ${isChecked ? 'checked' : ''} style="accent-color: #111827;">
                <div>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 13px; font-weight: 700; color: #111827;">${srv.name}</span>
                    ${srv.badge ? `<span style="font-size: 10px; font-weight: 700; background: ${srv.is_free ? '#ECFDF5' : '#EFF6FF'}; color: ${srv.is_free ? '#059669' : '#1D4ED8'}; padding: 1px 6px; border-radius: 4px;">${srv.badge}</span>` : ''}
                  </div>
                  <div style="font-size: 11.5px; color: #6B7280; margin-top: 2px;">
                    Estimasi tiba: <strong>${srv.eta}</strong> &bull; Ekspedisi: ${srv.courier}
                  </div>
                </div>
              </div>
              <div style="text-align: right;">
                <span style="font-size: 13px; font-weight: 700; color: ${srv.is_free ? '#059669' : '#111827'};">
                  ${srv.cost_formatted}
                </span>
              </div>
            `;
            shippingWrapper.appendChild(card);

            if (isChecked && shippingHidden) {
              shippingHidden.value = String(srv.cost_usd);
              if (window.cartManager) {
                window.cartManager.currentShippingCost = srv.cost_usd;
                if (typeof window.cartManager.updateTotals === 'function') {
                  window.cartManager.updateTotals();
                }
              }
            }
          });

          // Bind change events to new radio options
          const newRadios = shippingWrapper.querySelectorAll('input[name="shipping_service"]');
          newRadios.forEach(r => {
            r.addEventListener('change', function () {
              const cost = parseFloat(this.getAttribute('data-cost')) || 0;
              if (shippingHidden) shippingHidden.value = String(cost);

              shippingWrapper.querySelectorAll('.shipping-option-label').forEach(lbl => {
                lbl.style.borderColor = '#E5E7EB';
                lbl.style.background = '#FFFFFF';
              });
              const parent = this.closest('.shipping-option-label');
              if (parent) {
                parent.style.borderColor = '#111827';
                parent.style.background = '#FAF9F6';
              }

              if (window.cartManager) {
                window.cartManager.currentShippingCost = cost;
                if (typeof window.cartManager.updateTotals === 'function') {
                  window.cartManager.updateTotals();
                }
              }
            });
          });
        }
      })
      .catch(err => {
        console.warn('Shipping rate fetch error:', err);
        if (shippingZoneBadge) shippingZoneBadge.textContent = 'Standar Domestik';
      });
    }

    // Debounced trigger when city or province changes
    let shippingTimer = null;
    function triggerShippingRecalc() {
      clearTimeout(shippingTimer);
      shippingTimer = setTimeout(fetchShippingRates, 350);
    }

    const cityEl = document.getElementById('chkCity');
    const provEl = document.getElementById('chkProvince');
    if (cityEl) {
      cityEl.addEventListener('input', triggerShippingRecalc);
      cityEl.addEventListener('change', triggerShippingRecalc);
    }
    if (provEl) {
      provEl.addEventListener('input', triggerShippingRecalc);
      provEl.addEventListener('change', triggerShippingRecalc);
    }

    // Initial fetch on page load
    setTimeout(fetchShippingRates, 150);

    // Global copy function with toast
    window.copyAccNumber = function (accountNumber, bankName, e) {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      navigator.clipboard.writeText(accountNumber).then(function () {
        const toast = document.createElement('div');
        toast.className = 'toast toast-success';
        toast.style.cssText = 'position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: #111827; color: #FFFFFF; padding: 11px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; z-index: 99999; box-shadow: 0 10px 25px rgba(0,0,0,0.3); display: flex; align-items: center; gap: 8px;';
        toast.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Nomor rekening ${bankName} (${accountNumber}) berhasil disalin!</span>`;
        document.body.appendChild(toast);
        setTimeout(function () {
          toast.style.opacity = '0';
          toast.style.transition = 'opacity 0.3s ease';
          setTimeout(function () { toast.remove(); }, 300);
        }, 2200);
      }).catch(function () {
        prompt('Salin nomor rekening:', accountNumber);
      });
    };

    // Bank Account Item Radio Selection
    const bankItems = document.querySelectorAll('.bank-account-item');
    bankItems.forEach(function (item) {
      item.addEventListener('click', function () {
        bankItems.forEach(function (b) {
          b.style.borderColor = '#E5E7EB';
          b.style.background = '#FFFFFF';
        });
        this.style.borderColor = '#A18A68';
        this.style.background = '#FAF7F2';
        const radio = this.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
      });
    });

    // Payment Method Option Selection
    const paymentCards = document.querySelectorAll('.payment-card-box');
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

    function syncPaymentDisplay() {
      const selected = document.querySelector('input[name="payment_method"]:checked')?.value || 'bank_transfer';
      
      paymentCards.forEach(function (card) {
        card.style.borderColor = '#E5E7EB';
        card.style.background = '#FFFFFF';
        const nameSpan = card.querySelector('.payment-method-label span span');
        if (nameSpan) nameSpan.style.color = '#374151';
      });

      const bankDetails = document.getElementById('bankTransferDetails');
      const qrisDetails = document.getElementById('qrisDetails');
      const ccDetails = document.getElementById('creditCardDetails');
      const codDetails = document.getElementById('codDetails');

      if (bankDetails) bankDetails.style.display = 'none';
      if (qrisDetails) qrisDetails.style.display = 'none';
      if (ccDetails) ccDetails.style.display = 'none';
      if (codDetails) codDetails.style.display = 'none';

      if (selected === 'bank_transfer') {
        const c = document.getElementById('cardBankTransfer');
        if (c) {
          c.style.borderColor = '#111827';
          c.style.background = '#FAF9F6';
          const nameSpan = c.querySelector('.payment-method-label span span');
          if (nameSpan) nameSpan.style.color = '#111827';
        }
        if (bankDetails) bankDetails.style.display = 'flex';
      } else if (selected === 'qris') {
        const c = document.getElementById('cardQris');
        if (c) {
          c.style.borderColor = '#111827';
          c.style.background = '#FAF9F6';
          const nameSpan = c.querySelector('.payment-method-label span span');
          if (nameSpan) nameSpan.style.color = '#111827';
        }
        if (qrisDetails) qrisDetails.style.display = 'block';
      } else if (selected === 'credit_card') {
        const c = document.getElementById('cardCreditCard');
        if (c) {
          c.style.borderColor = '#111827';
          c.style.background = '#FAF9F6';
          const nameSpan = c.querySelector('.payment-method-label span span');
          if (nameSpan) nameSpan.style.color = '#111827';
        }
        if (ccDetails) ccDetails.style.display = 'block';
      } else if (selected === 'cod') {
        const c = document.getElementById('cardCod');
        if (c) {
          c.style.borderColor = '#111827';
          c.style.background = '#FAF9F6';
          const nameSpan = c.querySelector('.payment-method-label span span');
          if (nameSpan) nameSpan.style.color = '#111827';
        }
        if (codDetails) codDetails.style.display = 'block';
      }
    }

    paymentRadios.forEach(function (radio) {
      radio.addEventListener('change', syncPaymentDisplay);
    });

    syncPaymentDisplay();

    const selector = document.getElementById('savedAddressSelector');
    if (!selector) return;

    selector.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const raw = selectedOption.getAttribute('data-addr');
      if (!raw) return;

      try {
        const addr = JSON.parse(raw);
        const fullName = (addr.recipient_name || '').trim();
        const parts = fullName.split(/\s+/);
        const firstName = parts[0] || '';
        const lastName = parts.slice(1).join(' ') || '-';

        document.getElementById('chkFirstName').value = firstName;
        document.getElementById('chkLastName').value = lastName;
        document.getElementById('chkPhone').value = addr.phone || '';
        document.getElementById('chkProvince').value = addr.province || '';
        document.getElementById('chkCity').value = addr.city || '';
        document.getElementById('chkDistrict').value = addr.district || '';
        document.getElementById('chkPostcode').value = addr.postcode || '';
        document.getElementById('chkStreetAddress').value = addr.street_address || '';
        document.getElementById('chkOrderNotes').value = addr.notes || '';
        if (addr.label) {
          document.getElementById('chkAddressLabel').value = addr.label;
        }

        // Update Banner
        document.getElementById('bannerRecipientName').textContent = fullName;
        document.getElementById('bannerRecipientPhone').textContent = addr.phone || '-';
        document.getElementById('bannerAddressLabel').textContent = addr.label || 'Rumah';
        document.getElementById('bannerFullAddress').textContent = [
          addr.street_address,
          addr.district,
          addr.city,
          addr.province,
          addr.postcode
        ].filter(Boolean).join(', ');
      } catch (e) {}
    });
  });
</script>
@endpush
@endsection
