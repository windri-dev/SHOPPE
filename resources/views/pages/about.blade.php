@extends('layouts.storefront')

@section('title', __('Our Story') . ' - PUTRIJALVA')

@section('content')
<div class="container about-page">
  <div class="about-header">
    <h1 class="about-title">{{ __('About Our Brand') }}</h1>
    <p class="about-subtitle">
      {{ __('Contemporary ready-to-wear fashion and apparel designed for effortless everyday elegance.') }}
    </p>
  </div>

  <div class="about-content">
    @if(app()->getLocale() === 'en')
      <p>
        PUTRIJALVA was founded on the conviction that authentic elegance arises from precise silhouettes, pure organic fabrics, and meticulous artisanal craftsmanship. We create contemporary ready-to-wear pieces ranging from bias-cut silk slip dresses, relaxed artisanal Indonesian linen shirts, structured tailored blazers, to effortlessly refined knitwear sets.
      </p>

      <div class="about-media">
        <img src="{{ asset('images/hero-1.png') }}" alt="PUTRIJALVA Atelier Studio" loading="lazy">
      </div>

      <h2 class="about-section-title">Design Philosophy &amp; Fabric Excellence</h2>
      <p>
        Every creation begins with thoughtful material curation—from breathable washed linen and luxurious mulberry silk blends with graceful drape, to crisp organic cotton poplin. We design for unrestricted movement in tropical and urban climates with timeless durability that transcends fleeting seasonal trends.
      </p>
    @else
      <p>
        PUTRIJALVA didirikan atas dasar keyakinan bahwa keindahan sejati terpancar dari potongan siluet yang presisi, kemurnian kain berkualitas tinggi, dan sentuhan pengerjaan jahitan yang teliti. Kami menciptakan busana kontemporer mulai dari gaun satin berpotongan bias, kemeja santai berbahan linen alami Indonesia, blazer tailored berstruktur, hingga setelan rajut yang anggun dan nyaman.
      </p>

      <div class="about-media">
        <img src="{{ asset('images/hero-1.png') }}" alt="PUTRIJALVA Atelier Studio" loading="lazy">
      </div>

      <h2 class="about-section-title">Filosofi &amp; Kualitas Kain</h2>
      <p>
        Setiap rancangan busana lahir melalui kurasi material terbaik—mulai dari washed linen yang adem, mulberry silk blend yang berkarakter drape mewah, hingga katun poplin organik berpori sejuk. Kami memprioritaskan kenyamanan bergerak di iklim tropis dengan daya tahan pakai jangka panjang yang melampaui tren sesaat.
      </p>
    @endif
  </div>
</div>
@endsection
