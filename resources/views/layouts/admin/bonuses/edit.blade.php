@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>BONIFICAÇÕES</strong> | Editar</h1>

        <!-- Ações -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    {{-- voltar --}}
                    <a 
                        class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary"
                        href="{{ route('bonuses.index') }}">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
            'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
        ])

        <form method="POST" action="{{ route('bonuses.bonus.update', $bonus->id) }}">
            @csrf
            @method('PATCH')
            
            <div class="row mb-3">
                <div class="col-12 col-md-5">
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="card-title mb-0">Editar Registo</h5></div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome *</label>
                                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $bonus->name) }}">
                            </div>

                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Fornecedor</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">— Nenhum —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $bonus->supplier_id == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="brand_id" class="form-label">Marca</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">— Nenhuma —</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $bonus->brand_id == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $bonus->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notas Internas</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $bonus->notes) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Atualizar Registo</button>
                            <a href="{{ route('bonuses.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Cancelar</a>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection
