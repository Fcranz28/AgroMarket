let currentReportId = null;

window.viewReport = function (id) {
   currentReportId = id;
   fetch(`/admin/reportes/${id}`)
      .then(response => response.json())
      .then(report => {
         document.getElementById('modal-report-id').textContent = report.id;
         document.getElementById('modal-reporter').textContent = `${report.user.name} (${report.user.email})`;
         document.getElementById('modal-product').textContent = report.product.name;
         document.getElementById('modal-reason').textContent = report.reason;
         document.getElementById('modal-description').textContent = report.description;
         document.getElementById('modal-admin-notes').value = report.admin_notes || '';
         document.getElementById('modal-status').value = report.status;

         const evidenceContainer = document.getElementById('modal-evidence');
         evidenceContainer.innerHTML = '';
         if (report.evidence && report.evidence.length > 0) {
            report.evidence.forEach(img => {
               const imgEl = document.createElement('img');
               imgEl.src = img;
               imgEl.className = 'evidence-img';
               imgEl.onclick = () => window.open(img, '_blank');
               evidenceContainer.appendChild(imgEl);
            });
         } else {
            evidenceContainer.innerHTML = '<p class="text-muted" style="font-size: 0.9rem; font-style: italic;">No hay evidencias adjuntas.</p>';
         }

         const modal = document.getElementById('reportDetailModal');
         modal.style.display = 'flex';
         // Small delay to allow display:flex to apply before adding opacity class
         setTimeout(() => modal.classList.add('show'), 10);
      });
}

window.closeReportModal = function () {
   const modal = document.getElementById('reportDetailModal');
   modal.classList.remove('show');
   setTimeout(() => {
      modal.style.display = 'none';
      currentReportId = null;
   }, 300); // Match transition duration
}

window.updateReportStatus = function () {
   if (!currentReportId) return;

   const status = document.getElementById('modal-status').value;
   const notes = document.getElementById('modal-admin-notes').value;

   fetch(`/admin/reportes/${currentReportId}/status`, {
      method: 'PATCH',
      headers: {
         'Content-Type': 'application/json',
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ status: status, admin_notes: notes })
   })
      .then(response => response.json())
      .then(data => {
         Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: 'El estado del reporte ha sido actualizado.',
            timer: 1500,
            showConfirmButton: false
         }).then(() => {
            location.reload();
         });
      })
      .catch(error => {
         Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo actualizar el reporte.'
         });
      });
}

window.onclick = function (event) {
   const modal = document.getElementById('reportDetailModal');
   if (event.target == modal) {
      closeReportModal();
   }
}
