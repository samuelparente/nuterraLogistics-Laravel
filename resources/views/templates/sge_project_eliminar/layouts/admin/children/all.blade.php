@extends('layouts.master_admin')
@section('content_admin')
<main class="content">
	<div class="container-fluid p-0">
		<h1 class="h3 mb-3"><strong>CRIANÇAS</strong> | Ver Todas</h1>
		<div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Crianças</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Nome</th>
                                <th class="d-none d-xl-table-cell">Data de Nascimento</th>
                                <th class="d-none d-xl-table-cell">Instituição</th>
                                <th>Estado</th>
                                <th class="d-none d-md-table-cell">Encarregado de Educação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($children as $child)
                                <tr>
                                    <td>   
                                        <div class="avatar-container">
                                            <img src="{{ $child->avatar 
                                            ? route('child.avatar', ['id' => $child->id]) 
                                            : asset('assets/images/general/avatar_default.png') }}" 
                                            class="avatar_profile">
                                        </div>
                                    </td>
                                    <td>{{ $child->name }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $child->dob }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $child->institution->name ?? 'Sem instituição' }}</td>
                                    <td>
                                        @if($child->status)
                                        <span class="badge 
                                            @if($child->status->name == 'Ativo') bg-success 
                                            @elseif($child->status->name == 'Inativo') bg-danger 
                                            @elseif($child->status->name == 'Pendente') bg-warning 
                                            @endif">
                                            {{ $child->status->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sem Estado</span>
                                    @endif   
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $child->guardian->name ?? 'Sem Encarregado de Educação' }}</td>
                                    <td>
                                        <a href="{{ route('children.child.show', $child->id) }}" title="Ver perfil">
                                            <i class="bi bi-eye-fill icon-eye"></i>
                                        </a>
                                        <a href="{{ route('children.child.edit', $child->id) }}" title="Editar">
                                            <i class="bi bi-pencil-fill icon-edit"></i>
                                        </a>
                                        <a href="#" onclick="confirmDelete({{ $child->id }})" title="Eliminar">
                                            <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $child->id }}" action="{{ route('children.child.destroy', $child->id) }}" method="POST" style="display: none;">
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