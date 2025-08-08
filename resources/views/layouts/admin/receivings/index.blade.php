@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Pendentes</h1>

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

        {{-- Breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        {{-- Tabela de Pedidos Ativos --}}
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">Pendentes de Entrada</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-condensed table-hover align-middle table-bordered">
                                <thead>
                                    <tr class="align-middle text-start">
                                        <th><i class="bi bi-hash table-icons" title="ID"></i></th>
                                        <th><i class="bi bi-calendar-event table-icons" title="Data"></i></th>
                                        <th><i class="bi bi-box-seam table-icons" title="Itens"></i></th>
                                        <th><i class="bi bi-truck table-icons" title="Fornecedor(es)"></i></th>
                                        <th class="text-center" style="width:1.5rem;"><i class="bi bi-gear-fill table-icons" title="Ações"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr class="text-start">
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $order->items->count() }}</td>
                                            <td>
                                               @if ($order->suppliers_without_receiving->count())
                                                    @foreach ($order->suppliers_without_receiving as $supplier)
                                                            <span class="badge bg-primary-subtle text-dark mt-2 mb-2 d-flex justify-content-between align-items-center px-2 py-2 mt-1">
                                                               
                                                                <span class="text-start" style="width: 90%;">
                                                                {{ $supplier->name }}</span>
                                                                
                                                                <span class="ms-1" style="width: 10%;font-size:0.9rem;">
                                                                    <form action="{{ route('receivings.start.bySupplier') }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                                                        <button type="submit" class="btn btn-sm btn-outline-primary btn-general" title="Abrir Entrada para {{ $supplier->name }}">
                                                                            <i class="bi bi-box-arrow-in-down"></i>
                                                                        </button>
                                                                    </form>
                                                                </span>

                                                            </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Todos os fornecedores já têm receção iniciada.</span>
                                                @endif

                                            </td>

                                            <td>
                                                @php
                                                    $openReceiving = \App\Models\Admin\Receiving::where('order_id', $order->id)
                                                        ->whereNull('received_at')
                                                        ->first();
                                                @endphp

                                                @if ($openReceiving)
                                                    {{-- Botão de Eliminar Entrada --}}
                                                    <!-- <a href="#" 
                                                    class="btn btn-sm btn-outline-danger btn-general" 
                                                    onclick="confirmDelete({{ $openReceiving->id }})" 
                                                    title="Eliminar Entrada de Mercadorias">
                                                        <i class="bi bi-trash"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $openReceiving->id }}" 
                                                        action="{{ route('receivings.destroy', $openReceiving->id) }}" 
                                                        method="POST" 
                                                        style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form> -->
                                                @else
                                                    {{-- Botão de abrir nova receção --}}
                                                    <form action="{{ route('receivings.start', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary btn-general" title="Abrir Entrada para Todos.">
                                                            <i class="bi bi-dropbox"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhum pedido ativo para dar entrada de mercadorias.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paginação --}}
        @include('layouts.admin.partials.paginator', ['paginator' => $orders])

    </div>
</main>

@endsection
