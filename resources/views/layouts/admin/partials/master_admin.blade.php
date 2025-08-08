<!DOCTYPE html>
<html lang="pt">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Painel de administração da Nuterra">
	<meta name="author" content="Samuel Parente">
    <meta name="robots" content="noindex, nofollow">
	<meta name="keywords" content="">
	<link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="{{ asset('images/general/logo_pv.ico') }}"/>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- jQuery UI JS -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>--}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> 
    <!-- Cropper 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    -->
   <!-- Summernote CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet"> --}}
    <!-- Summernote JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script> --}}
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
     <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<title>{{ config('app.name') }}</title>
    <!-- Favicons -->
    <link href="{{ asset('images/general/logo_pv.ico') }}" rel="icon">
    <link href="{{ asset('images/general/logo_pv.ico') }}" rel="apple-touch-icon">
	<link href="{{ asset('assets/css/app-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    {{--bandeiras--}}
    @vite(['resources/css/icons.css'])
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="{{ route('backoffice.dashboard')}}">
                    <img class="logo-admin" src="{{ asset('images/general/logo-03.png') }}" alt="logotipo da nuterra">
                    <span class="company">NUTERRA</span>
                <span class="separator">|</span>
                <span class="department">logistics</span> 
                </a>
                <ul class="sidebar-nav">
                    {{-- Dashboard --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('backoffice.dashboard') }}">
                            <i class="align-middle menu-icons-color" data-feather="home"></i>
                            <span class="align-middle">Dashboard</span>
                        </a>
                    </li>
                    {{-- Utilizadores --}}
                    @hasanyrole('admin|super-admin')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('users.index') }}">
                            <i class="align-middle menu-icons-color" data-feather="users"></i>
                            <span class="align-middle">Utilizadores</span>
                        </a>
                    </li>
                    @endhasanyrole
                    {{-- Encomenda a fornecedor --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('orders.dashboard') }}">
                            <i class="align-middle menu-icons-color" data-feather="list"></i>
                            <span class="align-middle">Pedidos a Fornecedores</span>
                        </a>
                    </li>
                    {{-- Recepção de encomenda --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('receivings.dashboard') }}">
                            <i class="align-middle menu-icons-color" data-feather="package"></i>
                            <span class="align-middle">Entrada de Mercadorias</span>
                        </a>
                    </li> 
                    {{-- Bonificações --}}
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('bonuses.index') }}">
                            <i class="align-middle menu-icons-color" data-feather="percent"></i>
                            <span class="align-middle">Bonificações</span>
                        </a>
                    </li> 
                    {{-- definiçoes --}}
                    @hasanyrole('admin|super-admin')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('settings.edit') }}">
                            <i class="align-middle menu-icons-color" data-feather="settings"></i>
                            <span class="align-middle">Configurações</span>
                        </a>
                    </li>
                    @endhasanyrole                  
                </ul>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
            <i class="hamburger align-self-center"></i>
        </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        

                        
                        <li class="nav-item dropdown">
                            <!-- Ícone para telas pequenas -->
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <!-- Nome e Avatar -->
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img 
                                    src="{{ Auth::user()->avatar 
                                        ? asset('storage/images/general/avatars/' . Auth::user()->avatar) 
                                        : asset('images/general/avatars/avatar_default.png') }}" 
                                    class="avatar-profile-mini" 
                                    alt="Avatar">

                                <span class="text-dark">{{ Auth::user()->name }}</span>
                            </a>

                        
                            <!-- Dropdown Menu -->
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profiles.profile.show',Auth::user()->id) }}"><i class="align-middle me-1" data-feather="user"></i> Perfil</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="align-middle me-1" data-feather="log-out"></i> Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            {{-- empresa-cargo --}}
            <div class="role" style="padding:10px;"> 
                <div class="container-fluid p-0">
                    <span class="align-middle" style="color:#9c9c9c;font-size:0.9rem;">
                        <span class="permissions_company">NUTERRA</span>
                        <span class="permissions_separator">|</span>
                        <span class="permissions_department">logistics</span> | {{ Auth::user()->roleLabel }}
                    </span> 
                </div>
            </div>
            {{-- conteúdo admin --}}
            @yield('content_admin')

            {{-- carrinho do pedido --}}
            @if ($openOrder)
                <a href="{{ route('orders.order.edit') }}" 
                class="btn btn-floating-cart shadow"
                title="Ver Pedido Atual">
                    <i class="bi bi-cart"></i>
                    <span class="cart-badge">{{ $openOrder->orderItems->count() }}</span>
                </a>
            @else
                <a href="javascript:void(0);" 
                class="btn btn-floating-cart shadow disabled text-muted" 
                title="Nenhum pedido aberto" 
                style="pointer-events: none; background-color:#ffffff;">
                    <i class="bi bi-cart"></i>
                    <span class="cart-badge">0</span>
                </a>
            @endif

 
            {{-- footer admin --}}
            @include('layouts.admin.partials.footer_admin')
        </div>
    </div>

</body>
    
<script src="{{ asset('assets/js/app.js') }}?v={{ time() }}"></script>
 <!-- Main JS File -->
<script src="{{ asset('assets/js/main.js') }}?v={{ time() }}"></script>

</html>
