@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Pendentes</h1>

        {{-- Breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        {{-- Tabela de Pedidos Ativos --}}
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">Pedidos a fornecedor ativos</h5></div>
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
                                                @php
                                                    $suppliers = $order->items
                                                        ->filter(fn($item) => $item->supplier)
                                                        ->pluck('supplier.name')
                                                        ->unique();
                                                @endphp

                                                @if ($suppliers->count())
                                                    @foreach ($suppliers as $supplierName)
                                                        <span class="badge bg-light text-dark mb-1">{{ $supplierName }}</span><br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Sem fornecedor</span>
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
                                                    <a href="#" 
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
                                                    </form>
                                                @else
                                                    {{-- Botão de abrir nova receção --}}
                                                    <form action="{{ route('receivings.start', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary btn-general" title="Abrir Entrada de Mercadorias">
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
