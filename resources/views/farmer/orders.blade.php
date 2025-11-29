@extends('layouts.dashboard')

@section('title', 'Mis Pedidos')
@section('header', 'Gestión de Pedidos')

@section('content')
<div class="orders-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $pendingOrders->count() }}</h3>
                <p>Pedidos Pendientes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $orders->count() }}</h3>
                <p>Total de Pedidos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon revenue">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <h3>S/. {{ number_format($orders->sum('total'), 2) }}</h3>
                <p>Ventas Totales</p>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="orders-section">
        <div class="section-header">
            <h2>Pedidos Recientes</h2>
            <div class="filters">
                <button class="filter-btn active" data-filter="all">Todos</button>
                <button class="filter-btn" data-filter="pending">Pendientes</button>
                <button class="filter-btn" data-filter="processing">En Proceso</button>
                <button class="filter-btn" data-filter="shipped">Enviados</button>
            </div>
        </div>

        @if($orders->count() > 0)
            <div class="orders-grid">
                @foreach($orders as $order)
                    <div class="order-card" data-status="{{ $order->status }}">
                        <div class="order-header">
                            <div class="order-info">
                                <h3>Pedido #{{ $order->id }}</h3>
                                <span class="order-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <span class="status-badge status-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending')
                                        Pendiente
                                        @break
                                    @case('processing')
                                        En Proceso
                                        @break
                                    @case('shipped')
                                        Enviado
                                        @break
                                    @case('delivered')
                                        Entregado
                                        @break
                                    @default
                                        {{ $order->status }}
                                @endswitch
                            </span>
                        </div>

                        <div class="customer-info">
                            <div class="customer-avatar">
                                {{ substr($order->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="customer-name">{{ $order->user->name }}</p>
                                <p class="customer-email">{{ $order->user->email }}</p>
                            </div>
                        </div>

                        <div class="order-items">
                            <h4>Productos de tu tienda:</h4>
                            @foreach($order->items as $item)
                                @if($item->product->user_id == auth()->id())
                                    <div class="order-item">
                                        <img src="{{ $item->product->image_path ? Storage::url($item->product->image_path) : asset('img/placeholder.png') }}" 
                                             alt="{{ $item->product->name }}">
                                        <div class="item-details">
                                            <p class="item-name">{{ $item->product->name }}</p>
                                            <p class="item-quantity">Cantidad: {{ $item->quantity }} x S/. {{ number_format($item->price, 2) }}</p>
                                        </div>
                                        <div class="item-total">
                                            S/. {{ number_format($item->price * $item->quantity, 2) }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="order-footer">
                            <div class="order-total">
                                <span>Total del pedido:</span>
                                <strong>S/. {{ number_format($order->total, 2) }}</strong>
                            </div>
                            <div class="order-actions">
                                <button class="btn btn-outline" onclick="viewOrderDetails({{ $order->id }})">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </button>
                                @if(in_array($order->status, ['pending', 'processing']))
                                    <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }})">
                                        <i class="fas fa-truck"></i> Actualizar Estado
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>No tienes pedidos aún</h3>
                <p>Cuando los clientes compren tus productos, aparecerán aquí</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .orders-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-speed) ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.pending {
        background: linear-gradient(135deg, var(--warning), #ffb84d);
    }

    .stat-icon.total {
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
    }

    .stat-icon.revenue {
        background: linear-gradient(135deg, var(--success), #8fccb3);
    }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .stat-content p {
        color: var(--text-secondary);
        margin: 0;
        font-size: 0.9rem;
    }

    /* Orders Section */
    .orders-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-sm);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .section-header h2 {
        color: var(--text-primary);
        margin: 0;
        font-size: 1.5rem;
    }

    .filters {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.625rem 1.25rem;
        border: 2px solid var(--border-color);
        background: var(--bg-input);
        color: var(--text-secondary);
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all var(--transition-speed) ease;
    }

    .filter-btn:hover {
        border-color: var(--accent-primary);
        color: var(--text-primary);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        color: white;
        border-color: transparent;
    }

    /* Orders Grid */
    .orders-grid {
        display: grid;
        gap: 1.5rem;
    }

    .order-card {
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all var(--transition-speed) ease;
        background: var(--bg-secondary);
    }

    .order-card:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--accent-primary);
        transform: translateX(4px);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 2px solid var(--border-light);
    }

    .order-info h3 {
        margin: 0 0 0.5rem 0;
        color: var(--text-primary);
        font-size: 1.1rem;
    }

    .order-date {
        color: var(--text-muted);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pending {
        background: linear-gradient(135deg, rgba(255, 213, 165, 0.3), rgba(255, 213, 165, 0.1));
        color: var(--warning);
        border: 2px solid var(--warning);
    }

    .status-processing {
        background: linear-gradient(135deg, rgba(184, 224, 210, 0.3), rgba(184, 224, 210, 0.1));
        color: var(--accent-secondary);
        border: 2px solid var(--accent-secondary);
    }

    .status-shipped {
        background: linear-gradient(135deg, rgba(184, 224, 210, 0.3), rgba(184, 224, 210, 0.1));
        color: var(--success);
        border: 2px solid var(--success);
    }

    .status-delivered {
        background: linear-gradient(135deg, rgba(184, 224, 210, 0.3), rgba(184, 224, 210, 0.1));
        color: var(--success);
        border: 2px solid var(--success);
    }

    /* Customer Info */
    .customer-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding: 1rem;
        background: var(--bg-hover);
        border-radius: 12px;
    }

    .customer-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .customer-name {
        margin: 0;
        font-weight: 600;
        color: var(--text-primary);
    }

    .customer-email {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    /* Order Items */
    .order-items {
        margin-bottom: 1.25rem;
    }

    .order-items h4 {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0 0 1rem 0;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: var(--bg-input);
        border-radius: 12px;
        margin-bottom: 0.75rem;
    }

    .order-item img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid var(--border-color);
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        color: var(--text-primary);
    }

    .item-quantity {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .item-total {
        font-weight: 700;
        color: var(--accent-primary);
        font-size: 1.1rem;
    }

    /* Order Footer */
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.25rem;
        border-top: 2px solid var(--border-light);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .order-total span {
        color: var(--text-secondary);
        margin-right: 0.5rem;
    }

    .order-total strong {
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .order-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-speed) ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        font-size: 0.9rem;
    }

    .btn-outline {
        border: 2px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
    }

    .btn-outline:hover {
        border-color: var(--accent-primary);
        background: var(--bg-hover);
        color: var(--text-primary);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 5rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .empty-state h3 {
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        color: var(--text-secondary);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .order-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .orders-section {
            padding: 1rem;
        }

        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .status-badge {
            width: 100%;
            text-align: center;
        }

        .customer-info {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .order-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .item-total {
            margin-top: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const orderCards = document.querySelectorAll('.order-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
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

    function viewOrderDetails(orderId) {
        // Implement order details view
        alert('Ver detalles del pedido #' + orderId);
    }

    function updateOrderStatus(orderId) {
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
                });
            }
        });
    }
</script>
@endpush
@endsection
