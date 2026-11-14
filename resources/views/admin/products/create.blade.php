@extends('layouts.admin')

@section('title', 'Tambah Produk Baru - PUTRIJALVA Admin')
@section('page_title', 'Tambah Produk Baru')

@section('content')
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

        <!-- Card 2: Deskripsi Lengkap & Format Shopee -->
        <div style="background: #FFFFFF; border-radius: 12px; border: 1px solid #E5E7EB; padding: 22px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 16px;">
          <div style="border-bottom: 1px solid #F3F4F6; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <div>
              <h2 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                2. Deskripsi Lengkap &amp; Format Shopee Butik (Indonesia)
              </h2>
              <div style="font-size: 12px; color: #6B7280; margin-top: 2px;">Mendukung teks terstruktur, poin-poin fitur, spesifikasi kain, dan banner infografis.</div>
            </div>
          </div>

          <!-- Formatting Toolbar Shopee -->
          <div style="display: flex; gap: 6px; flex-wrap: wrap; background: #FAF9F6; border: 1px solid #D1D5DB; border-bottom: none; border-top-left-radius: 8px; border-top-right-radius: 8px; padding: 8px 12px; align-items: center;">
            <button type="button" onclick="insertDescTag('prodDesc', '<strong>', '</strong>')" class="table-action-btn" style="padding: 4px 10px; font-size: 12px; background: #fff;" title="Tebal (Bold)"><strong>B</strong></button>
            <button type="button" onclick="insertDescTag('prodDesc', '<h4>', '</h4>')" class="table-action-btn" style="padding: 4px 10px; font-size: 12px; background: #fff;" title="Subjudul (H4)">H4</button>
            <button type="button" onclick="insertDescList('prodDesc')" class="table-action-btn" style="padding: 4px 10px; font-size: 12px; background: #fff;" title="Sisipkan Daftar Fitur Utama">• Fitur List</button>
            <button type="button" onclick="insertDescImage('prodDesc')" class="table-action-btn" style="padding: 4px 10px; font-size: 12px; background: #fff; display: inline-flex; align-items: center; gap: 4px;" title="Sisipkan Banner/Gambar Infografis">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
              Gambar Banner
            </button>
            <button type="button" onclick="applyShopeeTemplate('prodDesc')" class="table-action-btn" style="padding: 4px 12px; font-size: 12px; background: #FAF7F2; border-color: #EFE8DC; color: #8C7554; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;" title="Terapkan format lengkap template Shopee">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
              Template Shopee Lengkap
            </button>
            <button type="button" onclick="toggleDescPreview('prodDesc', 'prodDescPreview')" class="table-action-btn" style="padding: 4px 12px; font-size: 12px; background: #fff; margin-left: auto; display: inline-flex; align-items: center; gap: 6px;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              Toggle Preview
            </button>
          </div>

          <div>
            <textarea name="description" id="prodDesc" rows="9" placeholder="Tuliskan deskripsi lengkap, keunggulan bahan, cara perawatan, atau klik tombol [Template Shopee Lengkap] di atas..." class="chat-search-input" style="width: 100%; font-size: 13.5px; border-top-left-radius: 0; border-top-right-radius: 0; line-height: 1.6; font-family: inherit; padding: 12px 14px;">{{ old('description') }}</textarea>
            <div id="prodDescPreview" style="display: none; padding: 16px; border: 1px solid #D1D5DB; border-top: none; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; background: #FFFFFF; max-height: 280px; overflow-y: auto; font-size: 13.5px; line-height: 1.65;"></div>
          </div>

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

// Rich Description Formatting Helpers
function insertDescTag(textareaId, openTag, closeTag) {
  const el = document.getElementById(textareaId);
  if (!el) return;
  const start = el.selectionStart;
  const end = el.selectionEnd;
  const val = el.value;
  const selectedText = val.substring(start, end) || 'Teks Tebal';
  el.value = val.substring(0, start) + openTag + selectedText + closeTag + val.substring(end);
  el.focus();
  el.setSelectionRange(start + openTag.length, start + openTag.length + selectedText.length);
}

