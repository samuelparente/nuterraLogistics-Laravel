@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>EVENTOS</strong> | Ver Todos</h1>

        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Eventos</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Instituição</th>
                                <th>Data/Hora Início</th>
                                <th>Data/Hora Fim</th>
                                <th>Local</th>
                                <th>Capacidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->description }}</td>
                                <td>{{ $event->eventType ? $event->eventType->name : 'N/A' }}</td>
                                <td>
                                    @if($event->eventStatus)
                                        <span class="badge 
                                            @if($event->eventStatus->name == 'Ativo') bg-success
                                            @elseif($event->eventStatus->name == 'Cancelado') bg-danger
                                            @elseif($event->eventStatus->name == 'Concluído') bg-info
                                            @elseif($event->eventStatus->name == 'Adiado') bg-warning
                                            @elseif($event->eventStatus->name == 'Em Andamento') bg-primary
                                            @else bg-secondary
                                            @endif">
                                            {{ $event->eventStatus->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sem status</span>
                                    @endif
                                </td>
                                <td>{{ $event->institution->name }}</td>
                                <td>{{ $event->event_date ? $event->event_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $event->end_date ? $event->end_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $event->location }}</td>
                                <td>{{ $event->capacity }}</td>
                                <td>
                                    <a href="{{ route('events.event.show', $event->id) }}" title="Ver detalhes">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    
                                     {{--<a href="{{ route('events.event.edit', $event->id) }}" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a> --}}
                                    <a href="#" onclick="confirmDelete({{ $event->id }})" title="Eliminar">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </a>
                                    <form id="delete-form-{{ $event->id }}" action="{{ route('events.event.destroy', $event->id) }}" method="POST" style="display: none;">
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
