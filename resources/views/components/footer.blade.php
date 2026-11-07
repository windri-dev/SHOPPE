<footer class="site-footer" id="contact">
  <div class="container">
    <!-- Footer Top: Navigation (Left) & Newsletter (Right) -->
    <div class="footer-top">
      <nav class="footer-nav" aria-label="Footer Navigation">
        <a href="{{ route('contact.index') }}">CONTACT</a>
        <a href="{{ route('privacy') }}#terms">TERMS OF SERVICES</a>
        <a href="{{ route('privacy') }}#shipping">SHIPPING AND RETURNS</a>
      </nav>

      <div class="newsletter-box">
        <form class="newsletter-form" id="newsletterForm">
          <input type="email" class="newsletter-input" id="newsletterEmail"
            placeholder="Give an email, get the newsletter." required aria-label="Newsletter email">
          <button type="submit" class="newsletter-submit" aria-label="Subscribe to newsletter">
            <svg width="25" height="15" viewBox="0 0 25 15" fill="none" stroke="currentColor" stroke-width="1.5">
              <line x1="0" y1="7.5" x2="23" y2="7.5"></line>
              <polyline points="16 1 23 7.5 16 14"></polyline>
            </svg>
          </button>
        </form>
      </div>
    </div>

    <!-- Footer Bottom: Copyright (Left) & Social Links (Right) -->
    <div class="footer-bottom">
      <p class="footer-copyright">
        &copy; {{ date('Y') }} SHOPPE. <a href="{{ route('privacy') }}#terms">Terms of use</a> and <a href="{{ route('privacy') }}">privacy policy</a>.
      </p>

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
  </div>
</footer>
