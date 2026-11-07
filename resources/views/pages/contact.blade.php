@extends('layouts.storefront')

@section('title', 'Contact Us - SHOPPE')

@section('content')
<div class="container contact-page">
  <div class="contact-header">
    <h1 class="contact-title">Contact Us</h1>
    <p class="contact-subtitle">
      Silakan hubungi tim Concierge kami untuk pertanyaan seputar koleksi perhiasan, pesanan, atau kolaborasi khusus.
    </p>
  </div>

  <div class="contact-form-container">
    @if(session('success'))
      <div style="background: #E8F5E9; border: 1px solid #C8E6C9; color: #2E7D32; padding: 14px 18px; border-radius: 4px; margin-bottom: 32px; font-size: 14px; text-align: center;">
        ✓ {{ session('success') }}
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
  </div>
</div>
@endsection
