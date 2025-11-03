document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showRegisterFormBtn = document.getElementById('showRegisterForm');
    const showLoginFormBtn = document.getElementById('showLoginForm');
    const togglePanels = document.querySelectorAll('.toggle-panel');

    // Verificar si hay que mostrar el formulario de registro
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('form') === 'register') {
        toggleForms(true);
    }

    // Función para cambiar entre formularios
    function toggleForms(showRegister) {
        if (showRegister) {
            loginForm.classList.remove('active');
            registerForm.classList.add('active');
        } else {
            registerForm.classList.remove('active');
            loginForm.classList.add('active');
        }

        // Alternar paneles
        togglePanels.forEach(panel => panel.classList.toggle('hidden'));
    }

    // Event listeners para los botones de cambio
    showRegisterFormBtn.addEventListener('click', () => toggleForms(true));
    showLoginFormBtn.addEventListener('click', () => toggleForms(false));

    // Manejar envío de formularios
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // Aquí iría la lógica de inicio de sesión
        console.log('Iniciando sesión...');
    });

    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        // Aquí iría la lógica de registro
        console.log('Registrando usuario...');
    });

    // Manejar clics en botones sociales
    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const platform = e.currentTarget.classList[1]; // google, facebook, o instagram
            console.log(`Iniciando sesión con ${platform}`);
            // Aquí iría la lógica de inicio de sesión social
        });
    });
});