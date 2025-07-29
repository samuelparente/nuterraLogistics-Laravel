@extends('layouts.admin.partials.master_admin')
@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>PERFIL</strong> | Editar</h1>
        </div>
        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Editar Registo</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profiles.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            {{-- Avatar --}}
                            <div class="col-12 col-lg-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Avatar</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="avatar-container" id="avatarPreviewImg">
                                            <img src="{{ $user->avatar ? asset('storage/images/general/avatars/' . $user->avatar) : asset('images/general/avatars/avatar_default.png') }}" class="avatar_profile">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Selecionar Imagem</label>
                                            <div class="d-flex align-items-center gap-3">
                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('avatarInput').click();">
                                                    Escolher Ficheiro
                                                </button>
                                                <span id="avatarFileName" class="text-muted">Nenhum ficheiro selecionado</span>
                                            </div>
                                            <input type="file" id="avatarInput" class="d-none" onchange="handleAvatarSelect(this)" accept="image/*">
                                        </div>
                                        <input type="hidden" id="cropped_avatar" name="cropped_avatar">
                                    </div>
                                </div>
                            </div>
                            {{-- Nome --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" disabled>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Password (opcional) --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Palavra-passe (opcional)</label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Nova Palavra-passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>
                            {{-- Papel --}}
                            <div class="mb-3">
                                <label for="role" class="form-label">Papel</label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" disabled>
                                    <option value="">Seleciona um papel</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                                            {{ $roleLabelsMap[$role->name] ?? ucfirst(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Estado --}}
                            <div class="mb-3">
                                <label for="status_id" class="form-label">Estado</label>
                                <select name="status_id" id="status_id" class="form-select @error('status_id') is-invalid @enderror" disabled>
                                    <option value="">Selecione...</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ old('status_id', $user->status_id) == $status->id ? 'selected' : '' }}>
                                            {{ $status->label_pt }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Avatar Base64 hidden --}}
                            @error('cropped_avatar')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-primary">Atualizar Registo</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal do Cropper -->
            <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cropperModalLabel">Cortar Avatar</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img id="cropperImage" style="width: 100%; max-height: 400px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="cropButton">Cortar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection
