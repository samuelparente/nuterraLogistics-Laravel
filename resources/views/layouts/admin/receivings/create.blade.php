@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
  <div class="container-fluid p-0">
    <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Criar Novo Produto</h1>

    <!-- Botões de ação -->
    <div class="row mb-3">
        <div class="col-12 col-lg-12 text-end">
            <div class="mt-3 mb-3 action-buttons-header-mobile">

                {{-- Entrada com scanner (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.singleScanner', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-upc-scan"></i> Entrada com Scanner
                </a>

                {{-- Adicionar Produto Extra (ativo apenas se a receção estiver em curso) --}}
                <a 
                    href="{{ route('receivings.items.single', $receiving->id) }}"
                    class="btn btn-sm me-2 action-buttons-header-mobile-inner {{ $receiving->status_id === 7 ? 'btn-outline-secondary' : 'btn-outline-secondary disabled' }}"
                    {{ $receiving->status_id === 7 ? '' : 'disabled' }}>
                    <i class="bi bi-plus-circle"></i> Adicionar Produto Extra
                </a>

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

    <div class="row mb-3">

            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Criar e adicionar Produto</h5></div>
                    <div class="card-body">
                            <form method="POST" action="{{ route('receivings.items.createAddProduct', $receiving->id)}}">
                            @csrf

                            <label class="mt-3">SKU</label>
                            <input type="text" name="item_sku" class="form-control" required>

                            <label class="mt-3">Código de Barras</label>
                            <input type="text" name="bar_code" class="form-control" required>

                            <label class="mt-3">Descrição Curta</label>
                            <input type="text" name="product_description" class="form-control" required>

                            <label class="mt-3">Descrição Completa</label>
                            <input type="text" name="product_full_description" class="form-control" required>

                            <label class="mt-3">Preço de Custo</label>
                            <input type="number" step="0.000001" min="0" name="pc" value="{{ old('pc') }}" class="form-control" required>
                            
                            <label for="moeda_id" class="form-label mt-3">Moeda</label>
                            <select name="moedaId" id="moeda_id" class="form-select" required>
                                <option value="EUR" @selected(old('moedaId', $receiving->fx_currency ?? 'EUR') === 'EUR')>EUR</option>
                                <option value="USD" @selected(old('moedaId', $receiving->fx_currency ?? 'EUR') === 'USD')>USD</option>
                            </select>

                            <label for="taxa_cambio" class="mt-3">Taxa de câmbio</label>
                            <input
                                id="taxa_cambio"
                                type="number"
                                step="0.000000000001"
                                min="0.000000000001"
                                value="{{ old('taxaCambio', $receiving->fx_rate_to_eur ?? '1.000000000000') }}"
                                name="taxaCambio"
                                class="form-control"
                                required
                            >
                            <div id="taxa_cambio_help" class="form-text">Para USD: 1 USD = taxa indicada em EUR.</div>

                            <label for="taxable_group_id" class="form-label mt-3">Taxa de IVA - Venda</label>
                            <select name="TaxableGroupID" id="taxable_group_id" class="form-select" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="1">Taxa Normal - 23%</option>
                                <option value="2">Taxa Intermédia - 13%</option>
                                <option value="3">Taxa Reduzida - 6%</option>
                                <option value="4">Taxa Isenta - 0%</option>
                            </select>

                            <label for="supplier_id" class="form-label mt-3">Fornecedor</label>
                            <select name="supplier_id" id="supplier_id" class="form-select" required>
                                <option value="" disabled @selected(! old('supplier_id', $receiving->supplier_id))>Selecione...</option>
                                @foreach ($suppliers as $supplier)
                                    <option
                                        value="{{ $supplier['id'] }}"
                                        @selected((int) old('supplier_id', $receiving->supplier_id) === (int) $supplier['id'])
                                    >
                                        {{ $supplier['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        
                            <label for="brand_id" class="form-label mt-3">Marca</label>
                            <select name="brand_id" id="brand_id" class="form-select" required>
                                <option value="" disabled @selected(! old('brand_id'))>Selecione...</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand['id'] }}" @selected((int) old('brand_id') === (int) $brand['id'])>
                                        {{ $brand['name'] }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="mt-3">Quantidade Recebida</label>
                            <input type="number" name="quantity" class="form-control" min="1" value="1" required>

                            <label class="mt-3">Lote</label>
                            <input type="text" name="batch_number" class="form-control" required>

                            <label class="mt-3">Data de Validade</label>
                            <input type="date" name="expiry_date" class="form-control" required>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary">Criar e Adicionar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>

    <a href="{{ route('receivings.form', ['order' => $receiving->order_id, 'supplier' => $receiving->supplier_id]) }}" class="btn btn-outline-secondary">
        Voltar
    </a>
  </div>
</main>

<script>
    (() => {
        const currency = document.getElementById('moeda_id');
        const rate = document.getElementById('taxa_cambio');
        const help = document.getElementById('taxa_cambio_help');

        const syncCurrency = (clearDefault = false) => {
            if (currency.value === 'EUR') {
                rate.value = '1.000000000000';
                rate.readOnly = true;
                help.textContent = 'Em EUR não é aplicada conversão cambial.';
                return;
            }

            rate.readOnly = false;

            if (clearDefault && Number(rate.value) === 1) {
                rate.value = '';
            }

            help.textContent = 'Introduza a taxa manual: 1 USD = taxa indicada em EUR.';
        };

        currency.addEventListener('change', () => syncCurrency(true));
        syncCurrency(false);
    })();
</script>
@endsection
