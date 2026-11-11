@extends('layouts.storefront')

@section('title', 'Contact Us - PUTRI.J')

@section('content')
<div class="container contact-page">
  <div class="contact-header">
    <h1 class="contact-title">Contact Us</h1>
    <p class="contact-subtitle">
      Silakan hubungi tim Concierge kami untuk pertanyaan seputar koleksi busana, pesanan, atau kolaborasi khusus.
    </p>
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
          <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nama Lengkap *" class="contact-input">
        </div>
        <div class="contact-input-wrap">
          <input type="email" name="email" required value="{{ old('email') }}" placeholder="Email *" class="contact-input">
        </div>
      </div>

      <div class="contact-form-row">
        <div class="contact-input-wrap">
          <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Nomor Telepon / WhatsApp" class="contact-input">
        </div>
        <div class="contact-input-wrap">
          <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Subjek *" class="contact-input" required>
        </div>
      </div>

      <div class="contact-input-wrap">
        <textarea name="message" rows="5" required placeholder="Tuliskan pesan Anda di sini... *" class="contact-input contact-textarea">{{ old('message') }}</textarea>
      </div>

      <button type="submit" class="contact-submit-btn">
        KIRIM PESAN
      </button>
    </form>

    <div style="margin-top: 32px; padding: 18px 22px; background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div style="font-size: 13.5px; color: #374151;">
        <strong>Butuh jawaban cepat?</strong> Lihat panduan pemesanan, pembayaran, dan pelacakan resi di halaman FAQ kami.
      </div>
      <a href="{{ route('faq') }}" class="btn btn-outline" style="font-size: 12px; padding: 8px 16px;">
        Buka Halaman FAQ &rarr;
      </a>
    </div>
  </div>
</div>
@endsection
