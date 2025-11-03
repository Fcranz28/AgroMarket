const productos = [
    {
        id: 1,
        nombre: "Manzanas Orgánicas",
        categoria: "frutas",
        precio: 2.99,
        unidad: "kg",
        imagen: "img/manzanas.jpg",
        descripcion: "Manzanas frescas cultivadas sin pesticidas"
    },
    {
        id: 2,
        nombre: "Tomates Cherry",
        categoria: "verduras",
        precio: 3.49,
        unidad: "500g",
        imagen: "img/tomates.jpg",
        descripcion: "Tomates cherry dulces y jugosos"
    },
    {
        id: 3,
        nombre: "Papas Amarillas",
        categoria: "tuberculos",
        precio: 1.99,
        unidad: "kg",
        imagen: "img/papas.jpg",
        descripcion: "Papas amarillas de primera calidad"
    },
    {
        id: 4,
        nombre: "Frijoles Canario",
        categoria: "granos",
        precio: 4.99,
        unidad: "kg",
        imagen: "img/frijoles.jpg",
        descripcion: "Frijoles canario seleccionados"
    },
    {
        id: 5,
        nombre: "Albahaca Fresca",
        categoria: "hierbas",
        precio: 1.50,
        unidad: "manojo",
        imagen: "img/albahaca.jpg",
        descripcion: "Albahaca aromática fresca"
    },
    {
        id: 6,
        nombre: "Semillas de Tomate",
        categoria: "semillas",
        precio: 5.99,
        unidad: "100g",
        imagen: "img/semillas.jpg",
        descripcion: "Semillas de tomate certificadas"
    }
];

// Función para crear las cards de productos
function createProductCard(producto) {
    return `
        <div class="product-card" data-product-id="${producto.id}">
            <div class="product-image">
                <img src="${producto.imagen}" alt="${producto.nombre}">
            </div>
            <div class="product-info">
                <h3>${producto.nombre}</h3>
                <p class="product-category">${producto.categoria}</p>
                <p class="product-description">${producto.descripcion}</p>
                <div class="product-price-cart">
                    <p class="price">S/. ${producto.precio} <span>/${producto.unidad}</span></p>
                    <button class="add-to-cart">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-8.9-5h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4l-3.87 7H8.53L4.27 2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Función para mostrar productos en el carrusel
function initProductCarousel() {
    const carousel = document.querySelector('.products-carousel .carousel-content');
    carousel.innerHTML = ''; // Limpiar el contenido existente
    
    // Duplicar los productos para el efecto infinito
    const repeatedProducts = [...productos, ...productos, ...productos];
    repeatedProducts.forEach(producto => {
        carousel.innerHTML += createProductCard(producto);
    });

    // Variables para el desplazamiento suave
    let isDown = false;
    let startX;
    let scrollLeft;
    let autoScrollInterval;
    
    // Función para el auto-scroll
    function startAutoScroll() {
        clearInterval(autoScrollInterval);
        autoScrollInterval = setInterval(() => {
            carousel.style.scrollBehavior = 'smooth';
            carousel.scrollLeft += 1;
            
            if (carousel.scrollLeft >= (carousel.scrollWidth - carousel.clientWidth - 10)) {
                carousel.style.scrollBehavior = 'auto';
                carousel.scrollLeft = carousel.scrollWidth / 3;
                setTimeout(() => {
                    carousel.style.scrollBehavior = 'smooth';
                }, 10);
            }
        }, 30);
    }

    function stopAutoScroll() {
        clearInterval(autoScrollInterval);
    }
    
    // Eventos táctiles y de mouse para desplazamiento suave
    carousel.addEventListener('mousedown', (e) => {
        isDown = true;
        carousel.style.cursor = 'grabbing';
        startX = e.pageX - carousel.offsetLeft;
        scrollLeft = carousel.scrollLeft;
        carousel.style.scrollBehavior = 'auto';
        stopAutoScroll();
    });

    carousel.addEventListener('mouseleave', () => {
        isDown = false;
        carousel.style.cursor = 'grab';
        startAutoScroll();
    });

    carousel.addEventListener('mouseup', () => {
        isDown = false;
        carousel.style.cursor = 'grab';
        carousel.style.scrollBehavior = 'smooth';
        startAutoScroll();
    });

    carousel.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 2;
        carousel.scrollLeft = scrollLeft - walk;
    });

    // Eventos táctiles para dispositivos móviles
    let touchStartX;
    let touchScrollLeft;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].pageX - carousel.offsetLeft;
        touchScrollLeft = carousel.scrollLeft;
        carousel.style.scrollBehavior = 'auto';
        stopAutoScroll();
    });

    carousel.addEventListener('touchmove', (e) => {
        e.preventDefault();
        const x = e.touches[0].pageX - carousel.offsetLeft;
        const walk = (x - touchStartX) * 2;
        carousel.scrollLeft = touchScrollLeft - walk;
    });

    carousel.addEventListener('touchend', () => {
        carousel.style.scrollBehavior = 'smooth';
        startAutoScroll();
    });

    // Efecto de scroll infinito
    carousel.addEventListener('scroll', () => {
        if (carousel.scrollLeft === 0) {
            carousel.style.scrollBehavior = 'auto';
            carousel.scrollLeft = carousel.scrollWidth / 3;
            setTimeout(() => {
                carousel.style.scrollBehavior = 'smooth';
            }, 10);
        } else if (carousel.scrollLeft >= (carousel.scrollWidth - carousel.clientWidth - 10)) {
            carousel.style.scrollBehavior = 'auto';
            carousel.scrollLeft = carousel.scrollWidth / 3;
            setTimeout(() => {
                carousel.style.scrollBehavior = 'smooth';
            }, 10);
        }
    });

    // Iniciar auto-scroll
    startAutoScroll();
}


// Función para mostrar productos aleatorios
function showRandomProducts() {
    const randomSection = document.querySelector('.random-products .products-grid');
    const shuffledProducts = [...productos].sort(() => 0.5 - Math.random());
    const selectedProducts = shuffledProducts.slice(0, 6);
    
    selectedProducts.forEach(producto => {
        randomSection.innerHTML += createProductCard(producto);
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    initProductCarousel();
    showRandomProducts();
});
