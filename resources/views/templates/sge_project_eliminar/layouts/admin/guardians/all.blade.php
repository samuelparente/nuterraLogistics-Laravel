@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>ENCARREGADOS DE EDUCAÇÃO</strong> | Ver Todos</h1>

		<div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Encarregados de Educação</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Instituição</th>
                                <th>Vínculo na Instituição</th>
                                <th>APP</th> 
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guardians as $guardian)
                                <tr>
                                    <td>   
                                        <div class="avatar-container">
                                            <img src="{{ $guardian->avatar 
                                            ? route('guardian.avatar', ['id' => $guardian->id]) 
                                            : asset('assets/images/general/avatar_default.png') }}" 
                                     class="avatar_profile">
                                
                                        </div>
                                    </td>
                                    <td>{{ $guardian->name }}</td>
                                    <td>{{ $guardian->institution->name }}</td>
                                    <td>Pertence à entidade?/Função</td>
                                    <td>Aceitar/Rejeitar app</td>
                                    <td>
                                        @if($guardian->status)
                                        <span class="badge 
                                            @if($guardian->status->name == 'Ativo') bg-success 
                                            @elseif($guardian->status->name == 'Inativo') bg-danger 
                                            @elseif($guardian->status->name == 'Pendente') bg-warning 
                                            @endif">
                                            {{ $guardian->status->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sem status</span>
                                    @endif
                                    
                                    </td>
                                    <td>
                                        <a href="{{ route('guardians.guardian.show', $guardian->id) }}" title="Ver perfil">
                                            <i class="bi bi-eye-fill icon-eye"></i>
                                        </a>
                                        <a href="{{ route('guardians.guardian.children.show', $guardian->id) }}" title="Ver Crianças associadas">
                                            <i class="bi bi-people-fill icon-people"></i>
                                        </a>
                                        <a href="{{ route('guardians.guardian.edit', $guardian->id) }}" title="Editar">
                                            <i class="bi bi-pencil-fill icon-edit"></i> 
                                        </a>
                        
                                        <a href="#" onclick="confirmDelete({{ $guardian->id }})" title="Eliminar">
                                            <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $guardian->id }}" action="{{ route('guardians.guardian.destroy', $guardian->id) }}" method="POST" style="display: none;">
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
