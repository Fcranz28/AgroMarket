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
                                            @if($order->status === 'delivered')
                                                <button class="btn-report-item" onclick="openReportModal('{{ $order->id }}', '{{ $item->product->id }}', '{{ $item->product->name }}')">
                                                    <i class="fas fa-exclamation-triangle"></i> Reportar
                                                </button>
                                            @endif
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

            <div id="addresses-grid" class="addresses-grid">
                <!-- Addresses will be loaded here via JS -->
            </div>

            <!-- Address Modal -->
            <div id="addressModal" class="map-modal">
                <div class="map-modal-content">
                    <div class="section-header">
                        <h2>Agregar Nueva Dirección</h2>
                        <button class="btn-link" onclick="closeAddressModal()" style="font-size: 1.5rem;">&times;</button>
                    </div>
                    
                    <div class="map-search-box">
                        <input type="text" id="map-search-input" placeholder="Buscar dirección...">
                    </div>
                    
                    <div id="google-map" class="map-container"></div>
                    
                    <div class="map-actions">
                        <button type="button" class="btn-cancel" onclick="closeAddressModal()">Cancelar</button>
                        <button type="button" class="btn-confirm" onclick="saveAddress()">Guardar Dirección</button>
                    </div>
                </div>
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

<!-- Report Modal -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeReportModal()">&times;</span>
        <h2>Reportar Producto</h2>
        <form id="reportForm" onsubmit="submitReport(event)">
            @csrf
            <input type="hidden" id="report_order_id" name="order_id">
            <input type="hidden" id="report_product_id" name="product_id">
            
            <div class="form-group">
                <label for="report_product_name">Producto</label>
                <input type="text" id="report_product_name" name="product_name" readonly class="form-control-plaintext">
            </div>

            <div class="form-group">
                <label for="report_reason">Motivo del Reporte</label>
                <select id="report_reason" name="reason" required>
                    <option value="">Seleccione un motivo</option>
                    <option value="damaged">Producto dañado</option>
                    <option value="wrong_item">Producto incorrecto</option>
                    <option value="quality">Mala calidad</option>
                    <option value="expired">Producto vencido</option>
                    <option value="other">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="report_description">Descripción</label>
                <textarea id="report_description" name="description" rows="4" placeholder="Describa el problema con detalle..." required></textarea>
            </div>

            <div class="form-group">
                <label for="report_evidence">Evidencias (Fotos)</label>
                <div class="evidence-upload-container" onclick="document.getElementById('report_evidence').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Haz clic para subir fotos</p>
                    <span class="upload-hint">Máximo 3 imágenes (JPG, PNG)</span>
                </div>
                <input type="file" id="report_evidence" name="evidence[]" accept="image/*" multiple style="display: none;" onchange="previewEvidence(this)">
                <div id="evidence_preview" class="evidence-preview-grid"></div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeReportModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar Reporte</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
    @vite(['resources/css/profile/addresses.css'])
@endpush

@push('scripts')
    <script>
        window.initMap = function() {
            console.log('Maps API loaded, waiting for module...');
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/profile/addresses.js'])
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
        const trackingModal = document.getElementById('trackingModal');
        const reportModal = document.getElementById('reportModal');
        if (event.target == trackingModal) {
            trackingModal.style.display = 'none';
        }
        if (event.target == reportModal) {
            reportModal.style.display = 'none';
        }
    }

    // Store order items data for report modal
    const orderItems = {
        @foreach($orders as $order)
            '{{ $order->id }}': [
                @foreach($order->items as $item)
                    { id: '{{ $item->product->id }}', name: '{{ $item->product->name }}' },
                @endforeach
            ],
        @endforeach
    };

    function openReportModal(orderId, productId, productName) {
        const modal = document.getElementById('reportModal');
        document.getElementById('report_order_id').value = orderId;
        document.getElementById('report_product_id').value = productId;
        document.getElementById('report_product_name').value = productName;
        
        modal.style.display = 'flex';
    }

    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
        document.getElementById('reportForm').reset();
        document.getElementById('evidence_preview').innerHTML = '';
    }

    function previewEvidence(input) {
        const previewContainer = document.getElementById('evidence_preview');
        previewContainer.innerHTML = '';
        
        if (input.files) {
            if (input.files.length > 3) {
                Swal.fire({
                    icon: 'error',
                    title: 'Límite excedido',
                    text: 'Solo puedes subir un máximo de 3 imágenes.',
                    confirmButtonColor: '#4caf50'
                });
                input.value = '';
                return;
            }

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'evidence-item';
                    div.innerHTML = `<img src="${e.target.result}" alt="Evidence">`;
                    previewContainer.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    function submitReport(event) {
        event.preventDefault();
        
        const form = document.getElementById('reportForm');
        const formData = new FormData(form);
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = 'Enviando...';

        fetch('{{ route("reports.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Reporte Enviado',
                text: 'Gracias por tu reporte. Lo revisaremos a la brevedad.',
                confirmButtonColor: '#4caf50'
            }).then(() => {
                closeReportModal();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al enviar el reporte. Por favor intenta nuevamente.',
                confirmButtonColor: '#dc3545'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        });
    }
</script>
@endsection