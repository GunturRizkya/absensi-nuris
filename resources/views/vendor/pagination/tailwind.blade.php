@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman">
        {{-- Mobile: prev / next --}}
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-xs font-bold text-slate-300 bg-white border border-slate-200 cursor-not-allowed leading-5 rounded-xl">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-xs font-bold text-blue-600 bg-white border border-slate-200 leading-5 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition">Sebelumnya</a>
            @endif

            <span class="text-[11px] font-bold text-slate-400">Hal. {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-xs font-bold text-blue-600 bg-white border border-slate-200 leading-5 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition">Berikutnya</a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-xs font-bold text-slate-300 bg-white border border-slate-200 cursor-not-allowed leading-5 rounded-xl">Berikutnya</span>
            @endif
        </div>

        {{-- Desktop: info + nomor --}}
        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-slate-400 font-medium leading-5">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-bold text-slate-600">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
                        dari
                    @endif
                    <span class="font-bold text-slate-600">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div>
                <span class="inline-flex items-center gap-1">
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-slate-300 bg-white border border-slate-200 cursor-not-allowed rounded-xl" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-slate-500 bg-white border border-slate-200 rounded-xl hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition" aria-label="Halaman sebelumnya">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-slate-300 bg-white cursor-default">…</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 text-xs font-extrabold text-white bg-blue-600 border border-blue-600 rounded-xl shadow-sm shadow-blue-500/20">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-xl hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-slate-500 bg-white border border-slate-200 rounded-xl hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition" aria-label="Halaman berikutnya">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-slate-300 bg-white border border-slate-200 cursor-not-allowed rounded-xl" aria-hidden="true">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
