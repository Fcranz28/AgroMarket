@extends('layouts.app')

@section('content')
<div class="verification-container">
    <div class="verification-card">
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 50%;"></div>
            </div>
            <span class="progress-text">Paso 2 de 3</span>
        </div>

        <div class="header-section">
            <h2>Verificación de Identidad</h2>
            <p>Para garantizar la seguridad de nuestra comunidad, necesitamos validar que eres un agricultor real.</p>
        </div>

        <form action="{{ route('onboarding.verification') }}" method="POST" enctype="multipart/form-data" class="verification-form">
            @csrf
            
            <div class="form-group">
                <label class="section-label">1. Foto de tu Rostro (Selfie)</label>
                <div class="upload-area" onclick="document.getElementById('face_photo').click()">
                    <div class="upload-content">
                        <div class="icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                        </div>
                        <p class="upload-text">Haz clic para subir o tomar una selfie</p>
                        <p class="upload-hint">Asegúrate de que tu rostro esté bien iluminado</p>
                    </div>
                    <input type="file" name="face_photo" id="face_photo" class="hidden-input" accept="image/*" required onchange="previewFile(this)">
                    <div class="preview-box"></div>
                </div>
            </div>

            <div class="documents-grid">
                <div class="form-group">
                    <label class="section-label">2. DNI (Frontal)</label>
                    <div class="upload-area" onclick="document.getElementById('dni_front').click()">
                        <div class="upload-content">
                            <div class="icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                </svg>
                            </div>
                            <p class="upload-text">Subir Foto Frontal</p>
                        </div>
                        <input type="file" name="dni_front" id="dni_front" class="hidden-input" accept="image/*" required onchange="previewFile(this)">
                        <div class="preview-box"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="section-label">3. DNI (Reverso)</label>
                    <div class="upload-area" onclick="document.getElementById('dni_back').click()">
                        <div class="upload-content">
                            <div class="icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                </svg>
                            </div>
                            <p class="upload-text">Subir Foto Reverso</p>
                        </div>
                        <input type="file" name="dni_back" id="dni_back" class="hidden-input" accept="image/*" required onchange="previewFile(this)">
                        <div class="preview-box"></div>
                    </div>
                </div>
            </div>

            <div class="security-note">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
                <div class="note-content">
                    <strong>Tus datos están protegidos</strong>
                    <p>La información proporcionada se utiliza únicamente para verificar tu identidad y asegurar la calidad de los productos en AgroMarket.</p>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                Enviar Solicitud
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </button>
        </form>
    </div>
</div>

{{-- Styles loaded via app.css --}}

@push('scripts')
    @vite(['resources/js/onboarding/farmer.js'])
@endpush
@endsection
