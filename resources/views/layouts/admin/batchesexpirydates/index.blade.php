@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>VALIDADES</strong> | Lotes a expirar</h1>

        {{-- Ações --}}
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    {{-- voltar --}}
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('backoffice.dashboard') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

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
                        <form method="GET" action="">
                            <div class="row align-items-end">

                                <div class="col-12 col-md-4">
                                    <label for="search" class="form-label">Pesquisar</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                           value="{{ $filters['search'] ?? '' }}"
                                           placeholder="Nome ou SKU">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="supplier_id" class="form-label">Fornecedor</label>
                                    <select name="supplier_id" id="supplier_id" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier['id'] }}"
                                                {{ ($filters['supplier_id'] ?? '') == $supplier['id'] ? 'selected' : '' }}>
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
                                            <option value="{{ $brand['id'] }}"
                                                {{ ($filters['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' }}>
                                                {{ $brand['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4 mt-3">
                                    <label for="months" class="form-label">Período (meses)</label>
                                    <input type="number" min="1" max="120" step="1"
                                           name="months" id="months" class="form-control"
                                           value="{{ $filters['months'] ?? 6 }}">
                                    <div class="form-text">Ex.: 6 = próximos 6 meses</div>
                                </div>

                                <div class="col-12 col-md-4 mt-3">
                                    <label for="warehouse_id" class="form-label">Armazém</label>
                                    <input disabled type="number" min="1" step="1"
                                           name="warehouse_id" id="warehouse_id" class="form-control"
                                           value="{{ $filters['warehouse_id'] ?? 1 }}">
                                </div>

                                <div class="col-12 col-md-4 mt-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                        <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                            <i class="bi bi-filter"></i> Filtrar
                                        </button>
                                        <a href="{{ route('batchesexpirydates.index') }}"
                                           class="btn btn-outline-secondary w-100 w-md-auto">
                                            <i class="bi bi-arrow-clockwise"></i> Limpar
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Lotes a expirar
                            <span class="text-muted" style="font-size:0.8rem;font-weight:normal;">
                                | Total linhas: {{ $products->count() }}
                                | SKUs: {{ $grouped->count() }}
                            </span>
                        </h5>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-condensed table-hover align-middle table-bordered">
                            <thead>
                                <tr class="align-middle">
                                    <th title="SKU"><i class="bi bi-hash table-icons"></i></th>
                                    <th title="Produto"><i class="bi bi-card-text table-icons"></i></th>
                                    <th title="Lote"><i class="bi bi-boxes table-icons"></i></th>
                                    <th title="Validade"><i class="bi bi-calendar-event table-icons"></i></th>
                                    <th class="text-end" title="Stock"><i class="bi bi-box-seam table-icons"></i></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($grouped as $sku => $rows)
                                    @php
                                        $rows = collect($rows)->values();
                                        $rowspan = $rows->count();
                                    @endphp

                                    @foreach($rows as $i => $row)
                                        @php
                                            $daysLeft = $row['DaysLeft'] ?? null;

                                            // Badge da validade
                                            $badgeStyle = 'background:#e2e8f0;color:#0f172a;'; // fallback (cinza)
                                            if ($daysLeft !== null) {
                                                if ($daysLeft <= 60) {
                                                    // Vermelho vivo com texto branco
                                                    $badgeStyle = 'background:#dc2626;color:#ffffff;'; // red-600
                                                } else {
                                                    // Amarelo
                                                    $badgeStyle = 'background:#facc15;color:#0f172a;'; // yellow-400
                                                }
                                            }

                                            $expDate = $row['ExpirationDate'] ?? '';
                                        @endphp

                                        <tr>
                                            @if($i === 0)
                                                <td rowspan="{{ $rowspan }}" class="align-top">
                                                    <span class="badge bg-dark">{{ $sku }}</span>
                                                </td>

                                                <td rowspan="{{ $rowspan }}" class="align-top">
                                                    {{ $row['ProductName'] ?? '' }}
                                                </td>
                                            @endif

                                            <td>
                                                <span class="badge bg-light text-dark">{{ $row['BatchNumber'] ?? '' }}</span>
                                            </td>

                                            <td>
                                                @if(!empty($expDate))
                                                    <span
                                                        class="badge"
                                                        style="{{ $badgeStyle }} border-radius:999px; padding:6px 10px; font-size:12px; letter-spacing:.2px;"
                                                        title="{{ $daysLeft !== null ? $daysLeft.' dias restantes' : '' }}"
                                                    >
                                                        {{ $expDate }}
                                                    </span>

                                                    @if($daysLeft !== null)
                                                        <span class="text-muted ms-2 small">
                                                            ({{ $daysLeft }} dias)
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">—</span>
                                                @endif
                                            </td>

                                            <td class="text-end">
                                                {{ (int)($row['StockQty'] ?? 0) }}
                                            </td>

                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Sem resultados.</td>
                                    </tr>
                                @endforelse
                                </tbody>


                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>
@endsection
