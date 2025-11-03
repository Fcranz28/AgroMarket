@extends('layouts.app')

@section('content')
    <div class="orders-container">
        <!-- Barra lateral de navegación -->
        <aside class="orders-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h3>¡Bienvenido!</h3>
                    <p>Usuario</p>
                </div>
            </div>

            <nav class="orders-nav">
                <button class="nav-btn active" data-section="orders">
                    <i class="fas fa-box"></i>
                    Tus Pedidos
                </button>
                <button class="nav-btn" data-section="profile">
                    <i class="fas fa-user-circle"></i>
                    Tu Perfil
                </button>
                <button class="nav-btn" data-section="addresses">
                    <i class="fas fa-map-marker-alt"></i>
                    Direcciones
                </button>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="orders-content">
            <!-- Sección de Pedidos -->
            <section class="orders-section active" id="orders">
                <h2>Tus Pedidos</h2>
                
                <div class="orders-filters">
                    <button class="filter-btn active">Todos</button>
                    <button class="filter-btn">Por pagar</button>
                    <button class="filter-btn">En proceso</button>
                    <button class="filter-btn">Enviados</button>
                    <button class="filter-btn">Entregados</button>
                </div>

                <ul class="orders-list">
                    <!-- Ejemplo de pedido -->
                    <li class="order-card">
                        <div class="order-header">
                            <div class="order-date">
                                <span class="label">Fecha:</span>
                                <span>01 Nov 2025</span>
                            </div>
                            <div class="order-number">
                                <span class="label">Pedido:</span>
                                <span>#AGM123456</span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge pending">Por pagar</span>
                            </div>
                        </div>
                        <div class="order-products">
                            <img src="img/producto1.jpg" alt="Producto">
                            <div class="product-details">
                                <h4>Nombre del Producto</h4>
                                <p>Cantidad: 2</p>
                                <p class="price">S/. 45.90</p>
                            </div>
                        </div>
                        <div class="order-footer">
                            <div class="order-total">
                                <span class="label">Total:</span>
                                <span class="total-amount">S/. 91.80</span>
                            </div>
                            <div class="order-actions">
                                <button class="action-btn">Ver Detalles</button>
                                <button class="action-btn primary">Pagar Ahora</button>
                            </div>
                        </div>
                    </li>

                    <!-- Más pedidos se agregarán dinámicamente -->
                </ul>
            </section>

            <!-- Sección de Perfil -->
            <section class="profile-section" id="profile">
                <h2>Tu Perfil</h2>
                <div class="profile-info">
                    <form class="profile-form">
                        <div class="form-group">
                            <label>Nombre completo</label>
                            <input type="text" value="Nombre del Usuario">
                        </div>
                        <div class="form-group">
                            <label>Correo electrónico</label>
                            <input type="email" value="usuario@email.com">
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" value="987654321">
                        </div>
                        <button type="submit" class="save-btn">Guardar Cambios</button>
                    </form>
                </div>
            </section>

            <!-- Sección de Direcciones -->
            <section class="addresses-section" id="addresses">
                <h2>Tus Direcciones</h2>
                <div class="addresses-list">
                    <div class="address-card">
                        <div class="address-header">
                            <h4>Casa</h4>
                            <span class="default-badge">Principal</span>
                        </div>
                        <p>Calle Principal 123</p>
                        <p>Lima, Perú</p>
                        <p>Teléfono: 987654321</p>
                        <div class="address-actions">
                            <button class="edit-btn">Editar</button>
                            <button class="delete-btn">Eliminar</button>
                        </div>
                    </div>
                    <button class="add-address-btn">
                        <i class="fas fa-plus"></i>
                        Agregar Nueva Dirección
                    </button>
                </div>
            </section>
        </main>
    </div>
@endsection