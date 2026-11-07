@extends('layouts.storefront')

@section('title', 'Blog & Editorial - SHOPPE')

@section('content')
<div class="container page-container">
  <h1 class="page-title text-center" style="margin-bottom: 40px;">Blog & Editorial</h1>
  <div class="blog-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
    @forelse($posts as $post)
      <article class="blog-card">
        <h2 style="font-size: 18px; margin-bottom: 10px;">{{ $post->title }}</h2>
        <p style="color: #666; font-size: 14px;">{{ $post->excerpt }}</p>
      </article>
    @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        <p>Belum ada artikel yang dipublikasikan.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
