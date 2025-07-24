@extends('layouts.master_home')

@section('content')
<main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div id="hero-carousel" data-bs-interval="3000" class="container carousel carousel-fade" data-bs-ride="carousel">

        <!-- Slide 1 -->
<div class="carousel-item active">
    <div class="carousel-container">
      <h2 class="animate__animated animate__fadeInDown">Bem-vindo à <span>{{ config('app.name') }}</span></h2>
      <p class="animate__animated animate__fadeInUp">
        Descubra a nossa plataforma inovadora, concebida para transformar a gestão dos infantários e creches. Com a {{ config('app.name') }}, simplifica a administração, melhora a comunicação e garante um ambiente seguro para o desenvolvimento dos mais pequenos.
      </p>
      <a href="#about" class="btn-get-started animate__animated animate__fadeInUp scrollto">Ler Mais</a>
    </div>
  </div>
  
  <!-- Slide 2 -->
  <div class="carousel-item">
    <div class="carousel-container">
      <h2 class="animate__animated animate__fadeInDown">Gestão e Comunicação Eficiente</h2>
      <p class="animate__animated animate__fadeInUp">
        A {{ config('app.name') }} oferece uma gestão centralizada e intuitiva que permite acompanhar, em tempo real, todas as atividades e processos do seu estabelecimento. A comunicação direta com os encarregados de educação facilita a partilha de informações essenciais.
      </p>
      <a href="#about" class="btn-get-started animate__animated animate__fadeInUp scrollto">Ler Mais</a>
    </div>
  </div>
  
  <!-- Slide 3 -->
  <div class="carousel-item">
    <div class="carousel-container">
      <h2 class="animate__animated animate__fadeInDown">Inovação e Segurança</h2>
      <p class="animate__animated animate__fadeInUp">
        Unimos tecnologia de ponta a práticas rigorosas de cibersegurança para proteger os dados e garantir a confiabilidade do sistema. Descubra como a {{ config('app.name') }} pode revolucionar o quotidiano da sua instituição.
      </p>
      <a href="#about" class="btn-get-started animate__animated animate__fadeInUp scrollto">Ler Mais</a>
    </div>
  </div>
  

        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>

        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>

    </div>

      <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28 " preserveAspectRatio="none">
        <defs>
          <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
        </defs>
        <g class="wave1">
          <use xlink:href="#wave-path" x="50" y="3"></use>
        </g>
        <g class="wave2">
          <use xlink:href="#wave-path" x="50" y="0"></use>
        </g>
        <g class="wave3">
          <use xlink:href="#wave-path" x="50" y="9"></use>
        </g>
      </svg>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Sobre</h2>
        <p>Quem somos nós</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

            <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
              <p>
                A <strong>{{ config('app.name') }}</strong> foi fundada em 2025 após identificarmos uma lacuna significativa na comunicação entre pais e infantários. A nossa paixão pela inovação impulsionou-nos a criar uma plataforma que simplifica a gestão e melhora a comunicação em tempo real.
              </p>
              <ul>
                <li><i class="bi bi-check2-circle"></i> <span>Gestão integrada de turmas, salas e recursos pedagógicos.</span></li>
                <li><i class="bi bi-check2-circle"></i> <span>Comunicação directa e eficaz entre pais e creches.</span></li>
                <li><i class="bi bi-check2-circle"></i> <span>Plataforma móvel intuitiva e segura.</span></li>
              </ul>
            </div>
          
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
              <p>
                Somos especialistas em desenvolvimento web, aplicações para Android e iOS, cibersegurança, gestão de servidores e formação em engenharia informática. O nosso conhecimento e excelência no trabalho permitem-nos criar soluções tecnológicas que transformam a gestão dos infantários e creches, oferecendo um serviço de elevada qualidade.
              </p>
              <a href="#" class="read-more"><span>Leia Mais</span><i class="bi bi-arrow-right"></i></a>
            </div>
          
          </div>
          

      </div>

    </section><!-- /About Section -->

    <!-- Features Section -->
