<span
    @class([
        'absolute flex items-center justify-center rounded-full bg-brand font-bold text-white',
        '-top-1 -right-1 size-[18px] text-[0.7rem]' => $variant === 'desktop',
        'top-1 left-1/2 ml-1.5 min-w-[18px] px-1 py-px text-[0.65rem]' => $variant === 'mobile',
        'hidden' => $count === 0,
    ])
    data-cart-count
    data-test="cart-count-{{ $variant }}"
>{{ $count }}</span>
