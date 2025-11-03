use App\Http\Controllers\Api\ProductApiController;

// Ruta para que 'categorias.js' obtenga productos con filtros
Route::get('/productos', [ProductApiController::class, 'index']);

document.addEventListener('DOMContentLoaded', () => {
    const productsGrid = document.querySelector('.products-grid');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const sortSelect = document.querySelector('.sort-select');
    const categoryTitle = document.querySelector('.category-title');
    const paginationContainer = document.querySelector('.pagination');
    let currentCategory = 'todos';
    let currentSort = 'newest';
    let currentPage = 1;

    // Función para mostrar el spinner de carga
    function showLoading() {
        productsGrid.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Cargando productos...</p>
            </div>
        `;
    }

    // Función para cargar los productos
    async function loadProducts(category = 'todos', sort = 'newest', page = 1) {
        showLoading();

        try {
            // En un entorno real, esta URL apuntaría a tu backend
            const response = await fetch(`api/productos.php?categoria=${category}&orden=${sort}&pagina=${page}`);
            
            if (!response.ok) {
                throw new Error('Error al cargar los productos');
            }

            const data = await response.json();
            
            // Actualizar el título de la categoría
            categoryTitle.textContent = data.categoryName || 'Todos los productos';
            
            // Renderizar los productos
            renderProducts(data.products);
            
            // Actualizar la paginación
            renderPagination(data.totalPages, page);
            
            // Actualizar el estado activo de los botones de categoría
            updateActiveCategory(category);
            
        } catch (error) {
            productsGrid.innerHTML = `
                <div class="error-message">
                    <p>Lo sentimos, ha ocurrido un error al cargar los productos.</p>
                    <button onclick="loadProducts('${category}', '${sort}', ${page})">
                        Intentar de nuevo
                    </button>
                </div>
            `;
            console.error('Error:', error);
        }
    }

    // Función para renderizar los productos
    function renderProducts(products) {
        if (!products || products.length === 0) {
            productsGrid.innerHTML = `
                <div class="no-products">
                    <p>No se encontraron productos en esta categoría.</p>
                </div>
            `;
            return;
        }

        productsGrid.innerHTML = products.map(product => `
            <article class="product-card">
                <img src="${product.imagen}" alt="${product.nombre}" loading="lazy">
                <div class="product-info">
                    <h3>${product.nombre}</h3>
                    <p class="price">$${product.precio.toFixed(2)}</p>
                    <button class="add-to-cart" data-id="${product.id}">
                        Agregar al carrito
                    </button>
                </div>
            </article>
        `).join('');
    }

    // Función para renderizar la paginación
    function renderPagination(totalPages, currentPage) {
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = '';
        
        // Botón Anterior
        paginationHTML += `
            <button 
                ${currentPage === 1 ? 'disabled' : ''} 
                onclick="loadProducts('${currentCategory}', '${currentSort}', ${currentPage - 1})"
            >
                Anterior
            </button>
        `;

        // Números de página
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 2 && i <= currentPage + 2)
            ) {
                paginationHTML += `
                    <button 
                        class="${i === currentPage ? 'active' : ''}"
                        onclick="loadProducts('${currentCategory}', '${currentSort}', ${i})"
                    >
                        ${i}
                    </button>
                `;
            } else if (
                i === currentPage - 3 || 
                i === currentPage + 3
            ) {
                paginationHTML += '<span>...</span>';
            }
        }

        // Botón Siguiente
        paginationHTML += `
            <button 
                ${currentPage === totalPages ? 'disabled' : ''} 
                onclick="loadProducts('${currentCategory}', '${currentSort}', ${currentPage + 1})"
            >
                Siguiente
            </button>
        `;

        paginationContainer.innerHTML = paginationHTML;
    }

    // Función para actualizar la categoría activa
    function updateActiveCategory(category) {
        categoryButtons.forEach(button => {
            button.classList.toggle('active', button.dataset.category === category);
        });
    }

    // Event Listeners
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const category = button.dataset.category;
            currentCategory = category;
            currentPage = 1;
            loadProducts(category, currentSort, currentPage);
        });
    });

    sortSelect.addEventListener('change', (e) => {
        currentSort = e.target.value;
        loadProducts(currentCategory, currentSort, currentPage);
    });

    // Cargar productos iniciales
    loadProducts();
});
