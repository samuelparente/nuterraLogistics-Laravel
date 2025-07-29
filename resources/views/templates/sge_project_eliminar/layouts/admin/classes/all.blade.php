@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>TURMAS</strong> | Ver Todas</h1>

		<div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Turmas</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Sala</th>
                                <th>Instituição</th>
                                <th>Estado</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes_group as $class_group)
                                <tr>
                                    <td>{{ $class_group->name }}</td>
                                    <td>{{ $class_group->classroom->name ?? 'Não Atribuída' }}</td>
                                    <td>{{ $class_group->institution->name }}</td>

                                    <td>
                                        @if($class_group->status)
                                        <span class="badge 
                                            @if($class_group->status->name == 'Ativo') bg-success 
                                            @elseif($class_group->status->name == 'Inativo') bg-danger 
                                            @elseif($class_group->status->name == 'Pendente') bg-warning 
                                            @endif">
                                            {{ $class_group->status->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sem status</span>
                                    @endif
                                    
                                    </td>
                                    <td>{{ $class_group->observations }}</td>
                                    <td>
                                        <a href="{{ route('classes.class.show', $class_group->id) }}" title="Ver">
                                            <i class="bi bi-eye-fill icon-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('classes.class.edit', $class_group->id) }}" title="Editar">
                                            <i class="bi bi-pencil-fill icon-edit"></i> 
                                        </a>
                        
                                        <a href="#" onclick="confirmDelete({{ $class_group->id }})" title="Eliminar">
                                            <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $class_group->id }}" action="{{ route('classes.class.destroy', $class_group->id) }}" method="POST" style="display: none;">
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
