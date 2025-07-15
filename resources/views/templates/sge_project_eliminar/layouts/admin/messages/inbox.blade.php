@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>MENSAGENS</strong> | Recebidas</h1>

        <div class="row">
            <div class="col-12 col-lg-12 col-xxl-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recebidas</h5>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>De</th>
                                <th>Assunto</th>
                                <th>Hora / Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                            <tr>
                                <td>{{ $message->sender->name }}</td>
                                <td>{{ $message->subject }}</td>
                                <td>{{ $message->created_at }}</td>
                                <td>
                                    <a href="{{ route('messages.message.show', $message->id) }}" title="Ver detalhes">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="#" onclick="confirmDelete({{ $message->id }})" title="Eliminar">
                                        <i class="bi bi-trash-fill text-danger"></i>
                                    </a>
                                    <form id="delete-form-{{ $message->id }}" action="{{ route('messages.message.destroy', $message->id) }}" method="POST" style="display: none;">
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
