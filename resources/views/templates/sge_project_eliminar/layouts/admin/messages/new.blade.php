@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>MENSAGENS</strong> | Nova</h1>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Enviar Nova Mensagem</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('messages.message.send') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="recipient" class="form-label">Destinatários</label>
                                <select name="recipient_ids[]" id="recipient" class="form-control" multiple="multiple" required>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Assunto</label>
                                <input type="text" name="subject" id="subject" class="form-control" placeholder="Assunto" required>
                            </div>

                            <div class="mb-3">
                                <label for="body" class="form-label">Mensagem</label>
                                <textarea name="body" id="body" class="form-control" placeholder="Mensagem" rows="10" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar o Select2 no campo de destinatários
        $('#recipient').select2({
            placeholder: "Selecione destinatários",
            allowClear: true,
            multiple: true,
            // Requisição AJAX para a rota 'messages.search'
            ajax: {
                url: '{{ route('messages.search') }}', // Usando a rota definida para busca
                dataType: 'json',
                delay: 250, // Para otimizar, não fazer requisição em cada tecla pressionada
                data: function (params) {
                    return {
                        q: params.term // Termo de busca que o Select2 envia
                    };
                },
                processResults: function (data) {
                    // Organize os dados para o Select2 (id, text)
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,  // ID do usuário
                                text: item.name // Nome do usuário
                            };
                        })
                    };
                },
                cache: true
            }
        });
    });
</script>

@endsection
