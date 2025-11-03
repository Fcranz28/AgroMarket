@extends('layouts.app')

@section('content')
    <section class="hero-section">
        <div class="hero-content" data-aos="fade-up">
            <h1>Conectando el Campo con el Futuro</h1>
            <p>Transformando la agricultura peruana a través de la innovación y el comercio justo</p>
        </div>
    </section>

    <section class="mission-section">
        <div class="mission-container">
            <div class="mission-card" data-aos="fade-right">
                <h2>Nuestra Misión</h2>
                <p>Empoderar a los agricultores peruanos conectándolos directamente con los consumidores, promoviendo el comercio justo y sostenible mientras facilitamos el acceso a tecnología y recursos que mejoren su productividad.</p>
            </div>
            <div class="mission-card" data-aos="fade-left">
                <h2>Nuestra Visión</h2>
                <p>Ser la plataforma líder en la transformación digital del sector agrícola peruano, creando un ecosistema donde la tecnología y la tradición se unen para construir un futuro más próspero para nuestras comunidades agrícolas.</p>
            </div>
        </div>
    </section>

    <section class="impact-section">
        <h2 class="section-title" data-aos="fade-up">Nuestro Impacto</h2>
        <div class="impact-stats">
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-number" data-target="1500">0</div>
                <p>Agricultores Beneficiados</p>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-number" data-target="25">0</div>
                <p>Comunidades Alcanzadas</p>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-number" data-target="40">0</div>
                <p>% Incremento en Ingresos</p>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="stat-number" data-target="10000">0</div>
                <p>Clientes Satisfechos</p>
            </div>
        </div>
    </section>

    <section class="values-section">
        <h2 class="section-title" data-aos="fade-up">Nuestros Valores</h2>
        <div class="values-container">
            <div class="value-card" data-aos="flip-left" data-aos-delay="100">
                <div class="value-icon">🤝</div>
                <h3>Compromiso Social</h3>
                <p>Trabajamos por el desarrollo sostenible de las comunidades agrícolas.</p>
            </div>
            <div class="value-card" data-aos="flip-left" data-aos-delay="200">
                <div class="value-icon">🌱</div>
                <h3>Sostenibilidad</h3>
                <p>Promovemos prácticas agrícolas responsables con el medio ambiente.</p>
            </div>
            <div class="value-card" data-aos="flip-left" data-aos-delay="300">
                <div class="value-icon">💡</div>
                <h3>Innovación</h3>
                <p>Integramos tecnología para mejorar la eficiencia y productividad.</p>
            </div>
            <div class="value-card" data-aos="flip-left" data-aos-delay="400">
                <div class="value-icon">⚖️</div>
                <h3>Comercio Justo</h3>
                <p>Garantizamos precios justos y condiciones equitativas.</p>
            </div>
        </div>
    </section>

    <section class="team-section">
        <h2 class="section-title" data-aos="fade-up">Nuestro Equipo</h2>
        <div class="team-container">
            <div class="team-card" data-aos="fade-up" data-aos-delay="100">
                <div class="team-photo-placeholder"></div>
                <h3>María Rodriguez</h3>
                <p class="team-role">Directora Ejecutiva</p>
                <p class="team-desc">Experta en desarrollo agrícola sostenible con 15 años de experiencia.</p>
            </div>
            <div class="team-card" data-aos="fade-up" data-aos-delay="200">
                <div class="team-photo-placeholder"></div>
                <h3>Carlos Mendoza</h3>
                <p class="team-role">Director de Tecnología</p>
                <p class="team-desc">Especialista en soluciones tecnológicas para el sector agrícola.</p>
            </div>
            <div class="team-card" data-aos="fade-up" data-aos-delay="300">
                <div class="team-photo-placeholder"></div>
                <h3>Ana Torres</h3>
                <p class="team-role">Gerente de Desarrollo Comunitario</p>
                <p class="team-desc">Dedicada a fortalecer vínculos con comunidades agrícolas.</p>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-container" data-aos="fade-up">
            <h2>Sé Parte del Cambio</h2>
            <p>Únete a nuestra comunidad y ayuda a transformar el sector agrícola peruano</p>
            <div class="cta-buttons">
                <a href="#" class="cta-btn primary">Únete como Agricultor</a>
                <a href="#" class="cta-btn secondary">Conoce Más</a>
            </div>
        </div>
    </section>
@endsection