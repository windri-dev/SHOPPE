/**
 * Shoppe - Hybrid AI Stylist & Human Admin LiveChat Controller
 * Menggabungkan pengecekan katalog barang/harga/stok real-time via backend AI Concierge
 * dengan eskalasi ke Staf Admin Manusia (tidak sepenuhnya AI yang handle).
 */

class LiveChatManager {
  constructor() {
    this.storageKey = 'shoppe_livechat_history_v3';
    this.ticketStorageKey = 'shoppe_livechat_ticket_v3';
    this.modeStorageKey = 'shoppe_livechat_mode_v3';

    const settings = this.loadStoreSettings();
    this.whatsappNumber = String(settings.whatsappNumber || 'https://www.whatsapp.com/').replace(/\D/g, '');
    this.ticketNumber = localStorage.getItem(this.ticketStorageKey) || null;
    this.handlingMode = localStorage.getItem(this.modeStorageKey) || 'ai';
    this.pollTimer = null;

    this.init();
  }

  loadStoreSettings() {
    try {
      return JSON.parse(localStorage.getItem('shoppe_store_settings_v1')) || {};
    } catch (e) {
      return {};
    }
  }

  init() {
    this.wrapper = document.getElementById('shoppeLiveChatWrapper');
    if (!this.wrapper) return;

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
    this.humanBtn = document.getElementById('shoppeLiveChatHumanBtn');
    this.humanBtnText = document.getElementById('shoppeLiveChatHumanBtnText');
    this.modeLabel = document.getElementById('shoppeLiveChatModeLabel');
    this.avatarText = document.getElementById('shoppeLiveChatAvatarText');
    this.noticeText = document.getElementById('shoppeLiveChatNoticeText');
    this.ticketInfo = document.getElementById('shoppeLiveChatTicketInfo');

    const waHeaderBtn = this.wrapper.querySelector('.shoppe-livechat-btn-whatsapp');
    if (waHeaderBtn) {
      waHeaderBtn.href = `https://wa.me/${this.whatsappNumber}?text=${encodeURIComponent('Halo PUTRI.J Concierge, saya ingin konsultasi produk perhiasan.')}`;
    }

    this.loadHistory();
    this.updateModeUI(this.handlingMode);
    this.attachEvents();
    this.initTeaser();

    if (this.ticketNumber) {
      this.syncThreadFromServer();
    }
  }

