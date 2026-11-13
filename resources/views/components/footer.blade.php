<footer class="site-footer" id="contact">
  <div class="container">
    <!-- Brand Header -->
    <div class="footer-brand-block">
      <a href="{{ route('home') }}" class="footer-brand-link" aria-label="PUTRIJALVA">
        <img src="{{ asset('images/logo.png') }}" alt="PUTRIJALVA" class="footer-brand-logo" width="160" height="42" loading="lazy">
      </a>
      <p class="footer-brand-tagline">{{ __('Spesialis Baju Crop Top & Atasan Wanita Kontemporer') }}</p>
    </div>

    <!-- Main Footer Content: Columns (Left) & Newsletter (Right) -->
    <div class="footer-top">
      <div class="footer-nav-grid">
        <div class="footer-nav-col">
          <h4 class="footer-col-title">{{ __('Belanja & Bantuan') }}</h4>
          <ul class="footer-links">
            <li><a href="{{ route('shop.index') }}">{{ __('Koleksi Crop Top') }}</a></li>
            <li><a href="{{ route('contact.index') }}">{{ __('Kontak Kami') }}</a></li>
            <li><a href="{{ route('faq') }}">{{ __('Tanya Jawab (FAQ)') }}</a></li>
          </ul>
        </div>
        <div class="footer-nav-col">
          <h4 class="footer-col-title">{{ __('Kebijakan & Layanan') }}</h4>
          <ul class="footer-links">
            <li><a href="{{ route('privacy') }}#shipping">{{ __('Pengiriman & Retur') }}</a></li>
            <li><a href="{{ route('privacy') }}#terms">{{ __('Ketentuan Layanan') }}</a></li>
            <li><a href="{{ route('privacy') }}">{{ __('Kebijakan Privasi') }}</a></li>
          </ul>
        </div>
      </div>

      <div class="newsletter-box">
        <div class="newsletter-header">
          <h4 class="newsletter-title">{{ __('Warta Eksklusif') }}</h4>
          <p class="newsletter-subtitle">{{ __('Dapatkan kabar koleksi terbaru, kurasi atasan crop top, dan penawaran eksklusif langsung ke email Anda.') }}</p>
        </div>
        <form class="newsletter-form" id="newsletterForm">
          <input type="email" class="newsletter-input" id="newsletterEmail"
            placeholder="{{ __('Masukkan email Anda...') }}" required aria-label="{{ __('Alamat Email') }}">
          <button type="submit" class="newsletter-submit" aria-label="{{ __('Langganan') }}">
            <span>{{ __('Langganan') }}</span>
            <svg class="newsletter-submit-icon" width="20" height="13" viewBox="0 0 25 15" fill="none" stroke="currentColor" stroke-width="1.8">
              <line x1="0" y1="7.5" x2="23" y2="7.5"></line>
              <polyline points="16 1 23 7.5 16 14"></polyline>
            </svg>
          </button>
        </form>
      </div>
    </div>

    <!-- Footer Bottom: Social Links & Copyright -->
    <div class="footer-bottom">
      <div class="footer-social-inline">
        <span class="social-label">{{ __('Ikuti Kami:') }}</span>
        <div class="social-links" aria-label="Social media links">
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </a>
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
        </div>
      </div>

      <p class="footer-copyright">
        &copy; {{ date('Y') }} PUTRIJALVA. <a href="{{ route('privacy') }}#terms">{{ __('Terms of use') }}</a> &amp; <a href="{{ route('privacy') }}">{{ __('privacy policy') }}</a>. {{ __('Hak Cipta Dilindungi.') }}
      </p>
    </div>
  </div>
</footer>
