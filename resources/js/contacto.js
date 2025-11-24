document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const formStatus = document.getElementById('formStatus');

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    // Show success message
                    formStatus.style.display = 'block';
                    formStatus.style.background = '#d4edda';
                    formStatus.style.color = '#155724';
                    formStatus.textContent = '¡Mensaje enviado exitosamente! Nos pondremos en contacto contigo pronto.';

                    // Reset form
                    form.reset();

                    // Scroll to message
                    formStatus.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    const data = await response.json();
                    throw new Error(data.error || 'Error al enviar el mensaje');
                }
            } catch (error) {
                console.error('Error:', error);
                formStatus.style.display = 'block';
                formStatus.style.background = '#f8d7da';
                formStatus.style.color = '#721c24';
                formStatus.textContent = 'Hubo un problema al enviar el mensaje. Por favor, intenta de nuevo.';
                formStatus.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar Mensaje';
            }
        });
    }
});
