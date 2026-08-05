@extends('layouts.admin.partials.master_admin') 

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Histórico</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('receivings.dashboard') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
        
        {{-- Breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">Histórico de Receções Finalizadas</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-condensed table-hover align-middle table-bordered">
                                <thead>
                                    <tr class="align-middle text-start">
                                        <th><i class="bi bi-hash table-icons" title="ID"></i></th>
                                        <th><i class="bi bi-calendar-check table-icons" title="Finalizado em"></i></th>
                                        <th><i class="bi bi-truck table-icons" title="Fornecedor"></i></th>
                                        <th><i class="bi bi-currency-exchange table-icons" title="Moeda e taxa de câmbio"></i></th>
                                        <th><i class="bi bi-receipt table-icons" title="Documento Sage"></i></th>
                                        <th><i class="bi bi-box-seam table-icons" title="Marcas"></i></th>
                                        <th><i class="bi bi-sticky table-icons" title="Notas"></i></th>
                                        <th><i class="bi bi-flag table-icons" title="Divergências"></i></th>
                                        <th class="text-center"><i class="bi bi-gear-fill table-icons" title="Ações"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($receivings as $receiving)
                                        <tr class="text-start">
                                            <td>{{ $receiving->id }}</td>
                                            <td>{{ $receiving->received_at?->format('d/m/Y H:i') }}</td>
                                            <td>{{ $receiving->supplier?->name }}</td>
                                            <td class="text-nowrap">
                                                <strong>{{ $receiving->fx_currency ?? 'EUR' }}</strong>
                                                @if (($receiving->fx_currency ?? 'EUR') === 'USD')
                                                    <div class="small text-muted">
                                                        1 USD = {{ $receiving->fx_rate_to_eur }} EUR
                                                    </div>
                                                @else
                                                    <div class="small text-muted">Sem conversão</div>
                                                @endif
                                            </td>
                                            <td>{{ $receiving->erp_document_reference ?: '—' }}</td>
                                            
                                            {{-- Marcas --}}
                                            <td>
                                                @foreach ($receiving->items->groupBy('brand_id') as $brandId => $brandItems)
                                                    @php
                                                        $brand = $brandItems->first()->brand;
                                                    @endphp
                                                    <span class="badge bg-primary-subtle text-dark d-block px-2 py-2 mt-1">
                                                        {{ $brand?->name ?? 'Sem marca' }}
                                                    </span>
                                                @endforeach
                                            </td>

                                            {{-- Notas --}}
                                            <td>{{ Str::limit($receiving->notes, 50) }}</td>

                                            {{-- Divergências por marca --}}
                                            <td>
                                                @foreach ($receiving->items->groupBy('brand_id') as $brandId => $brandItems)
                                                    @php
                                                        $hasDivergence = $brandItems->contains(function ($item) {
                                                            return is_null($item->order_item_id) || $item->received_qty != $item->ordered_qty;
                                                        });
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        @if ($hasDivergence)
                                                            <i class="bi bi-exclamation-circle-fill text-danger" title="Divergências encontradas"></i>
                                                            <a href="{{ route('receivings.divergences.brand', ['receiving' => $receiving->id, 'brand' => $brandId]) }}"
                                                               class="btn btn-sm btn-outline-warning btn-general"
                                                               title="Ver Divergências desta marca">
                                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                            </a>
                                                        @else
                                                            <i class="bi bi-check-circle-fill text-success" title="Sem divergências"></i>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </td>

                                            {{-- Ações --}}
                                            <td class="text-center">
                                                <a href="{{ route('receivings.details', ['receiving' => $receiving->id]) }}" 
                                                   class="btn btn-sm btn-outline-primary btn-general" 
                                                   title="Ver detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Nenhuma entrada de mercadorias finalizada encontrada.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.admin.partials.paginator', ['paginator' => $receivings])

    </div>
</main>

@endsection
