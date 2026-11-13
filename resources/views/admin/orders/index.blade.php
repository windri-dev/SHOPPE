@extends('layouts.admin')

@section('title', 'Manajemen Pesanan & Pembayaran - PUTRIJALVA Admin')
@section('page_title', 'Manajemen Pesanan & Verifikasi Pembayaran')

@section('content')
<div class="admin-orders-page" style="display: flex; flex-direction: column; gap: 20px;">
  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
        Daftar Semua Pesanan Masuk &amp; Verifikasi Pembayaran
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Pantau status transaksi, verifikasi mutasi transfer pelanggan, input nomor resi kurir, dan kontrol alur pengiriman.
      </p>
    </div>
    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.dashboard') }}" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151;">
        &larr; Dashboard Overview
      </a>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="admin-card" style="padding: 16px 20px; display: flex; flex-direction: column; gap: 14px;">
    <!-- Order Status Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div style="display: flex; gap: 6px; overflow-x: auto; flex-wrap: wrap; align-items: center;">
        <span style="font-size: 12px; font-weight: 600; color: #6B7280; margin-right: 4px;">Status Pesanan:</span>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
           class="chat-filter-pill {{ !request('status') || request('status') === 'all' ? 'active' : '' }}" style="text-decoration: none;">
          Semua ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
           class="chat-filter-pill {{ request('status') === 'pending' ? 'active' : '' }}" style="text-decoration: none;">
          Menunggu ({{ $statusCounts['pending'] }})
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), ['status' => 'processing'])) }}"
           class="chat-filter-pill {{ request('status') === 'processing' ? 'active' : '' }}" style="text-decoration: none;">
          Diproses ({{ $statusCounts['processing'] }})
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), ['status' => 'shipped'])) }}"
           class="chat-filter-pill {{ request('status') === 'shipped' ? 'active' : '' }}" style="text-decoration: none;">
          Dikirim ({{ $statusCounts['shipped'] }})
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), ['status' => 'delivered'])) }}"
           class="chat-filter-pill {{ request('status') === 'delivered' ? 'active' : '' }}" style="text-decoration: none;">
          Selesai ({{ $statusCounts['delivered'] }})
        </a>
        <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), ['status' => 'cancelled'])) }}"
           class="chat-filter-pill {{ request('status') === 'cancelled' ? 'active' : '' }}" style="text-decoration: none;">
          Batal ({{ $statusCounts['cancelled'] }})
        </a>
      </div>

      <!-- Search Box -->
      <form action="{{ route('admin.orders.index') }}" method="GET" style="display: flex; gap: 8px; min-width: 260px;">
        @if(request('status'))
          <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        @if(request('payment_status'))
          <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
        @endif
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari No. Order, Nama, Resi..."
          class="chat-search-input"
          style="padding-left: 12px; width: 220px;"
        >
        <button type="submit" class="table-action-btn" style="background: #1B1B18; color: #fff; padding: 6px 14px;">
          Cari
        </button>
        @if(request('search') || request('payment_status') || (request('status') && request('status') !== 'all'))
          <a href="{{ route('admin.orders.index') }}" class="table-action-btn" style="background: #E5E7EB; color: #374151; padding: 6px 10px; text-decoration: none;">
            Reset
          </a>
        @endif
      </form>
    </div>

    <!-- Payment Status Sub-Tabs -->
    <div style="display: flex; gap: 8px; align-items: center; border-top: 1px dashed #E5E7EB; padding-top: 10px; flex-wrap: wrap;">
      <span style="font-size: 12px; font-weight: 600; color: #6B7280; margin-right: 4px;">Status Pembayaran:</span>
      <a href="{{ route('admin.orders.index', array_merge(request()->except('payment_status', 'page'), ['payment_status' => 'all'])) }}"
         style="font-size: 11.5px; padding: 3px 10px; border-radius: 6px; text-decoration: none; border: 1px solid {{ !request('payment_status') || request('payment_status') === 'all' ? '#111827' : '#D1D5DB' }}; background: {{ !request('payment_status') || request('payment_status') === 'all' ? '#111827' : '#FFFFFF' }}; color: {{ !request('payment_status') || request('payment_status') === 'all' ? '#FFFFFF' : '#4B5563' }};">
        Semua
      </a>
      <a href="{{ route('admin.orders.index', array_merge(request()->except('payment_status', 'page'), ['payment_status' => 'waiting_verification'])) }}"
         style="font-size: 11.5px; padding: 3px 10px; border-radius: 6px; text-decoration: none; border: 1px solid {{ request('payment_status') === 'waiting_verification' ? '#2563EB' : '#BFDBFE' }}; background: {{ request('payment_status') === 'waiting_verification' ? '#2563EB' : '#EFF6FF' }}; color: {{ request('payment_status') === 'waiting_verification' ? '#FFFFFF' : '#1D4ED8' }}; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        Verifikasi Bukti ({{ $paymentCounts['waiting'] }})
      </a>
      <a href="{{ route('admin.orders.index', array_merge(request()->except('payment_status', 'page'), ['payment_status' => 'unpaid'])) }}"
         style="font-size: 11.5px; padding: 3px 10px; border-radius: 6px; text-decoration: none; border: 1px solid {{ request('payment_status') === 'unpaid' ? '#D97706' : '#FEF3C7' }}; background: {{ request('payment_status') === 'unpaid' ? '#D97706' : '#FFFBEB' }}; color: {{ request('payment_status') === 'unpaid' ? '#FFFFFF' : '#B45309' }};">
        Belum Dibayar ({{ $paymentCounts['unpaid'] }})
      </a>
      <a href="{{ route('admin.orders.index', array_merge(request()->except('payment_status', 'page'), ['payment_status' => 'paid'])) }}"
         style="font-size: 11.5px; padding: 3px 10px; border-radius: 6px; text-decoration: none; border: 1px solid {{ request('payment_status') === 'paid' ? '#059669' : '#D1FAE5' }}; background: {{ request('payment_status') === 'paid' ? '#059669' : '#ECFDF5' }}; color: {{ request('payment_status') === 'paid' ? '#FFFFFF' : '#047857' }};">
        Lunas ({{ $paymentCounts['paid'] }})
      </a>
    </div>
  </div>

  <!-- Orders Data Table -->
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>No. Order</th>
            <th>Pelanggan</th>
            <th>Total Tagihan</th>
            <th>Status Bayar</th>
            <th>Metode &amp; Resi Kurir</th>
            <th>Status Kirim</th>
            <th style="text-align: right;">Aksi Verifikasi &amp; Detail</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td>
                <strong style="color: #111827; font-size: 13.5px;">{{ $order->order_number }}</strong>
                <div style="font-size: 11px; color: #6B7280; margin-top: 2px;">
                  {{ $order->created_at?->format('d M Y H:i') }}
                </div>
                @if($order->notes)
                  <div style="font-size: 11px; color: #B45309; margin-top: 4px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 4px;" title="{{ $order->notes }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    <span>{{ $order->notes }}</span>
                  </div>
                @endif
              </td>
              <td>
                <div style="font-weight: 600; color: #111827;">{{ $order->customer_name }}</div>
                <div style="font-size: 12px; color: #6B7280;">{{ $order->customer_email }}</div>
                <div style="font-size: 11px; color: #9CA3AF; display: flex; align-items: center; gap: 6px;">
                  <span>{{ $order->customer_phone ?: '-' }}</span>
                  @if($order->customer_phone)
                    @php
                      $cleanPhone = preg_replace('/[^0-9]/', '', (string) $order->customer_phone);
                      if (str_starts_with($cleanPhone, '0')) {
                          $cleanPhone = '62' . substr($cleanPhone, 1);
                      }
                      $waMsg = urlencode("Halo {$order->customer_name}, kami dari Butik PUTRIJALVA mengonfirmasi pesanan #{$order->order_number} sebesar {$order->formatted_grand_total}." . ($order->tracking_number ? " Paket telah dikirim dengan nomor resi: {$order->tracking_number}." : ""));
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMsg }}" target="_blank" rel="noopener noreferrer" title="Chat WhatsApp Pelanggan" style="color: #25D366; display: inline-flex; align-items: center;">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="#25D366"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    </a>
                  @endif
                </div>
              </td>
              <td>
                <div style="font-weight: 700; color: #111827; font-size: 14px;">{{ $order->formatted_grand_total }}</div>
                <div style="font-size: 11px; color: #6B7280;">{{ $order->items->count() }} jenis produk</div>
              </td>
              <td>
                <!-- Payment Status Indicator -->
                @if($order->payment_status === 'paid')
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #D1FAE5; color: #065F46;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Lunas
                  </span>
                  @if($order->paid_at)
                    <div style="font-size: 10px; color: #6B7280; margin-top: 2px;">
                      {{ $order->paid_at->format('d/m H:i') }}
                    </div>
                  @endif
                @elseif($order->payment_status === 'waiting_verification')
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #DBEAFE; color: #1E40AF;" title="Pelanggan sudah mengonfirmasi transfer">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #2563EB;"></span>
                    Verifikasi Bukti
                  </span>
                @elseif($order->payment_status === 'refunded')
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #991B1B;">
                    Refund
                  </span>
                @else
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; background: #FEF3C7; color: #92400E;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #F59E0B;"></span>
                    Belum Dibayar
                  </span>
                @endif
              </td>
              <td>
                <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; background: #F3F4F6; padding: 2px 6px; border-radius: 4px; color: #374151;">
                  {{ str_replace('_', ' ', $order->payment_method) }}
                </span>
                @if($order->tracking_number)
                  <div style="font-size: 11px; font-family: monospace; color: #047857; margin-top: 3px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <span>{{ $order->tracking_number }}</span>
                  </div>
                @else
                  <div style="font-size: 11px; color: #9CA3AF; margin-top: 2px;">
                    Belum ada resi
                  </div>
                @endif
              </td>
              <td>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" style="display: inline-flex; gap: 6px; align-items: center;">
                  @csrf
                  <select name="status" class="status-select" onchange="this.form.submit()" aria-label="Ubah status {{ $order->order_number }}" style="padding: 4px 8px; font-size: 12px; border-radius: 6px;">
                    @foreach(\App\Enums\OrderStatus::cases() as $statusCase)
                      <option value="{{ $statusCase->value }}" {{ $order->status === $statusCase ? 'selected' : '' }}>
                        {{ $statusCase->label() }}
                      </option>
                    @endforeach
                  </select>
                </form>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: flex-end;">
                  <!-- One-Click Payment Verification Button -->
                  @if($order->payment_status !== 'paid')
                    <form action="{{ route('admin.orders.payment', $order) }}" method="POST" style="margin: 0;">
                      @csrf
                      <input type="hidden" name="payment_status" value="paid">
                      <button
                        type="submit"
                        onclick="return confirm('Konfirmasi bahwa pembayaran untuk {{ $order->order_number }} senilai {{ $order->formatted_grand_total }} telah LUNAS dan diterima?')"
                        class="table-action-btn"
                        style="background: #10B981; color: #FFFFFF; border: none; padding: 5px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;"
                        title="Verifikasi Pembayaran &amp; Tandai Lunas"
                      >
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Konfirmasi Lunas
                      </button>
                    </form>
                  @endif

                  <!-- Fast Modal Trigger / Detail -->
                  <button
                    type="button"
                    onclick="openPaymentModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ $order->payment_status }}', '{{ $order->tracking_number }}', '{{ addslashes($order->notes ?? '') }}')"
                    class="table-action-btn"
                    style="background: #F3F4F6; color: #374151; padding: 5px 10px; font-size: 11px;"
                    title="Kelola Pembayaran &amp; Resi Kurir"
                  >
                    Resi &amp; Bayar
                  </button>

                  <a href="{{ route('order.show', $order->order_number) }}" target="_blank" class="table-action-btn" style="text-decoration: none;" title="Lihat Faktur / Resi">
                    &nearr;
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 48px 20px; color: #6B7280;">
                Tidak ada data pesanan yang cocok dengan filter atau pencarian Anda.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($orders->hasPages())
      <div style="padding: 16px 24px; border-top: 1px solid #E5E7EB; background: #FAF9F6; display: flex; justify-content: center;">
        {{ $orders->links('partials.pagination') }}
      </div>
    @endif
  </div>
