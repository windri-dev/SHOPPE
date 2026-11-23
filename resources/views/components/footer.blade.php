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
          @php
            $ig = \App\Models\StoreSetting::get('social_instagram', 'https://instagram.com/putrijalva.official');
            $tt = \App\Models\StoreSetting::get('social_tiktok', 'https://tiktok.com/@putrijalva');
            $wa = \App\Models\StoreSetting::get('social_whatsapp', '6281234567890');
            $fb = \App\Models\StoreSetting::get('social_facebook', 'https://facebook.com/putrijalva');
          @endphp
          @if($ig)
            <a href="{{ $ig }}" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
              </svg>
            </a>
          @endif
          @if($tt)
            <a href="{{ $tt }}" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="TikTok">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.82 4.47 6.27 6.27 0 0 0 1.87-4.47V8.67a8.21 8.21 0 0 0 4.9 1.6v-3.58z"/>
              </svg>
            </a>
          @endif
          @if($wa)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (string)$wa) }}" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="WhatsApp">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
              </svg>
            </a>
          @endif
          @if($fb)
            <a href="{{ $fb }}" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Facebook">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
              </svg>
            </a>
          @endif
        </div>
      </div>

      <p class="footer-copyright">
        &copy; {{ date('Y') }} PUTRIJALVA. <a href="{{ route('privacy') }}#terms">{{ __('Terms of use') }}</a> &amp; <a href="{{ route('privacy') }}">{{ __('privacy policy') }}</a>. {{ __('Hak Cipta Dilindungi.') }}
      </p>
    </div>
  </div>
</footer>
