@if ($paginator->hasPages())
    <nav class="flex flex-col gap-3 rounded-[1.5rem] bg-white p-3 shadow-sm ring-1 ring-orange-100/70 sm:flex-row sm:items-center sm:justify-between" role="navigation" aria-label="Pagination Navigation">
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-10 items-center justify-center rounded-full bg-stone-100 px-4 text-sm font-bold text-stone-400">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-10 items-center justify-center rounded-full bg-orange-50 px-4 text-sm font-bold text-orange-700 transition hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Sebelumnya</a>
            @endif

            <span class="text-xs font-bold text-stone-500">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-10 items-center justify-center rounded-full bg-orange-700 px-4 text-sm font-bold text-white transition hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Berikutnya</a>
            @else
                <span class="inline-flex min-h-10 items-center justify-center rounded-full bg-stone-100 px-4 text-sm font-bold text-stone-400">Berikutnya</span>
            @endif
        </div>

        <p class="hidden text-sm font-semibold text-stone-500 sm:block">
            Menampilkan
            <span class="font-black text-stone-900">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-black text-stone-900">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-black text-stone-900">{{ $paginator->total() }}</span>
            menu
        </p>

        <div class="hidden items-center gap-1 sm:flex">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-10 min-w-10 cursor-not-allowed items-center justify-center rounded-full bg-stone-100 px-3 text-sm font-bold text-stone-400">‹</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-white px-3 text-sm font-bold text-stone-600 ring-1 ring-stone-200 transition hover:bg-orange-50 hover:text-orange-700 hover:ring-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">‹</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-full px-3 text-sm font-bold text-stone-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-orange-700 px-3 text-sm font-black text-white shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-white px-3 text-sm font-bold text-stone-600 ring-1 ring-stone-200 transition hover:bg-orange-50 hover:text-orange-700 hover:ring-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-white px-3 text-sm font-bold text-stone-600 ring-1 ring-stone-200 transition hover:bg-orange-50 hover:text-orange-700 hover:ring-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">›</a>
            @else
                <span class="inline-flex h-10 min-w-10 cursor-not-allowed items-center justify-center rounded-full bg-stone-100 px-3 text-sm font-bold text-stone-400">›</span>
            @endif
        </div>
    </nav>
@endif
