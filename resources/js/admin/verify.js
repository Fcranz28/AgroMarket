// Admin Verification AJAX functionality
document.addEventListener('DOMContentLoaded', function () {
   const dniForm = document.getElementById('dniForm');
   const dniInput = document.getElementById('dni_input');
   const consultarBtn = document.getElementById('consultarBtn');
   const resultContainer = document.getElementById('apiResultContainer');

   if (dniForm) {
      dniForm.addEventListener('submit', function (e) {
         e.preventDefault();

         const dni = dniInput.value.trim();
         const userId = document.querySelector('input[name="user_id"]').value;

         if (dni.length !== 8 || !/^\d+$/.test(dni)) {
            showError('Por favor ingrese un DNI válido de 8 dígitos');
            return;
         }

         // Show loading state
         consultarBtn.disabled = true;
         consultarBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Consultando...';

         // Get CSRF token
         const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

         // Make AJAX request
         fetch('/admin/usuarios/consultar-dni', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': csrfToken,
               'Accept': 'application/json'
            },
            body: JSON.stringify({
               dni: dni,
               user_id: userId
            })
         })
            .then(response => response.json())
            .then(data => {
               if (data.success) {
                  showSuccess(data.data);
               } else {
                  showError(data.message || 'Error al consultar el DNI');
               }
            })
            .catch(error => {
               console.error('Error:', error);
               showError('Error de conexión. Por favor intente nuevamente.');
            })
            .finally(() => {
               // Reset button state
               consultarBtn.disabled = false;
               consultarBtn.innerHTML = '<i class="fas fa-search me-2"></i>Consultar';
            });
      });
   }

   function showSuccess(data) {
      const html = `
            <div class="api-result-card mb-4 animate-fade-in">
                <div class="result-header">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span class="fw-bold text-success">Datos Encontrados</span>
                </div>
                <div class="result-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="result-label">Nombre Completo</label>
                            <div class="result-value">${data.first_name} ${data.first_last_name} ${data.second_last_name}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="result-label">DNI</label>
                            <div class="result-value">${data.document_number}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="result-label">Estado</label>
                            <div class="result-value text-success">
                                <i class="fas fa-check-circle me-1 small"></i> Validado
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

      resultContainer.innerHTML = html;
   }

   function showError(message) {
      const html = `
            <div class="alert alert-danger d-flex align-items-center mb-4 animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                <div>
                    <strong>Error en la consulta:</strong> ${message}
                </div>
            </div>
        `;

      resultContainer.innerHTML = html;
   }
});
