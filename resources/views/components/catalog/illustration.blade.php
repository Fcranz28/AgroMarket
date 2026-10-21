@props(['kind' => null, 'category' => null, 'name' => null, 'label' => null])

@php
    // Ilustración por categoría (slug) cuando no se indica una en concreto
    $byCategory = [
        'tuberculos' => 'papa',
        'frutas' => 'mango',
        'granos-y-cereales' => 'quinua',
        'verduras' => 'tomate',
        'miel-y-derivados' => 'miel',
        'hierbas-y-especias' => 'aji',
        'legumbres' => 'quinua',
        'semillas' => 'maiz',
    ];

    // Por el nombre del producto (p. ej. "Palta Hass" → palta), luego por su categoría
    $byName = ['palta' => 'palta', 'papa' => 'papa', 'mango' => 'mango', 'aji' => 'aji', 'quinua' => 'quinua', 'miel' => 'miel', 'tomate' => 'tomate', 'maiz' => 'maiz'];
    $slug = \Illuminate\Support\Str::slug((string) $name);

    $kind ??= collect($byName)->first(fn ($illustration, $word) => str_contains("-{$slug}-", "-{$word}-"))
        ?? $byCategory[$category]
        ?? 'hoja';
@endphp

<svg viewBox="0 0 200 200" {{ $attributes->class('block') }} @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif>
    <ellipse cx="100" cy="182" rx="58" ry="8" fill="#000" opacity="0.08"/>
    @switch($kind)
        @case('palta')
            <path d="M100 22c30 0 44 34 52 66 9 36-6 86-52 86s-61-50-52-86c8-32 22-66 52-66z" fill="#2E4A16"/>
            <path d="M100 34c24 0 36 30 42 58 8 32-5 72-42 72s-50-40-42-72c6-28 18-58 42-58z" fill="#C9DD7A"/>
            <path d="M100 44c19 0 29 26 34 50 6 27-4 60-34 60s-40-33-34-60c5-24 15-50 34-50z" fill="#E3EDA5"/>
            <circle cx="100" cy="116" r="28" fill="#8A5A2B"/>
            <circle cx="92" cy="108" r="8" fill="#B07A44"/>
            @break
        @case('papa')
            <path d="M44 104c-6-34 22-62 58-64 40-2 66 24 62 58-4 36-30 72-66 72-30 0-50-30-54-66z" fill="#D9A653"/>
            <path d="M62 88c4-22 24-36 46-36 20 0 34 8 40 20-14-6-32-8-50-2-14 4-26 10-36 18z" fill="#EBC27A"/>
            <circle cx="84" cy="104" r="4" fill="#A9762E"/><circle cx="122" cy="122" r="4" fill="#A9762E"/><circle cx="102" cy="146" r="3.5" fill="#A9762E"/><circle cx="138" cy="94" r="3" fill="#A9762E"/>
            @break
        @case('mango')
            <path d="M60 70c18-34 70-38 92-6 22 32 10 84-30 102-38 17-78-8-80-44-1-20 8-36 18-52z" fill="#F2A33A"/>
            <path d="M130 60c18 16 24 44 14 70-6-24-22-46-46-58 12-8 22-12 32-12z" fill="#E4572E" opacity="0.55"/>
            <path d="M72 80c10-16 26-24 42-24-14 8-28 22-34 40-4-6-6-10-8-16z" fill="#FFD27A"/>
            <path d="M108 48c4-12 12-20 24-24-2 10-8 18-16 24z" fill="#3F6B1C"/>
            <path d="M104 52c-2-8-2-14 2-20" stroke="#5B3A1A" stroke-width="4" stroke-linecap="round" fill="none"/>
            @break
        @case('aji')
            <path d="M72 56c18-10 36 2 40 22 6 34 18 66 46 92-40 6-82-22-92-62-6-22-6-42 6-52z" fill="#F4B41A"/>
            <path d="M80 66c10-6 20 0 22 12 4 20 10 38 24 56-24-6-42-26-48-50-2-8-2-14 2-18z" fill="#FFD866"/>
            <path d="M74 56c-4-10-2-20 6-28 6 8 10 16 10 24z" fill="#3F6B1C"/>
            <path d="M82 30c4-10 12-14 20-14" stroke="#3F6B1C" stroke-width="5" stroke-linecap="round" fill="none"/>
            @break
        @case('quinua')
            <path d="M34 104h132c0 38-30 66-66 66s-66-28-66-66z" fill="#B0763F"/>
            <path d="M34 104h132c-2 8-6 14-10 20H44c-4-6-8-12-10-20z" fill="#8F5A2C"/>
            <ellipse cx="100" cy="104" rx="66" ry="14" fill="#F2E3C2"/>
            <path d="M44 102c10-22 34-34 56-34s46 12 56 34c-18 8-36 10-56 10s-38-2-56-10z" fill="#EBD7AA"/>
            <g fill="#D4BB86"><circle cx="78" cy="88" r="3"/><circle cx="96" cy="80" r="3"/><circle cx="114" cy="86" r="3"/><circle cx="130" cy="94" r="3"/><circle cx="88" cy="98" r="3"/><circle cx="106" cy="96" r="3"/><circle cx="68" cy="98" r="3"/></g>
            @break
        @case('miel')
            <rect x="62" y="36" width="76" height="22" rx="6" fill="#3F6B1C"/>
            <path d="M56 64h88c8 0 12 6 12 14v80c0 10-8 18-18 18H62c-10 0-18-8-18-18V78c0-8 4-14 12-14z" fill="#E89B1C"/>
            <path d="M56 64h88c8 0 12 6 12 14v14H44V78c0-8 4-14 12-14z" fill="#F5C14D"/>
            <rect x="66" y="104" width="68" height="44" rx="8" fill="#FFF4D6"/>
            <path d="M100 114l10 6v12l-10 6-10-6v-12z" fill="#E89B1C"/>
            @break
        @case('tomate')
            <path d="M100 54c40 0 64 26 64 58s-26 60-64 60-64-28-64-60 24-58 64-58z" fill="#D83A2A"/>
            <path d="M64 92c8-16 22-24 36-26-10 8-18 18-22 32-6 0-10-2-14-6z" fill="#F07A5F"/>
            <path d="M100 44l8 18 20-6-14 14 16 10-22 0-8 16-8-16-22 0 16-10-14-14 20 6z" fill="#3F6B1C"/>
            @break
        @case('maiz')
            <path d="M100 24c24 0 32 34 32 72s-12 74-32 74-32-36-32-74 8-72 32-72z" fill="#4B1F4E"/>
            <g fill="#7A3A7E"><circle cx="88" cy="56" r="7"/><circle cx="104" cy="52" r="7"/><circle cx="118" cy="62" r="6"/><circle cx="84" cy="76" r="7"/><circle cx="100" cy="74" r="7"/><circle cx="116" cy="82" r="7"/><circle cx="86" cy="98" r="7"/><circle cx="102" cy="96" r="7"/><circle cx="118" cy="104" r="7"/><circle cx="88" cy="120" r="7"/><circle cx="104" cy="118" r="7"/><circle cx="92" cy="142" r="7"/><circle cx="106" cy="140" r="6"/></g>
            <path d="M68 110c-10 30 4 58 30 66-16-22-22-48-14-74z" fill="#7FA83A"/>
            <path d="M132 110c10 30-4 58-30 66 16-22 22-48 14-74z" fill="#5F8A26"/>
            @break
        @default
            <path d="M52 150c0-62 40-104 104-112-6 64-48 104-104 112z" fill="#7FA83A"/>
            <path d="M52 150l60-60" stroke="#3F6B1C" stroke-width="8" stroke-linecap="round"/>
    @endswitch
</svg>
