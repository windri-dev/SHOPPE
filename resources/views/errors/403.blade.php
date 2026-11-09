@extends('layouts.storefront')

@section('title', '403 Akses Ditolak - PUTRI.J')

@section('content')
<div class="container" style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 60px 16px;">
  <div style="max-width: 520px; text-align: center; background: #FFFFFF; border: 1px solid #EFECE6; border-radius: 16px; padding: 48px 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
    <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: #FAF5ED; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #A18A68;">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
    </div>

    <h1 style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 8px;">Akses Ditolak (403)</h1>
    <p style="font-size: 14px; color: #6B7280; line-height: 1.6; margin-bottom: 20px;">
      {{ $exception->getMessage() ?: 'Halaman pengelola toko ini dilindungi oleh pembatasan IP Whitelist. Alamat IP Anda tidak memiliki izin akses.' }}
    </p>

    <div style="display: inline-block; background: #FAF9F6; border: 1px dashed #DED6CA; border-radius: 8px; padding: 8px 16px; font-size: 13px; color: #707070; margin-bottom: 28px;">
      Alamat IP Anda: <strong style="color: #111827;">{{ request()->ip() }}</strong>
    </div>

    <div>
      <a href="{{ route('home') }}" class="btn btn-primary" style="padding: 12px 28px; font-size: 13px; letter-spacing: 0.05em;">
        Kembali ke Beranda
      </a>
    </div>
  </div>
</div>
@endsection

