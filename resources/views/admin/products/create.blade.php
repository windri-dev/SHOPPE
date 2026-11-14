@extends('layouts.admin')

@section('title', 'Tambah Produk Baru - PUTRIJALVA Admin')
@section('page_title', 'Tambah Produk Baru')

@section('content')
<style>
/* ================= WORD DOCUMENT EDITOR STYLES ================= */
.ribbon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border: 1px solid transparent;
  border-radius: 5px;
  background: transparent;
  color: #374151;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.15s ease;
  padding: 0;
}
.ribbon-btn:hover {
  background: #E5E7EB;
  border-color: #D1D5DB;
  color: #111827;
}
.ribbon-btn:active, .ribbon-btn.active {
  background: #E0E7FF;
  border-color: #A5B4FC;
  color: #4338CA;
}
.ribbon-btn-text {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  height: 28px;
  padding: 0 10px;
  border: 1px solid #D1D5DB;
  border-radius: 5px;
  background: #FFFFFF;
  color: #374151;
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}
.ribbon-btn-text:hover {
  background: #F3F4F6;
  border-color: #9CA3AF;
  color: #111827;
}
.ribbon-btn-special {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  height: 28px;
  padding: 0 12px;
  border: 1px solid #EFE8DC;
  border-radius: 5px;
  background: #FAF7F2;
  color: #8C7554;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}
.ribbon-btn-special:hover {
  background: #F4EFE6;
  border-color: #D8C8B0;
  color: #6D5638;
}

/* Word Document Paper Inside Canvas */
.word-document-paper:focus {
  outline: none;
}
.word-document-paper:empty:before {
  content: attr(data-placeholder);
  color: #9CA3AF;
  pointer-events: none;
  display: block;
  font-style: italic;
}
.word-document-paper p {
  margin: 0 0 12px 0;
  line-height: 1.75;
}
.word-document-paper h2 {
  font-size: 18px;
  font-weight: 800;
  color: #111827;
  margin: 20px 0 10px 0;
  border-bottom: 1px solid #F3F4F6;
  padding-bottom: 5px;
}
.word-document-paper h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1F2937;
  margin: 16px 0 8px 0;
}
.word-document-paper h4 {
  font-size: 14px;
  font-weight: 700;
  color: #374151;
  margin: 12px 0 6px 0;
}
.word-document-paper ul, .word-document-paper ol {
  margin: 0 0 14px 0;
  padding-left: 24px;
}
.word-document-paper li {
  margin-bottom: 5px;
  line-height: 1.65;
}
.word-document-paper table {
  width: 100%;
  border-collapse: collapse;
  margin: 16px 0;
  font-size: 13px;
  border: 1px solid #E5E7EB;
}
.word-document-paper table th {
  background: #FAF7F2;
  color: #111827;
  font-weight: 700;
  padding: 10px 14px;
  border: 1px solid #E5E7EB;
  text-align: left;
}
.word-document-paper table td {
  padding: 9px 14px;
  border: 1px solid #E5E7EB;
  color: #374151;
}
.word-document-paper blockquote {
  margin: 14px 0;
  padding: 10px 16px;
  border-left: 4px solid #A18A68;
  background: #FAF7F2;
  color: #4B5563;
  font-style: italic;
  border-radius: 0 6px 6px 0;
}
.word-document-paper hr {
  border: none;
  border-top: 1px solid #E5E7EB;
  margin: 18px 0;
}
.word-document-paper img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 10px 0;
}
.word-document-paper a {
  color: #2563EB;
  text-decoration: underline;
}
</style>
<div class="admin-create-product-page" style="display: flex; flex-direction: column; gap: 24px; max-width: 1200px; margin: 0 auto; padding-bottom: 40px;">

  <!-- Header & Breadcrumbs Bar -->
  <div style="background: #FFFFFF; padding: 20px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
    <div>
      <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6B7280; margin-bottom: 6px;">
        <a href="{{ route('admin.dashboard') }}" style="color: #6B7280; text-decoration: none;">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.products.index') }}" style="color: #6B7280; text-decoration: none;">Katalog Produk</a>
        <span>/</span>
        <span style="color: #A18A68; font-weight: 600;">Tambah Produk Baru</span>
      </div>
      <h1 style="font-size: 20px; font-weight: 800; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
        Tambah Koleksi Busana Baru
      </h1>
      <p style="font-size: 13px; color: #6B7280; margin: 4px 0 0 0;">
        Isi detail informasi produk, upload foto &amp; video ke server, tetapkan harga dalam Rupiah (Rp), dan kelola stok persediaan.
      </p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
      <a href="{{ route('admin.products.index') }}" class="btn-outline" style="font-size: 13px; padding: 9px 16px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 8px; color: #374151; display: inline-flex; align-items: center; gap: 6px; background: #FFFFFF; font-weight: 600;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali ke Katalog
      </a>
    </div>
  </div>

  @if($errors->any())
    <div style="background: #FEF2F2; border: 1px solid #F87171; border-radius: 10px; padding: 14px 18px; color: #991B1B; font-size: 13px;">
      <div style="font-weight: 700; margin-bottom: 4px;">Harap periksa kembali isian formulir:</div>
      <ul style="margin: 0; padding-left: 20px;">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Main Form -->
  <form id="createProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">

      <!-- ================= KOLOM KIRI (UTAMA): INFORMASI & DESKRIPSI ================= -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Card 1: Informasi Dasar Produk -->
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 16px;">
          <div style="border-bottom: 1px solid #F3F4F6; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              1. Informasi Dasar Produk
            </h2>
            <span style="font-size: 11px; background: #FAF7F2; color: #8C7554; padding: 3px 10px; border-radius: 99px; font-weight: 600; border: 1px solid #EFE8DC;">Wajib diisi</span>
          </div>

          <!-- Banner Auto-Translate AI -->
          <div style="background: #FDF8F0; border: 1px solid #F3E2C7; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <div style="font-size: 12px; color: #78350F; line-height: 1.45; display: flex; align-items: center; gap: 8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="#D97706" style="flex-shrink: 0;"><path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/></svg>
              <div><strong>Dua Bahasa Otomatis (Bilingual ID &bull; EN):</strong> Cukup isi Nama &amp; Deskripsi dalam Bahasa Indonesia. Sistem AI akan otomatis menerjemahkan ke versi Bahasa Inggris saat disimpan, atau klik tombol <em>Translate ke EN</em> di samping.</div>
            </div>
            <button type="button" id="btnAutoTranslate" onclick="triggerAiTranslate()" style="background: #A18A68; color: #FFF; border: none; padding: 7px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(161,138,104,0.2);">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.59 9.41L24 12L14.59 14.59L12 24L9.41 14.59L0 12L9.41 9.41L12 0Z"/></svg>
              Translate ke EN
            </button>
          </div>

          <!-- Nama Produk ID & EN -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div>
              <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px;">Nama Produk (Indonesia) *</label>
              <input type="text" name="name" id="prodName" required value="{{ old('name') }}" placeholder="Contoh: Gaun Sutra Satin Drape Midi" class="chat-search-input" style="width: 100%; font-size: 13.5px; padding: 10px 14px;">
              <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Nama utama yang tampil di etalase toko pelanggan Indonesia</div>
            </div>
            <div>
              <label style="display: block; font-size: 12.5px; font-weight: 600; color: #6B7280; margin-bottom: 6px;">Nama Produk (English - Auto AI)</label>
              <input type="text" name="name_en" id="prodNameEn" value="{{ old('name_en') }}" placeholder="Otomatis diisi terjemahan AI jika dikosongkan" class="chat-search-input" style="width: 100%; font-size: 13.5px; padding: 10px 14px; background: #FAF9F6;">
              <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Tampil saat pelanggan beralih ke bahasa Inggris (EN)</div>
            </div>
          </div>

          <!-- Kategori, SKU, & Badge Promo -->
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
            <div>
              <label style="display: block; font-size: 12.5px; font-weight: 700; color: #374151; margin-bottom: 6px;">Kategori Busana *</label>
              <select name="category_id" required class="chat-search-input" style="width: 100%; font-size: 13.5px; padding: 10px 14px; background: #FFFFFF;">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 6px;">SKU Produk (Opsional)</label>
              <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Auto-generate jika kosong" class="chat-search-input" style="width: 100%; font-size: 13.5px; padding: 10px 14px;">
              <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Kode unik inventaris (misal: SKU-DRS-001)</div>
            </div>
            <div>
              <label style="display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 6px;">Badge Promo (Opsional)</label>
              <input type="text" name="badge" value="{{ old('badge') }}" placeholder="Contoh: Best Seller / -20%" class="chat-search-input" style="width: 100%; font-size: 13.5px; padding: 10px 14px;">
              <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Label promosi di pojok foto produk</div>
            </div>
          </div>
        </div>

        <!-- Card 2: Deskripsi Lengkap & Format Shopee (Word-Like Rich Editor) -->
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 16px;">
          <div style="border-bottom: 1px solid #F3F4F6; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <div>
              <h2 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                2. Deskripsi Produk &amp; Format Dokumen (Word Editor)
              </h2>
              <div style="font-size: 12px; color: #6B7280; margin-top: 2px;">Tulis deskripsi bebas &amp; rapi seperti di Microsoft Word: styling judul, tabel ukuran, poin fitur, dan banner.</div>
            </div>
          </div>

          <!-- Word-Like Rich Text Editor Container -->
          <div class="word-editor-box" style="border: 1px solid #D1D5DB; border-radius: 10px; overflow: hidden; background: #F8F9FA; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">

            <!-- Ribbon Bar Toolbar (Word Style) -->
            <div class="word-ribbon-bar" style="background: #FFFFFF; border-bottom: 1px solid #E5E7EB; padding: 8px 12px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">

              <!-- 1. Undo / Redo -->
              <div style="display: flex; gap: 2px; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <button type="button" onclick="formatDoc('undo')" class="ribbon-btn" title="Undo / Batalkan (Ctrl+Z)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"></path><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path></svg>
                </button>
                <button type="button" onclick="formatDoc('redo')" class="ribbon-btn" title="Redo / Ulangi (Ctrl+Y)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"></path><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3L21 13"></path></svg>
                </button>
              </div>

              <!-- 2. Gaya Teks / Heading Selector -->
              <div style="display: flex; align-items: center; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <select id="headingSelector" onchange="formatHeading(this.value)" style="height: 28px; font-size: 12px; border: 1px solid #D1D5DB; border-radius: 6px; padding: 2px 8px; background: #FAF9F6; font-weight: 600; color: #374151; cursor: pointer;">
                  <option value="p">Normal (Paragraf)</option>
                  <option value="h2">Judul Utama (H2)</option>
                  <option value="h3">Subjudul Bagian (H3)</option>
                  <option value="h4">Poin / Fitur (H4)</option>
                </select>
              </div>

              <!-- 3. Format Font: Bold, Italic, Underline, Strikethrough -->
              <div style="display: flex; gap: 2px; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <button type="button" onclick="formatDoc('bold')" class="ribbon-btn" title="Tebal (Ctrl+B)"><strong>B</strong></button>
                <button type="button" onclick="formatDoc('italic')" class="ribbon-btn" title="Miring (Ctrl+I)"><em>I</em></button>
                <button type="button" onclick="formatDoc('underline')" class="ribbon-btn" title="Garis Bawah (Ctrl+U)"><u>U</u></button>
                <button type="button" onclick="formatDoc('strikeThrough')" class="ribbon-btn" title="Coret (Strikethrough)"><s>S</s></button>
              </div>

              <!-- 4. Warna Teks & Highlight / Stabilo -->
              <div style="display: flex; align-items: center; gap: 4px; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <label title="Warna Teks Huruf" class="ribbon-btn" style="cursor: pointer; position: relative;">
                  <span style="font-weight: 800; border-bottom: 3px solid #A18A68; padding-bottom: 1px; font-size: 12px;">A</span>
                  <input type="color" onchange="formatDoc('foreColor', this.value)" style="opacity: 0; width: 0; height: 0; padding: 0; border: none; position: absolute;" value="#111827">
                </label>
                <label title="Stabilo / Highlight Warna Latar" class="ribbon-btn" style="cursor: pointer; position: relative; background: #FEF9C3;">
                  <span style="font-size: 12px;">🖍</span>
                  <input type="color" onchange="formatDoc('hiliteColor', this.value)" style="opacity: 0; width: 0; height: 0; padding: 0; border: none; position: absolute;" value="#FEF08A">
                </label>
              </div>

              <!-- 5. Perataan Paragraf (Alignment) -->
              <div style="display: flex; gap: 2px; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <button type="button" onclick="formatDoc('justifyLeft')" class="ribbon-btn" title="Rata Kiri">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="17" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="17" y1="18" x2="3" y2="18"></line></svg>
                </button>
                <button type="button" onclick="formatDoc('justifyCenter')" class="ribbon-btn" title="Rata Tengah">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="10" x2="6" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="18" y1="18" x2="6" y2="18"></line></svg>
                </button>
                <button type="button" onclick="formatDoc('justifyRight')" class="ribbon-btn" title="Rata Kanan">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="7" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="7" y2="18"></line></svg>
                </button>
                <button type="button" onclick="formatDoc('justifyFull')" class="ribbon-btn" title="Rata Kanan Kiri (Justify)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                </button>
              </div>

              <!-- 6. Daftar & Indentasi -->
              <div style="display: flex; gap: 2px; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <button type="button" onclick="formatDoc('insertUnorderedList')" class="ribbon-btn" title="Daftar Poin (Bullets)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"></line><line x1="9" y1="12" x2="20" y2="12"></line><line x1="9" y1="18" x2="20" y2="18"></line><circle cx="4" cy="6" r="2"></circle><circle cx="4" cy="12" r="2"></circle><circle cx="4" cy="18" r="2"></circle></svg>
                </button>
                <button type="button" onclick="formatDoc('insertOrderedList')" class="ribbon-btn" title="Daftar Penomoran (1, 2, 3)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4"></path><path d="M4 10h2"></path></svg>
                </button>
                <button type="button" onclick="formatDoc('outdent')" class="ribbon-btn" title="Geser Kiri (Outdent)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="7 8 3 12 7 16"></polyline><line x1="21" y1="12" x2="3" y2="12"></line><line x1="21" y1="6" x2="11" y2="6"></line><line x1="21" y1="18" x2="11" y2="18"></line></svg>
                </button>
                <button type="button" onclick="formatDoc('indent')" class="ribbon-btn" title="Geser Kanan (Indent)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 8 21 12 17 16"></polyline><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="13" y2="6"></line><line x1="3" y1="18" x2="13" y2="18"></line></svg>
                </button>
              </div>

              <!-- 7. Sisipkan (Insert): Tabel, Gambar, Link, Garis -->
              <div style="display: flex; gap: 4px; border-right: 1px solid #E5E7EB; padding-right: 6px;">
                <button type="button" onclick="insertWordTable()" class="ribbon-btn-text" title="Sisipkan Tabel Ukuran / Spesifikasi Kain">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                  Tabel
                </button>
                <button type="button" onclick="insertWordImage()" class="ribbon-btn-text" title="Sisipkan Gambar Banner Infografis">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                  Gambar
                </button>
                <button type="button" onclick="insertWordLink()" class="ribbon-btn" title="Sisipkan Link Tautan">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </button>
                <button type="button" onclick="formatDoc('insertHorizontalRule')" class="ribbon-btn" title="Garis Pembatas (HR)">―</button>
              </div>

              <!-- 8. Template Shopee & Fitur Khusus -->
              <div style="display: flex; gap: 4px; margin-left: auto; align-items: center;">
                <button type="button" onclick="applyWordShopeeTemplate()" class="ribbon-btn-special" title="Terapkan format lengkap template Shopee butik">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                  Template Shopee Siap Pakai
                </button>
                <button type="button" onclick="formatDoc('removeFormat')" class="ribbon-btn" title="Hapus Format (Clear Formatting)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"></path><path d="M9 20h6"></path><line x1="12" y1="4" x2="12" y2="20"></line><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                </button>
                <button type="button" onclick="toggleSourceView()" id="btnToggleSource" class="ribbon-btn-text" title="Beralih antara Tampilan Word dan Kode HTML">
                  &lt;/&gt; HTML
                </button>
              </div>

            </div>

            <!-- Paper Canvas (Lembar Kerja Dokumen Seperti Word) -->
            <div class="word-canvas-wrapper" style="padding: 20px; background: #F1F3F5; min-height: 420px; display: flex; justify-content: center;">
              <div 
                id="wordEditorCanvas" 
                contenteditable="true" 
                class="word-document-paper" 
                data-placeholder="Tulis deskripsi produk bebas &amp; rapi seperti di Microsoft Word (judul, poin fitur, tabel ukuran, dll)..."
                oninput="syncWordEditor()"
                onblur="syncWordEditor()"
                style="background: #FFFFFF; width: 100%; min-height: 380px; padding: 28px 34px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #E5E7EB; border-radius: 4px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; line-height: 1.75; color: #1F2937; outline: none;"
              >{!! old('description', '') !!}</div>

              <!-- Textarea Sumber Kode HTML (tersembunyi secara default) -->
              <textarea 
                name="description" 
                id="hiddenDescription" 
                oninput="syncFromRawHtml(this.value)"
                style="display: none; width: 100%; min-height: 380px; padding: 18px; font-family: Consolas, monospace; font-size: 13px; line-height: 1.5; border: 1px solid #D1D5DB; border-radius: 4px; background: #1E293B; color: #F8FAFC;"
              >{{ old('description') }}</textarea>
            </div>

            <!-- Status Bar Word -->
            <div style="background: #FAF9F6; border-top: 1px solid #E5E7EB; padding: 6px 16px; font-size: 11.5px; color: #6B7280; display: flex; justify-content: space-between; align-items: center;">
              <div id="wordCountStatus">0 Kata &bull; 0 Karakter</div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                <span>Mode Dokumen Word &bull; Siap &bull; Auto-Sync Aktif</span>
              </div>
            </div>

          </div>

          <!-- Deskripsi English (Auto AI) -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 600; color: #6B7280; margin-bottom: 6px;">Deskripsi English (Auto AI Translation / Optional)</label>
            <textarea name="description_en" id="prodDescEn" rows="4" placeholder="Otomatis diisi terjemahan AI jika dikosongkan..." class="chat-search-input" style="width: 100%; font-size: 13px; background: #FAF9F6; line-height: 1.55; padding: 10px 14px;">{{ old('description_en') }}</textarea>
            <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Versi bahasa Inggris untuk pelanggan global</div>
          </div>
        </div>

      </div>

      <!-- ================= KOLOM KANAN (SIDEBAR): HARGA, MEDIA & STOK ================= -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Card 3: Penetapan Harga & Persediaan Stok -->
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 16px;">
          <div style="border-bottom: 1px solid #F3F4F6; padding-bottom: 12px;">
            <h2 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
              3. Harga &amp; Persediaan Stok
            </h2>
          </div>

          <!-- Harga Jual (Rp) -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #111827; margin-bottom: 6px;">Harga Jual (Rp) *</label>
            <div style="position: relative;">
              <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 700; color: #6B7280;">Rp</span>
              <input type="text" name="price" id="prodPrice" required value="{{ old('price', '750.000') }}" oninput="updateLivePricePreview()" class="chat-search-input" style="width: 100%; font-size: 14px; font-weight: 700; padding-left: 36px; padding-top: 10px; padding-bottom: 10px;">
            </div>
            <div id="priceUsdPreview" style="font-size: 11.5px; color: #8C7554; margin-top: 5px; font-weight: 600;">
              ≈ $ 46.88 USD (Otomatis dikonversi AI untuk pembeli global)
            </div>
          </div>

          <!-- Harga Coret / Normal (Rp) -->
          <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #4B5563; margin-bottom: 6px;">Harga Coret / Normal (Rp)</label>
            <div style="position: relative;">
              <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 700; color: #9CA3AF;">Rp</span>
              <input type="text" name="original_price" id="prodOriginalPrice" value="{{ old('original_price', '950.000') }}" oninput="updateLivePricePreview()" placeholder="Opsional diskon coret" class="chat-search-input" style="width: 100%; font-size: 13.5px; padding-left: 36px;">
            </div>
            <div id="origPriceUsdPreview" style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">Harga sebelum promo / coret</div>
          </div>

          <!-- Stok Total Siap Kirim -->
          <div>
            <label style="display: block; font-size: 12.5px; font-weight: 700; color: #111827; margin-bottom: 6px;">Stok Total Siap Kirim (pcs) *</label>
            <input type="number" name="stock" id="prodStock" required min="0" value="{{ old('stock', 20) }}" class="chat-search-input" style="width: 100%; font-size: 14px; font-weight: 700; padding: 10px 14px;">
            <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">Kuantitas stok barang yang tersedia untuk dipesan</div>
          </div>

          <!-- Alokasi Ukuran / Size Stocks -->
          <div style="background: #FAF9F6; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 8px;">Alokasi Ukuran (Size Stocks):</label>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #6B7280; display: block; text-align: center; margin-bottom: 2px;">S</span>
                <input type="number" name="size_stocks[S]" value="{{ old('size_stocks.S', 5) }}" min="0" class="chat-search-input" style="width: 100%; text-align: center; font-size: 12.5px; padding: 6px 4px;">
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #6B7280; display: block; text-align: center; margin-bottom: 2px;">M</span>
                <input type="number" name="size_stocks[M]" value="{{ old('size_stocks.M', 5) }}" min="0" class="chat-search-input" style="width: 100%; text-align: center; font-size: 12.5px; padding: 6px 4px;">
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #6B7280; display: block; text-align: center; margin-bottom: 2px;">L</span>
                <input type="number" name="size_stocks[L]" value="{{ old('size_stocks.L', 5) }}" min="0" class="chat-search-input" style="width: 100%; text-align: center; font-size: 12.5px; padding: 6px 4px;">
              </div>
              <div>
                <span style="font-size: 11px; font-weight: 700; color: #6B7280; display: block; text-align: center; margin-bottom: 2px;">XL</span>
                <input type="number" name="size_stocks[XL]" value="{{ old('size_stocks.XL', 5) }}" min="0" class="chat-search-input" style="width: 100%; text-align: center; font-size: 12.5px; padding: 6px 4px;">
              </div>
            </div>
          </div>

          <!-- Checkbox Featured -->
          <div style="display: flex; align-items: center; gap: 8px; padding-top: 4px;">
            <input type="checkbox" name="is_featured" id="isFeatured" value="1" {{ old('is_featured') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #A18A68; cursor: pointer;">
            <label for="isFeatured" style="font-size: 12.5px; font-weight: 600; color: #374151; cursor: pointer;">Tampilkan di Koleksi Unggulan Butik (Featured)</label>
          </div>
        </div>

        <!-- Card 4: Foto Produk (Upload ke Storage Server) -->
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 14px;">
          <div style="border-bottom: 1px solid #F3F4F6; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
              4. Foto Produk (Storage Server)
            </h2>
            <span style="font-size: 11px; color: #6B7280;">Maks 10MB</span>
          </div>

          <!-- Dropzone Foto -->
          <div>
            <div style="border: 2px dashed #D1D5DB; border-radius: 10px; padding: 22px 14px; text-align: center; background: #FAF9F6; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('imageFile').click()" id="imageDropzone">
              <input type="file" name="image" id="imageFile" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" style="display: none;" onchange="handleImageFileSelect(this)">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2" style="margin: 0 auto 8px; display: block;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
              <div style="font-size: 13.5px; font-weight: 700; color: #111827;">Pilih File Gambar dari Komputer / HP</div>
              <div style="font-size: 11.5px; color: #6B7280; margin-top: 3px;">Mendukung PNG, JPG, JPEG, WEBP</div>
            </div>

            <!-- Preview Foto -->
            <div id="imagePreviewWrap" style="display: none; align-items: center; gap: 12px; margin-top: 10px; padding: 12px; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px;">
              <img id="imagePreviewElement" src="" alt="Preview Foto" style="width: 58px; height: 58px; object-fit: cover; border-radius: 6px; border: 1px solid #86EFAC;">
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 13px; font-weight: 700; color: #166534; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="imageFileName">-</div>
                <div style="font-size: 11px; color: #15803D; margin-top: 2px;">File siap diupload ke storage server butik</div>
              </div>
              <button type="button" onclick="clearSelectedImage()" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">Hapus</button>
            </div>
          </div>

          <!-- Alternatif URL Gambar -->
          <div style="border-top: 1px dashed #E5E7EB; padding-top: 10px;">
            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #4B5563; margin-bottom: 4px;">Atau Tautkan Link URL Gambar Eksternal:</label>
            <input type="text" name="image_url" id="imageUrl" value="{{ old('image_url') }}" placeholder="Contoh: images/product-1.png atau https://..." class="chat-search-input" style="width: 100%; font-size: 12.5px;">
          </div>
        </div>

        <!-- Card 5: Video Produk (Upload ke Storage Server / URL) -->
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 14px;">
          <div style="border-bottom: 1px solid #F3F4F6; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
              5. Video Produk (Storage Server / MP4)
            </h2>
            <span style="font-size: 11px; color: #6B7280;">Maks 50MB</span>
          </div>

          <!-- Dropzone Video -->
          <div>
            <div style="border: 2px dashed #D1D5DB; border-radius: 10px; padding: 22px 14px; text-align: center; background: #FAF9F6; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('videoFile').click()" id="videoDropzone">
              <input type="file" name="video" id="videoFile" accept="video/mp4,video/quicktime,video/webm" style="display: none;" onchange="handleVideoFileSelect(this)">
              <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2" style="margin: 0 auto 8px; display: block;"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
              <div style="font-size: 13.5px; font-weight: 700; color: #111827;">Pilih File Video dari Komputer / HP</div>
              <div style="font-size: 11.5px; color: #6B7280; margin-top: 3px;">Mendukung format MP4, WEBM, MOV</div>
            </div>

            <!-- Preview Video -->
            <div id="videoPreviewWrap" style="display: none; align-items: center; gap: 12px; margin-top: 10px; padding: 12px; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px;">
              <video id="videoPreviewElement" src="" controls playsinline style="width: 86px; height: 54px; object-fit: cover; border-radius: 6px; border: 1px solid #86EFAC; background: #000;"></video>
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 13px; font-weight: 700; color: #166534; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="videoFileName">-</div>
                <div style="font-size: 11px; color: #15803D; margin-top: 2px;">File video siap diupload ke storage server</div>
              </div>
              <button type="button" onclick="clearSelectedVideo()" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer;">Hapus</button>
            </div>
          </div>

          <!-- Alternatif URL Video -->
          <div style="border-top: 1px dashed #E5E7EB; padding-top: 10px;">
            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #4B5563; margin-bottom: 4px;">Atau Tautkan Link URL Video Eksternal:</label>
            <input type="text" name="video_url" id="videoUrl" value="{{ old('video_url') }}" placeholder="Contoh: https://youtube.com/watch?v=... atau https://..." class="chat-search-input" style="width: 100%; font-size: 12.5px;">
          </div>
        </div>

      </div>

    </div>

    <!-- Sticky Bottom Bar -->
    <div style="margin-top: 24px; background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 16px 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div style="font-size: 13px; color: #6B7280; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        Semua data akan diverifikasi dan foto/video disimpan otomatis ke server penyimpanan.
      </div>
      <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.products.index') }}" class="table-action-btn" style="background: #F3F4F6; color: #374151; padding: 10px 20px; font-size: 13px; text-decoration: none; font-weight: 600;">
          Batal
        </a>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 10px 24px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Simpan Produk Baru
        </button>
      </div>
    </div>
  </form>

</div>

<script>
const GLOBAL_EXCHANGE_RATE = {{ $exchangeRate ?? 16000 }};

function updateLivePricePreview() {
  const priceInput = document.getElementById('prodPrice');
  const origInput = document.getElementById('prodOriginalPrice');
  const pricePreview = document.getElementById('priceUsdPreview');
  const origPreview = document.getElementById('origPriceUsdPreview');

  if (priceInput && pricePreview) {
    const raw = (priceInput.value || '').replace(/[^0-9.]/g, '');
    const num = parseFloat(raw);
    if (!isNaN(num) && num > 0) {
      const usd = num >= 1000 ? (num / GLOBAL_EXCHANGE_RATE).toFixed(2) : num.toFixed(2);
      pricePreview.textContent = `≈ $ ${usd} USD (Otomatis dikonversi AI untuk pembeli global)`;
      pricePreview.style.color = '#8C7554';
    } else {
      pricePreview.textContent = 'Masukkan nominal harga dalam Rupiah (Rp)';
      pricePreview.style.color = '#9CA3AF';
    }
  }

  if (origInput && origPreview) {
    const raw = (origInput.value || '').replace(/[^0-9.]/g, '');
    const num = parseFloat(raw);
    if (!isNaN(num) && num > 0) {
      const usd = num >= 1000 ? (num / GLOBAL_EXCHANGE_RATE).toFixed(2) : num.toFixed(2);
      origPreview.textContent = `≈ $ ${usd} USD (Harga Coret)`;
      origPreview.style.color = '#8C7554';
    } else {
      origPreview.textContent = 'Harga sebelum promo / coret';
      origPreview.style.color = '#9CA3AF';
    }
  }
}

// Image File Upload Handlers
function handleImageFileSelect(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
      const wrap = document.getElementById('imagePreviewWrap');
      const img = document.getElementById('imagePreviewElement');
      const nameEl = document.getElementById('imageFileName');
      if (img) img.src = e.target.result;
      if (nameEl) nameEl.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
      if (wrap) wrap.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  }
}

function clearSelectedImage() {
  const input = document.getElementById('imageFile');
  if (input) input.value = '';
  const wrap = document.getElementById('imagePreviewWrap');
  if (wrap) wrap.style.display = 'none';
}

// Video File Upload Handlers
function handleVideoFileSelect(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const wrap = document.getElementById('videoPreviewWrap');
    const player = document.getElementById('videoPreviewElement');
    const nameEl = document.getElementById('videoFileName');
    if (player) {
      player.src = URL.createObjectURL(file);
      player.load();
    }
    if (nameEl) {
      const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
      nameEl.textContent = `${file.name} (${sizeMb} MB)`;
    }
    if (wrap) wrap.style.display = 'flex';
  }
}