<section id="features" class="features section">

    <div class="container">
  
      <ul class="nav nav-tabs row d-flex" data-aos="fade-up" data-aos-delay="100">
        <li class="nav-item col-3">
          <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
            <i class="bi bi-binoculars"></i>
            <h4 class="d-none d-lg-block">Gestão Integrada</h4>
          </a>
        </li>
        <li class="nav-item col-3">
          <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
            <i class="bi bi-box-seam"></i>
            <h4 class="d-none d-lg-block">Comunicação Eficiente</h4>
          </a>
        </li>
        <li class="nav-item col-3">
          <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
            <i class="bi bi-brightness-high"></i>
            <h4 class="d-none d-lg-block">Plataforma Móvel</h4>
          </a>
        </li>
        <li class="nav-item col-3">
          <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-4">
            <i class="bi bi-command"></i>
            <h4 class="d-none d-lg-block">Segurança &amp; Suporte</h4>
          </a>
        </li>
      </ul><!-- End Tab Nav -->
  
      <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
  
        <!-- Tab Content Item 1 -->
        <div class="tab-pane fade active show" id="features-tab-1">
          <div class="row">
            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
              <h3>Gestão Integrada para Infantários e Creches</h3>
              <p class="fst-italic">
                A plataforma {{ config('app.name') }} centraliza a gestão escolar, permitindo uma administração organizada e eficaz.
              </p>
              <ul>
                <li><i class="bi bi-check2-all"></i> <span>Organização de turmas e alocação de salas.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Registo e acompanhamento dos alunos.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Gestão de horários e eventos escolares.</span></li>
              </ul>
              <p>
                Com relatórios detalhados e ferramentas intuitivas, terá controlo total sobre o funcionamento do seu estabelecimento.
              </p>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
              <img src="assets/img/working-1.jpg" alt="Gestão Integrada" class="img-fluid">
            </div>
          </div>
        </div><!-- End Tab Content Item 1 -->
  
        <!-- Tab Content Item 2 -->
        <div class="tab-pane fade" id="features-tab-2">
          <div class="row">
            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
              <h3>Comunicação Eficaz com os Encarregados de Educação</h3>
              <p>
                Melhore a interação entre pais e instituições com notificações em tempo real e uma comunicação transparente.
              </p>
              <p class="fst-italic">
                Mantenha os responsáveis informados sobre o progresso e as atividades dos seus filhos.
              </p>
              <ul>
                <li><i class="bi bi-check2-all"></i> <span>Mensagens e alertas instantâneos.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Atualizações diárias e relatórios de atividades.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Histórico de comunicações e eventos.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Interface intuitiva para fácil utilização.</span></li>
              </ul>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
              <img src="assets/img/working-2.jpg" alt="Comunicação Eficiente" class="img-fluid">
            </div>
          </div>
        </div><!-- End Tab Content Item 2 -->
  
        <!-- Tab Content Item 3 -->
        <div class="tab-pane fade" id="features-tab-3">
          <div class="row">
            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
              <h3>Plataforma Móvel Inovadora</h3>
              <p>
                A nossa aplicação móvel, disponível para Android e iOS, coloca a gestão e a comunicação na palma da mão.
              </p>
              <ul>
                <li><i class="bi bi-check2-all"></i> <span>Acesso em tempo real a informações e notificações.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Interface moderna e intuitiva.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Compatibilidade total com dispositivos móveis.</span></li>
              </ul>
              <p class="fst-italic">
                Simplifique o dia a dia com uma experiência móvel única e funcional.
              </p>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
              <img src="assets/img/working-3.jpg" alt="Plataforma Móvel" class="img-fluid">
            </div>
          </div>
        </div><!-- End Tab Content Item 3 -->
  
        <!-- Tab Content Item 4 -->
        <div class="tab-pane fade" id="features-tab-4">
          <div class="row">
            <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
              <h3>Segurança e Suporte Técnico de Excelência</h3>
              <p>
                A segurança dos seus dados é a nossa prioridade. Implementamos as melhores práticas de cibersegurança e oferecemos suporte técnico especializado.
              </p>
              <p class="fst-italic">
                Garantimos uma operação estável e protegida, 24 horas por dia.
              </p>
              <ul>
                <li><i class="bi bi-check2-all"></i> <span>Proteção avançada dos dados.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Monitorização e manutenção constantes.</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Suporte técnico dedicado e especializado.</span></li>
              </ul>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
              <img src="assets/img/working-4.jpg" alt="Segurança e Suporte" class="img-fluid">
            </div>
          </div>
        </div><!-- End Tab Content Item 4 -->
  
      </div>
  
    </div>
  </section><!-- /Features Section -->
  
   <!-- Call To Action Section -->
<section id="call-to-action" class="call-to-action section dark-background">

    <div class="container">
  
      <div class="row" data-aos="zoom-in" data-aos-delay="100">
        <div class="col-xl-9 text-center text-xl-start">
          <h3>Transforme a Gestão da Sua Instituição</h3>
          <p>
            Descubra a plataforma {{ config('app.name') }}, criada para revolucionar a comunicação entre pais e infantários. Simplifique a gestão, melhore a interação e garanta uma operação mais eficiente e segura.
          </p>
        </div>
        <div class="col-xl-3 cta-btn-container text-center">
          <a class="cta-btn align-middle" href="#">Experimente Já</a>
        </div>
      </div>
  
    </div>
  
  </section><!-- /Call To Action Section -->
  

    <!-- Services Section -->
