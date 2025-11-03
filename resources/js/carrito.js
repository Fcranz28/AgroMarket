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
    if (cartCount) {
        const totalItems = carrito.reduce((total, item) => total + item.cantidad, 0);
        cartCount.textContent = totalItems;
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
        const image = item.image_path ? `/storage/${item.image_path}` : '/img/placeholder.png';
        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.innerHTML = `
            <img src="${image}" alt="${item.name}">
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <p>S/. ${Number(item.price || 0).toFixed(2)} /${item.unit || ''}</p>
                <div class="quantity-controls">
                    <button onclick="updateQuantity(${item.id}, ${item.cantidad - 1})">-</button>
                    <span>${item.cantidad}</span>
                    <button onclick="updateQuantity(${item.id}, ${item.cantidad + 1})">+</button>
                </div>
            </div>
            <button class="remove-item" onclick="removeFromCart(${item.id})">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        `;
        cartContent.appendChild(itemElement);
    });
    if (totalElement) totalElement.textContent = calculateTotal().toFixed(2);
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

// Inicializar el carrito
document.addEventListener('DOMContentLoaded', () => {
    const cartButton = document.querySelector('#openCart');
    const closeCartButton = document.querySelector('#closeCart');
    const checkoutButton = document.querySelector('#checkoutButton');
    const cartButtonMobile = document.querySelector('.cart-button-mobile');

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

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.add-to-cart');
        if (btn) {
            const productCard = e.target.closest('.product-card');
            const productId = parseInt(productCard?.dataset.productId || btn.dataset.id, 10);
            try {
                const productos = await fetchProductos();
                const producto = productos.find(p => p.id === productId);
                if (producto) {
                    addToCart(producto);
                }
            } catch (err) {
                console.error(err);
                Swal.fire({ title: 'Error', text: 'No se pudo agregar el producto', icon: 'error' });
            }
        }
    });

    loadCartFromLocalStorage();
    updateCartCount();
});
