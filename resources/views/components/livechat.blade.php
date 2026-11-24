<aside
  class="shoppe-livechat-wrapper"
  id="shoppeLiveChatWrapper"
  aria-label="Customer Support Live Chat"
  data-endpoint="/livechat/message"
  data-thread-endpoint="/livechat/thread"
  data-user-name="{{ auth()->user()->name ?? '' }}"
  data-user-email="{{ auth()->user()->email ?? '' }}"
  data-user-phone="{{ auth()->user()->phone ?? '' }}"
>
  <div class="shoppe-livechat-teaser" id="shoppeLiveChatTeaser" style="display: none;">
    <span class="shoppe-livechat-teaser-text" style="display: inline-flex; align-items: center; gap: 6px;">
      Butuh rekomendasi busana? Chat kami di sini
      <svg width="13" height="13" viewBox="0 0 24 24" fill="#A18A68" aria-hidden="true"><path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/></svg>
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
          id="shoppeLiveChatReset"
          class="shoppe-livechat-btn-header"
          title="{{ __('Mulai Percakapan Baru') }}"
          aria-label="{{ __('Mulai Percakapan Baru') }}"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <polyline points="1 4 1 10 7 10"></polyline>
            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
          </svg>
        </button>

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
