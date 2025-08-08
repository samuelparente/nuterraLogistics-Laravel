@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>ADICIONAR PRODUTO</strong> | Individual</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    {{-- voltar --}}
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('lists.index') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
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
                        <form method="GET" action="{{ route('lists.single') }}">
                            <input type="text" name="search" class="form-control" placeholder="SKU ou Código de Barras" value="{{ request('search') }}" autofocus>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Pesquisar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        {{-- Produto encontrado --}}
        @if(isset($product))
                {{-- Card: Adição de Produto --}}
                <div class="col-12 col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Quantidade</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('orders.order.addSingle') }}">
                                @csrf

                                <input type="hidden" name="item_sku" value="{{ $product['ItemID'] }}">
                                <input type="hidden" name="supplier_id" value="{{ $product['SupplierID_Local'] }}">
                                <input type="hidden" name="brand_id" value="{{ $product['BrandID_Local'] }}">
                                <input type="hidden" name="product_name" value="{{ $product['ProductName'] }}">
                                <input type="hidden" name="bar_code" value="{{ $product['BarCode'] }}">

                                <div class="mb-3">
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Adicionar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

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

                {{-- Card: Estatísticas --}}
                <div class="col-12 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Estatísticas</h5></div>
                        <div class="card-body">
                            <p><i class="bi bi-clock-history me-1"></i> <strong>{{ $product['LastOutgoingDate'] ?? 'Sem registo' }}</strong></p>
                            <p><i class="bi bi-graph-up me-1"></i> <strong>30 dias: {{ $product['SalesLastPeriod'] ?? '0' }}</strong></p>
                            <p><i class="bi bi-calendar2-check me-1"></i> <strong>30 dias (período anterior): {{ $product['SalesPreviousYearPeriod'] ?? '0' }}</strong></p>
                            <p><i class="bi bi-lightbulb me-1"></i> <strong>{{ $product['SuggestedQty'] ?? '0' }}</strong></p>
                        </div>
                    </div>
                </div>

                {{-- Card: Bonificações --}}
                @if (!empty($product['HasBonus']) && $product['HasBonus'])
                    <div class="col-12 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Bonificações</h5></div>
                            <div class="card-body">
                                @foreach ($product['Bonuses'] as $bonus)
                                    <span class="badge bg-dark text-light d-inline-block mb-1"
                                        style="font-size: 0.75rem; max-width: 100%; white-space: normal; word-wrap: break-word; overflow-wrap: break-word;">
                                        {{ $bonus['description'] }}
                                        @if (!empty($bonus['notes']))
                                            <br>— {{ $bonus['notes'] }}
                                        @endif
                                    </span><br>

                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>


        @elseif(request('search'))
           
        @endif
        

    </div>
            <!-- <a href="{{ route('lists.index') }}" class="btn btn-outline-secondary" style="width:100px;">Cancelar</a> -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.querySelector('input[name="search"]');
            if (input) input.focus();
        });
    </script>

</main>
@endsection