function clearSelectedVideo() {
  const input = document.getElementById('videoFile');
  if (input) input.value = '';
  const player = document.getElementById('videoPreviewElement');
  if (player) {
    player.pause();
    player.removeAttribute('src');
    player.load();
  }
  const wrap = document.getElementById('videoPreviewWrap');
  if (wrap) wrap.style.display = 'none';
}

// ================= MICROSOFT WORD-LIKE RICH TEXT EDITOR SUITE =================
function formatDoc(cmd, value = null) {
  document.execCommand(cmd, false, value);
  syncWordEditor();
  const canvas = document.getElementById('wordEditorCanvas');
  if (canvas) canvas.focus();
}

function formatHeading(tag) {
  if (tag === 'p') {
    document.execCommand('formatBlock', false, '<p>');
  } else {
    document.execCommand('formatBlock', false, `<${tag}>`);
  }
  syncWordEditor();
}

function insertWordTable() {
  const rows = parseInt(prompt('Jumlah Baris Tabel (termasuk baris judul/header):', '4') || '4', 10);
  const cols = parseInt(prompt('Jumlah Kolom Tabel:', '4') || '4', 10);
  if (isNaN(rows) || isNaN(cols) || rows < 1 || cols < 1) return;

  let html = '<table style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 1px solid #E5E7EB;"><thead><tr style="background: #FAF7F2;">';
  for (let c = 0; c < cols; c++) {
    html += `<th style="padding: 10px 14px; border: 1px solid #E5E7EB; text-align: left; font-weight: 700; color: #111827;">Kolom ${c + 1}</th>`;
  }
  html += '</tr></thead><tbody>';
  for (let r = 1; r < rows; r++) {
    html += `<tr style="${r % 2 === 0 ? 'background: #FAFAFA;' : ''}">`;
    for (let c = 0; c < cols; c++) {
      html += '<td style="padding: 9px 14px; border: 1px solid #E5E7EB; color: #374151;">Teks isi</td>';
    }
    html += '</tr>';
  }
  html += '</tbody></table><p><br></p>';
  document.execCommand('insertHTML', false, html);
  syncWordEditor();
}

