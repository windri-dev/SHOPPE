@extends('layouts.admin')

@section('title', 'Manajemen Banner & Slider - PUTRIJALVA Admin')
@section('page_title', 'Banner & Slider Carousel Beranda')

@section('content')
<div class="admin-banners-page" style="display: flex; flex-direction: column; gap: 20px;">
  @if(session('success'))
    <div style="background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;">
      {{ session('success') }}
    </div>
  @endif

  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
        Manajemen Banner &amp; Slider Beranda
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Atur slide promosi, foto model, judul busana, harga promo, dan link tombol pada slider halaman depan toko.
      </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
      <button
        type="button"
        onclick="openCreateBannerModal()"
        class="table-action-btn"
        style="background: #111827; color: #FFFFFF; padding: 8px 16px; font-size: 12.5px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Banner Baru
      </button>

      <a href="{{ route('home') }}" target="_blank" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151; display: inline-flex; align-items: center; gap: 6px;">
        Lihat Slider Beranda &rarr;
      </a>
    </div>
  </div>

  <!-- Stats Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #A18A68;">
      <span style="font-size: 12px; font-weight: 600; color: #8F7655; text-transform: uppercase; letter-spacing: 0.05em;">Total Banner</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ $stats['total'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Slide terdaftar di sistem</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #10B981;">
      <span style="font-size: 12px; font-weight: 600; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">Banner Aktif</span>
      <div style="font-size: 26px; font-weight: 700; color: #10B981; margin-top: 4px;">{{ $stats['active'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Tampil di carousel beranda</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #9CA3AF;">
      <span style="font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em;">Banner Nonaktif</span>
      <div style="font-size: 26px; font-weight: 700; color: #6B7280; margin-top: 4px;">{{ $stats['inactive'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Disembunyikan sementara</div>
    </div>
  </div>

  <!-- Table Banners List -->
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6; display: flex; justify-content: space-between; align-items: center;">
      <div style="font-size: 14px; font-weight: 700; color: #111827;">
        Daftar Slide Banner Aktif &amp; Arsip
      </div>
      <div style="font-size: 12px; color: #6B7280;">
        Urutan terkecil (1, 2, 3...) tampil lebih awal di carousel
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="admin-table" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th style="width: 60px; text-align: center;">Urutan</th>
            <th style="width: 140px;">Preview Banner</th>
            <th>Judul &amp; Subtitle / Harga</th>
            <th>Tombol CTA &amp; Link</th>
            <th>Badge Promo</th>
            <th style="width: 110px; text-align: center;">Status</th>
            <th style="width: 140px; text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($banners as $banner)
            <tr style="border-bottom: 1px solid #F3F4F6;">
              <td style="text-align: center; font-weight: 700; font-size: 14px; color: #A18A68;">
                #{{ $banner->sort_order }}
              </td>
              <td>
                <div style="position: relative; width: 120px; height: 60px; border-radius: 8px; overflow: hidden; border: 1px solid #E5E7EB; background: #111827;">
                  <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
              </td>
              <td>
                <div style="font-weight: 700; color: #111827; font-size: 14px;">{{ $banner->getRawTitle() }}</div>
                @if($banner->title_en)
                  <div style="font-size: 11px; color: #6B7280; font-style: italic;">EN: {{ $banner->title_en }}</div>
                @endif
                @if($banner->getRawSubtitle())
                  <div style="font-size: 12.5px; font-weight: 600; color: #A18A68; margin-top: 2px;">
                    {{ $banner->getRawSubtitle() }}
                  </div>
                @endif
              </td>
              <td>
                <div style="font-size: 12px; font-weight: 600; color: #111827;">
                  &ldquo;{{ $banner->button_text }}&rdquo;
                </div>
                <div style="font-size: 11px; color: #6B7280; font-family: monospace; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  {{ $banner->button_link }}
                </div>
              </td>
              <td>
                @if($banner->badge_text)
                  <span style="font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 99px; background: #FEF3C7; color: #92400E; text-transform: uppercase;">
                    {{ $banner->badge_text }}
                  </span>
                @else
                  <span style="font-size: 11px; color: #9CA3AF;">-</span>
                @endif
              </td>
              <td style="text-align: center;">
                <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" style="margin: 0;">
                  @csrf
                  @if($banner->is_active)
                    <button type="submit" class="table-action-btn" style="background: #D1FAE5; color: #047857; border: 1px solid #A7F3D0; padding: 4px 8px; font-size: 11px; font-weight: 700; border-radius: 99px;" title="Klik untuk nonaktifkan">
                      &bull; Aktif
                    </button>
                  @else
                    <button type="submit" class="table-action-btn" style="background: #F3F4F6; color: #6B7280; border: 1px solid #E5E7EB; padding: 4px 8px; font-size: 11px; font-weight: 600; border-radius: 99px;" title="Klik untuk aktifkan">
                      Nonaktif
                    </button>
                  @endif
                </form>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px; align-items: center;">
                  @php
                    $bannerJson = json_encode([
                      'id' => $banner->id,
                      'title' => $banner->getRawTitle(),
                      'title_en' => $banner->title_en ?? '',
                      'subtitle' => $banner->getRawSubtitle() ?? '',
                      'subtitle_en' => $banner->subtitle_en ?? '',
                      'button_text' => $banner->button_text,
                      'button_text_en' => $banner->button_text_en ?? '',
                      'button_link' => $banner->button_link,
                      'badge_text' => $banner->badge_text ?? '',
                      'sort_order' => $banner->sort_order,
                      'image_url' => $banner->image_url,
                      'is_active' => $banner->is_active,
                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                  @endphp
                  <button
                    type="button"
                    data-banner="{!! $bannerJson !!}"
                    onclick="openEditBannerModal(this)"
                    class="table-action-btn"
                    style="background: #F3F4F6; color: #374151; padding: 5px 9px; font-size: 11px;"
                    title="Edit banner"
                  >
                    Edit
                  </button>

                  <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini dari slider secara permanen?')" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="table-action-btn"
                      style="background: #FFFFFF; color: #DC2626; border: 1px solid #FECACA; padding: 5px 8px; font-size: 11px;"
                      title="Hapus banner"
                    >
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 40px; color: #9CA3AF;">
                Belum ada banner terdaftar. Klik <strong>Tambah Banner Baru</strong> di atas untuk membuat slide pertama.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ================= MODAL: TAMBAH BANNER BARU ================= -->
<div id="createBannerModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 600px; max-height: 90vh; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">+ Tambah Banner Slider Baru</h3>
      <button type="button" onclick="closeCreateBannerModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" style="padding: 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
      @csrf

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Judul Banner (ID) *</label>
          <input type="text" name="title" required placeholder="Contoh: Aurelia Ruched Silk Crop Top" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #6B7280; margin-bottom: 4px;">Judul Banner (EN - Opsional)</label>
          <input type="text" name="title_en" placeholder="Contoh: Aurelia Ruched Silk Crop Top" class="chat-search-input" style="width: 100%; font-size: 13px; background: #FAF9F6;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Subtitle / Harga (ID)</label>
          <input type="text" name="subtitle" placeholder="Contoh: Rp 1.183.000 atau Spesialis Crop Top" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #6B7280; margin-bottom: 4px;">Subtitle / Harga (EN)</label>
          <input type="text" name="subtitle_en" placeholder="Contoh: $ 65.00" class="chat-search-input" style="width: 100%; font-size: 13px; background: #FAF9F6;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Teks Tombol CTA *</label>
          <input type="text" name="button_text" value="Lihat Koleksi" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Link Tujuan Tombol *</label>
          <input type="text" name="button_link" value="/shop" required placeholder="/shop atau /shop/nama-produk" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Badge Promo (Opsional)</label>
          <input type="text" name="badge_text" placeholder="Contoh: KOLEKSI UTAMA / NEW" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Urutan Tampil (Sort Order) *</label>
          <input type="number" name="sort_order" value="1" min="0" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <!-- Upload Foto Banner -->
      <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px;">
        <label style="display: block; font-size: 12px; font-weight: 700; color: #111827; margin-bottom: 4px;">Foto / Gambar Banner * (Rekomendasi rasio 2:1, misal 1200x600 px)</label>
        <input type="file" name="image" accept="image/png,image/jpeg,image/jpg,image/webp" class="chat-search-input" style="width: 100%; font-size: 12px; background: #fff;">
        <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">Atau masukkan path gambar internal (misal: <code>images/hero-1.png</code>):</div>
        <input type="text" name="image_url" placeholder="images/hero-1.png" class="chat-search-input" style="width: 100%; font-size: 12px; margin-top: 4px;">
      </div>

      <div style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" name="is_active" id="createBannerActive" value="1" checked style="width: 16px; height: 16px;">
        <label for="createBannerActive" style="font-size: 13px; font-weight: 600; color: #111827; cursor: pointer;">
          Aktifkan langsung di carousel beranda
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeCreateBannerModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600; border: none;">Simpan Banner</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= MODAL: EDIT BANNER ================= -->
<div id="editBannerModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 600px; max-height: 90vh; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Edit Banner Slider</h3>
      <button type="button" onclick="closeEditBannerModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="editBannerForm" method="POST" enctype="multipart/form-data" style="padding: 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      @method('PUT')

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Judul Banner (ID) *</label>
          <input type="text" name="title" id="editBannerTitle" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #6B7280; margin-bottom: 4px;">Judul Banner (EN)</label>
          <input type="text" name="title_en" id="editBannerTitleEn" class="chat-search-input" style="width: 100%; font-size: 13px; background: #FAF9F6;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Subtitle / Harga (ID)</label>
          <input type="text" name="subtitle" id="editBannerSubtitle" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #6B7280; margin-bottom: 4px;">Subtitle / Harga (EN)</label>
          <input type="text" name="subtitle_en" id="editBannerSubtitleEn" class="chat-search-input" style="width: 100%; font-size: 13px; background: #FAF9F6;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Teks Tombol CTA *</label>
          <input type="text" name="button_text" id="editBannerBtnText" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Link Tujuan Tombol *</label>
          <input type="text" name="button_link" id="editBannerBtnLink" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Badge Promo</label>
          <input type="text" name="badge_text" id="editBannerBadge" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Urutan Tampil (Sort Order) *</label>
          <input type="number" name="sort_order" id="editBannerSortOrder" min="0" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <!-- Preview & Ganti Foto Banner -->
      <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px;">
        <label style="display: block; font-size: 12px; font-weight: 700; color: #111827; margin-bottom: 6px;">Foto Banner Saat Ini:</label>
        <div style="width: 100%; height: 110px; border-radius: 6px; overflow: hidden; background: #111827; margin-bottom: 8px;">
          <img id="editBannerCurrentImg" src="" alt="Preview Banner" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <label style="display: block; font-size: 11.5px; font-weight: 600; color: #374151; margin-bottom: 4px;">Ganti Foto (Upload File Baru):</label>
        <input type="file" name="image" accept="image/png,image/jpeg,image/jpg,image/webp" class="chat-search-input" style="width: 100%; font-size: 12px; background: #fff;">
        <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">Atau ubah path gambar:</div>
        <input type="text" name="image_url" id="editBannerImageUrl" class="chat-search-input" style="width: 100%; font-size: 12px; margin-top: 4px;">
      </div>

      <div style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" name="is_active" id="editBannerActive" value="1" style="width: 16px; height: 16px;">
        <label for="editBannerActive" style="font-size: 13px; font-weight: 600; color: #111827; cursor: pointer;">
          Aktifkan di carousel beranda
        </label>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 10px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeEditBannerModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600; border: none;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function openCreateBannerModal() {
  document.getElementById('createBannerModalBackdrop').style.display = 'flex';
}

function closeCreateBannerModal() {
  document.getElementById('createBannerModalBackdrop').style.display = 'none';
}

function openEditBannerModal(btn) {
  try {
    const raw = btn.getAttribute('data-banner');
    const b = JSON.parse(raw);

    document.getElementById('editBannerForm').action = '/admin/banners/' + b.id;
    document.getElementById('editBannerTitle').value = b.title || '';
    document.getElementById('editBannerTitleEn').value = b.title_en || '';
    document.getElementById('editBannerSubtitle').value = b.subtitle || '';
    document.getElementById('editBannerSubtitleEn').value = b.subtitle_en || '';
    document.getElementById('editBannerBtnText').value = b.button_text || 'Lihat Koleksi';
    document.getElementById('editBannerBtnLink').value = b.button_link || '/shop';
    document.getElementById('editBannerBadge').value = b.badge_text || '';
    document.getElementById('editBannerSortOrder').value = b.sort_order ?? 0;
    document.getElementById('editBannerCurrentImg').src = b.image_url || '';
    document.getElementById('editBannerImageUrl').value = b.image_url || '';
    document.getElementById('editBannerActive').checked = Boolean(b.is_active);

    document.getElementById('editBannerModalBackdrop').style.display = 'flex';
  } catch (e) {
    console.error('Error opening banner edit modal:', e);
  }
}

function closeEditBannerModal() {
  document.getElementById('editBannerModalBackdrop').style.display = 'none';
}
</script>
@endpush
@endsection
