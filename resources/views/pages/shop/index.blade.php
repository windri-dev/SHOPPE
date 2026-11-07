@extends('layouts.storefront')

@section('title', 'Shop - Jewelry Collection | SHOPPE')

@section('content')
<div class="container shop-page">
  <h1 class="shop-page-title">Shop The Latest</h1>

  <div class="shop-layout">
    <!-- Sidebar Filter -->
    <aside class="shop-sidebar">
      <form action="{{ route('shop.index') }}" method="GET" class="filter-form" style="display: flex; flex-direction: column; gap: 28px;">
        <!-- Search filter -->
        <div class="filter-search-wrap">
          <input type="search" name="search" value="{{ request('search') }}" placeholder="Search..." class="filter-search-input">
          <span class="filter-search-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </span>
        </div>

        <!-- Sort By -->
        <div class="filter-group">
          <h3 class="filter-heading">Sort By</h3>
          <div class="filter-select-wrap">
            <select name="sort" class="filter-select" onchange="this.form.submit()">
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

        <!-- Categories -->
        <div class="filter-group">
          <h3 class="filter-heading">Categories</h3>
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
          <div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline btn-sm btn-block" style="font-size: 12px; padding: 10px 14px;">
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
          <div class="no-products-found" style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; background: #FAF9F6; border-radius: 8px;">
            <p style="font-size: 16px; margin-bottom: 15px;">Tidak ada perhiasan yang cocok dengan kriteria pencarian Anda.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-outline">Reset Filter</a>
          </div>
        @endforelse
      </div>

      <div class="pagination-wrapper">
        {{ $products->links('partials.pagination') }}
      </div>
    </div>
  </div>
</div>
@endsection