<section id="services" class="services section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <h2>Serviços</h2>
      <p>O que oferecemos</p>
    </div><!-- End Section Title -->
  
    <div class="container">
  
      <div class="row gy-4">
  
        <!-- Serviço 1 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="service-item position-relative">
            <div class="icon">
              <i class="bi bi-building" style="color: #0dcaf0;"></i>
            </div>
            <a href="service-details.html" class="stretched-link">
              <h3>Gestão Integrada</h3>
            </a>
            <p>
              Administre infantários e creches com um sistema centralizado que organiza turmas, salas e registos escolares de forma eficiente.
            </p>
          </div>
        </div><!-- End Serviço 1 -->
  
        <!-- Serviço 2 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="service-item position-relative">
            <div class="icon">
              <i class="bi bi-chat-text" style="color: #fd7e14;"></i>
            </div>
            <a href="service-details.html" class="stretched-link">
              <h3>Comunicação Eficiente</h3>
            </a>
            <p>
              Melhore a interação entre a instituição e os encarregados de educação com notificações em tempo real e canais diretos de comunicação.
            </p>
          </div>
        </div><!-- End Serviço 2 -->
  
        <!-- Serviço 3 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
          <div class="service-item position-relative">
            <div class="icon">
              <i class="bi bi-phone" style="color: #20c997;"></i>
            </div>
            <a href="service-details.html" class="stretched-link">
              <h3>Plataforma Móvel</h3>
            </a>
            <p>
              Acesse todas as funcionalidades da gestão escolar através de uma aplicação móvel intuitiva, disponível para Android e iOS.
            </p>
          </div>
        </div><!-- End Serviço 3 -->
  
        <!-- Serviço 4 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
          <div class="service-item position-relative">
            <div class="icon">
              <i class="bi bi-shield-lock" style="color: #df1529;"></i>
            </div>
            <a href="service-details.html" class="stretched-link">
              <h3>Segurança Avançada</h3>
            </a>
            <p>
              Proteja os dados da sua instituição com soluções de cibersegurança de última geração, garantindo a integridade e a confidencialidade das informações.
            </p>
          </div>
        </div><!-- End Serviço 4 -->
  
        <!-- Serviço 5 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
          <div class="service-item position-relative">
            <div class="icon">
              <i class="bi bi-headset" style="color: #6610f2;"></i>
            </div>
            <a href="service-details.html" class="stretched-link">
              <h3>Suporte Técnico</h3>
            </a>
            <p>
              Conte com uma equipa de suporte dedicada para auxiliar na implementação, manutenção e resolução de questões técnicas.
            </p>
          </div>
        </div><!-- End Serviço 5 -->
  
        <!-- Serviço 6 -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
          <div class="service-item position-relative">
            <div class="icon">
              <i class="bi bi-code-slash" style="color: #f3268c;"></i>
            </div>
            <a href="service-details.html" class="stretched-link">
              <h3>Desenvolvimento Personalizado</h3>
            </a>
            <p>
              Soluções sob medida para as necessidades específicas do seu estabelecimento, desenvolvidas por especialistas em tecnologia.
            </p>
          </div>
        </div><!-- End Serviço 6 -->
  
      </div>
  
    </div>
  
  </section><!-- /Services Section -->
  
