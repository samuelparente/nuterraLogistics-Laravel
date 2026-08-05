@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Detalhes</h1>

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

    <div class="card mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Documento e câmbio</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <div class="text-muted small">Fornecedor</div>
                    <strong>{{ $receiving->supplier?->name ?? '—' }}</strong>
                </div>
                <div class="col-12 col-md-3">
                    <div class="text-muted small">Moeda</div>
                    <strong>{{ $receiving->fx_currency ?? 'EUR' }}</strong>
                </div>
                <div class="col-12 col-md-3">
                    <div class="text-muted small">Taxa usada</div>
                    <strong>
                        @if (($receiving->fx_currency ?? 'EUR') === 'USD')
                            1 USD = {{ $receiving->fx_rate_to_eur }} EUR
                        @else
                            Sem conversão
                        @endif
                    </strong>
                </div>
                <div class="col-12 col-md-3">
                    <div class="text-muted small">Documento Sage</div>
                    <strong>{{ $receiving->erp_document_reference ?: '—' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Produtos Recebidos</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-condensed table-hover align-middle table-bordered">
                    <thead>
                        <tr>
                            <th title="SKU"><i class="bi bi-hash table-icons"></i></th>
                            <th title="Código Barras"><i class="bi bi-upc-scan table-icons"></i></th>
                            <th title="Nome"><i class="bi bi-card-text table-icons"></i></th>
                            <th title="Encomendado"><i class="bi bi-send-plus table-icons"></i></th>
                            <th title="Recebido"><i class="bi bi-clipboard-check table-icons"></i></th>
                            <th>Preço de origem</th>
                            <th>Preço enviado ao Sage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->product_sku }}</td>
                                <td>{{ $item->product_barcode }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td style="width:100px;">{{ $item->ordered_qty }}</td>
                                <td>
                                    @php
                                        $totalReceived = $item->batches->sum('quantity');
                                        $qty_error = $totalReceived != $item->ordered_qty;
                                    @endphp

                                    @if($item->batches->count())
                                        <div>
                                            <span class="badge bg-dark mt-1 mb-1">Total: {{ $totalReceived }}</span>
                                            @if($qty_error)
                                                <span class="badge bg-danger mt-1 mb-1" title="Quantidade diferente da encomendada"><i class="bi bi-exclamation-triangle"></i></span>
                                            @else
                                                <span class="badge bg-success mt-1 mb-1" title="Quantidade correta"><i class="bi bi-check-circle"></i></span>
                                            @endif
                                        </div>

                                        @foreach ($item->batches as $batch)
                                            <span class="badge bg-light text-dark mt-1 mb-1 d-block">
                                                {{ $batch->batch_number }} |
                                                {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }} |
                                                {{ $batch->quantity }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Nenhum lote inserido.</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if ($item->fx_source_unit_price !== null)
                                        {{ $item->fx_source_unit_price }} {{ $item->fx_currency ?? 'EUR' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{ $item->fx_unit_price_eur !== null ? $item->fx_unit_price_eur . ' EUR' : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Notas --}}
            <div class="mb-3 mt-4">
                <label for="notes" class="form-label">Observações</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" readonly>{{ $receiving->notes ?? '' }}</textarea>
            </div>

            
        </div>
    </div>
  </div>
</main>
@endsection
