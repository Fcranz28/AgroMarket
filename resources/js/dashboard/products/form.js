// Dashboard Products Form Scripts (Create/Edit)
document.addEventListener('DOMContentLoaded', function () {
   // Dynamic Stock Logic
   const unitOptions = document.querySelectorAll('.unit-option');
   const stockContainer = document.getElementById('stock-container');

   unitOptions.forEach(option => {
      option.addEventListener('click', function () {
         const checkbox = this.querySelector('input[type="checkbox"]');
         checkbox.checked = !checkbox.checked;

         if (checkbox.checked) {
            this.classList.add('active');
         } else {
            this.classList.remove('active');
         }

         updateStockInputs();
      });
   });

   function updateStockInputs() {
      const selectedUnits = [];
      document.querySelectorAll('input[name="unit[]"]:checked').forEach(cb => {
         selectedUnits.push({
            value: cb.value,
            text: cb.nextElementSibling.textContent
         });
      });

      const currentInputs = {};
      const currentPrices = {};

      // Save current values
      stockContainer.querySelectorAll('input[name="stock[]"]').forEach(input => {
         currentInputs[input.dataset.unit] = input.value;
      });
      stockContainer.querySelectorAll('input[name="price[]"]').forEach(input => {
         currentPrices[input.dataset.unit] = input.value;
      });

      stockContainer.innerHTML = '';

      if (selectedUnits.length === 0) {
         stockContainer.innerHTML = `
            <div class="empty-stock-state">
               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
               <p>Seleccione unidades para asignar stock y precio</p>
            </div>`;
         return;
      }

      selectedUnits.forEach(unit => {
         const stockValue = currentInputs[unit.value] || '';
         const priceValue = currentPrices[unit.value] || '';

         const div = document.createElement('div');
         div.className = 'stock-item';
         div.innerHTML = `
            <div class="stock-label">${unit.text}</div>
            <div class="stock-input-group">
               <div style="flex: 1;">
                  <label style="font-size: 0.75rem; color: #718096; margin-bottom: 0.25rem; display: block">Stock</label>
                  <input type="number" class="product-form-control" name="stock[]" placeholder="Cantidad" value="${stockValue}" required data-unit="${unit.value}">
               </div>
               <div style="flex: 1;">
                  <label style="font-size: 0.75rem; color: #718096; margin-bottom: 0.25rem; display: block;">Precio (S/.)</label>
                  <input type="number" step="0.01" class="product-form-control" name="price[]" placeholder="0.00" value="${priceValue}" required data-unit="${unit.value}">
               </div>
            </div>
         `;
         stockContainer.appendChild(div);
      });
   }

   // --- Image Upload Logic ---
   const dropZone = document.getElementById('dropZone');
   const imageInput = document.getElementById('imageInput');
   const uploadPlaceholder = document.getElementById('uploadPlaceholder');
   const previewContainer = document.getElementById('previewContainer');

   if (dropZone && imageInput) {
      // Trigger file input on click
      dropZone.addEventListener('click', () => imageInput.click());

      // Prevent default drag behaviors
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
         dropZone.addEventListener(eventName, preventDefaults, false);
         document.body.addEventListener(eventName, preventDefaults, false);
      });

      function preventDefaults(e) {
         e.preventDefault();
         e.stopPropagation();
      }

      // Highlight drop zone when item is dragged over it
      ['dragenter', 'dragover'].forEach(eventName => {
         dropZone.addEventListener(eventName, highlight, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
         dropZone.addEventListener(eventName, unhighlight, false);
      });

      function highlight(e) {
         dropZone.classList.add('highlight');
      }

      function unhighlight(e) {
         dropZone.classList.remove('highlight');
      }

      // Handle dropped files
      dropZone.addEventListener('drop', handleDrop, false);

      function handleDrop(e) {
         const dt = e.dataTransfer;
         const files = dt.files;
         imageInput.files = files; // Update input files
         handleFiles(files);
      }

      imageInput.addEventListener('change', function () {
         handleFiles(this.files);
      });

      function handleFiles(files) {
         if (files.length > 0) {
            uploadPlaceholder.style.display = 'none';
            previewContainer.style.display = 'grid'; // Use grid for multiple images
            previewContainer.innerHTML = ''; // Clear previous previews

            Array.from(files).forEach(file => {
               if (file.type.startsWith('image/')) {
                  const reader = new FileReader();
                  reader.onload = (e) => {
                     const previewDiv = document.createElement('div');
                     previewDiv.className = 'image-preview-item';
                     previewDiv.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                     `;
                     previewContainer.appendChild(previewDiv);
                  };
                  reader.readAsDataURL(file);
               }
            });
         } else {
            uploadPlaceholder.style.display = 'flex';
            previewContainer.style.display = 'none';
         }
      }
   }
});
