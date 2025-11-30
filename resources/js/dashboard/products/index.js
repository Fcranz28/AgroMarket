// Dashboard Products Index Scripts
document.addEventListener('DOMContentLoaded', function () {
   // Handle delete confirmations with SweetAlert
   document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', function (e) {
         e.preventDefault();
         const form = this.closest('.delete-form');
         const productName = this.dataset.productName;

         Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar "${productName}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#718096',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
         }).then((result) => {
            if (result.isConfirmed) {
               form.submit();
            }
         });
      });
   });
});
