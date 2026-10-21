@extends('layouts.app')

@php
    use App\Modules\Orders\Enums\OrderStatus;

    $tabs = [
        'orders' => ['icon' => 'fa-box', 'label' => 'Mis pedidos'],
        'profile' => ['icon' => 'fa-user-edit', 'label' => 'Mi perfil'],
        'addresses' => ['icon' => 'fa-map-marker-alt', 'label' => 'Direcciones'],
        'security' => ['icon' => 'fa-shield-alt', 'label' => 'Seguridad'],
    ];

    // Al volver de un formulario con errores se abre su pestaña
    $initialTab = match (true) {
        $errors->updateProfile->isNotEmpty() || session()->has('success') => 'profile',
        $errors->updatePassword->isNotEmpty() || session('status') === 'password-updated' => 'security',
        array_key_exists((string) request('tab'), $tabs) => (string) request('tab'),
        default => 'orders',
    };

    $reportReasons = [
        'damaged' => 'Producto dañado',
        'wrong_item' => 'Producto incorrecto',
        'quality' => 'Mala calidad',
        'expired' => 'Producto vencido',
        'other' => 'Otro',
    ];

    $avatarUrl = $user->avatar ? (filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : Storage::url($user->avatar)) : null;
@endphp

@section('content')
    <div class="mx-auto grid max-w-6xl gap-6 pt-6 pb-12 lg:grid-cols-[260px_minmax(0,1fr)]" x-data="{ tab: @js($initialTab) }">
        <aside class="flex flex-col gap-4">
            <x-ui.card class="flex flex-col items-center gap-2 text-center">
                <span class="flex size-20 items-center justify-center overflow-hidden rounded-full bg-brand-soft text-4xl text-brand">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="size-full object-cover">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </span>
                <p class="font-semibold text-ink">{{ $user->name }}</p>
                <p class="text-sm break-all text-muted">{{ $user->email }}</p>
            </x-ui.card>

            <nav class="-mx-4 flex gap-2 overflow-x-auto px-4 [scrollbar-width:none] lg:mx-0 lg:flex-col lg:px-0" aria-label="Mi cuenta">
                @foreach ($tabs as $key => $tab)
                    <button
                        type="button"
                        x-on:click="tab = @js($key)"
                        x-bind:class="tab === @js($key) ? 'bg-brand text-white shadow-soft' : 'bg-surface text-ink hover:bg-brand-soft hover:text-brand'"
                        class="flex shrink-0 items-center gap-3 rounded-xl border border-line px-4 py-2.5 text-sm font-medium whitespace-nowrap transition"
                    >
                        <i class="fas {{ $tab['icon'] }} w-4"></i>
                        {{ $tab['label'] }}
                        @if ($key === 'orders')
                            <span class="ml-auto rounded-pill bg-black/10 px-2 text-xs">{{ $orders->count() }}</span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </aside>

        <div class="min-w-0">
            {{-- Pedidos --}}
            <section x-show="tab === 'orders'" class="flex flex-col gap-4">
                <header>
                    <h1 class="text-2xl font-bold text-ink">Mis pedidos</h1>
                    <p class="text-muted">Historial completo de tus pedidos</p>
                </header>

                @forelse ($orders as $order)
                    @include('orders.partials.order-card', ['order' => $order])
                @empty
                    <x-ui.card class="flex flex-col items-center gap-3 py-14 text-center">
                        <i class="fas fa-shopping-bag text-4xl text-brand"></i>
                        <h2 class="text-lg font-semibold text-ink">No tienes pedidos aún</h2>
                        <p class="text-sm text-muted">Explora nuestros productos y realiza tu primera compra.</p>
                        <x-ui.button :href="route('products.index')">Ir a productos</x-ui.button>
                    </x-ui.card>
                @endforelse
            </section>

            {{-- Perfil --}}
            <section x-cloak x-show="tab === 'profile'" class="flex flex-col gap-4">
                <header>
                    <h1 class="text-2xl font-bold text-ink">Mi perfil</h1>
                    <p class="text-muted">Gestiona tu información personal</p>
                </header>

                @if (session('success'))
                    <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                @endif

                <x-ui.card>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-5" x-data="{ avatar: @js($avatarUrl) }">
                        @csrf
                        @method('PUT')

                        <div class="flex items-center gap-4">
                            <span class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-soft text-3xl text-brand">
                                <template x-if="avatar"><img x-bind:src="avatar" alt="" class="size-full object-cover"></template>
                                <template x-if="! avatar"><i class="fas fa-user"></i></template>
                            </span>
                            <div>
                                <label for="avatar" class="inline-flex cursor-pointer items-center gap-2 rounded-pill border border-line px-4 py-2 text-sm font-medium text-ink transition hover:border-brand hover:text-brand"><i class="fas fa-camera"></i> Cambiar foto</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="sr-only" x-on:change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => avatar = e.target.result; reader.readAsDataURL(file); }">
                                <p class="mt-1 text-xs text-muted">JPG, PNG o WebP. Máximo 2 MB.</p>
                                @error('avatar', 'updateProfile') <p class="text-xs text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-ui.input name="name" label="Nombre completo" :value="$user->name" bag="updateProfile" required autocomplete="name" />
                            <x-ui.input name="email" type="email" label="Correo electrónico" :value="$user->email" bag="updateProfile" required autocomplete="email" />
                            <x-ui.input name="phone" type="tel" label="Teléfono" :value="$user->phone" bag="updateProfile" autocomplete="tel" />
                            <x-ui.input name="dni" label="DNI" :value="$user->dni" bag="updateProfile" maxlength="8" inputmode="numeric" />
                            <x-ui.input name="current_password" id="profile_current_password" type="password" label="Contraseña actual" hint="Solo si cambias tu correo." bag="updateProfile" autocomplete="current-password" class="sm:col-span-2" />
                        </div>

                        <x-ui.button type="submit" class="self-start"><i class="fas fa-save"></i> Guardar cambios</x-ui.button>
                    </form>
                </x-ui.card>
            </section>

            {{-- Direcciones --}}
            <section x-cloak x-show="tab === 'addresses'" class="flex flex-col gap-4">
                <header>
                    <h1 class="text-2xl font-bold text-ink">Mis direcciones</h1>
                    <p class="text-muted">Las usamos para completar tus envíos en el checkout</p>
                </header>

                <livewire:identity.address-book />
            </section>

            {{-- Seguridad --}}
            <section x-cloak x-show="tab === 'security'" class="flex flex-col gap-4">
                <header>
                    <h1 class="text-2xl font-bold text-ink">Seguridad</h1>
                    <p class="text-muted">Actualiza tu contraseña o activa la verificación en dos pasos</p>
                </header>

                @if (session('status') === 'password-updated')
                    <x-ui.alert type="success">Tu contraseña se actualizó correctamente.</x-ui.alert>
                @endif

                <x-ui.card>
                    <form method="POST" action="{{ route('user-password.update') }}" class="flex flex-col gap-4">
                        @csrf
                        @method('PUT')

                        <x-ui.input name="current_password" type="password" label="Contraseña actual" bag="updatePassword" required autocomplete="current-password" />
                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-ui.input name="password" id="new_password" type="password" label="Nueva contraseña" bag="updatePassword" required minlength="8" autocomplete="new-password" />
                            <x-ui.input name="password_confirmation" id="confirm_password" type="password" label="Confirmar contraseña" bag="updatePassword" required autocomplete="new-password" />
                        </div>

                        <x-ui.button type="submit" class="self-start"><i class="fas fa-lock"></i> Actualizar contraseña</x-ui.button>
                    </form>
                </x-ui.card>

                <a href="{{ route('security') }}" class="flex items-center gap-3 rounded-card border border-line bg-surface p-5 transition hover:border-brand">
                    <span class="flex size-11 items-center justify-center rounded-full bg-brand-soft text-brand"><i class="fas fa-shield-alt"></i></span>
                    <span class="flex-1">
                        <span class="block font-semibold text-ink">Verificación en dos pasos</span>
                        <span class="text-sm text-muted">Pide un código de tu app de autenticación al iniciar sesión.</span>
                    </span>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
            </section>
        </div>
    </div>

    {{-- Seguimiento del pedido --}}
    <x-ui.modal name="tracking" title="Seguimiento del pedido">
        @php($steps = OrderStatus::trackingSteps())
        <p class="mb-6 text-sm text-muted">Pedido #<span x-text="data.orderId"></span></p>
        <ol class="flex flex-col gap-0" x-data="{ steps: @js(array_map(fn ($step) => $step->value, $steps)) }">
            @foreach ($steps as $index => $step)
                <li class="flex gap-4" x-data="{ get position() { return steps.indexOf(data.status) }, get state() { return {{ $index }} < position ? 'done' : ({{ $index }} === position ? 'current' : 'next') } }">
                    <div class="flex flex-col items-center">
                        <span
                            class="flex size-9 items-center justify-center rounded-full border-2 text-sm font-semibold"
                            x-bind:class="{ 'border-brand bg-brand text-white': state !== 'next', 'ring-4 ring-brand/25': state === 'current', 'border-line text-muted': state === 'next' }"
                        >
                            <template x-if="state === 'done'"><i class="fas fa-check"></i></template>
                            <template x-if="state !== 'done'"><span>{{ $index + 1 }}</span></template>
                        </span>
                        @unless ($loop->last)
                            <span class="h-8 w-0.5" x-bind:class="state === 'done' ? 'bg-brand' : 'bg-line'"></span>
                        @endunless
                    </div>
                    <p class="pt-1.5 font-medium" x-bind:class="state === 'next' ? 'text-muted' : 'text-ink'">{{ $step->label() }}</p>
                </li>
            @endforeach
        </ol>
    </x-ui.modal>

    {{-- Reporte de un producto entregado --}}
    <x-ui.modal name="report" title="Reportar un producto">
        <form
            x-data="{
                sending: false,
                previews: [],
                errors: {},
                pick(event) {
                    const files = [...event.target.files];
                    if (files.length > 3) {
                        event.target.value = '';
                        this.previews = [];
                        this.errors = { evidence: ['Puedes subir como máximo 3 imágenes.'] };
                        return;
                    }
                    this.errors = {};
                    this.previews = files.map((file) => URL.createObjectURL(file));
                },
                async submit() {
                    this.sending = true;
                    this.errors = {};
                    try {
                        const response = await fetch(@js(route('reports.store')), {
                            method: 'POST',
                            body: new FormData(this.$el),
                            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        });
                        const body = await response.json();
                        if (response.status === 422) {
                            this.errors = body.errors ?? {};
                            return;
                        }
                        if (! response.ok) throw new Error(body.message);
                        this.$el.reset();
                        this.previews = [];
                        $dispatch('close-modal', { name: 'report' });
                        $dispatch('notify', { type: 'success', message: 'Gracias por tu reporte. Lo revisaremos a la brevedad.' });
                    } catch (error) {
                        $dispatch('notify', { type: 'error', message: 'No se pudo enviar el reporte. Inténtalo de nuevo.' });
                    } finally {
                        this.sending = false;
                    }
                },
            }"
            x-on:submit.prevent="submit"
            class="flex flex-col gap-4"
        >
            <input type="hidden" name="order_id" x-bind:value="data.orderId">
            <input type="hidden" name="product_id" x-bind:value="data.productId">

            <p class="rounded-xl bg-canvas px-4 py-3 text-sm text-ink">Producto: <strong x-text="data.productName"></strong></p>

            <div class="flex flex-col gap-1.5">
                <x-ui.select name="reason" id="report_reason" label="Motivo" :options="$reportReasons" placeholder="Selecciona un motivo" required />
                <template x-if="errors.reason"><p class="text-xs text-danger" x-text="errors.reason[0]"></p></template>
            </div>

            <div class="flex flex-col gap-1.5">
                <x-ui.textarea name="description" id="report_description" label="Descripción" rows="4" placeholder="Describe el problema con detalle..." required />
                <template x-if="errors.description"><p class="text-xs text-danger" x-text="errors.description[0]"></p></template>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="report_evidence" class="text-sm font-medium text-ink">Fotos (opcional, máximo 3)</label>
                <input type="file" id="report_evidence" name="evidence[]" accept="image/*" multiple x-on:change="pick" class="text-sm text-muted file:mr-3 file:rounded-pill file:border-0 file:bg-brand-soft file:px-4 file:py-2 file:font-medium file:text-brand">
                <div class="flex gap-2" x-show="previews.length">
                    <template x-for="src in previews"><img x-bind:src="src" alt="" class="size-16 rounded-lg object-cover"></template>
                </div>
                <template x-if="errors.evidence"><p class="text-xs text-danger" x-text="errors.evidence[0]"></p></template>
            </div>

            <div class="flex justify-end gap-2">
                <x-ui.button variant="secondary" x-on:click="$dispatch('close-modal', { name: 'report' })">Cancelar</x-ui.button>
                <x-ui.button type="submit" x-bind:disabled="sending"><span x-text="sending ? 'Enviando…' : 'Enviar reporte'">Enviar reporte</span></x-ui.button>
            </div>
        </form>
    </x-ui.modal>
@endsection
