<!DOCTYPE html>
<html lang="pt">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Painel de administração da Nuterra">
	<meta name="author" content="Samuel Parente">
	<meta name="keywords" content="">
	<link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="{{ asset('images/general/logo_pv.ico') }}"/>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<title>{{ config('app.name') }}</title>
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: sans-serif;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('images/general/login/login-side.jpg') }}') center center / cover no-repeat;
            z-index: -2;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
       
    </style>
</head>
<body>
<div class="background"></div>
<div class="overlay"></div>

<div class="login-container">
    <div class="login-card">
        

        <img class="login-logo" src="{{ asset('images/general/logo-03.png') }}" alt="logotipo da nuterra">
        <div class="login-title mb-3">
            <span class="company">NUTERRA</span>
            <span class="separator">|</span>
            <span class="department">logistics</span>
        </div>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror" placeholder="email@exemplo.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" required class="form-control @error('password') is-invalid @enderror" placeholder="******">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-login px-4 py-2">
                    Entrar
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
