@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Em Curso</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    {{-- voltar --}}
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('receivings.dashboard') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

    @include('layouts.admin.partials.breadcrumbs', [
      'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

    <div class="card">
      <div class="card-header"><h5 class="card-title mb-0">Entradas em Curso</h5></div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-condensed table-hover table-bordered">
            <thead>
              <tr>
                <th><i class="bi bi-hash table-icons" title="ID"></i></th>
                <th><i class="bi bi-calendar-event table-icons" title="Data do pedido"></i></th>
                <th><i class="bi bi-truck table-icons" title="Fornecedor(es)"></i></th>
                <!-- <th title="Estado"><i class="bi bi-activity table-icons"></i></th> -->
                <th class="text-center" style="width:1.5rem;"><i class="bi bi-gear-fill table-icons" title="Ações"></i></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($receivings as $receiving)
                <tr>
                  <td>#{{ $receiving->order->id }}</td>
                  <td>{{ $receiving->order->created_at->format('d/m/Y H:i') }}</td>
                  <td>{{ $receiving->supplier->name }}</td>
                  <!-- <td>{{ $receiving->items->count() }}</td> -->
                  <!-- <td>
                      @if($receiving->status)
                          <span class="badge bg-{{ $receiving->status->color ?? 'secondary' }}">
                              {{ $receiving->status->label_pt }}
                          </span>
                      @else
                          <span class="badge bg-light text-muted">Desconhecido</span>
                      @endif
                  </td> -->
                  <td>
                    <div class="d-inline-flex align-items-center gap-2">

                      <a href="{{ route('receivings.form', ['order' => $receiving->order_id, 'supplier' => $receiving->supplier_id]) }}"
                        class="btn btn-sm btn-outline-secondary btn-general" title="Receber os Produtos do Fornecedor">
                        <i class="bi bi-dropbox"></i>
                      </a>
                      {{-- Botão de Eliminar Entrada --}}
                      <a href="#" 
                      class="btn btn-sm btn-outline-danger btn-general" 
                      onclick="confirmDelete({{ $receiving->id }})" 
                      title="Eliminar Entrada de Mercadorias">
                          <i class="bi bi-trash"></i>
                      </a>

                      <form id="delete-form-{{ $receiving->id }}" 
                          action="{{ route('receivings.destroy', $receiving->id) }}" 
                          method="POST" 
                          style="display: none;">
                          @csrf
                          @method('DELETE')
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center">Nenhum pedido pendente.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
