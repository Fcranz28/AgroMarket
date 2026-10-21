@extends('layouts.dashboard')

@section('title', 'Nuevo producto')
@section('header', 'Nuevo producto')

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        @include('dashboard.products.partials.form', ['action' => route('admin.productos.store'), 'product' => null, 'cancelUrl' => route('admin.productos.index'), 'multipleImages' => false])
    </div>
@endsection
