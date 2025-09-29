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
          <a 
            href="{{ route('receivings.pending') }}"
            class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
            <i class="bi bi-box"></i> Entradas em Curso
          </a>

          <a 
            class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary" 
            href="{{ route('receivings.form', ['order' => $receiving->order_id, 'supplier' => $receiving->supplier_id]) }}">
            <i class="bi bi-arrow-left"></i> Voltar
          </a>
        </div>
      </div>
    </div>

    @include('layouts.admin.partials.breadcrumbs', [
      'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
    ])

    <div class="row mb-3">
      {{-- PESQUISA --}}
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header"><h5 class="card-title mb-0">Pesquisar por SKU ou Código de Barras</h5></div>
          <div class="card-body">
            <form method="GET" action="{{ route('receivings.items.singleScanner', $receiving->id) }}" novalidate>
              <label for="search" class="form-label">SKU ou Código de Barras</label>
              <input 
                type="text" 
                id="search" 
                name="search" 
                class="form-control"
                placeholder="SKU ou Código de Barras"
                value="{{ request('search') }}"
                autofocus
              >
              <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-search"></i> Pesquisar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- ADICIONAR ITEM (MOSTRA APÓS PESQUISA COM PRODUTO) --}}
      @if(isset($product))
        <div class="col-12 col-lg-6">
          <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Adicionar</h5></div>
            <div class="card-body">
              <form 
                method="POST" 
                action="{{ route('receivings.items.addSingleScanner', $receiving->id) }}" 
                novalidate
              >
                @csrf

                {{-- Campos ocultos vindos do produto --}}
                <input type="hidden" name="item_sku" value="{{ old('item_sku', $product['ItemID'] ?? '') }}">
                <input type="hidden" name="supplier_id" value="{{ old('supplier_id', $product['SupplierID_Local'] ?? '') }}">
                <input type="hidden" name="brand_id" value="{{ old('brand_id', $product['BrandID_Local'] ?? '') }}">
                <input type="hidden" name="product_name" value="{{ old('product_name', $product['ProductName'] ?? '') }}">
                <input type="hidden" name="bar_code" value="{{ old('bar_code', $product['BarCode'] ?? '') }}">

                {{-- Quantidade --}}
                <label for="quantity" class="form-label">Quantidade</label>
                <input 
                  type="number" 
                  id="quantity" 
                  name="quantity" 
                  class="form-control @error('quantity') is-invalid @enderror" 
                  min="1" 
                  value="{{ old('quantity', 1) }}" 
                  required
                >
                @error('quantity')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Lote --}}
                <label for="batch_number" class="form-label mt-3">Lote</label>
                <input 
                  type="text" 
                  id="batch_number" 
                  name="batch_number" 
                  class="form-control @error('batch_number') is-invalid @enderror" 
                  value="{{ old('batch_number') }}" 
                  maxlength="255"
                  required
                >
                @error('batch_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- Data de Validade --}}
                <label for="expiry_date" class="form-label mt-3">Data de Validade</label>
                <input 
                  type="date" 
                  id="expiry_date" 
                  name="expiry_date" 
                  class="form-control @error('expiry_date') is-invalid @enderror" 
                  value="{{ old('expiry_date') }}" 
                  required
                >
                @error('expiry_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="text-end mt-3">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-journal-plus"></i> Adicionar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</main>

@if (session('createProductUrl'))
  <script>
    const createProductUrl = "{{ session('createProductUrl') }}";
  </script>
@endif
@endsection
