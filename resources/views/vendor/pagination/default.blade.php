@php
    $pattern_without_get = "~(.*)([\?|\&]page\=)([0-9]+)~";
    $pattern_with_get = "~(.*)(\?.*)([\?|\&]page\=)([0-9]+)~";
@endphp
@if ($paginator->hasPages())
    <ul class="pagination" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li>
                @if (preg_match($pattern_with_get,$paginator->previousPageUrl()))
                    <a href="{{ preg_replace($pattern_with_get, "$1/page/$4.html$2", $paginator->previousPageUrl()) }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                @else
                    <a href="{{ preg_replace($pattern_without_get, "$1/page/$3.html", $paginator->previousPageUrl()) }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                @endif
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                    @else
                        @if (preg_match($pattern_with_get,$url))
                        <li><a href="{{ preg_replace($pattern_with_get, "$1/page/$4.html$2", $url) }}">{{ $page }}</a></li>
                        @else
                        <li><a href="{{ preg_replace($pattern_without_get, "$1/page/$3.html", $url) }}">{{ $page }}</a></li>
                        @endif
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                @if (preg_match($pattern_with_get,$paginator->nextPageUrl()))
                <a href="{{ preg_replace($pattern_with_get, "$1/page/$4.html$2", $paginator->nextPageUrl()) }}" rel="prev" aria-label="@lang('pagination.next')">&rsaquo;</a>
                @else
                    <a href="{{ preg_replace($pattern_without_get, "$1/page/$3.html", $paginator->nextPageUrl()) }}" rel="prev" aria-label="@lang('pagination.next')">&rsaquo;</a>
                @endif
            </li>
        @else
            <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span aria-hidden="true">&rsaquo;</span>
            </li>
        @endif
    </ul>
@endif
