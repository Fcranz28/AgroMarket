@extends('layouts.dashboard')

@section('title', $product->name)
@section('header', 'Detalle del producto')

@section('content')
    @include('dashboard.products.partials.detail', ['editUrl' => route('admin.productos.edit', $product), 'backUrl' => route('admin.productos.index')])
@endsection
