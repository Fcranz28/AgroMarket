@extends('layouts.app')

@section('content')
<div class="container">
    <div class="alert alert-warning text-center" role="alert" style="margin-top: 50px;">
        <h4 class="alert-heading">¡Verificación Pendiente!</h4>
        <p>Tu cuenta de agricultor está en proceso de revisión por nuestros administradores.</p>
        <hr>
        <p class="mb-0">Te notificaremos cuando tu cuenta haya sido aprobada para que puedas comenzar a publicar productos.</p>
        <br>
        <a href="{{ route('home') }}" class="btn btn-primary">Volver al Inicio</a>
    </div>
</div>
@endsection
