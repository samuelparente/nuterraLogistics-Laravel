@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>UTILIZADORES</strong> | Ver Todos</h1>
        <!-- Botões de ação-->
        <div class="row mb-3">
            <div class="col-12 col-lg-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    <a href="{{ route('users.user.create') }}" class="btn btn-sm btn-outline-secondary me-2 action-buttons-header-mobile-inner">
                        <i class="bi bi-plus-circle-fill me-1"></i> Criar Novo
                    </a>
                </div>   
            </div>
        </div>
        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])

        <!-- Card: Filtros (unificado: pesquisa + filtros) -->
        <div class="row mb-3">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filtros</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('users.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Nome ou Email</label>
                                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Pesquisar por nome ou email">
                                </div>

                                <div class="col-md-3">
                                    <label for="role" class="form-label">Papel</label>
                                    <select name="role" id="role" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                                {{ $roleLabelsMap[$role->name] ?? ucfirst(str_replace('-', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="status" class="form-label">Estado</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}" {{ request('status') == (string) $status->id ? 'selected' : '' }}>
                                                {{ $status->label_pt }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-primary mb-1">Filtrar</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

		<!-- Tabela de utilizadores -->
		<div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Utilizadores</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th class="d-none d-xl-table-cell">Email</th>
                                <th class="d-none d-xl-table-cell">Papel</th>
                                <th class="d-none d-xl-table-cell">Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="avatar-container">
                                            <img
                                                src="{{ $user->avatar
                                                    ? asset('storage/images/general/avatars/' . $user->avatar)
                                                    : asset('images/general/avatars/avatar_default.png') }}"
                                                alt="Avatar"
                                                class="avatar_profile">
                                        </div>  
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->email }}</td>
                                    <td class="d-none d-xl-table-cell">
                                        @foreach ($user->roleLabels() as $roleLabel)
                                            <span class="badge bg-info">{{ $roleLabel }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($user->status)
                                            <span class="badge bg-{{ $user->status->color ?? 'secondary' }}">
                                                {{ $user->status->label_pt }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">Sem estado</span>
                                        @endif
                                    </td>
                                    <td> 
                                        <a href="{{ route('users.user.edit', $user->id) }}" title="Editar">
                                            <i class="bi bi-pencil-fill icon-edit"></i>
                                        </a>
                                        <a href="#" onclick="confirmDelete({{ $user->id }})" title="Eliminar">
                                            <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.user.destroy', $user->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum utilizador encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        @include('layouts.admin.partials.paginator', ['paginator' => $users])
	</div>
</main>

@endsection
