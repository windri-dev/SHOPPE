<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesanan #{{ $order->order_number }} Telah Dikirim - PUTRIJALVA</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #F8F6F2;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #1F2937;
      line-height: 1.6;
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
    .email-body {
      padding: 32px 28px;
    }
    .tracking-card {
      background: #FAF9F6;
      border: 1px solid #A18A68;
      border-radius: 8px;
      padding: 20px;
      margin: 24px 0;
      text-align: center;
    }
    .tracking-number {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: 0.1em;
      color: #111827;
      margin: 8px 0;
      font-family: monospace;
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
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="email-container">
      <div class="email-header">
        <h1 class="brand-logo">PUTRIJALVA</h1>
        <div style="font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase; color: #D1D5DB; margin-top: 4px;">Contemporary Ready-to-Wear Atelier</div>
      </div>
      <div class="email-body">
        <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 8px;">Koleksi Anda Sedang Menuju ke Alamat Anda!</h2>
        <p style="color: #4B5563; font-size: 14px; margin: 0 0 16px;">
          Halo <strong>{{ $order->customer_name }}</strong>, busana pilihan Anda dari pesanan <strong>#{{ $order->order_number }}</strong> telah selesai dikemas rapi dalam garment bag eksklusif PUTRIJALVA dan telah diserahkan kepada kurir pengiriman.
        </p>

        <div class="tracking-card">
          <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #8C8273; font-weight: 700;">Nomor Resi Pelacakan:</div>
          <div class="tracking-number">{{ $order->tracking_number ?? 'RESI-DALAM-PROSES' }}</div>
          <div style="font-size: 12.5px; color: #6B7280;">Kurir: Pengiriman Resmi Butik PUTRIJALVA</div>
        </div>

        <div style="text-align: center; margin-top: 24px;">
          <a href="{{ route('order.show', $order->order_number) }}" class="cta-btn">
            Lacak Status Pengiriman &rarr;
          </a>
        </div>
      </div>
      <div class="email-footer">
        <strong>BUTIK PUTRIJALVA INDONESIA</strong><br>
        Senopati Suites Tower 2, Kebayoran Baru, Jakarta Selatan 12190<br>
        Layanan Pelanggan: concierge@putrijalva.id • WhatsApp: +62 812-3456-7890
      </div>
    </div>
  </div>
</body>
</html>
