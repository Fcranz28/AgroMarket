@extends('layouts.app')

@push('scripts')
    @vite(['resources/css/contacto.css', 'resources/js/contacto.js'])
@endpush

@section('content')
    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-info">
                <h3>Contáctanos</h3>
                <p class="info-description">Estamos aquí para responder tus preguntas y escuchar tus comentarios. No dudes en ponerte en contacto con nosotros.</p>
                
                <div class="info-items">
                    <div class="info-item">
                        <span class="icon">
                            <img src="{{ asset('img/location-pin-svgrepo-com.svg') }}" alt="location">
                        </span>
                        <div class="info-content">
                            <span class="info-label">Dirección:</span>
                            <p>Sector Angostura km. 10, Cusco 08000</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">
                            <img src="{{ asset('img/phone-calling-svgrepo-com.svg') }}" alt="location">                            
                        </span>
                        <div class="info-content">
                            <span class="info-label">Teléfono:</span>
                            <p>+51 941 451 076</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">
                            <img src="{{ asset('img/gmail-old-svgrepo-com.svg') }}" alt="location">                            
                        </span>
                        <div class="info-content">
                            <span class="info-label">Email:</span>
                            <p>franzaguilar28@gmail.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">
                            <img src="{{ asset('img/web-svgrepo-com.svg') }}" alt="location">                            
                        </span>
                        <div class="info-content">
                            <span class="info-label">Sitio Web:</span>
                            <p>www.AgroMarket.org</p>
                        </div>
                    </div>
                </div>
            </div>

            <form class="contact-form" id="contactForm" action="https://formspree.io/f/xyzlyzzd" method="POST">
                <h2>Envíanos un mensaje</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="name" placeholder="Ej: Juan Pérez" required>
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
                        <select name="asunto" id="asunto">
                            <option value="" disabled selected>Selecciona un asunto</option>
                            <option value="consulta">Consulta sobre productos</option>
                            <option value="soporte">Soporte técnico</option>
                            <option value="sugerencia">Sugerencias</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="message" rows="7" placeholder="Escribe tu mensaje aquí..." required></textarea>
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="submit-btn">Enviar Mensaje</button>
                </div>
            </form>
        </div>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3878.6662255626034!2d-71.85895702394997!3d-13.556055371837957!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x916e7d4c9db42bf5%3A0xe56b22b37c2e89f9!2sUniversidad%20Continental%20-%20Campus%20Cusco!5e0!3m2!1ses-419!2spe!4v1762201294143!5m2!1ses-419!2spe" 
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
