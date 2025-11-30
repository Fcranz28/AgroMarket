document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    const bottomNavLinks = document.querySelectorAll('.bottom-nav-mobile a');
    const cartBtn = document.getElementById('openCart');

    // Manejar el tema oscuro/claro
    function updateTheme() {
        if (!themeToggle) return;
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const isDarkTheme = currentTheme === 'dark';
        const themeIcon = themeToggle.querySelector('.theme-icon');

        if (!themeIcon) return;

        if (isDarkTheme) {
            themeIcon.innerHTML = '\u003cpath d=\"M12,3c-4.97,0-9,4.03-9,9s4.03,9,9,9s9-4.03,9-9c0-0.46-0.04-0.92-0.1-1.36c-0.98,1.37-2.58,2.26-4.4,2.26 c-2.98,0-5.4-2.42-5.4-5.4c0-1.81,0.89-3.42,2.26-4.4C12.92,3.04,12.46,3,12,3L12,3z\"/\u003e';
        } else {
            themeIcon.innerHTML = '\u003cpath d=\"M12,9c1.65,0,3,1.35,3,3s-1.35,3-3,3s-3-1.35-3-3S10.35,9,12,9 M12,7c-2.76,0-5,2.24-5,5s2.24,5,5,5s5-2.24,5-5 S14.76,7,12,7L12,7z M2,13l2,0c0.55,0,1-0.45,1-1s-0.45-1-1-1l-2,0c-0.55,0-1,0.45-1,1S1.45,13,2,13z M20,13l2,0c0.55,0,1-0.45,1-1 s-0.45-1-1-1l-2,0c-0.55,0-1,0.45-1,1S19.45,13,20,13z M11,2v2c0,0.55,0.45,1,1,1s1-0.45,1-1V2c0-0.55-0.45-1-1-1S11,1.45,11,2z M11,20v2c0,0.55,0.45,1,1,1s1-0.45,1-1v-2c0-0.55-0.45-1-1-1C11.45,19,11,19.45,11,20z M5.99,4.58c-0.39-0.39-1.03-0.39-1.41,0 c-0.39,0.39-0.39,1.03,0,1.41l1.06,1.06c0.39,0.39,1.03,0.39,1.41,0s0.39-1.03,0-1.41L5.99,4.58z M18.36,16.95 c-0.39-0.39-1.03-0.39-1.41,0c-0.39,0.39-0.39,1.03,0,1.41l1.06,1.06c0.39,0.39,1.03,0.39,1.41,0c0.39-0.39,0.39-1.03,0-1.41 L18.36,16.95z M19.42,5.99c0.39-0.39,0.39-1.03,0-1.41c-0.39-0.39-1.03-0.39-1.41,0l-1.06,1.06c-0.39,0.39-0.39,1.03,0,1.41 s1.03,0.39,1.41,0L19.42,5.99z M7.05,18.36c0.39-0.39,0.39-1.03,0-1.41c-0.39-0.39-1.03-0.39-1.41,0l-1.06,1.06 c-0.39,0.39-0.39,1.03,0,1.41s1.03,0.39,1.41,0L7.05,18.36z\"/\u003e';
        }
    }

    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        updateTheme();
    } else if (savedTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        updateTheme();
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateTheme();
        });
    }

    // Manejar la navegación móvil
    bottomNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            bottomNavLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        });
    });

    // Agregar evento al botón del carrito en la barra superior
    // Agregar evento al botón del carrito en la barra superior
    if (cartBtn) {
        cartBtn.addEventListener('click', () => {
            const cartSidebar = document.getElementById('cartSidebar');
            if (cartSidebar) {
                cartSidebar.classList.add('open');
            }
        });
    }

    // Agregar evento al botón de cerrar carrito
    const closeCartBtn = document.getElementById('closeCart');
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', () => {
            const cartSidebar = document.getElementById('cartSidebar');
            if (cartSidebar) {
                cartSidebar.classList.remove('open');
            }
        });
    }

    // Cerrar carrito al hacer clic fuera
    document.addEventListener('click', (e) => {
        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar && cartSidebar.classList.contains('open') && !cartSidebar.contains(e.target) && !cartBtn.contains(e.target)) {
            cartSidebar.classList.remove('open');
        }
    });

    // --- INICIO DE LA SOLUCIÓN ---

    const gridContainer = document.querySelector('.products-grid');
    const carouselContainer = document.querySelector('.carousel-content');

    // En: resources/js/script.js

    // /**
    //  * Crea el HTML para una tarjeta de producto.
    //  * @param {object} product - El objeto del producto.
    //  * @returns {string} - El string HTML de la card.
    //  */
    function createProductCard(product) {
        // Usar la URL de imagen del servidor
        let imageUrl = '/img/placeholder.png';
        if (product.image_path) {
            imageUrl = `/storage/${product.image_path}`;
        } else if (product.image_url) {
            imageUrl = product.image_url;
        }
        const price = Number(product.price || 0).toFixed(2);

        return `
            <div class="product-card">
                <div class="product-image-container">
                    <a href="/producto/${product.slug}">
                        <img src="${imageUrl}" alt="${product.name}" class="product-image">
                    </a>
                </div>
                <div class="product-info">
                    <a href="/producto/${product.slug}" style="text-decoration: none; color: inherit;">
                        <h3 class="product-name">${product.name}</h3>
                    </a>
                    <p class="product-price">S/. ${price} / ${product.unit}</p>
                    
                    <button class="add-to-cart-btn" 
                            data-id="${product.id}"
                            data-name="${product.name}"
                            data-price="${price}"
                            data-image="${imageUrl}"
                            data-unit="${product.unit}">
                        Agregar al Carrito
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Obtiene todos los productos de la API.
     * @returns {Promise<Array>} - Una promesa que resuelve a un array de productos.
     */
    async function fetchProducts() {
        try {
            // Llama a la ruta definida en routes/api.php
            const response = await fetch('/api/productos');
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data.products || []; // Devuelve los productos o un array vacío
        } catch (error) {
            console.error('No se pudieron cargar los productos:', error);
            return []; // Devuelve un array vacío en caso de error
        }
    }

    /**
     * Muestra productos aleatorios en el grid con funcionalidad "Ver Más".
     */
    async function showRandomProducts() {
        if (!gridContainer) {
            console.warn('No se encontró el contenedor .products-grid');
            return;
        }

        const loadMoreBtn = document.querySelector('.load-more-btn');
        const products = await fetchProducts();

        // Mezclar productos una sola vez
        const shuffledProducts = products.sort(() => 0.5 - Math.random());

        let currentIndex = 0;
        const itemsPerPage = 8;

        // Función para renderizar el siguiente lote
        const loadNextBatch = () => {
            const nextBatch = shuffledProducts.slice(currentIndex, currentIndex + itemsPerPage);

            nextBatch.forEach(product => {
                gridContainer.innerHTML += createProductCard(product);
            });

            currentIndex += itemsPerPage;

            // Ocultar botón si no hay más productos
            if (currentIndex >= shuffledProducts.length) {
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            }
        };

        gridContainer.innerHTML = ''; // Limpiar inicial
        loadNextBatch(); // Cargar primeros 8

        // Event listener para el botón
        if (loadMoreBtn) {
            // Remover listeners anteriores para evitar duplicados si la función se llama varias veces
            const newBtn = loadMoreBtn.cloneNode(true);
            loadMoreBtn.parentNode.replaceChild(newBtn, loadMoreBtn);

            newBtn.addEventListener('click', () => {
                loadNextBatch();
            });
        }
    }

    /**
     * Muestra los productos más nuevos en el carrusel.
     */
    async function initProductCarousel() {
        if (!carouselContainer) {
            console.warn('No se encontró el contenedor .carousel-content');
            return;
        }

        const products = await fetchProducts();

        // La API ya los devuelve por 'latest('id')', así que solo tomamos los primeros 5
        // Para el loop infinito, necesitamos suficientes items. Si hay pocos, los duplicamos más veces.
        let carouselProducts = products.slice(0, 10); // Tomar más productos si es posible

        carouselContainer.innerHTML = ''; // Limpiar el contenedor

        // Función helper para renderizar
        const renderCards = (items) => {
            items.forEach(product => {
                carouselContainer.innerHTML += createProductCard(product);
            });
        };

        // Renderizar set original
        renderCards(carouselProducts);

        // Duplicar el contenido para el efecto de loop infinito
        // Si hay pocos productos, duplicar varias veces para llenar la pantalla
        if (carouselProducts.length > 0) {
            renderCards(carouselProducts); // Primer duplicado
            if (carouselProducts.length < 5) {
                renderCards(carouselProducts); // Segundo duplicado si son pocos
                renderCards(carouselProducts); // Tercer duplicado
            }
        }

        // Agregar clase de animación
        carouselContainer.classList.add('animate-scroll');
    }

    // Inicializar el carrusel y los productos aleatorios
    // (Estas líneas ya existían en tu script original)
    if (typeof initProductCarousel === 'function') {
        initProductCarousel();
    }
    if (typeof showRandomProducts === 'function') {
        showRandomProducts();
    }

    // --- FIN DE LA SOLUCIÓN ---

    // Mobile User Dropdown
    const mobileDropdownTrigger = document.querySelector('.mobile-dropdown-trigger');
    const mobileDropdownMenu = document.querySelector('.mobile-dropdown-menu');

    if (mobileDropdownTrigger && mobileDropdownMenu) {
        mobileDropdownTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileDropdownMenu.classList.toggle('show');

            // Rotar la flecha
            const arrow = mobileDropdownTrigger.querySelector('.dropdown-arrow');
            if (arrow) {
                arrow.style.transform = mobileDropdownMenu.classList.contains('show')
                    ? 'rotate(180deg)'
                    : 'rotate(0deg)';
            }
        });

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!mobileDropdownTrigger.contains(e.target) && !mobileDropdownMenu.contains(e.target)) {
                mobileDropdownMenu.classList.remove('show');
                const arrow = mobileDropdownTrigger.querySelector('.dropdown-arrow');
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
        });

        // Cerrar dropdown al seleccionar una opción
        const dropdownLinks = mobileDropdownMenu.querySelectorAll('a, button[type="submit"]');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileDropdownMenu.classList.remove('show');
                const arrow = mobileDropdownTrigger.querySelector('.dropdown-arrow');
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });
    }
});
