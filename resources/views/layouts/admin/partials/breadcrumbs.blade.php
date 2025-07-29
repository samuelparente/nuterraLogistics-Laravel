<nav aria-label="breadcrumb">
    <ol class="breadcrumb small justify-content-end">
        @foreach ($breadcrumbs as $breadcrumb)
            <li class="breadcrumb-item {{ $loop->last ? 'text-dark fw-bold' : '' }}">
                @if (!$loop->last && $breadcrumb['route'])
                    @php
                        $params = [];
                        if (isset($breadcrumb['params'])) {
                            foreach ($breadcrumb['params'] as $param) {
                                if (isset($$param)) {
                                    $params[$param] = $$param;
                                }
                            }
                        }
                    @endphp
                    <a href="{{ route($breadcrumb['route'], $params) }}">
                        @if (!empty($breadcrumb['icon']))
                            <i class="{{ $breadcrumb['icon'] }} me-1"></i>
                        @endif
                        {{ $breadcrumb['label'] }}
                    </a>
                @else
                    @if (!empty($breadcrumb['icon']))
                        <i class="{{ $breadcrumb['icon'] }} me-1"></i>
                    @endif
                    {{ $breadcrumb['label'] }}
                @endif
            </li>

            @if (!$loop->last)
                <li class="breadcrumb-separator">⏵</li>
            @endif
        @endforeach
    </ol>
</nav>
