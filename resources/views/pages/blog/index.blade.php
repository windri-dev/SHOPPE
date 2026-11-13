@extends('layouts.storefront')

@section('title', __('Jurnal & Blog') . ' - PUTRIJALVA')

@section('content')
<div class="container blog-page">
  <h1 class="blog-header-title">{{ __('Jurnal & Blog') }}</h1>

  <div class="blog-layout">
    <!-- Sidebar -->
    <aside class="blog-sidebar">
      <form action="{{ route('blog.index') }}" method="GET" class="blog-search-form">
        @if(request('category'))
          <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="{{ __('Search...') }}"
          class="blog-search-input"
          aria-label="{{ __('Search...') }}"
        >
        <button type="submit" class="blog-search-btn" aria-label="{{ __('Search...') }}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </button>
      </form>

      <div>
        <h2 class="blog-widget-title">{{ __('Category') }}</h2>
        <ul class="blog-category-list">
          <li>
            <a href="{{ route('blog.index') }}" class="blog-category-link {{ !request('category') ? 'active' : '' }}">
              {{ __('All') }}
            </a>
          </li>
          @foreach($categories as $categoryName)
            <li>
              <a
                href="{{ route('blog.index', ['category' => $categoryName]) }}"
                class="blog-category-link {{ request('category') === $categoryName ? 'active' : '' }}"
              >
                {{ __($categoryName) }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </aside>

    <!-- Main Blog Grid -->
    <div class="blog-main">
      <div class="blog-grid">
        @forelse($posts as $post)
          <article class="blog-card">
            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-media">
              <img
                src="{{ asset($post->image ?: 'images/blog-1.jpg') }}"
                alt="{{ $post->title }}"
                class="blog-card-img"
                loading="lazy"
                width="450"
                height="280"
              >
            </a>
            <div class="blog-card-meta">
              {{ __($post->category ?? 'Fashion') }} - {{ ($post->published_at ?? $post->created_at)?->format('F j, Y') }}
            </div>
            <h2 class="blog-card-title">
              <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
            </h2>
            <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
            <a href="{{ route('blog.show', $post->slug) }}" class="blog-card-readmore">{{ __('Read Story') }}</a>
          </article>
        @empty
          <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #FAF9F6; border-radius: 8px;">
            <p style="color: #707070; margin-bottom: 16px;">{{ __('Belum ada artikel yang cocok dengan pencarian Anda.') }}</p>
            <a href="{{ route('blog.index') }}" class="btn btn-outline btn-sm">{{ __('Lihat Semua Cerita') }}</a>
          </div>
        @endforelse
      </div>

      @if($posts->hasPages())
        <div class="pagination-wrapper">
          {{ $posts->links('partials.pagination') }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
