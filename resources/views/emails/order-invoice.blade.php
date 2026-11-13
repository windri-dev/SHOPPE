<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Pesanan #{{ $order->order_number }} - PUTRIJALVA</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #F8F6F2;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #1F2937;
      line-height: 1.6;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    .wrapper {
      width: 100%;
      background-color: #F8F6F2;
      padding: 40px 16px;
    }
    .email-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #FFFFFF;
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid #E5E7EB;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    .email-header {
      background-color: #111827;
      padding: 32px 24px;
      text-align: center;
      color: #FFFFFF;
    }
    .brand-logo {
      font-size: 26px;
      font-weight: 800;
      letter-spacing: 0.15em;
      margin: 0;
      color: #FFFFFF;
    }
    .brand-sub {
      font-size: 10px;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: #D1D5DB;
      margin-top: 4px;
    }
    .email-body {
      padding: 32px 28px;
    }
    .greeting {
      font-size: 18px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 8px;
    }
    .order-badge-card {
      background: #FAF9F6;
      border: 1px solid #E5E7EB;
      border-radius: 8px;
      padding: 16px 20px;
      margin: 20px 0;
    }
    .order-meta-table td {
      padding: 4px 0;
      font-size: 13px;
    }
    .order-items-table {
      margin: 24px 0;
      border-top: 1px solid #E5E7EB;
      border-bottom: 1px solid #E5E7EB;
    }
    .order-items-table th {
      text-align: left;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #6B7280;
      padding: 12px 0 8px;
    }
    .order-items-table td {
      padding: 12px 0;
      border-top: 1px solid #F3F4F6;
      font-size: 13.5px;
      vertical-align: middle;
    }
    .size-pill {
      display: inline-block;
      padding: 2px 7px;
      font-size: 11px;
      font-weight: 600;
      background: #F3F4F6;
      color: #374151;
      border-radius: 4px;
      margin-top: 4px;
    }
    .total-summary-table td {
      padding: 6px 0;
      font-size: 13.5px;
    }
    .grand-total-row td {
      padding-top: 12px;
      border-top: 2px solid #111827;
      font-size: 17px;
      font-weight: 800;
      color: #111827;
    }
    .bank-card {
      background: #FAF9F6;
      border: 1px dashed #A18A68;
      border-radius: 8px;
      padding: 18px 20px;
      margin: 24px 0;
    }
    .cta-btn {
      display: inline-block;
      background-color: #111827;
      color: #FFFFFF !important;
      text-decoration: none;
      padding: 14px 28px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin: 16px 0 8px;
    }
    .email-footer {
      background-color: #FAF9F6;
      border-top: 1px solid #E5E7EB;
      padding: 24px;
      text-align: center;
      font-size: 11.5px;
      color: #6B7280;
      line-height: 1.7;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="email-container">
      <!-- Header -->
      <div class="email-header">
        <h1 class="brand-logo">PUTRIJALVA</h1>
        <div class="brand-sub">Contemporary Ready-to-Wear Atelier</div>
      </div>

      <!-- Body -->
      <div class="email-body">
        <div class="greeting">Halo, {{ $order->customer_name }}</div>
        <p style="margin: 0 0 16px; color: #4B5563; font-size: 14px;">
          Terima kasih telah berbelanja di <strong>PUTRIJALVA</strong>. Pesanan busana Anda telah kami terima dengan detail rincian berikut:
        </p>

        <!-- Order Meta Card -->
        <div class="order-badge-card">
          <table class="order-meta-table">
            <tr>
              <td style="color: #6B7280; width: 40%;">Nomor Pesanan</td>
              <td style="font-weight: 700; color: #111827;">#{{ $order->order_number }}</td>
            </tr>
            <tr>
              <td style="color: #6B7280;">Waktu Pemesanan</td>
              <td style="color: #111827;">{{ $order->created_at?->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
              <td style="color: #6B7280;">Metode Pembayaran</td>
              <td style="font-weight: 600; color: #111827;">
                {{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}
              </td>
            </tr>
            <tr>
              <td style="color: #6B7280;">Status Pembayaran</td>
              <td>
                <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #FEF3C7; color: #92400E;">
                  {{ strtoupper($order->payment_status) }}
                </span>
              </td>
            </tr>
          </table>
        </div>

        <!-- Items Table -->
        <table class="order-items-table">
          <thead>
            <tr>
              <th>Busana Koleksi</th>
              <th style="text-align: center; width: 60px;">Jml</th>
              <th style="text-align: right; width: 110px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
              <tr>
                <td>
                  <strong style="color: #111827;">{{ $item->product_name }}</strong><br>
                  <span class="size-pill">Ukuran: {{ $item->size ?? 'M' }}</span>
                </td>
                <td style="text-align: center; color: #4B5563;">{{ $item->quantity }}x</td>
                <td style="text-align: right; font-weight: 600; color: #111827;">
                  {{ \App\Services\CurrencyService::formatPrice($item->line_total ?? ($item->unit_price * $item->quantity)) }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <!-- Total Calculation -->
        <table class="total-summary-table">
          <tr>
            <td style="color: #6B7280;">Subtotal Busana</td>
            <td style="text-align: right; color: #111827;">{{ \App\Services\CurrencyService::formatPrice($order->subtotal) }}</td>
          </tr>
          <tr>
            <td style="color: #6B7280;">Biaya Pengiriman</td>
            <td style="text-align: right; color: #111827;">
              @if($order->shipping_cost > 0)
                {{ \App\Services\CurrencyService::formatPrice($order->shipping_cost) }}
              @else
                <span style="color: #059669; font-weight: 600;">GRATIS</span>
              @endif
            </td>
          </tr>
          @if($order->discount_amount > 0)
            <tr>
              <td style="color: #059669;">Diskon Eksklusif</td>
              <td style="text-align: right; color: #059669; font-weight: 600;">
                - {{ \App\Services\CurrencyService::formatPrice($order->discount_amount) }}
              </td>
            </tr>
          @endif
          <tr class="grand-total-row">
            <td>TOTAL PEMBAYARAN</td>
            <td style="text-align: right; color: #A18A68;">
              {{ $order->formatted_grand_total }}
            </td>
          </tr>
        </table>

        <!-- Shipping Address -->
        <div style="margin-top: 28px; padding-top: 18px; border-top: 1px solid #E5E7EB;">
          <h4 style="margin: 0 0 6px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #6B7280;">Alamat Pengiriman</h4>
          <p style="margin: 0; font-size: 13.5px; color: #1F2937;">
            <strong>{{ $order->customer_name }}</strong> ({{ $order->customer_phone }})<br>
            {{ $order->shipping_address['street_address'] ?? '-' }}<br>
            {{ $order->shipping_address['city'] ?? '-' }}, {{ $order->shipping_address['province'] ?? '-' }} {{ $order->shipping_address['postcode'] ?? '' }}
          </p>
        </div>

        <!-- Bank Transfer Info if applicable -->
        @if($order->payment_method === 'bank_transfer')
          <div class="bank-card">
            <h4 style="margin: 0 0 8px; font-size: 13.5px; color: #A18A68; text-transform: uppercase; letter-spacing: 0.05em;">Instruksi Pembayaran Transfer Bank:</h4>
            <p style="margin: 0 0 8px; font-size: 13px; color: #4B5563;">
              Silakan transfer tepat sebesar <strong>{{ $order->formatted_grand_total }}</strong> ke salah satu rekening resmi butik kami:
            </p>
            <ul style="margin: 0; padding-left: 18px; font-size: 13px; color: #111827;">
              <li><strong>BCA:</strong> 8830-1289-00 a.n. PT PUTRIJALVA INDONESIA</li>
              <li><strong>Mandiri:</strong> 137-00-9821-332 a.n. PT PUTRIJALVA INDONESIA</li>
              <li><strong>BNI:</strong> 081-992-114 a.n. PT PUTRIJALVA INDONESIA</li>
            </ul>
          </div>
        @endif

        <!-- Call to Action -->
        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ route('order.show', $order->order_number) }}" class="cta-btn">
            Lihat Pesanan & Unggah Bukti Bayar &rarr;
          </a>
        </div>
      </div>

      <!-- Footer -->
      <div class="email-footer">
        <strong>BUTIK PUTRIJALVA INDONESIA</strong><br>
        Senopati Suites Tower 2, Kebayoran Baru, Jakarta Selatan 12190<br>
        Layanan Pelanggan: concierge@putrijalva.id • WhatsApp: +62 812-3456-7890<br>
        <span style="font-size: 10px; color: #9CA3AF; margin-top: 8px; display: inline-block;">
          Email ini dikirim secara otomatis oleh sistem butik daring PUTRIJALVA. Mohon simpan email ini sebagai bukti transaksi resmi.
        </span>
      </div>
    </div>
  </div>
</body>
</html>
