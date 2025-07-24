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
                        <table class="table table-condensed table-hover align-middle table-bordered">
                            <thead>
                                <tr class="align-middle">
                                    <th><i class="bi bi-card-text table-icons" title="Nome"></i></th>
                                    <th><i class="bi bi-truck table-icons" title="Fornecedor"></i></th>
                                    <th><i class="bi bi-bookmark table-icons" title="Marca"></i></th>
                                    <th><i class="bi bi-chat-left-text table-icons" title="Descrição"></i></th>
                                    <th><i class="bi bi-journal-text table-icons" title="Notas"></i></th>
                                    <th class="text-center"><i class="bi bi-gear-fill table-icons" title="Ações"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bonuses as $bonus)
                                    <tr>
                                        <td>{{ $bonus->name }}</td>
                                        <td>{{ $bonus->supplier?->name ?? '—' }}</td>
                                        <td>{{ $bonus->brand?->name ?? '—' }}</td>
                                        <td>{{ Str::limit($bonus->description, 50) ?? '—' }}</td>
                                        <td>{{ Str::limit($bonus->notes, 50) ?? '—' }}</td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-2">

                                                <!-- Ver Detalhes -->
                                                <a href="#" class="btn btn-sm btn-outline-success btn-general"
                                                onclick="event.preventDefault();showInfoSwal('Detalhes da Bonificação', `{!! nl2br(e($bonus->description ?? '—')) !!}<br><br>{!! nl2br(e($bonus->notes ?? '—')) !!}`)"
                                                title="Ver Detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <!-- Editar -->
                                                <a class="btn btn-sm btn-outline-warning btn-general" href="{{ route('bonuses.bonus.edit', $bonus->id) }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <!-- Eliminar -->
                                                <a class="btn btn-sm btn-outline-danger btn-general" href="#" onclick="confirmDelete({{ $bonus->id }})" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>

                                                <!-- Formulário oculto -->
                                                <form id="delete-form-{{ $bonus->id }}" 
                                                    action="{{ route('bonuses.bonus.destroy', $bonus->id) }}" 
                                                    method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum registo encontrado.</td>
                                    </tr>
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
