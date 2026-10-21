@extends('layouts.dashboard')

@php
    use App\Modules\Identity\Enums\VerificationStatus;

    $verificationColors = [
        VerificationStatus::Pending->value => 'warning',
        VerificationStatus::Approved->value => 'success',
        VerificationStatus::Rejected->value => 'danger',
        VerificationStatus::None->value => 'neutral',
    ];
@endphp

@section('title', 'Usuarios')
@section('header', 'Usuarios y verificaciones')

@section('content')
    <form action="{{ route('admin.users') }}" method="GET" class="flex flex-wrap items-center gap-2" role="search">
        <label for="user-search" class="sr-only">Buscar usuarios</label>
        <input id="user-search" type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o correo..." class="min-w-0 flex-1 rounded-pill border border-line bg-surface px-4 py-2 text-sm text-ink focus:border-brand focus:ring-2 focus:ring-brand/25 focus:outline-none sm:max-w-sm">
        <input type="hidden" name="sort" value="{{ request('sort') }}">
        <x-ui.button type="submit" size="sm"><i class="fas fa-search"></i> Buscar</x-ui.button>
        <x-ui.button :variant="request('sort') === 'reports_desc' ? 'primary' : 'secondary'" size="sm" :href="route('admin.users', array_filter(['sort' => request('sort') === 'reports_desc' ? null : 'reports_desc', 'search' => request('search')]))"><i class="fas fa-exclamation-triangle"></i> Más reportados</x-ui.button>
        @if (request('search') || request('sort'))
            <x-ui.button variant="ghost" size="sm" :href="route('admin.users')">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="border-b border-line text-xs tracking-wide text-muted uppercase">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-semibold">Usuario</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Rol</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Reportes</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Verificación</th>
                        <th scope="col" class="px-5 py-3 font-semibold">Cuenta</th>
                        <th scope="col" class="px-5 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-canvas/60">
                            <td class="px-5 py-3">
                                <p class="font-medium text-ink">{{ $user->name }}</p>
                                <p class="text-xs text-muted">{{ $user->email }} · #{{ $user->id }}</p>
                            </td>
                            <td class="px-5 py-3"><x-ui.badge color="neutral">{{ $user->role->label() }}</x-ui.badge></td>
                            <td class="px-5 py-3">
                                @if ($user->isFarmer())
                                    <x-ui.badge :color="$user->reports_received_count > 0 ? 'danger' : 'neutral'">{{ $user->reports_received_count }}</x-ui.badge>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if ($user->isFarmer())
                                    <x-ui.badge :color="$verificationColors[$user->verification_status->value]">{{ $user->verification_status->label() }}</x-ui.badge>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <x-ui.badge :color="$user->is_banned ? 'danger' : 'success'">{{ $user->is_banned ? 'Suspendida' : 'Activa' }}</x-ui.badge>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($user->isFarmer() && $user->verification_status === VerificationStatus::Pending)
                                        <x-ui.button size="sm" :href="route('admin.verify.view', $user)"><i class="fas fa-user-check"></i> Verificar</x-ui.button>
                                    @endif
                                    @unless ($user->isAdmin())
                                        <form method="POST" action="{{ route('admin.ban', $user) }}" x-data x-on:submit.prevent="Swal.fire({ title: @js($user->is_banned ? '¿Reactivar la cuenta?' : '¿Suspender la cuenta?'), text: @js($user->name), icon: 'warning', showCancelButton: true, confirmButtonText: @js($user->is_banned ? 'Reactivar' : 'Suspender'), cancelButtonText: 'Cancelar' }).then((result) => result.isConfirmed && $el.submit())">
                                            @csrf
                                            <button type="submit" @class(['rounded-lg p-2 transition', 'text-success hover:bg-success/10' => $user->is_banned, 'text-muted hover:bg-danger/10 hover:text-danger' => ! $user->is_banned]) title="{{ $user->is_banned ? 'Reactivar' : 'Suspender' }}" aria-label="{{ $user->is_banned ? 'Reactivar' : 'Suspender' }} a {{ $user->name }}">
                                                <i class="fas fa-{{ $user->is_banned ? 'unlock' : 'ban' }}"></i>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-muted">No se encontraron usuarios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-line px-5 py-4">{{ $users->withQueryString()->links() }}</div>
        @endif
    </x-ui.card>
@endsection
