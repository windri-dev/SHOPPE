@extends('layouts.admin')

@section('title', 'Katalog & Manajemen Stok Produk - PUTRI.J Admin')
@section('page_title', 'Katalog & Persediaan Produk')

@section('content')
<div class="admin-products-page" style="display: flex; flex-direction: column; gap: 20px;">
  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
        Katalog &amp; Manajemen Persediaan Produk
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Kelola 46 perhiasan, tambah perhiasan baru, sesuaikan stok masuk/keluar, dan perbarui harga jual etalase.
      </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
      <!-- Button Tambah Produk Baru -->
      <button
        type="button"
        onclick="openCreateProductModal()"
        class="table-action-btn"
        style="background: #111827; color: #FFFFFF; padding: 8px 16px; font-size: 12.5px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Produk Baru
      </button>

      <a href="{{ route('admin.categories.index') }}" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"></path></svg>
        Kelola Kategori
      </a>

      <a href="{{ route('shop.index') }}" target="_blank" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        Etalase Toko
      </a>
    </div>
  </div>

  <!-- Stats Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #111827;">
      <span style="font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em;">Total Katalog</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ number_format($stats['total']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Busana terdaftar</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #10B981;">
      <span style="font-size: 12px; font-weight: 600; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">Stok Aman</span>
      <div style="font-size: 26px; font-weight: 700; color: #10B981; margin-top: 4px;">{{ number_format($stats['instock']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Siap dipesan pembeli (> 3 pcs)</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #F59E0B;">
      <span style="font-size: 12px; font-weight: 600; color: #B45309; text-transform: uppercase; letter-spacing: 0.05em;">Stok Kritis / Menipis</span>
      <div style="font-size: 26px; font-weight: 700; color: #F59E0B; margin-top: 4px;">{{ number_format($stats['lowstock']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Tersisa 1 - 3 pcs</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #EF4444;">
      <span style="font-size: 12px; font-weight: 600; color: #B91C1C; text-transform: uppercase; letter-spacing: 0.05em;">Habis (Sold Out)</span>
      <div style="font-size: 26px; font-weight: 700; color: #EF4444; margin-top: 4px;">{{ number_format($stats['outofstock']) }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Butuh restock produksi</div>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="admin-card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <!-- Quick Stock Filter Pills -->
      <div style="display: flex; gap: 6px; overflow-x: auto; flex-wrap: wrap;">
        <a href="{{ route('admin.products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => ''])) }}"
           class="chat-filter-pill {{ !request('stock_status') ? 'active' : '' }}" style="text-decoration: none;">
          Semua ({{ $stats['total'] }})
        </a>
        <a href="{{ route('admin.products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => 'instock'])) }}"
           class="chat-filter-pill {{ request('stock_status') === 'instock' ? 'active' : '' }}" style="text-decoration: none;">
          Tersedia ({{ $stats['instock'] }})
        </a>
        <a href="{{ route('admin.products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => 'lowstock'])) }}"
           class="chat-filter-pill {{ request('stock_status') === 'lowstock' ? 'active' : '' }}" style="text-decoration: none;">
          Menipis ({{ $stats['lowstock'] }})
        </a>
        <a href="{{ route('admin.products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => 'outofstock'])) }}"
           class="chat-filter-pill {{ request('stock_status') === 'outofstock' ? 'active' : '' }}" style="text-decoration: none;">
          Habis ({{ $stats['outofstock'] }})
        </a>
      </div>

      <!-- Category & Search Form -->
      <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        @if(request('stock_status'))
          <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
        @endif

        <select name="category" onchange="this.form.submit()" class="chat-search-input" style="padding: 6px 12px; width: auto; font-size: 13px; background: #fff;">
          <option value="all">Semua Kategori ({{ $stats['total'] }})</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
              {{ $cat->name }} ({{ $cat->products_count }})
            </option>
          @endforeach
        </select>

        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari SKU, Nama Busana..."
          class="chat-search-input"
          style="padding-left: 12px; width: 220px;"
        >
        <button type="submit" class="table-action-btn" style="background: #1B1B18; color: #fff; padding: 6px 14px;">
          Filter
        </button>
        @if(request('search') || (request('category') && request('category') !== 'all') || request('stock_status'))
          <a href="{{ route('admin.products.index') }}" class="table-action-btn" style="background: #E5E7EB; color: #374151; padding: 6px 10px; text-decoration: none;">
            Reset
          </a>
        @endif
      </form>
    </div>
  </div>

  <!-- Products Data Table -->
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 60px;">Foto</th>
            <th>Busana &amp; SKU</th>
            <th>Kategori</th>
            <th>Harga Normal</th>
            <th>Terjual</th>
            <th>Status Stok</th>
            <th style="text-align: right; width: 240px;">Kelola Stok &amp; Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td>
                <div style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: #F3F4F6; border: 1px solid #E5E7EB;">
                  <img
                    src="{{ $product->first_image_url }}"
                    alt="{{ $product->name }}"
                    style="width: 100%; height: 100%; object-fit: cover;"
                    loading="lazy"
                  >
                </div>
              </td>
              <td>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                  <a href="{{ route('shop.show', $product->slug) }}" target="_blank" style="font-weight: 600; color: #111827; text-decoration: none; font-size: 14px;" class="hover:underline">
                    {{ $product->name }}
                  </a>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-family: monospace; font-size: 11px; color: #6B7280; background: #F3F4F6; padding: 1px 6px; border-radius: 4px;">
                      {{ $product->sku ?? 'SKU-NONE' }}
                    </span>
                    @if($product->badge)
                      <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 1px 6px; border-radius: 4px; background: {{ str_contains(strtolower($product->badge), 'sale') || str_contains(strtolower($product->badge), 'off') ? '#FEE2E2' : '#FEF3C7' }}; color: {{ str_contains(strtolower($product->badge), 'sale') || str_contains(strtolower($product->badge), 'off') ? '#DC2626' : '#D97706' }};">
                        {{ $product->badge }}
                      </span>
                    @endif
                    @if($product->hasVideo())
                      <span style="font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 4px; background: #111827; color: #A18A68; display: inline-flex; align-items: center; gap: 3px;">
                        ▶ Video
                      </span>
                    @endif
                  </div>
                </div>
              </td>
              <td>
                <span style="font-size: 13px; color: #4B5563;">
                  {{ $product->category->name ?? '-' }}
                </span>
              </td>
              <td>
                <div style="font-weight: 600; color: #111827; font-size: 14px;">
                  {{ $product->price_formatted }}
                </div>
                @if($product->original_price && $product->original_price > $product->price)
                  <div style="font-size: 11px; text-decoration: line-through; color: #9CA3AF;">
                    {{ $product->original_price_formatted }}
                  </div>
                @endif
              </td>
              <td>
                <div style="font-size: 13px; font-weight: 600; color: #374151;">
                  {{ $product->sales_count ?? 0 }} <span style="font-size: 11px; font-weight: 400; color: #6B7280;">pcs</span>
                </div>
              </td>
              <td>
                @if($product->stock <= 0)
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #FEE2E2; color: #DC2626;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span>
                    Habis (0)
                  </span>
                @elseif($product->stock <= 3)
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #FEF3C7; color: #D97706;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                    Sisa {{ $product->stock }} pcs
                  </span>
                @else
                  <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #D1FAE5; color: #059669;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                    {{ $product->stock }} pcs
                  </span>
                @endif
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 5px; align-items: center; justify-content: flex-end;">
                  <!-- Tombol Atur / Sesuaikan Stok -->
                  <button
                    type="button"
                    onclick="openAdjustStockModal('{{ $product->id }}', '{{ addslashes($product->name) }}', '{{ $product->stock }}')"
                    class="table-action-btn"
                    style="background: #111827; color: #FFFFFF; padding: 5px 9px; font-size: 11px; font-weight: 600;"
                    title="Tambah atau kurangi stok item ini"
                  >
                    ± Stok
                  </button>

                  <!-- Tombol Edit Produk -->
                  <button
                    type="button"
                    onclick="openEditProductModal('{{ $product->id }}', '{{ addslashes($product->name) }}', '{{ $product->sku }}', '{{ $product->category_id }}', '{{ $product->price }}', '{{ $product->original_price ?? '' }}', '{{ $product->stock }}', '{{ addslashes($product->badge ?? '') }}', '{{ addslashes($product->description ?? '') }}', '{{ addslashes($product->video_url ?? '') }}')"
                    class="table-action-btn"
                    style="background: #F3F4F6; color: #374151; padding: 5px 9px; font-size: 11px;"
                    title="Edit detail produk & harga"
                  >
                    Edit
                  </button>

                  <!-- Toggle Cepat (Habis <-> Restock 15) -->
                  <form action="{{ route('admin.products.toggle_stock', $product) }}" method="POST" style="margin: 0;">
                    @csrf
                    @if($product->stock > 0)
                      <button
                        type="submit"
                        onclick="return confirm('Tandai produk ini sebagai Habis (Stok 0)?')"
                        class="table-action-btn"
                        style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 5px 7px; font-size: 11px;"
                        title="Set stok ke 0"
                      >
                        Habis
                      </button>
                    @else
                      <button
                        type="submit"
                        class="table-action-btn"
                        style="background: #D1FAE5; color: #047857; border: 1px solid #A7F3D0; padding: 5px 7px; font-size: 11px; font-weight: 600;"
                        title="Restock 15 pcs"
                      >
                        +15
                      </button>
                    @endif
                  </form>

                  <!-- Hapus Produk -->
                  <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ addslashes($product->name) }}?');">
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="table-action-btn"
                      style="background: #FFFFFF; color: #9CA3AF; border: 1px solid #E5E7EB; padding: 5px 7px; font-size: 11px;"
                      title="Hapus atau arsipkan produk"
                    >
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>

                  <!-- Link ke Etalase -->
                  <a
                    href="{{ route('shop.show', $product->slug) }}"
                    target="_blank"
                    class="table-action-btn"
                    style="background: #FAF9F6; color: #374151; padding: 5px 7px; text-decoration: none;"
                    title="Lihat halaman toko"
                  >
                    &nearr;
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 48px 24px; color: #9CA3AF;">
                <div style="font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 4px;">Tidak ada perhiasan yang sesuai filter</div>
                <div style="font-size: 13px;">Silakan atur ulang pencarian atau filter status stok Anda.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid #E5E7EB; background: #FAF9F6; display: flex; justify-content: center;">
        {{ $products->links('partials.pagination') }}
      </div>
    @endif
  </div>
</div>

<!-- ================= MODAL 1: TAMBAH PRODUK BARU ================= -->
<div id="createProductModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 580px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Tambah Produk Busana Baru</h3>
      <button type="button" onclick="closeCreateProductModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" style="padding: 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Busana *</label>
        <input type="text" name="name" required placeholder="Contoh: Aurelia Silk Bias Cut Midi Dress" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kategori *</label>
          <select name="category_id" required class="chat-search-input" style="width: 100%; font-size: 13px; background: #fff;">
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">SKU (Opsional)</label>
          <input type="text" name="sku" placeholder="Auto-generate jika kosong" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Harga ($) *</label>
          <input type="number" step="0.01" name="price" required placeholder="45.00" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Harga Coret ($)</label>
          <input type="number" step="0.01" name="original_price" placeholder="60.00" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Stok Awal (pcs) *</label>
          <input type="number" name="stock" required value="15" min="0" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Badge Promo (Opsional)</label>
          <input type="text" name="badge" placeholder="Contoh: Best Seller / -20%" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">URL Gambar (Opsional)</label>
          <input type="text" name="image_url" placeholder="images/product-1.png" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">URL Video Produk (Opsional - MP4 / YouTube / Vimeo)</label>
        <input type="text" name="video_url" placeholder="Contoh: https://domain.com/video.mp4 atau https://youtube.com/watch?v=..." class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Deskripsi Busana &amp; Material</label>
        <textarea name="description" rows="3" placeholder="Deskripsi potongan, bahan kain, ukuran, dan saran styling..." class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeCreateProductModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Simpan Produk</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= MODAL 2: SESUAIKAN STOK BARANG ================= -->
<div id="adjustStockModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 440px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Penyesuaian Stok Persediaan</h3>
      <button type="button" onclick="closeAdjustStockModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="adjustStockForm" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      <div>
        <div style="font-size: 12px; color: #6B7280;">Nama Produk:</div>
        <div style="font-weight: 700; font-size: 15px; color: #111827; margin-top: 2px;" id="adjustStockProductName">-</div>
        <div style="font-size: 12px; color: #047857; margin-top: 2px;">Stok saat ini: <strong id="adjustStockCurrentQty">0</strong> pcs</div>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Jenis Penyesuaian *</label>
        <select name="adjustment_type" id="adjustStockType" class="chat-search-input" style="width: 100%; font-size: 13px; background: #fff;">
          <option value="add">+ Tambah Stok Masuk (Restock Produksi / Kiriman Supplier)</option>
          <option value="subtract">- Kurangi Stok (Barang Cacat / Rusak / Hilang)</option>
          <option value="set">= Atur Jumlah Pasti (Hasil Rekonsiliasi Stok Opname)</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Jumlah Quantity (pcs) *</label>
        <input type="number" name="quantity" min="0" required value="10" class="chat-search-input" style="width: 100%; font-size: 14px; font-weight: 700;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Alasan / Catatan Penyesuaian (Opsional)</label>
        <input type="text" name="reason" placeholder="Contoh: Batch Restock Pengrajin Emas 18K" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeAdjustStockModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #10B981; color: #FFFFFF; padding: 8px 18px; font-weight: 600; border: none;">Simpan Penyesuaian</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= MODAL 3: EDIT PRODUK ================= -->
<div id="editProductModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 580px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Edit Detail Busana &amp; Harga</h3>
      <button type="button" onclick="closeEditProductModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="editProductForm" method="POST" style="padding: 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      @method('PUT')

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Busana *</label>
        <input type="text" name="name" id="editProductName" required class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kategori *</label>
          <select name="category_id" id="editProductCategory" required class="chat-search-input" style="width: 100%; font-size: 13px; background: #fff;">
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">SKU *</label>
          <input type="text" name="sku" id="editProductSku" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Harga ($) *</label>
          <input type="number" step="0.01" name="price" id="editProductPrice" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Harga Coret ($)</label>
          <input type="number" step="0.01" name="original_price" id="editProductOriginalPrice" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Stok (pcs) *</label>
          <input type="number" name="stock" id="editProductStock" required min="0" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Badge Promo (Opsional)</label>
        <input type="text" name="badge" id="editProductBadge" placeholder="Contoh: Bestseller / -20% Off" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">URL Video Produk (Opsional - MP4 / YouTube / Vimeo)</label>
        <input type="text" name="video_url" id="editProductVideoUrl" placeholder="Contoh: https://domain.com/video.mp4 atau https://youtube.com/watch?v=..." class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Deskripsi &amp; Detail Kain</label>
        <textarea name="description" id="editProductDesc" rows="3" class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeEditProductModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
// Modal Tambah Produk
function openCreateProductModal() {
  document.getElementById('createProductModalBackdrop').style.display = 'flex';
}
function closeCreateProductModal() {
  document.getElementById('createProductModalBackdrop').style.display = 'none';
}

// Modal Sesuaikan Stok
function openAdjustStockModal(id, name, currentStock) {
  document.getElementById('adjustStockProductName').textContent = name;
  document.getElementById('adjustStockCurrentQty').textContent = currentStock;
  document.getElementById('adjustStockForm').action = '/admin/products/' + id + '/adjust-stock';
  document.getElementById('adjustStockModalBackdrop').style.display = 'flex';
}
function closeAdjustStockModal() {
  document.getElementById('adjustStockModalBackdrop').style.display = 'none';
}

// Modal Edit Produk
function openEditProductModal(id, name, sku, categoryId, price, originalPrice, stock, badge, desc, videoUrl) {
  document.getElementById('editProductForm').action = '/admin/products/' + id;
  document.getElementById('editProductName').value = name;
  document.getElementById('editProductSku').value = sku;
  document.getElementById('editProductCategory').value = categoryId;
  document.getElementById('editProductPrice').value = price;
  document.getElementById('editProductOriginalPrice').value = originalPrice || '';
  document.getElementById('editProductStock').value = stock;
  document.getElementById('editProductBadge').value = badge || '';
  document.getElementById('editProductVideoUrl').value = videoUrl || '';
  document.getElementById('editProductDesc').value = desc || '';
  document.getElementById('editProductModalBackdrop').style.display = 'flex';
}
function closeEditProductModal() {
  document.getElementById('editProductModalBackdrop').style.display = 'none';
}
</script>
@endsection
