@extends('layouts.storefront')

@section('title', 'FAQ & Pusat Bantuan Pelanggan - PUTRI.J')
@section('meta_description', 'Pertanyaan yang sering diajukan (FAQ) seputar cara pemesanan, konfirmasi pembayaran, buku alamat pengiriman, pelacakan resi, garansi tukar ukuran, dan perawatan busana PUTRI.J.')

@section('content')
<div class="container" style="padding-top: 48px; padding-bottom: 80px; max-width: 860px;">
  <!-- Header Title -->
  <div style="text-align: center; margin-bottom: 38px;">
    <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin: 0 0 10px;">
      Frequently Asked Questions (FAQ)
    </h1>
    <p style="font-size: 15px; color: #6B7280; max-width: 600px; margin: 0 auto;">
      Temukan jawaban instan seputar pemesanan, verifikasi pembayaran, manajemen lokasi pengiriman, hingga panduan ukuran &amp; perawatan busana.
    </p>
  </div>

  <!-- FAQ Accordion List -->
  <div id="faqListContainer" style="display: flex; flex-direction: column; gap: 12px;">
    @foreach($faqs as $idx => $faq)
      <div
        class="faq-item-card"
        data-category="{{ $faq['category'] }}"
        data-search="{{ strtolower($faq['question'] . ' ' . $faq['answer'] . ' ' . $faq['category_label']) }}"
        style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; overflow: hidden; transition: border-color 0.2s;"
      >
        <button
          type="button"
          class="faq-toggle-btn"
          aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}"
          style="width: 100%; padding: 18px 22px; background: none; border: none; display: flex; justify-content: space-between; align-items: center; gap: 16px; text-align: left; cursor: pointer;"
        >
          <div>
            <span style="display: inline-block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #A18A68; margin-bottom: 4px;">
              {{ $faq['category_label'] }}
            </span>
            <div style="font-size: 15px; font-weight: 600; color: #111827; line-height: 1.4;">
              {{ $faq['question'] }}
            </div>
          </div>
          <span class="faq-chevron" style="width: 28px; height: 28px; border-radius: 9999px; background: #FAF9F6; border: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #374151; transition: transform 0.2s; transform: {{ $idx === 0 ? 'rotate(180deg)' : 'rotate(0deg)' }};">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>

        <div class="faq-answer-panel" style="display: {{ $idx === 0 ? 'block' : 'none' }}; padding: 0 22px 18px; border-top: 1px solid #F3F4F6;">
          <p style="margin: 14px 0 0; font-size: 14px; color: #4B5563; line-height: 1.7;">
            {{ $faq['answer'] }}
          </p>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Still Need Help CTA Box -->
  <div style="margin-top: 44px; background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 12px; padding: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 6px;">Masih memiliki pertanyaan khusus?</h2>
      <p style="font-size: 13.5px; color: #6B7280; margin: 0;">
        Tim Jewellery Concierge kami siap melayani konsultasi ukuran, pelacakan pesanan, hingga kado spesial setiap hari.
      </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
      <button type="button" class="btn btn-primary open-livechat-btn" style="font-size: 13px; padding: 11px 20px; display: inline-flex; align-items: center; gap: 8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        Mulai Live Chat Sekarang
      </button>
      <a href="{{ route('contact.index') }}" class="btn btn-outline" style="font-size: 13px; padding: 11px 20px;">
        Kirim Tiket Kontak
      </a>
    </div>
  </div>
</div>

<!-- Structured Data FAQPage JSON-LD for SEO & AEO -->
@php
  $faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($item) => [
      '@type' => 'Question',
      'name' => $item['question'],
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => $item['answer'],
      ],
    ], $faqs),
  ];
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleBtns = document.querySelectorAll('.faq-toggle-btn');

    // Accordion Toggle
    toggleBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const card = this.closest('.faq-item-card');
        const panel = card.querySelector('.faq-answer-panel');
        const chevron = card.querySelector('.faq-chevron');
        const isOpen = this.getAttribute('aria-expanded') === 'true';

        this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        panel.style.display = isOpen ? 'none' : 'block';
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
      });
    });
  });
</script>
@endpush
@endsection

