@extends('layouts.admin')

@section('title', 'Live Chat & AI Concierge Hybrid - PUTRI.J Admin')
@section('page_title', 'Live Chat & AI Concierge Hybrid')

@section('content')
<div class="admin-livechat-page" style="display: flex; flex-direction: column; gap: 16px;">
  @if(session('success'))
    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;">
      {{ session('success') }}
    </div>
  @endif

  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        Pusat Interaksi Live Chat &amp; AI Concierge Hybrid
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Kolaborasi <strong>Aria AI Stylist</strong> (cek barang, harga &amp; stok otomatis) dengan <strong>Staf Admin Manusia</strong> (ambil alih manual &amp; komplain).
      </p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
      <button
        type="button"
        id="openKnowledgeModalBtn"
        style="display: inline-flex; align-items: center; gap: 7px; background: #111827; color: #FFFFFF; border: none; border-radius: 8px; padding: 9px 15px; font-size: 12px; font-weight: 600; cursor: pointer;"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
        Knowledge Base &amp; Batasan AI ({{ $knowledgeEntries->count() }})
      </button>
      <span class="chat-count-badge" id="chatUnreadCountBadge" style="padding: 6px 14px; font-size: 12px;">
        {{ $unreadCount }} Pesan Belum Dibaca
      </span>
      <a href="{{ route('admin.dashboard') }}" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151;">
        &larr; Dashboard
      </a>
    </div>
  </div>

  <!-- 2-Panel Messenger Console -->
  <div class="admin-livechat-console" id="adminLiveChatConsole" style="height: calc(100vh - 220px); min-height: 640px;">
    <!-- 1. LEFT SIDEBAR: Conversations List -->
    <aside class="chat-sidebar">
      <div class="chat-sidebar-header">
        <div class="chat-sidebar-title-row">
          <h3 class="chat-sidebar-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Inbox Obrolan
          </h3>
          <span style="font-size: 12px; color: #6B7280; font-weight: 600;" id="chatTotalCountText">
            {{ $inquiries->count() }} Tiket
          </span>
        </div>

        <div class="chat-search-wrap">
          <svg class="chat-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input type="text" id="chatSearchInput" class="chat-search-input" placeholder="Cari nama, email, tiket...">
        </div>

        <div class="chat-filter-pills" id="chatFilterPills">
          <button type="button" class="chat-filter-pill active" data-filter="all">Semua ({{ $inquiries->count() }})</button>
          <button type="button" class="chat-filter-pill" data-filter="human">Mode Admin ({{ $inquiries->where('handling_mode', 'human')->count() }})</button>
          <button type="button" class="chat-filter-pill" data-filter="unread">Belum Dibaca ({{ $inquiries->where('status.value', 'unread')->count() }})</button>
          <button type="button" class="chat-filter-pill" data-filter="resolved">Selesai</button>
        </div>
      </div>

      <div class="chat-conversations-list" id="chatConversationsList">
        @forelse($inquiries as $idx => $inq)
          @php
            $initials = strtoupper(substr($inq->name, 0, 2));
            $lastMsg = $inq->messages->last();
            $msgTimeObj = $lastMsg ? $lastMsg->created_at : $inq->created_at;
            $lastTime = $msgTimeObj ? ($msgTimeObj instanceof \Carbon\Carbon ? $msgTimeObj->format('H:i') : \Carbon\Carbon::parse($msgTimeObj)->format('H:i')) : now()->format('H:i');
            $isActive = $idx === 0;
            $modeVal = $inq->handling_mode ?? 'ai';
          @endphp
          <div
            class="chat-conversation-item {{ $isActive ? 'active' : '' }}"
            data-inquiry-id="{{ $inq->id }}"
            data-ticket="{{ $inq->ticket_number }}"
            data-name="{{ $inq->name }}"
            data-email="{{ $inq->email }}"
            data-phone="{{ $inq->phone ?? '' }}"
            data-channel="{{ $inq->channel }}"
            data-status="{{ $inq->status->value }}"
            data-mode="{{ $modeVal }}"
            data-subject="{{ $inq->subject }}"
            data-reply-url="{{ route('admin.inquiries.reply', $inq) }}"
            data-status-url="{{ route('admin.inquiries.status', $inq) }}"
            data-mode-url="{{ route('admin.inquiries.mode', $inq) }}"
          >
            <div class="chat-item-avatar-wrap">
              <div class="chat-item-avatar">{{ $initials }}</div>
              <span class="chat-channel-badge {{ $inq->channel === 'contact_form' ? 'form' : '' }}" title="{{ $inq->channel === 'contact_form' ? 'Formulir Kontak' : 'Livechat Toko' }}">
                @if($inq->channel === 'contact_form')
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                @else
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                @endif
              </span>
            </div>
            <div class="chat-item-content">
              <div class="chat-item-top">
                <span class="chat-item-name">{{ $inq->name }}</span>
                <span class="chat-item-time">{{ $lastTime }}</span>
              </div>
              <div class="chat-item-subject" style="display: flex; align-items: center; gap: 6px;">
                <span>{{ $inq->subject }}</span>
                <span
                  class="chat-item-mode-pill"
                  style="font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 99px; text-transform: uppercase; {{ $modeVal === 'human' ? 'background: #FEF3C7; color: #92400E;' : 'background: #F3E8FF; color: #6B21A8;' }}"
                >
                  {{ $modeVal === 'human' ? 'Admin Manual' : 'AI Hybrid' }}
                </span>
              </div>
              <div class="chat-item-preview-row">
                <span class="chat-item-snippet">
                  {{ $lastMsg ? $lastMsg->message : 'Belum ada pesan' }}
                </span>
                @if($inq->status->value === 'unread')
                  <span class="chat-unread-dot" title="Belum Dibaca"></span>
                @endif
              </div>
            </div>
          </div>
        @empty
          <div class="chat-empty-sidebar">Belum ada percakapan pelanggan.</div>
        @endforelse
      </div>
    </aside>

    <!-- 2. RIGHT PANEL: Active Chat Room -->
    <main class="chat-main-panel" id="chatMainPanel">
      @if($inquiries->isNotEmpty())
        @php
          $firstInq = $inquiries->first();
          $firstInitials = strtoupper(substr($firstInq->name, 0, 2));
          $firstMode = $firstInq->handling_mode ?? 'ai';
        @endphp
        <!-- Header -->
        <header class="chat-main-header">
          <div class="chat-header-user">
            <div class="chat-header-avatar" id="activeChatAvatar">{{ $firstInitials }}</div>
            <div class="chat-header-details">
              <h3 id="activeChatName">{{ $firstInq->name }}</h3>
              <div class="chat-header-meta">
                <span id="activeChatEmail">{{ $firstInq->email }}</span>
                <span>&bull;</span>
                <span id="activeChatPhone">{{ $firstInq->phone ?: 'Tanpa No. HP' }}</span>
                <span>&bull;</span>
                <span class="chat-header-channel-tag {{ $firstInq->channel === 'contact_form' ? 'form' : '' }}" id="activeChatChannel">
                  {{ $firstInq->channel === 'contact_form' ? 'Formulir Kontak' : 'Live Chat Toko' }}
                </span>
                <span>&bull;</span>
                <strong id="activeChatTicket" style="color: #374151;">{{ $firstInq->ticket_number }}</strong>
              </div>
            </div>
          </div>

          <div class="chat-header-actions" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <select
              id="activeChatModeSelect"
              class="chat-status-select"
              title="Pilih Mode Penanganan (AI vs Admin Manusia)"
              style="background: #F9FAFB; font-weight: 600; border-color: #D1D5DB;"
            >
              <option value="ai" {{ $firstMode === 'ai' ? 'selected' : '' }}>Mode: AI Stylist Aria (Otomatis)</option>
              <option value="human" {{ $firstMode === 'human' ? 'selected' : '' }}>Mode: Admin Manusia (Ambil Alih)</option>
            </select>

            <select id="activeChatStatusSelect" class="chat-status-select" title="Status Tiket">
              <option value="unread" {{ $firstInq->status->value === 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
              <option value="responded" {{ $firstInq->status->value === 'responded' ? 'selected' : '' }}>Sudah Dibalas</option>
              <option value="resolved" {{ $firstInq->status->value === 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
            </select>

            <a
              href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (string)$firstInq->phone) }}"
              target="_blank"
              class="btn-chat-wa"
              id="activeChatWaLink"
              title="Buka Chat WhatsApp"
              style="{{ $firstInq->phone ? '' : 'display: none;' }}"
            >
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.82 11.82 0 00-3.48-8.413z"/></svg>
              WhatsApp
            </a>
          </div>
        </header>

        <!-- Message Stream Body -->
        <div class="chat-messages-container" id="chatMessagesContainer">
          <div class="chat-subject-banner" id="activeChatSubjectBanner" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
              <span>Topik: <strong id="activeChatSubjectText">{{ $firstInq->subject }}</strong></span>
            </div>
            <span id="activeChatModeBannerBadge" style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 99px; {{ $firstMode === 'human' ? 'background: #FEF3C7; color: #92400E;' : 'background: #F3E8FF; color: #6B21A8;' }}">
              {{ $firstMode === 'human' ? 'Ditangani Admin Manusia (AI Nonaktif)' : 'Ditangani AI Stylist Aria (Otomatis)' }}
            </span>
          </div>

          <div class="chat-date-separator">Riwayat Percakapan Pelanggan, AI &amp; Admin</div>

          <div id="chatMessagesStream" style="display: flex; flex-direction: column; gap: 14px;">
            <!-- Rendered by JavaScript -->
          </div>
        </div>

        <!-- Quick Response Suggestion Chips + AI Smart Draft Button -->
        <div class="chat-quick-templates" style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
          <button
            type="button"
            id="btnGenerateAiDraft"
            style="display: inline-flex; align-items: center; gap: 5px; background: #A18A68; color: #FFFFFF; border: none; border-radius: 999px; padding: 5px 12px; font-size: 11px; font-weight: 700; cursor: pointer;"
            title="Biarkan AI membuat draft balasan berdasarkan stok katalog & knowledge base"
          >
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            Buat Draft AI
          </button>
          <button type="button" class="chat-template-chip" data-template="Halo! Saya staf Admin Concierge PUTRI.J yang mengambil alih percakapan Anda. Ada yang bisa saya bantu tindak lanjuti secara langsung?">
            + Ambil Alih Admin
          </button>
          <button type="button" class="chat-template-chip" data-template="Produk ini saat ini ready stock di gudang utama kami dan siap dikirim hari ini.">
            + Info Stok Ready
          </button>
          <button type="button" class="chat-template-chip" data-template="Kami menyediakan garansi tukar barang/size dalam 14 hari setelah pesanan diterima dengan menyertakan video unboxing.">
            + Garansi 14 Hari
          </button>
          <button type="button" class="chat-template-chip" data-template="Seluruh perhiasan PUTRI.J dibuat dari bahan Premium Brass 18K Gold-Dipped hypoallergenic bebas nikel & timbal.">
            + Info Hypoallergenic
          </button>
        </div>

        <!-- Chat Input Bar -->
        <footer class="chat-input-bar-wrap">
          <form id="adminChatSendForm" class="chat-input-form">
            <input
              type="text"
              id="adminChatInputText"
              class="chat-input-box"
              placeholder="Ketik balasan sebagai Admin Manusia... (Otomatis mengalihkan tiket ke Mode Admin Manusia)"
              autocomplete="off"
            >
            <button type="submit" id="adminChatSendBtn" class="chat-send-btn" title="Kirim Balasan Admin">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
          </form>
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 6px; font-size: 11px;">
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; color: #111827; font-weight: 600;">
              <input type="checkbox" id="adminSaveAsKnowledgeCheckbox" style="accent-color: #A18A68; width: 14px; height: 14px; cursor: pointer;">
              <span>💡 Simpan balasan ini ke <strong>Knowledge Base</strong> (agar pertanyaan serupa dijawab otomatis oleh AI)</span>
            </label>
            <span style="color: #6B7280;">Tiket otomatis beralih ke <strong>Mode Admin Manusia</strong> saat dikirim.</span>
          </div>
        </footer>
      @else
        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9CA3AF;">
          Tidak ada percakapan pelanggan yang aktif.
        </div>
      @endif
    </main>
  </div>

  <!-- 3. MODAL: AI KNOWLEDGE BASE, GUARDRAIL BOUNDARIES & RESPONSE RULES -->
  <div
    id="aiKnowledgeModal"
    style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(17, 24, 39, 0.6); align-items: center; justify-content: center; padding: 20px;"
  >
    <div style="background: #FFFFFF; width: 100%; max-width: 980px; max-height: 90vh; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
      <div style="padding: 18px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; background: #F9FAFB;">
        <div>
          <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">
            Manajemen Knowledge Base, Batasan (Guardrails) &amp; Aturan Respons AI
          </h3>
          <p style="margin: 3px 0 0 0; font-size: 12px; color: #6B7280;">
            Atur pengetahuan produk/kebijakan toko, batasan ketat yang tidak boleh dilanggar AI, serta kondisi kapan AI wajib mengalihkan chat ke Admin Manusia.
          </p>
        </div>
        <button type="button" id="closeKnowledgeModalBtn" style="background: transparent; border: none; font-size: 20px; color: #6B7280; cursor: pointer;">&times;</button>
      </div>

      <div style="padding: 20px 24px; overflow-y: auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px;">
        <!-- Form Tambah / Edit Knowledge & Batasan AI -->
        <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; padding: 16px;">
          <h4 id="knowledgeFormTitle" style="margin: 0 0 12px 0; font-size: 14px; font-weight: 700; color: #111827;">
            + Tambah Knowledge / Batasan Baru
          </h4>
          <form id="aiKnowledgeForm" method="POST" action="{{ route('admin.ai_knowledge.store') }}" style="display: flex; flex-direction: column; gap: 10px;">
            @csrf
            <input type="hidden" name="_method" id="knowledgeFormMethod" value="POST">

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Judul Aturan / Topik *</label>
              <input type="text" name="title" id="knTitle" required placeholder="Contoh: Batasan Komplain & Refund" style="width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px;">
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Kategori Aturan AI *</label>
              <select name="type" id="knType" required style="width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px; background: #FFF;">
                <option value="knowledge">Knowledge Base (Pengetahuan Toko &amp; Produk)</option>
                <option value="boundary">Batasan AI / Guardrail (Larangan &amp; Eskalasi)</option>
                <option value="response_rule">Aturan Respons Kostumer (Sapaan &amp; Panduan)</option>
              </select>
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Kata Kunci Pemicu (Pisahkan Koma)</label>
              <input type="text" name="keywords" id="knKeywords" placeholder="komplain, rusak, refund, uang kembali" style="width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px;">
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Isi Respons / Instruksi kepada Pelanggan *</label>
              <textarea name="content" id="knContent" rows="4" required placeholder="Tulis jawaban resmi atau pesan pembatas yang akan disampaikan AI..." style="width: 100%; padding: 8px 10px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 12px;"></textarea>
            </div>

            <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #111827; cursor: pointer;">
              <input type="checkbox" name="requires_human_handoff" id="knHandoff" value="1">
              <span>Wajib alihkan tiket ke <strong>Admin Manusia</strong> saat terpicu</span>
            </label>

            <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #111827; cursor: pointer;">
              <input type="checkbox" name="is_active" id="knActive" value="1" checked>
              <span>Aturan Aktif</span>
            </label>

            <div style="display: flex; gap: 8px; margin-top: 6px;">
              <button type="submit" style="flex: 1; background: #111827; color: #FFF; border: none; padding: 9px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                Simpan Aturan AI
              </button>
              <button type="button" id="resetKnowledgeFormBtn" style="display: none; background: #E5E7EB; color: #374151; border: none; padding: 9px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                Batal Edit
              </button>
            </div>
          </form>
        </div>

        <!-- Daftar Knowledge Base, Batasan, dan Aturan Respons -->
        <div style="display: flex; flex-direction: column; gap: 10px; max-height: 65vh; overflow-y: auto;">
          <div style="position: sticky; top: 0; background: #FFF; z-index: 2; padding-bottom: 4px;">
            <input
              type="text"
              id="knSearchInput"
              placeholder="Cari judul, topik, atau kata kunci knowledge..."
              style="width: 100%; padding: 8px 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 12px; box-sizing: border-box;"
            >
          </div>
          <div id="knowledgeEntriesList" style="display: flex; flex-direction: column; gap: 10px;">
          @forelse($knowledgeEntries as $entry)
            @php
              $badgeStyle = match($entry->type) {
                'boundary' => 'background: #FEE2E2; color: #991B1B;',
                'response_rule' => 'background: #DBEAFE; color: #1E40AF;',
                default => 'background: #ECFDF5; color: #065F46;',
              };
              $badgeLabel = match($entry->type) {
                'boundary' => 'Batasan AI (Guardrail)',
                'response_rule' => 'Aturan Respons Kostumer',
                default => 'Knowledge Base Toko',
              };
            @endphp
            <div class="knowledge-entry-card" data-title="{{ strtolower($entry->title) }}" data-keywords="{{ strtolower((string)$entry->keywords) }}" data-content="{{ strtolower($entry->content) }}" style="border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px; background: #FFFFFF; display: flex; flex-direction: column; gap: 6px;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                  <strong style="font-size: 13px; color: #111827;">{{ $entry->title }}</strong>
                  <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 99px; {{ $badgeStyle }}">
                    {{ $badgeLabel }}
                  </span>
                  @if($entry->requires_human_handoff)
                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 99px; background: #FEF3C7; color: #92400E;">
                      Auto-Handoff ke Admin Manusia
                    </span>
                  @endif
                  @if(! $entry->is_active)
                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 99px; background: #F3F4F6; color: #6B7280;">
                      Nonaktif
                    </span>
                  @endif
                </div>
                <div style="display: flex; gap: 6px;">
                  <button
                    type="button"
                    class="btn-edit-knowledge"
                    data-id="{{ $entry->id }}"
                    data-title="{{ $entry->title }}"
                    data-type="{{ $entry->type }}"
                    data-keywords="{{ $entry->keywords }}"
                    data-content="{{ $entry->content }}"
                    data-handoff="{{ $entry->requires_human_handoff ? '1' : '0' }}"
                    data-active="{{ $entry->is_active ? '1' : '0' }}"
                    data-update-url="{{ route('admin.ai_knowledge.update', $entry) }}"
                    style="font-size: 11px; padding: 4px 8px; border: 1px solid #D1D5DB; background: #FFF; border-radius: 5px; cursor: pointer;"
                  >
                    Edit
                  </button>
                  <form method="POST" action="{{ route('admin.ai_knowledge.destroy', $entry) }}" onsubmit="return confirm('Hapus aturan AI ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="font-size: 11px; padding: 4px 8px; border: 1px solid #FCA5A5; background: #FEF2F2; color: #B91C1C; border-radius: 5px; cursor: pointer;">
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
              @if($entry->keywords)
                <div style="font-size: 11px; color: #6B7280;">
                  <strong>Kata Kunci:</strong> <code>{{ $entry->keywords }}</code>
                </div>
              @endif
              <div style="font-size: 12px; color: #374151; line-height: 1.5; background: #F9FAFB; padding: 8px 10px; border-radius: 6px;">
                {{ $entry->content }}
              </div>
            </div>
          @empty
            <div style="padding: 24px; text-align: center; color: #9CA3AF; font-size: 13px;">
              Belum ada aturan Knowledge Base atau Batasan AI.
            </div>
          @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
  const inquiriesData = @json($inquiries->load('messages'));
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  let currentInquiryId = inquiriesData.length > 0 ? inquiriesData[0].id : null;
  let activeFilter = 'all';

  const conversationsList = document.getElementById('chatConversationsList');
  const messagesStream = document.getElementById('chatMessagesStream');
  const messagesContainer = document.getElementById('chatMessagesContainer');
  const chatSearchInput = document.getElementById('chatSearchInput');
  const filterPills = document.querySelectorAll('.chat-filter-pill');
  const sendForm = document.getElementById('adminChatSendForm');
  const chatInput = document.getElementById('adminChatInputText');
  const sendBtn = document.getElementById('adminChatSendBtn');
  const statusSelect = document.getElementById('activeChatStatusSelect');
  const modeSelect = document.getElementById('activeChatModeSelect');
  const modeBannerBadge = document.getElementById('activeChatModeBannerBadge');
  const btnGenerateAiDraft = document.getElementById('btnGenerateAiDraft');

  // Header Elements
  const headerAvatar = document.getElementById('activeChatAvatar');
  const headerName = document.getElementById('activeChatName');
  const headerEmail = document.getElementById('activeChatEmail');
  const headerPhone = document.getElementById('activeChatPhone');
  const headerChannel = document.getElementById('activeChatChannel');
  const headerTicket = document.getElementById('activeChatTicket');
  const headerSubject = document.getElementById('activeChatSubjectText');
  const headerWa = document.getElementById('activeChatWaLink');

  // Knowledge Modal Elements
  const knowledgeModal = document.getElementById('aiKnowledgeModal');
  const openKnowledgeBtn = document.getElementById('openKnowledgeModalBtn');
  const closeKnowledgeBtn = document.getElementById('closeKnowledgeModalBtn');
  const knowledgeForm = document.getElementById('aiKnowledgeForm');
  const knowledgeFormTitle = document.getElementById('knowledgeFormTitle');
  const knowledgeFormMethod = document.getElementById('knowledgeFormMethod');
  const resetKnowledgeFormBtn = document.getElementById('resetKnowledgeFormBtn');
  const defaultStoreAction = "{{ route('admin.ai_knowledge.store') }}";

  if (openKnowledgeBtn && knowledgeModal) {
    openKnowledgeBtn.addEventListener('click', () => {
      knowledgeModal.style.display = 'flex';
    });
  }
  if (closeKnowledgeBtn && knowledgeModal) {
    closeKnowledgeBtn.addEventListener('click', () => {
      knowledgeModal.style.display = 'none';
    });
  }

  document.querySelectorAll('.btn-edit-knowledge').forEach((btn) => {
    btn.addEventListener('click', () => {
      if (!knowledgeForm) return;
      knowledgeForm.action = btn.dataset.updateUrl;
      knowledgeFormMethod.value = 'PUT';
      knowledgeFormTitle.textContent = 'Edit Knowledge / Batasan AI';
      document.getElementById('knTitle').value = btn.dataset.title || '';
      document.getElementById('knType').value = btn.dataset.type || 'knowledge';
      document.getElementById('knKeywords').value = btn.dataset.keywords || '';
      document.getElementById('knContent').value = btn.dataset.content || '';
      document.getElementById('knHandoff').checked = btn.dataset.handoff === '1';
      document.getElementById('knActive').checked = btn.dataset.active === '1';
      if (resetKnowledgeFormBtn) resetKnowledgeFormBtn.style.display = 'inline-block';
    });
  });

  if (resetKnowledgeFormBtn && knowledgeForm) {
    resetKnowledgeFormBtn.addEventListener('click', () => {
      knowledgeForm.action = defaultStoreAction;
      knowledgeFormMethod.value = 'POST';
      knowledgeFormTitle.textContent = '+ Tambah Knowledge / Batasan Baru';
      knowledgeForm.reset();
      document.getElementById('knActive').checked = true;
      resetKnowledgeFormBtn.style.display = 'none';
    });
  }

  // Filter pencarian di dalam Modal Knowledge Base
  const knSearchInput = document.getElementById('knSearchInput');
  if (knSearchInput) {
    knSearchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      document.querySelectorAll('.knowledge-entry-card').forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const keywords = card.getAttribute('data-keywords') || '';
        const content = card.getAttribute('data-content') || '';
        if (!q || title.includes(q) || keywords.includes(q) || content.includes(q)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }

  // Buka Modal Knowledge Base dengan form terisi otomatis dari chat
  function openQuickKnowledge(question, answer) {
    if (!knowledgeModal) return;
    knowledgeModal.style.display = 'flex';
    if (resetKnowledgeFormBtn) resetKnowledgeFormBtn.click();

    const titleInput = document.getElementById('knTitle');
    const contentInput = document.getElementById('knContent');
    const keywordsInput = document.getElementById('knKeywords');
    const typeSelect = document.getElementById('knType');

    if (titleInput) {
      const cleanQ = (question || '').trim().replace(/[?.,!]+$/, '');
      titleInput.value = cleanQ ? ('FAQ: ' + cleanQ) : 'FAQ: Pertanyaan Baru';
    }
    if (contentInput) {
      contentInput.value = (answer || '').trim();
    }
    if (keywordsInput && question) {
      const cleanWords = question.toLowerCase().replace(/[^a-z0-9\s]/g, ' ')
        .split(/\s+/)
        .filter(w => w.length >= 3 && !['apa', 'apakah', 'bagaimana', 'bisa', 'kak', 'kakak', 'min', 'admin', 'sis', 'mau', 'tanya', 'ada', 'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'ini'].includes(w));
      const uniq = [...new Set(cleanWords)];
      keywordsInput.value = uniq.slice(0, 7).join(', ');
    }
    if (typeSelect) {
      typeSelect.value = 'knowledge';
    }
    if (contentInput && !answer) {
      contentInput.focus();
    }
  }

  // Event listener tombol "💡 Simpan ke Knowledge" di setiap balon chat
  if (messagesStream) {
    messagesStream.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-quick-to-knowledge');
      if (!btn) return;

      const inq = inquiriesData.find(i => i.id == currentInquiryId);
      const isQuestion = btn.getAttribute('data-is-question') === '1';
      const msgText = btn.getAttribute('data-message') || '';

      let question = '';
      let answer = '';

      if (isQuestion) {
        question = msgText;
        if (inq && inq.messages) {
          const idx = inq.messages.findIndex(m => m.message === msgText);
          if (idx !== -1 && inq.messages[idx + 1]) {
            answer = inq.messages[idx + 1].message;
          }
        }
      } else {
        answer = msgText;
        if (inq && inq.messages) {
          const idx = inq.messages.findIndex(m => m.message === msgText);
          if (idx !== -1 && inq.messages[idx - 1]) {
            question = inq.messages[idx - 1].message;
          } else {
            question = inq.subject || '';
          }
        }
      }

      openQuickKnowledge(question, answer);
    });
  }

  function getInitials(name) {
    if (!name) return 'PL';
    return name.trim().split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
  }

  function updateModeBanner(mode) {
    if (!modeBannerBadge) return;
    if (mode === 'human') {
      modeBannerBadge.textContent = 'Ditangani Admin Manusia (AI Nonaktif)';
      modeBannerBadge.style.background = '#FEF3C7';
      modeBannerBadge.style.color = '#92400E';
    } else {
      modeBannerBadge.textContent = 'Ditangani AI Stylist Aria (Otomatis)';
      modeBannerBadge.style.background = '#F3E8FF';
      modeBannerBadge.style.color = '#6B21A8';
    }
  }

  function renderMessages(inquiry) {
    if (!messagesStream || !inquiry) return;
    messagesStream.innerHTML = '';

    const customerInitials = getInitials(inquiry.name);

    if (!inquiry.messages || inquiry.messages.length === 0) {
      messagesStream.innerHTML = `
        <div style="text-align: center; color: #9CA3AF; padding: 30px; font-size: 13px;">
          Belum ada pesan dalam percakapan ini.
        </div>
      `;
      return;
    }

    inquiry.messages.forEach(msg => {
      const isCustomer = msg.sender_type === 'customer';
      const isAi = msg.sender_type === 'ai';
      const timeStr = msg.created_at ? (typeof msg.created_at === 'string' ? msg.created_at.substring(11, 16) : new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})) : '';

      const bubbleRow = document.createElement('div');
      bubbleRow.className = `chat-bubble-row ${isCustomer ? 'customer' : 'admin'}`;

      const avatarLabel = isCustomer ? customerInitials : (isAi ? 'AI' : 'CS');
      const senderLabel = isCustomer
        ? `${inquiry.name} (Pelanggan)`
        : (isAi ? 'Aria AI Stylist (Otomatis)' : 'Staf Admin Manusia (Anda)');

      const aiBubbleStyle = isAi ? 'background: #F5F3FF; color: #1F2937; border: 1px solid #DDD6FE;' : '';

      bubbleRow.innerHTML = `
        <div class="chat-bubble-avatar" style="${isAi ? 'background: #7C3AED; color: #FFF;' : ''}">${avatarLabel}</div>
        <div class="chat-bubble-content-wrap">
          <span class="chat-bubble-sender">${escapeHtml(senderLabel)}</span>
          <div class="chat-bubble" style="${aiBubbleStyle}">${escapeHtml(msg.message)}</div>
          <div class="chat-bubble-meta" style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 5px;">
              <span>${timeStr}</span>
              ${!isCustomer ? '<span title="Terkirim">&#10003;&#10003;</span>' : ''}
            </div>
            <button
              type="button"
              class="btn-quick-to-knowledge"
              data-is-question="${isCustomer ? '1' : '0'}"
              data-message="${escapeHtml(msg.message)}"
              style="background: none; border: none; color: #A18A68; font-size: 10px; font-weight: 700; cursor: pointer; padding: 0 4px; display: inline-flex; align-items: center; gap: 3px;"
              title="${isCustomer ? 'Simpan pertanyaan ini ke Knowledge Base' : 'Jadikan jawaban ini Knowledge Base'}"
            >
              💡 ${isCustomer ? 'Simpan ke Knowledge' : 'Jadikan Knowledge'}
            </button>
          </div>
        </div>
      `;

      messagesStream.appendChild(bubbleRow);
    });

    setTimeout(() => {
      if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }
    }, 50);
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
  }

  function selectInquiry(id) {
    currentInquiryId = id;
    const inquiry = inquiriesData.find(i => i.id == id);
    if (!inquiry) return;

    document.querySelectorAll('.chat-conversation-item').forEach(item => {
      if (item.getAttribute('data-inquiry-id') == id) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });

    if (headerAvatar) headerAvatar.textContent = getInitials(inquiry.name);
    if (headerName) headerName.textContent = inquiry.name;
    if (headerEmail) headerEmail.textContent = inquiry.email;
    if (headerPhone) headerPhone.textContent = inquiry.phone || 'Tanpa No. HP';
    if (headerTicket) headerTicket.textContent = inquiry.ticket_number;
    if (headerSubject) headerSubject.textContent = inquiry.subject || 'Layanan Toko';

    if (headerChannel) {
      if (inquiry.channel === 'contact_form') {
        headerChannel.textContent = 'Formulir Kontak';
        headerChannel.className = 'chat-header-channel-tag form';
      } else {
        headerChannel.textContent = 'Live Chat Toko';
        headerChannel.className = 'chat-header-channel-tag';
      }
    }

    if (headerWa) {
      if (inquiry.phone) {
        const cleanPhone = inquiry.phone.replace(/[^0-9]/g, '');
        headerWa.href = `https://wa.me/${cleanPhone}`;
        headerWa.style.display = 'inline-flex';
      } else {
        headerWa.style.display = 'none';
      }
    }

    if (statusSelect) {
      const val = typeof inquiry.status === 'object' ? inquiry.status.value : inquiry.status;
      statusSelect.value = val;
    }

    const modeVal = inquiry.handling_mode || 'ai';
    if (modeSelect) {
      modeSelect.value = modeVal;
    }
    updateModeBanner(modeVal);

    renderMessages(inquiry);
    if (chatInput) chatInput.focus();
  }

  if (conversationsList) {
    conversationsList.addEventListener('click', function(e) {
      const item = e.target.closest('.chat-conversation-item');
      if (item) {
        const id = item.getAttribute('data-inquiry-id');
        selectInquiry(id);
      }
    });
  }

  filterPills.forEach(pill => {
    pill.addEventListener('click', function() {
      filterPills.forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      activeFilter = this.getAttribute('data-filter');
      applySidebarFilter();
    });
  });

  if (chatSearchInput) {
    chatSearchInput.addEventListener('input', applySidebarFilter);
  }

  function applySidebarFilter() {
    const q = (chatSearchInput ? chatSearchInput.value : '').toLowerCase().trim();

    document.querySelectorAll('.chat-conversation-item').forEach(item => {
      const id = item.getAttribute('data-inquiry-id');
      const inq = inquiriesData.find(i => i.id == id);
      if (!inq) return;

      const statusVal = typeof inq.status === 'object' ? inq.status.value : inq.status;
      const modeVal = inq.handling_mode || 'ai';

      let matchesFilter = true;
      if (activeFilter === 'unread') matchesFilter = (statusVal === 'unread');
      else if (activeFilter === 'human') matchesFilter = (modeVal === 'human');
      else if (activeFilter === 'resolved') matchesFilter = (statusVal === 'resolved');

      let matchesSearch = true;
      if (q) {
        const name = (inq.name || '').toLowerCase();
        const email = (inq.email || '').toLowerCase();
        const ticket = (inq.ticket_number || '').toLowerCase();
        const subject = (inq.subject || '').toLowerCase();
        const msgText = (inq.messages || []).map(m => m.message.toLowerCase()).join(' ');
        matchesSearch = name.includes(q) || email.includes(q) || ticket.includes(q) || subject.includes(q) || msgText.includes(q);
      }

      if (matchesFilter && matchesSearch) {
        item.style.display = 'flex';
      } else {
        item.style.display = 'none';
      }
    });
  }

  document.querySelectorAll('.chat-template-chip').forEach(chip => {
    chip.addEventListener('click', function() {
      const tmpl = this.getAttribute('data-template');
      if (chatInput) {
        chatInput.value = tmpl;
        chatInput.focus();
      }
    });
  });

  if (btnGenerateAiDraft) {
    btnGenerateAiDraft.addEventListener('click', async function() {
      if (!currentInquiryId || !chatInput) return;
      const originalText = btnGenerateAiDraft.innerHTML;
      btnGenerateAiDraft.textContent = 'Menyusun Draft AI...';
      btnGenerateAiDraft.disabled = true;

      try {
        const res = await fetch(`/admin/inquiries/${currentInquiryId}/ai-draft`, {
          headers: { Accept: 'application/json' }
        });
        const payload = await res.json();
        if (payload?.success && payload?.data?.draft) {
          chatInput.value = payload.data.draft.replace(/\n+/g, ' ');
          chatInput.focus();
        }
      } catch (e) {
        console.error(e);
      } finally {
        btnGenerateAiDraft.innerHTML = originalText;
        btnGenerateAiDraft.disabled = false;
      }
    });
  }

  if (modeSelect) {
    modeSelect.addEventListener('change', async function() {
      if (!currentInquiryId) return;
      const newMode = this.value;
      const inq = inquiriesData.find(i => i.id == currentInquiryId);

      try {
        const res = await fetch(`/admin/inquiries/${currentInquiryId}/mode`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ handling_mode: newMode }),
        });

        const data = await res.json();
        if (data.success) {
          if (inq) inq.handling_mode = newMode;
          updateModeBanner(newMode);

          const sidebarItem = document.querySelector(`.chat-conversation-item[data-inquiry-id="${currentInquiryId}"]`);
          if (sidebarItem) {
            sidebarItem.setAttribute('data-mode', newMode);
            const pill = sidebarItem.querySelector('.chat-item-mode-pill');
            if (pill) {
              pill.textContent = newMode === 'human' ? 'Admin Manual' : 'AI Hybrid';
              pill.style.background = newMode === 'human' ? '#FEF3C7' : '#F3E8FF';
              pill.style.color = newMode === 'human' ? '#92400E' : '#6B21A8';
            }
          }
        }
      } catch (err) {
        console.error(err);
      }
    });
  }

  if (sendForm) {
    sendForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      if (!currentInquiryId || !chatInput) return;

      const message = chatInput.value.trim();
      if (!message) return;

      const inq = inquiriesData.find(i => i.id == currentInquiryId);
      if (!inq) return;

      sendBtn.disabled = true;
      const originalBtnHtml = sendBtn.innerHTML;
      sendBtn.innerHTML = '<span style="font-size: 11px;">...</span>';

      try {
        const replyUrl = `/admin/inquiries/${currentInquiryId}/reply`;
        const saveKnCheckbox = document.getElementById('adminSaveAsKnowledgeCheckbox');
        const shouldSaveKn = saveKnCheckbox ? saveKnCheckbox.checked : false;

        const res = await fetch(replyUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({
            message: message,
            save_as_knowledge: shouldSaveKn,
          }),
        });

        const data = await res.json();

        if (data.success) {
          if (data.data?.knowledge_saved) {
            alert('🎉 ' + data.message + '\nPertanyaan dan balasan ini kini menjadi pengetahuan resmi AI untuk menjawab pelanggan serupa secara otomatis.');
            if (saveKnCheckbox) saveKnCheckbox.checked = false;
          }
          const newMsg = {
            id: data.data.id,
            sender_type: 'admin',
            message: message,
            created_at: new Date().toISOString(),
          };

          if (!inq.messages) inq.messages = [];
          inq.messages.push(newMsg);

          inq.status = 'responded';
          inq.handling_mode = 'human';
          if (statusSelect) statusSelect.value = 'responded';
          if (modeSelect) modeSelect.value = 'human';
          updateModeBanner('human');

          const sidebarItem = document.querySelector(`.chat-conversation-item[data-inquiry-id="${currentInquiryId}"]`);
          if (sidebarItem) {
            const snipEl = sidebarItem.querySelector('.chat-item-snippet');
            const timeEl = sidebarItem.querySelector('.chat-item-time');
            const unreadDot = sidebarItem.querySelector('.chat-unread-dot');
            const pill = sidebarItem.querySelector('.chat-item-mode-pill');
            if (snipEl) snipEl.textContent = 'Admin: ' + message;
            if (timeEl) timeEl.textContent = data.data.created_at || 'Baru saja';
            if (unreadDot) unreadDot.remove();
            if (pill) {
              pill.textContent = 'Admin Manual';
              pill.style.background = '#FEF3C7';
              pill.style.color = '#92400E';
            }
          }

          renderMessages(inq);
          chatInput.value = '';
          updateUnreadBadgeCounter();
        } else {
          alert('Gagal mengirim pesan: ' + (data.message || 'Terjadi kesalahan'));
        }
      } catch (err) {
        console.error(err);
        alert('Gagal terhubung ke server untuk mengirim pesan.');
      } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalBtnHtml;
        chatInput.focus();
      }
    });
  }

  if (statusSelect) {
    statusSelect.addEventListener('change', async function() {
      if (!currentInquiryId) return;
      const newStatus = this.value;
      const inq = inquiriesData.find(i => i.id == currentInquiryId);

      try {
        const statusUrl = `/admin/inquiries/${currentInquiryId}/status`;
        const res = await fetch(statusUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ status: newStatus }),
        });

        const data = await res.json();
        if (data.success) {
          if (inq) inq.status = newStatus;
          updateUnreadBadgeCounter();
          applySidebarFilter();
        }
      } catch (err) {
        console.error(err);
      }
    });
  }

  function updateUnreadBadgeCounter() {
    const unreadCount = inquiriesData.filter(i => {
      const st = typeof i.status === 'object' ? i.status.value : i.status;
      return st === 'unread';
    }).length;

    const badge = document.getElementById('chatUnreadCountBadge');
    if (badge) {
      badge.textContent = `${unreadCount} Pesan Belum Dibaca`;
    }
  }

  if (inquiriesData.length > 0) {
    selectInquiry(inquiriesData[0].id);
  }
})();
</script>
@endpush
