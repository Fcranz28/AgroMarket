document.addEventListener('DOMContentLoaded', function () {
    const productContainer = document.getElementById('productDetailContainer');
    const product = productContainer ? JSON.parse(productContainer.dataset.product) : null;

    if (!product) return;
    const unitBtns = document.querySelectorAll('.product-detail-unit-btn');
    const selectedUnitInput = document.getElementById('selectedUnit');
    const selectedPriceInput = document.getElementById('selectedPrice');
    const stockDisplay = document.getElementById('stockDisplay');
    const quantityInput = document.getElementById('quantityInput');
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const priceDisplay = document.querySelector('.product-detail-price');

    // --- Gallery Logic ---
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.product-detail-thumbnail');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function () {
            const src = this.dataset.src;
            mainImage.src = src;
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // --- Unit Logic ---
    unitBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove active class from all
            unitBtns.forEach(b => b.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');

            // Update state
            const unit = this.dataset.unit;
            const price = parseFloat(this.dataset.price);
            const stock = parseInt(this.dataset.stock);

            selectedUnitInput.value = unit;
            selectedPriceInput.value = price;

            // Update UI
            updateStock(stock);
            updatePrice(price);
        });
    });

    // Initialize with active button
    const activeBtn = document.querySelector('.product-detail-unit-btn.active');
    if (activeBtn) {
        activeBtn.click();
    }

    function updateStock(stock) {
        stockDisplay.textContent = stock;
        quantityInput.max = stock;
        quantityInput.value = 1; // Reset quantity

        if (stock === 0) {
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Sin Stock';
            addToCartBtn.classList.add('btn-secondary');
            addToCartBtn.classList.remove('btn-primary');
        } else {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Agregar al Carrito';
            addToCartBtn.classList.add('btn-primary');
            addToCartBtn.classList.remove('btn-secondary');
        }
    }

    function updatePrice(price) {
        priceDisplay.textContent = 'S/. ' + price.toFixed(2);
    }

    // --- Quantity Logic ---
    decreaseBtn.addEventListener('click', () => {
        let val = parseInt(quantityInput.value);
        if (val > 1) quantityInput.value = val - 1;
    });

    increaseBtn.addEventListener('click', () => {
        let val = parseInt(quantityInput.value);
        let max = parseInt(quantityInput.max);
        if (val < max) quantityInput.value = val + 1;
    });

    // --- Add to Cart Logic ---
    addToCartBtn.addEventListener('click', () => {
        const selectedUnit = selectedUnitInput.value;
        const selectedPrice = parseFloat(selectedPriceInput.value);
        const quantity = parseInt(quantityInput.value);

        if (!selectedUnit) {
            alert('Por favor seleccione una unidad');
            return;
        }

        const productToAdd = {
            ...product,
            unit: selectedUnit,
            price: selectedPrice, // Use the selected unit price
            cantidad: quantity
        };

        const event = new CustomEvent('add-to-cart-detail', {
            detail: { product: productToAdd, quantity: quantity }
        });
        document.dispatchEvent(event);
    });
});
