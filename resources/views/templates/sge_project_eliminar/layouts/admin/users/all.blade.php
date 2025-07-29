@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>UTILIZADORES</strong> | Ver Todos</h1>


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
                                <th class="d-none d-xl-table-cell">Função</th>
                                <th class="d-none d-xl-table-cell">Instituição</th>
                                <th>Estado</th>
                                <th class="d-none d-md-table-cell">Tipo de Utilizador</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="avatar-container">
                                            <img src="{{ $user->avatar 
                                                ? asset('assets/images/institutions/' . $user->institution_id . '/users/avatar/' . $user->avatar) 
                                                : asset('assets/images/general/avatar_default.png') }}" 
                                                class="avatar_profile">
                                        </div>  
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->email }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->institutionAssociation() ?? 'Sem papel na instituição' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->staff->position->description ?? '' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->institution->name ?? 'Sem instituição' }}</td>
                                    <td>
                                        @if($user->status)
                                        <span class="badge 
                                            @if($user->status->name == 'Ativo') bg-success 
                                            @elseif($user->status->name == 'Inativo') bg-danger 
                                            @elseif($user->status->name == 'Pendente') bg-warning 
                                            @endif">
                                            {{ $user->status->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sem status</span>
                                    @endif                                    
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $user->type->description ?? 'Sem tipo' }}</td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

	</div>
</main>

@endsection