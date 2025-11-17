document.addEventListener('DOMContentLoaded', () => {
    // Seleccionamos los elementos de la vista
    const productsGrid = document.querySelector('.products-grid');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const sortSelect = document.querySelector('.sort-select');
    const categoryTitle = document.querySelector('.category-title');
    const paginationContainer = document.querySelector('.pagination');

    // Comprobación de seguridad (para evitar errores en otras páginas)
    if (!productsGrid) {
        // Si no estamos en la página de categorías, no hacemos nada.
        return; 
    }

    let currentCategory = 'todos';
    let currentSort = 'featured';

    function showLoading() {
        productsGrid.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Cargando productos...</p>
            </div>
        `;
    }

    /**
     * FUNCIÓN CORREGIDA
     * Carga productos desde la API usando los filtros actuales.
     */
    async function loadProducts() {
        showLoading();
        
        // 1. Obtener el texto del botón activo para el título
        const activeButton = document.querySelector('.category-btn.active');
        const categoryName = activeButton ? activeButton.textContent.trim() : 'Todos los Productos';
        categoryTitle.textContent = categoryName;

        try {
            // 2. SOLUCIÓN: Construir la URL con los parámetros de filtro y orden
            const url = `/api/productos?category=${currentCategory}&sort=${currentSort}`;
            
            const response = await fetch(url); // 3. Llamar a la API con los filtros
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            const products = Array.isArray(data.products) ? data.products : [];
            
            renderProducts(products); // Renderiza los productos
            paginationContainer.innerHTML = ''; // Limpiar paginación
            
        } catch (error) {
            console.error('Error:', error);
            productsGrid.innerHTML = `
                <div class="error-message">
                    <p>Lo sentimos, ha ocurrido un error al cargar los productos.</p>
                    <button id="retryLoad">Intentar de nuevo</button>
                </div>
            `;
            const retry = document.getElementById('retryLoad');
            if (retry) retry.addEventListener('click', loadProducts);
        }
    }

    /**
     * FUNCIÓN CORREGIDA (para la ruta de imagen)
     * Dibuja las cards de productos en el grid.
     */
    function renderProducts(products) {
        if (!products || products.length === 0) {
            productsGrid.innerHTML = `
                <div class="no-products">
                    <p>No se encontraron productos en esta categoría.</p>
                </div>
            `;
            return;
        }

        productsGrid.innerHTML = products.map(product => {
            // Usar la URL completa de la imagen
            const image = product.image_url || product.image_path || '/img/placeholder.png';
            const price = Number(product.price || 0).toFixed(2);
            return `
            <article class="product-card" data-product-id="${product.id}">
                <img src="${image}" alt="${product.name}" loading="lazy">
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <p class="price">S/. ${price}</p>
                    <button class="add-to-cart" data-id="${product.id}">Agregar al carrito</button>
                </div>
            </article>`;
        }).join('');
    }

    /**
     * Marca el botón de categoría como activo
     */
    function updateActiveCategory() {
        categoryButtons.forEach(button => {
            button.classList.toggle('active', button.dataset.category === currentCategory);
        });
    }

    // --- EVENT LISTENERS (Tu código original, que está correcto) ---

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentCategory = button.dataset.category;
            updateActiveCategory(); // Actualiza la clase 'active'
            loadProducts(); // Recarga los productos con el nuevo filtro
        });
    });

    sortSelect.addEventListener('change', () => {
        currentSort = sortSelect.value;
        loadProducts(); // Recarga los productos con el nuevo orden
    });

    // Carga inicial de productos
    loadProducts();
});
