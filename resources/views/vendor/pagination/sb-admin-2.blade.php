@if ($paginator->hasPages() || $paginator->total() > 0)
    @php
        $hidePerPage = $hidePerPage ?? request()->routeIs('complaints.*');
        $pageName = $paginator->getPageName();
        $selectId = 'per_page_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $pageName) . '_' . mt_rand(1000, 9999);
        $currentPerPage = (int) ($paginator->perPage() ?: 25);
        $options = [10, 25, 50, 100];
        if (!in_array($currentPerPage, $options)) {
            $options[] = $currentPerPage;
            sort($options);
        }

        $currentPage = $paginator->currentPage();
        $lastPage = max(1, $paginator->lastPage());
        $pageElements = [];

        if ($lastPage <= 5) {
            for ($i = 1; $i <= $lastPage; $i++) {
                $pageElements[] = $i;
            }
        } else {
            // Tampilkan maksimal 5 elemen (angka & titik-titik) sesuai contoh referensi:
            // Saat halaman 1 atau 2: 1, 2, 3, ... (tepat seperti gambar)
            if ($currentPage <= 2) {
                $pageElements = [1, 2, 3, '...'];
            } elseif ($currentPage == 3) {
                $pageElements = [1, 2, 3, 4, '...'];
            } elseif ($currentPage >= $lastPage - 1) {
                $pageElements = ['...', $lastPage - 2, $lastPage - 1, $lastPage];
            } elseif ($currentPage == $lastPage - 2) {
                $pageElements = ['...', $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
            } else {
                $pageElements = ['...', $currentPage - 1, $currentPage, $currentPage + 1, '...'];
            }
        }
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-2 w-full text-sm">
        {{-- Sisi Kiri Bawah: Page Row & Keterangan --}}
        <div class="flex flex-wrap items-center gap-3">
            @if (!$hidePerPage)
                <div class="flex items-center gap-2.5">
                    <label for="{{ $selectId }}" class="text-sm font-normal text-slate-900 whitespace-nowrap">
                        Rows per page
                    </label>
                    <div class="relative inline-flex items-center">
                        <select id="{{ $selectId }}"
                                onchange="const u = new URL(window.location.href); u.searchParams.set('per_page', this.value); u.searchParams.delete('{{ $pageName }}'); u.searchParams.delete('page'); window.location.href = u.toString();"
                                style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;"
                                class="no-native-arrow bg-white border border-slate-200 hover:border-slate-300 text-slate-900 text-sm font-semibold rounded-xl pl-3.5 pr-8 py-1.5 min-w-[70px] focus:outline-hidden focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-[0_1px_2px_rgba(0,0,0,0.04)] cursor-pointer transition">
                            @foreach ($options as $option)
                                <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
            @if ($paginator->total() > 0)
                <span class="text-xs text-slate-500 font-medium whitespace-nowrap {{ !$hidePerPage ? 'sm:border-l sm:border-slate-200 sm:pl-3' : '' }}">
                    Menampilkan <strong class="text-slate-800">{{ number_format($paginator->firstItem()) }}</strong> - <strong class="text-slate-800">{{ number_format($paginator->lastItem()) }}</strong> dari <strong class="text-slate-800">{{ number_format($paginator->total()) }}</strong> data
                </span>
            @endif
        </div>

        {{-- Sisi Kanan Bawah: Pagination Controls (Maksimal 5 Elemen) --}}
        <div class="flex items-center justify-end">
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1 sm:gap-1.5 text-sm select-none">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage() || $lastPage <= 1)
                    <span class="inline-flex items-center gap-1.5 text-slate-300 cursor-not-allowed select-none px-2 py-1.5 font-medium text-sm">
                        <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 text-slate-800 hover:text-primary transition-colors px-2 py-1.5 font-medium text-sm cursor-pointer">
                        <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Previous
                    </a>
                @endif

                {{-- Pagination Elements (Maksimal 5) --}}
                @foreach ($pageElements as $idx => $elem)
                    {{-- "Three Dots" Separator --}}
                    @if ($elem === '...')
                        @php
                            $targetPage = ($idx === 0) 
                                ? max(1, $currentPage - 3) 
                                : min($lastPage, $currentPage + 3);
                        @endphp
                        <a href="{{ $paginator->url($targetPage) }}" 
                           title="Loncat ke halaman {{ $targetPage }}"
                           class="inline-flex items-center justify-center min-w-[28px] h-9 px-1 text-slate-400 hover:text-slate-700 font-bold select-none text-base tracking-widest cursor-pointer transition-colors">
                            &hellip;
                        </a>
                    @elseif ($elem == $currentPage)
                        <span aria-current="page" class="z-10 inline-flex items-center justify-center min-w-[38px] h-9 px-3 text-sm font-semibold text-slate-900 bg-white border border-slate-200/90 rounded-xl shadow-[0_1px_2px_rgba(0,0,0,0.05)] cursor-default select-none">
                            {{ $elem }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($elem) }}" class="inline-flex items-center justify-center min-w-[32px] h-9 px-2 text-sm font-medium text-slate-800 hover:text-primary hover:bg-slate-100/60 rounded-xl transition-colors">
                            {{ $elem }}
                        </a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 text-slate-800 hover:text-primary transition-colors px-2 py-1.5 font-medium text-sm cursor-pointer">
                        Next
                        <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 text-slate-300 cursor-not-allowed select-none px-2 py-1.5 font-medium text-sm">
                        Next
                        <svg class="w-3.5 h-3.5 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
@endif
