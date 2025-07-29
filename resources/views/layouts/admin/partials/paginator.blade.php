@if ($paginator->total() > 0)
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">

        {{-- Primeira Página --}}
        <li class="page-item {{ ($paginator->onFirstPage()) ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Primeira">
                <i class="bi bi-chevron-bar-left"></i>
            </a>
        </li>

        {{-- Anterior --}}
        <li class="page-item {{ ($paginator->onFirstPage()) ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Anterior">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        {{-- Páginas --}}
        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $range = 2; // quantas páginas mostrar antes/depois
            $elements = [];

            if ($last <= 7) {
                // Se forem poucas páginas, mostra tudo
                $elements = range(1, $last);
            } else {
                $elements[] = 1;

                if ($current - $range > 2) {
                    $elements[] = '...';
                }

                $start = max(2, $current - $range);
                $end = min($last - 1, $current + $range);

                for ($i = $start; $i <= $end; $i++) {
                    $elements[] = $i;
                }

                if ($current + $range < $last - 1) {
                    $elements[] = '...';
                }

                $elements[] = $last;
            }
        @endphp

        @foreach ($elements as $page)
            @if ($page === '...')
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @else
                <li class="page-item {{ $paginator->currentPage() == $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endif
        @endforeach


        {{-- Seguinte --}}
        <li class="page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Seguinte">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>

        {{-- Última Página --}}
        <li class="page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Última">
                <i class="bi bi-chevron-bar-right"></i>
            </a>
        </li>

    </ul>
</nav>

<div class="text-center mt-2">
    <small class="text-muted">
        A mostrar {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados.
    </small>
</div>
@else
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class="page-item disabled">
            <a class="page-link" href="#"><i class="bi bi-chevron-bar-left"></i></a>
        </li>
        <li class="page-item disabled">
            <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
        </li>
        <li class="page-item active">
            <a class="page-link" href="#">1</a>
        </li>
        <li class="page-item disabled">
            <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
        </li>
        <li class="page-item disabled">
            <a class="page-link" href="#"><i class="bi bi-chevron-bar-right"></i></a>
        </li>
    </ul>
</nav>

<div class="text-center mt-2">
    <small class="text-muted">
        Não foram encontrados resultados.
    </small>
</div>
@endif
