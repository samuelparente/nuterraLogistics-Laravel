@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>ERP</strong> | Teste de documento de compra</h1>

        <div class="alert alert-warning">
            Os botões abaixo fazem criações reais no ERP de testes. Cada clique pode criar um novo documento.
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Destino</h5>
            </div>
            <div class="card-body">
                <code>{{ $endpoint }}</code>
            </div>
        </div>

        @foreach ($scenarios as $scenarioKey => $scenario)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-1">{{ $scenario['title'] }}</h5>
                    <p class="text-muted mb-0">{{ $scenario['description'] }}</p>
                </div>
                <div class="card-body">
                    <pre>{{ json_encode($scenario['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>

                    <form method="POST" action="{{ route('erp.test-document.store') }}">
                        @csrf
                        <input type="hidden" name="scenario" value="{{ $scenarioKey }}">

                        <button
                            type="submit"
                            class="btn btn-danger"
                            onclick="return confirm('Este pedido pode criar um documento real no ERP de testes. Pretende continuar?')"
                        >
                            {{ $scenario['button'] }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</main>
@endsection
