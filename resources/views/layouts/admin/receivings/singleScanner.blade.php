@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Entrada com Scanner</h1>
    <span class="text-muted">Pedido #{{ $receiving->id }} > {{ $receiving->supplier->name }} </span>
    <!-- Botões de ação -->
    <div class="row mb-3">
        <div class="col-12 col-lg-12 text-end">
            <div class="mt-3 mb-3 action-buttons-header-mobile">

                {{-- Voltar ao Painel de Entradas em Curso --}}
                <a 
                    href="{{ route('receivings.pending') }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Entradas em Curso
                </a>

            </div>
        </div>
    </div>

    @include('layouts.admin.partials.breadcrumbs', [
        'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

    {{-- Formulário de pesquisa --}}
    <div class="row mb-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Pesquisar por SKU ou Código de Barras</h5></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('receivings.items.singleScanner', $receiving->id) }}">
                        <input type="text" name="search" class="form-control" placeholder="SKU ou Código de Barras" value="{{ request('search') }}" autofocus>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($product))
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Adicionar Produto</h5></div>
                    <div class="card-body">
                            <form method="POST" action="{{ route('receivings.items.addSingleScanner', $receiving->id) }}">
                            @csrf

                            <input type="hidden" name="item_sku" value="{{ $product['ItemID'] }}">
                            <input type="hidden" name="supplier_id" value="{{ $product['SupplierID_Local'] }}">
                            <input type="hidden" name="brand_id" value="{{ $product['BrandID_Local'] }}">
                            <input type="hidden" name="product_name" value="{{ $product['ProductName'] }}">
                            <input type="hidden" name="bar_code" value="{{ $product['BarCode'] }}">
                            <label>Quantidade Recebida</label>
                            <input type="number" name="quantity" class="form-control" min="1" value="1" required>

                            <label class="mt-3">Lote</label>
                            <input type="text" name="batch_number" class="form-control" required>

                            <label class="mt-3">Data de Validade</label>
                            <input type="date" name="expiry_date" class="form-control" required>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <a href="{{ route('receivings.form', ['order' => $receiving->order_id, 'supplier' => $receiving->supplier_id]) }}" class="btn btn-outline-secondary">
        Voltar
    </a>
  </div>
</main>
@if (session('createProductUrl'))
    <script>
        const createProductUrl = "{{ session('createProductUrl') }}";
    </script>
@endif

@endsection
