<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="{{ route('homepage') }}" class="logo d-flex align-items-center">
        <img src="assets/images/website/logo_cores_rainbow_kids.png" alt="logotipo da rainbow kids">
        <h1 class="sitename">{{ config('app.name') }}</h1>
      </a>
      @include('layouts.partials.homepage_menu')
      
    </div>
  </header>
