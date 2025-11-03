document.addEventListener('DOMContentLoaded', function() {
    // Selecciona todos los inputs dentro de un .input-group
    const inputs = document.querySelectorAll('.input-group input');

    // Función para activar el label
    function handleFocus() {
        // Añade la clase 'active' al div padre (.input-group)
        this.parentElement.classList.add('active');
    }

    // Función para desactivar el label si el input está vacío
    function handleBlur() {
        // Solo quita 'active' si el input está vacío
        if (this.value === '') {
            this.parentElement.classList.remove('active');
        }
    }

    inputs.forEach(input => {
        // Agrega los listeners
        input.addEventListener('focus', handleFocus);
        input.addEventListener('blur', handleBlur);

        // Comprobación inicial por si la página se carga con datos (ej. al fallar la validación)
        if (input.value !== '') {
            input.parentElement.classList.add('active');
        }
    });
});