@extends('layouts.app')

@section('content')
    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-info">
                <h3>Contáctanos</h3>
                <p class="info-description">Estamos aquí para responder tus preguntas y escuchar tus comentarios. No dudes en ponerte en contacto con nosotros.</p>
                
                <div class="info-items">
                    <div class="info-item">
                        <span class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <div class="info-content">
                            <span class="info-label">Dirección:</span>
                            <p>Sector Angostura km. 10, Cusco 08000</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">
                            <i class="fas fa-phone"></i>
                        </span>
                        <div class="info-content">
                            <span class="info-label">Teléfono:</span>
                            <p>+51 941 451 076</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        <div class="info-content">
                            <span class="info-label">Email:</span>
                            <p>franzaguilar28@gmail.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">
                            <i class="fas fa-globe"></i>
                        </span>
                        <div class="info-content">
                            <span class="info-label">Sitio Web:</span>
                            <p>www.AgroMarket.org</p>
                        </div>
                    </div>
                </div>
            </div>

            <form class="contact-form" id="contactForm">
                <h2>Envíanos un mensaje</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="Ej: juan@email.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="Ej: 987654321" required>
                    </div>
                    <div class="form-group">
                        <label for="asunto">Asunto</label>
                        <input type="text" id="asunto" name="asunto" placeholder="Ej: Consulta sobre productos" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="7" placeholder="Escribe tu mensaje aquí..." required></textarea>
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="submit-btn">Enviar Mensaje</button>
                </div>
            </form>
        </div>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.964560140294!2d-77.03197642570847!3d-12.046639642354217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c8b5d35662c7%3A0x15f0bda5ccbd31eb!2sPlaza%20Mayor%20de%20Lima!5e0!3m2!1ses!2spe!4v1698990843595!5m2!1ses!2spe" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>
@endsection