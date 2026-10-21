@props(['title', 'subtitle' => null, 'sideTitle' => '¡Hola, Amigo!', 'sideText' => null, 'sideLink' => null, 'sideLinkLabel' => null])

@push('styles')
    @vite(['resources/css/login.css'])
@endpush

<div class="login-body">
    <div class="container-auth">
        <div class="form-panel">
            <div class="form-container">
                <h1>{{ $title }}</h1>
                @if ($subtitle)
                    <span>{{ $subtitle }}</span>
                @endif

                @if (session('status'))
                    @php
                        $statusMessages = [
                            'verification-link-sent' => 'Te enviamos un nuevo enlace de verificación.',
                            'password-updated' => 'Tu contraseña se actualizó.',
                        ];
                    @endphp
                    <div class="input-success" role="status">{{ $statusMessages[session('status')] ?? session('status') }}</div>
                @endif

                {{ $slot }}
            </div>
        </div>

        <div class="toggle-panel">
            <h1>{{ $sideTitle }}</h1>
            @if ($sideText)
                <p>{{ $sideText }}</p>
            @endif
            @if ($sideLink)
                <a href="{{ $sideLink }}" class="btn">{{ $sideLinkLabel }}</a>
            @endif
        </div>
    </div>
</div>
