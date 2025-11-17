let carrito = [];
let sidebarVisible = false;
let _productosCache = null;

async function fetchProductos() {
    if (_productosCache) return _productosCache;
    const res = await fetch('/api/productos');
    if (!res.ok) throw new Error('No se pudo cargar productos');
    const data = await res.json();
    _productosCache = Array.isArray(data.products) ? data.products : [];
    return _productosCache;
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    const cartCountMobile = document.querySelector('.cart-count-mobile');
    const totalItems = carrito.reduce((total, item) => total + item.cantidad, 0);

    if (cartCount) {
        cartCount.textContent = totalItems;
    }
    if (cartCountMobile) {
        cartCountMobile.textContent = totalItems;
    }
}

function addToCart(producto) {
    const existingProduct = carrito.find(item => item.id === producto.id);
    if (existingProduct) {
        existingProduct.cantidad += 1;
    } else {
        carrito.push({
            ...producto,
            cantidad: 1
        });
    }
    updateCartSidebar();
    updateCartCount();
    saveCartToLocalStorage();
    Swal.fire({
        title: '¡Agregado!',
        text: `${producto.name} se agregó al carrito`,
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
    });
}

function removeFromCart(productId) {
    const producto = carrito.find(item => item.id === productId);
    if (producto) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar ${producto.name} del carrito?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                carrito = carrito.filter(item => item.id !== productId);
                updateCartSidebar();
                updateCartCount();
                saveCartToLocalStorage();
                Swal.fire({
                    title: 'Eliminado',
                    text: `${producto.name} ha sido eliminado del carrito`,
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    }
}

function updateQuantity(productId, newQuantity) {
    const product = carrito.find(item => item.id === productId);
    if (product) {
        if (newQuantity > 0) {
            product.cantidad = newQuantity;
            updateCartSidebar();
            updateCartCount();
            saveCartToLocalStorage();
        } else {
            removeFromCart(productId);
        }
    }
}

function calculateTotal() {
    return carrito.reduce((total, item) => total + (Number(item.price || 0) * item.cantidad), 0);
}

function updateCartSidebar() {
    const cartContent = document.querySelector('.cart-content');
    const totalElement = document.querySelector('#cartTotal');
    if (!cartContent) return;
    cartContent.innerHTML = '';
    carrito.forEach(item => {
        // Usar directamente la imagen_url que ya es una URL completa
        const image = item.image || '/img/placeholder.png';

        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.dataset.productId = item.id;
        itemElement.innerHTML = `
            <img src="${image}" alt="${item.name}">
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <p>S/. ${Number(item.price || 0).toFixed(2)} /${item.unit || ''}</p>
                <div class="quantity-controls">
                    <button class="qty-btn qty-decrease" data-action="decrease">-</button>
                    <span class="qty-display">${item.cantidad}</span>
                    <button class="qty-btn qty-increase" data-action="increase">+</button>
                </div>
                <button class="remove-btn" data-action="remove">Eliminar</button>
            </div>
        `;
        cartContent.appendChild(itemElement);
    });
    
    // Actualizar total
    const total = carrito.reduce((sum, item) => sum + (item.price * item.cantidad), 0);
    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }
}

function toggleCartSidebar() {
    const sidebar = document.querySelector('.cart-sidebar');
    sidebarVisible = !sidebarVisible;
    if (sidebar) sidebar.style.transform = sidebarVisible ? 'translateX(0)' : 'translateX(100%)';
    document.body.style.overflow = sidebarVisible ? 'hidden' : 'auto';
}

function saveCartToLocalStorage() {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('carrito');
    if (savedCart) {
        carrito = JSON.parse(savedCart);
        updateCartSidebar();
    }
}

// --- Lógica existente para abrir/cerrar sidebar ---
const openCart = document.getElementById('openCart');
const closeCart = document.getElementById('closeCart');
const cartSidebar = document.querySelector('.cart-sidebar');

if (openCart) {
    openCart.addEventListener('click', () => {
        if (cartSidebar) cartSidebar.classList.add('open');
    });
}

if (closeCart) {
    closeCart.addEventListener('click', () => {
        if (cartSidebar) cartSidebar.classList.remove('open');
    });
}

// Cierra el sidebar si se hace clic fuera de él
document.addEventListener('click', (e) => {
    if (cartSidebar && !cartSidebar.contains(e.target) && !e.target.closest('#openCart')) {
        cartSidebar.classList.remove('open');
    }
});

// Inicializar el carrito
document.addEventListener('DOMContentLoaded', () => {
    const cartButton = document.getElementById('openCart');
    const closeCartButton = document.getElementById('closeCart');
    const checkoutButton = document.getElementById('checkoutButton');
    const cartButtonMobile = document.querySelector('.cart-button-mobile');
    const cartContent = document.querySelector('.cart-content');

    if (cartButton) cartButton.addEventListener('click', toggleCartSidebar);
    if (closeCartButton) closeCartButton.addEventListener('click', toggleCartSidebar);
    if (cartButtonMobile) cartButtonMobile.addEventListener('click', (e) => { e.preventDefault(); toggleCartSidebar(); });

    if (checkoutButton) {
        checkoutButton.addEventListener('click', () => {
            if (carrito.length === 0) {
                Swal.fire({ title: 'Carrito Vacío', text: 'Agrega productos al carrito antes de proceder al pago', icon: 'info' });
                return;
            }
            Swal.fire({
                title: 'Procesando Pago',
                text: `Total a pagar: S/. ${calculateTotal().toFixed(2)}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Pagar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: '¡Pago Exitoso!', text: 'Gracias por tu compra', icon: 'success' }).then(() => {
                        carrito = [];
                        saveCartToLocalStorage();
                        updateCartSidebar();
                        updateCartCount();
                        toggleCartSidebar();
                    });
                }
            });
        });
    }

    // Event delegation para agregar productos
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.add-to-cart, .add-to-cart-btn');
        if (btn) {
            const productCard = e.target.closest('.product-card');
            const idSource = productCard?.dataset.productId || btn.dataset.id;
            const productId = parseInt(idSource, 10);
            if (isNaN(productId)) {
                console.error('Producto sin id válido:', idSource);
                return;
            }
            try {
                const productos = await fetchProductos();
                const producto = productos.find(p => p.id === productId);
                if (producto) {
                    addToCart(producto);
                } else {
                    Swal.fire({ title: 'Error', text: 'Producto no encontrado', icon: 'error' });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({ title: 'Error', text: 'No se pudo agregar el producto', icon: 'error' });
            }
        }
    });

    // Event delegation para controlar cantidad y eliminar del carrito
    if (cartContent) {
        cartContent.addEventListener('click', (e) => {
            const cartItem = e.target.closest('.cart-item');
            if (!cartItem) return;

            const productId = parseInt(cartItem.dataset.productId, 10);
            const action = e.target.closest('button')?.dataset.action;

            if (action === 'increase') {
                updateQuantity(productId, carrito.find(item => item.id === productId).cantidad + 1);
            } else if (action === 'decrease') {
                updateQuantity(productId, carrito.find(item => item.id === productId).cantidad - 1);
            } else if (action === 'remove') {
                removeFromCart(productId);
            }
        });
    }

    loadCartFromLocalStorage();
    updateCartCount();
});
