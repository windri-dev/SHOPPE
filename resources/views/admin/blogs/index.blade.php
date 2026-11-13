@extends('layouts.admin')

@section('title', 'Jurnal & Artikel Blog - PUTRIJALVA Admin')
@section('page_title', 'Manajemen Jurnal & Artikel Editorial')

@section('content')
<div class="admin-blogs-page" style="display: flex; flex-direction: column; gap: 20px;">
  <!-- Header Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #FFFFFF; padding: 18px 24px; border-radius: 12px; border: 1px solid #E5E7EB; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
    <div>
      <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        Manajemen Jurnal &amp; Artikel Editorial
      </h2>
      <p style="font-size: 13px; color: #6B7280; margin: 0;">
        Terbitkan artikel tren atasan &amp; crop top, panduan perawatan pakaian, dan cerita di balik layar kurasi PUTRIJALVA.
      </p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
      <button
        type="button"
        onclick="openCreateBlogModal()"
        class="table-action-btn"
        style="background: #111827; color: #FFFFFF; padding: 8px 16px; font-size: 12.5px; font-weight: 600; border-radius: 6px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tulis Artikel Baru
      </button>

      <a href="{{ route('blog.index') }}" target="_blank" class="btn-outline" style="font-size: 12px; padding: 8px 14px; text-decoration: none; border: 1px solid #D1D5DB; border-radius: 6px; color: #374151; display: inline-flex; align-items: center; gap: 6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        Lihat Halaman Blog
      </a>
    </div>
  </div>

  <!-- Stats Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #A18A68;">
      <span style="font-size: 12px; font-weight: 600; color: #8F7655; text-transform: uppercase; letter-spacing: 0.05em;">Total Artikel Terbit</span>
      <div style="font-size: 26px; font-weight: 700; color: #111827; margin-top: 4px;">{{ $stats['total_posts'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Konten jurnal aktif di etalase</div>
    </div>
    <div class="admin-card" style="padding: 16px 20px; border-left: 4px solid #10B981;">
      <span style="font-size: 12px; font-weight: 600; color: #047857; text-transform: uppercase; letter-spacing: 0.05em;">Topik / Kategori Jurnal</span>
      <div style="font-size: 26px; font-weight: 700; color: #10B981; margin-top: 4px;">{{ $stats['categories_count'] }}</div>
      <div style="font-size: 12px; color: #9CA3AF; margin-top: 4px;">Fashion, Style, Jewelry Care, dll</div>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="admin-card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
      <div style="display: flex; gap: 6px; overflow-x: auto; flex-wrap: wrap;">
        <a href="{{ route('admin.blogs.index', array_merge(request()->except('category', 'page'), ['category' => 'all'])) }}"
           class="chat-filter-pill {{ !request('category') || request('category') === 'all' ? 'active' : '' }}" style="text-decoration: none;">
          Semua Topik ({{ $stats['total_posts'] }})
        </a>
        @foreach($blogCategories as $catName)
          <a href="{{ route('admin.blogs.index', array_merge(request()->except('category', 'page'), ['category' => $catName])) }}"
             class="chat-filter-pill {{ request('category') === $catName ? 'active' : '' }}" style="text-decoration: none;">
            {{ $catName }}
          </a>
        @endforeach
      </div>

      <form action="{{ route('admin.blogs.index') }}" method="GET" style="display: flex; gap: 8px; min-width: 260px;">
        @if(request('category'))
          <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari Judul, Penulis, Topik..."
          class="chat-search-input"
          style="padding-left: 12px; width: 230px;"
        >
        <button type="submit" class="table-action-btn" style="background: #1B1B18; color: #fff; padding: 6px 14px;">
          Cari
        </button>
        @if(request('search') || (request('category') && request('category') !== 'all'))
          <a href="{{ route('admin.blogs.index') }}" class="table-action-btn" style="background: #E5E7EB; color: #374151; padding: 6px 10px; text-decoration: none;">
            Reset
          </a>
        @endif
      </form>
    </div>
  </div>

  <!-- Blog Posts Table -->
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 70px;">Sampul</th>
            <th>Judul &amp; Ringkasan Artikel</th>
            <th>Topik</th>
            <th>Penulis</th>
            <th>Tanggal Terbit</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($posts as $post)
            <tr>
              <td>
                <div style="width: 56px; height: 42px; border-radius: 6px; overflow: hidden; background: #F3F4F6; border: 1px solid #E5E7EB;">
                  <img
                    src="{{ asset($post->image ?: 'images/blog-1.jpg') }}"
                    alt="{{ $post->title }}"
                    style="width: 100%; height: 100%; object-fit: cover;"
                    loading="lazy"
                  >
                </div>
              </td>
              <td style="max-width: 340px;">
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" style="font-weight: 700; color: #111827; font-size: 14px; text-decoration: none;">
                  {{ $post->title }}
                </a>
                <div style="font-size: 12px; color: #6B7280; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  {{ $post->excerpt }}
                </div>
              </td>
              <td>
                <span style="font-size: 11.5px; font-weight: 600; background: #FAF9F6; border: 1px solid #E5E7EB; color: #8F7655; padding: 2px 8px; border-radius: 9999px;">
                  {{ $post->category }}
                </span>
              </td>
              <td style="font-size: 12.5px; font-weight: 600; color: #374151;">
                {{ $post->author }}
              </td>
              <td style="font-size: 12.5px; color: #6B7280;">
                {{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px; align-items: center; justify-content: flex-end;">
                  <button
                    type="button"
                    onclick="openEditBlogModal({{ Js::from($post->id) }}, {{ Js::from($post->title) }}, {{ Js::from($post->category) }}, {{ Js::from($post->author) }}, {{ Js::from($post->image) }}, {{ Js::from($post->excerpt) }}, {{ Js::from($post->content) }})"
                    class="table-action-btn"
                    style="background: #F3F4F6; color: #374151; padding: 5px 10px; font-size: 11px;"
                  >
                    Edit
                  </button>

                  <form action="{{ route('admin.blogs.destroy', $post) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus artikel ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="table-action-btn"
                      style="background: #FFFFFF; color: #DC2626; border: 1px solid #FECACA; padding: 5px 8px; font-size: 11px;"
                      title="Hapus artikel"
                    >
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>

                  <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="table-action-btn" style="background: #FAF9F6; color: #374151; padding: 5px 8px; text-decoration: none;" title="Baca artikel">
                    &nearr;
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 48px 24px; color: #9CA3AF;">
                Belum ada artikel jurnal yang sesuai filter.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($posts->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid #E5E7EB; background: #FAF9F6; display: flex; justify-content: center;">
        {{ $posts->links('partials.pagination') }}
      </div>
    @endif
  </div>
</div>

<!-- MODAL TAMBAH ARTIKEL -->
<div id="createBlogModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Tulis Artikel Jurnal Baru</h3>
      <button type="button" onclick="closeCreateBlogModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form action="{{ route('admin.blogs.store') }}" method="POST" style="padding: 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Judul Artikel *</label>
        <input type="text" name="title" required placeholder="Contoh: The Art of Layering Fine Gold Necklaces" class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Topik / Kategori *</label>
          <input type="text" name="category" required value="Fashion" placeholder="Fashion / Style / Care" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Penulis *</label>
          <input type="text" name="author" required value="ANNY JOHNSON" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Path Gambar</label>
          <input type="text" name="image" value="images/blog-1.jpg" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kutipan Ringkas (Excerpt) *</label>
        <textarea name="excerpt" rows="2" required placeholder="Ringkasan 1-2 kalimat untuk kartu preview blog..." class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Isi Artikel Lengkap *</label>
        <textarea name="content" rows="5" required placeholder="Tuliskan isi lengkap artikel..." class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeCreateBlogModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Terbitkan Artikel</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT ARTIKEL -->
<div id="editBlogModalBackdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center; padding: 16px;">
  <div style="background: #FFFFFF; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; border-bottom: 1px solid #E5E7EB; background: #FAF9F6;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #111827;">Edit Artikel Jurnal</h3>
      <button type="button" onclick="closeEditBlogModal()" style="background: none; border: none; font-size: 22px; cursor: pointer; color: #6B7280;">&times;</button>
    </div>

    <form id="editBlogForm" method="POST" style="padding: 22px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
      @csrf
      @method('PUT')
      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Judul Artikel *</label>
        <input type="text" name="title" id="editBlogTitle" required class="chat-search-input" style="width: 100%; font-size: 13px;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Topik / Kategori *</label>
          <input type="text" name="category" id="editBlogCategory" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Penulis *</label>
          <input type="text" name="author" id="editBlogAuthor" required class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Path Gambar</label>
          <input type="text" name="image" id="editBlogImage" class="chat-search-input" style="width: 100%; font-size: 13px;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Kutipan Ringkas (Excerpt) *</label>
        <textarea name="excerpt" id="editBlogExcerpt" rows="2" required class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Isi Artikel Lengkap *</label>
        <textarea name="content" id="editBlogContent" rows="5" required class="chat-search-input" style="width: 100%; font-size: 13px;"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; border-top: 1px solid #E5E7EB; padding-top: 14px;">
        <button type="button" onclick="closeEditBlogModal()" class="table-action-btn" style="background: #F3F4F6; color: #374151;">Batal</button>
        <button type="submit" class="table-action-btn" style="background: #111827; color: #FFFFFF; padding: 8px 18px; font-weight: 600;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openCreateBlogModal() {
  document.getElementById('createBlogModalBackdrop').style.display = 'flex';
}
function closeCreateBlogModal() {
  document.getElementById('createBlogModalBackdrop').style.display = 'none';
}

function openEditBlogModal(id, title, category, author, image, excerpt, content) {
  document.getElementById('editBlogForm').action = '/admin/blogs/' + id;
  document.getElementById('editBlogTitle').value = title;
  document.getElementById('editBlogCategory').value = category;
  document.getElementById('editBlogAuthor').value = author;
  document.getElementById('editBlogImage').value = image || 'images/blog-1.jpg';
  document.getElementById('editBlogExcerpt').value = excerpt;
  document.getElementById('editBlogContent').value = content;
  document.getElementById('editBlogModalBackdrop').style.display = 'flex';
}
function closeEditBlogModal() {
  document.getElementById('editBlogModalBackdrop').style.display = 'none';
}
</script>
@endsection

