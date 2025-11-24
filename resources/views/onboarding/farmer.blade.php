@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="progress mb-5" style="height: 10px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Verificación de Identidad</h2>
                        <p class="text-muted">Para garantizar la seguridad, necesitamos validar tu identidad.</p>
                    </div>

                    <form action="{{ route('onboarding.verification') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">1. Foto de tu Rostro (Selfie)</label>
                            <div class="upload-area" onclick="document.getElementById('face_photo').click()">
                                <i class="fas fa-camera fa-2x mb-2 text-muted"></i>
                                <p class="mb-0">Haz clic para subir o tomar foto</p>
                                <input type="file" name="face_photo" id="face_photo" class="d-none" accept="image/*" required onchange="previewFile(this)">
                                <div class="preview-box mt-2"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">2. DNI (Frente)</label>
                                <div class="upload-area" onclick="document.getElementById('dni_front').click()">
                                    <i class="fas fa-id-card fa-2x mb-2 text-muted"></i>
                                    <p class="mb-0">Subir Frente</p>
                                    <input type="file" name="dni_front" id="dni_front" class="d-none" accept="image/*" required onchange="previewFile(this)">
                                    <div class="preview-box mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">3. DNI (Dorso)</label>
                                <div class="upload-area" onclick="document.getElementById('dni_back').click()">
                                    <i class="fas fa-id-card fa-2x mb-2 text-muted"></i>
                                    <p class="mb-0">Subir Dorso</p>
                                    <input type="file" name="dni_back" id="dni_back" class="d-none" accept="image/*" required onchange="previewFile(this)">
                                    <div class="preview-box mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-shield-alt fa-2x me-3"></i>
                            <div>
                                <strong>Tus datos están protegidos.</strong><br>
                                Solo usamos esta información para verificar que eres un agricultor real.
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg">Enviar Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #f8f9fa;
    }
    .upload-area:hover {
        border-color: var(--primary-color);
        background: #fff;
    }
    .preview-box img {
        max-width: 100%;
        max-height: 150px;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>

<script>
    function previewFile(input) {
        const previewBox = input.nextElementSibling;
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewBox.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
