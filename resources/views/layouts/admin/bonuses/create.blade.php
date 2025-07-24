@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>BONIFICAÇÕES</strong> | Criar Nova</h1>

        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        <form action="{{ route('bonuses.bonus.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-12 col-md-5">
                    <div class="card mb-4">
                        <div class="card-header">Dados da Bonificação</div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                            </div>

                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Fornecedor</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">— Nenhum —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="brand_id" class="form-label">Marca</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">— Nenhuma —</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notas Internas</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Gravar Registo</button>
                            <a href="{{ route('bonuses.index') }}" class="btn btn-outline-secondary">Cancelar</a>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection
