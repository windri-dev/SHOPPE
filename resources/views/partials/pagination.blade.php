@if ($paginator->hasPages())
  <nav class="shop-pagination" role="navigation" aria-label="Pagination Navigation">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <span class="pagination-item disabled" aria-disabled="true" aria-label="Previous">&larr;</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="pagination-item" rel="prev" aria-label="Previous">&larr;</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
      {{-- "Three Dots" Separator --}}
      @if (is_string($element))
        <span class="pagination-item disabled" aria-disabled="true">{{ $element }}</span>
      @endif

      {{-- Array Of Links --}}
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="pagination-item active" aria-current="page">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="pagination-item" rel="next" aria-label="Next">&rarr;</a>
    @else
      <span class="pagination-item disabled" aria-disabled="true" aria-label="Next">&rarr;</span>
    @endif
  </nav>
@endif

