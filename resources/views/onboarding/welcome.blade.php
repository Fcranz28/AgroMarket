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
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12">
                        <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
                    </svg>
                </div>
                <h3>Quiero Comprar</h3>
                <p>Busco productos frescos y de calidad directamente del campo a mi mesa.</p>
                <div class="role-btn">Seleccionar <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px; height:16px; margin-left:5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></div>
            </div>
        </form>

        <!-- Seller Option -->
        <form action="{{ route('onboarding.role') }}" method="POST" id="form-farmer" class="role-form">
            @csrf
            <input type="hidden" name="role" value="farmer">
            <div class="role-card" onclick="document.getElementById('form-farmer').submit()">
                <div class="role-icon farmer-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12">
                        <path d="M11.584 2.376a.75.75 0 01.832 0l9 6a.75.75 0 11-.832 1.248L12 3.901 3.416 9.624a.75.75 0 01-.832-1.248l9-6z" />
                        <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 010 1.5H3a.75.75 0 010-1.5h.75v-9.918a.75.75 0 01.634-.74A49.109 49.109 0 0112 9c2.59 0 5.134.202 7.616.592a.75.75 0 01.634.74zm-7.5 2.418a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0v-6.75zm3-.75a.75.75 0 01.75.75v6.75a.75.75 0 01-1.5 0v-6.75a.75.75 0 01.75-.75zM9 12.75a.75.75 0 00-1.5 0v6.75a.75.75 0 001.5 0v-6.75z" clip-rule="evenodd" />
                        <path d="M12 7.875a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z" />
                    </svg>
                </div>
                <h3>Quiero Vender</h3>
                <p>Soy productor y quiero ofrecer mis cosechas a miles de clientes.</p>
                <div class="role-btn">Seleccionar <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px; height:16px; margin-left:5px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg></div>
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
        background-color: var(--bg-primary, #f8f9fa);
        padding: 40px 20px;
        color: var(--text-main, #333);
    }
    .onboarding-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .onboarding-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-main, #333);
        margin-bottom: 10px;
    }
    .onboarding-header p {
        font-size: 1.1rem;
        color: var(--text-light, #666);
    }
    .role-selection {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .role-card {
        background: var(--card-bg, white);
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
        border-color: var(--accent-color, #7AA537);
    }
    .role-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .role-icon svg {
        width: 40px;
        height: 40px;
    }
    .user-icon {
        background-color: #e3f2fd;
        color: #2196f3;
    }
    .farmer-icon {
        background-color: #e8f5e9;
        color: #4caf50;
    }
    
    /* Dark Mode Adjustments for Icons */
    [data-theme="dark"] .user-icon {
        background-color: rgba(33, 150, 243, 0.2);
        color: #64b5f6;
    }
    [data-theme="dark"] .farmer-icon {
        background-color: rgba(76, 175, 80, 0.2);
        color: #81c784;
    }

    .role-card h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: var(--text-main, #333);
    }
    .role-card p {
        color: var(--text-light, #666);
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .role-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 25px;
        border-radius: 25px;
        background-color: var(--bg-secondary, #f0f0f0);
        color: var(--text-main, #333);
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .role-card:hover .role-btn {
        background-color: var(--accent-color, #7AA537);
        color: white;
    }
</style>
@endsection
