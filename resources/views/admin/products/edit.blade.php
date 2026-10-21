@extends('layouts.dashboard')

@section('title', 'Editar producto')
@section('header', 'Editar producto')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        @include('dashboard.products.partials.form', ['action' => route('admin.productos.update', $product), 'product' => $product, 'cancelUrl' => route('admin.productos.index'), 'multipleImages' => false])
    </div>
@endsection
