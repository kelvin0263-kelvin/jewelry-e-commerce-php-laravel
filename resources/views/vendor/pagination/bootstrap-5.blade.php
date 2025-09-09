@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mb-0" style="font-size: 0.7rem;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" style="padding: 0.3rem 0.5rem;">
                        <i class="fas fa-chevron-left me-1" style="font-size: 0.6rem;"></i>Previous
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding: 0.3rem 0.5rem;">
                        <i class="fas fa-chevron-left me-1" style="font-size: 0.6rem;"></i>Previous
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link" style="padding: 0.3rem 0.5rem;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link" style="padding: 0.3rem 0.5rem;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" style="padding: 0.3rem 0.5rem;">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding: 0.3rem 0.5rem;">
                        Next<i class="fas fa-chevron-right ms-1" style="font-size: 0.6rem;"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" style="padding: 0.3rem 0.5rem;">
                        Next<i class="fas fa-chevron-right ms-1" style="font-size: 0.6rem;"></i>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Results Info --}}
        <div class="text-center mt-2">
            <small class="text-muted" style="font-size: 0.6rem;">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </small>
        </div>
    </nav>
@endif