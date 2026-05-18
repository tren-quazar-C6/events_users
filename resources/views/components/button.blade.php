@props([
    'variant' => 'primary',   // primary | secondary | ghost
    'size'    => 'md',        // sm | md | lg
    'type'    => 'button',
])

@php
    // Clases base que TODO botón comparte
    $base = 'inline-flex items-center justify-center font-semibold rounded-btn transition focus:outline-none focus:ring-2 focus:ring-sage-dark/20 disabled:opacity-50 disabled:cursor-not-allowed';

    // Variantes visuales
    $variants = [
        'primary'   => 'bg-sage text-white hover:bg-sage-dark',
        'secondary' => 'bg-sage-light text-sage-dark hover:bg-sage/30',
        'ghost'     => 'bg-transparent text-sage-dark hover:bg-sage-light/50',
    ];

    // Tamaños
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
    ];

    // Unimos todo en una sola string de clases
    $classes = $base.' '.$variants[$variant].' '.$sizes[$size];
@endphp

<button type="{{ $type }}" {{ $attributes->class($classes) }}>
    {{ $slot }}
</button>