{{-- 
    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Portfolio</h2>
        <p>What we've done</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
            <li data-filter="*" class="filter-active">All</li>
            <li data-filter=".filter-app">App</li>
            <li data-filter=".filter-product">Card</li>
            <li data-filter=".filter-branding">Web</li>
          </ul><!-- End Portfolio Filters -->

          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-1.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>App 1</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-1.jpg" title="App 1" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-2.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Product 1</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-2.jpg" title="Product 1" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-3.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Branding 1</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-3.jpg" title="Branding 1" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-4.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>App 2</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-4.jpg" title="App 2" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-5.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Product 2</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-5.jpg" title="Product 2" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-6.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Branding 2</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-6.jpg" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-7.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>App 3</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-7.jpg" title="App 3" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-8.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Product 3</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-8.jpg" title="Product 3" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
              <img src="assets/img/masonry-portfolio/masonry-portfolio-9.jpg" class="img-fluid" alt="">
              <div class="portfolio-info">
                <h4>Branding 3</h4>
                <p>Lorem ipsum, dolor sit</p>
                <a href="assets/img/masonry-portfolio/masonry-portfolio-9.jpg" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->

          </div><!-- End Portfolio Container -->

        </div>

      </div>

    </section><!-- /Portfolio Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>What they are saying about us</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 1,
                  "spaceBetween": 40
                },
                "1200": {
                  "slidesPerView": 3,
                  "spaceBetween": 10
                }
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                <h3>Saul Goodman</h3>
                <h4>Ceo &amp; Founder</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                <h3>Sara Wilsson</h3>
                <h4>Designer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                <h3>Jena Karlis</h3>
                <h4>Store Owner</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                <h3>Matt Brandon</h3>
                <h4>Freelancer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                <h3>John Larson</h3>
                <h4>Entrepreneur</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor noster veniam enim culpa labore duis sunt culpa nulla illum cillum fugiat legam esse veniam culpa fore nisi cillum quid.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section --> --}}

    <!-- Pricing Section -->
    <section id="pricing" class="pricing section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Pricing</h2>
        <p>What they are saying about us</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-3">

          <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="pricing-item">
              <h3>Free</h3>
              <h4><sup>$</sup>0<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li class="na">Pharetra massa</li>
                <li class="na">Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div><!-- End Pricing Item -->

          <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="pricing-item featured">
              <h3>Business</h3>
              <h4><sup>$</sup>19<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li>Pharetra massa</li>
                <li class="na">Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div><!-- End Pricing Item -->

          <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="pricing-item">
              <h3>Developer</h3>
              <h4><sup>$</sup>29<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li>Pharetra massa</li>
                <li>Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div><!-- End Pricing Item -->

          <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="400">
            <div class="pricing-item">
              <span class="advanced">Advanced</span>
              <h3>Ultimate</h3>
              <h4><sup>$</sup>49<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li>Pharetra massa</li>
                <li>Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div><!-- End Pricing Item -->

        </div>

      </div>

    </section><!-- /Pricing Section -->

    <!-- Secção de Perguntas Frequentes -->
<section id="faq" class="faq section">
  <!-- Título da Secção -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Perguntas Frequentes</h2>
    <p>As dúvidas mais comuns sobre a nossa solução de gestão para infantários e creches.</p>
  </div><!-- Fim do Título da Secção -->

  <div class="container" data-aos="fade-up">
    <div class="row">
      <div class="col-12">
        <div class="custom-accordion" id="accordion-faq">

          <!-- Pergunta 1 -->
          <div class="accordion-item">
            <h2 class="mb-0">
              <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-faq-1">
                O que é a vossa solução de gestão para infantários?
              </button>
            </h2>
            <div id="collapse-faq-1" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion-faq">
              <div class="accordion-body">
                A nossa plataforma é um sistema de gestão completo para infantários e creches, que permite a administração de inscrições, comunicação com os pais, registo de atividades diárias e muito mais.
              </div>
            </div>
          </div>
          <!-- Fim Pergunta 1 -->

          <!-- Pergunta 2 -->
          <div class="accordion-item">
            <h2 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-faq-2">
                Como posso testar a plataforma antes de comprar?
              </button>
            </h2>
            <div id="collapse-faq-2" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion-faq">
              <div class="accordion-body">
                Oferecemos um período de teste gratuito para que possa explorar todas as funcionalidades da plataforma. Basta entrar em contacto connosco e solicitar um acesso de demonstração.
              </div>
            </div>
          </div>
          <!-- Fim Pergunta 2 -->

          <!-- Pergunta 3 -->
          <div class="accordion-item">
            <h2 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-faq-3">
                Como funciona a gestão de pagamentos e mensalidades?
              </button>
            </h2>
            <div id="collapse-faq-3" class="collapse" aria-labelledby="headingThree" data-parent="#accordion-faq">
              <div class="accordion-body">
                A plataforma permite automatizar a cobrança de mensalidades, gerar faturas e enviar notificações de pagamento aos encarregados de educação. Também suporta múltiplos métodos de pagamento, incluindo transferência bancária e MB WAY.
              </div>
            </div>
          </div>
          <!-- Fim Pergunta 3 -->

          <!-- Pergunta 4 -->
          <div class="accordion-item">
            <h2 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-faq-4">
                A plataforma é segura para armazenar os dados das crianças?
              </button>
            </h2>
            <div id="collapse-faq-4" class="collapse" aria-labelledby="headingFour" data-parent="#accordion-faq">
              <div class="accordion-body">
                Sim, levamos a segurança e privacidade muito a sério. Utilizamos encriptação de dados e cumprimos com o Regulamento Geral de Proteção de Dados (RGPD) para garantir que todas as informações são protegidas.
              </div>
            </div>
          </div>
          <!-- Fim Pergunta 4 -->

          <!-- Pergunta 5 -->
          <div class="accordion-item">
            <h2 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-faq-5">
                Que tipo de suporte oferecem?
              </button>
            </h2>
            <div id="collapse-faq-5" class="collapse" aria-labelledby="headingFive" data-parent="#accordion-faq">
              <div class="accordion-body">
                Oferecemos suporte técnico através de email, chat e telefone. Também disponibilizamos formação inicial e uma base de conhecimento com tutoriais para ajudar na utilização da plataforma.
              </div>
            </div>
          </div>
          <!-- Fim Pergunta 5 -->

        </div>
      </div>
    </div>
  </div>
