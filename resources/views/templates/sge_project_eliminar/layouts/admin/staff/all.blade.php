@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>FUNCIONÁRIOS</strong> | Ver Todos</h1>

        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Funcionários</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th class="d-none d-xl-table-cell">Cargo</th>
                                <th class="d-none d-xl-table-cell">Instituição</th>
                                <th class="d-none d-xl-table-cell">Turmas</th>
                                <th class="d-none d-xl-table-cell">Salas</th>
                                <th>Estado</th>
                                <th class="d-none d-md-table-cell">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $employee)
                                <tr>
                                    <td>   
                                        <div class="avatar-container">
                                            <img src="{{ $employee->avatar 
                                            ? route('employee.avatar', ['id' => $employee->id]) 
                                            : asset('assets/images/general/avatar_default.png') }}" 
                                            class="avatar_profile">
                                        </div>
                                    </td>
                                    <td>{{ $employee->name }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $employee->position->description ?? 'Sem cargo' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $employee->institution->name ?? 'Sem instituição' }}</td>
                                    <td class="d-none d-xl-table-cell">
                                        @if($employee->classGroups->isEmpty())
                                        <p>Nenhuma turma associada.</p>
                                    @else
                                        @foreach($employee->classGroups as $classGroup)
                                            <p>{{ $classGroup->name }}</p>
                                        @endforeach
                                    @endif
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        @if($employee->classrooms->isEmpty())
                                            <p>Nenhuma sala associada.</p>
                                        @else
                                            @foreach($employee->classrooms as $classroom)
                                                <p>{{ $classroom->name }}</p>
                                            @endforeach
                                        @endif
                                    </td>

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
                                    <td>
                                        <a href="{{ route('staff.employee.show', $employee->id) }}" title="Ver perfil">
                                            <i class="bi bi-eye-fill icon-eye"></i>
                                        </a>
                                        <a href="{{ route('staff.employee.edit', $employee->id) }}" title="Editar">
                                            <i class="bi bi-pencil-fill icon-edit"></i> 
                                        </a>
                        
                                        <a href="#" onclick="confirmDelete({{ $employee->id }})" title="Eliminar">
                                            <i class="bi bi-trash-fill icon-delete text-danger"></i>
                                        </a>
                                        <form id="delete-form-{{ $employee->id }}" action="{{ route('staff.employee.destroy', $employee->id) }}" method="POST" style="display: none;">
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
