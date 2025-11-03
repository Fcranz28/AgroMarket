@extends('layouts.app')

@section('content')
    <div class="container auth-container">
        <div class="forms-container">
            <!-- Formulario de Registro -->
            <form class="register-form" id="registerForm">
                <h2>Crear Cuenta</h2>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" placeholder="Nombre completo" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" placeholder="Correo electrónico" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Contraseña" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" placeholder="Confirmar contraseña" required>
                </div>
                <button type="submit" class="submit-btn">Registrarse</button>
                
                <div class="social-login">
                    <p>O regístrate con</p>
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
@endsection