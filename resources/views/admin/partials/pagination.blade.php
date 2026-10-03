@if ($paginator->hasPages())
  <nav class="admin-pager" aria-label="Pagination">
    <p class="admin-pager-summary">
      Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </p>
    <div class="admin-pager-links">
      @if ($paginator->onFirstPage())
        <span class="admin-pager-btn is-disabled" aria-disabled="true">Previous</span>
      @else
        <a class="admin-pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="admin-pager-gap">{{ $element }}</span>
        @endif
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="admin-pager-btn is-current" aria-current="page">{{ $page }}</span>
            @else
              <a class="admin-pager-btn" href="{{ $url }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a class="admin-pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
      @else
        <span class="admin-pager-btn is-disabled" aria-disabled="true">Next</span>
      @endif
    </div>
  </nav>
@endif
