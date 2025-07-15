@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>PERFIL</strong> | Visualizar</h1>
        </div>
        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])
        <div class="row">
            <div class="col-12 col-md-8 col-lg-8 mx-auto">

                <div class="card shadow rounded-4 overflow-hidden profile-card">
                    {{-- Cabeçalho com fundo --}}
                    <div class="profile-card-header-gradient">
                        <div class="profile-avatar-container">
                            <img src="{{ $user->avatar ? asset('storage/images/general/avatars/' . $user->avatar) : asset('images/general/avatars/avatar_default.png') }}" 
                                 alt="Avatar de {{ $user->name }}" 
                                 class="profile-avatar-single">
                        </div>
                    </div>

                    <div class="card-body text-center">
                        <h4 class="profile-name-text">{{ $user->name }}</h4>
                        <p class="profile-role-text">
                            {{ $roleLabelsMap[$user->getRoleNames()->first()] ?? ucfirst(str_replace('-', ' ', $user->getRoleNames()->first() ?? 'N/A')) }}
                        </p>

                        {{-- Ícones e informações --}}
                        <div class="profile-icons-row d-flex flex-wrap justify-content-around text-center mt-3">
                            <div class="profile-icon-block">
                                <i class="bi bi-person profile-icon-small"></i>
                                <p class="small mb-0">{{ $user->name }}</p>
                            </div>
                            <div class="profile-icon-block">
                                <i class="bi bi-envelope profile-icon-small"></i>
                                <p class="small mb-0">{{ $user->email }}</p>
                            </div>
                            <div class="profile-icon-block">
                                <i class="bi bi-shield-lock profile-icon-small"></i>
                                <p class="small mb-0">{{ $roleLabelsMap[$user->getRoleNames()->first()] ?? ucfirst(str_replace('-', ' ', $user->getRoleNames()->first() ?? 'N/A')) }}</p>
                            </div>
                            <div class="profile-icon-block">
                                <i class="bi bi-toggle-on profile-icon-small"></i>
                                <p class="small mb-0">
                                    @if($user->status)
                                        <span class="badge bg-{{ $user->status->color }}">{{ $user->status->label_pt }}</span>
                                    @else
                                        <span class="badge bg-secondary">Sem Estado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr class="my-3">

                        <a href="{{ route('profiles.profile.edit', $user->id) }}" class="btn profile-button-edit mb-3">
                            Editar Perfil
                        </a>

                    </div>
                </div>

            </div>
        </div>

    </div>
</main>
@endsection
