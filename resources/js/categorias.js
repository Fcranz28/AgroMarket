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
        showLoading();
        try {
            const response = await fetch('/api/productos');
            if (!response.ok) throw new Error('Error al cargar los productos');
            const data = await response.json();
            const products = Array.isArray(data.products) ? data.products : [];
            categoryTitle.textContent = 'Todos los productos';
            renderProducts(products);
            paginationContainer.innerHTML = '';
            updateActiveCategory(currentCategory);
        } catch (error) {
            productsGrid.innerHTML = `
                <div class="error-message">
                    <p>Lo sentimos, ha ocurrido un error al cargar los productos.</p>
                    <button id="retryLoad">Intentar de nuevo</button>
                </div>
            `;
            const retry = document.getElementById('retryLoad');
            if (retry) retry.addEventListener('click', loadProducts);
            console.error('Error:', error);
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
