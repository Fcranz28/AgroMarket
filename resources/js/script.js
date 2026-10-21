import { escapeHtml } from './utils/escape';
document.addEventListener('DOMContentLoaded', () => {
    const cartBtn = document.getElementById('openCart');

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
                    <a href="/producto/${escapeHtml(product.slug)}">
                        <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(product.name)}" class="product-image">
                    </a>
                </div>
                <div class="product-info">
                    <a href="/producto/${escapeHtml(product.slug)}" style="text-decoration: none; color: inherit;">
                        <h3 class="product-name">${escapeHtml(product.name)}</h3>
                    </a>
                    <p class="product-price">S/. ${price} / ${escapeHtml(product.unit)}</p>
                    
                    <button class="add-to-cart-btn" 
                            data-id="${escapeHtml(product.id)}"
                            data-name="${escapeHtml(product.name)}"
                            data-price="${price}"
                            data-image="${escapeHtml(imageUrl)}"
                            data-unit="${escapeHtml(product.unit)}">
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
});
