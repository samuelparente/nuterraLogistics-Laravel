@extends('layouts.admin.partials.master_admin')

@section('content_admin')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>CONFIGURAÇÕES</strong> | Gerais</h1>

        {{-- Ações --}}
        <div class="row mb-3">
            <div class="col-12 text-end">
                <div class="mt-3 mb-3 action-buttons-header-mobile">
                    <a href="{{ route('backoffice.dashboard') }}"
                       class="btn btn-sm me-2 action-buttons-header-mobile-inner btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        {{-- breadcrumbs --}}
        @include('layouts.admin.partials.breadcrumbs', [
			'breadcrumbs' => config('breadcrumbs')[Route::currentRouteName()] ?? []
	    ])

        {{-- Formulário --}}
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Card SMTP --}}
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-envelope-fill me-2"></i> SMTP</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="smtp_host" class="form-label">Servidor SMTP</label>
                                <input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host', $settings->smtp_host) }}">
                            </div>

                            <div class="mb-3">
                                <label for="smtp_port" class="form-label">Porta</label>
                                <input type="number" name="smtp_port" class="form-control" value="{{ old('smtp_port', $settings->smtp_port) }}">
                            </div>

                            <div class="mb-3">
                                <label for="smtp_encryption" class="form-label">Encriptação</label>
                                <select name="smtp_encryption" class="form-select">
                                    <option value="">Nenhuma</option>
                                    <option value="tls" {{ $settings->smtp_encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $settings->smtp_encryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="smtp_user" class="form-label">Utilizador</label>
                                <input type="text" name="smtp_user" class="form-control" value="{{ old('smtp_user', $settings->smtp_user) }}">
                            </div>

                            <div class="mb-3">
                                <label for="smtp_password" class="form-label">Palavra-passe</label>
                                <input type="password" name="smtp_password" class="form-control" value="{{ old('smtp_password', $settings->smtp_password) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Envio --}}
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-send-fill me-2"></i> Informações de Envio</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="smtp_from_address" class="form-label">Email do Remetente</label>
                                <input type="email" name="smtp_from_address" class="form-control" value="{{ old('smtp_from_address', $settings->smtp_from_address) }}">
                            </div>

                            <div class="mb-3">
                                <label for="smtp_from_name" class="form-label">Nome do Remetente</label>
                                <input type="text" name="smtp_from_name" class="form-control" value="{{ old('smtp_from_name', $settings->smtp_from_name) }}">
                            </div>

                           <div class="mb-3">
                                <label class="form-label">Destinatários Principais (To)</label>
                                @for ($i = 0; $i < 5; $i++)
                                    <input
                                        type="email"
                                        name="notification_to[]"
                                        class="form-control mb-1"
                                        value="{{ old("notification_to.$i", $settings->notification_to[$i] ?? '') }}"
                                        {{ $i === 0 ? 'required' : '' }}
                                        placeholder="Email {{ $i + 1 }}">
                                @endfor
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Com Cópia (CC)</label>
                                @for ($i = 0; $i < 5; $i++)
                                    <input
                                        type="email"
                                        name="notification_cc[]"
                                        class="form-control mb-1"
                                        value="{{ old("notification_cc.$i", $settings->notification_cc[$i] ?? '') }}"
                                        placeholder="Email {{ $i + 1 }}">
                                @endfor
                            </div>


                        </div>
                    </div>
                </div>

                {{-- Card Avisos de Validade --}}
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Avisos de Validade de Lotes
                            </h5>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label for="expiry_alert_days_login" class="form-label">
                                    Mostrar aviso no login quando faltarem (dias)
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    max="3650"
                                    name="expiry_alert_days_login"
                                    id="expiry_alert_days_login"
                                    class="form-control"
                                    value="{{ old('expiry_alert_days_login', $settings->expiry_alert_days_login ?? 180) }}"
                                >
                            </div>

                            <div class="mb-3">
                                <label for="expiry_alert_days_email" class="form-label">
                                    Aviso diário por email quando faltarem (dias)
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    max="3650"
                                    name="expiry_alert_days_email"
                                    id="expiry_alert_days_email"
                                    class="form-control"
                                    value="{{ old('expiry_alert_days_email', $settings->expiry_alert_days_email ?? 180) }}"
                                >
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            
            {{-- Botão Guardar --}}
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Configurações
                </button>
            </div>
        </form>
    </div>
</main>

@endsection
