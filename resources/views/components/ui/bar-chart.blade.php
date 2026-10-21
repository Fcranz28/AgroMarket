@props(['data', 'label', 'valueKey' => 'cents', 'money' => true, 'height' => 'h-56'])

@php
    /** @var list<array<string, mixed>> $data */
    $max = max(1, ...array_map(fn (array $point) => (int) $point[$valueKey], $data));
    $formatAxis = fn (float $value): string => $money
        ? \App\Modules\Shared\Money::fromCents((int) round($value))->format()
        : number_format($value);
    $isEmpty = collect($data)->every(fn (array $point) => (int) $point[$valueKey] === 0);
@endphp

{{--
    Gráfico de barras de una sola serie: barras finas ancladas a la base con el extremo
    redondeado, cuadrícula tenue, tooltip al pasar el mouse o con el teclado, y una tabla
    para lectores de pantalla.
--}}
<figure {{ $attributes->class('flex flex-col gap-3') }} x-data="{ active: null }">
    <figcaption class="sr-only">{{ $label }}</figcaption>

    <div @class(['relative', $height])>
        {{-- Cuadrícula: base, mitad y máximo --}}
        <div class="pointer-events-none absolute inset-0 flex flex-col justify-between" aria-hidden="true">
            @foreach ([1, 0.5, 0] as $fraction)
                <div class="flex items-center gap-2">
                    <span class="w-16 shrink-0 text-right text-[0.7rem] text-muted">{{ $fraction > 0 && ! $isEmpty ? $formatAxis($max * $fraction) : '' }}</span>
                    <span @class(['h-px flex-1', 'bg-line' => $fraction === 0, 'border-t border-dashed border-line/70' => $fraction > 0])></span>
                </div>
            @endforeach
        </div>

        <div class="absolute inset-y-0 right-0 left-18 flex items-end gap-0.5" role="list">
            @foreach ($data as $index => $point)
                @php($ratio = (int) $point[$valueKey] / $max)
                <button
                    type="button"
                    role="listitem"
                    class="group relative flex h-full flex-1 items-end justify-center focus:outline-none"
                    x-on:mouseenter="active = {{ $index }}"
                    x-on:mouseleave="active = null"
                    x-on:focus="active = {{ $index }}"
                    x-on:blur="active = null"
                    aria-label="{{ $point['label'] }}: {{ $point['formatted'] }}"
                >
                    <span
                        class="w-3/5 max-w-8 rounded-t-[4px] bg-brand transition group-hover:bg-brand-strong group-focus-visible:bg-brand-strong"
                        style="height: {{ (int) $point[$valueKey] > 0 ? max(2, round($ratio * 100, 2)).'%' : '0' }}"
                    ></span>

                    <span
                        x-cloak
                        x-show="active === {{ $index }}"
                        class="absolute bottom-full z-10 mb-1 rounded-lg border border-line bg-surface px-3 py-1.5 text-xs whitespace-nowrap text-ink shadow-lifted"
                    >
                        <span class="block text-muted">{{ $point['label'] }}</span>
                        <span class="font-semibold">{{ $point['formatted'] }}</span>
                    </span>
                </button>
            @endforeach
        </div>

        @if ($isEmpty)
            <p class="absolute inset-0 flex items-center justify-center text-sm text-muted">Aún no hay ventas en este periodo.</p>
        @endif
    </div>

    {{-- Etiquetas del eje X (en pantallas chicas, una sí y una no) --}}
    <div class="ml-18 flex gap-0.5 text-center text-[0.7rem] text-muted" aria-hidden="true">
        @foreach ($data as $index => $point)
            <span @class(['flex-1 truncate', 'max-sm:invisible' => $index % 2 === 1])>{{ \Illuminate\Support\Str::before($point['label'], ' ') }}</span>
        @endforeach
    </div>

    <table class="sr-only">
        <caption>{{ $label }}</caption>
        <thead><tr><th scope="col">Periodo</th><th scope="col">Valor</th></tr></thead>
        <tbody>
            @foreach ($data as $point)
                <tr><th scope="row">{{ $point['label'] }}</th><td>{{ $point['formatted'] }}</td></tr>
            @endforeach
        </tbody>
    </table>
</figure>
