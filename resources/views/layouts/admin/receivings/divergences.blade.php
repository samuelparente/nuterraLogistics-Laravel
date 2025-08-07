@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">

    <h1 class="h3 mb-3">
      <strong>Divergências</strong> | Detalhes
    </h1>

    <!-- Ações -->
    <div class="row mb-3">
        <div class="col-12 text-end">
            <div class="mt-3 mb-3 action-buttons-header-mobile">
                <a 
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                    href="{{ route('receivings.history') }}">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
    
    {{-- Breadcrumbs --}}
    @include('layouts.admin.partials.breadcrumbs', [
        'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Produtos</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-condensed table-hover align-middle table-bordered">
            <thead>
              <tr class="align-middle text-start">
                <th><i class="bi bi-hash table-icons" title="ID"></i></th>
                <th><i class="bi bi-upc-scan table-icons" title="Código de Barras"></i></th>
                <th><i class="bi bi-box table-icons" title="Produto"></i></th>
                <th><i class="bi bi-send-plus table-icons" title="Encomendado"></i></th>
                <th><i class="bi bi-clipboard-check table-icons" title="Recebido"></i></th>
                <th><i class="bi bi-flag table-icons" title="Divergência"></i></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($divergentItems as $item)
                <tr>
                  <td>{{ $item->id }}</td>
                  <td>{{ $item->product_barcode }}</td>
                  <td>{{ $item->product_name }}</td>
                  <td>{{ $item->ordered_qty }}</td>
                  <td>{{ $item->received_qty }}</td>
                  <td>
                    @if (is_null($item->order_item_id))
                      <span class="badge bg-warning text-dark">Produto Extra</span>
                    @elseif ($item->received_qty > $item->ordered_qty)
                      <span class="badge bg-danger">+{{ $item->received_qty - $item->ordered_qty }}</span>
                    @elseif ($item->received_qty < $item->ordered_qty)
                      <span class="badge bg-danger">-{{ $item->ordered_qty - $item->received_qty }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Sem divergências nesta marca.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
