@extends('layouts.storefront')

@section('title', $post->title . ' - PUTRI.J Editorial')
@section('meta_description', \Illuminate\Support\Str::limit($post->excerpt, 155))

@section('content')
<article class="container blog-single-page">
  <header class="blog-single-header">
    <h1 class="blog-single-title">{{ $post->title }}</h1>
    <div class="blog-single-byline">
      {{ __('by') }} <span class="author">{{ $post->author ?? 'PUTRI.J EDITORIAL' }}</span> - {{ ($post->published_at ?? $post->created_at)?->format('F j, Y') }}
    </div>
  </header>

  <div class="blog-single-hero">
    <img
      src="{{ asset($post->image ?: 'images/blog-1.jpg') }}"
      alt="{{ $post->title }}"
      width="1248"
      height="702"
    >
  </div>

  <div class="blog-single-content">
    <div class="blog-single-body">
      @foreach(explode("\n\n", $post->content) as $paragraph)
        @if(trim($paragraph) !== '')
          <p>{{ $paragraph }}</p>
        @endif
      @endforeach
    </div>

    <div class="blog-meta-row">
      <div class="blog-tags-side">
        <span class="blog-meta-label">{{ __('Tags') }}</span>
        <span class="blog-meta-divider"></span>
        <span class="blog-tags-list">{{ __($post->category ?? 'Fashion') }}, Style, Jewelry</span>
      </div>
      <div class="blog-share-side">
        <span class="blog-meta-label">{{ __('Share') }}</span>
        <span class="blog-meta-divider"></span>
        <div class="blog-share-icons">
          <a href="{{ route('blog.index') }}" class="blog-share-icon" title="{{ __('Semua Artikel') }}">
            &larr; {{ __('Semua Artikel') }}
          </a>
        </div>
      </div>
    </div>
  </div>

  @if($relatedPosts->count() > 0)
    <section style="margin-top: 64px;">
      <h2 class="section-title" style="margin-bottom: 32px; font-size: 24px;">{{ __('More From Our Journal') }}</h2>
      <div class="blog-grid" style="grid-template-columns: repeat(3, 1fr);">
        @foreach($relatedPosts as $related)
          <article class="blog-card">
            <a href="{{ route('blog.show', $related->slug) }}" class="blog-card-media">
              <img src="{{ asset($related->image ?: 'images/blog-1.jpg') }}" alt="{{ $related->title }}" class="blog-card-img" loading="lazy">
            </a>
            <div class="blog-card-meta">
              {{ __($related->category ?? 'Fashion') }} - {{ ($related->published_at ?? $related->created_at)?->format('F j, Y') }}
            </div>
            <h3 class="blog-card-title">
              <a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a>
            </h3>
            <a href="{{ route('blog.show', $related->slug) }}" class="blog-card-readmore">{{ __('Read Story') }}</a>
          </article>
        @endforeach
      </div>
    </section>
  @endif
</article>
@endsection

