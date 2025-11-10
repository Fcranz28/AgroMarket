// En: resources/js/carrito.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Selectores del DOM (basados en tu app.blade.php)
    const cartSidebar = document.getElementById('cartSidebar');
    const openCart = document.getElementById('openCart');
    const closeCart = document.getElementById('closeCart');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const cartCountElement = document.getElementById('cart-count');
    const cartSubtotalElement = document.getElementById('cart-subtotal');
    const checkoutButton = document.getElementById('checkout-btn');

    // --- Lógica existente para abrir/cerrar sidebar ---
    if (openCart) {
        openCart.addEventListener('click', (e) => {
            e.preventDefault();
            if (cartSidebar) cartSidebar.classList.add('open');
        });
    }

    if (closeCart) {
        closeCart.addEventListener('click', (e) => {
            e.preventDefault();
            if (cartSidebar) cartSidebar.classList.remove('open');
        });
    }
    
    document.addEventListener('click', (e) => {
        if (cartSidebar && !cartSidebar.contains(e.target) && !e.target.closest('#openCart')) {
            cartSidebar.classList.remove('open');
        }
    });

    // --- INICIO DE LA LÓGICA DEL CARRITO FALTANTE ---

    // Carga el carrito desde localStorage o lo inicia vacío
    let cart = JSON.parse(localStorage.getItem('agromarket_cart')) || [];

    // Función para guardar el carrito en localStorage
    function saveCart() {
        localStorage.setItem('agromarket_cart', JSON.stringify(cart));
        updateCartUI(); // Actualiza la UI cada vez que se guarda
    }

    // Función principal para añadir un producto
    function addToCart(product) {
        // Busca si el producto ya está en el carrito
        const existingProductIndex = cart.findIndex(item => item.id === product.id);

        if (existingProductIndex > -1) {
            // Si ya existe, incrementa la cantidad
            cart[existingProductIndex].quantity += 1;
        } else {
            // Si es nuevo, lo añade con cantidad 1
            product.quantity = 1;
            cart.push(product);
        }

        saveCart();
        showNotification(`${product.name} añadido al carrito`);
        if (cartSidebar) cartSidebar.classList.add('open'); // Abrir el carrito al añadir
    }

    // Función para actualizar el carrito (sidebar y contadores)
    function updateCartUI() {
        if (!cartItemsContainer) return; // No hacer nada si no existe el contenedor

        // 1. Limpiar el sidebar
        cartItemsContainer.innerHTML = '';
        let subtotal = 0;
        let totalItems = 0;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="cart-empty-message">Tu carrito está vacío.</p>';
            if(checkoutButton) checkoutButton.disabled = true;
        } else {
            // 2. Volver a dibujar los productos en el sidebar
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                totalItems += item.quantity;

                cartItemsContainer.innerHTML += `
                    <div class="cart-item" data-id="${item.id}">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <p class="cart-item-name">${item.name}</p>
                            <p class="cart-item-price">S/. ${item.price.toFixed(2)}</p>
                            <div class="cart-item-quantity">
                                <button class="quantity-btn decrease-btn">-</button>
                                <span>${item.quantity}</span>
                                <button class="quantity-btn increase-btn">+</button>
                            </div>
                        </div>
                        <button class="cart-item-remove">×</button>
                    </div>
                `;
            });
            if(checkoutButton) checkoutButton.disabled = false;
        }

        // 3. Actualizar totales
        if (cartSubtotalElement) cartSubtotalElement.textContent = `S/. ${subtotal.toFixed(2)}`;
        if (cartTotalElement) cartTotalElement.textContent = `S/. ${subtotal.toFixed(2)}`;
        if (cartCountElement) {
            cartCountElement.textContent = totalItems;
            cartCountElement.style.display = totalItems > 0 ? 'flex' : 'none';
        }
    }

    // Función para manejar cambios de cantidad o eliminar
    function handleCartActions(e) {
        const target = e.target;
        const itemElement = target.closest('.cart-item');
        if (!itemElement) return;

        const id = itemElement.dataset.id;
        const productIndex = cart.findIndex(item => item.id === id);
        if (productIndex === -1) return;

        if (target.classList.contains('increase-btn')) {
            cart[productIndex].quantity += 1;
            saveCart();
        } else if (target.classList.contains('decrease-btn')) {
            if (cart[productIndex].quantity > 1) {
                cart[productIndex].quantity -= 1;
            } else {
                // Si la cantidad es 1, eliminarlo
                cart.splice(productIndex, 1);
            }
            saveCart();
        } else if (target.classList.contains('cart-item-remove')) {
            cart.splice(productIndex, 1);
            saveCart();
        }
    }
    
    // Event listener para los botones DENTRO del sidebar
    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', handleCartActions);
    }

    // *** ¡LA PARTE MÁS IMPORTANTE! ***
    // Usamos "Event Delegation" para escuchar clics en todo el documento.
    // Esto captura clics en botones .add-to-cart-btn,
    // incluso si fueron creados dinámicamente por script.js o categorias.js
    document.addEventListener('click', (e) => {
        const clickedButton = e.target.closest('.add-to-cart-btn');

        if (clickedButton) {
            e.preventDefault();

            // Recoge los datos del producto desde los atributos data-*
            const product = {
                id: clickedButton.dataset.id,
                name: clickedButton.dataset.name,
                price: parseFloat(clickedButton.dataset.price),
                image: clickedButton.dataset.image,
                unit: clickedButton.dataset.unit
            };

            addToCart(product);
        }
    });

    // Función simple de notificación (CSS no incluido aquí)
    function showNotification(message) {
        let notif = document.createElement('div');
        notif.className = 'cart-notification show'; // Asegúrate de tener CSS para esto
        notif.textContent = message;
        document.body.appendChild(notif);

        setTimeout(() => {
            notif.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notif)) {
                    document.body.removeChild(notif);
                }
            }, 300);
        }, 2000);
    }

    // Carga inicial de la UI del carrito al entrar a la página
    updateCartUI();
});