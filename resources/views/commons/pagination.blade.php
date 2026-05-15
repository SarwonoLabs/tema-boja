@if ($paging && $paging->lastPage() > 1)
    <nav class="pag-boja">
        <p class="pag-info">
            Halaman <strong>{{ $paging->currentPage() }}</strong> dari <strong>{{ $paging->lastPage() }}</strong>
        </p>
        <ul class="pag-list">
            {{-- Previous --}}
            @if ($paging->onFirstPage())
                <li class="pag-disabled"><span><i class="fas fa-chevron-left"></i></span></li>
            @else
                <li><a href="{{ $paging->previousPageUrl() }}" title="Sebelumnya"><i class="fas fa-chevron-left"></i></a></li>
            @endif

            {{-- Page numbers (smart range) --}}
            @php
                $current = $paging->currentPage();
                $last    = $paging->lastPage();
                $start   = max(1, $current - 2);
                $end     = min($last, $current + 2);
                if ($start > 1) { $start = max(1, $current - 2); }
                if ($end < $last) { $end = min($last, $current + 2); }
            @endphp

            @if ($start > 1)
                <li><a href="{{ $paging->url(1) }}">1</a></li>
                @if ($start > 2)
                    <li class="pag-dots"><span>&hellip;</span></li>
                @endif
            @endif

            @for ($p = $start; $p <= $end; $p++)
                <li class="{{ $p == $current ? 'pag-active' : '' }}">
                    <a href="{{ $paging->url($p) }}">{{ $p }}</a>
                </li>
            @endfor

            @if ($end < $last)
                @if ($end < $last - 1)
                    <li class="pag-dots"><span>&hellip;</span></li>
                @endif
                <li><a href="{{ $paging->url($last) }}">{{ $last }}</a></li>
            @endif

            {{-- Next --}}
            @if ($paging->hasMorePages())
                <li><a href="{{ $paging->nextPageUrl() }}" title="Berikutnya"><i class="fas fa-chevron-right"></i></a></li>
            @else
                <li class="pag-disabled"><span><i class="fas fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </nav>
@endif
