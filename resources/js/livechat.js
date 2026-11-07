/**
 * Shoppe - LiveChat & Personal Stylist Widget Controller
 * Production-ready customer service widget with instant automated concierge,
 * two-way sync with Admin Dashboard Inquiries, WhatsApp integration, and localStorage persistence.
 */

class LiveChatManager {
  constructor() {
    this.storageKey = 'shoppe_livechat_history_v2';
    this.inquiriesKey = 'shoppe_admin_inquiries_v1';
    this.sessionThreadIdKey = 'shoppe_livechat_thread_id';
    this.isSubfolder = window.location.pathname.includes('/pages/');

    const settings = this.loadStoreSettings();
    this.whatsappNumber = String(settings.whatsappNumber || '6281234567890').replace(/\D/g, '');
    const parsedFreeShip = Number(settings.freeShippingMin);
    this.freeShippingMin = Number.isFinite(parsedFreeShip) && parsedFreeShip >= 0 ? parsedFreeShip : 50;

    this.init();
  }

  loadStoreSettings() {
    try {
      return JSON.parse(localStorage.getItem('shoppe_store_settings_v1')) || {};
    } catch (e) {
      return {};
    }
  }

  resolveLink(path) {
    if (path.startsWith('http') || path.startsWith('//')) return path;
    if (this.isSubfolder) {
      if (path === 'index.html') return '../index.html';
      if (path.startsWith('pages/')) return path.replace('pages/', '');
      return path;
    } else {
      return path;
    }
  }

  init() {
    this.ensureMarkup();

    this.wrapper = document.getElementById('shoppeLiveChatWrapper');
    this.triggerBtn = document.getElementById('shoppeLiveChatTrigger');
    this.windowEl = document.getElementById('shoppeLiveChatWindow');
    this.closeBtn = document.getElementById('shoppeLiveChatClose');
    this.messagesContainer = document.getElementById('shoppeLiveChatBody');
    this.form = document.getElementById('shoppeLiveChatForm');
    this.input = document.getElementById('shoppeLiveChatInput');
    this.sendBtn = document.getElementById('shoppeLiveChatSend');
    this.teaser = document.getElementById('shoppeLiveChatTeaser');
    this.teaserClose = document.getElementById('shoppeLiveChatTeaserClose');
    this.resetBtn = document.getElementById('shoppeLiveChatReset');
    this.unreadBadge = document.getElementById('shoppeLiveChatBadge');

    const waHeaderBtn = this.wrapper?.querySelector('.shoppe-livechat-btn-whatsapp');
    if (waHeaderBtn) {
      waHeaderBtn.href = `https://wa.me/${this.whatsappNumber}?text=${encodeURIComponent('Halo SHOPPE Concierge, saya ingin konsultasi produk perhiasan.')}`;
    }

    this.loadHistory();
    this.syncAdminReplies();
    this.attachEvents();
    this.initTeaser();

    // Listen for cross-tab Admin replies via localStorage event
    window.addEventListener('storage', (e) => {
      if (e.key === this.inquiriesKey) {
        this.syncAdminReplies();
      }
    });
  }

