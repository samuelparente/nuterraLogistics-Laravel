@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <!-- Título da página -->
        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>FUNCIONÁRIOS</strong> | Perfil</h1>
        </div>
        <div class="row">
            <!-- Coluna 1: Perfil  (coluna maior) -->
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Perfil do Funcionário</h5>
                    </div>
                    <div class="card-body">
                        <div class="row profile-container">
                            <div class="avatar-container-2">
                                <img src="{{ $employee->avatar 
                                ? route('employee.avatar', ['id' => $employee->id]) 
                                : asset('assets/images/general/avatar_default.png') }}" 
                                class="avatar-profile-2">
                    
                            </div>
                            <div class="col">
                                <h4 class="mb-1">{{ $employee->name }}</h4>
                                <p class="mb-1"><strong>Género:</strong> {{ $employee->gender->name ?? 'Não especificado' }}</p>
                                <p class="mb-1"><strong>Morada:</strong> {{ $employee->address_1 }} {{ $employee->address_2 }}</p>
                                <p class="mb-1"><strong>Localidade:</strong> {{ $employee->city }}</p>
                                <p class="mb-1"><strong>Distrito:</strong> {{ $employee->district }}</p>
                                <p class="mb-1"><strong>Código Postal:</strong> {{ $employee->postal_code }}</p>
                                <p class="mb-1"><strong>País:</strong> {{ $employee->country->name ?? 'Não informado' }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $employee->email }}</p>
                                <p class="mb-1"><strong>Telemóvel:</strong> {{ $employee->mobile_phone }}</p>
                                <p class="mb-1"><strong>Telefone:</strong> {{ $employee->phone }}</p>

                            </div>
                        </div>
                        <hr>
                        <div>
                            <p><strong>Observações:</strong> {{ $employee->observations ?? 'Sem observações' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coluna 2: Cards adicionais (coluna menor) -->
            <div class="col-xl-4">
                <div class="row">
                   <!-- Card: Informações Turmas -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Turmas</h5>
                            </div>
                            <div class="card-body">
                                @if($employee->classGroups->isEmpty())
                                    <p>Nenhuma turma associada.</p>
                                @else
                                    @foreach($employee->classGroups as $classGroup)
                                        <p>{{ $classGroup->name }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card: Informações Salas -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Salas</h5>
                            </div>
                            <div class="card-body">
                                @if($employee->classrooms->isEmpty())
                                    <p>Nenhuma sala associada.</p>
                                @else
                                    @foreach($employee->classrooms as $classroom)
                                        <p>{{ $classroom->name }}</p>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Card: Informações Educacionais (dados fictícios) -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informações Adicionais</h5>
                            </div>
                            <div class="card-body">
                              
                            </div>
                        </div>
                    </div>
                    <!-- Card: botão editar) -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"></h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('staff.employee.edit', $employee->id) }}" class="btn btn-primary">Editar</a>
                            </div>
                        </div>
                    </div>
                </div> <!-- fim da row interna -->
            </div> <!-- fim da coluna 2 -->
        </div> <!-- fim da row principal -->
    </div>
</main>
@endsection
