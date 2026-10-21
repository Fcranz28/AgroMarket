@extends('layouts.dashboard')

@section('title', $product->name)
@section('header', 'Detalle del producto')

@section('content')
    @include('dashboard.products.partials.detail', ['editUrl' => route('dashboard.productos.edit', $product), 'backUrl' => route('dashboard.productos.index')])
@endsection
