<aside
  class="shoppe-livechat-wrapper"
  id="shoppeLiveChatWrapper"
  aria-label="Customer Support Live Chat"
  data-endpoint="{{ route('livechat.send') }}"
  data-thread-endpoint="{{ url('/livechat/thread') }}"
  data-user-name="{{ auth()->user()->name ?? '' }}"
  data-user-email="{{ auth()->user()->email ?? '' }}"
  data-user-phone="{{ auth()->user()->phone ?? '' }}"
>
  <div class="shoppe-livechat-teaser" id="shoppeLiveChatTeaser" style="display: none;">
    <span class="shoppe-livechat-teaser-text">
      Butuh rekomendasi busana? Chat kami di sini ✨
    </span>
    <button type="button" class="shoppe-livechat-teaser-close" id="shoppeLiveChatTeaserClose" aria-label="Tutup notifikasi">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>
  </div>

  <button type="button" class="shoppe-livechat-trigger" id="shoppeLiveChatTrigger" aria-expanded="false" aria-label="Buka Live Chat PUTRIJALVA">
    <svg class="shoppe-livechat-icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
    </svg>
    <svg class="shoppe-livechat-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="18" y1="6" x2="6" y2="18"></line>
      <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
    <span class="shoppe-livechat-badge" id="shoppeLiveChatBadge" title="Concierge Online"></span>
  </button>

  <div class="shoppe-livechat-window" id="shoppeLiveChatWindow" role="region" aria-label="Jendela Live Chat">
    <header class="shoppe-livechat-header">
      <div class="shoppe-livechat-agent">
        <div class="shoppe-livechat-avatar-wrap">
          <span id="shoppeLiveChatAvatarText">PJ</span>
          <span class="shoppe-livechat-avatar-status"></span>
        </div>
        <div class="shoppe-livechat-agent-info">
          <h4>PUTRIJALVA Concierge</h4>
          <span id="shoppeLiveChatModeLabel">{{ __('Online') }} &bull; {{ app()->getLocale() === 'en' ? 'Ready to Assist' : 'Siap Membantu' }}</span>
        </div>
      </div>

      <div class="shoppe-livechat-header-actions">
        <button
          type="button"
          id="shoppeLiveChatHumanBtn"
          class="shoppe-livechat-btn-mode"
          title="{{ __('Hubungi CS') }}"
          aria-label="{{ __('Hubungi CS') }}"
        >
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span id="shoppeLiveChatHumanBtnText">{{ __('Hubungi CS') }}</span>
        </button>

        <a
          href="https://wa.me/6281234567890?text=Halo%20PUTRIJALVA,%20saya%20ingin%20konsultasi%20produk%20busana"
          target="_blank"
          rel="noopener noreferrer"
          class="shoppe-livechat-btn-header shoppe-livechat-btn-whatsapp"
          title="Chat via WhatsApp"
          aria-label="Buka di WhatsApp"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.82 11.82 0 00-3.48-8.413z"/>
          </svg>
        </a>

        <button type="button" class="shoppe-livechat-btn-header" id="shoppeLiveChatClose" aria-label="Tutup jendela chat">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </header>

    <div class="shoppe-livechat-notice" id="shoppeLiveChatNoticeBar">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <span id="shoppeLiveChatNoticeText">Ada pertanyaan seputar busana atau pesanan? Tim kami siap melayani Anda.</span>
    </div>

    <div class="shoppe-livechat-body" id="shoppeLiveChatBody" tabindex="0"></div>

    <footer class="shoppe-livechat-footer">
      <form class="shoppe-livechat-form" id="shoppeLiveChatForm">
        <input
          type="text"
          class="shoppe-livechat-input"
          id="shoppeLiveChatInput"
          placeholder="{{ __('Ketik pertanyaan Anda...') }}"
          autocomplete="off"
          aria-label="{{ __('Ketik pertanyaan Anda...') }}"
        />
        <button type="submit" class="shoppe-livechat-send-btn" id="shoppeLiveChatSend" aria-label="Kirim Pesan" disabled>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="22" y1="2" x2="11" y2="13"></line>
            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
          </svg>
        </button>
      </form>
    </footer>
  </div>
</aside>
