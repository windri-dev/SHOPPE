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
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #111827; margin-bottom: 10px;">
            {{ __('Pilihan Layanan Pengiriman:') }}
          </label>
          <div style="display: flex; flex-direction: column; gap: 8px;" id="shippingOptionsWrapper">
            <label style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border: 1.5px solid #111827; border-radius: 6px; cursor: pointer; background: #FAF9F6; transition: all 0.2s ease;" class="shipping-option-label">
              <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #111827;">
                <input type="radio" name="shipping_service" value="reguler" data-cost="0" checked style="accent-color: #111827;">
                <span>{{ __('Reguler (JNE / SiCepat / Anteraja)') }}</span>
              </span>
              <span style="font-size: 12px; font-weight: 700; color: #059669;">{{ __('GRATIS') }}</span>
            </label>

            <label style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border: 1px solid #E5E7EB; border-radius: 6px; cursor: pointer; background: #FFFFFF; transition: all 0.2s ease;" class="shipping-option-label">
              <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #374151;">
                <input type="radio" name="shipping_service" value="express" data-cost="2" style="accent-color: #111827;">
                <span>{{ __('Express Next Day (JNE YES / SiCepat BEST)') }}</span>
              </span>
              <span style="font-size: 12.5px; font-weight: 600; color: #111827;">{{ \App\Services\CurrencyService::formatPrice(2) }}</span>
            </label>

            <label style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border: 1px solid #E5E7EB; border-radius: 6px; cursor: pointer; background: #FFFFFF; transition: all 0.2s ease;" class="shipping-option-label">
              <span style="display: inline-flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #374151;">
                <input type="radio" name="shipping_service" value="sameday" data-cost="3.5" style="accent-color: #111827;">
                <span>{{ __('Sameday & Instant (Grab / Gojek - Jabodetabek)') }}</span>
              </span>
              <span style="font-size: 12.5px; font-weight: 600; color: #111827;">{{ \App\Services\CurrencyService::formatPrice(3.5) }}</span>
            </label>
          </div>
          <input type="hidden" name="shipping_cost" id="checkoutShippingCost" value="0">
        </div>

        <div class="checkout-payment-methods">
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="bank_transfer" class="payment-method-radio" checked>
              <span class="payment-method-name">{{ __('Direct bank transfer') }}</span>
            </label>
          </div>
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="qris" class="payment-method-radio">
              <span class="payment-method-name">QRIS Instant Payment</span>
            </label>
          </div>
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="credit_card" class="payment-method-radio">
              <span class="payment-method-name">Credit Card / Debit Online</span>
            </label>
          </div>
        </div>

        <!-- Hidden input for JSON cart items submitted by checkout form handler -->
        <div id="hiddenCartInputs"></div>

        <button type="submit" class="btn-place-order">
          {{ __('PLACE ORDER') }}
        </button>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Shipping Courier Option Selection & Dynamic Grand Total
    const shippingRadios = document.querySelectorAll('input[name="shipping_service"]');
    const shippingHidden = document.getElementById('checkoutShippingCost');
    const shippingLabels = document.querySelectorAll('.shipping-option-label');

    shippingRadios.forEach(function (radio) {
      radio.addEventListener('change', function () {
        const cost = parseFloat(this.getAttribute('data-cost')) || 0;
        if (shippingHidden) shippingHidden.value = String(cost);

        shippingLabels.forEach(function (lbl) {
          lbl.style.borderColor = '#E5E7EB';
          lbl.style.background = '#FFFFFF';
        });
        const currentLabel = this.closest('.shipping-option-label');
        if (currentLabel) {
          currentLabel.style.borderColor = '#111827';
          currentLabel.style.background = '#FAF9F6';
        }

        if (window.cartManager) {
          window.cartManager.currentShippingCost = cost;
          if (typeof window.cartManager.renderCheckoutPage === 'function') {
            window.cartManager.renderCheckoutPage();
          } else if (typeof window.cartManager.updateTotals === 'function') {
            window.cartManager.updateTotals();
          }
        }
      });
    });

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
