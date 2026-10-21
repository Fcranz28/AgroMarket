{{-- Enlaces del menú de usuario, compartidos por el dropdown del navbar y el de la barra móvil --}}
@guest
    <x-ui.dropdown-link :href="route('login')">
        <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 7 9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>
        Iniciar Sesión
    </x-ui.dropdown-link>
    <x-ui.dropdown-link :href="route('register')">
        <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        Registrarse
    </x-ui.dropdown-link>
@else
    @if (Auth::user()->isAdmin())
        <x-ui.dropdown-link :href="route('admin.dashboard')">
            <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            Panel Admin
        </x-ui.dropdown-link>
    @elseif (Auth::user()->isFarmer())
        <x-ui.dropdown-link :href="route('farmer.dashboard')">
            <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            Panel Agricultor
        </x-ui.dropdown-link>
    @endif

    <x-ui.dropdown-link :href="route('orders.index')">
        <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        Mi perfil
    </x-ui.dropdown-link>

    <x-ui.dropdown-link :href="route('security')">
        <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
        Seguridad
    </x-ui.dropdown-link>

    <form method="POST" action="{{ route('logout') }}" class="border-t border-line">
        @csrf
        <x-ui.dropdown-link>
            <svg class="size-[18px] shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="m17 7-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            Cerrar Sesión
        </x-ui.dropdown-link>
    </form>
@endguest
