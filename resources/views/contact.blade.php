@extends('layouts.app')

@php
    $contactItems = [
        ['label' => 'Dirección', 'value' => 'Sector Angostura km. 10, Cusco 08000', 'icon' => 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z'],
        ['label' => 'Teléfono', 'value' => '+51 941 451 076', 'icon' => 'M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2z'],
        ['label' => 'Email', 'value' => 'franzaguilar28@gmail.com', 'icon' => 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z'],
        ['label' => 'Sitio web', 'value' => 'www.AgroMarket.org', 'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm6.93 6h-2.95a15.65 15.65 0 0 0-1.38-3.56A8.03 8.03 0 0 1 18.93 8zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14a8.2 8.2 0 0 1 0-4h3.38a16.5 16.5 0 0 0 0 4H4.26zm.81 2h2.95c.32 1.25.78 2.45 1.38 3.56A7.99 7.99 0 0 1 5.07 16zm2.95-8H5.07a7.99 7.99 0 0 1 4.33-3.56A15.65 15.65 0 0 0 8.02 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66a14.7 14.7 0 0 1 0-4h4.68a14.7 14.7 0 0 1 0 4zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95a8.03 8.03 0 0 1-4.33 3.56zM16.36 14a16.5 16.5 0 0 0 0-4h3.38a8.2 8.2 0 0 1 0 4h-3.38z'],
    ];

    $subjects = [
        'Consulta sobre productos' => 'Consulta sobre productos',
        'Soporte técnico' => 'Soporte técnico',
        'Sugerencias' => 'Sugerencias',
        'Otros' => 'Otros',
    ];
@endphp

@section('content')
    <div class="mx-auto flex max-w-6xl flex-col gap-8 pt-6 pb-12">
        @if (request()->has('success'))
            <x-ui.alert type="success">¡Mensaje enviado! Nos pondremos en contacto contigo pronto.</x-ui.alert>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
            <section class="flex flex-col gap-6 rounded-card bg-linear-135 from-brand-strong to-brand p-8 text-white shadow-card">
                <div>
                    <h1 class="text-3xl font-bold">Contáctanos</h1>
                    <p class="mt-2 text-white/90">Estamos aquí para responder tus preguntas y escuchar tus comentarios.</p>
                </div>

                <ul class="flex flex-col gap-5">
                    @foreach ($contactItems as $item)
                        <li class="flex items-start gap-4">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-white/15">
                                <svg class="size-5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $item['icon'] }}"/></svg>
                            </span>
                            <div>
                                <p class="text-sm text-white/75">{{ $item['label'] }}</p>
                                <p class="font-medium break-all">{{ $item['value'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Formspree recibe el mensaje; Alpine lo envía sin salir de la página --}}
            <x-ui.card>
                <form
                    id="contactForm"
                    action="https://formspree.io/f/xyzlyzzd"
                    method="POST"
                    class="grid gap-4 sm:grid-cols-2"
                    x-data="{
                        sending: false,
                        status: null,
                        async submit() {
                            this.sending = true;
                            this.status = null;
                            try {
                                const response = await fetch(this.$el.action, { method: 'POST', body: new FormData(this.$el), headers: { Accept: 'application/json' } });
                                this.status = response.ok ? 'sent' : 'error';
                                if (response.ok) this.$el.reset();
                            } catch (error) {
                                this.status = 'error';
                            } finally {
                                this.sending = false;
                            }
                        },
                    }"
                    x-on:submit.prevent="submit"
                >
                    <h2 class="text-xl font-semibold text-ink sm:col-span-2">Envíanos un mensaje</h2>

                    <div class="sm:col-span-2" x-cloak x-show="status === 'sent'"><x-ui.alert type="success">¡Mensaje enviado! Nos pondremos en contacto contigo pronto.</x-ui.alert></div>
                    <div class="sm:col-span-2" x-cloak x-show="status === 'error'"><x-ui.alert type="error">Hubo un problema al enviar el mensaje. Inténtalo de nuevo.</x-ui.alert></div>

                    <x-ui.input name="name" id="nombre" label="Nombre completo" placeholder="Ej: Juan Pérez" required autocomplete="name" />
                    <x-ui.input name="_replyto" id="email" type="email" label="Correo electrónico" placeholder="Ej: juan@email.com" required autocomplete="email" />
                    <x-ui.input name="telefono" id="telefono" type="tel" label="Teléfono" placeholder="Ej: 987654321" required autocomplete="tel" />
                    <x-ui.select name="_subject" id="asunto" label="Asunto" :options="$subjects" placeholder="Selecciona un asunto" required />
                    <x-ui.textarea name="message" id="mensaje" label="Mensaje" rows="6" placeholder="Escribe tu mensaje aquí..." required class="sm:col-span-2" />

                    <input type="hidden" name="_next" value="{{ route('contact.show', ['success' => 1]) }}">

                    <x-ui.button type="submit" size="lg" class="sm:col-span-2" x-bind:disabled="sending">
                        <span x-text="sending ? 'Enviando…' : 'Enviar mensaje'">Enviar mensaje</span>
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>

        <div id="locations" class="overflow-hidden rounded-card border border-line shadow-soft">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3878.6662255626034!2d-71.85895702394997!3d-13.556055371837957!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x916e7d4c9db42bf5%3A0xe56b22b37c2e89f9!2sUniversidad%20Continental%20-%20Campus%20Cusco!5e0!3m2!1ses-419!2spe!4v1762201294143!5m2!1ses-419!2spe"
                title="Ubicación de AgroMarket"
                class="h-96 w-full border-0"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    </div>
@endsection
