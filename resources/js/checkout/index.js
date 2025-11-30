// Display cart summary with tax calculation
function displayCartSummary() {
    const cart = JSON.parse(localStorage.getItem('carrito') || '[]');
    const cartItems = document.getElementById('cart-items');
    const subtotalElement = document.getElementById('subtotal-amount');
    const taxElement = document.getElementById('tax-amount');
    const totalElement = document.getElementById('total-amount');

    if (cart.length === 0) {
        window.location.href = '/productos';
        return;
    }

    let subtotal = 0;
    cartItems.innerHTML = '';

    cart.forEach(item => {
        const itemPrice = parseFloat(item.price);
        const itemTotal = itemPrice * item.cantidad;
        subtotal += itemTotal;

        // Get image path
        let image = '/img/placeholder.png';
        if (item.image_path) {
            image = `/storage/${item.image_path}`;
        } else if (item.image_url) {
            image = item.image_url;
        } else if (item.image) {
            image = item.image;
        }

        const itemDiv = document.createElement('div');
        itemDiv.className = 'checkout-cart-item';
        itemDiv.innerHTML = `
                <img src="${image}" alt="${item.name}">
                <div class="checkout-cart-item-details">
                    <div class="checkout-cart-item-name">${item.name}</div>
                    <div class="checkout-cart-item-quantity">Cant: ${item.cantidad}</div>
                </div>
                <div class="checkout-cart-item-price">S/. ${itemTotal.toFixed(2)}</div>
            `;
        cartItems.appendChild(itemDiv);
    });

    // Calculate tax (18% IGV)
    const tax = subtotal * 0.18;
    const total = subtotal + tax;

    subtotalElement.textContent = `S/. ${subtotal.toFixed(2)}`;
    taxElement.textContent = `S/. ${tax.toFixed(2)}`;
    totalElement.textContent = `S/. ${total.toFixed(2)}`;
}

// Payment tab switching
function initPaymentTabs() {
    const tabs = document.querySelectorAll('.checkout-payment-tab');
    const methods = document.querySelectorAll('.checkout-payment-method');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const method = tab.getAttribute('data-method');

            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Update active method
            methods.forEach(m => m.classList.remove('active'));
            document.querySelector(`.checkout-payment-method[data-method="${method}"]`).classList.add('active');
        });
    });
}

// Run on DOM ready or immediately if already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        displayCartSummary();
        initPaymentTabs();
    });
} else {
    displayCartSummary();
    initPaymentTabs();
}
