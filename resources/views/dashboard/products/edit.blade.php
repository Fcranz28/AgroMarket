@extends('layouts.dashboard')

@section('title', 'Editar producto')
@section('header', 'Editar producto')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        @include('dashboard.products.partials.form', ['action' => route('dashboard.productos.update', $producto), 'product' => $producto, 'cancelUrl' => route('dashboard.productos.index'), 'multipleImages' => true])
    </div>
@endsection
