document.addEventListener('DOMContentLoaded', () => {
    const productsGrid = document.querySelector('.products-grid');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const sortSelect = document.querySelector('.sort-select');
    const categoryTitle = document.querySelector('.category-title');
    const paginationContainer = document.querySelector('.pagination');
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

    async function loadProducts() {
        showLoading(); // Función que ya tienes
        
        // 1. Obtener el texto del botón activo para el título
        const activeButton = document.querySelector('.category-btn.active');
        const categoryName = activeButton ? activeButton.textContent.trim() : 'Todos los Productos';
        categoryTitle.textContent = categoryName;

        try {
            // 2. Construir la URL con los parámetros de filtro y orden
            const url = `/api/productos?category=${currentCategory}&sort=${currentSort}`;
            
            const response = await fetch(url); // 3. Llamar a la API con los filtros
            if (!response.ok) {
                // Si la API falla, muestra un error
                throw new Error(`Error HTTP: ${response.status} (${response.statusText})`);
            }
            
            const data = await response.json();
            const products = Array.isArray(data.products) ? data.products : [];
            
            renderProducts(products); // Tu función para dibujar las cards
            updateActiveCategory(currentCategory); // Tu función para marcar el botón
        
        } catch (error) {
            console.error('No se pudieron cargar los productos:', error);
            productsGrid.innerHTML = `
                <div class="error-message">
                    <p>Lo sentimos, ha ocurrido un error al cargar los productos.</p>
                    <p><i>${error.message}</i></p>
                    <button id="retryLoad">Intentar de nuevo</button>
                </div>
            `;
            const retry = document.getElementById('retryLoad');
            if (retry) retry.addEventListener('click', loadProducts);
        }
    }

    function renderProducts(products) {
        if (!products || products.length === 0) {
            productsGrid.innerHTML = `
                <div class="no-products">
                    <p>No se encontraron productos.</p>
                </div>
            `;
            return;
        }

        productsGrid.innerHTML = products.map(product => {
            const image = product.image_path ? `/storage/${product.image_path}` : '/img/placeholder.png';
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

    function updateActiveCategory(category) {
        categoryButtons.forEach(button => {
            button.classList.toggle('active', button.dataset.category === category);
        });
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentCategory = button.dataset.category;
            loadProducts();
        });
    });

    sortSelect.addEventListener('change', () => {
        currentSort = sortSelect.value;
        loadProducts();
    });

    loadProducts();
});