</div>

<!-- Modal Kelola Pembayaran & Resi -->
<div id="paymentModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 480px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;" id="paymentModalTitle">Kelola Pembayaran &amp; Resi</h3>
      <button type="button" onclick="closePaymentModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="paymentModalForm" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Status Pembayaran</label>
        <select name="payment_status" id="modalPaymentStatus" class="chat-search-input" style="width: 100%; font-size: 13px;">
          <option value="unpaid">Belum Dibayar (Unpaid)</option>
          <option value="waiting_verification">Menunggu Verifikasi Bukti</option>
          <option value="paid">Lunas (Paid)</option>
          <option value="refunded">Dikembalikan (Refunded)</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nomor Resi Kurir (Tracking Number)</label>
        <input type="text" name="tracking_number" id="modalTrackingNumber" placeholder="Contoh: JNE-0192830192 / SICEPAT-991823" class="chat-search-input" style="width: 100%; font-size: 13px;">
        <span style="font-size: 11px; color: #6B7280;">Jika resi diisi pada pesanan yang diproses, status pesanan otomatis berubah jadi 'Dikirim'.</span>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Catatan Admin / Verifikasi Mutasi</label>
        <textarea name="notes" id="modalNotes" rows="3" placeholder="Catatan bukti transfer atau instruksi khusus..." class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closePaymentModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openPaymentModal(orderId, orderNumber, paymentStatus, trackingNumber, notes) {
  document.getElementById('paymentModalTitle').textContent = 'Kelola Pembayaran & Resi: ' + orderNumber;
  document.getElementById('paymentModalForm').action = '/admin/orders/' + orderId + '/payment';
  document.getElementById('modalPaymentStatus').value = paymentStatus || 'unpaid';
  document.getElementById('modalTrackingNumber').value = trackingNumber || '';
  document.getElementById('modalNotes').value = notes || '';
  document.getElementById('paymentModalBackdrop').style.display = 'flex';
}

function closePaymentModal() {
  document.getElementById('paymentModalBackdrop').style.display = 'none';
}
</script>
@endsection
