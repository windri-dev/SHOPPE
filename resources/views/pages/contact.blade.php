@extends('layouts.storefront')

@section('title', __('Contact Us') . ' - PUTRIJALVA')

@section('content')
<div class="container contact-page">
  <div class="contact-header">
    <h1 class="contact-title">{{ __('Contact Us') }}</h1>
    <p class="contact-subtitle">
      {{ __('Silakan hubungi tim Concierge kami untuk pertanyaan seputar koleksi busana, pesanan, atau kolaborasi khusus.') }}
    </p>
  </div>

  <!-- Dynamic Boutique Contact Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 32px;">
    @php
      $cEmail = \App\Models\StoreSetting::get('store_email', 'concierge@wn-cdn.net');
      $cPhone = \App\Models\StoreSetting::get('store_phone', '+62 812-3456-7890');
      $cWa = \App\Models\StoreSetting::get('social_whatsapp', '6281234567890');
      $cAddress = \App\Models\StoreSetting::get('store_address', 'PUTRIJALVA Atelier Studio, Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan');
    @endphp
    <!-- Email Card -->
    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
      <div style="width: 40px; height: 40px; border-radius: 50%; background: #FAF9F6; border: 1px solid #D5CBBB; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #A18A68;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
      </div>
      <h3 style="font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 4px;">Email Resmi</h3>
      <a href="mailto:{{ $cEmail }}" style="font-size: 13px; color: #A18A68; text-decoration: none; font-weight: 600;">{{ $cEmail }}</a>
    </div>

    <!-- WhatsApp Card -->
    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
      <div style="width: 40px; height: 40px; border-radius: 50%; background: #F0FDF4; border: 1px solid #BBF7D0; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #25D366;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
      </div>
      <h3 style="font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 4px;">WhatsApp Concierge</h3>
      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (string)$cWa) }}" target="_blank" rel="noopener noreferrer" style="font-size: 13px; color: #047857; text-decoration: none; font-weight: 600;">+{{ preg_replace('/[^0-9]/', '', (string)$cWa) }}</a>
    </div>

    <!-- Atelier Card -->
    <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
      <div style="width: 40px; height: 40px; border-radius: 50%; background: #FAF9F6; border: 1px solid #D5CBBB; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #A18A68;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
      </div>
      <h3 style="font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 4px;">Atelier Studio</h3>
      <p style="font-size: 12px; color: #4B5563; margin: 0; line-height: 1.4;">{{ $cAddress }}</p>
    </div>
  </div>

  <div class="contact-form-container">
    @if(session('success'))
      <div style="background: #E8F5E9; border: 1px solid #C8E6C9; color: #2E7D32; padding: 14px 18px; border-radius: 4px; margin-bottom: 32px; font-size: 14px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if($errors->any())
      <div style="background: #FFEBEE; border: 1px solid #FFCDD2; color: #C62828; padding: 14px 18px; border-radius: 4px; margin-bottom: 32px; font-size: 14px; text-align: center;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
      @csrf
      <div class="contact-form-row">
        <div class="contact-input-wrap">
          <input type="text" name="name" required value="{{ old('name') }}" placeholder="{{ __('Nama Lengkap *') }}" class="contact-input">
        </div>
        <div class="contact-input-wrap">
          <input type="email" name="email" required value="{{ old('email') }}" placeholder="{{ __('Email *') }}" class="contact-input">
        </div>
      </div>

      <div class="contact-form-row">
        <div class="contact-input-wrap">
          <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Nomor Telepon / WhatsApp') }}" class="contact-input">
        </div>
        <div class="contact-input-wrap">
          <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('Subjek *') }}" class="contact-input" required>
        </div>
      </div>

      <div class="contact-input-wrap">
        <textarea name="message" rows="5" required placeholder="{{ __('Tuliskan pesan Anda di sini... *') }}" class="contact-input contact-textarea">{{ old('message') }}</textarea>
      </div>

      <button type="submit" class="contact-submit-btn">
        {{ __('KIRIM PESAN') }}
      </button>
    </form>

    <div style="margin-top: 32px; padding: 18px 22px; background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div style="font-size: 13.5px; color: #374151;">
        <strong>{{ __('Butuh jawaban cepat?') }}</strong> {{ __('Lihat panduan pemesanan, pembayaran, dan pelacakan resi di halaman FAQ kami.') }}
      </div>
      <a href="{{ route('faq') }}" class="btn btn-outline" style="font-size: 12px; padding: 8px 16px;">
        {!! __('Buka Halaman FAQ &rarr;') !!}
      </a>
    </div>
  </div>
</div>
@endsection
