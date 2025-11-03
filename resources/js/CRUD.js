document.addEventListener('DOMContentLoaded', function() {
    // Manejo del tema oscuro
    const themeToggle = document.getElementById('themeToggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    function toggleTheme() {
        document.body.setAttribute('data-theme', 
            document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'
        );
        localStorage.setItem('theme', document.body.getAttribute('data-theme'));
        updateThemeIcon();
    }

    function updateThemeIcon() {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const icon = themeToggle.querySelector('i');
        icon.className = isDark ? 'fas fa-moon' : 'fas fa-sun';
    }

    const savedTheme = localStorage.getItem('theme') || 
        (prefersDarkScheme.matches ? 'dark' : 'light');
    document.body.setAttribute('data-theme', savedTheme);
    updateThemeIcon();

    themeToggle.addEventListener('click', toggleTheme);

    // Referencias a elementos del DOM
    const showFormBtn = document.getElementById('showFormBtn');
    const productForm = document.getElementById('productForm');
    const cancelBtn = document.getElementById('cancelBtn');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('previewContainer');
    const description = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const unitButtons = document.querySelectorAll('.unit-btn');

    // Mostrar/ocultar formulario
    showFormBtn.addEventListener('click', () => {
        productForm.classList.add('active');
        showFormBtn.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        productForm.classList.remove('active');
        showFormBtn.style.display = 'block';
        resetForm();
    });

    // Manejo de unidades de medida
    unitButtons.forEach(button => {
        button.addEventListener('click', () => {
            unitButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
        });
    });

    // Contador de caracteres para descripción
    description.addEventListener('input', () => {
        const count = description.value.length;
        charCount.textContent = count;
        if (count > 950) {
            charCount.style.color = '#dc3545';
        } else {
            charCount.style.color = 'inherit';
        }
    });

    // Manejo de drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('drag-over');
        });
    });

    dropzone.addEventListener('drop', handleDrop);
    fileInput.addEventListener('change', handleFiles);

    document.querySelector('.browse-btn').addEventListener('click', () => {
        fileInput.click();
    });

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files } });
    }

    function handleFiles(e) {
        const files = [...e.target.files];
        files.forEach(previewFile);
    }

    function previewFile(file) {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function() {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${reader.result}" alt="Preview">
                <button class="remove-image">
                    <i class="fas fa-times"></i>
                </button>
            `;

            previewContainer.appendChild(previewItem);

            previewItem.querySelector('.remove-image').addEventListener('click', () => {
                previewItem.remove();
            });
        };
    }

    // Resetear formulario
    function resetForm() {
        document.querySelector('.product-form').reset();
        previewContainer.innerHTML = '';
        charCount.textContent = '0';
        unitButtons.forEach(btn => btn.classList.remove('active'));
        unitButtons[0].classList.add('active');
    }

    // Validación y envío del formulario
    document.querySelector('.product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Aquí iría la lógica para enviar los datos al servidor
        const formData = new FormData(this);
        const selectedUnit = document.querySelector('.unit-btn.active').dataset.unit;
        formData.append('unit', selectedUnit);

        // Simulación de éxito
        alert('Producto guardado correctamente');
        resetForm();
        productForm.classList.remove('active');
        showFormBtn.style.display = 'block';
    });

    // Manejo de botones de editar y eliminar en la lista de productos
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.product-card');
            // Aquí iría la lógica para cargar los datos del producto en el formulario
            productForm.classList.add('active');
            showFormBtn.style.display = 'none';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                const card = this.closest('.product-card');
                card.remove();
            }
        });
    });
});
