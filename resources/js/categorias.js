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

    // NO ejecutar en el dashboard de agricultores
    if (window.location.pathname.startsWith('/agricultor/')) {
        return;
    }

    let currentCategory = 'all';
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
            let image = '/img/placeholder.png';
            if (product.image_path) {
                image = `/storage/${product.image_path}`;
            } else if (product.image_url) {
                image = product.image_url;
            }
            const price = Number(product.price || 0).toFixed(2);
            const categoryName = product.category_name || product.category || 'Productos';

            return `
            <article class="product-card" data-product-id="${product.id}">
                <a href="/producto/${product.slug}">
                    <div class="product-image-container">
                        <img src="${image}" alt="${product.name}" class="product-image" loading="lazy">
                    </div>
                </a>
                <div class="product-info">
                    <p class="product-category">${categoryName}</p>
                    <a href="/producto/${product.slug}" style="text-decoration: none; color: inherit;">
                        <h3 class="product-title">${product.name}</h3>
                    </a>
                    <div class="product-footer">
                        <p class="product-price">S/. ${price}</p>
                        <button class="btn-add-cart" data-id="${product.id}"></button>
                    </div>
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

    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            currentSort = sortSelect.value;
            loadProducts(); // Recarga los productos con el nuevo orden
        });
    }

    // Carga inicial de productos
    loadProducts();

    // --- MOBILE SIDEBAR LOGIC ---
    const mobileFilterBtn = document.getElementById('mobileFilterBtn');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');

    function openSidebar() {
        if (mobileSidebar) mobileSidebar.classList.add('open');
        if (mobileSidebarOverlay) mobileSidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeSidebar() {
        if (mobileSidebar) mobileSidebar.classList.remove('open');
        if (mobileSidebarOverlay) mobileSidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (mobileFilterBtn) {
        mobileFilterBtn.addEventListener('click', openSidebar);
    }

    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', closeSidebar);
    }

    if (mobileSidebarOverlay) {
        mobileSidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar when a category is selected (on mobile)
    const mobileCategoryButtons = document.querySelectorAll('.mobile-category-list .category-btn');
    mobileCategoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            closeSidebar();
            // The existing logic will handle the category selection and product loading
            // because we are using the same class .category-btn
        });
    });
});