  ensureMarkup() {
    if (document.getElementById('shoppeLiveChatWrapper')) return;

    const waUrl = `https://wa.me/${this.whatsappNumber}?text=${encodeURIComponent('Halo SHOPPE Concierge, saya ingin konsultasi produk perhiasan.')}`;

    const markup = `
      <aside class="shoppe-livechat-wrapper" id="shoppeLiveChatWrapper" aria-label="Customer Support Live Chat">
        <div class="shoppe-livechat-teaser" id="shoppeLiveChatTeaser" style="display: none;">
          <span>Ada yang bisa kami bantu seputar perhiasan? ✨</span>
          <button type="button" class="shoppe-livechat-teaser-close" id="shoppeLiveChatTeaserClose" aria-label="Tutup notifikasi">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <button type="button" class="shoppe-livechat-trigger" id="shoppeLiveChatTrigger" aria-expanded="false" aria-label="Buka Live Chat SHOPPE">
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
                <span>SJ</span>
                <span class="shoppe-livechat-avatar-status"></span>
              </div>
              <div class="shoppe-livechat-agent-info">
                <h4>SHOPPE Concierge</h4>
                <span>Aria • Stylist Online</span>
              </div>
            </div>

            <div class="shoppe-livechat-header-actions">
              <a href="${waUrl}" target="_blank" rel="noopener noreferrer" class="shoppe-livechat-btn-header shoppe-livechat-btn-whatsapp" title="Buka di WhatsApp" aria-label="Buka di WhatsApp">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.82 11.82 0 00-3.48-8.413z"/>
                </svg>
              </a>
              <button type="button" class="shoppe-livechat-btn-header" id="shoppeLiveChatClose" aria-label="Tutup jendela chat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>
          </header>

          <div class="shoppe-livechat-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>Konsultasi perhiasan, ketersediaan stok & bantuan pesanan.</span>
          </div>

          <div class="shoppe-livechat-body" id="shoppeLiveChatBody" tabindex="0"></div>

          <footer class="shoppe-livechat-footer">
            <form class="shoppe-livechat-form" id="shoppeLiveChatForm">
              <input 
                type="text" 
                class="shoppe-livechat-input" 
                id="shoppeLiveChatInput" 
                placeholder="Ketik pesan Anda..." 
                autocomplete="off" 
                aria-label="Pesan Live Chat"
              />
              <button type="submit" class="shoppe-livechat-send-btn" id="shoppeLiveChatSend" aria-label="Kirim Pesan" disabled>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="22" y1="2" x2="11" y2="13"></line>
                  <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
              </button>
            </form>
            <div class="shoppe-livechat-micro-info">
              <span>SHOPPE Concierge • 09:00 - 21:00 WIB</span>
              <button type="button" class="shoppe-livechat-reset-link" id="shoppeLiveChatReset" title="Hapus riwayat obrolan">Reset</button>
            </div>
          </footer>
        </div>
      </aside>
    `;

    document.body.insertAdjacentHTML('beforeend', markup);
  }

