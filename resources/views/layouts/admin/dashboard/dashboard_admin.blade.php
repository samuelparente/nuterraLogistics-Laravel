@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>DASHBOARD</strong> | Visão Geral</h1>
        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])

        <div class="row">

            <!-- Utilizadores -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-primary">
                            <i data-feather="users" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Utilizadores</h5>
                        </div>
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

            <!-- Pedido a Fornecedor -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-success">
                            <i data-feather="list" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Pedido a Fornecedor</h5>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-success col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

            <!-- Receber Encomenda -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-warning">
                            <i data-feather="package" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Receber Encomenda</h5>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-warning col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

@endsection
