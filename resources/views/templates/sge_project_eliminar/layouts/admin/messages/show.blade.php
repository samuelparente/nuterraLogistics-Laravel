@extends('layouts.master_admin')

@section('content_admin')

<main class="content">
	<div class="container-fluid p-0">

		<h1 class="h3 mb-3"><strong>MENSAGENS</strong> | Detalhe</h1>
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $message->subject }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ \Carbon\Carbon::parse($message->created_at)->locale('pt_PT')->translatedFormat('H:i - d \d\e F \d\e Y') }}</p>
                       
                        @foreach ($message->recipients as $recipient)
                        <p>
                        @if ($recipient->read_at)
                            <span class="badge bg-success">Lida</span>
                        @else
                            <span class="badge bg-danger">Por Ler</span>
                        @endif
                        <span class="badge bg-secondary">
                            {{ $recipient->recipient->name }}
                        </span></p>
                    @endforeach
                    
                        <p>{{ $message->body }}</p>

                    </div>
                </div>
            </div>

           

        </div>

	</div>
</main>
@endsection
