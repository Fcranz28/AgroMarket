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
                        <h2 class="fw-bold">¡Personaliza tu experiencia!</h2>
                        <p class="text-muted">Selecciona las categorías que más te interesan.</p>
                    </div>

                    <form action="{{ route('onboarding.preferences') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            @foreach($categories as $category)
                                <div class="col-md-4 col-6">
                                    <div class="interest-item">
                                        <input type="checkbox" id="cat_{{ $category->id }}" name="categories[]" value="{{ $category->id }}">
                                        <label for="cat_{{ $category->id }}">
                                            <span class="check-icon"><i class="fas fa-check"></i></span>
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg">Finalizar y Explorar</button>
                            <a href="{{ route('home') }}" class="btn btn-link text-muted">Omitir por ahora</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .interest-item {
        position: relative;
    }
    .interest-item input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    .interest-item label {
        display: block;
        padding: 15px;
        background: #f8f9fa;
        border: 2px solid #eee;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
        position: relative;
    }
    .interest-item input:checked + label {
        background: #e3f2fd;
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    .check-icon {
        display: none;
        position: absolute;
        top: 5px;
        right: 5px;
        font-size: 0.8rem;
    }
    .interest-item input:checked + label .check-icon {
        display: block;
    }
</style>
@endsection