function insertDescList(textareaId) {
  const el = document.getElementById(textareaId);
  if (!el) return;
  const snippet = "\nFitur Utama:\n• Material Berkualitas: Nyaman, lembut, dan sejuk di kulit\n• Desain Elegan: Cocok untuk acara formal maupun santai\n• Jahitan Rapi & Presisi: Awet dan tahan lama\n• Perawatan Mudah: Cepat kering dan tidak mudah kusut\n";
  const start = el.selectionStart;
  const val = el.value;
  el.value = val.substring(0, start) + snippet + val.substring(start);
  el.focus();
}

function insertDescImage(textareaId) {
  const url = prompt('Masukkan URL Gambar Infografis / Banner Produk:', 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=1000&q=80');
  if (!url) return;
  const alt = prompt('Keterangan Gambar (Opsional):', 'Infografis Keunggulan Produk') || 'Banner Produk';
  const imgHtml = `\n<div class="desc-banner-img"><img src="${url}" alt="${alt}"></div>\n`;
  const el = document.getElementById(textareaId);
  if (!el) return;
  const start = el.selectionStart;
  const val = el.value;
  el.value = val.substring(0, start) + imgHtml + val.substring(start);
  el.focus();
}

function applyShopeeTemplate(textareaId) {
  if (!confirm('Terapkan template format Shopee lengkap (Fitur Utama, Spesifikasi, & Banner)? Ini akan melengkapi atau mengganti teks yang ada.')) {
    return;
  }
  const template = `Koleksi Eksklusif & Elegan — Pilihan Terbaik untuk Tampil Anggun Percaya Diri

Dirancang khusus dengan potongan presisi tinggi yang memberikan kenyamanan maksimal sepanjang hari. Menggunakan material grade premium berkarakter sejuk dan jatuh rapi, menjadikannya pilihan sempurna untuk melengkapi gaya berkelas Anda.

Fitur Utama:
• Material Premium Berkualitas: Tekstur lembut, bernapas sejuk, dan aman untuk kulit sensitif
• Siluet Presisi & Fleksibel: Menyesuaikan bentuk tubuh dengan proporsional dan anggun
• Jahitan Standar Butik: Kerapian ganda untuk ketahanan maksimal penggunaan jangka panjang
• Warna Elegan Tahan Lama: Pigmen warna pekat dan tidak mudah pudar walau dicuci berulang

Spesifikasi Produk:
• Material: Silk Blend Satin Premium / Linen Organik
• Potongan: Tailored Relaxed Fit
• Pilihan Ukuran: S, M, L, XL
• Petunjuk Perawatan: Cuci lembut dengan air dingin, setrika suhu sedang

<div class="desc-banner-img">
  <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80" alt="Keunggulan Material & Koleksi Eksklusif">
</div>`;
  const el = document.getElementById(textareaId);
  if (!el) return;
  el.value = template;
  el.focus();
}

function toggleDescPreview(textareaId, previewId) {
  const ta = document.getElementById(textareaId);
  const prev = document.getElementById(previewId);
  if (!ta || !prev) return;

  if (prev.style.display === 'none') {
    let content = ta.value.trim();
    if (!content) {
      prev.innerHTML = '<p style="color: #9CA3AF; margin: 0; font-style: italic;">Belum ada deskripsi yang ditulis...</p>';
    } else {
      const hasHtml = /<[a-z][\s\S]*>/i.test(content);
      if (hasHtml) {
        prev.innerHTML = content;
      } else {
        prev.innerHTML = content.replace(/\n/g, '<br>');
      }
    }
    prev.style.display = 'block';
  } else {
    prev.style.display = 'none';
  }
}

// AI Auto Translate Helper
async function triggerAiTranslate() {
  const nameInput = document.getElementById('prodName');
  const descInput = document.getElementById('prodDesc');
  const nameEnInput = document.getElementById('prodNameEn');
  const descEnInput = document.getElementById('prodDescEn');
  const btn = document.getElementById('btnAutoTranslate');

  const nameVal = nameInput ? nameInput.value.trim() : '';
  const descVal = descInput ? descInput.value.trim() : '';

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
});
</script>
@endsection
