@extends('layouts.admin')

@section('title', 'Kategori Produk - PUTRI.J Admin')
@section('page_title', 'Manajemen Kategori Produk')

@section('content')
<div class="admin-categories-page" style="display: flex; flex-direction: column; gap: 20px;">
  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><path d="M4 6h16M4 12h16M4 18h7"></path></svg>
        Klasifikasi &amp; Manajemen Kategori Perhiasan
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Sesuaikan struktur kategori toko, perbarui nama &amp; slug URL, serta pantau persediaan perhiasan di tiap koleksi.
      </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
      <button
        type="button"
        onclick="openCreateCategoryModal()"
        class="table-action-btn"
        style="background: #111827; color: #FFFFFF; padding: 8px 16px; font-size: 12.5px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Kategori Baru
      </button>

      <a href="{{ route('admin.products.index') }}" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151;">
        Katalog Produk &rarr;
      </a>
    </div>
  </div>

  <!-- Stats Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #A18A68;">
      <span style="font-size: 12px; font-weight: 600; color: #8F7655; text-transform: uppercase; letter-spacing: 0.05em;">Total Kategori</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ $stats['total_categories'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Grup koleksi perhiasan</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #10B981;">
      <span style="font-size: 12px; font-weight: 600; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">Kategori Aktif</span>
      <div style="font-size: 26px; font-weight: 700; color: #10B981; margin-top: 4px;">{{ $stats['active_categories'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Berisi minimal 1 produk</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #111827;">
      <span style="font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em;">Total Produk Terhubung</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ $stats['total_products'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Item terklasifikasi</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #F59E0B;">
      <span style="font-size: 12px; font-weight: 600; color: #B45309; text-transform: uppercase; letter-spacing: 0.05em;">Kategori Kosong</span>
      <div style="font-size: 26px; font-weight: 700; color: #F59E0B; margin-top: 4px;">{{ $stats['empty_categories'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Belum ada item katalog</div>
    </div>
  </div>

  <!-- Search & Toolbar -->
  <div class="admin-card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <div style="font-size: 14px; font-weight: 600; color: #374151;">
        Menampilkan {{ $categories->total() }} Kategori Toko
      </div>

      <!-- Search Box -->
      <form action="{{ route('admin.categories.index') }}" method="GET" style="display: flex; gap: 8px; min-width: 260px;">
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari Nama Kategori, Slug..."
          class="chat-search-input"
          style="padding-left: 12px; width: 260px;"
        >
        <button type="submit" class="table-action-btn" style="background: #1B1B18; color: #fff; padding: 6px 14px;">
          Cari
        </button>
        @if(request('search'))
          <a href="{{ route('admin.categories.index') }}" class="table-action-btn" style="background: #E5E7EB; color: #374151; padding: 6px 10px; text-decoration: none;">
            Reset
          </a>
        @endif
      </form>
    </div>
  </div>

  <!-- Categories Data Table -->
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 240px;">Nama Kategori</th>
            <th>Slug URL</th>
            <th>Deskripsi Singkat</th>
            <th>Jumlah Produk</th>
            <th>Tanggal Dibuat</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td>
                <div style="font-weight: 700; color: #111827; font-size: 14px;">
                  {{ $category->name }}
                </div>
              </td>
              <td>
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" target="_blank" style="font-family: monospace; font-size: 12px; color: #2563EB; background: #EFF6FF; padding: 2px 7px; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="Lihat produk kategori ini di etalase">
                  <span>/shop?category={{ $category->slug }}</span>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
              </td>
              <td style="max-width: 300px; color: #4B5563; font-size: 13px;">
                {{ $category->description ?: 'Belum ada deskripsi koleksi.' }}
              </td>
              <td>
                <a
                  href="{{ route('admin.products.index', ['category' => $category->slug]) }}"
                  style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-weight: 600; font-size: 13px; color: #111827;"
                  title="Filter perhiasan di kategori {{ $category->name }}"
                >
                  <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 9999px; font-size: 12px;">{{ $category->products_count }}</span>
                  <span style="font-size: 12px; color: #6B7280; font-weight: 400;">produk &rarr;</span>
                </a>
              </td>
              <td>
                <div style="font-size: 12.5px; color: #374151;">
                  {{ $category->created_at ? $category->created_at->format('d M Y') : '-' }}
                </div>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: flex-end;">
                  <!-- Tombol Edit Kategori -->
                  <button
                    type="button"
                    onclick="openEditCategoryModal('{{ $category->id }}', '{{ addslashes($category->name) }}', '{{ $category->slug }}', '{{ addslashes($category->description ?? '') }}')"
                    class="table-action-btn"
                    style="background: #F3F4F6; color: #374151; padding: 5px 10px; font-size: 11px;"
                    title="Ubah nama, slug, atau deskripsi"
                  >
                    Edit
                  </button>

                  <!-- Tombol Hapus Kategori -->
                  <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ addslashes($category->name) }}?');">
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="table-action-btn"
                      style="background: #FFFFFF; color: #DC2626; border: 1px solid #FECACA; padding: 5px 8px; font-size: 11px;"
                      title="Hapus kategori (hanya jika 0 produk)"
                      {{ $category->products_count > 0 ? 'disabled' : '' }}
                    >
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 48px 24px; color: #9CA3AF;">
                <div style="font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 4px;">Tidak ada kategori yang cocok dengan pencarian</div>
                <div style="font-size: 13px;">Silakan buat kategori baru atau reset filter pencarian Anda.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($categories->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid #E5E7EB; background: #FAF9F6; display: flex; justify-content: center;">
        {{ $categories->links('partials.pagination') }}
      </div>
    @endif
  </div>
</div>

<!-- ================= MODAL 1: TAMBAH KATEGORI BARU ================= -->
<div id="createCategoryModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 480px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Tambah Kategori Koleksi Baru</h3>
      <button type="button" onclick="closeCreateCategoryModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Kategori *</label>
        <input type="text" name="name" required placeholder="Contoh: Choker &amp; Collars, Titanium Rings" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Slug URL (Opsional)</label>
        <input type="text" name="slug" placeholder="Biarkan kosong untuk auto-generate dari nama" class="chat-search-input" style="width: 100%; font-size: 13px;">
        <span style="font-size: 11px; color: #6B7280;">Digunakan pada URL etalase: /shop?category=nama-slug</span>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Deskripsi Singkat (Opsional)</label>
        <textarea name="description" rows="3" placeholder="Penjelasan singkat mengenai koleksi ini..." class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeCreateCategoryModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Simpan Kategori</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= MODAL 2: EDIT KATEGORI ================= -->
<div id="editCategoryModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 480px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Edit Kategori Perhiasan</h3>
      <button type="button" onclick="closeEditCategoryModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="editCategoryForm" method="POST" style="padding: 20px; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      @method('PUT')

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Nama Kategori *</label>
        <input type="text" name="name" id="editCategoryName" required class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Slug URL *</label>
        <input type="text" name="slug" id="editCategorySlug" required class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Deskripsi Singkat</label>
        <textarea name="description" id="editCategoryDesc" rows="3" class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeEditCategoryModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openCreateCategoryModal() {
  document.getElementById('createCategoryModalBackdrop').style.display = 'flex';
}
function closeCreateCategoryModal() {
  document.getElementById('createCategoryModalBackdrop').style.display = 'none';
}

function openEditCategoryModal(id, name, slug, desc) {
  document.getElementById('editCategoryForm').action = '/admin/categories/' + id;
  document.getElementById('editCategoryName').value = name;
  document.getElementById('editCategorySlug').value = slug;
  document.getElementById('editCategoryDesc').value = desc || '';
  document.getElementById('editCategoryModalBackdrop').style.display = 'flex';
}
function closeEditCategoryModal() {
  document.getElementById('editCategoryModalBackdrop').style.display = 'none';
}
</script>
@endsection

