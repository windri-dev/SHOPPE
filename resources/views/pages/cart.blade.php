@extends('layouts.storefront')

@section('title', __('Keranjang Belanja') . ' - PUTRIJALVA')

@section('content')
<div class="container cart-page">
  <h1 class="cart-page-title">{{ __('Keranjang Belanja') }}</h1>

  <div class="cart-page-layout">
    <!-- Left Column: Cart Items -->
    <div class="cart-items-column">
      <div class="cart-items-list" id="cartTableContainer">
        <!-- Rendered dynamically by cart.js -->
      </div>
    </div>

    <!-- Right Column: Cart Totals -->
    <div class="cart-totals-section">
      <h2 class="cart-totals-heading">{{ __('Cart totals') }}</h2>

      <div class="cart-totals-table">
        <div class="cart-totals-row">
          <span class="cart-totals-label">{{ __('SUBTOTAL') }}</span>
          <span class="cart-totals-val" id="cartPageSubtotal">$ 0.00</span>
        </div>
        <div class="cart-totals-row">
          <span class="cart-totals-label">{{ __('SHIPPING') }}</span>
          <div class="shipping-calc-wrap">
            <div class="shipping-calc-note" id="cartShippingNote">
              @if(app()->getLocale() === 'id')
                Bebas biaya kirim standar untuk pesanan di atas <strong>Rp 500.000</strong>
              @else
                Free standard shipping on orders over <strong>$50</strong>
              @endif
            </div>

            <div class="shipping-calc-toggle" id="shippingCalcToggle" role="button" tabindex="0">
              <span>{{ __('Hitung Ongkos Kirim') }}</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" id="shippingCalcIcon">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>

            <div class="shipping-calc-fields" id="shippingCalcFields" style="display: none;">
              <div class="shipping-calc-field-wrap">
                <select class="shipping-calc-select" id="cartCalcProvince" aria-label="Pilih Provinsi">
                  <option value="">{{ __('PILIH PROVINSI') }}</option>
                  <option value="DKI Jakarta">DKI Jakarta</option>
                  <option value="Jawa Barat">Jawa Barat</option>
                  <option value="Banten">Banten</option>
                  <option value="Jawa Tengah">Jawa Tengah</option>
                  <option value="DI Yogyakarta">DI Yogyakarta</option>
                  <option value="Jawa Timur">Jawa Timur</option>
                  <option value="Bali">Bali</option>
                  <option value="Sumatera Utara">Sumatera Utara</option>
                  <option value="Sumatera Barat">Sumatera Barat</option>
                  <option value="Riau">Riau</option>
                  <option value="Sumatera Selatan">Sumatera Selatan</option>
                  <option value="Lampung">Lampung</option>
                  <option value="Kalimantan Barat">Kalimantan Barat</option>
                  <option value="Kalimantan Timur">Kalimantan Timur</option>
                  <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                  <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                  <option value="Sulawesi Utara">Sulawesi Utara</option>
                  <option value="Papua">Papua</option>
                </select>
                <span class="shipping-calc-select-arrow">&darr;</span>
              </div>

              <div class="shipping-calc-field-wrap">
                <input type="text" class="shipping-calc-input" id="cartCalcCity" placeholder="{{ __('KOTA / KABUPATEN') }}">
              </div>

              <button type="button" class="btn-update-totals" id="btnUpdateShippingTotals">
                {{ __('UPDATE TOTALS') }}
              </button>

              <div id="cartCalcShippingResult" style="font-size: 12px; margin-top: 10px; line-height: 1.5;"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="cart-totals-divider"></div>

      <div class="cart-grand-total-row">
        <span class="cart-grand-total-label">{{ __('TOTAL') }}</span>
        <span class="cart-grand-total-val" id="cartPageTotal">$ 0.00</span>
      </div>

      @guest
        <div style="background: #FDFBF7; border: 1px solid #EFE8DC; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; font-size: 12.5px; color: #6B7280; line-height: 1.5;">
          <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; color: #111827; margin-bottom: 2px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span>{{ __('Checkout Cepat Tanpa Login') }}</span>
          </div>
          {{ __('Anda dapat langsung memesan sebagai Tamu (Guest) tanpa harus login. Cukup lengkapi data penerima dan nomor kontak aktif saat checkout.') }}
        </div>
      @endguest

      <a href="{{ route('checkout.index') }}" class="btn-proceed-checkout">
        {{ __('PROCEED TO CHECKOUT') }}
      </a>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('shippingCalcToggle');
  const fields = document.getElementById('shippingCalcFields');
  const icon = document.getElementById('shippingCalcIcon');
  const updateBtn = document.getElementById('btnUpdateShippingTotals');
  const provEl = document.getElementById('cartCalcProvince');
  const cityEl = document.getElementById('cartCalcCity');
  const resultEl = document.getElementById('cartCalcShippingResult');

  if (toggleBtn && fields) {
    toggleBtn.addEventListener('click', function () {
      const isHidden = fields.style.display === 'none';
      fields.style.display = isHidden ? 'flex' : 'none';
      if (icon) {
        icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
        icon.style.transition = 'transform 0.2s ease';
      }
    });
  }

  if (updateBtn) {
    updateBtn.addEventListener('click', function () {
      const prov = provEl ? provEl.value.trim() : '';
      const city = cityEl ? cityEl.value.trim() : '';

      if (!prov && !city) {
        if (resultEl) {
          resultEl.innerHTML = '<span style="color: #DC2626;">Silakan pilih provinsi atau ketik nama kota.</span>';
        }
        return;
      }

      const subtotalUsd = (window.cartManager && typeof window.cartManager.getCartSubtotal === 'function')
        ? window.cartManager.getCartSubtotal()
        : 0;
      const itemCount = (window.cartManager && typeof window.cartManager.getCartCount === 'function')
        ? window.cartManager.getCartCount()
        : 1;
      const weightKg = Math.max(0.5, (itemCount * 0.25));

      updateBtn.disabled = true;
      updateBtn.textContent = 'Menghitung...';
      if (resultEl) resultEl.innerHTML = '<span style="color: #6B7280;">Mengambil tarif ekspedisi...</span>';

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
        updateBtn.disabled = false;
        updateBtn.textContent = 'UPDATE TOTALS';

        if (!res.success || !res.data) {
          if (resultEl) resultEl.innerHTML = '<span style="color: #DC2626;">Gagal menghitung tarif. Silakan coba lagi.</span>';
          return;
        }

        const data = res.data;
        const matched = data.matched_service || (data.available_services && data.available_services[0]);

        if (matched) {
          if (window.cartManager) {
            window.cartManager.currentShippingCost = matched.cost_usd;
            window.cartManager.updateTotals();
          }

          if (resultEl) {
            resultEl.innerHTML = `
              <div style="background: #FAF9F6; border: 1px solid #EFE8DC; border-radius: 6px; padding: 10px; margin-top: 6px;">
                <div style="font-weight: 700; color: #111827; display: flex; justify-content: space-between;">
                  <span>${matched.name}</span>
                  <span style="color: ${matched.is_free ? '#059669' : '#111827'};">${matched.cost_formatted}</span>
                </div>
                <div style="font-size: 11px; color: #6B7280; margin-top: 2px;">
                  Estimasi tiba: ${matched.eta} &bull; ${matched.badge || ''}
                </div>
              </div>
            `;
          }
        }
      })
      .catch(err => {
        updateBtn.disabled = false;
        updateBtn.textContent = 'UPDATE TOTALS';
        if (resultEl) resultEl.innerHTML = '<span style="color: #DC2626;">Koneksi terputus saat menghitung ongkir.</span>';
      });
    });
  }
});
</script>
@endpush
@endsection
