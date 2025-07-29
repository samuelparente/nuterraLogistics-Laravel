@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | {{ $supplier->name }} (Pedido #{{ $order->id }})</h1>

    
    <!-- Botões de ação -->
    <div class="row mb-3">
        <div class="col-12 col-lg-12 text-end">
            <div class="mt-3 mb-3 action-buttons-header-mobile">

                {{-- Entrada com scanner (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.singleScanner', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-upc-scan"></i> Entrada com Scanner
                </a>

                {{-- Adicionar Produto Extra (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.single', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-plus-circle"></i> Adicionar Produto Extra
                </a>

                {{-- Voltar ao Painel de Pendentes --}}
                <a 
                    href="{{ route('receivings.pending') }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Entradas em Curso
                </a>

            </div>
        </div>
    </div>


    {{-- Breadcrumbs --}}
    @include('layouts.admin.partials.breadcrumbs', [
      'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

  
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Artigos Recebidos</h5></div>
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
                                <th class="text-center"><i class="bi bi-gear-fill table-icons" style="width:1.5rem;" title="Ações"></i></th>
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
                                        {{-- Mostrar lotes existentes --}}
                                        @php
                                            $totalReceived = $item->batches->sum('quantity');
                                        @endphp

                                        @if($item->batches->count())
                                            <div>
                                                <span class="badge bg-dark mt-1 mb-1" >Total: {{ $totalReceived }}</span>
                                            </div>

                                            @foreach ($item->batches as $batch)
                                            <span class="badge bg-light text-dark mt-1 mb-1 d-flex justify-content-between align-items-center px-2 py-2">
                                                <span class="text-start" style="width: 90%;">
                                                    {{ $batch->batch_number }} |
                                                    {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }} |
                                                    {{ $batch->quantity }}
                                                </span>

                                                <span class="ms-1" style="width: 10%;font-size:0.9rem;">
                                                    <a href="#" 
                                                        onclick="confirmDelete('batch-{{ $batch->id }}')" 
                                                        class="text-danger text-decoration-none"
                                                        title="Eliminar Lote" 
                                                        data-bs-toggle="tooltip">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>

                                                    <form id="delete-form-batch-{{ $batch->id }}" 
                                                        action="{{ route('receivings.batches.destroy', $batch->id) }}" 
                                                        method="POST" 
                                                        style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </span>
                                            </span>

                                        @endforeach

                                        @else
                                            <span class="text-muted small">Nenhum lote inserido.</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        
                                        <button 
                                            class="btn btn-sm btn-general btn-outline-secondary m-1"
                                            onclick="addBatch(this)"
                                            data-item-id="{{ $item->id }}"
                                            data-product-name="{{ $item->product_name }}"
                                            data-url="{{ route('receivings.items.batches.store', ['item' => $item->id]) }}"
                                            title="Adicionar Lote e Quantidade" 
                                            data-toggle="tooltip" data-placement="bottom"
                                            >
                                            
                                            <i class="bi bi-capsule"></i>
                                        </button>

                                        @if ($item->order_item_id == 0)
                                            <a class="btn btn-sm btn-general btn-outline-danger m-1" 
                                                href="#" 
                                                onclick="confirmDelete({{ $item->id }})" 
                                                title="Eliminar Lote" 
                                                data-toggle="tooltip" data-placement="bottom">                                            
                                                <i class="bi bi-trash table-icon-remove"></i>
                                            </a>
                                            <form id="delete-form-{{ $item->id }}" 
                                                action="{{ route('receivings.items.destroy', $item->id) }}" 
                                                method="POST" 
                                                style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <a class="btn btn-sm btn-outline-secondary btn-general text-muted disabled m-1" 
                                                href="#" 
                                                tabindex="-1" 
                                                aria-disabled="true"
                                                title="Item original. Não pode remover." 
                                                data-toggle="tooltip" data-placement="bottom">
                                                <i class="bi bi-trash table-icon-remove"></i>
                                            </a>                                     
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
               

                {{-- Formulário de receção --}}
                <form method="POST" action="{{ route('receivings.store', ['order' => $order->id, 'supplier' => $supplier->id]) }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                {{-- Notas --}}
                <div class="mb-3">
                    <label for="notes" class="form-label">Observações (opcional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $receiving->notes ?? '' }}</textarea>
                </div>
                    {{-- Ações --}}
                    <div class="d-flex gap-2">
                        {{-- Guardar (POST para store) --}}
                        <button type="submit" formaction="{{ route('receivings.store', [$order->id, $supplier->id]) }}" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar
                        </button>

                        {{-- Finalizar (POST para finalize) --}}
                        <button type="submit" formaction="{{ route('receivings.finalize', [$order->id, $supplier->id]) }}" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Finalizar Receção
                        </button>
                    </div>
                </form>
            </div>
        </div>
  </div>
</main>
<script>
    const csrfToken = '{{ csrf_token() }}';

    // $(function () {
    // $('[data-toggle="tooltip"]').tooltip()
    // })
</script>


@endsection
