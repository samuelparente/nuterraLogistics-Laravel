@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Pedidos Ativos</h1>

        {{-- Breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        {{-- Tabela de Pedidos Ativos --}}
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">Pedidos Ativos</h5></div>
                    <div class="card-body">
                        <table class="table table-condensed table-hover align-middle table-bordered table-responsive">
                            <thead>
                                <tr class="align-middle text-start">
                                    <th><i class="bi bi-hash table-icons" title="ID"></i></th>
                                    <th><i class="bi bi-calendar-event table-icons" title="Data"></i></th>
                                    <th><i class="bi bi-box-seam table-icons" title="Itens"></i></th>
                                    <th><i class="bi bi-truck table-icons" title="Fornecedor(es)"></i></th>
                                    <th><i class="bi bi-file-earmark-arrow-down table-icons" title="Ficheiros"></i></th>
                                    <th><i class="bi bi-box-arrow-in-down table-icons" title="Ações"></i></th>
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
                                            @if($order->files && is_array($order->files))
                                                @foreach($order->files as $file)
                                                    <a href="{{ route('orders.downloadFile', ['path' => $file['path']]) }}"
                                                    class="btn btn-sm btn-outline-primary mb-1" download>
                                                        <i class="bi bi-download"></i> {{ $file['filename'] }}
                                                    </a><br>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Sem ficheiros</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="" class="btn btn-sm btn-success">
                                                <i class="bi bi-box-arrow-in-down"></i> Dar Entrada
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum pedido ativo por rececionar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Paginação --}}
        @include('layouts.admin.partials.paginator', ['paginator' => $orders])

    </div>
</main>

@endsection
