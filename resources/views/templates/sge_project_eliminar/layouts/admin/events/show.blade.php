@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>EVENTOS</strong> | Detalhe</h1>
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $event->title }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $event->eventType->name }}</p>
                       
                        
                        <p>{{ $event->institution->name }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Hora e Data de Início</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ \Carbon\Carbon::parse($event->event_date)->locale('pt_PT')->translatedFormat('H:i - d \d\e F \d\e Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Hora e Data de Fim</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ \Carbon\Carbon::parse($event->end_date)->locale('pt_PT')->translatedFormat('H:i - d \d\e F \d\e Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Local</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $event->location }}</p>
                    </div>
                </div>
            </div>

        </div>
		<div class="row">
            <div class="col-12 col-md-10">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Descrição</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $event->description }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Estado</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            @if ($event->eventStatus->name === 'Ativo')
                                <span class="badge bg-success">Activo</span>
                            @elseif ($event->eventStatus->name === 'Cancelado')
                                <span class="badge bg-danger">Cancelado</span>
                            @elseif ($event->eventStatus->name === 'Em Curso')
                                <span class="badge bg-warning">Em Curso</span>
                            @elseif ($event->eventStatus->name === 'Concluído')
                                <span class="badge bg-primary">Concluído</span>
                            @elseif ($event->eventStatus->name === 'Adiado')
                                <span class="badge bg-secondary">Adiado</span>
                            @else
                                <span class="badge bg-dark">Sem Estado Definido</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

        </div>
		

	</div>
</main>
@endsection
