@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        {{-- Info --}}
        <p class="text-sm text-neutral-500">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-semibold text-neutral-700">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-semibold text-neutral-700">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-semibold text-neutral-700">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        {{-- Page numbers + jump input --}}
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-neutral-300 bg-white border border-neutral-200 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-neutral-500 bg-white border border-neutral-200 hover:bg-neutral-50 hover:text-neutral-700 transition" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @endif

            {{-- Page numbers: show max 5 around current --}}
            @php
                $totalPages = $paginator->lastPage();
                $currentPage = $paginator->currentPage();
                $startPage = max($currentPage - 2, 1);
                $endPage = min($startPage + 4, $totalPages);
                if ($endPage - $startPage < 4) {
                    $startPage = max($endPage - 4, 1);
                }
            @endphp

            @if ($startPage > 1)
                <a href="{{ $paginator->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-neutral-600 bg-white border border-neutral-200 hover:bg-neutral-50 hover:text-neutral-800 transition">1</a>
                @if ($startPage > 2)
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-neutral-400">...</span>
                @endif
            @endif

            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-semibold text-white bg-accent border border-accent shadow-fluent-sm cursor-default">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-neutral-600 bg-white border border-neutral-200 hover:bg-neutral-50 hover:text-neutral-800 transition" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($endPage < $totalPages)
                @if ($endPage < $totalPages - 1)
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-neutral-400">...</span>
                @endif
                <a href="{{ $paginator->url($totalPages) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-neutral-600 bg-white border border-neutral-200 hover:bg-neutral-50 hover:text-neutral-800 transition">{{ $totalPages }}</a>
            @endif

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-neutral-500 bg-white border border-neutral-200 hover:bg-neutral-50 hover:text-neutral-700 transition" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-neutral-300 bg-white border border-neutral-200 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @endif

            {{-- Go to page input --}}
            @if ($totalPages > 5)
                <span class="hidden sm:inline-flex items-center text-xs text-neutral-400 mx-1.5">|</span>
                <form class="inline-flex items-center gap-1 ml-1 sm:ml-0" onsubmit="goToPage(event, {{ $totalPages }})">
                    <input type="number" min="1" max="{{ $totalPages }}" placeholder="Hlm" class="w-16 h-9 text-center text-sm rounded-lg bg-white border border-neutral-200 text-neutral-600 placeholder:text-neutral-300 outline-none focus:border-accent focus:ring-[3px] focus:ring-accent/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" aria-label="Halaman tujuan">
                    <button type="submit" class="flex items-center justify-center h-9 px-3 text-sm font-medium text-white bg-accent hover:bg-accent-hover rounded-lg transition">Go</button>
                </form>
            @endif
        </div>
    </nav>

    <script>
        function goToPage(event, totalPages) {
            event.preventDefault();
            const input = event.target.querySelector('input[type=number]');
            let page = parseInt(input.value);
            if (isNaN(page) || page < 1 || page > totalPages) return;
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }
    </script>
@endif