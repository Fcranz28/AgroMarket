document.addEventListener('DOMContentLoaded', function() {
    // Manejo de la navegación en la barra lateral
    const navButtons = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('section');

    navButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remover clase activa de todos los botones y secciones
            navButtons.forEach(btn => btn.classList.remove('active'));
            sections.forEach(section => section.classList.remove('active'));

            // Agregar clase activa al botón clickeado y su sección correspondiente
            button.classList.add('active');
            const sectionId = button.getAttribute('data-section');
            document.getElementById(sectionId).classList.add('active');
        });
    });

    // Manejo de los filtros de pedidos
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            // Aquí se puede agregar la lógica para filtrar los pedidos
        });
    });

    // Función para simular la carga de pedidos (para demostración)
    function loadOrders() {
        // Esta función se puede modificar para cargar pedidos desde una base de datos
        const ordersList = document.querySelector('.orders-list');
        
        // Ejemplo de datos de pedidos (se puede reemplazar con datos reales)
        const orders = [
            {
                date: '01 Nov 2025',
                number: 'AGM123456',
                status: 'pending',
                statusText: 'Por pagar',
                product: {
                    name: 'Papa Amarilla',
                    quantity: 2,
                    price: 45.90,
                    image: 'img/producto1.jpg'
                }
            },
            {
                date: '31 Oct 2025',
                number: 'AGM123455',
                status: 'processing',
                statusText: 'En proceso',
                product: {
                    name: 'Tomate Italiano',
                    quantity: 3,
                    price: 12.50,
                    image: 'img/producto1.jpg'
                }
            }
        ];

        // Limpiar lista actual
        ordersList.innerHTML = '';

        // Agregar cada pedido a la lista
        orders.forEach(order => {
            const total = order.product.price * order.product.quantity;
            
            const orderHtml = `
                <li class="order-card">
                    <div class="order-header">
                        <div class="order-date">
                            <span class="label">Fecha:</span>
                            <span>${order.date}</span>
                        </div>
                        <div class="order-number">
                            <span class="label">Pedido:</span>
                            <span>#${order.number}</span>
                        </div>
                        <div class="order-status">
                            <span class="status-badge ${order.status}">${order.statusText}</span>
                        </div>
                    </div>
                    <div class="order-products">
                        <img src="${order.product.image}" alt="${order.product.name}">
                        <div class="product-details">
                            <h4>${order.product.name}</h4>
                            <p>Cantidad: ${order.product.quantity}</p>
                            <p class="price">S/. ${order.product.price.toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="order-total">
                            <span class="label">Total:</span>
                            <span class="total-amount">S/. ${total.toFixed(2)}</span>
                        </div>
                        <div class="order-actions">
                            <button class="action-btn">Ver Detalles</button>
                            <button class="action-btn primary">Pagar Ahora</button>
                        </div>
                    </div>
                </li>
            `;

            ordersList.insertAdjacentHTML('beforeend', orderHtml);
        });
    }

    // Cargar pedidos al iniciar
    loadOrders();

    // Manejo del formulario de perfil
    const profileForm = document.querySelector('.profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Aquí se puede agregar la lógica para guardar los cambios del perfil
            alert('Cambios guardados correctamente');
        });
    }

    // Manejo de los botones de dirección
    const addAddressBtn = document.querySelector('.add-address-btn');
    if (addAddressBtn) {
        addAddressBtn.addEventListener('click', () => {
            // Aquí se puede agregar la lógica para agregar una nueva dirección
            alert('Formulario para agregar nueva dirección');
        });
    }

    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Aquí se puede agregar la lógica para editar una dirección
            alert('Editar dirección');
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Aquí se puede agregar la lógica para eliminar una dirección
            if (confirm('¿Estás seguro de que deseas eliminar esta dirección?')) {
                // Lógica para eliminar la dirección
            }
        });
    });
});