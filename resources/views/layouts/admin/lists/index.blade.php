@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>PEDIDOS A FORNECEDORES</strong> | Listagens</h1>

        <!-- Botões de ação-->
        <div class="row mb-3">
            <div class="col-12 col-lg-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">

                    {{-- Criar Pedido (desativado se já existir pedido em aberto) --}}
                    <form method="POST" action="{{ route('orders.order.createEmpty') }}" class="d-inline">
                        @csrf
                        <button 
                            type="submit" 
                            class="btn btn-sm me-2 action-buttons-header-mobile-inner
                                {{ $openOrder ? 'btn-outline-secondary disabled' : 'btn-outline-secondary' }}" 
                            {{ $openOrder ? 'disabled' : '' }}
                        >
                            <i class="bi bi-file-plus"></i> Criar Pedido
                        </button>
                    </form>

                    {{-- Adicionar individual (desativado se NÃO existir pedido em aberto) --}}
                    <a 
                        href="{{ route('lists.single')}}"
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $openOrder ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                        {{ $openOrder ? '' : 'disabled' }}>
                        <i class="bi bi-upc-scan"></i> Adicionar Individual
                    </a>

                    {{-- Adicionar Selecionados (desativado se NÃO existir pedido em aberto) --}}
                    <button 
                        id="btn-add-selected" 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $openOrder ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                        {{ $openOrder ? '' : 'disabled' }}
                        data-url="{{ route('orders.order.addItems') }}">
                        <i class="bi bi-cart-plus"></i> Adicionar Seleccionados
                    </button>

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

        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Filtros</h5></div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row align-items-end">
                                <div class="col-12 col-md-4">
                                    <label for="search" class="form-label">Pesquisar</label>
                                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Nome, SKU ou Código de Barras">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="supplier_id" class="form-label">Fornecedor</label>
                                    <select name="supplier_id" id="supplier_id" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier['id'] }}" {{ $filters['supplier_id'] == $supplier['id'] ? 'selected' : '' }}>
                                                {{ $supplier['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="brand_id" class="form-label">Marca</label>
                                    <select name="brand_id" id="brand_id" class="form-select">
                                        <option value="">Todas</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand['id'] }}" {{ $filters['brand_id'] == $brand['id'] ? 'selected' : '' }}>
                                                {{ $brand['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 mt-3">
                                    <label for="start_date" class="form-label">Data Início</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ old('start_date', $startDate) }}">
                                </div>

                                <div class="col-12 col-md-4 mt-3">
                                    <label for="end_date" class="form-label">Data Fim</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ old('end_date', $endDate) }}">
                                </div>

                                <div class="col-12 col-md-4 mt-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                        <button type="submit" class="btn btn-primary w-100 w-md-auto"><i class="bi bi-filter"></i> Filtrar</button>
                                        <a href="" class="btn btn-outline-secondary w-100 w-md-auto"><i class="bi bi-arrow-clockwise"></i> Limpar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      
        <!-- Tabela -->
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Lista de Produtos | <span class="text-muted" style="font-size:0.8rem;font-weight:normal;">Total encontrado: {{ count($products) }}</span></h5>
                        
                </div>
                    <div class="card-body table-responsive">
                        <table class="table table-condensed table-hover align-middle table-bordered">
                            <thead>
                                <tr class="align-middle">

                                    @php
                                        $sort = request('sort');
                                        $currentDirection = request('direction', 'asc');
                                    @endphp

                                    <th class="align-middle"><input class="custom-checkbox checkbox-large" type="checkbox" id="select-all" checked/></th>
                                    <th title="SKU"><i class="bi bi-hash table-icons"></i></th>
                                    <th title="Código de Barras"><i class="bi bi-upc-scan table-icons"></i></th>
                                    
                                    <th title="Nome do Produto">
                                        <a href="{{ request()->fullUrlWithQuery([
                                            'sort' => 'ProductName',
                                            'direction' => ($sort === 'ProductName' && $currentDirection === 'asc') ? 'desc' : 'asc'
                                        ]) }}"
                                        class="text-decoration-none text-dark d-flex justify-content-between align-items-center gap-2">

                                            <i class="bi bi-card-text table-icons"></i>

                                            <span class="d-inline-flex flex-column lh-1">
                                                <i class="bi bi-caret-up{{ $sort === 'ProductName' && $currentDirection === 'asc' ? '-fill text-primary' : ' text-muted' }}"></i>
                                                <i class="bi bi-caret-down{{ $sort === 'ProductName' && $currentDirection === 'desc' ? '-fill text-primary' : ' text-muted' }}"></i>
                                            </span>
                                        </a>
                                    </th>


                                   <th class="" title="Stock Disponível">
                                        <a href="{{ request()->fullUrlWithQuery([
                                            'sort' => 'StockQty',
                                            'direction' => $sort === 'StockQty' && $currentDirection === 'asc' ? 'desc' : 'asc'
                                        ]) }}" 
                                        class="text-decoration-none text-dark d-flex justify-content-between align-items-center gap-2">
                                            
                                            {{-- Ícone de stock à esquerda --}}
                                            <i class="bi bi-box-seam table-icons"></i>

                                            {{-- Setas de ordenação à direita --}}
                                            <span class="d-inline-flex flex-column lh-1">
                                                <i class="bi bi-caret-up{{ $sort === 'StockQty' && $currentDirection === 'asc' ? '-fill text-primary' : ' text-muted' }}"></i>
                                                <i class="bi bi-caret-down{{ $sort === 'StockQty' && $currentDirection === 'desc' ? '-fill text-primary' : ' text-muted' }}"></i>
                                            </span>
                                        </a>
                                    </th>


                                    <th title="Vendas no Período"><i class="bi bi-graph-up table-icons"></i></th>

                                  <th class="" title="Sugestão para 30 dias">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'SuggestedQty',
                                        'direction' => $sort === 'SuggestedQty' && $currentDirection === 'asc' ? 'desc' : 'asc'
                                    ]) }}" 
                                    class="text-decoration-none text-dark d-flex justify-content-between align-items-center gap-2">
                                        
                                        {{-- Ícone à esquerda --}}
                                        <i class="bi bi-lightbulb table-icons"></i>
                                        
                                        {{-- Setas à direita --}}
                                        <span class="d-inline-flex flex-column lh-1">
                                            <i class="bi bi-caret-up{{ $sort === 'SuggestedQty' && $currentDirection === 'asc' ? '-fill text-primary' : ' text-muted' }}"></i>
                                            <i class="bi bi-caret-down{{ $sort === 'SuggestedQty' && $currentDirection === 'desc' ? '-fill text-primary' : ' text-muted' }}"></i>
                                        </span>
                                    </a>
                                </th>


                                    <th title="Último Preço de Custo"><i class="bi bi-currency-euro table-icons"></i></th>
                                    <th class="text-center" title="Bónus disponível"><i class="bi bi-gift table-icons"></i></th>
                                    <th title="Marca"><i class="bi bi-bookmark table-icons"></i></th>
                                    <th title="Fornecedor"><i class="bi bi-truck table-icons"></i></th>
                                    <th class="text-center"><i class="bi bi-gear-fill table-icons" title="Ações"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr id="row-{{ $product['ItemID'] }}">
                                        <td><input type="checkbox" name="products[]" value="{{ $product['ItemID'] }}" class="custom-checkbox row-checkbox" checked></td>
                                        <td><span class="badge bg-dark">{{ $product['ItemID'] ?? '' }}</span></td>
                                        <td><span class="badge bg-dark">{{ $product['BarCode'] ?? '' }}</span></td>
                                        <td>{{ $product['ProductName'] ?? '' }}</td>
                                        <td class="text-end">{{ $product['StockQty'] ?? '' }}</td>
                                        <td>
                                            <div class="d-flex flex-column small text-muted">
                                                @php
                                                    $semVendas = empty($product['LastOutgoingDate']);
                                                @endphp

                                                <span class="{{ $semVendas ? 'text-danger' : 'text-muted' }}">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    {{ $semVendas ? 'Sem Vendas' : $product['LastOutgoingDate'] }}
                                                </span>
                                                <span>
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    Actual:
                                                    @if (!empty($product['SalesLastPeriod']) && $product['SalesLastPeriod'] > 0)
                                                        <strong>{{ $product['SalesLastPeriod'] }}</strong>
                                                    @else
                                                        <span class="text-danger">Sem Vendas</span>
                                                    @endif
                                                </span>
                                                <span>
                                                    <i class="bi bi-calendar2-check me-1"></i>
                                                    Anterior:
                                                    @if (!empty($product['SalesPreviousYearPeriod']) && $product['SalesPreviousYearPeriod'] > 0)
                                                        <strong>{{ $product['SalesPreviousYearPeriod'] }}</strong>
                                                    @else
                                                        <span class="text-danger">Sem Vendas</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                       <td class="text-end">
                                            <input type="number" 
                                                name="suggested_order_qty[{{ $product['ItemID'] }}]" 
                                                value="{{ $product['SuggestedQty'] ?? 0 }}" 
                                                class="form-control form-control-sm text-end ms-auto d-block" 
                                                min="0" 
                                                style="width: 60px;">
                                        </td>

                                        <td class="text-end">
                                            {{ isset($product['CostPrice']) ? number_format($product['CostPrice'], 2, ',', '.') . ' €' : '' }}
                                        </td>
                                        <td class="text-center">
                                            @if (!empty($product['Bonuses']) && collect($product['Bonuses'])->isNotEmpty())
                                                @php
                                                    $popoverId = 'popover-' . $loop->index;

                                                    $popoverHtml = collect($product['Bonuses'])->map(function ($bonus) {
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
                                        <td>{{ $product['BrandName'] ?? '' }}</td>
                                        <td>{{ $product['SupplierName'] ?? '' }}</td>


                                        <td>
                                            
                                            <button 
                                                class="btn btn-sm btn-outline-secondary btn-add-to-order"
                                                data-item-id="{{ $product['ItemID'] }}"
                                                data-bar-code="{{ $product['BarCode'] }}"
                                                data-product-name="{{ $product['ProductName'] }}"
                                                data-supplier-id="{{ $product['SupplierID_Local'] }}"
                                                data-brand-id="{{ $product['BrandID_Local'] }}"
                                                data-url="{{ route('orders.order.addItems') }}"
                                                title="Adicionar ao Pedido"
                                            >
                                                <i class="bi bi-cart-plus table-icon-add"></i>
                                            </button>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Nenhum produto encontrado.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- @include('layouts.admin.partials.paginator', ['paginator' => $products]) --}}
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