</section>
<!-- Fim da Secção de Perguntas Frequentes -->


    <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Equipa</h2>
        <p>Os Nossos Especialistas</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-8 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/images/team/team-1.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Samuel Parente</h4>
                <span>Desenvolvedor Web Fullstack</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-8 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/images/team/team-2.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Ivo Baptista</h4>
                <span>Desenvolvedor Aplicações Móveis</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-8 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/images/team/team-3.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Richard Brian</h4>
                <span>Especialista de Cibersegurança e Servidores</span>
              </div>
            </div>
          </div><!-- End Team Member -->

          {{-- <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
            <div class="team-member">
              <div class="member-img">
                <img src="assets/img/team/team-4.jpg" class="img-fluid" alt="">
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
              <div class="member-info">
                <h4>Amanda Jepson</h4>
                <span>Accountant</span>
              </div>
            </div>
          </div><!-- End Team Member --> --}}

        </div>

      </div>

    </section><!-- /Team Section -->

    {{-- <!-- Recent Posts Section -->
    <section id="recent-posts" class="recent-posts section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Recent Posts</h2>
        <p>Recent Blog Posts<br></p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <article>

              <div class="post-img">
                <img src="assets/img/blog/blog-1.jpg" alt="" class="img-fluid">
              </div>

              <p class="post-category">Politics</p>

              <h2 class="title">
                <a href="blog-details.html">Dolorum optio tempore voluptas dignissimos</a>
              </h2>

              <div class="d-flex align-items-center">
                <img src="assets/img/blog/blog-author.jpg" alt="" class="img-fluid post-author-img flex-shrink-0">
                <div class="post-meta">
                  <p class="post-author">Maria Doe</p>
                  <p class="post-date">
                    <time datetime="2022-01-01">Jan 1, 2022</time>
                  </p>
                </div>
              </div>

            </article>
          </div><!-- End post list item -->

          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <article>

              <div class="post-img">
                <img src="assets/img/blog/blog-2.jpg" alt="" class="img-fluid">
              </div>

              <p class="post-category">Sports</p>

              <h2 class="title">
                <a href="blog-details.html">Nisi magni odit consequatur autem nulla dolorem</a>
              </h2>

              <div class="d-flex align-items-center">
                <img src="assets/img/blog/blog-author-2.jpg" alt="" class="img-fluid post-author-img flex-shrink-0">
                <div class="post-meta">
                  <p class="post-author">Allisa Mayer</p>
                  <p class="post-date">
                    <time datetime="2022-01-01">Jun 5, 2022</time>
                  </p>
                </div>
              </div>

            </article>
          </div><!-- End post list item -->

          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <article>

              <div class="post-img">
                <img src="assets/img/blog/blog-3.jpg" alt="" class="img-fluid">
              </div>

              <p class="post-category">Entertainment</p>

              <h2 class="title">
                <a href="blog-details.html">Possimus soluta ut id suscipit ea ut in quo quia et soluta</a>
              </h2>

              <div class="d-flex align-items-center">
                <img src="assets/img/blog/blog-author-3.jpg" alt="" class="img-fluid post-author-img flex-shrink-0">
                <div class="post-meta">
                  <p class="post-author">Mark Dower</p>
                  <p class="post-date">
                    <time datetime="2022-01-01">Jun 22, 2022</time>
                  </p>
                </div>
              </div>

            </article>
          </div><!-- End post list item -->

        </div><!-- End recent posts list -->

      </div>

    </section><!-- /Recent Posts Section --> --}}

    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contatos</h2>
        <p>Vamos conversar?</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Endereço</h3>
                <p>Rua 123, Celorico da Madeira</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Ligue-nos</h3>
                <p>+351 919 999 999</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email</h3>
                <p>info@example.com</p>
              </div>
            </div><!-- End Info Item -->

          </div>

          <div class="col-lg-8">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Nome" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Assunto" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Mensagem" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>

                  <button type="submit">Send Message</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>
@endsection
