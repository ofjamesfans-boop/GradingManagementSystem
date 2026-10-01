@if ($paginator->hasPages())
    <nav class="gf-pagination" aria-label="Pagination">
        <p class="gf-pagination-summary">
            Showing {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} records
        </p>
        <div class="gf-pagination-controls">
            @if ($paginator->onFirstPage())
                <span class="gf-pagination-link" aria-disabled="true">Previous</span>
            @else
                <a class="gf-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="gf-pagination-gap" aria-hidden="true">{{ $element }}</span>
                @else
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="gf-pagination-link" aria-current="page" aria-label="Page {{ $page }}">{{ $page }}</span>
                        @else
                            <a class="gf-pagination-link" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="gf-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
            @else
                <span class="gf-pagination-link" aria-disabled="true">Next</span>
            @endif
        </div>
    </nav>
@endif
