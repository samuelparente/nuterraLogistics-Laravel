@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>ERRO</strong> | Bloqueado</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-danger">Não pode efectuar esta operação.</h5>
                    </div>
                    <div class="card-body">
                    Contacte o administrador do sistema.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
