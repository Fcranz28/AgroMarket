@extends('layouts.app')

@section('content')
<div class="account-container">
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

<style>
    .account-container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 30px;
    }

    .account-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .user-profile-card {
        background: var(--card-background-color, #fff);
        padding: 30px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        text-align: center;
        margin-bottom: 20px;
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color, #4caf50), #66bb6a);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-profile-card h3 {
        margin: 0 0 5px;
        color: var(--text-color, #333);
        font-size: 1.1rem;
    }

    .user-email {
        color: var(--text-color-light, #666);
        font-size: 0.9rem;
        margin: 0;
    }

    .account-nav {
        background: var(--card-background-color, #fff);
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .nav-item {
        width: 100%;
        padding: 12px 15px;
        border: none;
        background: none;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s;
        color: var(--text-color, #333);
        font-size: 0.95rem;
        margin-bottom: 5px;
    }

    .nav-item i {
        font-size: 1.2rem;
        width: 24px;
    }

    .nav-item span:not(.badge) {
        flex: 1;
        text-align: left;
    }

    .nav-item .badge {
        background: var(--primary-color, #4caf50);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .nav-item:hover {
        background: var(--hover-background, #f5f5f5);
    }

    .nav-item.active {
        background: var(--primary-color, #4caf50);
        color: white;
    }

    .account-content {
        background: var(--card-background-color, #fff);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .section-header {
        margin-bottom: 30px;
    }

    .section-header h1 {
        margin: 0 0 5px;
        color: var(--text-color, #333);
        font-size: 1.8rem;
    }

    .section-header p {
        margin: 0;
        color: var(--text-color-light, #666);
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .orders-grid {
        display: grid;
        gap: 20px;
    }

    .order-card {
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s;
    }

    .order-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color, #e0e0e0);
    }

    .order-id {
        font-weight: 600;
        color: var(--text-color, #333);
        margin-right: 15px;
    }

    .order-date {
        color: var(--text-color-light, #666);
        font-size: 0.9rem;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-processing {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-shipped {
        background: #d4edda;
        color: #155724;
    }

    .status-delivered {
        background: #d4edda;
        color: #155724;
    }

    .order-items {
        margin-bottom: 20px;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px 0;
    }

    .order-item img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .item-details {
        flex: 1;
    }

    .item-details h4 {
        margin: 0 0 5px;
        font-size: 0.95rem;
        color: var(--text-color, #333);
    }

    .item-details p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-color-light, #666);
    }

    .item-price {
        font-weight: 600;
        color: var(--primary-color, #4caf50);
    }

    .more-items {
        color: var(--text-color-light, #666);
        font-size: 0.9rem;
        font-style: italic;
        margin: 10px 0 0;
    }

    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid var(--border-color, #e0e0e0);
    }

    .order-total strong {
        font-size: 1.2rem;
        color: var(--primary-color, #4caf50);
    }

    .order-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-outline {
        border: 1px solid var(--border-color, #ddd);
        background: transparent;
        color: var(--text-color, #333);
    }

    .btn-outline:hover {
        background: var(--hover-background, #f5f5f5);
    }

    .btn-primary {
        background: var(--primary-color, #4caf50);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-color-dark, #45a049);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--text-color-light, #ccc);
        margin-bottom: 20px;
    }

    .empty-state h3 {
        margin: 0 0 10px;
        color: var(--text-color, #333);
    }

    .empty-state p {
        margin: 0 0 20px;
        color: var(--text-color-light, #666);
    }

    .profile-form,
    .security-form {
        max-width: 800px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-color, #333);
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--input-background-color, #fff);
        color: var(--text-color, #333);
    }

    .addresses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .address-card {
        border: 1px solid var(--border-color, #e0e0e0);
        border-radius: 10px;
        padding: 20px;
    }

    .address-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .address-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: var(--text-color, #333);
    }

    .default-badge {
        background: var(--primary-color, #4caf50);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .address-text {
        color: var(--text-color, #333);
        margin-bottom: 10px;
        line-height: 1.5;
    }

    .address-phone {
        color: var(--text-color-light, #666);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .address-actions {
        padding-top: 15px;
        border-top: 1px solid var(--border-color, #e0e0e0);
    }

    .btn-link {
        background: none;
        border: none;
        color: var(--primary-color, #4caf50);
        cursor: pointer;
        font-size: 0.9rem;
        padding: 0;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .add-address-card {
        border: 2px dashed var(--border-color, #ddd);
        border-radius: 10px;
        padding: 40px 20px;
        background: none;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: var(--text-color-light, #666);
        transition: all 0.2s;
    }

    .add-address-card:hover {
        border-color: var(--primary-color, #4caf50);
        color: var(--primary-color, #4caf50);
        background: var(--hover-background, #f5f5f5);
    }

    .add-address-card i {
        font-size: 2rem;
    }

    .avatar-upload-section {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 30px;
        padding: 20px;
        background: var(--hover-background, #f9f9f9);
        border-radius: 10px;
    }

    .current-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, var(--primary-color, #4caf50), #66bb6a);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .current-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .current-avatar i {
        font-size: 4rem;
        color: white;
    }

    .btn-upload {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .help-text {
        color: var(--text-color-light, #666);
        font-size: 0.85rem;
        margin: 10px 0 0;
    }

    @media (max-width: 768px) {
        .account-container {
            grid-template-columns: 1fr;
        }

        .account-sidebar {
            position: static;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .addresses-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

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
</script>
@endsection