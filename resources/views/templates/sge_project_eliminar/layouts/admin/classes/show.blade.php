@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>TURMAS</strong> | Detalhe</h1>
        <div class="row">
            <!-- Card: Informações da turma -->
            <div class="col-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $class_group->name }}</h5>
                    </div>
                    <div class="card-body">
                        <p>Associada à sala {{ $class_group->classroom->name ?? '' }}</p>
                        <a href="{{ route('classrooms.classroom.show', $class_group->classroom->id) }}" class="btn btn-primary">Ver Detalhes da Sala</a>

                    </div>
                </div>
            </div>
        </div>
		<div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Crianças</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Estado</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($children as $child)
                                <tr>
                                    <td>{{ $child->name }}</td>
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
                                        <span class="badge bg-secondary">Sem status</span>
                                    @endif
                                    
                                    </td>
                                    <td>{{ $child->observations }}</td>
                                    <td>
                                        <a href="{{ route('children.child.show', $child->id) }}" title="Ver">
                                            <i class="bi bi-eye-fill icon-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Funcionários</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Função</th>
                                <th>Estado</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class_group_staff as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->position->description ?? 'Sem função' }}</td> <!-- Correção aqui -->

                                <td>
                                    @if($employee->status)
                                        <span class="badge 
                                            @if($employee->status->name == 'Ativo') bg-success 
                                            @elseif($employee->status->name == 'Inativo') bg-danger 
                                            @elseif($employee->status->name == 'Pendente') bg-warning 
                                            @endif">
                                            {{ $employee->status->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sem status</span>
                                    @endif
                                </td>

                                <td>{{ $employee->observations }}</td>

                                <td>
                                    <a href="{{ route('staff.employee.show', $employee->id) }}" title="Ver">
                                        <i class="bi bi-eye-fill icon-eye"></i>
                                    </a>
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
