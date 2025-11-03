document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Obtener los valores del formulario
        const nombre = document.getElementById('nombre').value;
        const email = document.getElementById('email').value;
        const telefono = document.getElementById('telefono').value;
        const asunto = document.getElementById('asunto').value;
        const mensaje = document.getElementById('mensaje').value;

        // Validar que todos los campos estén llenos
        if (!nombre || !email || !telefono || !asunto || !mensaje) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, complete todos los campos',
                icon: 'error',
                confirmButtonColor: 'var(--color-primary)'
            });
            return;
        }

        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, ingrese un email válido',
                icon: 'error',
                confirmButtonColor: 'var(--color-primary)'
            });
            return;
        }

        // Validar formato de teléfono (permite números y algunos caracteres especiales)
        const telefonoRegex = /^[0-9+\-\s()]+$/;
        if (!telefonoRegex.test(telefono)) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor, ingrese un número de teléfono válido',
                icon: 'error',
                confirmButtonColor: 'var(--color-primary)'
            });
            return;
        }

        // Aquí normalmente enviarías los datos al servidor
        // Por ahora solo mostraremos un mensaje de éxito
        Swal.fire({
            title: '¡Mensaje Enviado!',
            text: 'Gracias por contactarnos. Te responderemos pronto.',
            icon: 'success',
            confirmButtonColor: 'var(--color-primary)'
        }).then(() => {
            // Limpiar el formulario
            contactForm.reset();
        });
    });
});
