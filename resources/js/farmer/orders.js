// Filter functionality
document.addEventListener('DOMContentLoaded', function () {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const orderCards = document.querySelectorAll('.order-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const filter = this.dataset.filter;

            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter cards
            orderCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});

window.viewOrderDetails = function (orderId) {
    // Implement order details view
    alert('Ver detalles del pedido #' + orderId);
}

window.updateOrderStatus = function (orderId, currentStatus) {
    // Implement status update
    Swal.fire({
        title: 'Actualizar Estado',
        input: 'select',
        inputOptions: {
            'pending': 'Pendiente',
            'processing': 'En Proceso',
            'shipped': 'Enviado',
            'delivered': 'Entregado'
        },
        inputValue: currentStatus,
        inputPlaceholder: 'Selecciona nuevo estado',
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f0abdd',
        cancelButtonColor: '#718096'
    }).then((result) => {
        if (result.isConfirmed) {
            const newStatus = result.value;

            fetch(`/agricultor/pedidos/${orderId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Actualizado!', 'El estado del pedido ha sido actualizado', 'success')
                            .then(() => {
                                location.reload();
                            });
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo actualizar el estado', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
                });
        }
    });
}
