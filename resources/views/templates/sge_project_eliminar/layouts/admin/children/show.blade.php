@extends('layouts.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <!-- Título da página -->
        <div class="mb-3">
            <h1 class="h3 mb-3"><strong>CRIANÇAS</strong> | Perfil</h1>
        </div>
        <div class="row">
            <!-- Coluna 1: Perfil da Criança (coluna maior) -->
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Perfil da Criança</h5>
                    </div>
                    <div class="card-body">
                        <div class="row profile-container">
                            <div class="avatar-container-2">
                                <img src="{{ $child->avatar 
                                ? route('child.avatar', ['id' => $child->id]) 
                                : asset('assets/images/general/avatar_default.png') }}" 
                                class="avatar-profile-2">
                    
                            </div>
                            <div class="col">
                                <h4 class="mb-1">{{ $child->name }}</h4>
                                <p class="mb-1"><strong>Data de Nascimento:</strong> {{ \Carbon\Carbon::parse($child->dob)->format('d/m/Y') }}</p>
                                <p class="mb-1"><strong>Morada:</strong> {{ $child->address_1 }} {{ $child->address_2 }}</p>
                                <p class="mb-1"><strong>Localidade:</strong> {{ $child->location }}</p>
                                <p class="mb-1"><strong>Distrito:</strong> {{ $child->district }}</p>
                                <p class="mb-1"><strong>Código Postal:</strong> {{ $child->postal_code }}</p>
                                <p class="mb-1"><strong>País:</strong> {{ $child->country->name ?? '' }}</p>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <p><strong>Observações:</strong> {{ $child->observations ?? '' }}</p>
                            <p><strong>Alergias:</strong> {{ $child->allergies ?? '' }}</p>
                            <p><strong>Medicação:</strong> {{ $child->medications ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coluna 2: Cards adicionais (coluna menor) -->
            <div class="col-xl-4">
                <div class="row">
                    <!-- Card: Informações do Guardião -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Encarregado de Educação</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ $child->guardian->name ?? '' }}</strong></p>
                                <p>{{ $child->guardian->email ?? '' }}</p>
                                <p>{{ $child->guardian->mobile_phone ?? '' }}</p>
                                <p>{{ $child->guardian->phone ?? '' }}</p>
                                <p>{{ $child->guardian->address_1 ?? '' }} {{ $child->guardian->address_2 ?? '' }}</p>
                                <p>{{ $child->guardian->postal_code ?? '' }} {{ $child->guardian->city ?? '' }}</p>
                                <p>{{ $child->guardian->country->name ?? '' }}</p>

                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: Informações Educacionais (dados fictícios) -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informações Educacionais</h5>
                            </div>
                            <div class="card-body">
                                <p>Turma: {{ $child->classGroup->name ?? 'Não atribuída.' }}</p>
                                <p>Sala: {{ $child->classGroup->classroom->name ?? 'Não atribuída.' }}</p>   
                                <p>
                                    @if($child->classroom)
                                        <p><strong>Funcionários na Sala:</strong></p>
                                        @foreach($child->classroom->activeStaff as $staff)
                                            <p>{{ $staff->name }}, {{ $staff->position->description ?? 'Sem função' }}</p>
                                        @endforeach
                                    @endif

                                
                                </p>                                                        
                            </div>
                    </div>
                    <!-- Card: botão editar) -->
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"></h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('children.child.edit', $child->id) }}" class="btn btn-primary">Editar</a>
                            </div>
                        </div>
                    </div>
                </div> <!-- fim da row interna -->
            </div> <!-- fim da coluna 2 -->
        </div> <!-- fim da row principal -->
    </div>
</main>
@endsection
