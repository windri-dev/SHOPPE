@extends('layouts.storefront')

@section('title', 'Checkout - PUTRI.J')

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
            <span>Alamat Pengiriman Terdaftar</span>
          </div>
          <div style="font-size: 14px; color: #111827;">
            <strong id="bannerRecipientName">{{ $user->name }}</strong>
            <span style="color: #6B7280; margin: 0 6px;">|</span>
            <span id="bannerRecipientPhone" style="font-weight: 600;">{{ $user->phone ?: 'No. HP belum diisi' }}</span>
            <span id="bannerAddressLabel" style="margin-left: 8px; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; background: #FEF3C7; color: #92400E;">{{ $user->address_label ?: 'Rumah' }}</span>
          </div>
          <div id="bannerFullAddress" style="font-size: 13px; color: #4B5563; margin-top: 4px;">
            @if($user->address)
              {{ $user->address }}, {{ implode(', ', array_filter([$user->district, $user->city, $user->province, $user->postcode])) }}
            @else
              Alamat pengiriman utama belum dilengkapi di profil Anda. Silakan isi di bawah ini (otomatis tersimpan ke akun Anda).
            @endif
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          @if(count($addressBook) > 1)
            <select id="savedAddressSelector" class="checkout-input" style="margin: 0; font-size: 12px; padding: 8px 12px; height: auto; width: auto; background: #FAF9F6; font-weight: 600;">
              @foreach($addressBook as $idx => $addr)
                <option value="{{ $idx }}" data-addr="{{ json_encode($addr) }}" {{ !empty($addr['is_primary']) ? 'selected' : '' }}>
                  Pilih: {{ $addr['label'] ?? 'Alamat' }} - {{ Str::limit($addr['street_address'] ?? '', 28) }}
                </option>
              @endforeach
            </select>
          @endif
          <a href="{{ route('account.index', ['tab' => 'addresses']) }}" class="btn btn-outline" style="font-size: 12px; padding: 8px 14px;">
            Kelola Buku Alamat
          </a>
        </div>
      </div>
    </div>
  @endauth

  <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm" class="checkout-grid">
    @csrf

    <!-- Billing & Shipping Details -->
    <div class="checkout-billing-form">
      <h2 class="checkout-section-title">Detail Penerima &amp; Alamat Pengiriman</h2>
      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="first_name" id="chkFirstName" value="{{ old('first_name', auth()->user()->first_name ?? '') }}" placeholder="Nama Depan *" class="checkout-input" required>
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="last_name" id="chkLastName" value="{{ old('last_name', auth()->user()->last_name ?? '') }}" placeholder="Nama Belakang *" class="checkout-input" required>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="tel" name="phone" id="chkPhone" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="Nomor Telepon / WhatsApp *" class="checkout-input" required>
        </div>
        <div class="checkout-field-wrap">
          <input type="email" name="email" id="chkEmail" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="Alamat Email *" class="checkout-input" required>
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
              <option value="Rumah" {{ $selectedLabel === 'Rumah' ? 'selected' : '' }}>Label: Rumah</option>
              <option value="Kantor" {{ $selectedLabel === 'Kantor' ? 'selected' : '' }}>Label: Kantor</option>
              <option value="Apartemen" {{ $selectedLabel === 'Apartemen' ? 'selected' : '' }}>Label: Apartemen</option>
              <option value="Kos" {{ $selectedLabel === 'Kos' ? 'selected' : '' }}>Label: Kos</option>
            </select>
            <span class="checkout-select-arrow">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </span>
          </div>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="province" id="chkProvince" value="{{ old('province', auth()->user()->province ?? '') }}" placeholder="Provinsi (Contoh: DKI Jakarta)" class="checkout-input">
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="city" id="chkCity" value="{{ old('city', auth()->user()->city ?? '') }}" placeholder="Kota / Kabupaten *" class="checkout-input" required>
        </div>
      </div>

      <div class="checkout-form-row">
        <div class="checkout-field-wrap">
          <input type="text" name="district" id="chkDistrict" value="{{ old('district', auth()->user()->district ?? '') }}" placeholder="Kecamatan (Contoh: Kebayoran Baru)" class="checkout-input">
        </div>
        <div class="checkout-field-wrap">
          <input type="text" name="postcode" id="chkPostcode" value="{{ old('postcode', auth()->user()->postcode ?? '') }}" placeholder="Kode Pos *" class="checkout-input" required>
        </div>
      </div>

      <div class="checkout-field-wrap">
        <input type="text" name="street_address" id="chkStreetAddress" value="{{ old('street_address', auth()->user()->address ?? '') }}" placeholder="Alamat Lengkap (Nama Jalan, Gedung, No. Rumah, RT/RW) *" class="checkout-input" required>
      </div>

      <div class="checkout-field-wrap">
        <textarea name="order_notes" id="chkOrderNotes" rows="3" placeholder="Catatan untuk kurir / patokan lokasi (opsional)..." class="checkout-input" style="min-height: 80px; resize: vertical;">{{ old('order_notes', auth()->user()->address_notes ?? '') }}</textarea>
      </div>

      @auth
        <div style="margin-top: 8px;">
          <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: #374151; cursor: pointer;">
            <input type="checkbox" name="save_to_profile" value="1" checked>
            <span>Simpan perubahan alamat ini sebagai lokasi pengiriman utama di profil saya</span>
          </label>
        </div>
      @endauth
    </div>

    <!-- Order Review -->
    <div class="checkout-order-col">
      <div class="checkout-order-card">
        <h2 class="checkout-section-title" style="margin-bottom: 24px;">Your Order</h2>
        <div id="checkoutItemsList">
          <!-- Populated from cart JS -->
        </div>

        <div class="checkout-payment-methods">
          <div class="payment-method-item">
            <label class="payment-method-header">
              <input type="radio" name="payment_method" value="bank_transfer" class="payment-method-radio" checked>
              <span class="payment-method-name">Direct bank transfer</span>
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
          PLACE ORDER
        </button>
      </div>
    </div>
  </form>
</div>

@auth
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
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
@endauth
@endsection
