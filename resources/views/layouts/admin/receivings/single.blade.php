@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ADICIONAR PRODUTO EXTRA</strong> | Receção #{{ $receiving->id }}</h1>

    @include('layouts.admin.partials.breadcrumbs', [
        'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

    {{-- Formulário de pesquisa --}}
    <div class="row mb-3">
        <div class="col-12 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Pesquisar por SKU ou Código de Barras</h5></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('receivings.items.single', $receiving->id) }}">
                        <input type="text" name="search" class="form-control" placeholder="SKU ou Código de Barras" value="{{ request('search') }}" autofocus>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($product))
             {{-- Card: Detalhes do Produto --}}
            <div class="col-12 col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Detalhes</h5></div>
                    <div class="card-body">
                        <p><i class="bi bi-card-text me-1"></i> <strong>{{ $product['ProductName'] }}</strong></p>
                        <p><i class="bi bi-hash me-1"></i> <strong>{{ $product['ItemID'] }}</strong></p>
                        <p><i class="bi bi-upc-scan me-1"></i> <strong>{{ $product['BarCode'] ?? '-' }}</strong></p>
                        <p><i class="bi bi-bookmark me-1"></i> <strong>{{ $product['BrandName'] }}</strong></p>
                        <p><i class="bi bi-truck me-1"></i> <strong>{{ $product['SupplierName'] }}</strong></p>
                        <p><i class="bi bi-box-seam me-1"></i> <strong>{{ $product['StockQty'] ?? '0' }}</strong></p>
                        <p><i class="bi bi-currency-euro me-1"></i> 
                            <strong>{{ isset($product['CostPrice']) ? number_format($product['CostPrice'], 2, ',', '.') . ' €' : '-' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Adicionar Produto</h5></div>
                    <div class="card-body">
                            <form method="POST" action="{{ route('receivings.items.addExtra', $receiving->id) }}">
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
                                <button type="submit" class="btn btn-success">Adicionar</button>
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
@if(isset($product))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // limpa o campo de pesquisa
      const searchInput = document.getElementById('search');
      if (searchInput) {
        searchInput.value = '';   // apaga texto
        searchInput.focus();      // mantém o foco para o próximo scan
      }
    });
  </script>
@endif

@endsection
