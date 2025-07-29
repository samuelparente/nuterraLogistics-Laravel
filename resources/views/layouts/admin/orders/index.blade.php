@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>PEDIDOS</strong> | Histórico</h1>

        {{-- Breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Filtros</h5></div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('orders.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Estado</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                                {{ $status->label_pt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="date" class="form-label">Data</label>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                                </div>

                                <div class="col-md-4 d-grid">
                                    <button type="submit" class="btn btn-primary mb-1">Filtrar</button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela de Pedidos --}}
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">Histórico de Pedidos</h5></div>
                    <table class="table table-condensed table-hover align-middle table-bordered table-responsive">
                        <thead>
                            <tr class="align-middle text-start">
                                <th><i class="bi bi-hash table-icons" title="ID"></i></th>
                                <th><i class="bi bi-calendar-event table-icons" title="Data"></i></th>
                                <th><i class="bi bi-activity table-icons" title="Estado"></i></th>
                                <th><i class="bi bi-box-seam table-icons" title="Itens"></i></th>
                                <th><i class="bi bi-truck table-icons" title="Fornecedor(es)"></i></th>
                                <th><i class="bi bi-file-earmark-arrow-down table-icons" title="Ficheiros"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr class="text-start">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($order->status)
                                            <span class="badge bg-{{ $order->status->color ?? 'secondary' }}">
                                                {{ $order->status->label_pt }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">Desconhecido</span>
                                        @endif
                                    </td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum pedido encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Paginação --}}
        @include('layouts.admin.partials.paginator', ['paginator' => $orders])

    </div>
</main>

@endsection
