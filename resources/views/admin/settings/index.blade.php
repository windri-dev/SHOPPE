@php
  $isId = app()->getLocale() === 'id';
@endphp

@extends('layouts.admin')

@section('title', ($isId ? 'Pengaturan Toko & Bank' : 'Store & Bank Settings') . ' - PUTRIJALVA Admin')
@section('page_title', $isId ? 'Pengaturan Toko' : 'Store Settings')

@section('content')
<div class="admin-page-content" style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; padding-bottom: 60px;">

  <!-- Header Card -->
  <div class="admin-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 20px; font-weight: 800; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 10px;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="3"></circle>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
        </svg>
        <span>{{ $isId ? 'Pengaturan Toko & Rekening Pembayaran' : 'Store & Payment Settings' }}</span>
      </h1>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        {{ $isId ? 'Konfigurasi rekening bank pembayaran yang aktif untuk pelanggan serta tautan akun media sosial resmi butik.' : 'Configure active payment bank accounts for customers and official boutique social media links.' }}
      </p>
    </div>

    <!-- Tab Buttons -->
    <div style="display: flex; gap: 8px; background: #F3F4F6; padding: 4px; border-radius: 8px;">
      <a href="{{ route('admin.settings.index', ['tab' => 'banks']) }}" style="padding: 7px 16px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 6px; transition: all 0.2s; color: {{ $activeTab === 'banks' ? '#111827' : '#6B7280' }}; background: {{ $activeTab === 'banks' ? '#FFFFFF' : 'transparent' }}; box-shadow: {{ $activeTab === 'banks' ? '0 1px 3px rgba(0,0,0,0.08)' : 'none' }}; display: flex; align-items: center; gap: 6px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
        <span>{{ $isId ? 'Rekening Bank Pembayaran' : 'Bank Accounts' }}</span>
      </a>
      <a href="{{ route('admin.settings.index', ['tab' => 'social']) }}" style="padding: 7px 16px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 6px; transition: all 0.2s; color: {{ $activeTab === 'social' ? '#111827' : '#6B7280' }}; background: {{ $activeTab === 'social' ? '#FFFFFF' : 'transparent' }}; box-shadow: {{ $activeTab === 'social' ? '0 1px 3px rgba(0,0,0,0.08)' : 'none' }}; display: flex; align-items: center; gap: 6px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
        <span>{{ $isId ? 'Akun Media Sosial & Kontak' : 'Social Media & Contact' }}</span>
      </a>
    </div>
  </div>

  @if($activeTab === 'banks')
    <!-- TAB 1: BANK ACCOUNTS MANAGEMENT -->
    <div style="display: flex; flex-direction: column; gap: 18px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 2px 0;">
            {{ $isId ? 'Daftar Rekening Bank Butik' : 'Boutique Bank Accounts' }}
          </h2>
          <p style="font-size: 12.5px; color: #6B7280; margin: 0;">
            {{ $isId ? 'Hanya rekening dengan status "Aktif" yang akan ditampilkan kepada pelanggan di halaman pembayaran.' : 'Only active accounts will be presented to customers on checkout and payment pages.' }}
          </p>
        </div>
        <button type="button" onclick="openAddBankModal()" class="btn-dash-primary" style="background: #111827; color: #FFFFFF; border: none; border-radius: 8px; padding: 9px 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <span>{{ $isId ? 'Tambah Rekening Baru' : 'Add Bank Account' }}</span>
        </button>
      </div>

      <!-- Bank Cards Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        @forelse($banks as $bank)
          <div style="background: #FFFFFF; border: 1.5px solid {{ $bank->is_active ? '#E5E7EB' : '#F3F4F6' }}; border-radius: 10px; padding: 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); opacity: {{ $bank->is_active ? '1' : '0.65' }}; position: relative; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  {!! $bank->bank_icon_svg !!}
                  <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: {{ $bank->bank_color }};">
                      {{ $bank->bank_name }}
                    </h3>
                    @if($bank->bank_code)
                      <span style="font-size: 10px; color: #94A3B8; text-transform: uppercase; font-family: monospace;">KODE: {{ $bank->bank_code }}</span>
                    @endif
                  </div>
                </div>
                <!-- Status Badge -->
                @if($bank->is_active)
                  <span style="font-size: 11px; font-weight: 700; background: #ECFDF5; color: #047857; padding: 2px 8px; border-radius: 9999px; border: 1px solid #A7F3D0; display: inline-flex; align-items: center; gap: 4px;">
                    <span style="width: 5px; height: 5px; border-radius: 50%; background: #10B981;"></span>
                    {{ $isId ? 'Aktif' : 'Active' }}
                  </span>
                @else
                  <span style="font-size: 11px; font-weight: 600; background: #F3F4F6; color: #6B7280; padding: 2px 8px; border-radius: 9999px;">
                    {{ $isId ? 'Nonaktif' : 'Inactive' }}
                  </span>
                @endif
              </div>

              <!-- Account Number & Copy Box -->
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 14px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <span style="font-size: 10px; color: #64748B; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">No. Rekening</span>
                  <span style="font-family: monospace; font-size: 16px; font-weight: 800; color: #0F172A;">{{ $bank->account_number }}</span>
                </div>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $bank->clean_account_number }}'); alert('Nomor rekening disalin: {{ $bank->clean_account_number }}')" style="background: #E2E8F0; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; color: #334155; cursor: pointer;">
                  Salin
                </button>
              </div>

              <!-- Account Holder & Branch -->
              <div style="font-size: 12px; color: #374151; margin-bottom: 4px;">
                <span style="color: #6B7280;">Atas Nama:</span> <strong>{{ $bank->account_holder }}</strong>
              </div>
              @if($bank->branch)
                <div style="font-size: 11.5px; color: #6B7280; margin-bottom: 4px;">
                  Cabang: {{ $bank->branch }}
                </div>
              @endif
              @if($bank->instructions)
                <div style="font-size: 11px; color: #9CA3AF; margin-top: 6px; font-style: italic;">
                  {{ $bank->instructions }}
                </div>
              @endif
            </div>

            <!-- Card Actions -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #F3F4F6; padding-top: 12px; margin-top: 14px;">
              <!-- Toggle Active Form -->
              <form action="{{ route('admin.settings.banks.toggle', $bank) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="table-action-btn" style="font-size: 11.5px; padding: 4px 10px; cursor: pointer; border-color: {{ $bank->is_active ? '#F59E0B' : '#10B981' }}; color: {{ $bank->is_active ? '#B45309' : '#047857' }}; background: #fff;">
                  {{ $bank->is_active ? ($isId ? 'Nonaktifkan' : 'Deactivate') : ($isId ? 'Aktifkan' : 'Activate') }}
                </button>
              </form>

              <div style="display: flex; gap: 6px;">
                <!-- Edit Button -->
                <button
                  type="button"
                  onclick="openEditBankModal('{{ $bank->id }}', '{{ addslashes($bank->bank_name) }}', '{{ $bank->account_number }}', '{{ addslashes($bank->account_holder) }}', '{{ addslashes($bank->branch ?? '') }}', '{{ addslashes($bank->instructions ?? '') }}', '{{ $bank->sort_order }}', {{ $bank->is_active ? 'true' : 'false' }}, '{{ $bank->bank_code }}')"
                  class="table-action-btn"
                  style="font-size: 11.5px; padding: 4px 10px; cursor: pointer; background: #F3F4F6;"
                >
                  {{ $isId ? 'Edit' : 'Edit' }}
                </button>

                <!-- Delete Form -->
                <form action="{{ route('admin.settings.banks.destroy', $bank) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus rekening {{ $bank->bank_name }} ({{ $bank->account_number }})?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="table-action-btn" style="font-size: 11.5px; padding: 4px 8px; cursor: pointer; color: #DC2626; border-color: #FECACA; background: #FEF2F2;" title="Hapus Rekening">
                    &times;
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="admin-card" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6B7280;">
            {{ $isId ? 'Belum ada rekening bank yang terdaftar. Klik "+ Tambah Rekening Baru" untuk menambahkan.' : 'No bank accounts registered yet.' }}
          </div>
        @endforelse
      </div>
    </div>

  @else
    <!-- TAB 2: SOCIAL MEDIA & STORE CONTACT SETTINGS -->
    <div class="admin-card" style="padding: 24px;">
      <h2 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">
        {{ $isId ? 'Tautan Akun Media Sosial & Kontak Butik' : 'Social Media & Contact Information' }}
      </h2>
      <p style="font-size: 12.5px; color: #6B7280; margin: 0 0 20px 0;">
        {{ $isId ? 'Tautan ini akan langsung muncul di footer etalase toko, tombol share produk, dan menu bantuan pelanggan.' : 'These links will be displayed in the storefront footer, product share links, and customer concierge.' }}
      </p>

      <form action="{{ route('admin.settings.social') }}" method="POST" style="display: flex; flex-direction: column; gap: 18px;">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
          <!-- Instagram -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E1306C" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
              Instagram URL / Username
            </label>
            <input type="text" name="social_instagram" value="{{ old('social_instagram', $social['instagram']) }}" placeholder="https://instagram.com/putrijalva.official" class="chat-search-input" style="width: 100%; font-size: 13px;">
            <span style="font-size: 11px; color: #9CA3AF; margin-top: 3px; display: block;">Contoh: https://instagram.com/putrijalva.official</span>
          </div>

          <!-- TikTok -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.82 4.47 6.27 6.27 0 0 0 1.87-4.47V8.67a8.21 8.21 0 0 0 4.9 1.6v-3.58z"/></svg>
              TikTok URL / Akun
            </label>
            <input type="text" name="social_tiktok" value="{{ old('social_tiktok', $social['tiktok']) }}" placeholder="https://tiktok.com/@putrijalva" class="chat-search-input" style="width: 100%; font-size: 13px;">
            <span style="font-size: 11px; color: #9CA3AF; margin-top: 3px; display: block;">Contoh: https://tiktok.com/@putrijalva</span>
          </div>

          <!-- WhatsApp CS -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
              Nomor WhatsApp Concierge / CS
            </label>
            <input type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $social['whatsapp']) }}" placeholder="6281234567890" class="chat-search-input" style="width: 100%; font-size: 13px;">
            <span style="font-size: 11px; color: #9CA3AF; margin-top: 3px; display: block;">Gunakan format angka internasional tanpa tanda + (contoh: 6281234567890)</span>
          </div>

          <!-- Facebook -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1877F2" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
              Facebook Page URL
            </label>
            <input type="text" name="social_facebook" value="{{ old('social_facebook', $social['facebook']) }}" placeholder="https://facebook.com/putrijalva" class="chat-search-input" style="width: 100%; font-size: 13px;">
          </div>

          <!-- YouTube -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FF0000" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
              YouTube Channel URL
            </label>
            <input type="text" name="social_youtube" value="{{ old('social_youtube', $social['youtube']) }}" placeholder="https://youtube.com/@putrijalva" class="chat-search-input" style="width: 100%; font-size: 13px;">
          </div>

          <!-- Official Store Email -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px;">
              Email Resmi Butik
            </label>
            <input type="email" name="store_email" value="{{ old('store_email', $social['email']) }}" placeholder="concierge@wn-cdn.net" class="chat-search-input" style="width: 100%; font-size: 13px;">
          </div>
        </div>

        <!-- Store Address -->
        <div>
          <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px;">
            Alamat Atelier &amp; Kantor Butik
          </label>
          <textarea name="store_address" rows="3" class="chat-search-input" style="width: 100%; font-size: 13px;">{{ old('store_address', $social['address']) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; border-top: 1px solid #E5E7EB; padding-top: 16px; margin-top: 8px;">
          <button type="submit" class="btn-dash-primary" style="background: #111827; color: #FFFFFF; border: none; border-radius: 8px; padding: 11px 26px; font-size: 13.5px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
            {{ $isId ? 'Simpan Pengaturan Sosial Media' : 'Save Social Settings' }}
          </button>
        </div>
      </form>
    </div>
  @endif

</div>

<!-- MODAL TAMBAH REKENING BANK -->
<div id="addBankModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Tambah Rekening Bank Baru</h3>
      <button type="button" onclick="closeAddBankModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form action="{{ route('admin.settings.banks.store') }}" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      <!-- Preset Bank Dropdown -->
      <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px;">
        <label style="display: block; font-size: 12px; font-weight: 700; color: #1E293B; margin-bottom: 6px;">
          Pilih Bank Terdaftar (Nama & Ikon Tersedia Otomatis) *
        </label>
        <select id="presetBankSelect" onchange="applyAddBankPreset(this.value)" class="chat-search-input" style="width: 100%; font-size: 13px; font-weight: 600; background: #FFFFFF; cursor: pointer;">
          <option value="">-- Klik untuk Pilih Bank Resmi --</option>
          <option value="bca">BCA (Bank Central Asia)</option>
          <option value="mandiri">Bank Mandiri</option>
          <option value="bni">Bank BNI (Bank Negara Indonesia)</option>
          <option value="bri">Bank BRI (Bank Rakyat Indonesia)</option>
          <option value="bsi">Bank Syariah Indonesia (BSI)</option>
          <option value="cimb">CIMB Niaga</option>
          <option value="permata">Bank Permata</option>
          <option value="danamon">Bank Danamon</option>
          <option value="jago">Bank Jago</option>
          <option value="seabank">SeaBank Indonesia</option>
          <option value="btn">Bank BTN</option>
          <option value="jenius">Jenius (BTPN)</option>
          <option value="custom">Bank Lainnya (Ketik Manual)...</option>
        </select>
        <div id="addPresetBankPreview" style="display: none; align-items: center; gap: 8px; margin-top: 10px; padding: 8px 12px; background: #FFFFFF; border: 1px dashed #CBD5E1; border-radius: 6px;">
          <span id="addPresetBankIcon"></span>
          <div>
            <span id="addPresetBankName" style="font-weight: 700; font-size: 13px; display: block;"></span>
            <span style="font-size: 11px; color: #64748B;">Nama & ikon bank otomatis terisi dan siap digunakan.</span>
          </div>
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Bank *</label>
        <input type="text" name="bank_name" id="addBankName" required placeholder="Contoh: BCA / Bank Mandiri / BNI / BRI" class="chat-search-input" style="width: 100%; font-size: 13px;">
        <input type="hidden" name="bank_code" id="addBankCode" value="">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nomor Rekening *</label>
        <input type="text" name="account_number" required placeholder="Contoh: 123-456-7890" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Pemilik Rekening (Atas Nama) *</label>
        <input type="text" name="account_holder" required value="PT PUTRIJALVA INDONESIA" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Cabang Bank (Opsional)</label>
        <input type="text" name="branch" placeholder="Contoh: KCU Sudirman Jakarta" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Petunjuk Tambahan (Opsional)</label>
        <input type="text" name="instructions" placeholder="Contoh: Transfer tepat hingga 3 digit terakhir" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" name="is_active" id="addBankActive" value="1" checked style="width: 16px; height: 16px;">
        <label for="addBankActive" style="font-size: 13px; font-weight: 600; color: #374151; cursor: pointer;">
          Aktifkan rekening ini langsung di halaman pembayaran
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeAddBankModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="btn-dash-primary" style="background: #111827; color: #FFFFFF; border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 700; cursor: pointer;">Simpan Rekening</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT REKENING BANK -->
<div id="editBankModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Edit Data Rekening Bank</h3>
      <button type="button" onclick="closeEditBankModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="editBankForm" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      @method('PUT')
      <!-- Preset Bank Dropdown (Optional Override) -->
      <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 12px;">
        <label style="display: block; font-size: 12px; font-weight: 700; color: #1E293B; margin-bottom: 6px;">
          Ganti ke Bank Terdaftar (Nama & Ikon Otomatis)
        </label>
        <select id="editPresetBankSelect" onchange="applyEditBankPreset(this.value)" class="chat-search-input" style="width: 100%; font-size: 13px; font-weight: 600; background: #FFFFFF; cursor: pointer;">
          <option value="">-- Tetap Gunakan Bank Saat Ini --</option>
          <option value="bca">BCA (Bank Central Asia)</option>
          <option value="mandiri">Bank Mandiri</option>
          <option value="bni">Bank BNI (Bank Negara Indonesia)</option>
          <option value="bri">Bank BRI (Bank Rakyat Indonesia)</option>
          <option value="bsi">Bank Syariah Indonesia (BSI)</option>
          <option value="cimb">CIMB Niaga</option>
          <option value="permata">Bank Permata</option>
          <option value="danamon">Bank Danamon</option>
          <option value="jago">Bank Jago</option>
          <option value="seabank">SeaBank Indonesia</option>
          <option value="btn">Bank BTN</option>
          <option value="jenius">Jenius (BTPN)</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Bank *</label>
        <input type="text" name="bank_name" id="editBankName" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        <input type="hidden" name="bank_code" id="editBankCode" value="">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nomor Rekening *</label>
        <input type="text" name="account_number" id="editAccountNumber" required class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Pemilik Rekening *</label>
        <input type="text" name="account_holder" id="editAccountHolder" required class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Cabang Bank (Opsional)</label>
        <input type="text" name="branch" id="editBranch" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Petunjuk Tambahan (Opsional)</label>
        <input type="text" name="instructions" id="editInstructions" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" name="is_active" id="editBankActive" value="1" style="width: 16px; height: 16px;">
        <label for="editBankActive" style="font-size: 13px; font-weight: 600; color: #374151; cursor: pointer;">
          Rekening Aktif (Tampilkan di halaman checkout)
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeEditBankModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="btn-dash-primary" style="background: #111827; color: #FFFFFF; border: none; border-radius: 8px; padding: 9px 20px; font-size: 13px; font-weight: 700; cursor: pointer;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
const bankPresetsData = {
  bca: { name: 'BCA (Bank Central Asia)', code: 'bca', color: '#003399', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#003399"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="9" text-anchor="middle">BCA</text></svg>' },
  mandiri: { name: 'Bank Mandiri', code: 'mandiri', color: '#003066', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#003066"/><path d="M4 14C8 10 16 10 20 14" stroke="#FDB913" stroke-width="2.5" stroke-linecap="round"/><text x="12" y="11" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="800" font-size="6.5" text-anchor="middle">MANDIRI</text></svg>' },
  bni: { name: 'Bank BNI (Bank Negara Indonesia)', code: 'bni', color: '#FA5400', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#FA5400"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="9" text-anchor="middle">BNI</text></svg>' },
  bri: { name: 'Bank BRI (Bank Rakyat Indonesia)', code: 'bri', color: '#00529C', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#00529C"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="9" text-anchor="middle">BRI</text></svg>' },
  bsi: { name: 'Bank Syariah Indonesia (BSI)', code: 'bsi', color: '#00A39D', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#00A39D"/><circle cx="12" cy="7" r="2" fill="#EAAA00"/><text x="12" y="18" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="8" text-anchor="middle">BSI</text></svg>' },
  cimb: { name: 'CIMB Niaga', code: 'cimb', color: '#ED1C24', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#ED1C24"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="8" text-anchor="middle">CIMB</text></svg>' },
  permata: { name: 'Bank Permata', code: 'permata', color: '#84BD00', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#84BD00"/><polygon points="12,5 18,12 12,19 6,12" fill="#FFFFFF"/></svg>' },
  danamon: { name: 'Bank Danamon', code: 'danamon', color: '#F15A24', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#F15A24"/><line x1="6" y1="12" x2="18" y2="12" stroke="#FFD100" stroke-width="3"/><line x1="6" y1="8" x2="18" y2="8" stroke="#FFFFFF" stroke-width="2"/><line x1="6" y1="16" x2="18" y2="16" stroke="#FFFFFF" stroke-width="2"/></svg>' },
  jago: { name: 'Bank Jago', code: 'jago', color: '#FF7F00', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#FF7F00"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="7.5" text-anchor="middle">JAGO</text></svg>' },
  seabank: { name: 'SeaBank Indonesia', code: 'seabank', color: '#FF5722', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#FF5722"/><text x="12" y="15" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="800" font-size="7" text-anchor="middle">Sea</text></svg>' },
  btn: { name: 'Bank BTN', code: 'btn', color: '#1C3F94', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#1C3F94"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="8.5" text-anchor="middle">BTN</text></svg>' },
  jenius: { name: 'Jenius (BTPN)', code: 'jenius', color: '#00A4E4', icon: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#00A4E4"/><circle cx="12" cy="12" r="6" stroke="#FFFFFF" stroke-width="2.5"/></svg>' }
};

function applyAddBankPreset(key) {
  const p = bankPresetsData[key];
  const nameInput = document.getElementById('addBankName');
  const codeInput = document.getElementById('addBankCode');
  const previewDiv = document.getElementById('addPresetBankPreview');
  const iconSpan = document.getElementById('addPresetBankIcon');
  const nameSpan = document.getElementById('addPresetBankName');

  if (p) {
    if (nameInput) nameInput.value = p.name;
    if (codeInput) codeInput.value = p.code;
    if (previewDiv) previewDiv.style.display = 'flex';
    if (iconSpan) iconSpan.innerHTML = p.icon;
    if (nameSpan) {
      nameSpan.textContent = p.name;
      nameSpan.style.color = p.color;
    }
  } else {
    if (previewDiv) previewDiv.style.display = 'none';
    if (key === 'custom') {
      if (nameInput) nameInput.value = '';
      if (codeInput) codeInput.value = 'other';
    }
  }
}

function applyEditBankPreset(key) {
  const p = bankPresetsData[key];
  const nameInput = document.getElementById('editBankName');
  const codeInput = document.getElementById('editBankCode');
  if (p) {
    if (nameInput) nameInput.value = p.name;
    if (codeInput) codeInput.value = p.code;
  }
}

function openAddBankModal() {
  document.getElementById('presetBankSelect').value = '';
  document.getElementById('addPresetBankPreview').style.display = 'none';
  document.getElementById('addBankModalBackdrop').style.display = 'flex';
}
function closeAddBankModal() {
  document.getElementById('addBankModalBackdrop').style.display = 'none';
}

function openEditBankModal(id, bankName, accountNumber, accountHolder, branch, instructions, sortOrder, isActive, bankCode) {
  document.getElementById('editBankForm').action = '/admin/settings/banks/' + id;
  document.getElementById('editBankName').value = bankName;
  document.getElementById('editBankCode').value = bankCode || '';
  document.getElementById('editAccountNumber').value = accountNumber;
  document.getElementById('editAccountHolder').value = accountHolder;
  document.getElementById('editBranch').value = branch;
  document.getElementById('editInstructions').value = instructions;
  document.getElementById('editBankActive').checked = isActive;
  document.getElementById('editPresetBankSelect').value = '';
  document.getElementById('editBankModalBackdrop').style.display = 'flex';
}
function closeEditBankModal() {
  document.getElementById('editBankModalBackdrop').style.display = 'none';
}
</script>
@endsection
