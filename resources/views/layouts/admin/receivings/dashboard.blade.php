@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>ENTRADA DE MERCADORIAS</strong> | Visão Geral</h1>
        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])

        <div class="row">

            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 {{ !$hasActiveOrders ? 'bg-light text-muted' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 {{ !$hasActiveOrders ? 'text-muted' : 'text-danger' }}">
                            <i data-feather="clock" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Entradas Pendentes</h5>
                        </div>

                        @if ($hasActiveOrders)
                            <a href="{{ route('receivings.index') }}" class="btn btn-sm btn-outline-danger col-6 mx-auto">Aceder</a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary col-6 mx-auto" disabled>Sem pedidos pendentes de abertura</button>
                        @endif
                    </div>
                </div>
            </div>

           <!-- entradas em aberto -->
           <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 {{ !$hasPendingReceivings ? 'bg-light text-muted' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 {{ !$hasPendingReceivings ? 'text-muted' : 'text-primary' }}">
                            <i data-feather="package" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Entradas em Curso</h5>
                        </div>

                        @if ($hasPendingReceivings)
                            <a href="{{ route('receivings.pending') }}" class="btn btn-sm btn-outline-primary col-6 mx-auto">Aceder</a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary col-6 mx-auto" disabled>Não existem entradas em curso</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- divergências -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-warning">
                            <i data-feather="clock" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Divergências</h5>
                        </div>
                        <a href="{{ route('lists.index') }}" class="btn btn-sm btn-outline-warning col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-primary">
                            <i data-feather="list" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Histórico</h5>
                        </div>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

@endsection
