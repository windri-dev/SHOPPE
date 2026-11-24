{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <!-- Halaman Utama & Statis -->
  <url>
    <loc>{{ url('/') }}</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ route('shop.index') }}</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc>{{ route('blog.index') }}</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>{{ route('contact.index') }}</loc>
    <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>

  <!-- Kategori Produk -->
  @foreach($categories as $category)
    <url>
      <loc>{{ route('shop.index', ['category' => $category->slug]) }}</loc>
      <lastmod>{{ $category->updated_at ? $category->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
    </url>
  @endforeach

  <!-- Katalog Busana / Produk Aktif -->
  @foreach($products as $product)
    <url>
      <loc>{{ route('shop.show', $product->slug) }}</loc>
      <lastmod>{{ $product->updated_at ? $product->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.9</priority>
    </url>
  @endforeach

  <!-- Artikel Jurnal & Editorial -->
  @foreach($posts as $post)
    <url>
      <loc>{{ route('blog.show', $post->slug) }}</loc>
      <lastmod>{{ $post->updated_at ? $post->updated_at->format('Y-m-d') : now()->format('Y-m-d') }}</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.7</priority>
    </url>
  @endforeach
</urlset>
