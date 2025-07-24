@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <!-- Título da página -->
        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>SALAS</strong> | Detalhe</h1>
        </div>
        <div class="row">
  
                <div class="col-12 col-md-8">
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
                                <strong>Capacidade Máxima:</strong> {{ $classroom->max_capacity }}
                            </p>
                            <a href="{{ route('classrooms.classroom.show', $classroom->id) }}" class="btn btn-primary">Ver Detalhes</a>
                        </div>
                    </div>
                </div>
                <!-- Card: botão editar) -->
                <div class="col-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0"></h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('classrooms.classroom.edit', $classroom->id) }}" class="btn btn-primary">Editar</a>
                        </div>
                    </div>
                </div>

            
            
        </div> <!-- fim da row principal -->
    </div>
</main>
@endsection