function insertWordImage() {
  const url = prompt('Masukkan URL Gambar Infografis / Banner Produk:', 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=1000&q=80');
  if (!url) return;
  const alt = prompt('Keterangan Gambar (Alt text / Deskripsi):', 'Infografis Detail Produk') || 'Banner Produk';
  const html = `<div style="text-align: center; margin: 18px 0;"><img src="${url}" alt="${alt}" style="max-width: 100%; height: auto; border-radius: 8px; display: inline-block;"><div style="font-size: 11.5px; color: #6B7280; margin-top: 5px;"><em>${alt}</em></div></div><p><br></p>`;
  document.execCommand('insertHTML', false, html);
  syncWordEditor();
}

function insertWordLink() {
  const url = prompt('Masukkan URL Link Tautan:', 'https://');
  if (url) {
    formatDoc('createLink', url);
  }
}

function applyWordShopeeTemplate() {
  if (!confirm('Terapkan template format lengkap butik Shopee? Ini akan memasukkan susunan standar dengan fitur utama, tabel ukuran, dan spesifikasi.')) {
    return;
  }
  const canvas = document.getElementById('wordEditorCanvas');
  const template = `<h2>Koleksi Busana Eksklusif Butik — Anggun &amp; Berkelas</h2>
<p>Dirancang istimewa untuk menghadirkan kenyamanan dan keanggunan sejati sepanjang hari. Menggunakan bahan pilihan berkarakter sejuk dan jatuh menawan, busana ini menjadi pilihan sempurna untuk melengkapi momen spesial Anda.</p>

<h3>Keunggulan &amp; Fitur Utama:</h3>
<ul>
  <li><strong>Material Premium Grade A:</strong> Tekstur sejuk, halus di kulit, tidak menerawang, dan menyerap keringat.</li>
  <li><strong>Jahitan Standar Butik:</strong> Presisi kerapian ganda untuk ketahanan maksimal pemakaian jangka panjang.</li>
  <li><strong>Potongan Siluet Anggun:</strong> Menyesuaikan bentuk tubuh dengan proporsional untuk penampilan percaya diri.</li>
  <li><strong>Warna Eksklusif &amp; Tahan Lama:</strong> Menggunakan teknik pewarnaan modern yang tidak mudah luntur.</li>
</ul>

<h3>Panduan Ukuran (Size Chart):</h3>
<table style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 1px solid #E5E7EB;">
  <thead>
    <tr style="background: #FAF7F2;">
      <th style="padding: 10px 14px; border: 1px solid #E5E7EB; text-align: left; font-weight: 700; color: #111827;">Ukuran</th>
      <th style="padding: 10px 14px; border: 1px solid #E5E7EB; text-align: left; font-weight: 700; color: #111827;">Lingkar Dada (LD)</th>
      <th style="padding: 10px 14px; border: 1px solid #E5E7EB; text-align: left; font-weight: 700; color: #111827;">Panjang Baju (PB)</th>
      <th style="padding: 10px 14px; border: 1px solid #E5E7EB; text-align: left; font-weight: 700; color: #111827;">Lebar Bahu</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB; font-weight: 600;">S</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">92 - 94 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">135 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">37 cm</td>
    </tr>
    <tr style="background: #FAFAFA;">
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB; font-weight: 600;">M</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">96 - 98 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">137 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">38 cm</td>
    </tr>
    <tr>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB; font-weight: 600;">L</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">102 - 104 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">140 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">40 cm</td>
    </tr>
    <tr style="background: #FAFAFA;">
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB; font-weight: 600;">XL</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">108 - 110 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">140 cm</td>
      <td style="padding: 9px 14px; border: 1px solid #E5E7EB;">42 cm</td>
    </tr>
  </tbody>
</table>

<h3>Spesifikasi &amp; Petunjuk Perawatan:</h3>
<ul>
  <li><strong>Komposisi Bahan:</strong> Silk Blend Satin Premium / Katun Linen Organik</li>
  <li><strong>Karakter Bahan:</strong> Ringan, jatuh berkelas, dan breathable</li>
  <li><strong>Petunjuk Cuci:</strong> Dianjurkan cuci dengan tangan atau putaran pakaian halus</li>
  <li><strong>Penyetrikaan:</strong> Gunakan setrika bersuhu rendah hingga sedang atau gunakan steamer pakaian</li>
</ul>

<div style="text-align: center; margin: 20px 0;">
  <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="Koleksi Busana Eksklusif Butik" style="max-width: 100%; border-radius: 8px;">
</div>
<p><br></p>`;

  canvas.innerHTML = template;
  syncWordEditor();
}

function toggleSourceView() {
  const canvas = document.getElementById('wordEditorCanvas');
  const raw = document.getElementById('hiddenDescription');
  const btn = document.getElementById('btnToggleSource');
  if (!canvas || !raw) return;

  if (raw.style.display === 'none') {
    raw.value = canvas.innerHTML;
    canvas.style.display = 'none';
    raw.style.display = 'block';
    if (btn) {
      btn.style.background = '#1E293B';
      btn.style.color = '#FFFFFF';
      btn.style.borderColor = '#0F172A';
    }
  } else {
    canvas.innerHTML = raw.value;
    raw.style.display = 'none';
    canvas.style.display = 'block';
    if (btn) {
      btn.style.background = '#FFFFFF';
      btn.style.color = '#374151';
      btn.style.borderColor = '#D1D5DB';
    }
    syncWordEditor();
  }
}

function syncWordEditor() {
  const canvas = document.getElementById('wordEditorCanvas');
  const raw = document.getElementById('hiddenDescription');
  const status = document.getElementById('wordCountStatus');
  if (!canvas || !raw) return;

  if (canvas.style.display !== 'none') {
    raw.value = canvas.innerHTML;
  }

  const text = (canvas.innerText || '').trim();
  const words = text ? text.split(/\s+/).length : 0;
  const chars = text.length;

  if (status) {
    status.innerHTML = `<strong>${words}</strong> Kata &bull; <strong>${chars}</strong> Karakter`;
  }
}

function syncFromRawHtml(val) {
  const canvas = document.getElementById('wordEditorCanvas');
  if (canvas) {
    canvas.innerHTML = val;
  }
  const text = (canvas ? canvas.innerText || '' : '').trim();
  const words = text ? text.split(/\s+/).length : 0;
  const chars = text.length;
  const status = document.getElementById('wordCountStatus');
  if (status) {
    status.innerHTML = `<strong>${words}</strong> Kata &bull; <strong>${chars}</strong> Karakter`;
  }
}

// AI Auto Translate Helper
async function triggerAiTranslate() {
  const nameInput = document.getElementById('prodName');
  const canvas = document.getElementById('wordEditorCanvas');
  const rawDesc = document.getElementById('hiddenDescription');
  const nameEnInput = document.getElementById('prodNameEn');
  const descEnInput = document.getElementById('prodDescEn');
  const btn = document.getElementById('btnAutoTranslate');

  const nameVal = nameInput ? nameInput.value.trim() : '';
  syncWordEditor();
  const descVal = rawDesc ? rawDesc.value.trim() : (canvas ? canvas.innerText.trim() : '');

  if (!nameVal) {
    alert('Harap isi Nama Produk dalam Bahasa Indonesia terlebih dahulu.');
    if (nameInput) nameInput.focus();
    return;
  }

  const originalBtnText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '⏳ Menerjemahkan...';

  try {
    const res = await fetch("{{ route('admin.products.auto_translate') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: nameVal,
        description: descVal
      })
    });

    const data = await res.json();
    if (data.status === 'success') {
      if (nameEnInput) nameEnInput.value = data.name_en || '';
      if (descEnInput) descEnInput.value = data.description_en || '';
    } else {
      alert('Gagal menerjemahkan: ' + (data.message || 'Terjadi kesalahan'));
    }
  } catch (err) {
    console.error('Translation error:', err);
    alert('Koneksi terjemahan AI gagal. Silakan coba kembali.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalBtnText;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateLivePricePreview();
  syncWordEditor();

  const form = document.getElementById('createProductForm');
  if (form) {
    form.addEventListener('submit', function() {
      syncWordEditor();
    });
  }
});
</script>
@endsection
