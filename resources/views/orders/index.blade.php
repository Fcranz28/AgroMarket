@extends('layouts.app')

@section('content')
<div class="user-orders-container">
    <!-- Sidebar Navigation -->
    <aside class="account-sidebar">
        <div class="user-profile-card">
            <div class="user-avatar">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                @else
                    <i class="fas fa-user-circle"></i>
                @endif
            </div>
            <h3>{{ $user->name }}</h3>
            <p class="user-email">{{ $user->email }}</p>
        </div>

        <nav class="account-nav">
            <button class="nav-item active" data-section="orders">
                <i class="fas fa-box"></i>
                <span>Mis Pedidos</span>
                <span class="badge">{{ $orders->count() }}</span>
            </button>
            <button class="nav-item" data-section="profile">
                <i class="fas fa-user-edit"></i>
                <span>Mi Perfil</span>
            </button>
            <button class="nav-item" data-section="addresses">
                <i class="fas fa-map-marker-alt"></i>
                <span>Direcciones</span>
            </button>
            <button class="nav-item" data-section="security">
                <i class="fas fa-shield-alt"></i>
                <span>Seguridad</span>
            </button>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="account-content">
        <!-- Orders Section -->
        <section class="content-section active" id="orders-section">
            <div class="section-header">
                <h1>Mis Pedidos</h1>
                <p>Historial completo de tus pedidos</p>
            </div>

            @if($orders->count() > 0)
                <div class="orders-grid">
                    @foreach($orders as $order)
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <span class="order-id">ID de Seguimiento: #{{ $order->id }}</span>
                                    <span class="order-date">{{ $order->created_at->format('d M Y') }}</span>
                                </div>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="order-items">
                                @foreach($order->items->take(3) as $item)
                                    <div class="order-item">
                                        <img src="{{ $item->product->image_path ? Storage::url($item->product->image_path) : asset('img/placeholder.png') }}" 
                                             alt="{{ $item->product->name }}">
                                        <div class="item-details">
                                            <h4>{{ $item->product->name }}</h4>
                                            <p>Cantidad: {{ $item->quantity }}</p>
                                        </div>
                                        <span class="item-price">S/. {{ number_format($item->price, 2) }}</span>
                                    </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <p class="more-items">+ {{ $order->items->count() - 3 }} producto(s) más</p>
                                @endif
                            </div>

                            <div class="order-footer">
                                <div class="order-total">
                                    <span>Total:</span>
                                    <strong>S/. {{ number_format($order->total, 2) }}</strong>
                                </div>
                                <div class="order-actions">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline">
                                        Ver Detalles
                                    </a>
                                    
                                    @if($order->invoice)
                                        <a href="{{ route('invoice.download', $order->invoice->id) }}" class="btn btn-outline">
                                            <i class="fas fa-file-invoice"></i> Descargar Factura
                                        </a>
                                    @endif
                                    
                                    <button class="btn btn-outline" onclick="openTrackingModal('{{ $order->id }}', '{{ $order->status }}')">
                                        <i class="fas fa-map-marker-alt"></i> Rastrear Pedido
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No tienes pedidos aún</h3>
                    <p>Explora nuestros productos y realiza tu primera compra</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        Ir a Productos
                    </a>
                </div>
            @endif
        </section>

        <!-- Profile Section -->
        <section class="content-section" id="profile-section">
            <div class="section-header">
                <h1>Mi Perfil</h1>
                <p>Gestiona tu información personal</p>
            </div>

            <form class="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Avatar Upload -->
                <div class="avatar-upload-section">
                    <div class="current-avatar">
                        @if($user->avatar)
                            @if(filter_var($user->avatar, FILTER_VALIDATE_URL))
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                            @else
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                            @endif
                        @else
                            <i class="fas fa-user-circle"></i>
                        @endif
                    </div>
                    <div>
                        <label for="avatar" class="btn btn-outline btn-upload">
                            <i class="fas fa-camera"></i>
                            Cambiar Foto
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                        <p class="help-text">JPG, PNG o GIF. Máximo 2MB</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nombre Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="dni">DNI</label>
                        <input type="text" id="dni" name="dni" value="{{ old('dni', $user->dni ?? '') }}" maxlength="8">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>
            </form>
        </section>

        <!-- Addresses Section -->
        <section class="content-section" id="addresses-section">
            <div class="section-header">
                <h1>Mis Direcciones</h1>
                <p>Administra tus direcciones de envío</p>
            </div>

            <div class="addresses-grid">
                @if($user->shipping_address)
                    <div class="address-card">
                        <div class="address-header">
                            <h3>Dirección Principal</h3>
                            <span class="default-badge">Principal</span>
                        </div>
                        <p class="address-text">{{ $user->shipping_address }}</p>
                        @if($user->phone)
                            <p class="address-phone">
                                <i class="fas fa-phone"></i>
                                {{ $user->phone }}
                            </p>
                        @endif
                        <div class="address-actions">
                            <button class="btn-link" onclick="editAddress()">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                @endif

                <button class="add-address-card" onclick="addAddress()">
                    <i class="fas fa-plus-circle"></i>
                    <span>Agregar Nueva Dirección</span>
                </button>
            </div>
        </section>

        <!-- Security Section -->
        <section class="content-section" id="security-section">
            <div class="section-header">
                <h1>Seguridad</h1>
                <p>Actualiza tu contraseña</p>
            </div>

            <form class="security-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for rel="current_password">Contraseña Actual</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" id="new_password" name="password" required minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="password_confirmation" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-lock"></i>
                    Actualizar Contraseña
                </button>
            </form>
        </section>
    </main>
