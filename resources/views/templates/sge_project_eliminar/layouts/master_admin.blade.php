<!DOCTYPE html>
<html lang="pt">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="assets/images/website/logo_cores_rainbow_kids.png" />
   
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

    <!-- Cropper -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

     <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<title>{{ config('app.name') }}</title>
    <!-- Favicons -->
    <link href="{{ asset('assets/images/website/logo_cores_rainbow_kids.png') }}" rel="icon">
    <link href="{{ asset('assets/images/website/logo_cores_rainbow_kids.png') }}" rel="apple-touch-icon">


	<link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

    @include('layouts.partials.header_admin')

    <body>
        <div class="wrapper">
            <nav id="sidebar" class="sidebar js-sidebar">
                <div class="sidebar-content js-simplebar">
                    <a class="sidebar-brand" href="https://www.rainbowkids.pt">
                        <img class="logo-admin" src="{{ asset('assets/images/website/logo_cores_rainbow_kids.png') }}" alt="logotipo da rainbow kids">
                        <span class="align-middle">{{ config('app.name') }}</span>
                        @if(Auth::user()->type_id === 4)
                            <span class="align-middle institution-name super-admin">SUPER ADMIN - TODAS AS PERMISSÕES</span>
                        @else
                            <span class="align-middle institution-name">{{ Auth::user()->institution->name }}</span>   
                        @endif
                        
                    </a>
            
                    <ul class="sidebar-nav">
                        
                        <li class="sidebar-header">
                            Dashboard
                        </li>
                         <li class="sidebar-item ">{{-- active --}}
                            <a class="sidebar-link" href="{{ route('dashboard') }}">
                            <i class="align-middle" data-feather="eye"></i> <span class="align-middle">Visão Geral</span>
                            </a>
                        </li>
    
                        
    
                        <li class="sidebar-header">
                            Utilizadores
                        </li>
                         <li class="sidebar-item ">{{-- active --}}
                            <a class="sidebar-link" href="{{ route('users.user.new') }}">
                            <i class="align-middle" data-feather="user-plus"></i> <span class="align-middle">Criar Novo</span>
                            </a>
                        </li>
    
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('users.all') }}">
                        <i class="align-middle" data-feather="users"></i> <span class="align-middle">Ver Todos</span>
                        </a>
                        </li>
    
                    
                        <li class="sidebar-header">
                            Crianças
                        </li>
    
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('children.child.create') }}">
                            <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Criar Nova</span>
                            </a>
                                    </li>
    
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="{{ route('children.all') }}">
                            <i class="align-middle" data-feather="list"></i> <span class="align-middle">Ver Todas</span>
                            </a>
                                    </li>

                                    <li class="sidebar-header">
                                        Encarregados de Educação
                                    </li>
                
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="{{ route('guardians.guardian.create') }}">
                                        <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Criar Novo</span>
                                        </a>
                                                </li>
                
                                                <li class="sidebar-item">
                                                    <a class="sidebar-link" href="{{ route('guardians.all') }}">
                                        <i class="align-middle" data-feather="list"></i> <span class="align-middle">Ver Todos</span>
                                        </a>
                                                </li>
            


                                    <li class="sidebar-header">
                                        Funcionários
                                    </li>
                
                                    <li class="sidebar-item">
                                        <a class="sidebar-link" href="{{ route('staff.employee.create') }}">
                                        <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Criar Novo</span>
                                        </a>
                                                </li>
                
                                                <li class="sidebar-item">
                                                    <a class="sidebar-link" href="{{ route('staff.all') }}">
                                        <i class="align-middle" data-feather="list"></i> <span class="align-middle">Ver Todos</span>
                                        </a>
                                                </li>
                                        <li class="sidebar-header">
                                            Turmas
                                        </li>
                    
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="{{ route('classes.class.create') }}">
                                            <i class="align-middle" data-feather="clipboard"></i> <span class="align-middle">Criar Nova</span>
                                            </a>
                                                    </li>
                    
                                                    <li class="sidebar-item">
                                                        <a class="sidebar-link" href="{{ route('classes.all') }}">
                                            <i class="align-middle" data-feather="list"></i> <span class="align-middle">Ver Todas</span>
                                            </a>
                                                    </li>
                    
                                            <li class="sidebar-header">
                                                Salas
                                            </li>
                        
                                            <li class="sidebar-item">
                                                <a class="sidebar-link" href="{{ route('classrooms.classroom.create') }}">
                                                <i class="align-middle" data-feather="plus-square"></i> <span class="align-middle">Criar Nova</span>
                                                </a>
                                                        </li>
                        
                                                        <li class="sidebar-item">
                                                            <a class="sidebar-link" href="{{ route('classrooms.all') }}">
                                                <i class="align-middle" data-feather="square"></i> <span class="align-middle">Ver Todas</span>
                                                </a>
                                        </li>	
                                        <li class="sidebar-header">
                                            Ementa
                                        </li>
                    
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="#">
                                            <i class="align-middle" data-feather="send"></i> <span class="align-middle">Criar Nova</span>
                                            </a>
                                                    </li>
                    
                                                    <li class="sidebar-item">
                                                        <a class="sidebar-link" href="#">
                                            <i class="align-middle" data-feather="inbox"></i> <span class="align-middle">Ver Todas</span>
                                            </a>
                                                    </li>	
                                                <li class="sidebar-header">
                                                    Mensagens
                                                </li>
                            
                                                <li class="sidebar-item">
                                                    <a class="sidebar-link" href="{{ route('messages.message.create') }}">
                                                    <i class="align-middle" data-feather="send"></i> <span class="align-middle">Enviar Nova</span>
                                                    </a>
                                                </li>
                
                                                <li class="sidebar-item">
                                                    <a class="sidebar-link" href="{{ route('messages.inbox') }}">
                                                        <i class="align-middle" data-feather="inbox"></i> <span class="align-middle">Recebidas</span>
                                                    </a>
                                                </li>
                                                <li class="sidebar-item">
                                                    <a class="sidebar-link" href="{{ route('messages.outbox') }}">
                                                        <i class="align-middle" data-feather="outbox"></i> <span class="align-middle">Enviadas</span>
                                                    </a>
                                                </li>		

                                                <li class="sidebar-header">
                                                    Agenda
                                                </li>
                            
                                                <li class="sidebar-item">
                                                    <a class="sidebar-link" href="{{ route('events.event.create') }}">
                                                    <i class="align-middle" data-feather="bookmark"></i> <span class="align-middle">Criar Evento</span>
                                                    </a>
                                                            </li>
                            
                                                            <li class="sidebar-item">
                                                                <a class="sidebar-link" href="{{ route('events.all') }}">
                                                    <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Ver Todos os Eventos</span>
                                                    </a>
                                        </li>	
                                        {{-- <li class="sidebar-header">
                                            Alertas
                                        </li>
                    
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="ui-buttons.html">
                                            <i class="align-middle" data-feather="alert-triangle"></i> <span class="align-middle">Criar Novo</span>
                                            </a>
                                        </li>
                    
                                        <li class="sidebar-item">
                                            <a class="sidebar-link" href="ui-forms.html">
                                            <i class="align-middle" data-feather="table"></i> <span class="align-middle">Ver Todos</span>
                                            </a>
                                        </li>	 --}}
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
                           
                           
                            {{-- <li class="nav-item dropdown">
                                <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="position-relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell align-middle">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg>
                                        <!-- Exibir o número de notificações não lidas -->
                                        <span class="indicator">{{ $unreadNotificationsCount }}</span>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                                    <div class="dropdown-menu-header">
                                        {{ $unreadNotificationsCount }} New Notifications
                                    </div>
                                    <div class="list-group">
                                        @foreach ($notifications as $notification)
                                            <a href="{{ route('notifications.show', $notification->id) }}" class="list-group-item">
                                                <div class="row g-0 align-items-center">
                                                    <div class="col-2">
                                                        <!-- Ícone de notificação dependendo do tipo -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle text-danger">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                        </svg>
                                                    </div>
                                                    <div class="col-10">
                                                        <div class="text-dark">{{ $notification->title }}</div>
                                                        <div class="text-muted small mt-1">{{ $notification->message }}</div>
                                                        <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="dropdown-menu-footer">
                                        <a href="{{ route('events.all') }}" class="text-muted">Show all notifications</a>
                                    </div>
                                </div>
                            </li>
                            
                             --}}

                             <li class="nav-item dropdown">
                                <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="position-relative">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell align-middle">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg>
                                        <span class="indicator">{{ count($notifications) }}</span>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                                    <div class="dropdown-menu-header">
                                        @php
                                            $count = count($notifications);
                                        @endphp

                                        @if($count === 0)
                                            Sem Novos Eventos
                                        @elseif($count === 1)
                                        {{ $count }} Novo Evento
                                        @else
                                            {{ $count }} Novos Eventos
                                        @endif
                                    </div>
                                    <div class="list-group">
                                        @foreach($notifications as $notification)
                                            @if ($notification->event)
                                                <a href="{{ route('events.event.show', ['id' => $notification->event->id]) }}" class="list-group-item">
                                                    <div class="row g-0 align-items-center">
                                                        <div class="col-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell text-warning">
                                                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="col-10">
                                                            <div class="text-dark">{{ $notification->data['message'] }}</div>
                                                            <div class="text-muted small mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach

                                    </div>
                                    <div class="dropdown-menu-footer">
                                        <a href="{{ route('events.all') }}" class="text-muted">Ver todos os Eventos</a>
                                    </div>
                                </div>
                            </li>
                            

                            <li class="nav-item dropdown">
                                <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
                                    <div class="position-relative">
                                        <i class="align-middle" data-feather="message-square"></i>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
                                    <div class="dropdown-menu-header">
                                        <div class="position-relative">
                                            4 New Messages
                                        </div>
                                    </div>
                                    <div class="list-group">
                                        <a href="#" class="list-group-item">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <img src="img/avatars/avatar-5.jpg" class="avatar img-fluid rounded-circle" alt="Vanessa Tucker">
                                                </div>
                                                <div class="col-10 ps-2">
                                                    <div class="text-dark">Vanessa Tucker</div>
                                                    <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.</div>
                                                    <div class="text-muted small mt-1">15m ago</div>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="list-group-item">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <img src="img/avatars/avatar-2.jpg" class="avatar img-fluid rounded-circle" alt="William Harris">
                                                </div>
                                                <div class="col-10 ps-2">
                                                    <div class="text-dark">William Harris</div>
                                                    <div class="text-muted small mt-1">Curabitur ligula sapien euismod vitae.</div>
                                                    <div class="text-muted small mt-1">2h ago</div>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="list-group-item">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <img src="img/avatars/avatar-4.jpg" class="avatar img-fluid rounded-circle" alt="Christina Mason">
                                                </div>
                                                <div class="col-10 ps-2">
                                                    <div class="text-dark">Christina Mason</div>
                                                    <div class="text-muted small mt-1">Pellentesque auctor neque nec urna.</div>
                                                    <div class="text-muted small mt-1">4h ago</div>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="list-group-item">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <img src="img/avatars/avatar-3.jpg" class="avatar img-fluid rounded-circle" alt="Sharon Lessman">
                                                </div>
                                                <div class="col-10 ps-2">
                                                    <div class="text-dark">Sharon Lessman</div>
                                                    <div class="text-muted small mt-1">Aenean tellus metus, bibendum sed, posuere ac, mattis non.</div>
                                                    <div class="text-muted small mt-1">5h ago</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="dropdown-menu-footer">
                                        <a href="#" class="text-muted">Show all messages</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <!-- Ícone para telas pequenas -->
                                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                    <i class="align-middle" data-feather="settings"></i>
                                </a>
                            
                                <!-- Nome e Avatar -->
                                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ Auth::user()->avatar 
                                        ? route('user.avatar', ['id' => Auth::user()->id])  
                                        : asset('assets/images/general/avatar_default.png') }}" 
                                        class="avatar-profile-mini">
                                    <span class="text-dark">{{ Auth::user()->name }}</span>
                                </a>
                            
                                <!-- Dropdown Menu -->
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="user"></i> Perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="settings"></i> Definições e Privacidade</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Centro de Ajuda</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item"><i class="align-middle me-1" data-feather="log-out"></i> Sair</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            
                            
                        </ul>
                    </div>
                </nav>

                    @yield('content_admin') 
          
                @include('layouts.partials.footer_admin')

            </div>
        </div>
    
        

    
        
    
    </body>
    

    


<script src="{{ asset('assets/js/app.js') }}"></script>
 <!-- Main JS File -->
 <script src="{{ asset('assets/js/main.js') }}"></script>

</html>
