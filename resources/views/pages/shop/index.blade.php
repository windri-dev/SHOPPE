@extends('layouts.storefront')

@section('title', 'Shop - Jewelry Collection | PUTRI.J')

@section('content')
<div class="container shop-page">
  <div class="shop-header-wrap">
    <h1 class="shop-page-title">Shop The Latest</h1>
    <p class="shop-page-subtitle mobile-only">Koleksi perhiasan artisan modern berlapis emas 18k dan perak murni.</p>
  </div>

  <div class="shop-layout">
    <!-- Sidebar Filter & Mobile Quick Toolbar -->
    <aside class="shop-sidebar">
      <form action="{{ route('shop.index') }}" method="GET" class="filter-form" id="shopFilterForm">
        <!-- 1. Search Bar -->
        <div class="filter-search-wrap">
          <input
            type="search"
            name="search"
            id="shopSearchInput"
            value="{{ request('search') }}"
            placeholder="Search..."
            class="filter-search-input"
            autocomplete="off"
            aria-label="Search jewelry"
          >
          <a
            href="{{ route('shop.index', request()->except('search', 'page')) }}"
            class="filter-search-clear"
            id="shopSearchClear"
            aria-label="Clear search"
            style="{{ request('search') ? 'display: inline-flex;' : 'display: none;' }}"
          >&times;</a>
          <span class="filter-search-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </span>
        </div>

        <!-- 2. Sort By Dropdown (Desktop: below search; Mobile: toolbar below categories) -->
        <div class="filter-group filter-group-sort">
          <div class="filter-heading-row desktop-only">
            <h3 class="filter-heading">Sort By</h3>
          </div>
          <div class="shop-toolbar-row">
            <span class="shop-toolbar-info">
              Menampilkan <strong>{{ $products->count() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
            </span>
            <div class="filter-select-wrap">
              <select name="sort" class="filter-select" onchange="document.getElementById('shopFilterForm').submit()" aria-label="Sort products">
                <option value="default" {{ request('sort') === 'default' ? 'selected' : '' }}>Default</option>
                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
              </select>
              <span class="filter-select-chevron">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </span>
            </div>
          </div>
        </div>

        <!-- 3. Categories (Desktop: vertical text list; Mobile: horizontal scrollable pills) -->
        <div class="filter-group filter-group-categories">
          <div class="filter-heading-row">
            <h3 class="filter-heading">Categories</h3>
            <span class="filter-heading-count desktop-only">{{ $products->total() }} pieces</span>
          </div>
          <ul class="category-filter-list">
            <li>
              <a href="{{ route('shop.index', request()->except('category', 'page')) }}" class="category-filter-link {{ !request('category') ? 'active' : '' }}">
                <span>All Products</span>
              </a>
            </li>
            @foreach($categories as $category)
              <li>
                <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}" class="category-filter-link {{ request('category') === $category->slug ? 'active' : '' }}">
                  <span>{{ $category->name }}</span>
                  <span class="category-filter-count">({{ $category->products_count }})</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        @if(request()->anyFilled(['search', 'category', 'sort']))
          <div class="filter-reset-wrap">
            <a href="{{ route('shop.index') }}" class="btn-reset-filters">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
              Reset Filter
            </a>
          </div>
        @endif
      </form>
    </aside>

    <!-- Main Grid -->
    <div class="shop-main">
      <div class="product-grid">
        @forelse($products as $product)
          <x-product-card :product="$product" />
        @empty
          <div class="no-products-found">
            <div class="no-products-icon">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A18A68" stroke-width="1.75">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
              </svg>
            </div>
            <h3>Perhiasan Tidak Ditemukan</h3>
            <p>Maaf, tidak ada produk perhiasan yang sesuai dengan filter atau kata kunci Anda.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-outline btn-sm">Reset Semua Filter</a>
          </div>
        @endforelse
      </div>

      <div class="pagination-wrapper">
        {{ $products->links('partials.pagination') }}
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('shopSearchInput');
    const searchClear = document.getElementById('shopSearchClear');
    if (searchInput && searchClear) {
      searchInput.addEventListener('input', function () {
        if (this.value.trim().length > 0) {
          searchClear.style.display = 'inline-flex';
        } else {
          searchClear.style.display = 'none';
        }
      });
      searchClear.addEventListener('click', function (e) {
        if (!searchInput.value) return;
        @if(!request('search'))
          e.preventDefault();
          searchInput.value = '';
          searchClear.style.display = 'none';
          searchInput.focus();
        @endif
      });
    }
  });
</script>
@endpush
@endsection