  attachEvents() {
    this.triggerBtn?.addEventListener('click', () => this.toggleChat());
    this.closeBtn?.addEventListener('click', () => this.closeChat());

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

    this.form?.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleUserSend();
    });

    this.input?.addEventListener('input', () => {
      const val = this.input.value.trim();
      this.sendBtn.disabled = val.length === 0;
    });

    this.humanBtn?.addEventListener('click', () => {
      if (this.handlingMode === 'human') {
        this.syncThreadFromServer();
        return;
      }
      this.submitMessage('Saya ingin berbicara langsung dengan Staf Admin Manusia (CS).', true);
    });

    this.resetBtn?.addEventListener('click', () => {
      this.clearHistory();
    });

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

  updateModeUI(mode) {
    this.handlingMode = mode === 'human' ? 'human' : 'ai';
    localStorage.setItem(this.modeStorageKey, this.handlingMode);

    if (this.handlingMode === 'human') {
      if (this.avatarText) this.avatarText.textContent = 'CS';
      if (this.modeLabel) this.modeLabel.textContent = 'Admin CS • Mode Manual';
      if (this.humanBtnText) this.humanBtnText.textContent = 'Admin Aktif';
      this.humanBtn?.classList.add('is-human');
      if (this.noticeText) {
        this.noticeText.innerHTML = 'Ditangani langsung oleh <strong>Staf Admin Manusia</strong>. AI otomatis nonaktif.';
      }
    } else {
      if (this.avatarText) this.avatarText.textContent = 'AI';
      if (this.modeLabel) this.modeLabel.textContent = 'Aria AI • Hybrid CS';
      if (this.humanBtnText) this.humanBtnText.textContent = 'Minta Admin';
      this.humanBtn?.classList.remove('is-human');
      if (this.noticeText) {
        this.noticeText.innerHTML = 'Cek stok &amp; harga real-time. Klik <strong>Minta Admin</strong> kapan saja untuk bicara dengan staf manusia.';
      }
    }

    if (this.ticketInfo) {
      this.ticketInfo.textContent = 'PUTRI.J Concierge • 09:00 - 21:00 WIB';
    }
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
    this.wrapper.classList.add('is-open');
    this.triggerBtn?.setAttribute('aria-expanded', 'true');
    this.hideTeaser();
    this.scrollToBottom();
    if (this.ticketNumber) {
      this.syncThreadFromServer();
    }
    this.startPolling();
    setTimeout(() => {
      this.input?.focus();
    }, 250);
  }

  closeChat() {
    this.wrapper.classList.remove('is-open');
    this.triggerBtn?.setAttribute('aria-expanded', 'false');
    this.stopPolling();
  }

  toggleChat() {
    if (this.isOpen()) {
      this.closeChat();
    } else {
      this.openChat();
    }
  }

  startPolling() {
    this.stopPolling();
    this.pollTimer = setInterval(() => {
      if (this.isOpen() && this.ticketNumber) {
        this.syncThreadFromServer();
      }
    }, 8000);
  }

  stopPolling() {
    if (this.pollTimer) {
      clearInterval(this.pollTimer);
      this.pollTimer = null;
    }
  }

  getCurrentTime() {
    const now = new Date();
    return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  loadHistory() {
    let saved = null;
    try {
      saved = JSON.parse(localStorage.getItem(this.storageKey) || 'null');
    } catch (e) {
      saved = null;
    }

    if (!saved || saved.length === 0) {
      this.messages = [
        {
          sender: 'agent',
          senderBadge: 'Aria AI Stylist',
          text: 'Halo! Selamat datang di **PUTRI.J Concierge**.\nSaya **Aria (AI Stylist)** yang terhubung langsung ke database stok & harga katalog kami. Anda juga dapat menekan tombol **Minta Admin** kapan saja jika ingin dilayani langsung oleh staf manusia.',
          time: this.getCurrentTime()
        }
      ];
      this.saveHistory();
    } else {
      // Bersihkan pesan otomatis lama yang menampilkan nomor tiket & perbarui brand ke PUTRI.J
      this.messages = saved
        .filter(
          (m) => !(m.senderBadge === 'Mode Admin Manusia' || String(m.text || '').includes('Pesan Anda telah diterima di ruang obrolan'))
        )
        .map((m) => ({
          ...m,
          text: String(m.text || '').replace(/SHOPPE/g, 'PUTRI.J'),
          senderBadge: m.senderBadge ? String(m.senderBadge).replace(/SHOPPE/g, 'PUTRI.J') : m.senderBadge
        }));
      this.saveHistory();
    }

    this.renderMessages();
  }

  saveHistory() {
    try {
      localStorage.setItem(this.storageKey, JSON.stringify(this.messages));
    } catch (e) {
      // ignore storage quota errors
    }
  }

  clearHistory() {
    localStorage.removeItem(this.storageKey);
    localStorage.removeItem(this.ticketStorageKey);
    localStorage.removeItem(this.modeStorageKey);
    this.ticketNumber = null;
    this.updateModeUI('ai');
    this.loadHistory();
  }

  renderMessages() {
    this.messagesContainer.innerHTML = '';
    this.messages.forEach((msg) => {
      this.appendMessageElement(msg, false);
    });
    this.scrollToBottom();
  }

  formatSafeBubbleHtml(rawText) {
    const escaped = this.escapeHtml(
      String(rawText || '')
        .trim()
        .replace(/<strong>(.*?)<\/strong>/gi, '**$1**')
        .replace(/<br\s*\/?>/gi, '\n')
    );
    return escaped
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.*?)\*/g, '<em>$1</em>')
      .replace(/\n{2,}/g, '<div style="height: 6px;"></div>')
      .replace(/\n/g, '<br>');
  }

  appendMessageElement(msg, animate = true) {
    const div = document.createElement('div');
    const safeSender = msg.sender === 'user' ? 'user' : 'agent';
    div.className = `shoppe-livechat-msg ${safeSender}`;
    if (!animate) div.style.animation = 'none';

    let badgeHtml = '';
    if (safeSender === 'agent' && msg.senderBadge) {
      const isHuman = msg.senderBadge.toLowerCase().includes('admin');
      badgeHtml = `<div class="shoppe-livechat-sender-badge ${isHuman ? 'is-human' : ''}">${this.escapeHtml(msg.senderBadge)}</div>`;
    }

    let contentHtml = `${badgeHtml}<div>${this.formatSafeBubbleHtml(msg.text || '')}</div>`;

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
      if (this.messagesContainer) {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
      }
    });
  }

  handleUserSend() {
    const val = this.input.value.trim();
    if (!val) return;

    this.input.value = '';
    this.sendBtn.disabled = true;

    this.submitMessage(val, false);
  }

  async submitMessage(text, requestHuman = false) {
    this.sendUserMessage(text);
    if (this.handlingMode !== 'human' || requestHuman) {
      this.showTypingIndicator();
    }

    const customerName = this.wrapper?.dataset.userName || localStorage.getItem('shoppe_display_name') || 'Tamu LiveChat';
    const customerEmail = this.wrapper?.dataset.userEmail || localStorage.getItem('shoppe_email') || 'guest@shoppe.com';
    const endpoint = this.wrapper?.dataset.endpoint || '/livechat/message';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          name: customerName,
          email: customerEmail,
          message: text,
          ticket_number: this.ticketNumber,
          request_human: requestHuman
        })
      });

      const payload = await response.json();
      this.removeTypingIndicator();

      if (payload && payload.success && payload.data) {
        const data = payload.data;
        if (data.ticket_number) {
          this.ticketNumber = data.ticket_number;
          localStorage.setItem(this.ticketStorageKey, this.ticketNumber);
        }

        if (data.handling_mode) {
          this.updateModeUI(data.handling_mode);
        }

        if (data.ai_reply) {
          const badge = data.handled_by === 'handoff'
            ? 'Sistem Eskalasi Admin'
            : (data.handled_by === 'ai_boundary' ? 'Batasan AI (Guardrail)' : 'Aria AI Stylist');

          this.sendAgentMessage(data.ai_reply, data.action_btn || null, badge);
        }
        return;
      }
    } catch (e) {
      // Fallback if offline
    }

    this.removeTypingIndicator();
  }

  async syncThreadFromServer() {
    if (!this.ticketNumber) return;
    const baseThreadUrl = this.wrapper?.dataset.threadEndpoint || '/livechat/thread';

    try {
      const res = await fetch(`${baseThreadUrl}/${encodeURIComponent(this.ticketNumber)}`, {
        headers: { Accept: 'application/json' }
      });
      if (!res.ok) return;
      const payload = await res.json();
      if (!payload?.success || !payload?.data) return;

      const { handling_mode, messages } = payload.data;
      if (handling_mode) {
        this.updateModeUI(handling_mode);
      }

      if (Array.isArray(messages)) {
        let hasNewAdminMsg = false;
        messages.forEach((srvMsg) => {
          if (srvMsg.sender_type === 'admin') {
            const exists = this.messages.some((m) => m.dbId === srvMsg.id);
            if (!exists) {
              const adminMsg = {
                dbId: srvMsg.id,
                sender: 'agent',
                senderBadge: 'Staf Admin Manusia',
                text: srvMsg.message,
                time: srvMsg.time || this.getCurrentTime()
              };
              this.messages.push(adminMsg);
              this.appendMessageElement(adminMsg, true);
              hasNewAdminMsg = true;
            }
          }
        });

        if (hasNewAdminMsg) {
          this.saveHistory();
          this.scrollToBottom();
        }
      }
    } catch (e) {
      // ignore network error
    }
  }

  sendUserMessage(text) {
    const msg = {
      sender: 'user',
      text: text,
      time: this.getCurrentTime()
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

  sendAgentMessage(text, actionBtn = null, senderBadge = 'Aria AI Stylist') {
    this.removeTypingIndicator();

    const msg = {
      sender: 'agent',
      senderBadge: senderBadge,
      text: text,
      actionBtn: actionBtn,
      time: this.getCurrentTime()
    };

    this.messages.push(msg);
    this.saveHistory();
    this.appendMessageElement(msg);
    this.scrollToBottom();
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
