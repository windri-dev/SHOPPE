@extends('layouts.storefront')

@section('title', 'Our Story - PUTRI.J')

@section('content')
<div class="container about-page">
  <div class="about-header">
    <h1 class="about-title">About Our Brand</h1>
    <p class="about-subtitle">
      Contemporary ready-to-wear fashion and apparel designed for effortless everyday elegance.
    </p>
  </div>

  <div class="about-content">
    <p>
      PUTRI.J didirikan atas dasar keyakinan bahwa keindahan sejati terpancar dari potongan siluet yang presisi, kemurnian kain berkualitas tinggi, dan sentuhan pengerjaan jahitan yang teliti. Kami menciptakan busana kontemporer mulai dari gaun satin berpotongan bias, kemeja santai berbahan French linen, blazer tailored berstruktur, hingga setelan rajut yang anggun dan nyaman.
    </p>

    <div class="about-media">
      <img src="{{ asset('images/hero-1.png') }}" alt="PUTRI.J Atelier Studio" loading="lazy">
    </div>

    <h2 class="about-section-title">Filosofi &amp; Kualitas Kain</h2>
    <p>
      Setiap rancangan busana lahir melalui kurasi material terbaik—mulai dari washed linen yang adem, mulberry silk blend yang berkarakter drape mewah, hingga katun poplin organik berpori sejuk. Kami memprioritaskan kenyamanan bergerak di iklim tropis dengan daya tahan pakai jangka panjang yang melampaui tren sesaat.
    </p>
  </div>
</div>
@endsection
