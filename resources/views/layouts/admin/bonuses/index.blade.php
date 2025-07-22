@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>BONIFICAÇÕES</strong> | Ver Todas</h1>

        <!-- Botão Criar -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ route('bonuses.bonus.create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-plus-circle-fill me-1"></i> Criar Nova
                </a>
            </div>
        </div>

        <!-- Breadcrumbs -->
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        <!-- Card de Filtros -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Filtros</h5></div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('bonuses.index') }}">
                            <div class="row align-items-end">
                                <div class="col-12 col-md-4">
                                    <label for="search" class="form-label">Nome</label>
                                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Pesquisar por nome">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="supplier_id" class="form-label">Fornecedor</label>
                                    <select name="supplier_id" id="supplier_id" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="brand_id" class="form-label">Marca</label>
                                    <select name="brand_id" id="brand_id" class="form-select">
                                        <option value="">Todas</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 mt-3">
                                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                        <button type="submit" class="btn btn-primary w-100 w-md-auto">Filtrar</button>
                                        <a href="{{ route('bonuses.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">Limpar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card da Tabela -->
        <div class="row">
            <div class="col-12">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">Lista de Bonificações</h5></div>
                    <div class="card-body table-responsive"> <!-- SCROLL MOBILE -->
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th class="">Fornecedor</th>
                                    <th class="">Marca</th>
                                    <th class="">Descrição</th>
                                    <th class="">Notas</th>
                                    <th class=""></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bonuses as $bonus)
                                    <tr>
                                        <td>{{ $bonus->name }}</td>
                                        <td class="">{{ $bonus->supplier?->name ?? '—' }}</td>
                                        <td class="">{{ $bonus->brand?->name ?? '—' }}</td>
                                        <td class="">{{ Str::limit($bonus->description, 50) ?? '—' }}</td>
                                        <td class="">{{ Str::limit($bonus->notes, 50) ?? '—' }}</td>
                                        <td class="text-end">
                                            @php
                                                $infoHtml = ($bonus->description ?? '—') . "<br><br>" . ($bonus->notes ?? '—');
                                            @endphp

                                            <div class="d-inline-flex align-items-center">
                                                <!-- Ver Detalhes -->
                                                <a href="#" onclick="event.preventDefault();showInfoSwal('Detalhes da Bonificação', `{!! $infoHtml !!}`)" title="Ver Detalhes">
                                                    <i class="bi bi-eye-fill text-info icon-view"></i>
                                                </a>

                                                <!-- Editar -->
                                                <a href="{{ route('bonuses.bonus.edit', $bonus->id) }}" title="Editar">
                                                    <i class="bi bi-pencil-fill icon-edit"></i>
                                                </a>

                                                <!-- Eliminar -->
                                                <a href="#" onclick="confirmDelete({{ $bonus->id }})" title="Eliminar">
                                                    <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                                </a>

                                                <!-- Formulário de Eliminação (oculto) -->
                                                <form id="delete-form-{{ $bonus->id }}" action="{{ route('bonuses.bonus.destroy', $bonus->id) }}" method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Nenhuma registo encontrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         @include('layouts.admin.partials.paginator', ['paginator' => $bonuses])
    </div>
</main>
@endsection
