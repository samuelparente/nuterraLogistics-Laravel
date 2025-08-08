@extends('layouts.admin.partials.master_admin')

@section('content_admin')

<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>PEDIDOS A FORNECEDORES</strong> | Visão Geral</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">

                    {{-- voltar --}}
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('backoffice.dashboard') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])

        <div class="row">

            <!-- listagens -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-danger">
                            <i data-feather="book-open" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Listagens</h5>
                        </div>
                        <a href="{{ route('lists.index') }}" class="btn btn-sm btn-outline-danger col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

           @php
                $hasOpenOrder = isset($openOrder) && $openOrder !== null;
            @endphp

            <!-- novo pedido -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 {{ $hasOpenOrder ? 'bg-light text-muted' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 {{ $hasOpenOrder ? 'text-muted' : 'text-success' }}">
                            <i data-feather="plus-circle" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Criar Pedido</h5>
                        </div>
                        @if ($hasOpenOrder)
                            <button class="btn btn-sm btn-outline-secondary col-6 mx-auto" disabled>Já existe um pedido em aberto</button>
                        @else
                            <form action="{{ route('orders.order.createEmpty') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success col-6 mx-auto">Aceder</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- pedido atual -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100 {{ !$hasOpenOrder ? 'bg-light text-muted' : '' }}">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 {{ !$hasOpenOrder ? 'text-muted' : 'text-primary' }}">
                            <i data-feather="shopping-cart" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Pedido em Aberto</h5>
                        </div>
                        @if ($hasOpenOrder)
                            <a href="{{ route('orders.order.edit') }}" class="btn btn-sm btn-outline-primary col-6 mx-auto">Aceder</a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary col-6 mx-auto" disabled>Não existe um pedido criado</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Histórico -->
            <div class="col-12 col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <div class="mb-3 text-warning">
                            <i data-feather="list" class="taxonomy-icons" style="width:48px; height:48px;"></i>
                            <h5 class="card-title mt-2 mb-1">Histórico</h5>
                        </div>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-warning col-6 mx-auto">Aceder</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

@endsection
