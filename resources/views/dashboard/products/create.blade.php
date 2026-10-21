@extends('layouts.dashboard')

@section('title', 'Nuevo producto')
@section('header', 'Nuevo producto')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        @include('dashboard.products.partials.form', ['action' => route('dashboard.productos.store'), 'product' => null, 'cancelUrl' => route('dashboard.productos.index'), 'multipleImages' => true])
    </div>
@endsection
