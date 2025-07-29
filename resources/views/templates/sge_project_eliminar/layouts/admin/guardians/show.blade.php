@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <!-- Título da página -->
        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>ENCARREGADOS DE EDUCAÇÃO</strong> | Perfil</h1>
        </div>
        <div class="row">
            <!-- Coluna 1: Perfil da Criança (coluna maior) -->
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Perfil do Encarregado de Educação</h5>
                    </div>
                    <div class="card-body">
                        <div class="row profile-container">
                            <div class="avatar-container-2">
                                <img src="{{ $guardian->avatar 
                                ? route('guardian.avatar', ['id' => $guardian->id]) 
                                : asset('assets/images/general/avatar_default.png') }}" 
                                class="avatar-profile-2">
                    
                            </div>
                            <div class="col">
                                <h4 class="mb-1">{{ $guardian->name }}</h4>
                                <p class="mb-1"><strong>Género:</strong> {{ $guardian->gender->name ?? 'Não especificado' }}</p>
                                <p class="mb-1"><strong>Morada:</strong> {{ $guardian->address_1 }} {{ $guardian->address_2 }}</p>
                                <p class="mb-1"><strong>Localidade:</strong> {{ $guardian->location }}</p>
                                <p class="mb-1"><strong>Distrito:</strong> {{ $guardian->district }}</p>
                                <p class="mb-1"><strong>Código Postal:</strong> {{ $guardian->postal_code }}</p>
                                <p class="mb-1"><strong>País:</strong> {{ $guardian->country->name ?? 'Não informado' }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $guardian->email }}</p>
                                <p class="mb-1"><strong>Telemóvel:</strong> {{ $guardian->mobile_phone }}</p>
                                <p class="mb-1"><strong>Telefone:</strong> {{ $guardian->phone }}</p>

                            </div>
                        </div>
                        <hr>
                        <div>
                            <p><strong>Observações:</strong> {{ $guardian->observations ?? 'Sem observações' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coluna 2: Cards adicionais (coluna menor) -->
            <div class="col-xl-4">
                <div class="row">
                    <!-- Card: Informações das crianças -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Crianças</h5>
                            </div>
                            <div class="card-body">
                                @forelse($children as $child)
                                    <p><strong>{{ $child->name }}</strong></p>
                                @empty
                                    <p>Sem crianças associadas</p>
                                @endforelse
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Card: Informações Educacionais (dados fictícios) -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informações Adicionais</h5>
                            </div>
                            <div class="card-body">
                              
                            </div>
                        </div>
                    </div>
                    <!-- Card: botão editar) -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"></h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('guardians.guardian.edit', $guardian->id) }}" class="btn btn-primary">Editar</a>
                            </div>
                        </div>
                    </div>
                </div> <!-- fim da row interna -->
            </div> <!-- fim da coluna 2 -->
        </div> <!-- fim da row principal -->
    </div>
</main>
@endsection
