<nav id="navmenu" class="navmenu">
    <ul>
      <li><a href="#hero" class="active">Início</a></li>
      <li><a href="#services">Serviços</a></li>
      <li><a href="#pricing">Planos</a></li>
      <li><a href="blog.html">Blog</a></li>
      <li class="dropdown">
        <a href="#">A Rainbow Kids <i class="bi bi-chevron-down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="#portfolio">Parceiros</a></li>
          <li><hr></li>
          <li><a href="#about">Sobre</a></li>
          <li><a href="#team">Equipa</a></li>
          <li><hr></li>
          <li><a href="#contact">Contatos</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#">Acesso <i class="bi bi-chevron-down"></i></a>
        <ul class="dropdown-menu">
          <li><a href="{{ route('register') }}">Registar</a></li>
          <li><a href="{{ route('login') }}">Entrar</a></li>
        </ul>
      </li>
    </ul>
    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
  </nav>
