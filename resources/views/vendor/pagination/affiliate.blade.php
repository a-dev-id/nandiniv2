@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-600">
            Showing <span class="font-medium text-slate-900">{{ $paginator->firstItem() }}</span>
            to <span class="font-medium text-slate-900">{{ $paginator->lastItem() }}</span>
            of <span class="font-medium text-slate-900">{{ $paginator->total() }}</span> results
        </p>

        <div class="inline-flex w-fit overflow-hidden border border-slate-300 bg-white shadow-sm">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-10 w-10 items-center justify-center border-r border-slate-300 bg-slate-100 text-slate-400" aria-disabled="true" aria-label="Previous page">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-10 w-10 items-center justify-center border-r border-slate-300 text-slate-700 transition hover:bg-amber-50 hover:text-[#8B6B35]" rel="prev" aria-label="Previous page">&lsaquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-10 min-w-10 items-center justify-center border-r border-slate-300 px-3 text-sm text-slate-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-10 min-w-10 items-center justify-center border-r border-[#A88444] bg-[#A88444] px-3 text-sm font-medium text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center border-r border-slate-300 px-3 text-sm font-medium text-slate-700 transition hover:bg-amber-50 hover:text-[#8B6B35]" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-10 w-10 items-center justify-center text-slate-700 transition hover:bg-amber-50 hover:text-[#8B6B35]" rel="next" aria-label="Next page">&rsaquo;</a>
            @else
                <span class="inline-flex h-10 w-10 items-center justify-center bg-slate-100 text-slate-400" aria-disabled="true" aria-label="Next page">&rsaquo;</span>
            @endif
        </div>
    </nav>
@endif
