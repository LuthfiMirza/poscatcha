@if ($paginator->hasPages())
  <nav class="online-orders-pager" role="navigation" aria-label="Pagination Navigation">
    <div class="online-orders-pager__summary">
      Menampilkan
      <strong>{{ $paginator->firstItem() }}</strong>
      sampai
      <strong>{{ $paginator->lastItem() }}</strong>
      dari
      <strong>{{ $paginator->total() }}</strong>
      pesanan
    </div>

    <div class="online-orders-pager__links">
      @if ($paginator->onFirstPage())
        <span class="online-orders-pager__btn is-disabled">‹</span>
      @else
        <a class="online-orders-pager__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="online-orders-pager__btn is-disabled">{{ $element }}</span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="online-orders-pager__btn is-active" aria-current="page">{{ $page }}</span>
            @else
              <a class="online-orders-pager__btn" href="{{ $url }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a class="online-orders-pager__btn" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
      @else
        <span class="online-orders-pager__btn is-disabled">›</span>
      @endif
    </div>
  </nav>
@endif
