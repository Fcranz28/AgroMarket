@extends('layouts.dashboard')

@section('title', 'Verificación de Agricultor')

@section('content')
<div class="container-fluid verify-farmer-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Verificación de Agricultor</h2>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Farmer Information & Evidence -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">Información del Agricultor</h5>
                </div>
                <div class="card-body">
                    <div class="user-info-section">
                        <div class="avatar-circle">
                            <span class="initials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-0 fw-bold">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            <span class="badge bg-warning text-dark mt-2">Verificación Pendiente</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold text-muted mb-2">Teléfono</label>
                        <p class="fs-5">{{ $user->phone ?? 'No registrado' }}</p>
                    </div>

                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Evidencias Presentadas</h6>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted mb-2">Foto de Rostro (Selfie)</label>
                        <div class="evidence-img-container">
                            @if($user->face_photo)
                                <img src="{{ asset('storage/' . $user->face_photo) }}" class="img-fluid rounded evidence-img" alt="Selfie">
                            @else
                                <div class="alert alert-secondary">No hay foto disponible</div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2">DNI (Frontal)</label>
                            <div class="evidence-img-container">
                                @if($user->dni_front)
                                    <img src="{{ asset('storage/' . $user->dni_front) }}" class="img-fluid rounded evidence-img" alt="DNI Frontal">
                                @else
                                    <div class="alert alert-secondary">No hay foto disponible</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted mb-2">DNI (Reverso)</label>
                            <div class="evidence-img-container">
                                @if($user->dni_back)
                                    <img src="{{ asset('storage/' . $user->dni_back) }}" class="img-fluid rounded evidence-img" alt="DNI Reverso">
                                @else
                                    <div class="alert alert-secondary">No hay foto disponible</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: RENIEC Verification -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">Validación con RENIEC</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Ingresa el DNI para validar los datos con el servicio de RENIEC.
                    </div>

                    <form id="dniForm" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <div class="mb-3">
                            <label for="dni_input" class="form-label fw-bold text-muted small text-uppercase">Número de DNI</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0" id="dni_input" name="dni" value="{{ old('dni', $user->dni) }}" placeholder="Ingrese los 8 dígitos" required maxlength="8" pattern="[0-9]{8}">
                                <button class="btn btn-primary px-4" type="submit" id="consultarBtn">
                                    <i class="fas fa-search me-2"></i>Consultar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="apiResultContainer">
                        @if(session('apiResult'))
                            @if(session('apiResult')['success'])
                                <div class="api-result-card mb-4">
                                    <div class="result-header">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span class="fw-bold text-success">Datos Encontrados</span>
                                    </div>
                                    <div class="result-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="result-label">Nombre Completo</label>
                                                <div class="result-value">{{ session('apiResult')['data']['first_name'] }} {{ session('apiResult')['data']['first_last_name'] }} {{ session('apiResult')['data']['second_last_name'] }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="result-label">DNI</label>
                                                <div class="result-value">{{ session('apiResult')['data']['document_number'] }}</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="result-label">Estado</label>
                                                <div class="result-value text-success">
                                                    <i class="fas fa-check-circle me-1 small"></i> Validado
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                    <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                                    <div>
                                        <strong>Error en la consulta:</strong> {{ session('apiResult')['message'] }}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <hr class="my-4 border-light">

                    <h5 class="fw-bold mb-3 text-primary">Acciones de Verificación</h5>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <form action="{{ route('admin.verify', ['user' => $user->id, 'status' => 'approved']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm">
                                    <i class="fas fa-check-circle me-2"></i>Aprobar
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.verify', ['user' => $user->id, 'status' => 'rejected']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 py-3 fw-bold">
                                    <i class="fas fa-times-circle me-2"></i>Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles loaded via dashboard.css --}}
@endsection

@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/admin/verify.js'])
@endpush
