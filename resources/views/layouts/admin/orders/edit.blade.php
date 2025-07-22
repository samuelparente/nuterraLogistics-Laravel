@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Pedido #{{ $order->id }}</strong> | Carrinho</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">

                {{-- listagens --}}
                <a href="{{ route('lists.index') }}" class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
                    <i class="bi bi-cart-plus"></i> Listagens
                </a>
                {{-- eliminar pedido --}}
                <a class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-danger" href="#" onclick="confirmDelete({{ $order->id }})" title="Eliminar Pedido">
                    <i class="bi bi-trash"></i> Eliminar Pedido
                </a>
               
                <form id="delete-form-{{ $order->id }}" action="{{ route('orders.order.destroy', $order->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>

        <!-- Tabela de Itens do Pedido -->
        
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Itens no Pedido</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Código Barras</th>
                                <th>Nome</th>
                                <th>Marca</th>
                                <th>Fornecedor</th>
                                <th class="text-end">Quantidade</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_sku }}</td>
                                    <td>{{ $item->product_barcode }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->brand->name ?? '' }}</td>
                                    <td>{{ $item->supplier->name ?? '' }}</td>
                                    <td class="text-end">
                                        <input type="number" 
                                            name="quantities[{{ $item->id }}]" 
                                            value="{{ $item->quantity }}" 
                                            class="form-control form-control-sm text-end d-block ms-auto" 
                                            style="width: 80px;" 
                                            min="1">
                                    </td>
                                    <td class="text-end">
                                    {{-- eliminar item --}}
                                        <a class="btn btn-sm btn-outline-danger btn-remove-item-in-order" 
                                            href="#" 
                                            onclick="confirmDelete({{ $item->id }})" 
                                            title="Eliminar Item">
                                                <i class="bi bi-trash table-icon-remove"></i>
                                            </a>
                                    
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('orders.order.order_item.destroy', $item->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nenhum produto no pedido.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
<form method="POST" action="{{ route('orders.order.update', $order->id) }}">
            @csrf
            @method('PATCH')
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-save"></i> Finalizar Pedido
                </button>
            </div>
        

        </form>
    </div>
</main>
@endsection