  attachEvents() {
    this.triggerBtn.addEventListener('click', () => this.toggleChat());
    this.closeBtn.addEventListener('click', () => this.closeChat());

    if (this.teaser) {
      this.teaser.addEventListener('click', (e) => {
        if (!e.target.closest('#shoppeLiveChatTeaserClose')) {
          this.openChat();
        }
      });
    }

    if (this.teaserClose) {
      this.teaserClose.addEventListener('click', (e) => {
        e.stopPropagation();
        this.hideTeaser();
      });
    }

    this.form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleUserSend();
    });

    this.input.addEventListener('input', () => {
      const val = this.input.value.trim();
      this.sendBtn.disabled = val.length === 0;
    });

    document.querySelectorAll('.shoppe-livechat-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        const query = chip.dataset.query;
        if (query) {
          this.sendUserMessage(query);
          this.processQuery(query);
        }
      });
    });

    if (this.resetBtn) {
      this.resetBtn.addEventListener('click', () => {
        if (confirm('Hapus seluruh riwayat percakapan chat?')) {
          this.clearHistory();
        }
      });
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isOpen()) {
        this.closeChat();
      }
    });

    document.addEventListener('click', (e) => {
      const trigger = e.target.closest('[data-action="open-livechat"], .open-livechat-btn');
      if (trigger) {
        e.preventDefault();
        this.openChat();
      }
    });
  }

  initTeaser() {
    const hasSeen = sessionStorage.getItem('shoppe_chat_teaser_dismissed');
    if (!hasSeen && !this.isOpen()) {
      setTimeout(() => {
        if (!this.isOpen() && this.teaser) {
          this.teaser.style.display = 'flex';
        }
      }, 4000);
    }
  }

  hideTeaser() {
    if (this.teaser) {
      this.teaser.style.display = 'none';
      sessionStorage.setItem('shoppe_chat_teaser_dismissed', 'true');
    }
  }

  isOpen() {
    return this.wrapper.classList.contains('is-open');
  }

  openChat() {
    this.syncAdminReplies();
    this.wrapper.classList.add('is-open');
    this.triggerBtn.setAttribute('aria-expanded', 'true');
    this.hideTeaser();
    this.scrollToBottom();
    setTimeout(() => {
      this.input?.focus();
    }, 250);
  }

  closeChat() {
    this.wrapper.classList.remove('is-open');
    this.triggerBtn.setAttribute('aria-expanded', 'false');
  }

  toggleChat() {
    if (this.isOpen()) {
      this.closeChat();
    } else {
      this.openChat();
    }
  }

  getCurrentTime() {
    const now = new Date();
    return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  loadHistory() {
    let saved = null;
    try {
      saved = JSON.parse(localStorage.getItem(this.storageKey) || sessionStorage.getItem(this.storageKey));
    } catch (e) {
      console.warn('Failed to parse chat history:', e);
    }

    if (!saved || saved.length === 0) {
      this.messages = [
        {
          sender: 'agent',
          text: 'Halo! Selamat datang di **SHOPPE** ✨\nSaya Aria, personal stylist Anda. Ada yang bisa kami bantu seputar koleksi perhiasan kami hari ini?',
          time: this.getCurrentTime()
        }
      ];
      this.saveHistory();
    } else {
      this.messages = saved;
    }

    this.renderMessages();
  }

  saveHistory() {
    try {
      localStorage.setItem(this.storageKey, JSON.stringify(this.messages));
    } catch (e) {
      console.error('Error saving chat history:', e);
    }
  }

  clearHistory() {
    localStorage.removeItem(this.storageKey);
    sessionStorage.removeItem(this.storageKey);
    this.loadHistory();
  }

  renderMessages() {
    this.messagesContainer.innerHTML = '';
    this.messages.forEach(msg => {
      this.appendMessageElement(msg, false);
    });
    this.scrollToBottom();
  }

  // Safe rendering: escape first, then allow **bold** and linebreaks
  formatSafeBubbleHtml(rawText) {
    const escaped = this.escapeHtml(String(rawText || '')
      .replace(/<strong>(.*?)<\/strong>/gi, '**$1**')
      .replace(/<br\s*\/?>/gi, '\n'));
    return escaped
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br>');
  }

  appendMessageElement(msg, animate = true) {
    const div = document.createElement('div');
    const safeSender = msg.sender === 'user' ? 'user' : 'agent';
    div.className = `shoppe-livechat-msg ${safeSender}`;
    if (!animate) div.style.animation = 'none';

    let contentHtml = this.formatSafeBubbleHtml(msg.text || '');

    if (msg.actionBtn && msg.actionBtn.url && msg.actionBtn.label) {
      const safeUrl = this.escapeHtml(msg.actionBtn.url);
      const safeLabel = this.escapeHtml(msg.actionBtn.label);
      const safeClass = this.escapeHtml(msg.actionBtn.className || '');
      contentHtml += `
        <a href="${safeUrl}" ${msg.actionBtn.external ? 'target="_blank" rel="noopener noreferrer"' : ''} class="shoppe-livechat-btn-action ${safeClass}">
          ${safeLabel}
        </a>
      `;
    }

    div.innerHTML = `
      <div class="shoppe-livechat-bubble">${contentHtml}</div>
      <span class="shoppe-livechat-time">${this.escapeHtml(msg.time || '')}</span>
    `;

    this.messagesContainer.appendChild(div);
  }

  scrollToBottom() {
    requestAnimationFrame(() => {
      this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    });
  }

  handleUserSend() {
    const val = this.input.value.trim();
    if (!val) return;

    this.input.value = '';
    this.sendBtn.disabled = true;

    const inqId = this.pushToAdminInquiries(val);
    this.sendUserMessage(val, inqId);
    this.processQuery(val);
  }

  // Forward customer chat message into Admin Dashboard Inquiries (localStorage)
  pushToAdminInquiries(text) {
    const inqId = 'inq-chat-' + Date.now();
    try {
      const customerName = localStorage.getItem('shoppe_display_name') || 'Pelanggan LiveChat';
      const customerContact = localStorage.getItem('shoppe_email') || 'LiveChat Web';
      let inquiries = [];
      const saved = localStorage.getItem(this.inquiriesKey);
      if (saved) {
        inquiries = JSON.parse(saved);
      } else {
        inquiries = [
          { id: 'inq-1', name: 'Nadia S.', contact: '08129883192', topic: 'Konsultasi Perhiasan', message: 'Apakah Lira Earrings aman untuk telinga sensitif?', date: 'Hari ini, 14:20', status: 'answered', reply: 'Halo Nadia, semua anting kami hypoallergenic bebas timbal & nikel.' },
          { id: 'inq-2', name: 'Rizky Kurniawan', contact: '08571829101', topic: 'Cek Resi & Pengiriman', message: 'Kapan resi pesanan #8549120499211 dikirimkan?', date: 'Hari ini, 11:05', status: 'waiting', reply: '' },
          { id: 'inq-3', name: 'Clara Wijaya', contact: 'clara@example.com', topic: 'Kado Ulang Tahun', message: 'Bisa minta tolong sertakan kartu ucapan custom?', date: 'Kemarin', status: 'answered', reply: 'Bisa banget Kak Clara! Silakan tuliskan ucapan saat konfirmasi WhatsApp.' }
        ];
      }

      inquiries.unshift({
        id: inqId,
        name: customerName,
        contact: customerContact,
        topic: 'Live Chat Concierge',
        message: text,
        date: 'Baru saja, ' + this.getCurrentTime(),
        status: 'waiting',
        reply: ''
      });

      localStorage.setItem(this.inquiriesKey, JSON.stringify(inquiries));
    } catch (e) {
      console.warn('Could not save livechat inquiry to admin store:', e);
    }
    return inqId;
  }

  // Check if Admin replied to any user message in dashboard.html and inject into chat
  syncAdminReplies() {
    try {
      const saved = localStorage.getItem(this.inquiriesKey);
      if (!saved) return;
      const inquiries = JSON.parse(saved);
      if (!Array.isArray(inquiries)) return;

      let updated = false;
      inquiries.forEach(inq => {
        if (inq.id && inq.reply && inq.status === 'answered') {
          const alreadyRendered = this.messages.some(m => m.adminReplyFor === inq.id && m.rawReply === inq.reply);
          const hasMatchingUserMsg = this.messages.some(m => m.inqId === inq.id);
          if (hasMatchingUserMsg && !alreadyRendered) {
            const replyMsg = {
              sender: 'agent',
              text: `**Balasan Admin Store (${inq.name}):**\n${inq.reply}`,
              time: this.getCurrentTime(),
              adminReplyFor: inq.id,
              rawReply: inq.reply
            };
            this.messages.push(replyMsg);
            this.appendMessageElement(replyMsg, true);
            updated = true;
          }
        }
      });

      if (updated) {
        this.saveHistory();
        this.scrollToBottom();
      }
    } catch (e) {
      // ignore
    }
  }

  sendUserMessage(text, inqId = null) {
    const msg = {
      sender: 'user',
      text: text,
      time: this.getCurrentTime(),
      inqId: inqId
    };

    this.messages.push(msg);
    this.saveHistory();
    this.appendMessageElement(msg);
    this.scrollToBottom();
  }

  showTypingIndicator() {
    if (document.getElementById('shoppeLiveChatTyping')) return;

    const div = document.createElement('div');
    div.id = 'shoppeLiveChatTyping';
    div.className = 'shoppe-livechat-msg agent';
    div.innerHTML = `
      <div class="shoppe-livechat-typing">
        <span></span>
        <span></span>
        <span></span>
      </div>
    `;

    this.messagesContainer.appendChild(div);
    this.scrollToBottom();
  }

  removeTypingIndicator() {
    const typing = document.getElementById('shoppeLiveChatTyping');
    if (typing) {
      typing.remove();
    }
  }

  sendAgentMessage(text, actionBtn = null) {
    this.removeTypingIndicator();

    const msg = {
      sender: 'agent',
      text: text,
      actionBtn: actionBtn,
      time: this.getCurrentTime()
    };

    this.messages.push(msg);
    this.saveHistory();
    this.appendMessageElement(msg);
    this.scrollToBottom();

    if (!this.isOpen()) {
      this.playNotificationSound();
    }
  }

  playNotificationSound() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();

      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.12);

      gain.gain.setValueAtTime(0.08, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);

      osc.connect(gain);
      gain.connect(ctx.destination);

      osc.start();
      osc.stop(ctx.currentTime + 0.35);
    } catch (e) {
      // ignore autoplay block
    }
  }

  processQuery(rawQuery) {
    this.showTypingIndicator();

    const q = rawQuery.toLowerCase().trim();
    const delay = 650 + Math.random() * 400;

    setTimeout(() => {
      let reply = '';
      let actionBtn = null;

      const shopUrl = this.resolveLink('pages/shop.html');
      const accountUrl = this.resolveLink('pages/account.html');
      const waUrl = `https://wa.me/${this.whatsappNumber}?text=${encodeURIComponent('Halo SHOPPE, saya ingin bertanya: ' + rawQuery)}`;

      if (q.includes('pesan') || q.includes('order') || q.includes('resi') || q.includes('lacak') || q.includes('status')) {
        reply = `Untuk melacak status pesanan Anda, Anda dapat membuka halaman **Akun Saya** pada tab Orders, atau kirimkan ID Pesanan Anda ke WhatsApp Admin kami untuk pengecekan kilat.`;
        actionBtn = {
          label: '🔍 Cek Akun & Pesanan',
          url: accountUrl,
          external: false
        };
      }
      else if (q.includes('bahan') || q.includes('emas') || q.includes('karat') || q.includes('material') || q.includes('lapis') || q.includes('alergi') || q.includes('luntur')) {
        reply = `Koleksi SHOPPE dibuat menggunakan **Premium Brass** dengan pelapisan **18K Gold-Dipped** berkualitas tinggi. Produk kami bersifat **hypoallergenic**, 100% bebas timbal & nikel, serta tahan terhadap air untuk pemakaian harian yang nyaman.`;
        actionBtn = {
          label: '✨ Jelajahi Koleksi',
          url: shopUrl,
          external: false
        };
      }
      else if (q.includes('ukuran') || q.includes('size') || q.includes('lingkar') || q.includes('panjang') || q.includes('dimensi')) {
        reply = `Semua kalung kami dilengkapi dengan rantai extender **40cm + 5cm**, sedangkan anting & hair pin dirancang fleksibel dengan bobot super ringan (kurang dari 8 gram per pasang) agar tidak membebani telinga Anda sepanjang hari.`;
        actionBtn = {
          label: '📖 Buka Katalog Toko',
          url: shopUrl,
          external: false
        };
      }
      else if (q.includes('ongkir') || q.includes('kirim') || q.includes('ekspedisi') || q.includes('retur') || q.includes('tukar') || q.includes('garansi') || q.includes('lama')) {
        reply = `Kami menyediakan **Gratis Ongkir** ke seluruh Indonesia untuk pesanan di atas **$${this.freeShippingMin}** dengan garansi tukar produk **14 hari** jika terdapat cacat pabrik atau ketidaksesuaian ukuran. Setiap pesanan dikemas aman dalam velvet jewelry pouch khas Shoppe.`;
        actionBtn = {
          label: '🚚 Lihat Info Pengiriman',
          url: this.resolveLink('pages/privacy-policy.html#shipping'),
          external: false
        };
      }
      else if (q.includes('wa') || q.includes('whatsapp') || q.includes('admin') || q.includes('cs') || q.includes('human') || q.includes('manusia') || q.includes('telepon')) {
        reply = `Pesan Anda telah diteruskan ke Inbox Admin Store kami. Ingin berbicara langsung via WhatsApp? Tim Personal Shopper kami siap melayani konsultasi private.`;
        actionBtn = {
          label: '💬 Hubungi WhatsApp Admin',
          url: waUrl,
          external: true,
          className: 'btn-wa'
        };
      }
      else if (q.includes('rekomendasi') || q.includes('anting') || q.includes('kalung') || q.includes('hair') || q.includes('kado') || q.includes('populer') || q.includes('best seller')) {
        reply = `Tiga produk paling dicintai pelanggan bulan ini:\n1. **Lira Earrings** ($20.00) - Anting minimalis elegan.\n2. **Hal Earrings** ($25.00) - Desain loop modern.\n3. **Plaine Necklace** ($19.00) - Kalung layering esensial.`;
        actionBtn = {
          label: '🛍️ Belanja Best Seller',
          url: shopUrl,
          external: false
        };
      }
      else if (q.includes('halo') || q.includes('hai') || q.includes('hi') || q.includes('hello') || q.includes('pagi') || q.includes('siang') || q.includes('sore') || q.includes('malam') || q.includes('assalam')) {
        reply = `Halo! Senang bisa menyapa Anda. Ceritakan perhiasan seperti apa yang sedang Anda cari untuk melengkapi gaya Anda hari ini. ✨`;
      }
      else {
        reply = `Terima kasih atas pesan Anda! Pesan Anda sudah tercatat di **Dashboard Admin** kami dan tim stylist SHOPPE juga dapat dihubungi langsung melalui WhatsApp resmi.`;
        actionBtn = {
          label: '💬 Lanjut Chat WhatsApp',
          url: waUrl,
          external: true,
          className: 'btn-wa'
        };
      }

      this.sendAgentMessage(reply, actionBtn);
    }, delay);
  }

  escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }
}

if (typeof window !== 'undefined') {
  window.LiveChatManager = LiveChatManager;
  const initChat = () => {
    if (!window.ShoppeLiveChat) {
      window.ShoppeLiveChat = new LiveChatManager();
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChat);
  } else {
    initChat();
  }
}
