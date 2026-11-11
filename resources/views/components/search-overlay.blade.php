<div class="search-overlay" id="searchOverlay" aria-hidden="true">
  <div class="container search-overlay-container">
    <form class="search-form" id="searchForm" action="{{ route('shop.index') }}" method="GET" role="search">
      <button type="submit" class="search-submit-btn" aria-label="Submit search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>
      <input 
        type="search" 
        class="search-input" 
        id="searchInput" 
        name="search"
        value="{{ request('search') }}"
        placeholder="{{ __('Search products...') }}" 
        autocomplete="off"
      >
      <button type="button" class="search-close-btn" id="searchClose" aria-label="Close search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </form>
  </div>
</div>
