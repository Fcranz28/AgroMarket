@extends('layouts.app')

@section('content')
<div class="onboarding-container">
    <div class="onboarding-header">
        <h1>Bienvenido a AgroMarket</h1>
        <p>Para comenzar, cuéntanos cómo deseas usar la plataforma.</p>
    </div>
    
    <div class="role-selection">
        <!-- Buyer Option -->
        <form action="{{ route('onboarding.role') }}" method="POST" id="form-user" class="role-form">
            @csrf
            <input type="hidden" name="role" value="user">
            <div class="role-card" onclick="document.getElementById('form-user').submit()">
                <div class="role-icon user-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h3>Quiero Comprar</h3>
                <p>Busco productos frescos y de calidad directamente del campo a mi mesa.</p>
                <div class="role-btn">Seleccionar <i class="fas fa-arrow-right"></i></div>
            </div>
        </form>

        <!-- Seller Option -->
        <form action="{{ route('onboarding.role') }}" method="POST" id="form-farmer" class="role-form">
            @csrf
            <input type="hidden" name="role" value="farmer">
            <div class="role-card" onclick="document.getElementById('form-farmer').submit()">
                <div class="role-icon farmer-icon">
                    <i class="fas fa-tractor"></i>
                </div>
                <h3>Quiero Vender</h3>
                <p>Soy productor y quiero ofrecer mis cosechas a miles de clientes.</p>
                <div class="role-btn">Seleccionar <i class="fas fa-arrow-right"></i></div>
            </div>
        </form>
    </div>
</div>

<style>
    .onboarding-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        padding: 40px 20px;
    }
    .onboarding-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .onboarding-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    .onboarding-header p {
        font-size: 1.1rem;
        color: #666;
    }
    .role-selection {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .role-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        width: 300px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .role-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border-color: var(--primary-color);
    }
    .role-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
    }
    .user-icon {
        background-color: #e3f2fd;
        color: #2196f3;
    }
    .farmer-icon {
        background-color: #e8f5e9;
        color: #4caf50;
    }
    .role-card h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #333;
    }
    .role-card p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .role-btn {
        display: inline-block;
        padding: 10px 25px;
        border-radius: 25px;
        background-color: #f0f0f0;
        color: #333;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .role-card:hover .role-btn {
        background-color: var(--primary-color);
        color: white;
    }
</style>
@endsection
