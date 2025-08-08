@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>PEDIDOS A FORNECEDORES</strong> | Em Aberto</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">

                    <a href="{{ route('lists.index') }}" class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
                        <i class="bi bi-cart-plus"></i> Listagens
                    </a>
                    <a class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-danger" href="#" onclick="confirmDelete({{ $order->id }})" title="Eliminar Pedido">
                        <i class="bi bi-trash"></i> Eliminar Pedido
                    </a>
                    <form id="delete-form-{{ $order->id }}" action="{{ route('orders.order.destroy', $order->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    {{-- voltar --}}
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('orders.dashboard') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

         <!-- Breadcrumbs -->
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        <!-- Tabela de Itens do Pedido -->
        
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Produtos</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-condensed table-hover align-middle table-bordered">
                        <thead>
                            <tr>
                                <th title="SKU"><i class="bi bi-hash table-icons"></i></th>
                                <th title="Código Barras"><i class="bi bi-upc-scan table-icons"></i></th>
                                <th title="Nome"><i class="bi bi-card-text table-icons"></i></th>
                                <th title="Marca"><i class="bi bi-bookmark table-icons"></i></th>
                                <th title="Fornecedor"><i class="bi bi-truck table-icons"></i></th>
                                <th class="text-center" title="Bonificações"><i class="bi bi-gift table-icons"></i></th>
                                <th title="Quantidade"><i class="bi bi-box-seam table-icons"></i></th>
                                <th class="text-center"><i class="bi bi-gear-fill table-icons" title="Ações"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $item)
                                <tr>
                                    <td><span class="badge bg-dark">{{ $item->product_sku }}</span></td>
                                    <td><span class="badge bg-dark">{{ $item->product_barcode }}</span></td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->brand->name ?? '' }}</td>
                                    <td>{{ $item->supplier->name ?? '' }}</td>

                                    <td class="text-center">
                                            @if (!empty($item->Bonuses) && collect($item->Bonuses)->isNotEmpty())
                                                @php
                                                    $popoverId = 'popover-' . $loop->index;

                                                    $popoverHtml = collect($item['Bonuses'])->map(function ($bonus) {
                                                        $desc = e($bonus['description'] ?? '');
                                                        $notes = !empty($bonus['notes']) ? e($bonus['notes']) : null;

                                                        return '<span class="badge bg-light text-dark d-block mb-1" style="font-size: 0.75rem;">'
                                                            . $desc . ($notes ? ' — ' . $notes : '') .
                                                            '</span>';
                                                    })->implode('');
                                                @endphp

                                                {{-- Botão para ativar popover --}}
                                                <span
                                                    class="badge bg-success"
                                                    role="button"
                                                    tabindex="0"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="focus"
                                                    data-bs-placement="top"
                                                    data-popover-content="{{ $popoverId }}"
                                                    data-bs-title="Bónus disponíveis"
                                                    data-bs-custom-class="custom-popover"
                                                >
                                                    <i class="bi bi-gift-fill"></i>
                                                </span>

                                                {{-- Conteúdo escondido com o HTML real --}}
                                                <div id="{{ $popoverId }}" class="d-none">
                                                    {!! $popoverHtml !!}
                                                </div>
                                            @endif

                                        </td>
                                    <td>
                                        <input type="number" 
                                            name="quantities[{{ $item->id }}]" 
                                            value="{{ $item->quantity }}" 
                                            class="form-control form-control-sm text-end d-block ms-auto" 
                                            style="width: 80px;" 
                                            min="1">
                                    </td>

                                    <td class="text-center">
                                        <a class="btn btn-sm btn-general btn-outline-danger" 
                                            href="#" 
                                            onclick="confirmDelete({{ $item->id }})" 
                                            title="Eliminar Item">
                                            <i class="bi bi-trash table-icon-remove"></i>
                                        </a>
                                        <form id="delete-form-{{ $item->id }}" 
                                              action="{{ route('orders.order.order_item.destroy', $item->id) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum produto no pedido.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <form method="POST" action="{{ route('orders.order.update', $order->id) }}">
                @csrf
                @method('PATCH')

                <div class="text-start mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-plus"></i> Enviar</button>
                </div>
            </form>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
            const contentId = el.getAttribute('data-popover-content');
            const contentElement = document.getElementById(contentId);
            const htmlContent = contentElement ? contentElement.innerHTML : '';

            new bootstrap.Popover(el, {
                html: true,
                content: htmlContent,
                container: 'body',
            });
        });
    });
</script>

@endsection
