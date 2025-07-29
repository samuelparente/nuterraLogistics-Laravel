@extends('layouts.master_admin') 

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>SALAS</strong> | Ver Todas</h1>

		<div class="row">
            @foreach($classrooms as $classroom)
                <div class="col-12 col-md-3">
                    <div class="card">
                        <img class="card-img-top" src="{{ $classroom->cover 
                            ? route('classroom.cover', ['id' => $classroom->id]) 
                            : asset('assets/images/general/classroom_default.png') }}" 
                            alt="Imagem da Sala">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $classroom->name }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                <strong>Crianças:</strong> {{ $classroom->activeChildren()->count() }}<br>
                                <strong>Funcionários:</strong> {{ $classroom->activeStaff()->count() }}<br>
                                <strong>Capacidade Máxima:</strong> {{ $classroom->max_capacity }}<br>
                                <strong>Instituição:</strong> {{ $classroom->institution->name }}
                            </p>
                            <a href="{{ route('classrooms.classroom.show', $classroom->id) }}" class="btn btn-primary">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-4">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tabela de Salas</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Crianças</th>
                                <th>Funcionários</th>
                                <th>Capacidade Máxima</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classrooms as $classroom)
                                <tr>
                                    <td>{{ $classroom->name }}</td>
                                    <td>{{ $classroom->activeChildren()->count() }}</td> <!-- Contagem de Crianças -->
                                    <td>{{ $classroom->activeStaff()->count() }}</td> <!-- Contagem de Funcionários -->
                                    <td>{{ $classroom->max_capacity }}</td>
                                    <td>
                                        @if($classroom->status)
                                            <span class="badge 
                                                @if($classroom->status->name == 'Ativo') bg-success 
                                                @elseif($classroom->status->name == 'Inativo') bg-danger 
                                                @elseif($classroom->status->name == 'Pendente') bg-warning 
                                                @endif">
                                                {{ $classroom->status->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Sem status</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('classrooms.classroom.show', $classroom->id) }}" title="Ver">
                                            <i class="bi bi-eye-fill icon-eye"></i>
                                        </a>
                                        <a href="{{ route('classrooms.classroom.edit', $classroom->id) }}" title="Editar">
                                            <i class="bi bi-pencil-fill icon-edit"></i> 
                                        </a>
                        
                                        <a href="#" onclick="confirmDelete({{ $classroom->id }})" title="Eliminar">
                                            <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $classroom->id }}" action="{{ route('classrooms.classroom.destroy', $classroom->id) }}" method="POST" style="display: none;">
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
