@extends('layouts.storefront')

@section('title', 'Our Story - SHOPPE')

@section('content')
<div class="container about-page">
  <div class="about-header">
    <h1 class="about-title">About Our Brand</h1>
    <p class="about-subtitle">
      Modern luxury jewelry designed for effortless everyday elegance.
    </p>
  </div>

  <div class="about-content">
    <p>
      SHOPPE didirikan atas dasar keyakinan bahwa keindahan sejati terletak pada kesederhanaan garis bentuk, kemurnian material, dan sentuhan artisan yang teliti. Kami menciptakan perhiasan kontemporer mulai dari anting lingkaran berlapis emas 18k, kalung minimalis, hingga aksesori rambut berbahan kuningan organik.
    </p>

    <div class="about-media">
      <img src="{{ asset('images/hero-1.png') }}" alt="SHOPPE Artisan Studio" loading="lazy">
    </div>

    <h2 class="about-section-title">Filosofi & Pengerjaan</h2>
    <p>
      Setiap koleksi dirancang dengan pendekatan arsitektural yang halus, memadukan siluet modern dan kenyamanan pemakaian sepanjang hari. Kami menggunakan material hypoallergenic berkualitas tinggi dengan proses finishing cermat yang tahan lama.
    </p>
  </div>
</div>
@endsection