</div>

<!-- Tracking Modal -->
<div id="trackingModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeTrackingModal()">&times;</span>
        <h2>Rastreo de Pedido</h2>
        <div class="stepper-wrapper">
            <div class="stepper-item" id="step-pending">
                <div class="step-counter">1</div>
                <div class="step-name">Pendiente</div>
            </div>
            <div class="stepper-item" id="step-processing">
                <div class="step-counter">2</div>
                <div class="step-name">En Proceso</div>
            </div>
            <div class="stepper-item" id="step-shipped">
                <div class="step-counter">3</div>
                <div class="step-name">Enviado</div>
            </div>
            <div class="stepper-item" id="step-delivered">
                <div class="step-counter">4</div>
                <div class="step-name">Entregado</div>
            </div>
        </div>
    </div>
</div>

@push('styles')
{{-- Styles loaded via app.css → orders/index.css --}}
@endpush

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const navItems = document.querySelectorAll('.nav-item');
        const sections = document.querySelectorAll('.content-section');

        navItems.forEach(item => {
            item.addEventListener('click', () => {
                const sectionId = item.dataset.section;
                
                // Update active nav item
                navItems.forEach(nav => nav.classList.remove('active'));
                item.classList.add('active');

                // Show corresponding section
                sections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === sectionId + '-section') {
                        section.classList.add('active');
                    }
                });
            });
        });
    });

    function editAddress() {
        alert('Función de editar dirección - Por implementar');
    }

    function addAddress() {
        alert('Función de agregar dirección - Por implementar');
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarContainer = document.querySelector('.current-avatar');
                avatarContainer.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openTrackingModal(orderId, status) {
        const modal = document.getElementById('trackingModal');
        modal.style.display = 'flex';
        
        // Reset steps
        const steps = ['pending', 'processing', 'shipped', 'delivered'];
        steps.forEach(step => {
            const el = document.getElementById('step-' + step);
            if(el) el.classList.remove('active', 'completed');
        });

        // Activate steps based on status
        let active = true;
        steps.forEach(step => {
            const el = document.getElementById('step-' + step);
            if (el && active) {
                if (step === status) {
                    el.classList.add('active');
                    active = false; 
                } else {
                    el.classList.add('completed');
                }
            }
        });
    }

    function closeTrackingModal() {
        document.getElementById('trackingModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('trackingModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection