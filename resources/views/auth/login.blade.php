<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroMercado - Iniciar Sesión</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <!-- Formulario de Inicio de Sesión -->
            <form class="login-form active" id="loginForm">
                <h2>Iniciar Sesión</h2>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="Correo electrónico" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Contraseña" required>
                </div>
                <div class="forgot-password">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="submit-btn">Iniciar Sesión</button>
                
                <div class="social-login">
                    <p>O inicia sesión con</p>
                    <div class="social-icons">
                        <button type="button" class="social-btn google">
                            <i class="fab fa-google"></i>
                            <span>Google</span>
                        </button>
                        <button type="button" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </button>
                        <button type="button" class="social-btn instagram">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle-panel">
                <h3>¿No tienes una cuenta?</h3>
                <p>Únete a nuestra comunidad y descubre los mejores productos del campo</p>
                <button type="button" id="showRegisterForm">Registrarse</button>
            </div>
            <div class="toggle-panel hidden">
                <h3>¿Ya tienes una cuenta?</h3>
                <p>Inicia sesión para acceder a tu cuenta y realizar tus compras</p>
                <button type="button" id="showLoginForm">Iniciar Sesión</button>
            </div>
        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>