<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - AgroMercado</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="nosotros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>
<body>
    <nav class="floating-navbar">
        <div class="logo-container">
            <a href="index.html">
               <img src="img/Logo_Claro.png" alt="Logo" class="logo logo-claro">
               <img src="img/Logo_Oscuro.png" alt="Logo" class="logo logo-oscuro">
            </a>
        </div>
        
        <div class="nav-links">
            <a href="index.html">Inicio</a>
            <a href="#">Productos</a>
            <a href="contacto.html">Contacto</a>
            <a href="nosotros.html" class="active">Nosotros</a>
        </div>

        <div class="nav-right">
            <div class="search-container">
                <select class="category-select">
                    <option value="todos">Todas las Categorias</option>
                    <option value="frutas">Frutas</option>
                    <option value="verduras">Verduras y Hortalizas</option>
                    <option value="tuberculos">Tubérculos y Raíces</option>
                    <option value="granos">Granos y Legumbres</option>
                    <option value="hierbas">Hierbas y Aromáticas</option>
                    <option value="semillas">Semillas y Plantones</option>
                    <option value="insumos">Insumos Agrícolas</option>
                    <option value="herramientas">Herramientas Manuales</option>
                    <option value="maquinaria">Maquinaria Agrícola</option>
                    <option value="riego">Sistemas de Riego</option>
                    <option value="tecnologia">Tecnología Agrícola</option>
                </select>
                <input type="text" class="search-input" placeholder="Buscar...">
                <button class="search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </button>
            </div>
            
            <button class="btn cart-btn" id="openCart">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <span class="cart-count">0</span>
            </button>
            <button class="btn login-btn">Iniciar sesión</button>
            <button class="btn register-btn">Registrarse</button>
            
            <button id="themeToggle" class="theme-toggle">
                <svg class="theme-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,9c1.65,0,3,1.35,3,3s-1.35,3-3,3s-3-1.35-3-3S10.35,9,12,9 M12,7c-2.76,0-5,2.24-5,5s2.24,5,5,5s5-2.24,5-5 S14.76,7,12,7L12,7z M2,13l2,0c0.55,0,1-0.45,1-1s-0.45-1-1-1l-2,0c-0.55,0-1,0.45-1,1S1.45,13,2,13z M20,13l2,0c0.55,0,1-0.45,1-1 s-0.45-1-1-1l-2,0c-0.55,0-1,0.45-1,1S19.45,13,20,13z M11,2v2c0,0.55,0.45,1,1,1s1-0.45,1-1V2c0-0.55-0.45-1-1-1S11,1.45,11,2z M11,20v2c0,0.55,0.45,1,1,1s1-0.45,1-1v-2c0-0.55-0.45-1-1-1C11.45,19,11,19.45,11,20z M5.99,4.58c-0.39-0.39-1.03-0.39-1.41,0 c-0.39,0.39-0.39,1.03,0,1.41l1.06,1.06c0.39,0.39,1.03,0.39,1.41,0s0.39-1.03,0-1.41L5.99,4.58z M18.36,16.95 c-0.39-0.39-1.03-0.39-1.41,0c-0.39,0.39-0.39,1.03,0,1.41l1.06,1.06c0.39,0.39,1.03,0.39,1.41,0c0.39-0.39,0.39-1.03,0-1.41 L18.36,16.95z M19.42,5.99c0.39-0.39,0.39-1.03,0-1.41c-0.39-0.39-1.03-0.39-1.41,0l-1.06,1.06c-0.39,0.39-0.39,1.03,0,1.41 s1.03,0.39,1.41,0L19.42,5.99z M7.05,18.36c0.39-0.39,0.39-1.03,0-1.41c-0.39-0.39-1.03-0.39-1.41,0l-1.06,1.06 c-0.39,0.39-0.39,1.03,0,1.41s1.03,0.39,1.41,0L7.05,18.36z"/>
                </svg>
            </button>
        </div>
    </nav>

    <main class="content">
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
    </main>

    <!-- Sidebar del carrito -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Tu Carrito</h3>
            <button class="close-cart" id="closeCart">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        <div class="cart-content" id="cartContent">
            <!-- Los items del carrito se insertarán aquí -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">Total: S/. <span id="cartTotal">0.00</span></div>
            <button class="checkout-button" id="checkoutButton">Proceder al Pago</button>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-column">
                <h4>Acerca de</h4>
                <ul>
                    <li><a href="#">Nuestra Historia</a></li>
                    <li><a href="#">Misión y Visión</a></li>
                    <li><a href="#">Equipo</a></li>
                    <li><a href="#">Trabaja con Nosotros</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Ayuda</h4>
                <ul>
                    <li><a href="#">Centro de Ayuda</a></li>
                    <li><a href="#">Envíos</a></li>
                    <li><a href="#">Devoluciones</a></li>
                    <li><a href="#">Estado del Pedido</a></li>
                    <li><a href="#">Formas de Pago</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Contacto</h4>
                <ul>
                    <li><a href="#">WhatsApp</a></li>
                    <li><a href="#">Email</a></li>
                    <li><a href="#">Teléfono</a></li>
                    <li><a href="#">Sucursales</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Síguenos</h4>
                <div class="social-links">
                    <a href="#" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 0 1 1.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 0 1-1.153 1.772 4.915 4.915 0 0 1-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 0 1-1.772-1.153 4.904 4.904 0 0 1-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 0 1 1.153-1.772A4.897 4.897 0 0 1 5.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.25a1.25 1.25 0 0 0-2.5 0 1.25 1.25 0 0 0 2.5 0zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 AgroMercado. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script src="carrito.js"></script>
    <script src="nosotros.js"></script>
</body>
</html>