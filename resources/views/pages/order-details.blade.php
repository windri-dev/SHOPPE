@extends('layouts.storefront')

@section('title', 'Order ' . $order->order_number . ' - SHOPPE')

@section('content')
<div class="container page-container" style="max-width: 800px;">
  <div class="order-receipt-card" style="border: 1px solid #E0E0E0; padding: 40px; border-radius: 4px; background: #fff;">
    <div style="text-align: center; margin-bottom: 30px;">
      <span style="display: inline-block; padding: 6px 14px; background: #E8F5E9; color: #2E7D32; font-size: 13px; font-weight: 600; border-radius: 9999px;">
        Pesanan Diterima
      </span>
      <h1 style="font-size: 28px; margin: 15px 0 5px;">Terima Kasih Atas Pesanan Anda</h1>
      <p style="color: #666;">Nomor Pesanan: <strong>{{ $order->order_number }}</strong></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 20px 0; margin-bottom: 30px; font-size: 14px;">
      <div>
        <strong>Penerima:</strong><br>
        {{ $order->customer_name }}<br>
        {{ $order->customer_email }}<br>
        {{ $order->customer_phone }}
      </div>
      <div>
        <strong>Alamat Pengiriman:</strong><br>
        {{ $order->shipping_address['street'] ?? '-' }}<br>
        {{ $order->shipping_address['city'] ?? '-' }}, {{ $order->shipping_address['postcode'] ?? '' }}<br>
        {{ $order->shipping_address['country'] ?? 'Indonesia' }}
      </div>
    </div>

    <h2 style="font-size: 18px; margin-bottom: 15px;">Rincian Barang</h2>
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
      <thead>
        <tr style="border-bottom: 1px solid #ddd; text-align: left;">
          <th style="padding: 10px 0;">Produk</th>
          <th style="padding: 10px 0; text-align: center;">Qty</th>
          <th style="padding: 10px 0; text-align: right;">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
          <tr style="border-bottom: 1px solid #f0f0f0;">
            <td style="padding: 12px 0;">{{ $item->product_name }}</td>
            <td style="padding: 12px 0; text-align: center;">{{ $item->quantity }}</td>
            <td style="padding: 12px 0; text-align: right;">$ {{ number_format($item->line_total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" style="padding-top: 15px; text-align: right;"><strong>Grand Total:</strong></td>
          <td style="padding-top: 15px; text-align: right; font-size: 18px; font-weight: bold; color: #A18A68;">
            {{ $order->formatted_grand_total }}
          </td>
        </tr>
      </tfoot>
    </table>

    <div style="text-align: center; margin-top: 40px;">
      <a href="{{ route('shop.index') }}" class="btn btn-outline">Lanjut Belanja &rarr;</a>
    </div>
  </div>
</div>
@endsection
