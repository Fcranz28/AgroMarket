@extends('layouts.app')

@push('styles')
{{-- Styles loaded via app.css → about.css --}}
@endpush

@section('content')
<div style="margin: 0; padding: 0; width: 100%;">
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="about-hero-content" data-aos="fade-up">
            <h1>Conectando el Campo con el Futuro</h1>
            <p>Transformando la agricultura peruana a través de la innovación tecnológica y el comercio justo</p>
            
            <div class="hero-stats">
                <div class="hero-stat" data-aos="zoom-in" data-aos-delay="100">
                    <span class="hero-stat-number" data-count="1500">0</span>
                    <span class="hero-stat-label">Agricultores</span>
                </div>
                <div class="hero-stat" data-aos="zoom-in" data-aos-delay="200">
                    <span class="hero-stat-number" data-count="25">0</span>
                    <span class="hero-stat-label">Comunidades</span>
                </div>
                <div class="hero-stat" data-aos="zoom-in" data-aos-delay="300">
                    <span class="hero-stat-number" data-count="10000">0</span>
                    <span class="hero-stat-label">Clientes</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="about-section">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Nuestra Razón de Ser</h2>
            <p class="section-subtitle">Comprometidos con el desarrollo sostenible del sector agrícola peruano</p>
        </div>

        <div class="mission-vision-grid">
            <div class="mission-card-new" data-aos="fade-right">
                <div class="mission-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Misión</h3>
                <p>Empoderar a los agricultores peruanos conectándolos directamente con los consumidores, promoviendo el comercio justo y sostenible mientras facilitamos el acceso a tecnología y recursos que mejoren su productividad y calidad de vida.</p>
            </div>

            <div class="mission-card-new" data-aos="fade-left">
                <div class="mission-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Visión</h3>
                <p>Ser la plataforma líder en la transformación digital del sector agrícola peruano, creando un ecosistema donde la tecnología y la tradición se unen para construir un futuro más próspero para nuestras comunidades agrícolas.</p>
            </div>
        </div>
    </section>

    <!-- Nuestros Valores -->
    <section class="about-section" style="background: var(--primary-bg);">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Nuestros Valores</h2>
            <p class="section-subtitle">Los principios que guían nuestro trabajo diario</p>
        </div>

        <div class="values-grid">
            <div class="value-item" data-aos="flip-up" data-aos-delay="100">
                <div class="value-icon-wrapper">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4>Compromiso Social</h4>
                <p>Trabajamos por el desarrollo sostenible de las comunidades agrícolas</p>
            </div>

            <div class="value-item" data-aos="flip-up" data-aos-delay="200">
                <div class="value-icon-wrapper">
                    <i class="fas fa-seedling"></i>
                </div>
                <h4>Sostenibilidad</h4>
                <p>Promovemos prácticas agrícolas responsables con el medio ambiente</p>
            </div>

            <div class="value-item" data-aos="flip-up" data-aos-delay="300">
                <div class="value-icon-wrapper">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h4>Innovación</h4>
                <p>Integramos tecnología para mejorar la eficiencia y productividad</p>
            </div>

            <div class="value-item" data-aos="flip-up" data-aos-delay="400">
                <div class="value-icon-wrapper">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h4>Comercio Justo</h4>
                <p>Garantizamos precios justos y condiciones equitativas</p>
            </div>
        </div>
    </section>

    <!-- Impacto -->
    <section class="about-section">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Nuestro Impacto</h2>
            <p class="section-subtitle">Resultados que transforman vidas</p>
        </div>

        <div class="impact-grid">
            <div class="impact-card" data-aos="zoom-in" data-aos-delay="100">
                <span class="impact-number" data-count="1500">0</span>
                <p class="impact-label">Agricultores Beneficiados</p>
            </div>

            <div class="impact-card" data-aos="zoom-in" data-aos-delay="200">
                <span class="impact-number" data-count="25">0</span>
                <p class="impact-label">Comunidades Alcanzadas</p>
            </div>

            <div class="impact-card" data-aos="zoom-in" data-aos-delay="300">
                <span class="impact-number" data-count="40">0</span>
                <span style="font-size: 2rem; margin-left: 0.25rem;">%</span>
                <p class="impact-label">Incremento en Ingresos</p>
            </div>

            <div class="impact-card" data-aos="zoom-in" data-aos-delay="400">
                <span class="impact-number" data-count="10000">0</span>
                <p class="impact-label">Clientes Satisfechos</p>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="about-section" style="background: var(--primary-bg);">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Nuestro Equipo</h2>
            <p class="section-subtitle">Las personas detrás de la transformación agrícola</p>
        </div>

        <div class="team-grid">
            <div class="team-member" data-aos="fade-up" data-aos-delay="100">
                <div class="team-photo">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="team-info">
                    <h4>María Rodríguez</h4>
                    <span class="team-role">Directora Ejecutiva</span>
                    <p class="team-description">Experta en desarrollo agrícola sostenible con 15 años de experiencia trabajando con comunidades rurales.</p>
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member" data-aos="fade-up" data-aos-delay="200">
                <div class="team-photo">
                    <img src="{{ asset('img/team/franz.jpg') }}" alt="Franz Aguilar" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="team-info">
                    <h4>Franz Aguilar</h4>
                    <span class="team-role">Director de Tecnología</span>
                    <p class="team-description">Desarrollador Full Stack y creador de AgroMarket. Apasionado por conectar la tecnología con el campo para impulsar el desarrollo agrícola.</p>
                    <div class="team-social">
                        <a href="www.linkedin.com/in/franz-kennedy-aguilar-cerna-5ab72226a" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://github.com/Fcranz28" target="_blank"><i class="fab fa-github"></i></a>
                        <a href="https://web.facebook.com/franz.aguilarcerna/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>

            <div class="team-member" data-aos="fade-up" data-aos-delay="300">
                <div class="team-photo">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="team-info">
                    <h4>Ana Torres</h4>
                    <span class="team-role">Gerente de Desarrollo Comunitario</span>
                    <p class="team-description">Dedicada a fortalecer vínculos con comunidades agrícolas y promover el desarrollo sostenible.</p>
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="cta-section-new">
        <div class="cta-content" data-aos="fade-up">
            <h2>Sé Parte del Cambio</h2>
            <p>Únete a nuestra comunidad y ayuda a transformar el sector agrícola peruano</p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="cta-btn-new cta-btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Únete como Agricultor
                </a>
                <a href="{{ route('products.index') }}" class="cta-btn-new cta-btn-secondary">
                    <i class="fas fa-shopping-bag"></i>
                    Explora Productos
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
@vite(['resources/js/about.js'])
@endpush